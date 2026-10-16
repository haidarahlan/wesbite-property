<?php

namespace App\Console\Commands;

use App\Contracts\WhatsAppGateway;
use App\Models\Survey;
use App\Models\User;
use App\Models\WhatsAppMessage;
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
    protected $description = 'Kirim pengingat survei lapangan H-1 ke konsumen via WhatsApp dan notifikasi dashboard ke tim marketing';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppGateway $gateway): int
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
            $picName = $survey->pic?->name ?? 'Tim Marketing Wastu Property';
            $jam = $survey->survey_date->format('H:i');
            $tgl = $survey->survey_date->translatedFormat('l, d F Y');

            // Format Pesan WhatsApp Pengingat H-1
            $messageText = "Halo Bapak/Ibu *{$lead->name}* (Ref: *{$lead->reference_code}*),\n\n"
                . "Mengingatkan kembali rencana agenda kunjungan survei lapangan properti *{$unit->title}* ({$unit->code}) yang telah dijadwalkan pada:\n"
                . "📅 *{$tgl}*\n"
                . "⏰ *Pukul {$jam} WIB*\n"
                . "👤 *PIC Pendamping:* {$picName}\n"
                . ($survey->notes ? "📍 *Catatan / Titik Kumpul:* {$survey->notes}\n\n" : "\n")
                . "Mohon konfirmasi jika ada perubahan jadwal atau kendala perjalanan. Sampai bertemu besok!";

            // 1. Kirim via WhatsApp Gateway
            $isSent = $gateway->send($lead->phone, $messageText);

            // 2. Simpan ke riwayat pesan WhatsApp
            WhatsAppMessage::create([
                'lead_id' => $lead->id,
                'message_id' => 'reminder-h1-' . $survey->id . '-' . time(),
                'direction' => 'outbound',
                'phone_number' => $lead->phone,
                'message_body' => $messageText,
                'status' => $isSent ? 'sent' : 'failed',
                'gateway_provider' => config('whatsapp.default', 'fake'),
                'payload' => [
                    'type' => 'survey_reminder_h1',
                    'survey_id' => $survey->id,
                ],
            ]);

            // 3. Kirim notifikasi dashboard ke Admin & PIC
            $notifiables = User::where('is_active', true)->get();
            foreach ($notifiables as $user) {
                $user->notify(new SurveyReminderNotification($survey));
            }

            // 4. Update status timestamp pengingat
            $survey->update([
                'reminder_sent_at' => now(),
            ]);

            $sentCount++;
            $this->line(" Pengingat H-1 berhasil dikirim ke {$lead->name} ({$lead->phone}) untuk unit {$unit->code}");
        }

        $this->info("Selesai. Total {$sentCount} pengingat survei berhasil diproses.");

        return Command::SUCCESS;
    }
}
