<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\WhatsAppGateway;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Unit;
use App\Models\User;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    /**
     * Tampilkan daftar lead dengan pencarian & filter status/sumber.
     */
    public function index(Request $request)
    {
        $query = Lead::with(['unit', 'assignedUser'])->latest();

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter sumber
        if ($request->filled('source') && $request->source !== 'all') {
            $query->where('source', $request->source);
        }

        // Pencarian teks (Nama, No HP, Kode Referensi)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('reference_code', 'like', "%{$search}%")
                  ->orWhereHas('unit', function ($uq) use ($search) {
                      $uq->where('code', 'like', "%{$search}%")
                         ->orWhere('title', 'like', "%{$search}%");
                  });
            });
        }

        $leads = $query->paginate(15)->withQueryString();

        // Hitung statistik ringkas untuk kartu atas
        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'Baru')->count();
        $interestedLeads = Lead::where('status', 'Tertarik')->count();
        $followUpLeads = Lead::where('status', 'Follow-up')->count();
        $surveyLeads = Lead::whereIn('status', ['Survei Terjadwal', 'Survei Selesai'])->count();

        $units = Unit::select('id', 'code', 'title')->orderBy('code')->get();
        $users = User::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('admin.leads.index', compact(
            'leads',
            'totalLeads',
            'newLeads',
            'interestedLeads',
            'followUpLeads',
            'surveyLeads',
            'units',
            'users'
        ));
    }

    /**
     * Simpan lead baru secara manual oleh Admin/Marketing.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'source' => ['required', Rule::in(['whatsapp', 'website', 'manual', 'telepon', 'walk_in'])],
            'status' => ['required', Rule::in([
                'Baru', 'Tertarik', 'Tidak Tertarik', 'Follow-up', 'Survei Terjadwal', 'Survei Selesai', 'Closing', 'Batal'
            ])],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama lead wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ]);

        // Bersihkan format nomor HP (ke format 62)
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $validated['phone'] = $cleanPhone;

        // Generate Kode Referensi Unik
        $count = Lead::withTrashed()->count() + 1;
        $validated['reference_code'] = 'L-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $lead = Lead::create($validated);

        return redirect()->route('admin.leads.show', $lead->id)
            ->with('success', "Lead berhasil ditambahkan dengan kode referensi {$lead->reference_code}.");
    }

    /**
     * Tampilkan detail lead, riwayat chat WhatsApp, dan form balas pesan.
     */
    public function show(Lead $lead)
    {
        $lead->load(['unit.photos', 'assignedUser', 'whatsappMessages']);

        // Ambil semua pesan masuk & keluar yang berkaitan dengan nomor HP lead
        $messages = WhatsAppMessage::where('phone_number', $lead->phone)
            ->orWhere('lead_id', $lead->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $units = Unit::select('id', 'code', 'title', 'price', 'status')->get();
        $users = User::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('admin.leads.show', compact('lead', 'messages', 'units', 'users'));
    }

    /**
     * Perbarui data lead (status, catatan, PIC, unit).
     */
    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'status' => ['required', Rule::in([
                'Baru', 'Tertarik', 'Tidak Tertarik', 'Follow-up', 'Survei Terjadwal', 'Survei Selesai', 'Closing', 'Batal'
            ])],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Bersihkan nomor
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $validated['phone'] = $cleanPhone;

        $lead->update($validated);

        return back()->with('success', 'Data lead berhasil diperbarui.');
    }

    /**
     * Kirim balasan pesan WhatsApp langsung dari dashboard admin.
     */
    public function sendReply(Request $request, Lead $lead, WhatsAppGateway $gateway)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'message.required' => 'Isi pesan balasan tidak boleh kosong.',
        ]);

        $messageText = trim($validated['message']);

        // Kirim via gateway (FakeGateway / Fonnte / Waha)
        $sent = $gateway->send($lead->phone, $messageText);

        // Catat ke whatsapp_messages sebagai outbound
        WhatsAppMessage::create([
            'lead_id' => $lead->id,
            'message_id' => 'out-' . time() . '-' . uniqid(),
            'direction' => 'outbound',
            'phone_number' => $lead->phone,
            'message_body' => $messageText,
            'status' => $sent ? 'sent' : 'failed',
            'gateway_provider' => config('whatsapp.default', 'fake'),
            'payload' => [
                'sender_user_id' => auth()->id(),
                'sender_name' => auth()->user()->name,
            ],
        ]);

        // Update status lead ke Follow-up jika sebelumnya Baru atau Tertarik
        if (in_array($lead->status, ['Baru', 'Tertarik'])) {
            $lead->update(['status' => 'Follow-up']);
        }

        return back()->with('success', 'Pesan balasan WhatsApp berhasil dikirim.');
    }

    /**
     * Hapus lead (Soft Delete).
     */
    public function destroy(Lead $lead)
    {
        $code = $lead->reference_code;
        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', "Lead {$code} berhasil dihapus.");
    }
}
