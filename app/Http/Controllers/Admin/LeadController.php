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
     * Tampilkan detail lead dan galeri screenshot bukti chat konsumen.
     */
    public function show(Lead $lead)
    {
        $lead->load(['unit.photos', 'assignedUser', 'chatProofs.uploader']);

        $units = Unit::select('id', 'code', 'title', 'price', 'status')->get();
        $users = User::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('admin.leads.show', compact('lead', 'units', 'users'));
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
        $code = $lead->reference_code;
        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', "Lead {$code} berhasil dihapus.");
    }
}
