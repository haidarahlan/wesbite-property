<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    /**
     * Tampilkan daftar lead dengan pencarian & filter status/sumber.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Lead::with(['unit', 'assignedUser'])->latest();

        // JIKA ADMIN BIASA (MARKETING): Hanya tampilkan lead miliknya sendiri (atau yang belum ditugaskan/diambil)
        if (!$isSuperAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereNull('assigned_to');
            });
        }

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

        // Hitung statistik ringkas untuk kartu atas (Sesuai Hak Akses Pengguna)
        $metricQuery = Lead::query();
        if (!$isSuperAdmin) {
            $metricQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereNull('assigned_to');
            });
        }

        $totalLeads = (clone $metricQuery)->count();
        $newLeads = (clone $metricQuery)->where('status', 'Baru')->count();
        $interestedLeads = (clone $metricQuery)->where('status', 'Tertarik')->count();
        $followUpLeads = (clone $metricQuery)->where('status', 'Follow-up')->count();
        $surveyLeads = (clone $metricQuery)->whereIn('status', ['Survei Terjadwal', 'Survei Selesai'])->count();

        // Unit yang bisa dipilih: status Tersedia atau Booking, tetapi belum memiliki lead aktif yang memegang unit tersebut
        $assignedUnitIds = Lead::whereNotNull('unit_id')
            ->whereNotIn('status', ['Batal', 'Tidak Tertarik'])
            ->pluck('unit_id')
            ->toArray();

        $units = Unit::whereIn('status', ['Tersedia', 'Booking'])
            ->whereNotIn('id', $assignedUnitIds)
            ->select('id', 'code', 'title', 'status')
            ->orderBy('code')
            ->get();
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
            'survey_date' => ['nullable', 'required_if:status,Survei Terjadwal', 'date'],
            'survey_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama lead wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'survey_date.required_if' => 'Tanggal & jam survei wajib diisi jika status dipilih Survei Terjadwal.',
        ]);

        // Bersihkan format nomor HP (ke format 62)
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $validated['phone'] = $cleanPhone;

        // Otomatis tugaskan ke user yang sedang login jika tidak dipilih atau jika bukan superadmin
        if (empty($validated['assigned_to']) || !auth()->user()->isSuperAdmin()) {
            $validated['assigned_to'] = auth()->id();
        }

        // Generate Kode Referensi Unik
        $count = Lead::withTrashed()->count() + 1;
        $validated['reference_code'] = 'L-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $lead = Lead::create($validated);

        // Jika status Survei Terjadwal, otomatis buat record di tabel surveys (Survei Lapangan)
        if ($validated['status'] === 'Survei Terjadwal' && !empty($validated['survey_date']) && $lead->unit_id) {
            \App\Models\Survey::create([
                'lead_id' => $lead->id,
                'unit_id' => $lead->unit_id,
                'pic_user_id' => $lead->assigned_to ?? auth()->id(),
                'survey_date' => $validated['survey_date'],
                'status' => 'Terjadwal',
                'notes' => $validated['survey_notes'] ?? $lead->notes,
            ]);
        }

        return redirect()->route('admin.leads.show', $lead->id)
            ->with('success', "Lead berhasil ditambahkan dengan kode referensi {$lead->reference_code}.");
    }

    /**
     * Tampilkan detail lead dan galeri screenshot bukti chat konsumen.
     */
    public function show(Lead $lead)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $lead->assigned_to && $lead->assigned_to !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat data prospek milik marketing lain.');
        }

        $lead->load(['unit.photos', 'assignedUser', 'chatProofs.uploader', 'surveys']);

        $latestSurvey = $lead->surveys()->first();

        // Unit yang bisa dipilih: status Tersedia atau Booking yang belum aktif dipegang lead lain, atau unit milik lead ini saat ini
        $assignedUnitIdsOtherLeads = Lead::whereNotNull('unit_id')
            ->where('id', '!=', $lead->id)
            ->whereNotIn('status', ['Batal', 'Tidak Tertarik'])
            ->pluck('unit_id')
            ->toArray();

        $units = Unit::whereIn('status', ['Tersedia', 'Booking'])
            ->whereNotIn('id', $assignedUnitIdsOtherLeads)
            ->orWhere('id', $lead->unit_id)
            ->select('id', 'code', 'title', 'price', 'status')
            ->orderBy('code')
            ->get();
        $users = User::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('admin.leads.show', compact('lead', 'units', 'users', 'latestSurvey'));
    }

    /**
     * Perbarui data lead (status, catatan, PIC, unit, & sinkronisasi agenda survei).
     */
    public function update(Request $request, Lead $lead)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $lead->assigned_to && $lead->assigned_to !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah data prospek milik marketing lain.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'status' => ['required', Rule::in([
                'Baru', 'Tertarik', 'Tidak Tertarik', 'Follow-up', 'Survei Terjadwal', 'Survei Selesai', 'Closing', 'Batal'
            ])],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'survey_date' => ['nullable', 'required_if:status,Survei Terjadwal', 'date'],
            'survey_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'survey_date.required_if' => 'Tanggal & jam survei wajib ditentukan ketika status diubah ke Survei Terjadwal.',
        ]);

        // Bersihkan nomor
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $validated['phone'] = $cleanPhone;

        $lead->update($validated);

        // SINKRONISASI OTOMATIS KE SURVEI LAPANGAN (OPSI 1)
        if ($validated['status'] === 'Survei Terjadwal' && !empty($validated['survey_date'])) {
            $unitId = $validated['unit_id'] ?? $lead->unit_id;
            
            if ($unitId) {
                // Cari survei aktif yang masih terjadwal milik lead ini, jika ada update; jika belum ada create baru
                $existingSurvey = \App\Models\Survey::where('lead_id', $lead->id)
                    ->where('status', 'Terjadwal')
                    ->latest()
                    ->first();

                if ($existingSurvey) {
                    $existingSurvey->update([
                        'unit_id' => $unitId,
                        'pic_user_id' => $validated['assigned_to'] ?? $existingSurvey->pic_user_id,
                        'survey_date' => $validated['survey_date'],
                        'notes' => $validated['survey_notes'] ?? $existingSurvey->notes,
                    ]);
                } else {
                    \App\Models\Survey::create([
                        'lead_id' => $lead->id,
                        'unit_id' => $unitId,
                        'pic_user_id' => $validated['assigned_to'] ?? auth()->id(),
                        'survey_date' => $validated['survey_date'],
                        'status' => 'Terjadwal',
                        'notes' => $validated['survey_notes'] ?? $lead->notes,
                    ]);
                }
            }
        }

        return back()->with('success', 'Data lead, agenda survei, dan status unit berhasil disinkronkan.');
    }

    /**
     * Upload screenshot bukti percakapan chat WhatsApp konsumen yang tertarik.
     */
    public function uploadChatProof(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            'title' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'image.required' => 'File gambar screenshot bukti chat wajib diunggah.',
            'image.image' => 'File harus berupa format gambar valid (JPG, PNG, WebP).',
            'image.max' => 'Ukuran file gambar maksimal 5 MB.',
        ]);

        $path = $request->file('image')->store('chat_proofs', 'public');

        $lead->chatProofs()->create([
            'image_path' => $path,
            'title' => $validated['title'] ?? 'Bukti Chat WhatsApp Konsumen',
            'notes' => $validated['notes'],
            'uploaded_by' => auth()->id(),
        ]);

        // Otomatis update status ke "Tertarik" atau "Follow-up" jika sebelumnya Baru
        if ($lead->status === 'Baru') {
            $lead->update(['status' => 'Tertarik']);
        }

        return back()->with('success', 'Bukti percakapan WhatsApp konsumen berhasil diunggah dan disimpan ke CRM.');
    }

    /**
     * Hapus screenshot bukti chat.
     */
    public function deleteChatProof(\App\Models\LeadChatProof $proof)
    {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($proof->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($proof->image_path);
        }

        $proof->delete();
        return back()->with('success', 'Bukti chat berhasil dihapus.');
    }

    /**
     * Hapus lead (Soft Delete).
     */
    public function destroy(Lead $lead)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $lead->assigned_to && $lead->assigned_to !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus data prospek milik marketing lain.');
        }

        $code = $lead->reference_code;
        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', "Lead {$code} berhasil dihapus.");
    }
}
