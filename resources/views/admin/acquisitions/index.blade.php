@extends('layouts.admin')

@section('title', 'Target Akuisisi & Kandidat Properti')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ tab: 'candidates', openTargetModal: false, openCandidateModal: false, selectedCandidate: null, openReviewModal: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">Strategi Akuisisi</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-bullseye text-[#C5A038]"></i>
                <span>Target Akuisisi & Kandidat Unit</span>
            </h1>
            <p class="text-xs text-gray-400">
                @if($isSuperAdmin)
                    Monitoring pencapaian target akuisisi properti baru dan evaluasi usulan listing potensial dari tim lapangan.
                @else
                    Ajukan usulan listing rumah/vila/tanah potensial dari survei lapangan untuk ditinjau oleh Managing Director.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($isSuperAdmin)
                <button @click="openTargetModal = true"
                        class="px-3.5 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-200 text-xs font-semibold rounded-xl border border-[#2B3543] flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-plus text-[#C5A038]"></i>
                    <span>Tetapkan Target Baru</span>
                </button>
            @endif

            <button @click="openCandidateModal = true"
                    class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white text-xs font-semibold rounded-xl shadow-lg shadow-[#C5A038]/20 flex items-center gap-2 transition-all cursor-pointer">
                <i class="fa-solid fa-upload"></i>
                <span>Ajukan Kandidat Properti</span>
            </button>
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
                        <span class="text-[11px] text-gray-400 block">Target Nilai Properti</span>
                        <span class="text-lg font-black text-[#C5A038] font-mono">Rp {{ number_format($activeTarget->target_value / 1000000000, 2, ',', '.') }} M</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] text-gray-400 block">Target Unit</span>
                        <span class="text-lg font-black text-white font-mono">{{ $activeTarget->target_units }} Unit</span>
                    </div>
                </div>
            </div>

            <!-- Dual Progress Bar (Unit & Nilai) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 text-xs">
                <div>
                    <div class="flex justify-between text-gray-300 font-medium mb-1.5">
                        <span>Pencapaian Unit Disetujui</span>
                        <span class="font-bold text-[#C5A038]">{{ $activeTarget->approvedCandidates()->count() }} / {{ $activeTarget->target_units }} Unit ({{ $activeTarget->units_progress_percentage }}%)</span>
                    </div>
                    <div class="w-full bg-[#14181E] h-3 rounded-full overflow-hidden border border-[#2B3543]">
                        <div class="bg-[#C5A038] h-full rounded-full transition-all duration-700" style="width: {{ $activeTarget->units_progress_percentage }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-gray-300 font-medium mb-1.5">
                        <span>Realisasi Nilai Properti</span>
                        <span class="font-bold text-emerald-400">Rp {{ number_format($activeTarget->acquired_value / 1000000000, 2, ',', '.') }} M ({{ $activeTarget->value_progress_percentage }}%)</span>
                    </div>
                    <div class="w-full bg-[#14181E] h-3 rounded-full overflow-hidden border border-[#2B3543]">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-700" style="width: {{ $activeTarget->value_progress_percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab Selector -->
    <div class="flex items-center gap-2 border-b border-[#2B3543] pb-2 text-xs font-semibold">
        <button @click="tab = 'candidates'" 
                :class="tab === 'candidates' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-list-check"></i>
            <span>Daftar Usulan Kandidat ({{ $candidates->total() }})</span>
        </button>
        <button @click="tab = 'targets'" 
                :class="tab === 'targets' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-bullseye"></i>
            <span>Master Target Akuisisi ({{ $targets->count() }})</span>
        </button>
    </div>

    <!-- TAB 1: KANDIDAT PROPERTI -->
    <div x-show="tab === 'candidates'" class="space-y-4">
        
        <!-- Tabel Kandidat -->
        <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-300">
                    <thead class="bg-[#161B22] border-b border-[#2B3543] text-[11px] text-gray-400 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Properti Potensial</th>
                            <th class="py-3 px-4">Estimasi Harga</th>
                            <th class="py-3 px-4">Lokasi & Pemilik</th>
                            <th class="py-3 px-4">Diajukan Oleh</th>
                            <th class="py-3 px-4">Status & Evaluasi</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2B3543]/60">
                        @forelse($candidates as $can)
                            <tr class="hover:bg-[#232B35]/50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-white text-xs">{{ $can->title }}</div>
                                    <span class="inline-block px-2 py-0.2 rounded text-[10px] bg-[#161B22] text-[#C5A038] border border-[#2B3543] mt-1">
                                        {{ $can->property_type }}
                                    </span>
                                    @if($can->convertedUnit)
                                        <a href="{{ route('admin.units.show', $can->converted_unit_id) }}" class="text-[10px] text-emerald-400 hover:underline inline-flex items-center gap-1 ml-2">
                                            <i class="fa-solid fa-link"></i> Listing Unit [{{ $can->convertedUnit->code }}]
                                        </a>
                                    @endif
                                </td>

                                <td class="py-3 px-4 font-mono font-bold text-amber-400">
                                    Rp {{ number_format($can->estimated_price, 0, ',', '.') }}
                                </td>

                                <td class="py-3 px-4">
                                    <div class="text-gray-200">{{ $can->city }}{{ $can->district ? ', ' . $can->district : '' }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $can->owner_name ?? 'Pemilik belum diisi' }} {{ $can->owner_phone ? "({$can->owner_phone})" : '' }}
                                    </div>
                                </td>

                                <td class="py-3 px-4">
                                    <div class="text-gray-200 font-medium">{{ $can->submitter->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $can->created_at->format('d/m/Y') }}</div>
                                </td>

                                <td class="py-3 px-4">
                                    @php
                                        $canBadge = match($can->status) {
                                            'Diajukan' => 'bg-blue-950/60 text-blue-300 border-blue-500/30',
                                            'Disetujui' => 'bg-emerald-950/60 text-emerald-300 border-emerald-500/30',
                                            'Ditolak' => 'bg-red-950/60 text-red-300 border-red-500/30',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $canBadge }}">
                                        {{ $can->status }}
                                    </span>

                                    @if($can->review_notes)
                                        <div class="text-[10px] text-gray-400 mt-1 italic">
                                            "{{ Str::limit($can->review_notes, 40) }}"
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-center">
                                    @if($isSuperAdmin && $can->status === 'Diajukan')
                                        <button @click="selectedCandidate = {{ $can->toJson() }}; openReviewModal = true"
                                                class="px-3 py-1.5 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 mx-auto transition-all cursor-pointer">
                                            <i class="fa-solid fa-gavel text-xs"></i>
                                            <span>Evaluasi</span>
                                        </button>
                                    @else
                                        <button @click="selectedCandidate = {{ $can->toJson() }}; openReviewModal = true"
                                                class="px-2.5 py-1 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 rounded-lg text-xs font-medium flex items-center gap-1 mx-auto transition-all cursor-pointer">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                            <span>Detail</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    Belum ada kandidat properti yang diajukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($candidates->hasPages())
                <div class="p-4 bg-[#161B22] border-t border-[#2B3543]">
                    {{ $candidates->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- TAB 2: MASTER TARGET AKUISISI -->
    <div x-show="tab === 'targets'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($targets as $tgt)
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-white">{{ $tgt->period_name }}</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $tgt->status === 'Aktif' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-gray-800 text-gray-400' }}">
                            {{ $tgt->status }}
                        </span>
                    </div>

                    <div class="text-xs text-gray-400 space-y-1">
                        <div>Wilayah: <strong class="text-gray-200">{{ $tgt->target_region ?? 'Semua Wilayah' }}</strong></div>
                        <div>Target Unit: <strong class="text-white">{{ $tgt->target_units }} Unit</strong> (Realisasi: {{ $tgt->approved_candidates_count }} Unit)</div>
                        <div>Target Nilai: <strong class="text-[#C5A038]">Rp {{ number_format($tgt->target_value / 1000000000, 2, ',', '.') }} M</strong></div>
                    </div>

                    <!-- Progress Bar Unit -->
                    <div class="pt-2 border-t border-[#2B3543] text-xs">
                        <div class="flex justify-between text-[11px] text-gray-400 mb-1">
                            <span>Pencapaian Target</span>
                            <span class="font-bold text-[#C5A038]">{{ $tgt->units_progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-[#14181E] h-2 rounded-full overflow-hidden">
                            <div class="bg-[#C5A038] h-full rounded-full" style="width: {{ $tgt->units_progress_percentage }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 py-12 text-center text-gray-500 bg-[#1F2731] rounded-2xl border border-[#2B3543]">
                    Belum ada target akuisisi yang ditetapkan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL 1: FORM AJUKAN KANDIDAT PROPERTI (MARKETING) -->
    <div x-show="openCandidateModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openCandidateModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-upload text-[#C5A038]"></i>
                    <span>Ajukan Kandidat Properti Potensial</span>
                </h3>
                <button @click="openCandidateModal = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('admin.acquisitions.candidates.store') }}" method="POST" class="space-y-3.5">
                @csrf
                @if($activeTarget)
                    <input type="hidden" name="target_id" value="{{ $activeTarget->id }}">
                @endif

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Nama / Judul Properti <span class="text-red-400">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Rumah Kolonial Sleman Siap Huni"
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Tipe Properti <span class="text-red-400">*</span></label>
                        <select name="property_type" required class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="Rumah">Rumah</option>
                            <option value="Vila">Vila & Resort</option>
                            <option value="Tanah">Tanah Kavling</option>
                            <option value="Ruko">Ruko / Komersial</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Estimasi Harga (Rp) <span class="text-red-400">*</span></label>
                        <input type="number" name="estimated_price" required placeholder="Contoh: 850000000"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Kota / Kabupaten <span class="text-red-400">*</span></label>
                        <input type="text" name="city" required placeholder="Contoh: Magelang"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Kecamatan</label>
                        <input type="text" name="district" placeholder="Contoh: Borobudur"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Nama Pemilik / Developer</label>
                        <input type="text" name="owner_name" placeholder="Nama pemilik..."
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">No. Kontak / WA Pemilik</label>
                        <input type="text" name="owner_phone" placeholder="08xxxxxxxxxx"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Alamat Lengkap Properti</label>
                    <textarea name="address" rows="2" placeholder="Alamat jalan atau patokan lokasi..."
                              class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Catatan Potensi & Spesifikasi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Spesifikasi luas tanah, akses jalan, potensi minat pasar..."
                              class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2B3543]">
                    <button type="button" @click="openCandidateModal = false" class="px-4 py-2 bg-[#2B3543] hover:bg-[#384455] text-gray-300 rounded-xl font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-xl font-semibold shadow-md">
                        Ajukan Kandidat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: FORM TARGET BARU (SUPERADMIN) -->
    @if($isSuperAdmin)
        <div x-show="openTargetModal" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="openTargetModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-bullseye text-[#C5A038]"></i>
                        <span>Tetapkan Target Akuisisi Baru</span>
                    </h3>
                    <button @click="openTargetModal = false" class="text-gray-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form action="{{ route('admin.acquisitions.targets.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Nama Periode Target <span class="text-red-400">*</span></label>
                        <input type="text" name="period_name" required placeholder="Contoh: Q4 2026 atau Semester 1 2027"
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
                            <label class="block text-gray-300 font-semibold mb-1">Target Nilai Properti (Rp) <span class="text-red-400">*</span></label>
                            <input type="number" name="target_value" required min="0" value="10000000000"
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Wilayah Fokus Akuisisi</label>
                        <input type="text" name="target_region" placeholder="Contoh: Borobudur, Magelang, Sleman Utara"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Catatan Strategis</label>
                        <textarea name="notes" rows="2" placeholder="Catatan arahan tipe properti yang diprioritaskan..."
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

    <!-- MODAL 3: EVALUASI KANDIDAT PROPERTI (SUPERADMIN & PREVIEW) -->
    <div x-show="openReviewModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openReviewModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-gavel text-[#C5A038]"></i>
                    <span>Review & Evaluasi Kandidat Properti</span>
                </h3>
                <button @click="openReviewModal = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <template x-if="selectedCandidate">
                <div class="space-y-4">
                    <div class="bg-[#161B22] p-4 rounded-xl border border-[#2B3543] space-y-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-white text-sm" x-text="selectedCandidate.title"></h4>
                                <span class="text-[#C5A038] font-mono font-bold text-xs" x-text="'Rp ' + Number(selectedCandidate.estimated_price).toLocaleString('id-ID')"></span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                  :class="selectedCandidate.status === 'Disetujui' ? 'bg-emerald-950 text-emerald-400' : (selectedCandidate.status === 'Ditolak' ? 'bg-red-950 text-red-400' : 'bg-blue-950 text-blue-400')"
                                  x-text="selectedCandidate.status"></span>
                        </div>
                        <div class="text-[11px] text-gray-400">
                            Lokasi: <span class="text-gray-300" x-text="selectedCandidate.city + (selectedCandidate.district ? ', ' + selectedCandidate.district : '')"></span>
                        </div>
                        <p class="text-[11px] text-gray-400 italic bg-[#1F2731] p-2 rounded-lg" x-text="selectedCandidate.description || 'Tidak ada deskripsi tambahan.'"></p>
                    </div>

                    @if($isSuperAdmin)
                        <template x-if="selectedCandidate.status === 'Diajukan'">
                            <form :action="'{{ url('/admin/acquisitions/candidates') }}/' + selectedCandidate.id + '/review'" method="POST" class="space-y-3 pt-2">
                                @csrf
                                <div>
                                    <label class="block text-gray-300 font-semibold mb-1">Keputusan Evaluasi <span class="text-red-400">*</span></label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="border border-[#2B3543] rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:border-emerald-500 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-950/20">
                                            <input type="radio" name="status" value="Disetujui" required class="text-emerald-500">
                                            <span class="font-bold text-emerald-400">Setujui Usulan</span>
                                        </label>
                                        <label class="border border-[#2B3543] rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:border-red-500 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-950/20">
                                            <input type="radio" name="status" value="Ditolak" class="text-red-500">
                                            <span class="font-bold text-red-400">Tolak Usulan</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="p-3 bg-[#161B22] rounded-xl border border-[#2B3543] flex items-center gap-2">
                                    <input type="checkbox" name="create_listing" id="create_listing" value="1" checked class="rounded text-[#C5A038] focus:ring-[#C5A038]">
                                    <label for="create_listing" class="text-xs text-gray-300 cursor-pointer">
                                        Otomatis jadikan draft listing di <strong>Manajemen Unit</strong> jika disetujui.
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-gray-300 font-semibold mb-1">Catatan Evaluasi Managing Director</label>
                                    <textarea name="review_notes" rows="2.5" placeholder="Catatan kelayakan lokasi, estimasi nego harga, instruksi survei lanjutan..."
                                              class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#2B3543]">
                                    <button type="button" @click="openReviewModal = false" class="px-4 py-2 bg-[#2B3543] text-gray-300 rounded-xl font-semibold">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-xl font-semibold shadow-md">
                                        Simpan Keputusan
                                    </button>
                                </div>
                            </form>
                        </template>
                    @endif
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
