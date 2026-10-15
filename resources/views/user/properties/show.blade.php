@extends('layouts.public')

@section('title', $unit->title . ' (' . $unit->code . ') - Wastu Property')
@section('meta_description', Str::limit(strip_tags($unit->description ?? "Properti {$unit->title} di {$unit->city} dengan harga {$unit->formatted_price}."), 150))

@section('content')
<div class="pt-28 pb-16 max-w-6xl mx-auto px-6 space-y-8">
    
    <!-- Breadcrumb & Title Header -->
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <a href="{{ url('/') }}" class="hover:text-[#C5A038]">Home</a>
            <span>/</span>
            <a href="{{ url('/properti') }}" class="hover:text-[#C5A038]">Daftar Properti</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">{{ $unit->title }}</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $unit->status === 'Tersedia' ? 'bg-emerald-500 text-white' : ($unit->status === 'Booking' ? 'bg-amber-500 text-white' : 'bg-red-500 text-white') }}">
                        {{ $unit->status }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px] font-mono font-bold">
                        {{ $unit->code }}
                    </span>
                    <span class="text-xs text-gray-500">
                        <i class="fa-solid fa-location-dot text-[#C5A038] mr-1"></i>
                        {{ $unit->district ? $unit->district . ', ' : '' }}{{ $unit->city }}, {{ $unit->province }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 font-serif">{{ $unit->title }}</h1>
            </div>

            <div class="text-left md:text-right">
                <span class="text-xs text-gray-400 block font-medium">Harga Penawaran</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-[#C5A038] font-mono tracking-tight">{{ $unit->formatted_price }}</span>
            </div>
        </div>
    </div>

    <!-- Gallery & Detail Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left 8 Cols: Foto Galeri & Spesifikasi -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Main Photo Slider / Grid -->
            <div class="space-y-3">
                <div class="aspect-[16/10] rounded-3xl overflow-hidden bg-gray-100 shadow-md border border-gray-100">
                    <img src="{{ $unit->primary_photo_url }}" alt="{{ $unit->title }}" class="w-full h-full object-cover">
                </div>

                @if($unit->photos->count() > 1)
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                        @foreach($unit->photos as $p)
                            <div class="aspect-video rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                                <img src="{{ $p->url }}" class="w-full h-full object-cover hover:scale-105 transition-transform cursor-pointer">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Spesifikasi Box -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Spesifikasi Properti</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <span class="text-gray-400 block mb-1">Kamar Tidur</span>
                        <span class="text-base font-extrabold text-gray-800"><i class="fa-solid fa-bed text-[#C5A038] mr-1.5"></i>{{ $unit->bedrooms }}</span>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <span class="text-gray-400 block mb-1">Kamar Mandi</span>
                        <span class="text-base font-extrabold text-gray-800"><i class="fa-solid fa-bath text-[#C5A038] mr-1.5"></i>{{ $unit->bathrooms }}</span>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <span class="text-gray-400 block mb-1">Luas Tanah</span>
                        <span class="text-base font-extrabold text-gray-800"><i class="fa-solid fa-vector-square text-[#C5A038] mr-1.5"></i>{{ $unit->land_area }} m²</span>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <span class="text-gray-400 block mb-1">Luas Bangunan</span>
                        <span class="text-base font-extrabold text-gray-800"><i class="fa-solid fa-ruler-combined text-[#C5A038] mr-1.5"></i>{{ $unit->building_area }} m²</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-600 border-t border-gray-100 pt-4">
                    <p><strong>Legalitas Sertifikat:</strong> {{ $unit->certificate ?? 'SHM (Sertifikat Hak Milik)' }}</p>
                    <p><strong>Daya Listrik:</strong> {{ $unit->electricity ?? '2200 VA' }}</p>
                    <p><strong>Developer:</strong> {{ $unit->developer->name ?? 'Independen / Pemilik Langsung' }}</p>
                    <p><strong>Alamat:</strong> {{ $unit->address ?? ($unit->city . ', ' . $unit->province) }}</p>
                </div>

                @if($unit->description)
                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Deskripsi Lengkap</h4>
                        <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">{{ $unit->description }}</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 4 Cols: CTA WhatsApp & Contact Box -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Sticky Contact Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-xl space-y-5 sticky top-28">
                <div>
                    <span class="text-[11px] font-bold text-[#C5A038] uppercase tracking-wider block mb-1">Tertarik dengan Unit Ini?</span>
                    <h3 class="text-lg font-bold text-gray-900 leading-snug">Hubungi Tim Marketing Wastu Property</h3>
                    <p class="text-xs text-gray-500 mt-1">Dapatkan jadwal survei lokasi dan konsultasi skema pembayaran (Cash / KPR).</p>
                </div>

                <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-100 text-xs text-gray-700">
                    <p class="font-semibold text-gray-900 mb-1">Pesan Otomatis WhatsApp:</p>
                    <p class="text-[11px] italic text-gray-600 bg-white p-2.5 rounded-xl border border-amber-200">
                        "{{ $waMessage }}"
                    </p>
                </div>

                <!-- Tombol Utama Direct WhatsApp (Click to chat) -->
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode($waMessage) }}" 
                   target="_blank"
                   class="w-full py-3.5 px-4 rounded-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    <span>Hubungi via WhatsApp</span>
                </a>

                <div class="pt-2 text-center text-[11px] text-gray-400">
                    <p><i class="fa-solid fa-shield-halved text-[#C5A038] mr-1"></i> Transaksi Aman & Terverifikasi AREBI</p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
