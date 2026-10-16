<?php

namespace App\Notifications;

use App\Models\AvailabilityRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeveloperConfirmedAvailabilityNotification extends Notification
{
    use Queueable;

    public function __construct(public AvailabilityRequest $availabilityRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $unit = $this->availabilityRequest->unit;
        $dev = $this->availabilityRequest->developer;

        return [
            'availability_request_id' => $this->availabilityRequest->id,
            'title' => 'Konfirmasi Stok Developer Masuk',
            'message' => "Developer {$dev->name} telah mengonfirmasi unit {$unit->code} status: {$this->availabilityRequest->confirmed_unit_status}.",
            'unit_id' => $unit->id,
            'unit_code' => $unit->code,
            'status' => $this->availabilityRequest->confirmed_unit_status,
            'url' => route('admin.units.show', $unit->id),
        ];
    }
}
