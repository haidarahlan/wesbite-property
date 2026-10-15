<!-- Floating Pill Navbar -->
<header x-data="{ mobileMenuOpen: false, scrolly: false }" 
        @scroll.window="scrolly = (window.pageYOffset > 20)"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 pt-4 sm:pt-6 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="glass-navbar border border-white/60 shadow-lg shadow-black/5 rounded-full px-5 sm:px-8 py-3 sm:py-3.5 flex items-center justify-between transition-all"
             :class="{ 'shadow-xl bg-white/95': scrolly }">
            
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-600 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-house-chimney text-lg text-[#C5A038]"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold tracking-wider text-[#C5A038] uppercase leading-none font-serif">WASTU</span>
                    <span class="text-xs font-semibold tracking-widest text-[#B08C2B] uppercase leading-tight">PROPERTY</span>
                </div>
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="hidden md:flex items-center gap-7 lg:gap-8 text-sm font-medium text-gray-700">
                <a href="{{ url('/') }}" class="hover:text-[#C5A038] transition-colors {{ request()->is('/') ? 'text-[#C5A038] font-semibold' : '' }}">
                    Home
                </a>
                
                <!-- Dropdown: Tentang -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="flex items-center gap-1.5 hover:text-[#C5A038] transition-colors py-1 cursor-pointer">
                        <span>Tentang</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200" :class="{ 'rotate-180 text-[#C5A038]': open }"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute top-full left-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2.5 px-1.5 z-50">
                        <a href="{{ url('/#tentang-kami') }}" class="block px-3.5 py-2 text-xs font-medium text-gray-700 hover:bg-amber-50 hover:text-[#C5A038] rounded-xl transition-colors">
                            Tentang Kami
                        </a>
                        <a href="{{ url('/#visi-misi') }}" class="block px-3.5 py-2 text-xs font-medium text-gray-700 hover:bg-amber-50 hover:text-[#C5A038] rounded-xl transition-colors">
                            Visi & Misi
                        </a>
                        <a href="{{ url('/#kemitraan') }}" class="block px-3.5 py-2 text-xs font-medium text-gray-700 hover:bg-amber-50 hover:text-[#C5A038] rounded-xl transition-colors">
                            Kemitraan Developer
                        </a>
                    </div>
                </div>

                <!-- Dropdown: Layanan -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="flex items-center gap-1.5 hover:text-[#C5A038] transition-colors py-1 cursor-pointer">
                        <span>Layanan</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200" :class="{ 'rotate-180 text-[#C5A038]': open }"></i>
                    </button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute top-full left-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2.5 px-1.5 z-50">
                        <a href="{{ url('/#layanan') }}" class="block px-3.5 py-2 text-xs font-medium text-gray-700 hover:bg-amber-50 hover:text-[#C5A038] rounded-xl transition-colors">
                            Titip Jual Properti
                        </a>
                        <a href="{{ url('/#layanan') }}" class="block px-3.5 py-2 text-xs font-medium text-gray-700 hover:bg-amber-50 hover:text-[#C5A038] rounded-xl transition-colors">
                            Pembelian Properti
                        </a>
                        <a href="{{ url('/#layanan') }}" class="block px-3.5 py-2 text-xs font-medium text-gray-700 hover:bg-amber-50 hover:text-[#C5A038] rounded-xl transition-colors">
                            Jasa Developer & Marketing
                        </a>
                    </div>
                </div>

                <a href="{{ url('/#properti') }}" class="hover:text-[#C5A038] transition-colors">
                    Daftar Properti
                </a>

                <a href="{{ url('/#artikel') }}" class="hover:text-[#C5A038] transition-colors">
                    Artikel
                </a>

                <a href="{{ url('/#kontak') }}" class="hover:text-[#C5A038] transition-colors">
                    Kontak
                </a>
            </nav>

            <!-- CTA Button & Mobile Trigger -->
            <div class="flex items-center gap-3">
                <a href="https://wa.me/6281234567890?text=Halo%20Wastu%20Property,%20saya%20ingin%20berkonsultasi" 
                   target="_blank"
                   class="hidden sm:inline-flex items-center justify-center px-6 py-2 rounded-full text-xs sm:text-sm font-semibold text-white shadow-md shadow-amber-600/20 bg-[#C5A038] hover:bg-[#B08C2B] active:scale-95 transition-all">
                    Hubungi Kami
                </a>

                <!-- Mobile Hamburger Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 hover:bg-gray-200 focus:outline-none">
                    <i class="fa-solid fa-bars text-sm" x-show="!mobileMenuOpen"></i>
                    <i class="fa-solid fa-xmark text-sm" x-show="mobileMenuOpen"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden mt-2 bg-white/95 backdrop-blur-md border border-gray-100 shadow-2xl rounded-3xl p-5 space-y-3">
            <a @click="mobileMenuOpen = false" href="{{ url('/') }}" class="block px-3 py-2 text-sm font-semibold text-gray-800 hover:text-[#C5A038] hover:bg-amber-50/50 rounded-xl">Home</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#tentang-kami') }}" class="block px-3 py-2 text-sm font-semibold text-gray-800 hover:text-[#C5A038] hover:bg-amber-50/50 rounded-xl">Tentang Kami</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#layanan') }}" class="block px-3 py-2 text-sm font-semibold text-gray-800 hover:text-[#C5A038] hover:bg-amber-50/50 rounded-xl">Layanan</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#properti') }}" class="block px-3 py-2 text-sm font-semibold text-gray-800 hover:text-[#C5A038] hover:bg-amber-50/50 rounded-xl">Daftar Properti</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#artikel') }}" class="block px-3 py-2 text-sm font-semibold text-gray-800 hover:text-[#C5A038] hover:bg-amber-50/50 rounded-xl">Artikel</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#kontak') }}" class="block px-3 py-2 text-sm font-semibold text-gray-800 hover:text-[#C5A038] hover:bg-amber-50/50 rounded-xl">Kontak</a>
            <div class="pt-2">
                <a href="https://wa.me/6281234567890?text=Halo%20Wastu%20Property,%20saya%20ingin%20berkonsultasi" 
                   target="_blank"
                   class="w-full inline-flex items-center justify-center px-5 py-2.5 rounded-full text-sm font-semibold text-white bg-[#C5A038] hover:bg-[#B08C2B] shadow-md">
                    <i class="fa-brands fa-whatsapp mr-2 text-base"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</header>
