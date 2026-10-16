@extends('layouts.admin')

@section('title', 'Executive Dashboard Analitik CRM')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">Executive Analytics</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-[#C5A038]"></i>
                <span>Executive Dashboard & Analitik Bisnis</span>
            </h1>
            <p class="text-xs text-gray-400">Ringkasan performa funnel CRM, efektivitas konversi prospek, dan nilai portfolio aset properti PT Wastu Pratama Wijaya.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.index') }}" 
               class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white text-xs font-semibold rounded-xl shadow-lg shadow-[#C5A038]/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-file-invoice text-sm"></i>
                <span>Generator Laporan Resmi</span>
            </a>
        </div>
    </div>

    <!-- 1. FUNNEL KONVERSI CRM (BPMN PIPELINE) -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-filter text-[#C5A038]"></i>
                    <span>Funnel Konversi Pemasaran (Leads to Closing)</span>
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Alur tahapan prospek dari pesan awal hingga berhasil transaksi unit.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#C5A038]/20 text-[#C5A038] border border-[#C5A038]/30">
                Closing Rate: {{ $closingRate }}%
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2">
            <!-- Step 1: Total Leads Masuk -->
            <div class="bg-[#161B22] border border-[#2B3543] rounded-2xl p-4 text-center relative">
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">1. Total Leads Masuk</div>
                <div class="text-2xl font-black text-white mt-1">{{ $totalLeads }}</div>
                <div class="text-[10px] text-gray-500 mt-1">100% Volume Prospek</div>
                <div class="hidden sm:block absolute -right-3 top-1/2 -translate-y-1/2 z-10 text-gray-600">
                    <i class="fa-solid fa-chevron-right text-base"></i>
                </div>
            </div>

            <!-- Step 2: Prospek Tertarik (Valid) -->
            <div class="bg-[#161B22] border border-blue-500/30 rounded-2xl p-4 text-center relative">
                <div class="text-[11px] font-semibold text-blue-400 uppercase tracking-wider">2. Prospek Tertarik</div>
                <div class="text-2xl font-black text-blue-400 mt-1">{{ $interestedLeads }}</div>
                <div class="text-[10px] text-gray-400 mt-1">{{ $interestRate }}% dari total leads</div>
                <div class="hidden sm:block absolute -right-3 top-1/2 -translate-y-1/2 z-10 text-gray-600">
                    <i class="fa-solid fa-chevron-right text-base"></i>
                </div>
            </div>

            <!-- Step 3: Survei Terlaksana -->
            <div class="bg-[#161B22] border border-purple-500/30 rounded-2xl p-4 text-center relative">
                <div class="text-[11px] font-semibold text-purple-400 uppercase tracking-wider">3. Survei Selesai</div>
                <div class="text-2xl font-black text-purple-400 mt-1">{{ $completedSurveys }}</div>
                <div class="text-[10px] text-gray-400 mt-1">{{ $surveyRate }}% dari prospek tertarik</div>
                <div class="hidden sm:block absolute -right-3 top-1/2 -translate-y-1/2 z-10 text-gray-600">
                    <i class="fa-solid fa-chevron-right text-base"></i>
                </div>
            </div>

            <!-- Step 4: Closing Terjual -->
            <div class="bg-[#161B22] border border-emerald-500/30 rounded-2xl p-4 text-center">
                <div class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider">4. Unit Terjual</div>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ $closedUnits }}</div>
                <div class="text-[10px] text-gray-400 mt-1">Closing Penjualan</div>
            </div>
        </div>
    </div>

    <!-- 2. GRID DUA KOLOM: EFJEKTIVITAS CHANNEL & STATUS ASSET -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Kiri (5 Cols): Efektivitas Channel Sumber Leads -->
        <div class="lg:col-span-5 bg-[#1F2731] border border-[#2B3543] rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-[#2B3543] pb-3">
                <i class="fa-solid fa-chart-pie text-[#C5A038]"></i>
                <span>Efektivitas Sumber Prospek (Channels)</span>
            </h3>

            <div class="space-y-3 pt-2">
                @php
                    $channels = [
                        'whatsapp' => ['name' => 'WhatsApp Gateway', 'icon' => 'fa-brands fa-whatsapp text-emerald-400', 'color' => 'bg-emerald-500'],
                        'website' => ['name' => 'Formulir Website Publik', 'icon' => 'fa-solid fa-globe text-blue-400', 'color' => 'bg-blue-500'],
                        'manual' => ['name' => 'Offline / Telepon / Walk-in', 'icon' => 'fa-solid fa-phone text-amber-400', 'color' => 'bg-amber-500'],
                    ];
                @endphp

                @foreach($channels as $key => $info)
                    @php
                        $cnt = $leadSources[$key] ?? 0;
                        $pct = $totalLeads > 0 ? round(($cnt / $totalLeads) * 100, 1) : 0;
                    @endphp
                    <div class="text-xs space-y-1.5">
                        <div class="flex items-center justify-between text-gray-300">
                            <span class="flex items-center gap-2">
                                <i class="{{ $info['icon'] }}"></i>
                                <span>{{ $info['name'] }}</span>
                            </span>
                            <span class="font-bold text-white font-mono">{{ $cnt }} Leads ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-[#14181E] h-2.5 rounded-full overflow-hidden border border-[#2B3543]">
                            <div class="{{ $info['color'] }} h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Kanan (7 Cols): Nilai Portfolio Aset & Status Unit -->
        <div class="lg:col-span-7 bg-[#1F2731] border border-[#2B3543] rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center justify-between border-b border-[#2B3543] pb-3">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-sack-dollar text-[#C5A038]"></i>
                    <span>Nilai Portfolio & Status Unit Properti</span>
                </span>
                <span class="text-xs text-gray-400 font-mono">Total Unit: {{ $unitsAvailable + $unitsBooked + $unitsSold }}</span>
            </h3>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <div class="bg-[#161B22] p-4 rounded-2xl border border-[#2B3543]">
                    <span class="text-[11px] text-gray-400 block">Total Nilai Listing Aktif (Tersedia)</span>
                    <div class="text-lg font-black text-[#C5A038] font-mono mt-1">
                        Rp {{ number_format($totalPortfolioValue / 1000000000, 2, ',', '.') }} Miliar
                    </div>
                    <span class="text-[10px] text-emerald-400 mt-1 block">● {{ $unitsAvailable }} Unit Siap Dipasarkan</span>
                </div>

                <div class="bg-[#161B22] p-4 rounded-2xl border border-[#2B3543]">
                    <span class="text-[11px] text-gray-400 block">Akumulasi Nilai Terjual (Closing)</span>
                    <div class="text-lg font-black text-emerald-400 font-mono mt-1">
                        Rp {{ number_format($soldPortfolioValue / 1000000000, 2, ',', '.') }} Miliar
                    </div>
                    <span class="text-[10px] text-gray-400 mt-1 block">● {{ $unitsSold }} Unit Berhasil Terjual</span>
                </div>
            </div>

            <!-- Bar Status Unit -->
            <div class="pt-2 text-xs space-y-2">
                <div class="flex justify-between text-gray-400 text-[11px]">
                    <span>Komposisi Status Properti:</span>
                    <span>Tersedia: {{ $unitsAvailable }} • Booking: {{ $unitsBooked }} • Terjual: {{ $unitsSold }}</span>
                </div>
                @php
                    $allU = max(1, ($unitsAvailable + $unitsBooked + $unitsSold));
                    $pctAvail = ($unitsAvailable / $allU) * 100;
                    $pctBook = ($unitsBooked / $allU) * 100;
                    $pctSold = ($unitsSold / $allU) * 100;
                @endphp
                <div class="w-full h-3 rounded-full overflow-hidden flex bg-[#14181E] border border-[#2B3543]">
                    <div style="width: {{ $pctAvail }}%" class="bg-emerald-500" title="Tersedia"></div>
                    <div style="width: {{ $pctBook }}%" class="bg-amber-500" title="Booking"></div>
                    <div style="width: {{ $pctSold }}%" class="bg-red-500" title="Terjual"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. TABEL PERFORMA TIM MARKETING (SUPERADMIN EVALUASI) -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-3xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-[#2B3543]">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-users-gear text-[#C5A038]"></i>
                <span>Evaluasi Performa & Produktivitas Tim Marketing</span>
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">Metrik penugasan leads dan pelaksanaan pendampingan survei lapangan per PIC.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-[11px] text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Nama Petugas Marketing</th>
                        <th class="py-3 px-4">Role / Posisi</th>
                        <th class="py-3 px-4">Leads Ditugaskan</th>
                        <th class="py-3 px-4">Survei Lapangan Terlaksana</th>
                        <th class="py-3 px-4">Status Akun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543]/60">
                    @forelse($marketingStaff as $staff)
                        <tr class="hover:bg-[#232B35]/50 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-white">{{ $staff->name }}</td>
                            <td class="py-3.5 px-4">
                                <span class="uppercase text-[10px] font-bold px-2 py-0.5 rounded {{ $staff->isSuperAdmin() ? 'bg-amber-950 text-[#C5A038] border border-[#C5A038]/30' : 'bg-blue-950 text-blue-300 border border-blue-500/30' }}">
                                    {{ $staff->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-400">{{ $staff->assigned_leads_count }} Leads</td>
                            <td class="py-3.5 px-4 font-mono font-bold text-purple-400">{{ $staff->completed_surveys_count }} Survei Selesai</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-950 text-emerald-300 border border-emerald-500/30">
                                    Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Belum ada data staf.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
