@extends('layouts.admin')

@section('title', 'Pengaturan Akun & Profil')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="profileManager()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">Pengaturan</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-user-gear text-[#C5A038]"></i>
                <span>Pengaturan Akun & Profil</span>
            </h1>
            <p class="text-xs text-gray-400">
                Kelola informasi akun Anda, perbarui foto profil, buat tanda tangan digital, dan ubah kata sandi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-bold font-mono
                @if($user->isManagingDirector()) bg-amber-500/20 text-[#C5A038] border border-[#C5A038]/30
                @elseif($user->role === 'superadmin') bg-blue-500/20 text-blue-400 border border-blue-500/30
                @else bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 @endif">
                <i class="fa-solid fa-shield-halved mr-1"></i>
                {{ strtoupper(str_replace('_', ' ', $user->role)) }}
            </span>
        </div>
    </div>

    <!-- TAB / SECTION 1: INFORMASI PROFIL & FOTO & TTD -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-3xl p-6 shadow-xl space-y-6">
        <div class="flex items-center gap-3 border-b border-[#2B3543] pb-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-[#C5A038]">
                <i class="fa-solid fa-id-card text-lg"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Identitas & Tanda Tangan Digital</h2>
                <p class="text-xs text-gray-400">Data ini digunakan untuk PIC marketing, laporan berkala, dan dokumen resmi.</p>
            </div>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Foto Profil (Avatar) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Foto Profil (Avatar)</label>
                <div class="flex flex-col sm:flex-row items-center gap-5">
                    <div class="relative group">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden bg-[#161B22] border-2 border-[#C5A038]/50 flex items-center justify-center shadow-lg shrink-0">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!avatarPreview">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-2xl font-black text-[#C5A038]">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                @endif
                            </template>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <label class="cursor-pointer px-4 py-2 bg-[#2B3543] hover:bg-[#384556] text-white rounded-xl font-semibold transition-all inline-flex items-center gap-2">
                                <i class="fa-solid fa-camera"></i>
                                <span>Pilih Foto Baru</span>
                                <input type="file" name="avatar" accept="image/*" class="hidden" @change="handleAvatarSelect">
                            </label>

                            @if($user->avatar)
                                <label class="cursor-pointer px-3 py-2 bg-red-950/60 hover:bg-red-900/60 text-red-300 border border-red-500/30 rounded-xl font-semibold transition-all inline-flex items-center gap-1.5">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded bg-[#161B22] border-red-500 text-red-600 focus:ring-0">
                                    <span>Hapus Foto</span>
                                </label>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-400">Format yang didukung: JPG, PNG, WebP. Maksimal 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Nama & Email & No Telepon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3.5 py-2.5 text-white text-xs focus:border-[#C5A038] focus:outline-none transition-colors">
                    @error('name')
                        <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Alamat Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3.5 py-2.5 text-white text-xs focus:border-[#C5A038] focus:outline-none transition-colors">
                    @error('email')
                        <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nomor WhatsApp / Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 628123456789"
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3.5 py-2.5 text-white text-xs focus:border-[#C5A038] focus:outline-none transition-colors">
                    @error('phone')
                        <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Bagian Tanda Tangan Digital (Signature Pad & File Upload) -->
            <div class="pt-4 border-t border-[#2B3543]/80 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#C5A038]">Tanda Tangan Digital (TTD)</label>
                        <p class="text-[11px] text-gray-400">TTD ini otomatis disertakan pada format cetak resmi laporan evaluasi & performa.</p>
                    </div>

                    <!-- Toggle Mode Input TTD: Canvas Manual vs Upload Gambar -->
                    <div class="flex items-center p-1 bg-[#161B22] border border-[#2B3543] rounded-xl text-[11px]">
                        <button type="button" @click="signMode = 'draw'" 
                                :class="signMode === 'draw' ? 'bg-[#C5A038] text-white font-bold' : 'text-gray-400 hover:text-white'"
                                class="px-3 py-1 rounded-lg transition-all">
                            <i class="fa-solid fa-signature mr-1"></i> Gores TTD Manual
                        </button>
                        <button type="button" @click="signMode = 'upload'" 
                                :class="signMode === 'upload' ? 'bg-[#C5A038] text-white font-bold' : 'text-gray-400 hover:text-white'"
                                class="px-3 py-1 rounded-lg transition-all">
                            <i class="fa-solid fa-upload mr-1"></i> Upload Gambar
                        </button>
                    </div>
                </div>

                <!-- Tampilan TTD Saat Ini jika ada -->
                @if($user->signature_url)
                    <div class="p-3 bg-[#161B22] border border-[#2B3543] rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-24 h-14 bg-white rounded-xl p-1.5 flex items-center justify-center border border-gray-300">
                                <img src="{{ $user->signature_url }}" class="max-h-full max-w-full object-contain">
                            </div>
                            <span class="text-xs text-gray-300">Tanda Tangan Aktif Saat Ini</span>
                        </div>
                        <label class="cursor-pointer text-xs text-red-400 hover:text-red-300 flex items-center gap-1.5">
                            <input type="checkbox" name="remove_signature" value="1" class="rounded bg-[#161B22] border-red-500 text-red-600 focus:ring-0">
                            <span>Hapus TTD</span>
                        </label>
                    </div>
                @endif

                <!-- MODE 1: Canvas TTD Manual -->
                <div x-show="signMode === 'draw'" class="space-y-2">
                    <div class="border-2 border-dashed border-[#2B3543] rounded-2xl p-2 bg-[#161B22] relative">
                        <canvas id="signatureCanvas" class="w-full h-40 bg-white rounded-xl cursor-crosshair touch-none"></canvas>
                        <button type="button" @click="clearSignature" 
                                class="absolute top-4 right-4 px-2.5 py-1 rounded-lg bg-gray-800/80 text-gray-200 hover:text-white hover:bg-gray-700 text-[11px] border border-gray-600 shadow-sm flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left"></i> Bersihkan Canvas
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400">Gunakan mouse atau jari Anda di canvas putih di atas untuk membubuhkan tanda tangan.</p>
                    <input type="hidden" name="signature_data" id="signatureDataInput">
                </div>

                <!-- MODE 2: Upload File Gambar TTD -->
                <div x-show="signMode === 'upload'" class="space-y-2" x-cloak>
                    <div class="p-4 bg-[#161B22] border border-[#2B3543] rounded-2xl space-y-2">
                        <label class="block text-xs text-gray-300 font-medium">Pilih Berkas Gambar TTD (PNG Transparan Disarankan)</label>
                        <input type="file" name="signature_file" accept="image/png,image/jpeg,image/webp" 
                               class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#2B3543] file:text-white hover:file:bg-[#384556] cursor-pointer">
                        <span class="text-[10px] text-gray-400 block">Maksimal 2MB. Format PNG, JPG, atau WebP.</span>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan Profil -->
            <div class="flex justify-end pt-2">
                <button type="submit" @click="prepareSubmit" 
                        class="px-6 py-2.5 bg-[#C5A038] hover:bg-[#B08C2B] text-white font-bold text-xs rounded-xl shadow-lg shadow-[#C5A038]/20 flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB / SECTION 2: KEAMANAN (GANTI PASSWORD) -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-3xl p-6 shadow-xl space-y-6">
        <div class="flex items-center gap-3 border-b border-[#2B3543] pb-4">
            <div class="w-10 h-10 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400">
                <i class="fa-solid fa-lock text-lg"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Keamanan & Ubah Password</h2>
                <p class="text-xs text-gray-400">Pastikan akun Anda menggunakan kombinasi kata sandi yang kuat dan aman.</p>
            </div>
        </div>

        <form action="{{ route('admin.profile.password.update') }}" method="POST" class="space-y-4 max-w-xl">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kata Sandi Saat Ini <span class="text-red-400">*</span></label>
                <input type="password" name="current_password" required placeholder="Masukkan kata sandi lama Anda"
                       class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3.5 py-2.5 text-white text-xs focus:border-[#C5A038] focus:outline-none transition-colors">
                @error('current_password')
                    <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kata Sandi Baru <span class="text-red-400">*</span></label>
                    <input type="password" name="password" required placeholder="Minimal 8 karakter"
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3.5 py-2.5 text-white text-xs focus:border-[#C5A038] focus:outline-none transition-colors">
                    @error('password')
                        <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Konfirmasi Kata Sandi Baru <span class="text-red-400">*</span></label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3.5 py-2.5 text-white text-xs focus:border-[#C5A038] focus:outline-none transition-colors">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="px-5 py-2.5 bg-[#2B3543] hover:bg-[#384556] text-white font-bold text-xs rounded-xl shadow-md flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-key"></i>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function profileManager() {
        return {
            signMode: 'draw',
            avatarPreview: null,
            canvas: null,
            ctx: null,
            isDrawing: false,
            hasDrawn: false,

            init() {
                this.$nextTick(() => {
                    this.setupCanvas();
                });
            },

            handleAvatarSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        this.avatarPreview = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            setupCanvas() {
                this.canvas = document.getElementById('signatureCanvas');
                if (!this.canvas) return;

                this.ctx = this.canvas.getContext('2d');
                
                // Set ukuran resolusi canvas
                const rect = this.canvas.getBoundingClientRect();
                this.canvas.width = rect.width;
                this.canvas.height = 160;

                this.ctx.strokeStyle = '#161B22';
                this.ctx.lineWidth = 2.5;
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';

                // Mouse events
                this.canvas.addEventListener('mousedown', (e) => this.startDraw(e));
                this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                window.addEventListener('mouseup', () => this.stopDraw());

                // Touch events (Mobile/Tablet)
                this.canvas.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    const touch = e.touches[0];
                    const mouseEvent = new MouseEvent('mousedown', {
                        clientX: touch.clientX,
                        clientY: touch.clientY
                    });
                    this.canvas.dispatchEvent(mouseEvent);
                }, { passive: false });

                this.canvas.addEventListener('touchmove', (e) => {
                    e.preventDefault();
                    const touch = e.touches[0];
                    const mouseEvent = new MouseEvent('mousemove', {
                        clientX: touch.clientX,
                        clientY: touch.clientY
                    });
                    this.canvas.dispatchEvent(mouseEvent);
                }, { passive: false });

                this.canvas.addEventListener('touchend', (e) => {
                    const mouseEvent = new MouseEvent('mouseup', {});
                    window.dispatchEvent(mouseEvent);
                });
            },

            startDraw(e) {
                this.isDrawing = true;
                this.hasDrawn = true;
                const rect = this.canvas.getBoundingClientRect();
                this.ctx.beginPath();
                this.ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
            },

            draw(e) {
                if (!this.isDrawing) return;
                const rect = this.canvas.getBoundingClientRect();
                this.ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
                this.ctx.stroke();
            },

            stopDraw() {
                if (this.isDrawing) {
                    this.isDrawing = false;
                }
            },

            clearSignature() {
                if (!this.ctx || !this.canvas) return;
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                this.hasDrawn = false;
                document.getElementById('signatureDataInput').value = '';
            },

            prepareSubmit() {
                if (this.signMode === 'draw' && this.hasDrawn && this.canvas) {
                    const dataUrl = this.canvas.toDataURL('image/png');
                    document.getElementById('signatureDataInput').value = dataUrl;
                }
            }
        };
    }
</script>
@endpush
