<?php

namespace App\Contracts;

interface WhatsAppGateway
{
    /**
     * Kirim pesan WhatsApp ke nomor tujuan.
     *
     * @param string $to Nomor tujuan (format 628xxx)
     * @param string $message Isi pesan
     * @return array Respon dari gateway ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function send(string $to, string $message): array;

    /**
     * Parsing incoming payload dari webhook provider menjadi format standar.
     *
     * @param array $payload Raw webhook request data
     * @return array ['message_id' => string, 'phone' => string, 'body' => string, 'timestamp' => int]
     */
    public function parseIncoming(array $payload): ?array;

    /**
     * Verifikasi secret token webhook untuk keamanan.
     *
     * @param string|null $signatureOrToken
     * @return bool
     */
    public function verifyWebhook(?string $signatureOrToken): bool;
}
