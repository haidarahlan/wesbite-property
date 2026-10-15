<?php

namespace App\Services;

use App\Models\FaqTemplate;
use App\Models\Lead;
use App\Models\MessageFilterRule;
use App\Models\Unit;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\FakeGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageClassifierService
{
    protected FakeGateway $fakeGateway;

    public function __construct()
    {
        $this->fakeGateway = new FakeGateway();
    }

    /**
     * Memproses dan mengklasifikasikan pesan masuk WhatsApp:
     * - Menentukan apakah Tertarik vs Tidak Tertarik
     * - Jika Tertarik -> buat / update data Lead di CRM & hubungkan ke Unit terkait
     * - Jika Tidak Tertarik -> picu Auto-Reply FAQ WhatsApp
     */
    public function processIncomingMessage(WhatsAppMessage $message): array
    {
        return DB::transaction(function () use ($message) {
            $body = strtolower($message->message_body);
            $phone = $message->phone_number;

            // 1. Deteksi Unit Properti (jika konsumen mencantumkan kode unit, misal WP-BORO-01)
            $matchedUnit = null;
            $units = Unit::all();
            foreach ($units as $unit) {
                if (str_contains($body, strtolower($unit->code)) || str_contains($body, strtolower($unit->title))) {
                    $matchedUnit = $unit;
                    break;
                }
            }

            // 2. Evaluasi Keyword Berdasarkan Tabel message_filter_rules
            $rules = MessageFilterRule::where('is_active', true)->get();
            $scoreInterested = 0;
            $scoreNotInterested = 0;

            foreach ($rules as $rule) {
                $kw = strtolower($rule->keyword);
                if (str_contains($body, $kw)) {
                    if ($rule->classification === 'Tertarik') {
                        $scoreInterested += $rule->weight;
                    } else {
                        $scoreNotInterested += $rule->weight;
                    }
                }
            }

            // Jika ada kode unit yang cocok, berikan bobot tertarik tinggi secara default
            if ($matchedUnit) {
                $scoreInterested += 3;
            }

            // Keputusan Klasifikasi
            $classification = ($scoreNotInterested > $scoreInterested) ? 'Tidak Tertarik' : 'Tertarik';

            // Update pesan dengan hasil klasifikasi
            $message->update([
                'classification' => $classification,
            ]);

            $lead = null;
            $autoReplySent = false;
            $replyMessage = null;

            if ($classification === 'Tertarik') {
                // A. KONSUMEN TERTARIK: Cari atau Buat Lead Baru di CRM
                $lead = Lead::firstOrNew(['phone' => $phone]);

                if (!$lead->exists) {
                    $lead->reference_code = 'L-' . str_pad((string) (Lead::count() + 1), 4, '0', '0');
                    $lead->source = 'whatsapp';
                    $lead->status = 'Tertarik';
                }

                if ($matchedUnit) {
                    $lead->unit_id = $matchedUnit->id;
                }

                $lead->save();

                // Hubungkan chat ke lead
                $message->update(['lead_id' => $lead->id]);

            } else {
                // B. KONSUMEN TIDAK TERTARIK / RAGU: Kirim Auto-Reply FAQ
                $faqTemplate = FaqTemplate::where('is_default', true)->first() 
                    ?? FaqTemplate::first();

                $replyText = $faqTemplate 
                    ? $faqTemplate->template_message 
                    : "Halo, terima kasih telah menghubungi Wastu Property. Jika ada pertanyaan mengenai legalitas tanah, KPR bank, atau katalog properti kami, silakan sampaikan di sini.";

                // Kirim via FakeGateway
                $sendResult = $this->fakeGateway->send($phone, $replyText);

                // Catat pesan auto-reply keluar ke whatsapp_messages
                WhatsAppMessage::create([
                    'message_id' => $sendResult['message_id'],
                    'direction' => 'outbound',
                    'phone_number' => $phone,
                    'message_body' => $replyText,
                    'status' => 'sent',
                    'gateway_provider' => 'fake',
                    'classification' => 'Auto-Reply FAQ',
                    'payload' => $sendResult,
                ]);

                $message->update(['auto_replied' => true]);
                $autoReplySent = true;
                $replyMessage = $replyText;
            }

            return [
                'classification' => $classification,
                'lead' => $lead,
                'unit' => $matchedUnit,
                'auto_reply_sent' => $autoReplySent,
                'reply_message' => $replyMessage,
            ];
        });
    }
}
