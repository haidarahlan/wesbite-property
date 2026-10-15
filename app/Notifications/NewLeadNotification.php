<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLeadNotification extends Notification
{
    use Queueable;

    public Lead $lead;
    public string $messageSnippet;

    public function __construct(Lead $lead, string $messageSnippet = '')
    {
        $this->lead = $lead;
        $this->messageSnippet = $messageSnippet;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Tersimpan di database untuk bell badge di dashboard
    }

    public function toArray(object $notifiable): array
    {
        return [
            'lead_id' => $this->lead->id,
            'reference_code' => $this->lead->reference_code,
            'phone' => $this->lead->phone,
            'unit_title' => $this->lead->unit->title ?? 'Umum / Belum memilih unit',
            'snippet' => $this->messageSnippet,
            'title' => 'Lead Baru Diterima via WhatsApp!',
            'created_at' => now()->translatedFormat('d M Y, H:i'),
        ];
    }
}
