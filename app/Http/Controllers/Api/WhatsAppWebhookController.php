<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Endpoint Webhook POST /webhook/whatsapp
     * Bersifat idempotent dan memverifikasi secret signature.
     */
    public function handle(Request $request)
    {
        $gateway = WhatsAppGatewayManager::create();

        // 1. Verifikasi Secret Webhook
        $token = $request->header('X-Hub-Signature') 
            ?? $request->header('Authorization') 
            ?? $request->input('token') 
            ?? $request->header('X-Webhook-Secret');

        if (!$gateway->verifyWebhook($token)) {
            Log::warning('WhatsApp Webhook Unauthorized Access Attempt');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Parse Incoming Payload
        $parsed = $gateway->parseIncoming($request->all());

        if (!$parsed || empty($parsed['phone']) || empty($parsed['body'])) {
            return response()->json(['status' => 'ignored', 'message' => 'Empty or non-text message'], 200);
        }

        // 3. Idempotency Check: cegah duplikasi pesan yang sama jika di-retry provider
        $existing = WhatsAppMessage::where('message_id', $parsed['message_id'])->first();
        if ($existing) {
            return response()->json(['status' => 'duplicate', 'message_id' => $parsed['message_id']], 200);
        }

        // 4. Simpan pesan masuk ke tabel whatsapp_messages
        $messageRecord = WhatsAppMessage::create([
            'message_id' => $parsed['message_id'],
            'direction' => 'inbound',
            'phone_number' => $parsed['phone'],
            'message_body' => $parsed['body'],
            'status' => 'received',
            'gateway_provider' => config('services.whatsapp.default_provider', 'fake'),
            'payload' => $request->all(),
        ]);

        // Jalankan klasifikasi pesan masuk & auto-reply (Sub-Fase 3B)
        $classifier = app(\App\Services\MessageClassifierService::class);
        $classificationResult = $classifier->processIncomingMessage($messageRecord);

        Log::info("WhatsApp Inbound Message Processed: from {$parsed['phone']} (Classification: {$classificationResult['classification']})");

        return response()->json([
            'status' => 'success',
            'message' => 'Message processed',
            'classification' => $classificationResult['classification'],
            'data' => [
                'id' => $messageRecord->id,
                'phone' => $messageRecord->phone_number,
                'lead_id' => $classificationResult['lead']->id ?? null,
                'auto_reply_sent' => $classificationResult['auto_reply_sent'],
            ]
        ], 200);
    }
}
