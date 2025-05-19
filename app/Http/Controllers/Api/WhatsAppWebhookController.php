<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Handle incoming webhook
     */
    public function handle(Request $request)
    {
        try {
            Log::info('WhatsApp webhook received', [
                'payload' => $request->all()
            ]);

            // Validate webhook signature if provided
            if ($signature = $request->header('X-Webhook-Signature')) {
                $this->validateSignature($request, $signature);
            }

            $data = $request->all();

            // Handle different types of webhooks
            switch ($data['type'] ?? '') {
                case 'message':
                    return $this->handleMessage($data);
                
                case 'status':
                    return $this->handleStatus($data);
                
                default:
                    Log::warning('Unknown webhook type', ['data' => $data]);
                    return response()->json(['status' => 'ignored']);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle incoming message
     */
    protected function handleMessage(array $data)
    {
        $from = $data['from'] ?? null;
        $message = $data['message'] ?? null;
        $messageId = $data['message_id'] ?? null;

        if (!$from || !$message) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid message data'
            ], 400);
        }

        // Process message with auto-replies
        $processed = $this->whatsappService->processIncomingMessage($from, $message, $messageId);

        return response()->json([
            'status' => 'success',
            'processed' => $processed
        ]);
    }

    /**
     * Handle status update
     */
    protected function handleStatus(array $data)
    {
        $messageId = $data['message_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$messageId || !$status) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid status data'
            ], 400);
        }

        // Update message status in database
        $message = \App\Models\WhatsAppMessage::where('message_id', $messageId)->first();
        if ($message) {
            $message->update([
                'status' => $status,
                'metadata' => array_merge($message->metadata ?? [], [
                    'status_updated_at' => now()->toIso8601String(),
                    'status_data' => $data
                ])
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Validate webhook signature
     */
    protected function validateSignature(Request $request, string $signature)
    {
        $payload = $request->getContent();
        $secret = config('whatsapp.webhook_secret');

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid webhook signature');
        }
    }
}
