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
            
            <!-- Sticky Contact Card & Formulir Minat -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-xl space-y-5 sticky top-28">
                <div>
                    <span class="text-[11px] font-bold text-[#C5A038] uppercase tracking-wider block mb-1">Formulir Minat & Survei</span>
                    <h3 class="text-lg font-bold text-gray-900 leading-snug">Jadwalkan Kunjungan Unit</h3>
                    <p class="text-xs text-gray-500 mt-1">Isi formulir singkat di bawah ini untuk mendapatkan nomor referensi resmi dan terhubung langsung ke marketing.</p>
                </div>

                @if($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulir Minat Konsumen (Fase 3C) -->
                <form action="{{ route('user.properties.interest', $unit->slug) }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Santoso" value="{{ old('name') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-[#C5A038] focus:border-[#C5A038] transition-all bg-gray-50/50">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor WhatsApp Aktif <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" required placeholder="Contoh: 081234567890" value="{{ old('phone') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-[#C5A038] focus:border-[#C5A038] transition-all bg-gray-50/50">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Pertanyaan / Rencana Survei (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Contoh: Apakah bisa survei hari Sabtu? Simulasi KPR DP 10%?"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-[#C5A038] focus:border-[#C5A038] transition-all bg-gray-50/50">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 px-4 rounded-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                        <span>Kirim Minat & Chat WhatsApp</span>
                    </button>
                </form>

                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink mx-3 text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Atau Chat Langsung</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- Tombol Chat Cepat -->
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode($waMessage) }}" 
                   target="_blank"
                   class="w-full py-2.5 px-4 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs text-gray-500"></i>
                    <span>Tanya Cepat Tanpa Formulir</span>
                </a>

                <div class="pt-2 text-center text-[11px] text-gray-400">
                    <p><i class="fa-solid fa-shield-halved text-[#C5A038] mr-1"></i> Data Anda Aman & Terverifikasi AREBI</p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
