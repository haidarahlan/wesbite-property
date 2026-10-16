<?php

namespace App\Notifications;

use App\Models\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SurveyReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public Survey $survey)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'survey_id' => $this->survey->id,
            'title' => 'Pengingat Survei Lapangan Besok (H-1)',
            'message' => "Survei unit {$this->survey->unit->code} bersama {$this->survey->lead->name} ({$this->survey->lead->reference_code}) terjadwal besok pukul {$this->survey->survey_date->format('H:i')}.",
            'lead_id' => $this->survey->lead_id,
            'unit_id' => $this->survey->unit_id,
            'url' => route('admin.surveys.index'),
        ];
    }
}
