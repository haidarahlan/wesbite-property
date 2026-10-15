<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Models\Developer;
use App\Models\Unit;
use App\Models\UnitPhoto;
use App\Services\UnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UnitController extends Controller
{
    protected UnitService $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    /**
     * Tampilan Dashboard Manajemen Unit dengan Dark Mode & Analytics Sesuai Referensi Gambar.
     */
    public function index(Request $request)
    {
        $query = Unit::with(['photos', 'developer'])->latest();

        // Filter Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhereHas('developer', function ($devQuery) use ($search) {
                      $devQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter Tipe
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter Lokasi (Provinsi / Kota / Kecamatan)
        if ($request->filled('province') && $request->province !== 'all') {
            $query->where('province', $request->province);
        }
        if ($request->filled('district') && $request->district !== 'all') {
            $query->where('city', $request->district);
        }
        if ($request->filled('subdistrict') && $request->subdistrict !== 'all') {
            $query->where('district', $request->subdistrict);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        if (in_array($sortField, ['title', 'price', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $units = $query->paginate(10)->withQueryString();

        // Data Statistik untuk Chart & Map (Sesuai Referensi Dark Dashboard)
        $totalUnits = Unit::count();
        $statusAvailable = Unit::where('status', 'Tersedia')->count();
        $statusBooking = Unit::where('status', 'Booking')->count();
        $statusSold = Unit::where('status', 'Terjual')->count();

        // Sebaran Kecamatan
        $districtsStats = Unit::select('district', \DB::raw('count(*) as total'))
            ->whereNotNull('district')
            ->groupBy('district')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // Tren Bulanan
        $monthlyListings = Unit::select(
                \DB::raw('MONTHNAME(created_at) as month_name'),
                \DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month_name')
            ->get();

        // Dropdown list untuk filter
        $provinces = Unit::select('province')->distinct()->pluck('province');
        $cities = Unit::select('city')->distinct()->pluck('city');
        $districts = Unit::select('district')->distinct()->whereNotNull('district')->pluck('district');
        $types = Unit::select('type')->distinct()->pluck('type');

        return view('admin.units.index', compact(
            'units',
            'totalUnits',
            'statusAvailable',
            'statusBooking',
            'statusSold',
            'districtsStats',
            'monthlyListings',
            'provinces',
            'cities',
            'districts',
            'types'
        ));
    }

    /**
     * Form tambah unit baru.
     */
    public function create()
    {
        $developers = Developer::where('status', 'Aktif')->get();
        return view('admin.units.create', compact('developers'));
    }

    /**
     * Simpan unit baru.
     */
    public function store(StoreUnitRequest $request)
    {
        $unit = $this->unitService->createUnit($request->validated(), auth()->id());

        return redirect()->route('admin.units.show', $unit->id)
            ->with('success', "Unit {$unit->code} ({$unit->title}) berhasil ditambahkan!");
    }

    /**
     * Detail unit + galeri foto + riwayat status (audit trail).
     */
    public function show(Unit $unit)
    {
        $unit->load(['photos', 'developer', 'statusHistories.user']);
        return view('admin.units.show', compact('unit'));
    }

    /**
     * Form edit unit.
     */
    public function edit(Unit $unit)
    {
        $unit->load(['photos', 'developer']);
        $developers = Developer::all();
        return view('admin.units.edit', compact('unit', 'developers'));
    }

    /**
     * Update data unit.
     */
    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $this->unitService->updateUnit($unit, $request->validated(), auth()->id());

        return redirect()->route('admin.units.show', $unit->id)
            ->with('success', "Unit {$unit->code} berhasil diperbarui!");
    }

    /**
     * Soft delete unit (Admin & Superadmin).
     */
    public function destroy(Unit $unit)
    {
        Gate::authorize('delete', $unit);

        $this->unitService->softDeleteUnit($unit);

        return redirect()->route('admin.units.index')
            ->with('success', "Unit {$unit->code} berhasil dihapus (soft delete).");
    }

    /**
     * Hapus permanen unit (Khusus Superadmin).
     */
    public function forceDelete(int $id)
    {
        $unit = Unit::withTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $unit);

        $this->unitService->forceDeleteUnit($unit);

        return redirect()->route('admin.units.index')
            ->with('success', "Unit {$unit->code} berhasil dihapus permanen dari sistem.");
    }

    /**
     * Hapus foto unit secara individual.
     */
    public function deletePhoto(UnitPhoto $photo)
    {
        $unitId = $photo->unit_id;
        $this->unitService->deletePhoto($photo);

        return back()->with('success', 'Foto unit berhasil dihapus.');
    }
}
