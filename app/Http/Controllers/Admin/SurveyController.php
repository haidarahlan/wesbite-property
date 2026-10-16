<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\WhatsAppGateway;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Survey;
use App\Models\Unit;
use App\Models\User;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    /**
     * Tampilkan daftar survei lapangan.
     */
    public function index(Request $request)
    {
        $query = Survey::with(['lead', 'unit', 'pic'])->latest('survey_date');

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter periode
        if ($request->filled('timeframe')) {
            if ($request->timeframe === 'today') {
                $query->whereDate('survey_date', Carbon::today());
            } elseif ($request->timeframe === 'tomorrow') {
                $query->whereDate('survey_date', Carbon::tomorrow());
            } elseif ($request->timeframe === 'upcoming') {
                $query->where('survey_date', '>=', Carbon::now());
            }
        }

        // Filter pencarian teks
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('lead', function ($lq) use ($search) {
                    $lq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%")
                       ->orWhere('reference_code', 'like', "%{$search}%");
                })->orWhereHas('unit', function ($uq) use ($search) {
                    $uq->where('code', 'like', "%{$search}%")
                       ->orWhere('title', 'like', "%{$search}%");
                });
            });
        }

        $surveys = $query->paginate(15)->withQueryString();

        // Metrik Ringkas
        $totalSurveys = Survey::count();
        $scheduledSurveys = Survey::where('status', 'Terjadwal')->count();
        $tomorrowSurveys = Survey::where('status', 'Terjadwal')
            ->whereDate('survey_date', Carbon::tomorrow())
            ->count();
        $completedSurveys = Survey::where('status', 'Selesai')->count();

        $leads = Lead::whereIn('status', ['Tertarik', 'Follow-up', 'Baru', 'Survei Terjadwal'])
            ->select('id', 'name', 'phone', 'reference_code', 'unit_id')
            ->orderBy('name')
            ->get();
        $units = Unit::select('id', 'code', 'title')->orderBy('code')->get();
        $users = User::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('admin.surveys.index', compact(
            'surveys',
            'totalSurveys',
            'scheduledSurveys',
            'tomorrowSurveys',
            'completedSurveys',
            'leads',
            'units',
            'users'
        ));
    }

    /**
     * Jadwalkan survei baru dan kirim notifikasi/pesan WhatsApp konfirmasi ke konsumen.
     */
    public function store(Request $request, WhatsAppGateway $gateway)
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'survey_date' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'lead_id.required' => 'Konsumen/Lead wajib dipilih.',
            'unit_id.required' => 'Unit properti wajib dipilih.',
            'survey_date.required' => 'Tanggal & jam survei wajib ditentukan.',
            'survey_date.after' => 'Waktu survei harus di masa mendatang.',
        ]);

        $survey = Survey::create([
            'lead_id' => $validated['lead_id'],
            'unit_id' => $validated['unit_id'],
            'pic_user_id' => $validated['pic_user_id'],
            'survey_date' => $validated['survey_date'],
            'status' => 'Terjadwal',
            'notes' => $validated['notes'],
        ]);

        // Perbarui status lead menjadi "Survei Terjadwal"
        $lead = Lead::findOrFail($validated['lead_id']);
        $lead->update([
            'status' => 'Survei Terjadwal',
            'unit_id' => $validated['unit_id'],
            'assigned_to' => $validated['pic_user_id'] ?? $lead->assigned_to,
        ]);

        // Kirim Pesan Konfirmasi Jadwal ke WhatsApp Konsumen
        $unit = Unit::findOrFail($validated['unit_id']);
        $pic = User::find($validated['pic_user_id']);
        $picName = $pic ? $pic->name : 'Tim Marketing Wastu Property';
        $waktu = Carbon::parse($validated['survey_date'])->translatedFormat('l, d F Y - H:i') . ' WIB';

        $confirmText = "Halo Bapak/Ibu *{$lead->name}*,\n\n"
            . "Jadwal kunjungan survei lapangan untuk unit *{$unit->title}* ({$unit->code}) telah berhasil kami agendakan:\n\n"
            . "📅 *Waktu:* {$waktu}\n"
            . "👤 *PIC Pendamping:* {$picName}\n"
            . ($validated['notes'] ? "📍 *Catatan/Titik Kumpul:* {$validated['notes']}\n\n" : "\n")
            . "Kami akan mengirimkan pengingat H-1 sebelum jadwal kunjungan. Terima kasih atas kepercayaan Anda pada Wastu Property.";

        $sent = $gateway->send($lead->phone, $confirmText);

        WhatsAppMessage::create([
            'lead_id' => $lead->id,
            'message_id' => 'survey-conf-' . $survey->id . '-' . time(),
            'direction' => 'outbound',
            'phone_number' => $lead->phone,
            'message_body' => $confirmText,
            'status' => $sent ? 'sent' : 'failed',
            'gateway_provider' => config('whatsapp.default', 'fake'),
            'payload' => [
                'type' => 'survey_confirmation',
                'survey_id' => $survey->id,
            ],
        ]);

        return redirect()->route('admin.surveys.index')
            ->with('success', "Jadwal survei berhasil dibuat dan pesan konfirmasi telah dikirim ke nomor WA {$lead->phone}.");
    }

    /**
     * Update hasil survei atau status pelaksanaan survei.
     */
    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Terjadwal', 'Selesai', 'Dibatalkan', 'Dijadwal Ulang'])],
            'survey_date' => ['required', 'date'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'result' => ['nullable', 'string', 'max:2000'],
        ]);

        $survey->update($validated);

        // Update status lead jika status survei selesai atau batal
        if ($validated['status'] === 'Selesai') {
            $survey->lead->update(['status' => 'Survei Selesai']);
        } elseif ($validated['status'] === 'Dibatalkan') {
            $survey->lead->update(['status' => 'Batal']);
        } elseif ($validated['status'] === 'Terjadwal' || $validated['status'] === 'Dijadwal Ulang') {
            $survey->lead->update(['status' => 'Survei Terjadwal']);
        }

        return back()->with('success', 'Data survei dan hasil kunjungan berhasil diperbarui.');
    }

    /**
     * Kirim pengingat survei secara manual langsung dari dashboard (on-demand).
     */
    public function sendManualReminder(Survey $survey, WhatsAppGateway $gateway)
    {
        $lead = $survey->lead;
        $unit = $survey->unit;
        $picName = $survey->pic?->name ?? 'Tim Marketing Wastu Property';
        $jam = $survey->survey_date->format('H:i');
        $tgl = $survey->survey_date->translatedFormat('l, d F Y');

        $messageText = "Halo Bapak/Ibu *{$lead->name}* (Ref: *{$lead->reference_code}*),\n\n"
            . "Mengingatkan kembali agenda survei lapangan properti *{$unit->title}* ({$unit->code}) pada:\n"
            . "📅 *{$tgl}*\n"
            . "⏰ *Pukul {$jam} WIB*\n"
            . "👤 *PIC Pendamping:* {$picName}\n"
            . ($survey->notes ? "📍 *Titik Kumpul:* {$survey->notes}\n\n" : "\n")
            . "Mohon konfirmasi jika ada perubahan. Terima kasih.";

        $sent = $gateway->send($lead->phone, $messageText);

        WhatsAppMessage::create([
            'lead_id' => $lead->id,
            'message_id' => 'reminder-manual-' . $survey->id . '-' . time(),
            'direction' => 'outbound',
            'phone_number' => $lead->phone,
            'message_body' => $messageText,
            'status' => $sent ? 'sent' : 'failed',
            'gateway_provider' => config('whatsapp.default', 'fake'),
            'payload' => [
                'type' => 'survey_reminder_manual',
                'survey_id' => $survey->id,
            ],
        ]);

        $survey->update(['reminder_sent_at' => now()]);

        return back()->with('success', "Pengingat WA berhasil dikirim ke {$lead->name} ({$lead->phone}).");
    }

    /**
     * Hapus jadwal survei.
     */
    public function destroy(Survey $survey)
    {
        $survey->delete();
        return back()->with('success', 'Jadwal survei berhasil dihapus.');
    }
}
