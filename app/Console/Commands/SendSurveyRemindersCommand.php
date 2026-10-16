<?php

namespace App\Console\Commands;

use App\Models\Survey;
use App\Models\User;
use App\Notifications\SurveyReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendSurveyRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'surveys:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Kirim pengingat survei lapangan H-1 ke tim marketing via in-app notification';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memeriksa jadwal survei lapangan untuk besok (H-1)...');

        $tomorrowStart = Carbon::tomorrow()->startOfDay();
        $tomorrowEnd = Carbon::tomorrow()->endOfDay();

        // Ambil semua survei berstatus Terjadwal untuk besok yang belum pernah dikirim pengingat
        $surveys = Survey::with(['lead', 'unit', 'pic'])
            ->where('status', 'Terjadwal')
            ->whereBetween('survey_date', [$tomorrowStart, $tomorrowEnd])
            ->whereNull('reminder_sent_at')
            ->get();

        if ($surveys->isEmpty()) {
            $this->info('Tidak ada survei lapangan H-1 yang memerlukan pengingat hari ini.');
            return Command::SUCCESS;
        }

        $sentCount = 0;

        foreach ($surveys as $survey) {
            $lead = $survey->lead;
            $unit = $survey->unit;

            // Kirim notifikasi internal dashboard ke Admin & PIC
            $notifiables = User::where('is_active', true)->get();
            foreach ($notifiables as $user) {
                $user->notify(new SurveyReminderNotification($survey));
            }

            // Update status timestamp pengingat
            $survey->update([
                'reminder_sent_at' => now(),
            ]);

            $sentCount++;
            $this->line("Notifikasi pengingat survei H-1 dibuat untuk {$lead->name} ({$lead->phone}) pada unit {$unit->code}");
        }

        $this->info("Selesai. Total {$sentCount} pengingat survei berhasil diproses.");

        return Command::SUCCESS;
    }
}
