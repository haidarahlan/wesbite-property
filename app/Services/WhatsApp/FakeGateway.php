<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use Illuminate\Support\Str;

class FakeGateway implements WhatsAppGateway
{
    public function send(string $to, string $message): array
    {
        // Fake simulator: selalu berhasil mengirim tanpa koneksi internet/nomor fisik
        return [
            'success' => true,
            'message_id' => 'fake_msg_' . Str::random(16),
            'provider' => 'fake',
            'to' => $to,
            'message' => $message,
            'error' => null,
        ];
    }

    public function parseIncoming(array $payload): ?array
    {
        // Format simulasi pesan masuk lokal
        return [
            'message_id' => $payload['message_id'] ?? ('fake_in_' . Str::random(12)),
            'phone' => preg_replace('/[^0-9]/', '', $payload['phone'] ?? $payload['from'] ?? ''),
            'body' => trim($payload['message'] ?? $payload['body'] ?? ''),
            'timestamp' => now()->timestamp,
        ];
    }

    public function verifyWebhook(?string $signatureOrToken): bool
    {
        // Fake gateway selalu memverifikasi untuk kemudahan demo lokal
        return true;
    }
}
