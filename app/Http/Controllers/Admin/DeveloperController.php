<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeveloperController extends Controller
{
    /**
     * Tampilkan daftar developer / mitra properti.
     */
    public function index(Request $request)
    {
        $query = Developer::withCount('units')->latest();

        // Filter status kerja sama
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter pencarian teks
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $developers = $query->paginate(15)->withQueryString();

        // Statistik
        $totalDevelopers = Developer::count();
        $activeDevelopers = Developer::where('status', 'Aktif')->count();
        $inactiveDevelopers = Developer::where('status', '!=', 'Aktif')->count();

        return view('admin.developers.index', compact(
            'developers',
            'totalDevelopers',
            'activeDevelopers',
            'inactiveDevelopers'
        ));
    }

    /**
     * Simpan data developer baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['Aktif', 'Nonaktif', 'Dalam Penjajakan'])],
        ], [
            'name.required' => 'Nama PIC / Kontak developer wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / Telepon developer wajib diisi.',
        ]);

        // Bersihkan format nomor HP
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $validated['phone'] = $cleanPhone;

        Developer::create($validated);

        return redirect()->route('admin.developers.index')
            ->with('success', 'Data mitra developer berhasil ditambahkan.');
    }

    /**
     * Update data developer.
     */
    public function update(Request $request, Developer $developer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['Aktif', 'Nonaktif', 'Dalam Penjajakan'])],
        ]);

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $validated['phone'] = $cleanPhone;

        $developer->update($validated);

        return back()->with('success', 'Data mitra developer berhasil diperbarui.');
    }

    /**
     * Hapus data developer.
     */
    public function destroy(Developer $developer)
    {
        if ($developer->units()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus developer yang masih memiliki keterkaitan unit listing properti.');
        }

        $developer->delete();
        return back()->with('success', 'Data mitra developer berhasil dihapus.');
    }
}
