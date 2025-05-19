<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessage;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display a listing of WhatsApp messages.
     */
    public function index(Request $request)
    {
        $query = WhatsAppMessage::with(['user', 'order'])
            ->latest();

        // Filter by direction
        if ($request->has('direction')) {
            $query->where('direction', $request->direction);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by phone number
        if ($request->filled('phone')) {
            $query->where('phone_number', 'LIKE', '%' . $request->phone . '%');
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $messages = $query->paginate(20);

        return view('admin.whatsapp.index', [
            'messages' => $messages,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Show conversation with a specific phone number.
     */
    public function conversation($phoneNumber)
    {
        $messages = WhatsAppMessage::where('phone_number', $phoneNumber)
            ->with(['user', 'order'])
            ->orderBy('created_at', 'DESC')
            ->paginate(50);

        $user = User::where('phone_number', 'LIKE', '%' . $phoneNumber)->first();

        return view('admin.whatsapp.conversation', [
            'messages' => $messages,
            'phone_number' => $phoneNumber,
            'user' => $user,
        ]);
    }

    /**
     * Send a new message.
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $result = $this->whatsappService->sendMessage(
                $request->phone_number,
                $request->message,
                ['source' => 'admin_panel']
            );

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesan berhasil dikirim',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim pesan',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'error' => $e->getMessage(),
                'phone' => $request->phone_number,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengirim pesan',
            ], 500);
        }
    }

    /**
     * Get message statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => WhatsAppMessage::count(),
            'sent' => WhatsAppMessage::where('direction', 'outbound')->count(),
            'received' => WhatsAppMessage::where('direction', 'inbound')->count(),
            'failed' => WhatsAppMessage::where('status', 'failed')->count(),
            'today' => WhatsAppMessage::whereDate('created_at', today())->count(),
            'users' => WhatsAppMessage::distinct('phone_number')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Export messages to CSV.
     */
    public function export(Request $request)
    {
        $query = WhatsAppMessage::with(['user', 'order']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="whatsapp-messages.csv"',
        ];

        $callback = function() use ($messages) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Tanggal',
                'Nomor HP',
                'Nama Pengguna',
                'Pesan',
                'Arah',
                'Status',
                'ID Pesanan',
            ]);

            foreach ($messages as $message) {
                fputcsv($file, [
                    $message->id,
                    $message->created_at,
                    $message->phone_number,
                    $message->user ? $message->user->name : '-',
                    $message->message,
                    $message->direction,
                    $message->status,
                    $message->order_id ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}