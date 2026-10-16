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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] text-gray-400 mb-1">Leads Masuk Periode Ini</div>
            <div class="text-2xl font-black text-blue-400 font-mono">{{ $leads->count() }} Prospek</div>
            <div class="text-[10px] text-gray-500 mt-1">Konsumen baru tercatat</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] text-gray-400 mb-1">Survei Lapangan Terjadwal</div>
            <div class="text-2xl font-black text-purple-400 font-mono">{{ $surveys->count() }} Agenda</div>
            <div class="text-[10px] text-gray-500 mt-1">{{ $surveys->where('status', 'Selesai')->count() }} Kunjungan selesai</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] text-gray-400 mb-1">Unit Berhasil Closing</div>
            <div class="text-2xl font-black text-emerald-400 font-mono">{{ $soldUnits->count() }} Unit</div>
            <div class="text-[10px] text-gray-500 mt-1">Transaksi penjualan selesai</div>
        </div>
    </div>

    <!-- Pratinjau Tabel Leads Terkait -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 bg-[#161B22] border-b border-[#2B3543] flex items-center justify-between text-xs">
            <span class="font-bold text-white">Daftar Leads Periode ({{ $leads->count() }})</span>
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
                        <th class="py-2.5 px-4">Sumber</th>
                        <th class="py-2.5 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543]/60">
                    @forelse($leads->take(15) as $ld)
                        <tr class="hover:bg-[#232B35]/50">
                            <td class="py-2.5 px-4 font-mono font-bold text-[#C5A038]">{{ $ld->reference_code }}</td>
                            <td class="py-2.5 px-4 text-white">{{ $ld->name ?? '-' }}</td>
                            <td class="py-2.5 px-4 font-mono">{{ $ld->phone }}</td>
                            <td class="py-2.5 px-4">{{ $ld->unit?->code ?? '-' }}</td>
                            <td class="py-2.5 px-4 capitalize">{{ $ld->source }}</td>
                            <td class="py-2.5 px-4">{{ $ld->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada data leads pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
