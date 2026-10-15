<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaGateway implements WhatsAppGateway
{
    protected string $apiUrl;
    protected string $session;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.waha.url', 'http://localhost:3000');
        $this->session = config('services.whatsapp.waha.session', 'default');
    }

    public function send(string $to, string $message): array
    {
        try {
            // Format chatId WAHA: 628xxx@c.us
            $cleanPhone = preg_replace('/[^0-9]/', '', $to);
            $chatId = $cleanPhone . '@c.us';

            $response = Http::post("{$this->apiUrl}/api/sendText", [
                'session' => $this->session,
                'chatId' => $chatId,
                'text' => $message,
            ]);

            $json = $response->json();
            $success = $response->successful();

            return [
                'success' => $success,
                'message_id' => $json['id'] ?? null,
                'provider' => 'waha',
                'error' => $success ? null : 'Failed to send via WAHA',
            ];
        } catch (\Throwable $e) {
            Log::error('WAHA Gateway Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message_id' => null,
                'provider' => 'waha',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function parseIncoming(array $payload): ?array
    {
        // WAHA webhook format: event = message, payload.from, payload.body
        $data = $payload['payload'] ?? $payload;
        if (empty($data['from']) || empty($data['body'])) {
            return null;
        }

        $phone = explode('@', $data['from'])[0];

        return [
            'message_id' => $data['id'] ?? ('waha_' . uniqid()),
            'phone' => preg_replace('/[^0-9]/', '', $phone),
            'body' => $data['body'],
            'timestamp' => $data['timestamp'] ?? now()->timestamp,
        ];
    }

    public function verifyWebhook(?string $signatureOrToken): bool
    {
        return true;
    }
}
