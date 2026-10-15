@extends('layouts.public')

@section('title', ($hero->title ?? 'Agen Properti Indonesia') . ' - PT Wastu Pratama Wijaya')
@section('meta_description', Str::limit(strip_tags($hero->content ?? 'Wastu Property adalah agen properti profesional terpercaya.'), 150))

@section('content')
<div x-data="{
    searchQuery: '{{ request('search') }}',
    selectedLocation: '{{ request('location', 'all') }}',
    selectedType: '{{ request('type', 'all') }}',
    selectedStatus: '{{ request('status', 'all') }}'
}">

    <!-- ==================== HERO SECTION (DATA DINAMIS CMS) ==================== -->
    <section class="relative min-h-[580px] lg:min-h-[660px] flex items-center justify-center overflow-hidden pt-28 pb-16">
        <!-- Background Image with Warm Golden Skyline Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ $hero && $hero->image_path ? asset('storage/' . $hero->image_path) : 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80' }}" 
                 alt="Wastu Property Skyline Banner" 
                 class="w-full h-full object-cover object-center filter brightness-90">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-950/85 via-amber-900/60 to-amber-800/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-black/30"></div>
        </div>

        <div class="max-w-6xl mx-auto px-6 relative z-10 text-white w-full">
            <div class="max-w-2xl space-y-5">
                
                <!-- Tagline Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-400/20 backdrop-blur-md border border-amber-300/30 text-amber-200 text-xs font-semibold tracking-wide uppercase">
                    <i class="fa-solid fa-certificate text-amber-400"></i>
                    <span>{{ $hero->subtitle ?? 'Melayani Dengan Hati' }}</span>
                </div>

                <!-- Hero Title -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-amber-400 font-serif drop-shadow-md">
                    {{ $hero->title ?? 'AGEN PROPERTI INDONESIA' }}
                </h1>

                <!-- Sub-Headline -->
                <p class="text-sm sm:text-base lg:text-lg text-amber-50/90 font-normal leading-relaxed drop-shadow">
                    {{ $hero->content ?? 'Agen Properti Profesional, yang saat ini telah menangani pemasaran unit-unit Properti di Jogja, Bantul, Magelang, Jakarta, Malang, Bali dan kota-kota lainnya.' }}
                </p>

                <!-- Action Button -->
                <div class="pt-3 flex flex-wrap items-center gap-3">
                    <a href="#properti" 
                       class="px-7 py-3 rounded-full bg-[#C5A038] hover:bg-[#B08C2B] text-white text-sm font-semibold shadow-lg shadow-amber-900/40 transition-all hover:scale-105 active:scale-95 inline-flex items-center gap-2">
                        <span>Jelajahi Properti</span>
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    </a>
                    
                    <a href="https://wa.me/{{ $whatsappNumber }}?text=Halo%20Wastu%20Property,%20saya%20ingin%20konsultasi%20properti" 
                       target="_blank"
                       class="px-7 py-3 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white text-sm font-semibold transition-all inline-flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-emerald-400 text-base"></i>
                        <span>Konsultasi Gratis</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== SEARCH & FILTER QUICK BAR ==================== -->
    <section class="relative z-20 -mt-10 sm:-mt-8 px-4">
        <div class="max-w-5xl mx-auto bg-white rounded-3xl sm:rounded-full p-4 sm:p-3 shadow-2xl border border-gray-100">
            <form action="{{ url('/') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 sm:gap-2 items-center">
                <!-- Lokasi -->
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl sm:rounded-full">
                    <i class="fa-solid fa-location-dot text-[#C5A038] text-sm"></i>
                    <div class="w-full">
                        <label class="block text-[10px] uppercase font-bold text-gray-400">Lokasi / Kota</label>
                        <select name="location" class="w-full text-xs font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer">
                            <option value="all">Semua Lokasi</option>
                            @foreach($availableCities as $city)
                                <option value="{{ $city }}" {{ request('location') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tipe Properti -->
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl sm:rounded-full">
                    <i class="fa-solid fa-house text-[#C5A038] text-sm"></i>
                    <div class="w-full">
                        <label class="block text-[10px] uppercase font-bold text-gray-400">Tipe Properti</label>
                        <select name="type" class="w-full text-xs font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer">
                            <option value="all">Semua Tipe</option>
                            @foreach($availableTypes as $t)
                                <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl sm:rounded-full">
                    <i class="fa-solid fa-tags text-[#C5A038] text-sm"></i>
                    <div class="w-full">
                        <label class="block text-[10px] uppercase font-bold text-gray-400">Status</label>
                        <select name="status" class="w-full text-xs font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer">
                            <option value="all">Semua Status</option>
                            <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Booking" {{ request('status') == 'Booking' ? 'selected' : '' }}>Booking</option>
                            <option value="Terjual" {{ request('status') == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                        </select>
                    </div>
                </div>

                <!-- Tombol Cari -->
                <button type="submit" 
                        class="w-full h-full min-h-[46px] rounded-2xl sm:rounded-full bg-[#C5A038] hover:bg-[#B08C2B] text-white font-semibold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-md shadow-amber-600/20 active:scale-95 transition-all cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari Properti</span>
                </button>
            </form>
        </div>
    </section>

    <!-- ==================== PROYEK TERKINI & LISTING PROPERTI (DATABASE REAL) ==================== -->
    <section id="properti" class="max-w-6xl mx-auto px-6 pt-24 pb-12">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-3 justify-center mb-3">
                <span class="w-12 h-0.5 bg-[#C5A038]"></span>
                <span class="text-xs font-extrabold uppercase tracking-widest text-gray-800">PROYEK TERKINI & LISTING UNIT</span>
                <span class="w-12 h-0.5 bg-[#C5A038]"></span>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                Unit properti pilihan yang telah terverifikasi legalitasnya dan siap Anda miliki dengan skema cash maupun pendampingan KPR.
            </p>
        </div>

        <!-- Property Grid Dinamis dari Database CRM -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            @forelse($units as $unit)
                <x-property-card 
                    :code="$unit->code"
                    :title="$unit->title"
                    :price="$unit->formatted_price"
                    :priceNumeric="(int)$unit->price"
                    :location="($unit->district ? $unit->district . ', ' : '') . $unit->city"
                    :type="$unit->type"
                    :status="$unit->status"
                    :beds="$unit->bedrooms"
                    :baths="$unit->bathrooms"
                    :buildingArea="$unit->building_area"
                    :landArea="$unit->land_area"
                    :imageUrl="$unit->primary_photo_url"
                    :whatsappNumber="$whatsappNumber"
                />
            @empty
                <div class="col-span-3 py-16 text-center">
                    <div class="w-16 h-16 rounded-full bg-amber-50 text-[#C5A038] flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-house-circle-xmark text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800 mb-1">Tidak Ada Properti yang Sesuai</h3>
                    <p class="text-xs text-gray-500">Coba ganti filter kota atau tipe properti Anda.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="pt-8 flex justify-center">
            {{ $units->fragment('properti')->links() }}
        </div>
    </section>

    <!-- ==================== TENTANG KAMI & VISI MISI (DATA DINAMIS CMS) ==================== -->
    <section id="tentang-kami" class="bg-amber-50/40 py-20 border-y border-amber-100/60">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Image Collage -->
                <div class="relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white aspect-[4/3]">
                        <img src="{{ $about && $about->image_path ? asset('storage/' . $about->image_path) : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80' }}" 
                             alt="Kantor Wastu Property" 
                             class="w-full h-full object-cover">
                    </div>
                    <!-- Stat Card Overlay -->
                    <div class="absolute -bottom-6 -right-6 bg-white rounded-2xl p-6 shadow-xl border border-amber-100 hidden sm:block">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-[#C5A038]">
                                <i class="fa-solid fa-award text-2xl"></i>
                            </div>
                            <div>
                                <span class="text-2xl font-black text-gray-900 block font-serif">100+ Proyek</span>
                                <span class="text-xs text-gray-500">Terpercaya di Seluruh Indonesia</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Text Content -->
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2">
                        <span class="w-8 h-0.5 bg-[#C5A038]"></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-[#C5A038]">TENTANG KAMI</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-bold text-gray-900 font-serif leading-tight">
                        {{ $about->title ?? 'Mitra Akuisisi & Pemasaran Properti Terpercaya Anda' }}
                    </h2>

                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                        {{ $about->content ?? 'PT. Wastu Pratama Wijaya (Wastu Property) adalah perusahaan agen perumahan dan pengembang properti profesional yang berbasis di Sleman, Daerah Istimewa Yogyakarta. Kami memiliki komitmen penuh dalam memberikan solusi komprehensif mulai dari akuisisi unit, pemasaran, hingga konsultasi kepemilikan properti.' }}
                    </p>

                    <!-- Visi & Misi Box -->
                    <div id="visi-misi" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-white border border-amber-100/80 shadow-sm">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-[#C5A038] mb-2.5">
                                <i class="fa-solid fa-bullseye text-sm"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 mb-1">Visi Kami</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $visionMissionData['vision'] ?? 'Menjadi agen dan pengembang properti terdepan di Indonesia yang berintegritas dan melayani dengan hati.' }}</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-amber-100/80 shadow-sm">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-[#C5A038] mb-2.5">
                                <i class="fa-solid fa-handshake-angle text-sm"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 mb-1">Misi Kami</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $visionMissionData['mission'] ?? 'Memberikan pendampingan transaksi properti yang transparan, aman, dan berdaya guna bagi konsumen serta developer mitra.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== LAYANAN KAMI (DATA DINAMIS CMS) ==================== -->
    <section id="layanan" class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center max-w-xl mx-auto mb-14">
            <div class="inline-flex items-center gap-2 justify-center mb-2">
                <span class="w-8 h-0.5 bg-[#C5A038]"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-[#C5A038]">LAYANAN KAMI</span>
                <span class="w-8 h-0.5 bg-[#C5A038]"></span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 font-serif">Solusi Properti Terlengkap</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($services as $serv)
                <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] group-hover:bg-[#C5A038] group-hover:text-white flex items-center justify-center mb-5 transition-colors">
                        <i class="{{ $serv->icon ?? 'fa-solid fa-house' }} text-lg"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">{{ $serv->title }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ $serv->description }}</p>
                </div>
            @empty
                <div class="col-span-4 text-center py-6 text-xs text-gray-400">Belum ada layanan yang ditambahkan di CMS.</div>
            @endforelse
        </div>
    </section>

    <!-- ==================== FAQ SECTION (DATA DINAMIS CMS) ==================== -->
    <section id="faq" class="max-w-4xl mx-auto px-6 py-16">
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 justify-center mb-2">
                <span class="w-8 h-0.5 bg-[#C5A038]"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-[#C5A038]">TANYA JAWAB (FAQ)</span>
                <span class="w-8 h-0.5 bg-[#C5A038]"></span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 font-serif">Pertanyaan yang Sering Diajukan</h2>
        </div>

        <div class="space-y-3" x-data="{ openFaq: null }">
            @forelse($faqs as $fIndex => $faq)
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                    <button @click="openFaq = (openFaq === {{ $fIndex }} ? null : {{ $fIndex }})" 
                            class="w-full px-6 py-4 text-left flex items-center justify-between font-bold text-xs sm:text-sm text-gray-800 hover:text-[#C5A038]">
                        <span>{{ $faq->question }}</span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="{ 'rotate-180': openFaq === {{ $fIndex }} }"></i>
                    </button>
                    <div x-show="openFaq === {{ $fIndex }}" class="px-6 pb-5 text-xs text-gray-600 leading-relaxed border-t border-gray-50 pt-3" style="display: none;">
                        {{ $faq->answer }}
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-xs text-gray-400">Belum ada daftar FAQ.</div>
            @endforelse
        </div>
    </section>

    <!-- ==================== ARTIKEL TERBARU ==================== -->
    <section id="artikel" class="bg-gray-50 py-20 border-t border-gray-200/60">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-16 h-1 bg-[#F8B803] rounded-full"></div>
                <h2 class="text-xs sm:text-sm font-extrabold tracking-widest uppercase text-gray-800">
                    ARTIKEL TERBARU
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80" 
                             alt="Perbedaan Letter C dan Girik" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-slate-700/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">BLOG</span>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-[#C5A038] transition-colors leading-snug line-clamp-2">Perbedaan Letter C dan Girik yang Jarang Diketahui Orang</h3>
                        <div class="mt-auto pt-3 border-t border-gray-50 flex flex-col gap-2">
                            <span class="text-[11px] font-bold text-[#F8B803] uppercase">READ MORE &raquo;</span>
                            <span class="text-[10px] text-gray-400">August 15, 2026</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=600&q=80" 
                             alt="Kenapa Letter C Sering Disebut Setengah Sertifikat" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-slate-700/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">BLOG</span>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-[#C5A038] transition-colors leading-snug line-clamp-2">Kenapa Letter C Sering Disebut 'Setengah Sertifikat'?</h3>
                        <div class="mt-auto pt-3 border-t border-gray-50 flex flex-col gap-2">
                            <span class="text-[11px] font-bold text-[#F8B803] uppercase">READ MORE &raquo;</span>
                            <span class="text-[10px] text-gray-400">August 14, 2026</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=600&q=80" 
                             alt="Letter C Tanah Masih Berlaku" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-slate-700/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">BLOG</span>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-[#C5A038] transition-colors leading-snug line-clamp-2">Letter C Tanah Masih Berlaku? Ini Jawabannya Menurut Hukum Agraria</h3>
                        <div class="mt-auto pt-3 border-t border-gray-50 flex flex-col gap-2">
                            <span class="text-[11px] font-bold text-[#F8B803] uppercase">READ MORE &raquo;</span>
                            <span class="text-[10px] text-gray-400">August 13, 2026</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80" 
                             alt="Contoh Letter C Tanah" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-slate-700/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">BLOG</span>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-[#C5A038] transition-colors leading-snug line-clamp-2">Contoh Letter C Tanah dan Cara Membacanya untuk Orang Awam</h3>
                        <div class="mt-auto pt-3 border-t border-gray-50 flex flex-col gap-2">
                            <span class="text-[11px] font-bold text-[#F8B803] uppercase">READ MORE &raquo;</span>
                            <span class="text-[10px] text-gray-400">August 12, 2026</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== KEMITRAAN & CALL TO ACTION ==================== -->
    <section id="kemitraan" class="max-w-6xl mx-auto px-6 py-20">
        <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-amber-950 rounded-3xl p-8 sm:p-14 text-white relative overflow-hidden shadow-2xl">
            <div class="max-w-xl space-y-4 relative z-10">
                <span class="text-xs font-bold uppercase tracking-widest text-[#C5A038]">KEMITRAAN DEVELOPER</span>
                <h3 class="text-2xl sm:text-4xl font-bold font-serif leading-tight">
                    Anda Developer Properti? Mari Bersinergi Bersama Wastu Property.
                </h3>
                <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                    Percepat penjualan unit proyek perumahan atau villa Anda bersama tim pemasar profesional kami yang berpengalaman dan memiliki jangkauan pembeli potensial di berbagai kota.
                </p>
                <div class="pt-4 flex flex-wrap gap-3">
                    <a href="https://wa.me/{{ $whatsappNumber }}?text=Halo%20Wastu%20Property,%20saya%20developer%20ingin%20mengajukan%20kerja%20sama%20pemasaran" 
                       target="_blank"
                       class="px-7 py-3 rounded-full bg-[#C5A038] hover:bg-[#B08C2B] text-white font-semibold text-xs uppercase tracking-wider shadow-lg transition-all inline-flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span>Ajukan Kemitraan</span>
                    </a>
                </div>
            </div>
            <div class="absolute -right-10 -bottom-10 opacity-10 text-white pointer-events-none">
                <i class="fa-solid fa-city text-[280px]"></i>
            </div>
        </div>
    </section>

    <!-- ==================== KONTAK SECTION ==================== -->
    <section id="kontak" class="max-w-6xl mx-auto px-6 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-map-pin text-xl"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-1">Kantor Pusat</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ $settings['office_address'] ?? 'Jl. Green Hills Ambarukmo No. A14, Condongcatur, Depok, Sleman, DIY 55281' }}</p>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-phone text-xl"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-1">Telepon & WhatsApp</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">+{{ $whatsappNumber }} (Customer Care)<br>{{ $settings['office_phone'] ?? '+62 274-123456' }} (Office)</p>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-envelope text-xl"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-1">Email Resmi</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ $settings['contact_email'] ?? 'info@wastuproperty.co.id' }}</p>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
