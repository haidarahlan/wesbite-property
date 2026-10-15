<!-- Wastu Property Golden Footer -->
<footer class="bg-wastu-gradient text-gray-900 pt-16 pb-12 mt-20 relative overflow-hidden">
    <!-- Subtle architectural background pattern overlay -->
    <div class="absolute inset-0 opacity-5 pointer-events-none bg-[radial-gradient(#000_1px,transparent_1px)] [background-size:16px_16px]"></div>

    <div class="max-w-6xl mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-8 pb-12 border-b border-black/10">
            
            <!-- Kolom 1: LOKASI -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold tracking-widest uppercase text-gray-900">LOKASI</h4>
                <div class="space-y-3 text-xs leading-relaxed text-gray-800">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-location-dot mt-0.5 text-gray-900 text-sm"></i>
                        <span>Jl. Green Hills Ambarukmo No. A14, Dabag, Condongcatur, Depok, Sleman, DIY 55281</span>
                    </div>
                    <div class="flex items-start gap-2.5 pt-1">
                        <i class="fa-regular fa-clock mt-0.5 text-gray-900 text-sm"></i>
                        <span>Mon - Sat : 9.00 AM - 4.00 PM</span>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="pt-2">
                    <h5 class="text-[11px] font-bold tracking-wider uppercase text-gray-900 mb-2.5">SOCIAL MEDIA</h5>
                    <div class="flex items-center gap-2.5 text-gray-900">
                        <a href="https://instagram.com" target="_blank" class="w-7 h-7 rounded-full bg-black/80 text-white flex items-center justify-center hover:bg-black transition-colors" title="Instagram">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="https://tiktok.com" target="_blank" class="w-7 h-7 rounded-full bg-black/80 text-white flex items-center justify-center hover:bg-black transition-colors" title="TikTok">
                            <i class="fa-brands fa-tiktok text-xs"></i>
                        </a>
                        <a href="https://youtube.com" target="_blank" class="w-7 h-7 rounded-full bg-black/80 text-white flex items-center justify-center hover:bg-black transition-colors" title="YouTube">
                            <i class="fa-brands fa-youtube text-xs"></i>
                        </a>
                        <a href="https://facebook.com" target="_blank" class="w-7 h-7 rounded-full bg-black/80 text-white flex items-center justify-center hover:bg-black transition-colors" title="Facebook">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                        @php
                            $waFooter = \App\Models\SiteSetting::get('whatsapp_business', '6281234567890');
                        @endphp
                        <a href="https://wa.me/{{ $waFooter }}" target="_blank" class="w-7 h-7 rounded-full bg-black/80 text-white flex items-center justify-center hover:bg-black transition-colors" title="WhatsApp">
                            <i class="fa-brands fa-whatsapp text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: PERUSAHAAN KAMI -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold tracking-widest uppercase text-gray-900">PERUSAHAAN KAMI</h4>
                <ul class="space-y-2 text-xs text-gray-800">
                    <li><a href="{{ url('/#tentang-kami') }}" class="hover:text-black hover:font-semibold transition-all">Tentang Kami</a></li>
                    <li><a href="{{ url('/#visi-misi') }}" class="hover:text-black hover:font-semibold transition-all">Visi & Misi</a></li>
                    <li><a href="{{ route('public.properties') }}" class="hover:text-black hover:font-semibold transition-all">Daftar Properti</a></li>
                    <li><a href="{{ url('/sitemap.xml') }}" target="_blank" class="hover:text-black hover:font-semibold transition-all">Sitemap XML</a></li>
                </ul>
            </div>

            <!-- Kolom 3: LAYANAN KAMI -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold tracking-widest uppercase text-gray-900">LAYANAN KAMI</h4>
                <ul class="space-y-2 text-xs text-gray-800">
                    <li><a href="{{ url('/#layanan') }}" class="hover:text-black hover:font-semibold transition-all">Titip Jual Properti</a></li>
                    <li><a href="{{ url('/#layanan') }}" class="hover:text-black hover:font-semibold transition-all">Pembelian Properti</a></li>
                    <li><a href="{{ url('/#layanan') }}" class="hover:text-black hover:font-semibold transition-all">Jasa Developer</a></li>
                    <li><a href="{{ url('/#layanan') }}" class="hover:text-black hover:font-semibold transition-all">Agen Marketing</a></li>
                </ul>
            </div>

            <!-- Kolom 4: ASOSIASI KAMI -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold tracking-widest uppercase text-gray-900">ASOSIASI KAMI</h4>
                <div class="flex items-center gap-2">
                    <div class="w-12 h-10 bg-white/90 rounded border border-black/10 flex items-center justify-center p-1 shadow-sm" title="AREBI">
                        <span class="text-[9px] font-extrabold text-amber-700 tracking-tighter">AREBI</span>
                    </div>
                    <div class="w-12 h-10 bg-white/90 rounded border border-black/10 flex items-center justify-center p-1 shadow-sm" title="DEPRINDO">
                        <span class="text-[9px] font-extrabold text-green-700 tracking-tighter">DEPRINDO</span>
                    </div>
                    <div class="w-12 h-10 bg-white/90 rounded border border-black/10 flex items-center justify-center p-1 shadow-sm" title="KADIN">
                        <span class="text-[9px] font-extrabold text-blue-800 tracking-tighter">KADIN</span>
                    </div>
                </div>
            </div>

            <!-- Kolom 5: VERIFIED BY -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold tracking-widest uppercase text-gray-900">VERIFIED BY</h4>
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="h-8 px-2 bg-white/90 rounded border border-black/10 flex items-center justify-center shadow-sm">
                        <span class="text-[9px] font-bold text-orange-600">TrustMark</span>
                    </div>
                    <div class="h-8 px-2 bg-emerald-700 text-white rounded border border-black/10 flex items-center justify-center shadow-sm">
                        <span class="text-[9px] font-bold">KOMINFO</span>
                    </div>
                    <div class="h-8 px-2 bg-blue-700 text-white rounded border border-black/10 flex items-center justify-center shadow-sm">
                        <span class="text-[9px] font-bold">SSL 256-Bit</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Bottom Bar -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-gray-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded bg-white/80 border border-black/10 flex items-center justify-center text-[#9E7D22]">
                    <i class="fa-solid fa-house text-sm"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-serif font-bold tracking-wider text-sm leading-tight text-gray-900">WASTU PROPERTY</span>
                    <span class="text-[10px] text-gray-700">PT. WASTU PRATAMA WIJAYA</span>
                </div>
            </div>

            <p class="text-gray-800 text-center sm:text-right">
                PT. WASTU PRATAMA WIJAYA &copy; 2025 All rights reserved
            </p>
        </div>
    </div>
</footer>
