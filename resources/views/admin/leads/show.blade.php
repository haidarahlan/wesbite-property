@extends('layouts.admin')

@section('title', "Detail & Bukti Chat - {$lead->reference_code}")

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ openUploadModal: false, previewImage: null, selectedProof: null, openZoomModal: false }">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.leads.index') }}" class="hover:text-[#C5A038]">Leads</a>
                <span>/</span>
                <span class="text-gray-200">{{ $lead->reference_code }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <span>{{ $lead->name ?? 'Prospek Tanpa Nama' }}</span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-[#C5A038]/20 text-[#C5A038] border border-[#C5A038]/40">
                        {{ $lead->reference_code }}
                    </span>
                </h1>
            </div>
            <p class="text-xs text-gray-400">Sumber: <span class="capitalize text-gray-300 font-semibold">{{ $lead->source }}</span> • Dibuat pada {{ $lead->created_at->format('d M Y H:i') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Tombol Direct Chat WhatsApp -->
            <a href="https://wa.me/{{ $lead->phone }}" target="_blank"
               class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-lg shadow-emerald-600/20 flex items-center gap-2 transition-all">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Buka WhatsApp Konsumen</span>
            </a>

            <!-- Tombol Jadwalkan Survei -->
            <a href="{{ route('admin.surveys.index') }}?search={{ urlencode($lead->phone) }}" 
               class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl shadow-lg shadow-purple-600/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Jadwalkan Survei</span>
            </a>

            <a href="{{ route('admin.leads.index') }}" class="px-3.5 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl border border-[#2B3543] flex items-center gap-2 transition-all">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            
            <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lead ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-gray-400 hover:text-red-400 bg-[#232B35] hover:bg-red-950/40 rounded-xl border border-[#2B3543] transition-all" title="Hapus Lead">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Layout Grid: Kiri Profil Konsumen, Kanan Galeri Bukti Chat Konsumen -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- KOLOM KIRI (5 Cols): Profil Lead & Edit Data -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Card Edit Data & Status -->
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center justify-between border-b border-[#2B3543] pb-3">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-[#C5A038]"></i>
                        <span>Informasi Konsumen</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#161B22] text-[#C5A038] border border-[#2B3543]">
                        {{ $lead->status }}
                    </span>
                </h3>

                <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $lead->name) }}" required
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Nomor WhatsApp Konsumen</label>
                        <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" required
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Status Prospek</label>
                        <select name="status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none font-semibold">
                            @foreach(['Baru', 'Tertarik', 'Tidak Tertarik', 'Follow-up', 'Survei Terjadwal', 'Survei Selesai', 'Closing', 'Batal'] as $st)
                                <option value="{{ $st }}" {{ $lead->status === $st ? 'selected' : '' }}>
                                    ● {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Unit Properti yang Diminati</label>
                        <select name="unit_id" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="">-- Belum Memilih Unit Tertentu --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ $lead->unit_id == $unit->id ? 'selected' : '' }}>
                                    [{{ $unit->code }}] {{ $unit->title }} (Rp {{ number_format($unit->price / 1000000, 0, ',', '.') }} Jt)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Tugaskan PIC Marketing</label>
                        <select name="assigned_to" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $lead->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Catatan Tambahan Konsumen</label>
                        <textarea name="notes" rows="3" placeholder="Catatan preferensi, rencana pembayaran cash/KPR..."
                                  class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">{{ old('notes', $lead->notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[#C5A038] hover:bg-[#b08d2e] text-white font-bold rounded-xl transition-all shadow-md cursor-pointer">
                        Simpan Perubahan Data
                    </button>
                </form>
            </div>

            <!-- Card Ringkasan Unit Terkait (Jika Ada) -->
            @if($lead->unit)
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-[#2B3543] pb-2 text-xs">
                        <span class="text-gray-400 font-semibold uppercase">Detail Properti Terkait</span>
                        <a href="{{ route('public.property.detail', $lead->unit->slug) }}" target="_blank" class="text-[#C5A038] hover:underline flex items-center gap-1">
                            <span>Lihat di Web</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                    <div class="flex gap-3 items-center">
                        @if($lead->unit->photos->count() > 0)
                            <img src="{{ asset('storage/' . $lead->unit->photos->first()->photo_path) }}" alt="{{ $lead->unit->title }}" class="w-16 h-16 rounded-xl object-cover border border-[#2B3543]">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-[#161B22] flex items-center justify-center text-gray-600 border border-[#2B3543]">
                                <i class="fa-solid fa-building text-xl"></i>
                            </div>
                        @endif
                        <div class="text-xs">
                            <span class="px-2 py-0.5 rounded bg-[#C5A038]/20 text-[#C5A038] font-bold text-[10px]">{{ $lead->unit->code }}</span>
                            <h4 class="font-bold text-white mt-1">{{ $lead->unit->title }}</h4>
                            <div class="text-[#C5A038] font-black text-sm mt-0.5">Rp {{ number_format($lead->unit->price, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- KOLOM KANAN (7 Cols): PANEL BUKTI TANGKAPAN LAYAR CHAT KONSUMEN -->
        <div class="lg:col-span-7 space-y-4">
            
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm space-y-5">
                
                <!-- Header Panel Bukti Chat -->
                <div class="flex items-center justify-between border-b border-[#2B3543] pb-4">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-images text-[#C5A038]"></i>
                            <span>Bukti Chat WhatsApp Konsumen ({{ $lead->chatProofs->count() }})</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Dokumentasi screenshot percakapan asli antara marketing dan konsumen sebagai bukti minat valid.</p>
                    </div>

                    <button @click="openUploadModal = true"
                            class="px-3.5 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white text-xs font-semibold rounded-xl shadow-md flex items-center gap-1.5 transition-all cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Bukti Chat</span>
                    </button>
                </div>

                <!-- Galeri Screenshot Bukti Chat -->
                @if($lead->chatProofs->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($lead->chatProofs as $proof)
                            <div class="bg-[#161B22] border border-[#2B3543] rounded-2xl overflow-hidden hover:border-[#C5A038]/50 transition-all flex flex-col justify-between group">
                                
                                <!-- Image Preview Container -->
                                <div class="relative aspect-[4/3] bg-[#0d1117] overflow-hidden cursor-pointer"
                                     @click="selectedProof = {{ $proof->toJson() }}; openZoomModal = true">
                                    <img src="{{ $proof->image_url }}" alt="{{ $proof->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-2">
                                        <i class="fa-solid fa-magnifying-glass-plus text-base"></i>
                                        <span>Klik untuk Perbesar</span>
                                    </div>
                                </div>

                                <!-- Caption / Info -->
                                <div class="p-3.5 space-y-2 text-xs">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="font-bold text-white line-clamp-1">{{ $proof->title ?? 'Screenshot Chat WhatsApp' }}</h4>
                                        <form action="{{ route('admin.leads.chat-proof.destroy', $proof->id) }}" method="POST" onsubmit="return confirm('Hapus bukti chat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors p-1" title="Hapus Bukti">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>

                                    @if($proof->notes)
                                        <p class="text-[11px] text-gray-400 italic bg-[#1F2731] p-2 rounded-xl border border-[#2B3543]">
                                            "{{ $proof->notes }}"
                                        </p>
                                    @endif

                                    <div class="text-[10px] text-gray-500 flex items-center justify-between pt-1 border-t border-[#2B3543]/60">
                                        <span>Oleh: <strong class="text-gray-300">{{ $proof->uploader->name ?? 'Staf' }}</strong></span>
                                        <span>{{ $proof->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 border-2 border-dashed border-[#2B3543] rounded-2xl flex flex-col items-center justify-center text-center p-6 space-y-3">
                        <div class="w-14 h-14 rounded-full bg-[#161B22] text-[#C5A038] flex items-center justify-center text-2xl border border-[#2B3543]">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Belum Ada Bukti Chat Konsumen</h4>
                            <p class="text-xs text-gray-400 mt-1 max-w-sm">
                                Ambil screenshot hasil percakapan Anda dengan konsumen di WhatsApp, lalu unggah ke sini sebagai bukti autentik minat dan progres konsumen.
                            </p>
                        </div>
                        <button @click="openUploadModal = true"
                                class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-200 text-xs font-semibold rounded-xl border border-[#2B3543] transition-all">
                            <i class="fa-solid fa-plus mr-1"></i> Unggah Screenshot Pertama
                        </button>
                    </div>
                @endif

            </div>

        </div>

    </div>

    <!-- MODAL 1: FORM UPLOAD BUKTI CHAT -->
    <div x-show="openUploadModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openUploadModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-[#C5A038]"></i>
                    <span>Upload Bukti Percakapan WhatsApp</span>
                </h3>
                <button @click="openUploadModal = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('admin.leads.chat-proof.store', $lead->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-gray-300 font-semibold mb-1">File Gambar Screenshot Chat <span class="text-red-400">*</span></label>
                    <input type="file" name="image" required accept="image/png,image/jpeg,image/webp"
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#C5A038] file:text-white hover:file:bg-[#b08d2e]">
                    <span class="text-[10px] text-gray-500 mt-1 block">Format: JPG, PNG, atau WebP (Maks. 5 MB)</span>
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Judul / Topik Pembicaraan</label>
                    <input type="text" name="title" placeholder="Contoh: Janjian survei lokasi unit Borobudur hari Sabtu"
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Rangkuman / Catatan Chat</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Konsumen tertarik skema KPR DP 10%, minta dikirimkan denah dan simulasi angsuran..."
                              class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2B3543]">
                    <button type="button" @click="openUploadModal = false" class="px-4 py-2 bg-[#2B3543] hover:bg-[#384455] text-gray-300 rounded-xl font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-xl font-semibold shadow-md">
                        Simpan Bukti Chat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: ZOOM / LIGHTBOX BUKTI CHAT -->
    <div x-show="openZoomModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
        <div @click.away="openZoomModal = false" class="max-w-3xl w-full text-center space-y-3">
            <div class="flex items-center justify-between text-white text-xs px-2">
                <span class="font-bold text-sm" x-text="selectedProof?.title || 'Bukti Screenshot Chat WhatsApp'"></span>
                <button @click="openZoomModal = false" class="p-2 text-gray-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="bg-[#14181E] p-2 rounded-2xl border border-[#2B3543] inline-block max-h-[80vh] overflow-auto">
                <img :src="selectedProof ? ('/storage/' + selectedProof.image_path) : ''" 
                     class="max-h-[75vh] w-auto rounded-xl mx-auto object-contain">
            </div>

            <template x-if="selectedProof?.notes">
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-xl p-3 text-xs text-gray-300 max-w-xl mx-auto">
                    <span class="font-bold text-[#C5A038] block mb-0.5">Catatan Marketing:</span>
                    <p x-text="selectedProof.notes" class="italic"></p>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
