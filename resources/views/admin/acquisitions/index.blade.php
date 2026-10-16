@extends('layouts.admin')

@section('title', 'Target Penjualan Unit Properti')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ openTargetModal: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">Target Bisnis</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-bullseye text-[#C5A038]"></i>
                <span>Target Unit Penjualan</span>
            </h1>
            <p class="text-xs text-gray-400">
                Monitoring pencapaian target penjualan dan closing unit properti PT Wastu Pratama Wijaya.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($isSuperAdmin)
                <button @click="openTargetModal = true"
                        class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white text-xs font-semibold rounded-xl shadow-lg shadow-[#C5A038]/20 flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tetapkan Target Baru</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Active Target Progress Card (Jika Ada Target Aktif) -->
    @if($activeTarget)
        <div class="bg-gradient-to-br from-[#1F2731] to-[#161B22] border border-[#2B3543] rounded-3xl p-6 shadow-xl relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-[#C5A038]/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#2B3543]/80">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            Target Aktif
                        </span>
                        <h2 class="text-base font-bold text-white">{{ $activeTarget->period_name }}</h2>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        Wilayah Fokus: <strong class="text-gray-200">{{ $activeTarget->target_region ?? 'Semua Wilayah' }}</strong> • 
                        Periode: {{ $activeTarget->start_date->translatedFormat('d M Y') }} s/d {{ $activeTarget->end_date->translatedFormat('d M Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[11px] text-gray-400 block">Target Nilai Closing</span>
                        <span class="text-lg font-black text-[#C5A038] font-mono">Rp {{ number_format($activeTarget->target_value / 1000000000, 2, ',', '.') }} M</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] text-gray-400 block">Target Unit Terjual</span>
                        <span class="text-lg font-black text-white font-mono">{{ $activeTarget->target_units }} Unit</span>
                    </div>
                </div>
            </div>

            <!-- Dual Progress Bar (Unit & Nilai Closing) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 text-xs">
                <div>
                    <div class="flex justify-between text-gray-300 font-medium mb-1.5">
                        <span>Pencapaian Unit Closing / Terjual</span>
                        <span class="font-bold text-[#C5A038]">{{ $activeTarget->sold_units_count }} / {{ $activeTarget->target_units }} Unit ({{ $activeTarget->units_progress_percentage }}%)</span>
                    </div>
                    <div class="w-full bg-[#14181E] h-3 rounded-full overflow-hidden border border-[#2B3543]">
                        <div class="bg-[#C5A038] h-full rounded-full transition-all duration-700" style="width: {{ $activeTarget->units_progress_percentage }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-gray-300 font-medium mb-1.5">
                        <span>Realisasi Nilai Transaksi Closing</span>
                        <span class="font-bold text-emerald-400">Rp {{ number_format($activeTarget->sold_units_value / 1000000000, 2, ',', '.') }} M ({{ $activeTarget->value_progress_percentage }}%)</span>
                    </div>
                    <div class="w-full bg-[#14181E] h-3 rounded-full overflow-hidden border border-[#2B3543]">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-700" style="width: {{ $activeTarget->value_progress_percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Daftar Master Target Unit -->
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-[#C5A038]"></i>
                <span>Daftar Target Unit & Periode ({{ $targets->count() }})</span>
            </h3>
            <span class="text-xs text-gray-400">Target ditetapkan khusus oleh Managing Director</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($targets as $tgt)
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 space-y-3 relative hover:border-[#C5A038]/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-white">{{ $tgt->period_name }}</h3>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $tgt->start_date?->format('d/m/Y') }} - {{ $tgt->end_date?->format('d/m/Y') }}</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $tgt->status === 'Aktif' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-gray-800 text-gray-400' }}">
                            {{ $tgt->status }}
                        </span>
                    </div>

                    <div class="text-xs text-gray-400 space-y-1.5 pt-1">
                        <div class="flex justify-between">
                            <span>Wilayah Fokus:</span>
                            <strong class="text-gray-200">{{ $tgt->target_region ?? 'Semua Wilayah' }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Target Unit:</span>
                            <strong class="text-white font-mono">{{ $tgt->target_units }} Unit</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Realisasi Closing:</span>
                            <strong class="text-emerald-400 font-mono">{{ $tgt->sold_units_count }} Unit ({{ $tgt->units_progress_percentage }}%)</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Target Nilai:</span>
                            <strong class="text-[#C5A038] font-mono">Rp {{ number_format($tgt->target_value / 1000000000, 2, ',', '.') }} M</strong>
                        </div>
                    </div>

                    <!-- Progress Bar Unit -->
                    <div class="pt-2 border-t border-[#2B3543] text-xs">
                        <div class="flex justify-between text-[11px] text-gray-400 mb-1">
                            <span>Pencapaian Target</span>
                            <span class="font-bold text-[#C5A038] font-mono">{{ $tgt->units_progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-[#14181E] h-2 rounded-full overflow-hidden">
                            <div class="bg-[#C5A038] h-full rounded-full transition-all" style="width: {{ $tgt->units_progress_percentage }}%"></div>
                        </div>
                    </div>

                    @if($tgt->notes)
                        <div class="pt-2 text-[10px] text-gray-400 italic bg-[#161B22] p-2 rounded-xl border border-[#2B3543]/50">
                            "{{ $tgt->notes }}"
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500 bg-[#1F2731] rounded-2xl border border-[#2B3543]">
                    Belum ada target unit yang ditetapkan. Klik "Tetapkan Target Baru" untuk memulai.
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL: FORM TARGET BARU (SUPERADMIN) -->
    @if($isSuperAdmin)
        <div x-show="openTargetModal" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="openTargetModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-bullseye text-[#C5A038]"></i>
                        <span>Tetapkan Target Unit Baru</span>
                    </h3>
                    <button @click="openTargetModal = false" class="text-gray-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form action="{{ route('admin.acquisitions.targets.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Nama Periode Target <span class="text-red-400">*</span></label>
                        <input type="text" name="period_name" required placeholder="Contoh: Target Q4 2026 atau Semester 1 2027"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Tanggal Mulai <span class="text-red-400">*</span></label>
                            <input type="date" name="start_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Tanggal Berakhir <span class="text-red-400">*</span></label>
                            <input type="date" name="end_date" required value="{{ date('Y-12-31') }}"
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Target Jumlah Unit <span class="text-red-400">*</span></label>
                            <input type="number" name="target_units" required min="1" value="10"
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Target Nilai Closing (Rp) <span class="text-red-400">*</span></label>
                            <input type="number" name="target_value" required min="0" value="10000000000"
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Wilayah Fokus Penjualan</label>
                        <input type="text" name="target_region" placeholder="Contoh: Sleman, Yogyakarta, Magelang"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Catatan Strategis</label>
                        <textarea name="notes" rows="2" placeholder="Catatan arahan tipe properti atau fokus penjualan..."
                                  class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2B3543]">
                        <button type="button" @click="openTargetModal = false" class="px-4 py-2 bg-[#2B3543] hover:bg-[#384455] text-gray-300 rounded-xl font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-xl font-semibold shadow-md">
                            Simpan Target
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
