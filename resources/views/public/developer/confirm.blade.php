<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Ketersediaan Unit - PT Wastu Pratama Wijaya</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-['Plus_Jakarta_Sans'] antialiased min-h-screen flex flex-col justify-between">

    <!-- Header Sederhana Profesional -->
    <header class="bg-white border-b border-gray-100 shadow-sm py-4">
        <div class="max-w-4xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#1F2731] flex items-center justify-center text-[#C5A038] font-black text-xl shadow-md">
                    W
                </div>
                <div>
                    <h1 class="text-sm font-bold text-gray-900 leading-tight">PT Wastu Pratama Wijaya</h1>
                    <p class="text-[10px] text-gray-500 font-medium">Portal Verifikasi & Konfirmasi Stok Developer</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                Akses Resmi Mitra
            </span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow py-8 px-4">
        <div class="max-w-2xl mx-auto space-y-6">

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- KONDISI 1: SUKSES SUBMIT -->
            @if(session('confirmed_success') || $isConfirmed)
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl text-center space-y-5">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl shadow-inner">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Terima Kasih atas Konfirmasi Anda!</h2>
                        <p class="text-xs text-gray-500 mt-1">Data ketersediaan unit telah berhasil diperbarui dan tersinkronisasi otomatis ke website dan dashboard pemasaran kami.</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 text-xs text-left space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Unit Properti:</span>
                            <span class="font-bold text-gray-900">{{ $availRequest->unit->title }} ({{ $availRequest->unit->code }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status Terkonfirmasi:</span>
                            <span class="font-bold text-emerald-600">{{ $availRequest->confirmed_unit_status }}</span>
                        </div>
                        @if($availRequest->notes)
                            <div class="pt-2 border-t border-gray-200">
                                <span class="text-gray-500 block mb-1">Catatan Anda:</span>
                                <p class="text-gray-700 italic bg-white p-2.5 rounded-xl border border-gray-200">{{ $availRequest->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <p class="text-[11px] text-gray-400">Anda dapat menutup halaman ini sekarang.</p>
                </div>

            <!-- KONDISI 2: TOKEN KEDALUWARSA -->
            @elseif($isExpired)
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-2xl shadow-inner">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Tautan Konfirmasi Kedaluwarsa</h2>
                        <p class="text-xs text-gray-500 mt-1">Masa berlaku tautan konfirmasi ini telah habis (batas waktu 48 jam). Mohon hubungi tim admin kami untuk permintaan pembaruan ketersediaan yang baru.</p>
                    </div>
                </div>

            <!-- KONDISI 3: FORMULIR KONFIRMASI AKTIF -->
            @else
                <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
                    
                    <!-- Detail Singkat Unit -->
                    <div class="p-6 bg-gradient-to-br from-[#1F2731] to-[#161B22] text-white space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#C5A038]/20 text-[#C5A038] border border-[#C5A038]/40">
                                {{ $availRequest->unit->code }}
                            </span>
                            <span class="text-[11px] text-gray-400">
                                Berakhir dalam: <strong class="text-amber-400">{{ $availRequest->expires_at->diffForHumans() }}</strong>
                            </span>
                        </div>

                        <div class="flex gap-4 items-center">
                            @if($availRequest->unit->photos->count() > 0)
                                <img src="{{ asset('storage/' . $availRequest->unit->photos->first()->photo_path) }}" 
                                     alt="{{ $availRequest->unit->title }}" 
                                     class="w-20 h-20 rounded-2xl object-cover border border-white/10 shrink-0">
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-white/5 flex items-center justify-center text-gray-500 shrink-0 border border-white/10">
                                    <i class="fa-solid fa-building text-2xl"></i>
                                </div>
                            @endif

                            <div>
                                <h2 class="text-base font-bold text-white leading-snug">{{ $availRequest->unit->title }}</h2>
                                <p class="text-xs text-[#C5A038] font-bold mt-1">Rp {{ number_format($availRequest->unit->price, 0, ',', '.') }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $availRequest->unit->district }}, {{ $availRequest->unit->city }}</p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[11px] text-gray-300">
                            <span>Status di Sistem Saat Ini:</span>
                            <span class="font-bold text-amber-400">{{ $availRequest->current_unit_status }}</span>
                        </div>
                    </div>

                    <!-- Formulir Konfirmasi Developer -->
                    <form action="{{ route('public.developer.confirm.submit', $availRequest->token) }}" method="POST" class="p-6 sm:p-8 space-y-5">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">
                                1. Konfirmasi Ketersediaan Unit Saat Ini <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="border-2 rounded-2xl p-3.5 flex items-center gap-3 cursor-pointer hover:border-emerald-500 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                                    <input type="radio" name="confirmed_unit_status" value="Tersedia" class="text-emerald-600 focus:ring-emerald-500" required
                                           {{ $availRequest->current_unit_status === 'Tersedia' ? 'checked' : '' }}>
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">Tersedia</div>
                                        <div class="text-[10px] text-gray-500">Siap dipasarkan</div>
                                    </div>
                                </label>

                                <label class="border-2 rounded-2xl p-3.5 flex items-center gap-3 cursor-pointer hover:border-amber-500 transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/50">
                                    <input type="radio" name="confirmed_unit_status" value="Booking" class="text-amber-600 focus:ring-amber-500"
                                           {{ $availRequest->current_unit_status === 'Booking' ? 'checked' : '' }}>
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">Booking</div>
                                        <div class="text-[10px] text-gray-500">Ada tanda jadi</div>
                                    </div>
                                </label>

                                <label class="border-2 rounded-2xl p-3.5 flex items-center gap-3 cursor-pointer hover:border-red-500 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50/50">
                                    <input type="radio" name="confirmed_unit_status" value="Terjual" class="text-red-600 focus:ring-red-500"
                                           {{ $availRequest->current_unit_status === 'Terjual' ? 'checked' : '' }}>
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">Terjual</div>
                                        <div class="text-[10px] text-gray-500">Sudah akad / lunas</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">
                                2. Perpanjang Masa Berlaku Listing (Opsional)
                            </label>
                            <input type="date" name="extended_valid_until" min="{{ date('Y-m-d') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-[#C5A038] focus:border-[#C5A038]">
                            <span class="text-[11px] text-gray-400 mt-1 block">Tentukan batas waktu pemasaran jika unit memiliki batas waktu kerja sama khusus.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">
                                3. Catatan Khusus untuk Tim Marketing (Opsional)
                            </label>
                            <textarea name="notes" rows="3" placeholder="Contoh: Unit siap serah terima akhir bulan, ada diskon khusus promo kemerdekaan, dll."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-[#C5A038] focus:border-[#C5A038]"></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full py-3.5 px-4 rounded-2xl bg-[#C5A038] hover:bg-[#b08d2e] active:scale-98 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-[#C5A038]/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Konfirmasi Ketersediaan</span>
                        </button>

                    </form>

                </div>
            @endif

        </div>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-gray-400 border-t border-gray-100 bg-white">
        &copy; {{ date('Y') }} PT Wastu Pratama Wijaya. Portal Resmi Developer & Agen Properti.
    </footer>

</body>
</html>
