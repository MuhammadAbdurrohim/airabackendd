<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveStream;
use App\Models\Product;
use App\Models\LiveOrder;
use App\Models\LiveAnalytics;
use App\Models\LiveComment;
use App\Services\ZegoCloudService;
use App\Services\FCMService;
use App\Notifications\LiveStreamNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LiveStreamingController extends Controller
{
    protected $zegoCloudService;
    protected $fcmService;

    public function __construct(ZegoCloudService $zegoCloudService, FCMService $fcmService)
    {
        $this->zegoCloudService = $zegoCloudService;
        $this->fcmService = $fcmService;
    }

    // Existing methods...

    // Pin product to live stream
    public function pinProduct(Request $request)
    {
        $request->validate([
            'live_stream_id' => 'required|exists:live_streams,id',
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $stream = LiveStream::findOrFail($request->live_stream_id);
            $stream->pinned_product_id = $request->product_id;
            $stream->save();

            $product = Product::findOrFail($request->product_id);

            // Send push notification to topic 'live_streams' about pinned product
            $title = 'Produk Baru di Live!';
            $body = 'Produk "' . $product->name . '" sedang ditampilkan di live streaming.';
            $data = [
                'type' => 'pinned_product',
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $product->image_url ?? '',
                'product_price' => $product->price,
                'live_stream_id' => $stream->id,
            ];

            // Send FCM notification
            $this->fcmService->sendTopicNotification('live_streams', $title, $body, $data);

            // Send in-app notification to all admins
            $admins = \App\Models\Admin::all();
            Notification::send($admins, new LiveStreamNotification('stream.product_pinned', [
                'title' => $stream->title,
                'product_name' => $product->name,
                'product_id' => $product->id,
                'stream_id' => $stream->id
            ]));

            return response()->json(['message' => 'Produk berhasil dipasang di live stream.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memasang produk: ' . $e->getMessage()], 500);
        }
    }

    // Save live analytics data
    public function saveAnalytics(Request $request)
    {
        $request->validate([
            'live_stream_id' => 'required|exists:live_streams,id',
            'total_comments' => 'required|integer|min:0',
            'active_users' => 'required|integer|min:0',
        ]);

        try {
            $analytics = LiveAnalytics::create([
                'live_stream_id' => $request->live_stream_id,
                'total_comments' => $request->total_comments,
                'active_users' => $request->active_users,
                'recorded_at' => Carbon::now(),
            ]);

            // Notify admins about significant changes in analytics
            if ($request->active_users >= 100 || $request->total_comments >= 1000) {
                $admins = \App\Models\Admin::all();
                Notification::send($admins, new LiveStreamNotification('stream.analytics', [
                    'stream_id' => $request->live_stream_id,
                    'active_users' => $request->active_users,
                    'total_comments' => $request->total_comments,
                    'recorded_at' => $analytics->recorded_at->format('Y-m-d H:i:s')
                ]));
            }

            return response()->json(['message' => 'Analytics data saved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save analytics: ' . $e->getMessage()], 500);
        }
    }

    // List order-type comments
    public function orderComments(Request $request)
    {
        $request->validate([
            'stream_id' => 'required|exists:live_streams,id',
        ]);

        $stream = LiveStream::findOrFail($request->stream_id);

        $orderComments = $stream->comments()
            ->where('type', 'ORDER')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.streaming.order_comments', compact('orderComments', 'stream'));
    }

    // Export order comments to Excel
    public function exportOrderComments(Request $request)
    {
        $request->validate([
            'stream_id' => 'required|exists:live_streams,id',
        ]);

        $stream = LiveStream::findOrFail($request->stream_id);

        try {
            $fileName = 'order_comments_' . $stream->id . '_' . date('Y-m-d_His') . '.xlsx';

            Excel::create($fileName, function($excel) use ($stream) {
                $excel->sheet('Order Comments', function($sheet) use ($stream) {
                    $comments = $stream->comments()
                        ->where('type', 'ORDER')
                        ->with('user')
                        ->orderBy('created_at', 'asc')
                        ->get()
                        ->map(function($comment) {
                            return [
                                'Time' => $comment->created_at->format('Y-m-d H:i:s'),
                                'User' => $comment->user->name,
                                'Comment' => $comment->content,
                            ];
                        });

                    $sheet->fromArray($comments);
                });
            })->store('xlsx', storage_path('app/public/exports'));

            return response()->download(storage_path('app/public/exports/' . $fileName))
                ->deleteFileAfterSend();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export order comments: ' . $e->getMessage());
        }
    }

    /**
     * Display live orders history
     */
    public function liveOrders(Request $request)
    {
        $query = LiveOrder::with(['order', 'liveStream', 'buyer', 'voucher'])
            ->latest();

        // Filter by live stream
        if ($request->filled('live_stream_id')) {
            $query->where('live_stream_id', $request->live_stream_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $liveOrders = $query->paginate(10)
            ->withQueryString();

        $liveStreams = LiveStream::all();

        return view('admin.streaming.live_orders.index', compact('liveOrders', 'liveStreams'));
    }

    /**
     * Export live orders to Excel
     */
    public function exportLiveOrders(Request $request)
    {
        $query = LiveOrder::with(['order', 'liveStream', 'buyer', 'voucher']);

        // Apply filters
        if ($request->filled('live_stream_id')) {
            $query->where('live_stream_id', $request->live_stream_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $liveOrders = $query->get();

        // Send notification for export event
        $admins = \App\Models\Admin::all();
        Notification::send($admins, new LiveStreamNotification('stream.orders_exported', [
            'total_orders' => $liveOrders->count(),
            'date_range' => [
                'start' => $request->start_date ?? 'all',
                'end' => $request->end_date ?? 'all'
            ],
            'stream_id' => $request->live_stream_id ?? 'all'
        ]));

        // Prepare data for export
        $exportData = [];
        foreach ($liveOrders as $order) {
            $exportData[] = [
                'ID Pesanan' => $order->order->order_number,
                'Live Stream' => $order->liveStream->title,
                'Pembeli' => $order->buyer->name,
                'Total' => number_format($order->total_amount, 0, ',', '.'),
                'Voucher' => $order->voucher ? $order->voucher->code : '-',
                'Diskon' => number_format($order->discount_amount, 0, ',', '.'),
                'Total Akhir' => number_format($order->getFinalAmount(), 0, ',', '.'),
                'Tanggal' => $order->created_at->format('d/m/Y H:i:s'),
            ];
        }

        // Generate Excel file
        $fileName = 'live_orders_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(function($excel) use ($exportData) {
            $excel->sheet('Live Orders', function($sheet) use ($exportData) {
                $sheet->fromArray($exportData);
            });
        }, $fileName);
    }
}
