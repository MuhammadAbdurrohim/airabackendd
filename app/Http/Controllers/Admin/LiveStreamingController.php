<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveStream;
use App\Models\Product;
use App\Models\LiveAnalytics;
use App\Models\LiveComment;
use App\Services\ZegoCloudService;
use App\Services\FCMService;
use Illuminate\Http\Request;
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
                'product_image' => $product->image_url ?? '', // Assuming image_url attribute
                'product_price' => $product->price,
                'live_stream_id' => $stream->id,
            ];

            $this->fcmService->sendTopicNotification('live_streams', $title, $body, $data);

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
            LiveAnalytics::create([
                'live_stream_id' => $request->live_stream_id,
                'total_comments' => $request->total_comments,
                'active_users' => $request->active_users,
                'recorded_at' => Carbon::now(),
            ]);

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
}
