@extends('layouts.admin')

@section('title', 'CMS Company Profile & Pengaturan')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ activeTab: 'settings' }">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">CMS Company Profile</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-sliders text-[#C5A038]"></i>
                <span>Kelola Konten & Pengaturan Website</span>
            </h1>
            <p class="text-xs text-gray-400">Atur nomor WhatsApp bisnis, visibilitas unit terjual, hero banner, layanan, testimoni, dan FAQ.</p>
        </div>
        <a href="{{ url('/') }}" target="_blank" class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl border border-[#2B3543] flex items-center gap-2">
            <i class="fa-solid fa-globe text-[#C5A038]"></i>
            <span>Tinjau Website Publik &nearr;</span>
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-[#2B3543] overflow-x-auto pb-2 text-xs font-semibold">
        <button @click="activeTab = 'settings'" 
                :class="activeTab === 'settings' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-gear"></i>
            <span>Pengaturan Umum & WA</span>
        </button>
        <button @click="activeTab = 'hero'" 
                :class="activeTab === 'hero' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-image"></i>
            <span>Hero Banner</span>
        </button>
        <button @click="activeTab = 'about'" 
                :class="activeTab === 'about' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-circle-info"></i>
            <span>Tentang & Visi Misi</span>
        </button>
        <button @click="activeTab = 'services'" 
                :class="activeTab === 'services' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-briefcase"></i>
            <span>Layanan ({{ $services->count() }})</span>
        </button>
        <button @click="activeTab = 'faqs'" 
                :class="activeTab === 'faqs' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-circle-question"></i>
            <span>FAQ ({{ $faqs->count() }})</span>
        </button>
    </div>

    <!-- TAB 1: PENGATURAN UMUM & WA BISNIS -->
    <div x-show="activeTab === 'settings'" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-6">
        <div>
            <h3 class="text-sm font-bold text-white mb-1">Pengaturan Bisnis & WhatsApp</h3>
            <p class="text-xs text-gray-400">Nomor ini menjadi tujuan tombol "Hubungi via WhatsApp" di seluruh listing properti.</p>
        </div>

        <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Nomor WhatsApp Bisnis (Format: 628xxx)</label>
                    <div class="relative">
                        <i class="fa-brands fa-whatsapp absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400 text-sm"></i>
                        <input type="text" name="whatsapp_business" value="{{ $settings['whatsapp_business'] ?? '6281234567890' }}" required
                               class="w-full pl-9 pr-4 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Email Resmi Perusahaan</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'info@wastuproperty.co.id' }}"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Nomor Telepon Kantor (Telepon Statis)</label>
                    <input type="text" name="office_phone" value="{{ $settings['office_phone'] ?? '+62 274-123456' }}"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Jam Operasional</label>
                    <input type="text" name="working_hours" value="{{ $settings['working_hours'] ?? 'Mon - Sat : 9.00 AM - 4.00 PM' }}"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-300 mb-1">Alamat Kantor Lengkap</label>
                    <textarea name="office_address" rows="2" class="w-full px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">{{ $settings['office_address'] ?? 'Jl. Green Hills Ambarukmo No. A14, Dabag, Condongcatur, Depok, Sleman, DIY 55281' }}</textarea>
                </div>

                <!-- Template Pesan Minat WhatsApp (Sesuai BPMN: Konsumen Kirim Pesan Minat via WhatsApp) -->
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-300 mb-1">
                        Template Pesan Minat WhatsApp (Click-to-Chat Konsumen)
                        <span class="text-[10px] text-amber-400 font-normal ml-1">Gunakan tag: {kode_unit}, {nama_unit}</span>
                    </label>
                    <input type="text" name="wa_template_interest" 
                           value="{{ $settings['wa_template_interest'] ?? 'Halo Wastu Property, saya tertarik dengan unit {kode_unit} ({nama_unit}). Mohon info ketersediaan dan detailnya.' }}"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 text-xs focus:outline-none focus:border-[#C5A038]">
                    <p class="text-[10px] text-gray-400 mt-1">Pesan ini otomatis terisi di aplikasi WhatsApp konsumen saat menekan tombol "Hubungi via WhatsApp".</p>
                </div>

                <!-- Template Auto-Reply FAQ (Sesuai BPMN: Jika Konsumen Ragu/Tidak Tertarik -> Kirim auto-reply FAQ) -->
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-300 mb-1">Template Balasan Cepat FAQ (Auto-Reply / Quick Reply WhatsApp)</label>
                    <textarea name="wa_template_faq_reply" rows="2" 
                              class="w-full px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 text-xs focus:outline-none focus:border-[#C5A038]">{{ $settings['wa_template_faq_reply'] ?? 'Terima kasih telah menghubungi Wastu Property. Untuk informasi seputar legalitas tanah (SHM/Girik), konsultasi KPR bank, atau survei lokasi gratis, Anda dapat melihat panduan kami atau sampaikan pertanyaan Anda di sini.' }}</textarea>
                    <p class="text-[10px] text-gray-400 mt-1">Digunakan admin / sistem saat konsumen belum yakin atau butuh info umum seputar properti.</p>
                </div>

                <!-- Pengaturan Unit Terjual (Syarat 2B) -->
                <div class="sm:col-span-2 p-4 bg-[#14181E] rounded-xl border border-[#2B3543]">
                    <span class="text-xs font-bold text-white block mb-1">Visibilitas Unit Terjual di Website Publik</span>
                    <label class="inline-flex items-center gap-2 cursor-pointer mt-1">
                        <input type="checkbox" name="hide_sold_units" value="1" {{ ($settings['hide_sold_units'] ?? '0') === '1' ? 'checked' : '' }}
                               class="rounded bg-[#1F2731] border-[#2B3543] text-[#C5A038]">
                        <span class="text-gray-300 text-xs">Sembunyikan unit yang berstatus <strong>"Terjual"</strong> dari listing website publik</span>
                    </label>
                    <p class="text-[10px] text-gray-400 mt-1">Jika centang dihilangkan, unit berstatus Terjual akan tetap ditampilkan dengan badge label merah "Terjual".</p>
                </div>
            </div>

            <div class="pt-3 border-t border-[#2B3543] flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-bold rounded-xl shadow-lg">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: HERO BANNER -->
    <div x-show="activeTab === 'hero'" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-6" style="display: none;">
        <div>
            <h3 class="text-sm font-bold text-white mb-1">Konten Hero Banner Utama</h3>
            <p class="text-xs text-gray-400">Ubah judul utama dan deskripsi pada banner atas halaman depan.</p>
        </div>

        <form action="{{ route('admin.cms.section.update', 'hero') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ imagePreview: null }">
            @csrf
            @method('PUT')
            <div class="space-y-4 text-xs">
                
                <!-- Gambar Background Hero Banner -->
                <div class="p-4 bg-[#14181E] border border-[#2B3543] rounded-2xl space-y-3">
                    <label class="block font-semibold text-gray-300">
                        Foto Background Hero Banner
                        <span class="text-gray-400 font-normal ml-1">(Rasio ideal landscape 16:9, Maks. 3 MB)</span>
                    </label>

                    <div class="flex flex-col sm:flex-row gap-4 items-start">
                        <!-- Current / Preview Thumbnail -->
                        <div class="relative w-full sm:w-64 h-36 rounded-xl overflow-hidden border border-[#2B3543] bg-[#0D1117] shrink-0">
                            <!-- Live Preview if selected -->
                            <template x-if="imagePreview">
                                <img :src="imagePreview" alt="Preview Gambar Baru" class="w-full h-full object-cover">
                            </template>

                            <!-- Existing Image if no preview -->
                            <template x-if="!imagePreview">
                                <img src="{{ $hero && $hero->image_path ? asset('storage/' . $hero->image_path) : 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80' }}" 
                                     alt="Hero Banner Saat Ini" 
                                     class="w-full h-full object-cover">
                            </template>

                            <div class="absolute bottom-2 left-2 px-2 py-0.5 rounded bg-black/70 backdrop-blur-sm text-[10px] text-gray-200 font-medium">
                                {{ $hero && $hero->image_path ? 'Gambar Kustom Aktif' : 'Gambar Default Sistem' }}
                            </div>
                        </div>

                        <!-- Upload Input & Guidance -->
                        <div class="flex-1 space-y-2">
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                                   @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result }; reader.readAsDataURL(file); }"
                                   class="w-full bg-[#1F2731] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#C5A038] file:text-white hover:file:bg-[#b08d2e] cursor-pointer">
                            <p class="text-[11px] text-gray-400">
                                Pilih gambar baru jika ingin mengganti latar belakang hero banner pada halaman utama website publik. Biarkan kosong jika ingin tetap menggunakan gambar saat ini.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Tagline Kecil (Badge)</label>
                    <input type="text" name="subtitle" value="{{ $hero->subtitle }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Headline Utama Banner</label>
                    <input type="text" name="title" value="{{ $hero->title }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Sub-headline / Deskripsi</label>
                    <textarea name="content" rows="3" required class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">{{ $hero->content }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-t border-[#2B3543] flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-bold rounded-xl shadow-lg cursor-pointer">
                    Perbarui Hero Banner
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: TENTANG KAMI & VISI MISI -->
    <div x-show="activeTab === 'about'" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-6" style="display: none;">
        <div>
            <h3 class="text-sm font-bold text-white mb-1">Tentang Kami, Visi & Misi</h3>
            <p class="text-xs text-gray-400">Profil perusahaan yang tampil di halaman utama dan Tentang Kami.</p>
        </div>

        <form action="{{ route('admin.cms.section.update', 'about') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Judul Tentang Kami</label>
                    <input type="text" name="title" value="{{ $about->title }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Deskripsi Lengkap Tentang Kami</label>
                    <textarea name="content" rows="4" required class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">{{ $about->content }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-t border-[#2B3543] flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-bold rounded-xl shadow-lg">
                    Simpan Tentang Kami
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 4: LAYANAN -->
    <div x-show="activeTab === 'services'" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-6" style="display: none;">
        <div class="flex items-center justify-between pb-3 border-b border-[#2B3543]">
            <div>
                <h3 class="text-sm font-bold text-white">Daftar Layanan Perusahaan</h3>
                <p class="text-xs text-gray-400">Titip Jual Properti, Pembelian, Jasa Developer, Agen Marketing.</p>
            </div>
        </div>

        <!-- Tambah Layanan Baru -->
        <form action="{{ route('admin.cms.services.store') }}" method="POST" class="p-4 bg-[#14181E] rounded-xl border border-[#2B3543] space-y-3">
            @csrf
            <span class="text-xs font-bold text-[#C5A038] block">+ Tambah Layanan Baru</span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block text-gray-300 mb-1">Nama Layanan</label>
                    <input type="text" name="title" placeholder="Contoh: Konsultasi KPR Bank" required
                           class="w-full px-3 py-2 bg-[#1F2731] border border-[#2B3543] rounded-lg text-gray-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-300 mb-1">Ikon FontAwesome</label>
                    <input type="text" name="icon" value="fa-solid fa-handshake" required
                           class="w-full px-3 py-2 bg-[#1F2731] border border-[#2B3543] rounded-lg text-gray-200 focus:outline-none">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-gray-300 mb-1">Deskripsi Singkat</label>
                    <input type="text" name="description" placeholder="Bantuan proses berkas KPR ke berbagai bank terkemuka..." required
                           class="w-full px-3 py-2 bg-[#1F2731] border border-[#2B3543] rounded-lg text-gray-200 focus:outline-none">
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-semibold rounded-lg">
                Simpan Layanan
            </button>
        </form>

        <!-- Tabel Layanan -->
        <div class="space-y-2">
            @foreach($services as $serv)
                <div class="flex items-center justify-between p-3.5 bg-[#14181E] rounded-xl border border-[#2B3543] text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-[#C5A038] flex items-center justify-center">
                            <i class="{{ $serv->icon }} text-xs"></i>
                        </div>
                        <div>
                            <span class="font-bold text-white block">{{ $serv->title }}</span>
                            <span class="text-[11px] text-gray-400">{{ $serv->description }}</span>
                        </div>
                    </div>
                    <form action="{{ route('admin.cms.services.destroy', $serv->id) }}" method="POST" onsubmit="return confirm('Hapus layanan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 p-1.5"><i class="fa-solid fa-trash text-xs"></i></button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 5: FAQ -->
    <div x-show="activeTab === 'faqs'" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-6" style="display: none;">
        <div class="flex items-center justify-between pb-3 border-b border-[#2B3543]">
            <div>
                <h3 class="text-sm font-bold text-white">Kelola Pertanyaan Sering Diajukan (FAQ)</h3>
                <p class="text-xs text-gray-400">Tanya jawab seputar prosedur pembelian, legalitas sertifikat, dan titip jual.</p>
            </div>
        </div>

        <!-- Tambah FAQ Baru -->
        <form action="{{ route('admin.cms.faqs.store') }}" method="POST" class="p-4 bg-[#14181E] rounded-xl border border-[#2B3543] space-y-3">
            @csrf
            <span class="text-xs font-bold text-[#C5A038] block">+ Tambah Pertanyaan FAQ Baru</span>
            <div class="space-y-3 text-xs">
                <div>
                    <label class="block text-gray-300 mb-1">Pertanyaan</label>
                    <input type="text" name="question" placeholder="Contoh: Apakah bisa dibantu proses KPR?" required
                           class="w-full px-3 py-2 bg-[#1F2731] border border-[#2B3543] rounded-lg text-gray-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-300 mb-1">Jawaban</label>
                    <textarea name="answer" rows="2" placeholder="Ya, tim kami mendampingi pengajuan KPR hingga akad kredit selesai..." required
                              class="w-full px-3 py-2 bg-[#1F2731] border border-[#2B3543] rounded-lg text-gray-200 focus:outline-none"></textarea>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-semibold rounded-lg">
                Simpan FAQ
            </button>
        </form>

        <div class="space-y-2">
            @forelse($faqs as $faq)
                <div class="p-3.5 bg-[#14181E] rounded-xl border border-[#2B3543] text-xs flex items-start justify-between gap-3">
                    <div>
                        <span class="font-bold text-white block mb-1">Q: {{ $faq->question }}</span>
                        <p class="text-[11px] text-gray-400">A: {{ $faq->answer }}</p>
                    </div>
                    <form action="{{ route('admin.cms.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Hapus FAQ ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 p-1.5"><i class="fa-solid fa-trash text-xs"></i></button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-gray-400 py-4 text-center">Belum ada daftar FAQ.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
