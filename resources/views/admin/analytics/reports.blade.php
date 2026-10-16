@extends('layouts.admin')

@section('title', 'Generator Laporan CRM')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.analytics.dashboard') }}" class="hover:text-[#C5A038]">Analitik</a>
                <span>/</span>
                <span class="text-gray-200">Generator Laporan</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-[#C5A038]"></i>
                <span>Generator Laporan Resmi & Ekspor Data</span>
            </h1>
            <p class="text-xs text-gray-400">Unduh rekap performa pemasaran properti dalam format Excel/CSV atau cetak laporan resmi untuk evaluasi berkala.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-lg shadow-emerald-600/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-print"></i>
                <span>Format Siap Cetak (PDF / Print)</span>
            </a>

            <a href="{{ route('admin.reports.export.leads', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-[#C5A038] text-xs font-semibold rounded-xl border border-[#2B3543] flex items-center gap-2 transition-all">
                <i class="fa-solid fa-file-excel"></i>
                <span>Ekspor CSV (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Filter Periode Laporan -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs items-end">
            <div>
                <label class="block text-gray-400 font-medium mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
            </div>

            <div>
                <label class="block text-gray-400 font-medium mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white font-bold rounded-xl transition-all shadow-md">
                    Filter Laporan
                </button>
                <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 bg-[#161B22] text-gray-400 hover:text-white rounded-xl border border-[#2B3543]">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Ringkasan Angka Laporan Periode Terpilih -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] text-gray-400 mb-1 flex items-center justify-between">
                <span>Leads Masuk</span>
                <i class="fa-solid fa-users text-blue-400"></i>
            </div>
            <div class="text-2xl font-black text-blue-400 font-mono">{{ $leads->count() }} Prospek</div>
            <div class="text-[10px] text-gray-500 mt-1">{{ $leads->whereNotNull('assigned_to')->count() }} telah memiliki PIC</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] text-gray-400 mb-1 flex items-center justify-between">
                <span>Survei Lapangan</span>
                <i class="fa-solid fa-calendar-check text-purple-400"></i>
            </div>
            <div class="text-2xl font-black text-purple-400 font-mono">{{ $surveys->count() }} Agenda</div>
            <div class="text-[10px] text-gray-500 mt-1">{{ $surveys->where('status', 'Selesai')->count() }} Kunjungan selesai</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] text-gray-400 mb-1 flex items-center justify-between">
                <span>Target Unit Penjualan</span>
                <i class="fa-solid fa-bullseye text-[#C5A038]"></i>
            </div>
            <div class="text-2xl font-black text-[#C5A038] font-mono">{{ $acquisitionTargets->sum('target_units') }} Unit</div>
            <div class="text-[10px] text-gray-400 mt-1">{{ $soldUnits->count() }} unit closing tercapai</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] text-gray-400 mb-1 flex items-center justify-between">
                <span>Unit Closing Terjual</span>
                <i class="fa-solid fa-handshake text-emerald-400"></i>
            </div>
            <div class="text-2xl font-black text-emerald-400 font-mono">{{ $soldUnits->count() }} Unit</div>
            <div class="text-[10px] text-gray-500 mt-1">Transaksi penjualan selesai</div>
        </div>
    </div>

    <!-- Tabel Rekap Target Unit Penjualan -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 bg-[#161B22] border-b border-[#2B3543] flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bullseye text-[#C5A038]"></i>
                <span class="font-bold text-white">Target & Progres Penjualan Unit ({{ $acquisitionTargets->count() }} Periode)</span>
            </div>
            <span class="text-gray-400 text-[11px]">Monitoring kuota target unit penjualan & closing</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-[11px] text-gray-400 uppercase">
                    <tr>
                        <th class="py-2.5 px-4">Nama Target Periode</th>
                        <th class="py-2.5 px-4">Wilayah Fokus</th>
                        <th class="py-2.5 px-4 text-center">Target Unit</th>
                        <th class="py-2.5 px-4 text-center">Realisasi Closing</th>
                        <th class="py-2.5 px-4">Target Nilai Closing</th>
                        <th class="py-2.5 px-4">Status Target</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543]/60">
                    @forelse($acquisitionTargets as $target)
                        @php
                            $soldCount = $target->sold_units_count;
                            $pct = $target->units_progress_percentage;
                        @endphp
                        <tr class="hover:bg-[#232B35]/50">
                            <td class="py-3 px-4">
                                <div class="font-bold text-white">{{ $target->period_name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $target->start_date?->format('d/m/Y') }} - {{ $target->end_date?->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-200">{{ $target->target_region ?? 'Seluruh Wilayah' }}</td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-white">{{ $target->target_units }} Unit</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold font-mono {{ $pct >= 100 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-blue-500/20 text-blue-400' }}">
                                    {{ $soldCount }} Unit ({{ $pct }}%)
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-[#C5A038]">
                                {{ $target->target_value ? 'Rp ' . number_format($target->target_value, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $target->status === 'Aktif' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-gray-700/50 text-gray-400' }}">
                                    {{ $target->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Belum ada target unit penjualan yang ditetapkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pratinjau Tabel Leads Terkait Lengkap dengan PIC -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 bg-[#161B22] border-b border-[#2B3543] flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-address-book text-blue-400"></i>
                <span class="font-bold text-white">Daftar Leads & PIC Marketing ({{ $leads->count() }})</span>
            </div>
            <span class="text-gray-400">Rentang: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
        </div>
        <div class="overflow-x-auto max-h-96">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-[11px] text-gray-400 uppercase">
                    <tr>
                        <th class="py-2.5 px-4">Ref</th>
                        <th class="py-2.5 px-4">Nama Konsumen</th>
                        <th class="py-2.5 px-4">WhatsApp</th>
                        <th class="py-2.5 px-4">Unit Minat</th>
                        <th class="py-2.5 px-4">PIC Marketing</th>
                        <th class="py-2.5 px-4">Sumber</th>
                        <th class="py-2.5 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543]/60">
                    @forelse($leads as $ld)
                        <tr class="hover:bg-[#232B35]/50">
                            <td class="py-2.5 px-4 font-mono font-bold text-[#C5A038]">{{ $ld->reference_code }}</td>
                            <td class="py-2.5 px-4 text-white font-medium">{{ $ld->name ?? '-' }}</td>
                            <td class="py-2.5 px-4 font-mono">{{ $ld->phone }}</td>
                            <td class="py-2.5 px-4">{{ $ld->unit?->title ?? ($ld->unit?->code ?? '-') }}</td>
                            <td class="py-2.5 px-4">
                                @if($ld->assignedUser)
                                    <div class="flex items-center gap-1.5 text-blue-300">
                                        <i class="fa-solid fa-user-check text-[10px]"></i>
                                        <span class="font-medium">{{ $ld->assignedUser->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-500 italic">Belum ada PIC</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 capitalize">{{ $ld->source }}</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    @if($ld->status === 'Survei Terjadwal') bg-purple-500/20 text-purple-300
                                    @elseif($ld->status === 'Closing') bg-emerald-500/20 text-emerald-300
                                    @elseif($ld->status === 'Batal' || $ld->status === 'Tidak Tertarik') bg-red-500/20 text-red-300
                                    @else bg-blue-500/20 text-blue-300 @endif">
                                    {{ $ld->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">Tidak ada data leads pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
