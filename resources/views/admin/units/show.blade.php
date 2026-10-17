@extends('layouts.admin')

@section('title', 'Detail Unit: ' . $unit->title)

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Manajemen Unit</a>
                <span>/</span>
                <span class="text-gray-200">{{ $unit->code }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold text-white">{{ $unit->title }}</h1>
                @if($unit->status == 'Tersedia')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-950/90 text-emerald-400 border border-emerald-500/30">Tersedia</span>
                @elseif($unit->status == 'Booking')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-950/90 text-amber-400 border border-amber-500/30">Booking</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-950/90 text-red-400 border border-red-500/30">Terjual</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($unit->developer)
                <form action="{{ route('admin.units.check-availability', $unit->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-semibold rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer" title="Kirim link verifikasi stok ke developer">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Minta Cek Ketersediaan</span>
                    </button>
                </form>
            @endif

            @if($unit->external_url)
                <a href="{{ $unit->external_url }}" target="_blank"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all flex items-center gap-2" title="Buka listing di website luar">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Listing Eksternal</span>
                </a>
            @endif

            <a href="{{ route('admin.units.edit', $unit->id) }}" 
               class="px-4 py-2 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-semibold rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Unit</span>
            </a>
            <a href="{{ url('/properti/' . $unit->slug) }}" target="_blank"
               class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl border border-[#2B3543] flex items-center gap-2">
                <i class="fa-solid fa-globe text-[#C5A038]"></i>
                <span>Tinjau Publik</span>
            </a>
        </div>
    </div>

    <!-- Alert Modal Hasil Pembuatan Link Cek Ketersediaan (Fase 4B) -->
    @if(session('availability_success'))
        @php $avail = session('availability_success'); @endphp
        <div class="bg-emerald-950/40 border border-emerald-500/40 rounded-2xl p-5 shadow-lg space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-emerald-300 font-bold text-sm">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                    <span>Tautan Cek Ketersediaan Berhasil Dibuat (Berlaku 48 Jam)</span>
                </div>
                <a href="{{ $avail['wa_link'] }}" target="_blank" 
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl flex items-center gap-2 shadow-md transition-all">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Kirim Pesan ke Developer Sekarang</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs pt-2">
                <div class="bg-[#161B22] p-3 rounded-xl border border-[#2B3543]">
                    <span class="text-gray-400 block mb-1">Tautan Publik Konfirmasi Developer:</span>
                    <a href="{{ $avail['confirm_url'] }}" target="_blank" class="text-blue-400 hover:underline font-mono text-[11px] break-all">
                        {{ $avail['confirm_url'] }}
                    </a>
                </div>
                <div class="bg-[#161B22] p-3 rounded-xl border border-[#2B3543]">
                    <span class="text-gray-400 block mb-1">Tujuan Developer:</span>
                    <div class="font-bold text-white">{{ $avail['dev_name'] }} <span class="text-emerald-400 font-mono">({{ $avail['dev_phone'] }})</span></div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Spesifikasi & Galeri Foto -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Quick Info Card -->
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-[#2B3543]">
                    <div>
                        <span class="text-xs text-gray-400 block">Harga Listing</span>
                        <span class="text-2xl font-extrabold text-[#C5A038] font-mono">{{ $unit->formatted_price }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400 block">Tipe Properti</span>
                        <span class="text-sm font-bold text-gray-200">{{ $unit->type }}</span>
                    </div>
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div class="p-3 bg-[#14181E] rounded-xl border border-[#2B3543]">
                        <span class="text-gray-400 block">Kamar Tidur</span>
                        <span class="text-base font-bold text-white">{{ $unit->bedrooms }}</span>
                    </div>
                    <div class="p-3 bg-[#14181E] rounded-xl border border-[#2B3543]">
                        <span class="text-gray-400 block">Kamar Mandi</span>
                        <span class="text-base font-bold text-white">{{ $unit->bathrooms }}</span>
                    </div>
                    <div class="p-3 bg-[#14181E] rounded-xl border border-[#2B3543]">
                        <span class="text-gray-400 block">Luas Tanah</span>
                        <span class="text-base font-bold text-white">{{ $unit->land_area }} m²</span>
                    </div>
                    <div class="p-3 bg-[#14181E] rounded-xl border border-[#2B3543]">
                        <span class="text-gray-400 block">Luas Bangunan</span>
                        <span class="text-base font-bold text-white">{{ $unit->building_area }} m²</span>
                    </div>
                </div>

                <!-- Address & Legal -->
                <div class="pt-2 text-xs space-y-2 text-gray-300">
                    <p><strong class="text-gray-400">Lokasi:</strong> {{ $unit->address ? $unit->address . ', ' : '' }}{{ $unit->district ? $unit->district . ', ' : '' }}{{ $unit->city }}, {{ $unit->province }}</p>
                    <p><strong class="text-gray-400">Sertifikat:</strong> {{ $unit->certificate ?? 'Dalam Proses' }} | <strong class="text-gray-400">Listrik:</strong> {{ $unit->electricity ?? '-' }}</p>
                    <p><strong class="text-gray-400">Developer:</strong> {{ $unit->developer->name ?? 'Independen / Pemilik Langsung' }}</p>
                </div>
            </div>

            <!-- Galeri Foto Multi -->
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-images text-[#C5A038]"></i>
                    <span>Galeri Foto Properti ({{ $unit->photos->count() }})</span>
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @forelse($unit->photos as $photo)
                        <div class="relative group aspect-video rounded-xl overflow-hidden border border-[#2B3543] bg-[#14181E]">
                            <img src="{{ $photo->url }}" class="w-full h-full object-cover">
                            @if($photo->is_primary)
                                <span class="absolute top-2 left-2 bg-[#C5A038] text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">
                                    Foto Utama
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-3 py-8 text-center text-xs text-gray-400">Belum ada foto yang diunggah.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Col: Audit Trail Riwayat Status (Sesuai Spesifikasi 2A) -->
        <div class="space-y-6">
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-[#2B3543] pb-3">
                    <i class="fa-solid fa-clock-rotate-left text-[#C5A038]"></i>
                    <span>Riwayat Perubahan Status (Audit Trail)</span>
                </h3>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-[#2B3543]">
                    @forelse($unit->statusHistories as $history)
                        <div class="relative text-xs">
                            <!-- Bullet -->
                            <div class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-[#C5A038] ring-4 ring-[#1F2731]"></div>
                            
                            <div>
                                <div class="flex items-center gap-1.5 font-bold text-gray-200">
                                    @if($history->from_status)
                                        <span class="text-gray-400 line-through">{{ $history->from_status }}</span>
                                        <i class="fa-solid fa-arrow-right text-[10px] text-gray-500"></i>
                                    @endif
                                    <span class="text-[#C5A038]">{{ $history->to_status }}</span>
                                </div>
                                <p class="text-gray-400 mt-1 text-[11px]">{{ $history->notes ?? 'Tidak ada catatan.' }}</p>
                                <span class="text-[10px] text-gray-400 block mt-1">
                                    Oleh: <strong class="text-gray-400">{{ $history->user->name ?? 'Sistem' }}</strong> • {{ $history->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">Belum ada riwayat perubahan status.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
