<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteGateway implements WhatsAppGateway
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.fonnte.token', '');
        $this->apiUrl = config('services.whatsapp.fonnte.url', 'https://api.fonnte.com');
    }

    public function send(string $to, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post("{$this->apiUrl}/send", [
                'target' => $to,
                'message' => $message,
            ]);

            $json = $response->json();
            $success = $response->successful() && ($json['status'] ?? false);

            return [
                'success' => $success,
                'message_id' => $json['id'] ?? null,
                'provider' => 'fonnte',
                'error' => $success ? null : ($json['reason'] ?? 'Failed to send via Fonnte'),
            ];
        } catch (\Throwable $e) {
            Log::error('Fonnte Gateway Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message_id' => null,
                'provider' => 'fonnte',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function parseIncoming(array $payload): ?array
    {
        // Payload Fonnte: sender, message, id
        if (empty($payload['sender']) || empty($payload['message'])) {
            return null;
        }

        return [
            'message_id' => $payload['id'] ?? ('fonnte_' . uniqid()),
            'phone' => preg_replace('/[^0-9]/', '', $payload['sender']),
            'body' => $payload['message'],
            'timestamp' => now()->timestamp,
        ];
    }

    public function verifyWebhook(?string $signatureOrToken): bool
    {
        $secret = config('services.whatsapp.webhook_secret', '');
        if (empty($secret)) {
            return true;
        }

        return $signatureOrToken === $secret;
    }
}
