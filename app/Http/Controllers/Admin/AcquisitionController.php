<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcquisitionCandidate;
use App\Models\AcquisitionTarget;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AcquisitionController extends Controller
{
    public function __construct()
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang memiliki hak akses Target & Usulan Unit.');
        }
    }

    /**
     * Tampilkan daftar target unit penjualan properti.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang memiliki hak akses Target Unit.');
        }

        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        // Data Target Unit
        $targets = AcquisitionTarget::with(['createdBy'])
            ->latest()
            ->get();

        $activeTarget = AcquisitionTarget::where('status', 'Aktif')->latest()->first();

        return view('admin.acquisitions.index', compact(
            'targets',
            'activeTarget',
            'isSuperAdmin'
        ));
    }

    /**
     * Superadmin membuat target akuisisi baru (misal: Target Q4 2026).
     */
    public function storeTarget(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang berwenang menetapkan target akuisisi.');
        }

        $validated = $request->validate([
            'period_name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'target_units' => ['required', 'integer', 'min:1'],
            'target_value' => ['required', 'numeric', 'min:0'],
            'target_region' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'Aktif';

        AcquisitionTarget::create($validated);

        return back()->with('success', "Target akuisisi periode {$validated['period_name']} berhasil ditetapkan.");
    }

    /**
     * Tim Marketing / Admin mengajukan kandidat properti potensial dari lapangan.
     */
    public function storeCandidate(Request $request)
    {
        $validated = $request->validate([
            'target_id' => ['nullable', 'exists:acquisition_targets,id'],
            'title' => ['required', 'string', 'max:200'],
            'property_type' => ['required', 'string'],
            'estimated_price' => ['required', 'numeric', 'min:0'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'owner_name' => ['nullable', 'string', 'max:100'],
            'owner_phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1500'],
        ]);

        $validated['submitted_by'] = auth()->id();
        $validated['status'] = 'Diajukan';

        AcquisitionCandidate::create($validated);

        return back()->with('success', 'Usulan kandidat properti berhasil diajukan untuk evaluasi Superadmin.');
    }

    /**
     * Superadmin menyetujui atau menolak kandidat properti.
     * Jika disetujui, opsional langsung dibuatkan draft unit listing baru di database.
     */
    public function reviewCandidate(Request $request, AcquisitionCandidate $candidate)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang berwenang mengevaluasi kandidat properti.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Disetujui', 'Ditolak'])],
            'review_notes' => ['nullable', 'string', 'max:1000'],
            'create_listing' => ['nullable', 'boolean'],
        ]);

        $candidate->status = $validated['status'];
        $candidate->review_notes = $validated['review_notes'];
        $candidate->reviewed_by = auth()->id();
        $candidate->reviewed_at = now();

        // Jika disetujui dan Superadmin memilih jadikan listing unit langsung
        if ($validated['status'] === 'Disetujui' && $request->boolean('create_listing')) {
            $code = 'WP-' . strtoupper(Str::random(5));
            $unit = Unit::create([
                'code' => $code,
                'title' => $candidate->title,
                'slug' => Str::slug($candidate->title) . '-' . strtolower($code),
                'type' => $candidate->property_type,
                'price' => $candidate->estimated_price,
                'city' => $candidate->city,
                'district' => $candidate->district ?? $candidate->city,
                'province' => 'Jawa Tengah',
                'address' => $candidate->address,
                'status' => 'Tersedia',
                'is_published' => false, // Disimpan sebagai draft terlebih dahulu
                'description' => $candidate->description,
            ]);

            $candidate->converted_unit_id = $unit->id;
        }

        $candidate->save();

        $actionText = $candidate->status === 'Disetujui' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Kandidat properti berhasil {$actionText}.");
    }
}
