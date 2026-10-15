@extends('layouts.admin')

@section('title', 'Manajemen Unit Properti - Dark Dashboard')

@section('content')
<div class="space-y-6">

    <!-- ================= TOP CONTROLS & KPI ROW (SESUAI GAMBAR REFERENSI) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- 1. Dropdown Filters (Provinsi, Kota/Kabupaten, Kecamatan) -->
        <div class="lg:col-span-3 space-y-3">
            <form id="filterForm" action="{{ route('admin.units.index') }}" method="GET" class="space-y-3">
                <!-- Provinsi -->
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl px-4 py-2.5 flex items-center justify-between shadow-sm">
                    <span class="text-xs font-semibold text-gray-300">Provinsi</span>
                    <select name="province" onchange="this.form.submit()" class="bg-transparent text-xs text-gray-400 focus:outline-none border-none text-right cursor-pointer">
                        <option value="all" class="bg-[#1F2731] text-gray-200">Semua</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }} class="bg-[#1F2731] text-gray-200">{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kota/Kabupaten -->
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl px-4 py-2.5 flex items-center justify-between shadow-sm">
                    <span class="text-xs font-semibold text-gray-300">Kota / Kab</span>
                    <select name="district" onchange="this.form.submit()" class="bg-transparent text-xs text-gray-400 focus:outline-none border-none text-right cursor-pointer">
                        <option value="all" class="bg-[#1F2731] text-gray-200">Semua</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ request('district') == $c ? 'selected' : '' }} class="bg-[#1F2731] text-gray-200">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kecamatan -->
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl px-4 py-2.5 flex items-center justify-between shadow-sm">
                    <span class="text-xs font-semibold text-gray-300">Kecamatan</span>
                    <select name="subdistrict" onchange="this.form.submit()" class="bg-transparent text-xs text-gray-400 focus:outline-none border-none text-right cursor-pointer">
                        <option value="all" class="bg-[#1F2731] text-gray-200">Semua</option>
                        @foreach($districts as $d)
                            <option value="{{ $d }}" {{ request('subdistrict') == $d ? 'selected' : '' }} class="bg-[#1F2731] text-gray-200">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- 2. Mini Table Units Ringkas -->
        <div class="lg:col-span-5 bg-[#1F2731] border border-[#2B3543] rounded-2xl p-4 shadow-sm flex flex-col justify-between overflow-hidden">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-[#2B3543]">
                <span class="text-xs font-bold text-gray-200 uppercase tracking-wider flex items-center gap-1.5">
                    <span>Unit</span>
                    <i class="fa-solid fa-caret-up text-[#C5A038]"></i>
                </span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Data Listing</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</span>
            </div>

            <div class="divide-y divide-[#2B3543]/60 text-xs">
                @forelse($units->take(4) as $index => $u)
                    <div class="py-2 flex items-center justify-between gap-2 hover:bg-[#28323E] px-2 rounded-lg transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="text-[11px] font-mono text-gray-400 w-3">{{ $index + 1 }}</span>
                            <span class="font-medium text-gray-200 truncate">{{ $u->title }}</span>
                        </div>
                        <a href="{{ route('admin.units.show', $u->id) }}" class="text-[11px] text-blue-400 hover:underline truncate max-w-[130px]">
                            {{ url('/properti/' . $u->slug) }}
                        </a>
                        <div>
                            @if($u->status == 'Tersedia')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">Tersedia</span>
                            @elseif($u->status == 'Booking')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-950/80 text-amber-400 border border-amber-500/30">Booking</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-950/80 text-red-400 border border-red-500/30">Terjual</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-xs text-gray-400">Belum ada unit terdaftar.</div>
                @endforelse
            </div>
        </div>

        <!-- 3. Status Donut Chart (Sesuai Screenshot) -->
        <div class="lg:col-span-2 bg-[#1F2731] border border-[#2B3543] rounded-2xl p-4 shadow-sm flex flex-col items-center justify-center relative">
            <span class="text-xs font-bold text-gray-300 absolute top-3.5 left-4">Status</span>
            <div class="w-24 h-24 mt-4">
                <canvas id="statusDonutChart"></canvas>
            </div>
        </div>

        <!-- 4. Total Counter Unit (Sesuai Screenshot: Unit 61) -->
        <div class="lg:col-span-2 bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm flex flex-col justify-center items-center text-center">
            <span class="text-xs font-bold text-gray-300 uppercase tracking-widest mb-1">Unit</span>
            <span class="text-5xl font-black text-white font-mono tracking-tight">{{ $totalUnits }}</span>
            <span class="text-[10px] text-gray-400 mt-1">Total Unit Terdata</span>
        </div>

    </div>

    <!-- ================= MIDDLE ROW: CHARTS & INTERACTIVE MAP ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Column: Horizontal Bar (Kecamatan) & Listing Bulanan -->
        <div class="lg:col-span-6 space-y-5">
            <!-- Kecamatan Horizontal Bars (Sesuai Screenshot) -->
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-8 h-3 rounded-sm bg-blue-600 inline-block"></span>
                    <h3 class="text-xs font-bold text-gray-200">Kecamatan</h3>
                </div>
                
                <div class="space-y-3">
                    @forelse($districtsStats as $stat)
                        <div class="flex items-center gap-3 text-xs">
                            <span class="w-16 text-gray-400 truncate">{{ $stat->district }}</span>
                            <div class="flex-grow bg-[#14181E] h-4 rounded-sm overflow-hidden">
                                @php
                                    $max = $districtsStats->max('total') ?: 1;
                                    $pct = ($stat->total / $max) * 100;
                                @endphp
                                <div class="bg-blue-600 h-full rounded-sm transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-gray-300 font-mono text-[11px] w-6 text-right">{{ $stat->total }}</span>
                        </div>
                    @empty
                        <div class="text-xs text-gray-400 py-2">Data kecamatan belum tersedia.</div>
                    @endforelse
                </div>
            </div>

            <!-- Listing Bulanan Bar Chart (Sesuai Screenshot) -->
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-8 h-3 rounded-sm bg-[#F5DE88] inline-block"></span>
                    <h3 class="text-xs font-bold text-gray-200">Listing Bulanan</h3>
                </div>
                <div class="h-40">
                    <canvas id="monthlyListingChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column: Interactive Map (Sesuai Screenshot Map Sebaran Unit) -->
        <div class="lg:col-span-6 bg-[#1F2731] border border-[#2B3543] rounded-2xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-200 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-[#C5A038]"></i>
                    <span>Peta Sebaran Properti</span>
                </span>
                <span class="text-[10px] text-gray-400">Yogyakarta & Sekitarnya</span>
            </div>

            <!-- Map Container -->
            <div id="unitMap" class="w-full h-80 rounded-xl overflow-hidden border border-[#2B3543] z-10"></div>

            <!-- Subdistrict Legend (Bahasa Indonesia) -->
            <div class="pt-4 flex items-center gap-4 text-[11px] flex-wrap">
                <span class="text-gray-400 font-semibold">Kecamatan:</span>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span><span class="text-gray-300">Borobudur</span></div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span><span class="text-gray-300">Ngagilik</span></div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="text-gray-300">Depok</span></div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span><span class="text-gray-300">Kasihan</span></div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span><span class="text-gray-300">Batu</span></div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span><span class="text-gray-300">Kotagede</span></div>
            </div>
        </div>

    </div>

    <!-- ================= FULL CRUD DATA TABLE SECTION ================= -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
        
        <!-- Table Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-[#C5A038]"></i>
                    <span>Tabel Kelola Semua Unit Properti</span>
                </h2>
                <p class="text-xs text-gray-400">Pencarian, filter status, tipe properti, dan otorisasi CRUD.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.units.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-semibold shadow-md shadow-amber-900/30 transition-all hover:scale-105 active:scale-95">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Unit Baru</span>
                </a>
            </div>
        </div>

        <!-- Filter Search & Dropdowns -->
        <form action="{{ route('admin.units.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2">
            <div class="sm:col-span-5 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode unit, judul, kota, atau developer..." 
                       class="w-full pl-9 pr-4 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 placeholder-gray-400 focus:outline-none focus:border-[#C5A038]">
            </div>

            <div class="sm:col-span-3">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-300 focus:outline-none">
                    <option value="all">Semua Status</option>
                    <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Booking" {{ request('status') == 'Booking' ? 'selected' : '' }}>Booking</option>
                    <option value="Terjual" {{ request('status') == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-300 focus:outline-none">
                    <option value="all">Semua Tipe</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full h-full min-h-[36px] bg-[#28323E] hover:bg-[#34404F] text-gray-200 text-xs font-semibold rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </form>

        <!-- Table View -->
        <div class="overflow-x-auto rounded-xl border border-[#2B3543]">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-gray-400 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Unit</th>
                        <th class="py-3 px-4">Tipe & Lokasi</th>
                        <th class="py-3 px-4">Harga</th>
                        <th class="py-3 px-4">Spesifikasi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Publish</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543] text-gray-300">
                    @forelse($units as $unit)
                        <tr class="hover:bg-[#28323E]/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-10 rounded-lg bg-[#14181E] overflow-hidden shrink-0 border border-[#2B3543]">
                                        <img src="{{ $unit->primary_photo_url }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.units.show', $unit->id) }}" class="font-bold text-white hover:text-[#C5A038] transition-colors block">
                                            {{ $unit->title }}
                                        </a>
                                        <span class="text-[10px] font-mono text-[#C5A038]">{{ $unit->code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-gray-200 block">{{ $unit->type }}</span>
                                <span class="text-[11px] text-gray-400">{{ $unit->district ? $unit->district . ', ' : '' }}{{ $unit->city }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-amber-400 font-mono">
                                {{ $unit->formatted_price }}
                            </td>
                            <td class="py-3.5 px-4 text-[11px] text-gray-400">
                                <span>{{ $unit->bedrooms }} KT / {{ $unit->bathrooms }} KM</span>
                                <span class="block">LT: {{ $unit->land_area }}m² | LB: {{ $unit->building_area }}m²</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($unit->status == 'Tersedia')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">Tersedia</span>
                                @elseif($unit->status == 'Booking')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-950/80 text-amber-400 border border-amber-500/30">Booking</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-950/80 text-red-400 border border-red-500/30">Terjual</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($unit->is_published)
                                    <span class="text-emerald-400 text-xs" title="Tayang di website publik"><i class="fa-solid fa-circle-check"></i> Published</span>
                                @else
                                    <span class="text-gray-400 text-xs" title="Disembunyikan"><i class="fa-solid fa-eye-slash"></i> Draft</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.units.show', $unit->id) }}" class="p-1.5 rounded-lg bg-[#14181E] text-gray-300 hover:text-white hover:bg-[#28323E]" title="Detail Unit">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.units.edit', $unit->id) }}" class="p-1.5 rounded-lg bg-[#14181E] text-amber-400 hover:text-amber-300 hover:bg-[#28323E]" title="Edit Unit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    
                                    <!-- Delete (Policy: Admin/Superadmin soft delete, Superadmin can force delete) -->
                                    <form action="{{ route('admin.units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-[#14181E] text-red-400 hover:text-red-300 hover:bg-[#28323E]" title="Hapus Unit">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Tidak ada data unit properti yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $units->links() }}
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Donut Chart Status (Sesuai Screenshot Dark Dashboard)
        const ctxDonut = document.getElementById('statusDonutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Tersedia', 'Booking', 'Terjual'],
                datasets: [{
                    data: [{{ $statusAvailable }}, {{ $statusBooking }}, {{ $statusSold }}],
                    backgroundColor: ['#A3D9A5', '#F5DE88', '#B0C4DE'],
                    borderColor: '#1F2731',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '65%'
            }
        });

        // 2. Bar Chart Listing Bulanan
        const ctxBar = document.getElementById('monthlyListingChart').getContext('2d');
        const months = {!! json_encode($monthlyListings->pluck('month_name')) !!};
        const counts = {!! json_encode($monthlyListings->pluck('count')) !!};

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: months.length ? months : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Listing',
                    data: counts.length ? counts : [25, 10, 15, 30, 20, 18],
                    backgroundColor: '#F5DE88',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#9CA3AF', font: { size: 10 } } },
                    y: { grid: { color: '#2B3543' }, ticks: { color: '#9CA3AF', font: { size: 10 } } }
                },
                plugins: { legend: { display: false } }
            }
        });

        // 3. Interactive Leaflet Map Sebaran Unit (Sesuai Screenshot)
        const map = L.map('unitMap').setView([-7.7956, 110.3695], 11); // Center Jogja
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Contoh Marker Sebaran
        const markers = [
            { lat: -7.6079, lng: 110.2038, title: 'Casa de Borobudur 2', color: '#EF4444' },
            { lat: -7.7470, lng: 110.3950, title: 'Green Hills Residence', color: '#10B981' },
            { lat: -7.8300, lng: 110.3300, title: 'Villa Etnik Kasongan', color: '#D97706' },
            { lat: -7.7820, lng: 110.3700, title: 'Kavling Kotabaru', color: '#3B82F6' }
        ];

        markers.forEach(m => {
            const circle = L.circleMarker([m.lat, m.lng], {
                radius: 7,
                fillColor: m.color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.85
            }).addTo(map);
            circle.bindPopup(`<strong>${m.title}</strong>`);
        });
    });
</script>
@endpush
@endsection
