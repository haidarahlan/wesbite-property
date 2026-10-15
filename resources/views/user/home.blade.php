@extends('layouts.public')

@section('title', 'Wastu Property - Agen Properti Profesional Indonesia | PT. Wastu Pratama Wijaya')

@section('content')
<div x-data="{
    searchQuery: '',
    selectedLocation: 'all',
    selectedType: 'all',
    selectedStatus: 'all'
}">

    <!-- ==================== HERO SECTION ==================== -->
    <section class="relative min-h-[580px] lg:min-h-[660px] flex items-center justify-center overflow-hidden pt-28 pb-16">
        <!-- Background Image with Warm Golden Skyline Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80" 
                 alt="Wastu Property Skyline Banner" 
                 class="w-full h-full object-cover object-center filter brightness-90">
            <!-- Warm Sunset Golden Gradient Filter (sesuai foto 1) -->
            <div class="absolute inset-0 bg-gradient-to-r from-amber-950/85 via-amber-900/60 to-amber-800/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-black/30"></div>
        </div>

        <div class="max-w-6xl mx-auto px-6 relative z-10 text-white w-full">
            <div class="max-w-2xl space-y-5 animate-fade-in">
                
                <!-- Tagline Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-400/20 backdrop-blur-md border border-amber-300/30 text-amber-200 text-xs font-semibold tracking-wide uppercase">
                    <i class="fa-solid fa-certificate text-amber-400"></i>
                    <span>Melayani Dengan Hati</span>
                </div>

                <!-- Hero Title (Sesuai Screenshot 1) -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-amber-400 font-serif drop-shadow-md">
                    AGEN PROPERTI INDONESIA
                </h1>

                <!-- Sub-Headline (Sesuai Screenshot 1) -->
                <p class="text-sm sm:text-base lg:text-lg text-amber-50/90 font-normal leading-relaxed drop-shadow">
                    Agen Properti Profesional, yang saat ini telah menangani pemasaran unit-unit Properti di Jogja, Bantul, Magelang, Jakarta, Malang, Bali dan kota-kota lainnya.
                </p>

                <!-- Action Button -->
                <div class="pt-3 flex flex-wrap items-center gap-3">
                    <a href="#properti" 
                       class="px-7 py-3 rounded-full bg-[#C5A038] hover:bg-[#B08C2B] text-white text-sm font-semibold shadow-lg shadow-amber-900/40 transition-all hover:scale-105 active:scale-95 inline-flex items-center gap-2">
                        <span>Jelajahi Properti</span>
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    </a>
                    
                    <a href="https://wa.me/6281234567890?text=Halo%20Wastu%20Property,%20saya%20ingin%20konsultasi%20properti" 
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
            <form action="#properti" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 sm:gap-2 items-center">
                <!-- Lokasi -->
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl sm:rounded-full">
                    <i class="fa-solid fa-location-dot text-[#C5A038] text-sm"></i>
                    <div class="w-full">
                        <label class="block text-[10px] uppercase font-bold text-gray-400">Lokasi / Kota</label>
                        <select x-model="selectedLocation" class="w-full text-xs font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer">
                            <option value="all">Semua Lokasi</option>
                            <option value="Jogja">Yogyakarta</option>
                            <option value="Sleman">Sleman</option>
                            <option value="Bantul">Bantul</option>
                            <option value="Magelang">Magelang</option>
                            <option value="Jakarta">Jakarta</option>
                            <option value="Bali">Bali</option>
                        </select>
                    </div>
                </div>

                <!-- Tipe Properti -->
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl sm:rounded-full">
                    <i class="fa-solid fa-house text-[#C5A038] text-sm"></i>
                    <div class="w-full">
                        <label class="block text-[10px] uppercase font-bold text-gray-400">Tipe Properti</label>
                        <select x-model="selectedType" class="w-full text-xs font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer">
                            <option value="all">Semua Tipe</option>
                            <option value="Rumah Tinggal">Rumah Tinggal</option>
                            <option value="Vila">Vila & Resort</option>
                            <option value="Tanah">Tanah Kavling</option>
                            <option value="Kos-kosan">Kost Eksklusif</option>
                            <option value="Ruko">Ruko & Komersial</option>
                        </select>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl sm:rounded-full">
                    <i class="fa-solid fa-tags text-[#C5A038] text-sm"></i>
                    <div class="w-full">
                        <label class="block text-[10px] uppercase font-bold text-gray-400">Status Ketersediaan</label>
                        <select x-model="selectedStatus" class="w-full text-xs font-semibold text-gray-800 bg-transparent focus:outline-none cursor-pointer">
                            <option value="all">Semua Status</option>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Booking">Booking</option>
                            <option value="Terjual">Terjual</option>
                        </select>
                    </div>
                </div>

                <!-- Tombol Cari -->
                <button type="button" 
                        class="w-full h-full min-h-[46px] rounded-2xl sm:rounded-full bg-[#C5A038] hover:bg-[#B08C2B] text-white font-semibold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-md shadow-amber-600/20 active:scale-95 transition-all cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari Properti</span>
                </button>
            </form>
        </div>
    </section>

    <!-- ==================== PROYEK TERKINI & LISTING PROPERTI ==================== -->
    <section id="properti" class="max-w-6xl mx-auto px-6 pt-24 pb-12">
        <!-- Section Header (Sesuai Referensi Gambar Proyek Terkini) -->
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-3 justify-center mb-3">
                <span class="w-12 h-0.5 bg-[#C5A038]"></span>
                <span class="text-xs font-extrabold uppercase tracking-widest text-gray-800">PROYEK TERKINI</span>
                <span class="w-12 h-0.5 bg-[#C5A038]"></span>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                Dapatkan segera hunian eksklusif dengan nuansa etnik jawa di lingkungan destinasi wisata dunia. Setelah sukses dengan tahap pertama, kini kami menghadirkan <strong class="text-gray-900 font-bold">Casa de Borobudur 2</strong> yang bisa anda miliki!
            </p>
        </div>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            <!-- Property 1 -->
            <x-property-card 
                code="WP-BORO-01"
                title="Casa de Borobudur 2 - Tipe Limasan"
                price="Rp 650.000.000"
                :priceNumeric="650000000"
                location="Borobudur, Magelang"
                type="Rumah Tinggal"
                status="Tersedia"
                :beds="3"
                :baths="2"
                :buildingArea="85"
                :landArea="120"
                imageUrl="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80"
                whatsappNumber="6281234567890"
            />

            <!-- Property 2 -->
            <x-property-card 
                code="WP-SLM-04"
                title="Green Hills Residence Ambarukmo"
                price="Rp 1.250.000.000"
                :priceNumeric="1250000000"
                location="Condongcatur, Sleman"
                type="Rumah Tinggal"
                status="Tersedia"
                :beds="4"
                :baths="3"
                :buildingArea="130"
                :landArea="150"
                imageUrl="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80"
                whatsappNumber="6281234567890"
            />

            <!-- Property 3 -->
            <x-property-card 
                code="WP-BTL-02"
                title="Villa Etnik Kasongan Heritage"
                price="Rp 890.000.000"
                :priceNumeric="890000000"
                location="Kasongan, Bantul"
                type="Vila"
                status="Booking"
                :beds="3"
                :baths="3"
                :buildingArea="110"
                :landArea="180"
                imageUrl="https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=800&q=80"
                whatsappNumber="6281234567890"
            />

            <!-- Property 4 -->
            <x-property-card 
                code="WP-JOG-09"
                title="Kavling Premium Kotabaru Heritage"
                price="Rp 2.100.000.000"
                :priceNumeric="2100000000"
                location="Gondokusuman, Yogyakarta"
                type="Tanah"
                status="Tersedia"
                :beds="0"
                :baths="0"
                :buildingArea="0"
                :landArea="320"
                imageUrl="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80"
                whatsappNumber="6281234567890"
            />

            <!-- Property 5 -->
            <x-property-card 
                code="WP-MLG-03"
                title="Batu Panorama Villa & Private Pool"
                price="Rp 1.750.000.000"
                :priceNumeric="1750000000"
                location="Batu, Malang"
                type="Vila"
                status="Tersedia"
                :beds="4"
                :baths="4"
                :buildingArea="160"
                :landArea="200"
                imageUrl="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80"
                whatsappNumber="6281234567890"
            />

            <!-- Property 6 -->
            <x-property-card 
                code="WP-SLM-08"
                title="Kost Eksklusif Pogung UGM 12 Kamar"
                price="Rp 3.200.000.000"
                :priceNumeric="3200000000"
                location="Pogung, Sleman"
                type="Kos-kosan"
                status="Terjual"
                :beds="12"
                :baths="12"
                :buildingArea="280"
                :landArea="250"
                imageUrl="https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=800&q=80"
                whatsappNumber="6281234567890"
            />
        </div>

        <div class="text-center pt-10">
            <a href="#properti" class="inline-flex items-center gap-2 px-8 py-3 rounded-full border border-[#C5A038] text-[#C5A038] hover:bg-[#C5A038] hover:text-white font-semibold text-xs uppercase tracking-wider transition-all">
                <span>Lihat Seluruh Listing Properti</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </section>

    <!-- ==================== TENTANG KAMI & VISI MISI ==================== -->
    <section id="tentang-kami" class="bg-amber-50/40 py-20 border-y border-amber-100/60">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Image Collage -->
                <div class="relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white aspect-[4/3]">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80" 
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
                        Mitra Akuisisi & Pemasaran Properti Terpercaya Anda
                    </h2>

                    <p class="text-sm text-gray-600 leading-relaxed">
                        <strong>PT. Wastu Pratama Wijaya (Wastu Property)</strong> adalah perusahaan agen perumahan dan pengembang properti profesional yang berbasis di Sleman, Daerah Istimewa Yogyakarta. Kami memiliki komitmen penuh dalam memberikan solusi komprehensif mulai dari akuisisi unit, pemasaran, hingga konsultasi kepemilikan properti.
                    </p>

                    <!-- Visi & Misi Box -->
                    <div id="visi-misi" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-white border border-amber-100/80 shadow-sm">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-[#C5A038] mb-2.5">
                                <i class="fa-solid fa-bullseye text-sm"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 mb-1">Visi Kami</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Menjadi agen dan pengembang properti terdepan di Indonesia yang berintegritas dan melayani dengan hati.</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-amber-100/80 shadow-sm">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-[#C5A038] mb-2.5">
                                <i class="fa-solid fa-handshake-angle text-sm"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 mb-1">Misi Kami</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">Memberikan pendampingan transaksi properti yang transparan, aman, dan berdaya guna bagi konsumen serta developer mitra.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== LAYANAN KAMI ==================== -->
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
            <!-- Layanan 1 -->
            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] group-hover:bg-[#C5A038] group-hover:text-white flex items-center justify-center mb-5 transition-colors">
                    <i class="fa-solid fa-file-contract text-lg"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Titip Jual Properti</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Bantu pasarkan unit properti Anda dengan strategi digital marketing dan jaringan buyer kami secara cepat dan tepat.</p>
            </div>

            <!-- Layanan 2 -->
            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] group-hover:bg-[#C5A038] group-hover:text-white flex items-center justify-center mb-5 transition-colors">
                    <i class="fa-solid fa-key text-lg"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Pembelian Properti</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Konsultasi pemilihan rumah, tanah kavling, villa, hingga kost eksklusif yang sesuai anggaran dan prospek masa depan.</p>
            </div>

            <!-- Layanan 3 -->
            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] group-hover:bg-[#C5A038] group-hover:text-white flex items-center justify-center mb-5 transition-colors">
                    <i class="fa-solid fa-city text-lg"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Jasa Developer</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Kerja sama akuisisi lahan, perencanaan konsep perumahan, hingga manajemen konstruksi berstandar tinggi.</p>
            </div>

            <!-- Layanan 4 -->
            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] group-hover:bg-[#C5A038] group-hover:text-white flex items-center justify-center mb-5 transition-colors">
                    <i class="fa-solid fa-bullhorn text-lg"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Agen Marketing</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Dukungan promosi multi-channel (website, WhatsApp Business, media sosial) untuk mempercepat closing unit.</p>
            </div>
        </div>
    </section>

    <!-- ==================== ARTIKEL TERBARU (SESUAI GAMBAR 2) ==================== -->
    <section id="artikel" class="bg-gray-50 py-20 border-t border-gray-200/60">
        <div class="max-w-6xl mx-auto px-6">
            
            <!-- Section Header (Sesuai Screenshot 2: Garis Kuning Emas + ARTIKEL TERBARU) -->
            <div class="flex items-center gap-4 mb-10">
                <div class="w-16 h-1 bg-[#F8B803] rounded-full"></div>
                <h2 class="text-xs sm:text-sm font-extrabold tracking-widest uppercase text-gray-800">
                    ARTIKEL TERBARU
                </h2>
            </div>

            <!-- Article Cards Grid (Sesuai Screenshot 2) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Artikel 1 -->
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80" 
                             alt="Perbedaan Letter C dan Girik" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-slate-700/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                            BLOG
                        </span>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-[#C5A038] transition-colors leading-snug line-clamp-2">
                            Perbedaan Letter C dan Girik yang Jarang Diketahui Orang
                        </h3>
                        <div class="mt-auto pt-3 border-t border-gray-50 flex flex-col gap-2">
                            <a href="#" class="text-[11px] font-bold text-[#F8B803] hover:text-[#C5A038] uppercase">
                                READ MORE &raquo;
                            </a>
                            <span class="text-[10px] text-gray-400">August 15, 2026</span>
                        </div>
                    </div>
                </div>

                <!-- Artikel 2 -->
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=600&q=80" 
                             alt="Kenapa Letter C Sering Disebut Setengah Sertifikat" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-slate-700/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                            BLOG
                        </span>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-[#C5A038] transition-colors leading-snug line-clamp-2">
                            Kenapa Letter C Sering Disebut 'Setengah Sertifikat'?
                        </h3>
                        <div class="mt-auto pt-3 border-t border-gray-50 flex flex-col gap-2">
                            <a href="#" class="text-[11px] font-bold text-[#F8B803] hover:text-[#C5A038] uppercase">
                                READ MORE &raquo;
                            </a>
                            <span class="text-[10px] text-gray-400">August 14, 2026</span>
                        </div>
                    </div>
                </div>

                <!-- Artikel 3 -->
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=600&q=80" 
                             alt="Letter C Tanah Masih Berlaku" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-slate-700/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                            BLOG
                        </span>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-[#C5A038] transition-colors leading-snug line-clamp-2">
                            Letter C Tanah Masih Berlaku? Ini Jawabannya Menurut Hukum Agraria
                        </h3>
                        <div class="mt-auto pt-3 border-t border-gray-50 flex flex-col gap-2">
                            <a href="#" class="text-[11px] font-bold text-[#F8B803] hover:text-[#C5A038] uppercase">
                                READ MORE &raquo;
                            </a>
                            <span class="text-[10px] text-gray-400">August 13, 2026</span>
                        </div>
                    </div>
                </div>

                <!-- Artikel 4 -->
                <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col group">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80" 
                             alt="Contoh Letter C Tanah" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-slate-700/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                            BLOG
                        </span>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-[#C5A038] transition-colors leading-snug line-clamp-2">
                            Contoh Letter C Tanah dan Cara Membacanya untuk Orang Awam
                        </h3>
                        <div class="mt-auto pt-3 border-t border-gray-50 flex flex-col gap-2">
                            <a href="#" class="text-[11px] font-bold text-[#F8B803] hover:text-[#C5A038] uppercase">
                                READ MORE &raquo;
                            </a>
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
                    <a href="https://wa.me/6281234567890?text=Halo%20Wastu%20Property,%20saya%20developer%20ingin%20mengajukan%20kerja%20sama%20pemasaran" 
                       target="_blank"
                       class="px-7 py-3 rounded-full bg-[#C5A038] hover:bg-[#B08C2B] text-white font-semibold text-xs uppercase tracking-wider shadow-lg transition-all inline-flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span>Ajukan Kemitraan</span>
                    </a>
                </div>
            </div>

            <!-- Background subtle ornament -->
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
                    <p class="text-xs text-gray-500 leading-relaxed">Jl. Green Hills Ambarukmo No. A14, Condongcatur, Depok, Sleman, DIY 55281</p>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-phone text-xl"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-1">Telepon & WhatsApp</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">+62 812-3456-7890 (Customer Care)<br>+62 274-123456 (Office)</p>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-[#C5A038] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-envelope text-xl"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-1">Email Resmi</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">info@wastuproperty.co.id<br>marketing@wastuproperty.co.id</p>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
