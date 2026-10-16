<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard CRM - PT Wastu Pratama Wijaya')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Support Dark Mode Theme) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        wastu: {
                            gold: '#C5A038',
                            'gold-hover': '#B08C2B',
                            dark: '#14181E',
                            card: '#1F2731',
                            cardBorder: '#2B3543',
                            blue: '#1A73E8'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js & FontAwesome -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js & Leaflet (Peta Sebaran) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #14181E;
        }
        /* Custom scrollbar for dark tables */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #14181E;
        }
        ::-webkit-scrollbar-thumb {
            background: #2B3543;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #C5A038;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#14181E] text-gray-200 antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

    <!-- Top Navigation Bar dengan Logo Emas Wastu (Sesuai Referensi Gambar) -->
    <header class="bg-[#1B222B] border-b border-[#2B3543] px-6 py-3.5 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-400 hover:text-white">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>

            <!-- Brand Logo (Sesuai Screenshot Gambar) -->
            <a href="{{ route('admin.units.index') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-[#C5A038]">
                    <i class="fa-solid fa-house-chimney text-base"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-extrabold tracking-wider text-[#C5A038] uppercase font-serif leading-none">WASTU</span>
                    <span class="text-[10px] font-bold tracking-widest text-amber-500/80 uppercase leading-tight">PROPERTY</span>
                </div>
            </a>
        </div>

        <!-- Right Quick Action & User Info -->
        <div class="flex items-center gap-4">
            <a href="{{ url('/') }}" target="_blank" 
               class="hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-[#232B35] hover:bg-[#2B3543] text-gray-300 hover:text-white text-xs border border-[#2B3543] transition-all">
                <i class="fa-solid fa-globe text-[#C5A038]"></i>
                <span>Website Publik</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-gray-400"></i>
            </a>

            <div class="h-6 w-px bg-[#2B3543] hidden sm:block"></div>

            @auth
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#C5A038]/20 border border-[#C5A038]/40 flex items-center justify-center text-[#C5A038] text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:flex flex-col text-left">
                        <span class="text-xs font-bold text-gray-200">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-amber-400/80 font-medium uppercase">
                            {{ str_replace('_', ' ', auth()->user()->role) }}
                        </span>
                    </div>

                    <!-- Tombol Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg bg-[#232B35] text-gray-400 hover:text-red-400 hover:bg-[#2B3543] transition-colors" title="Keluar">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <div class="flex flex-grow">
        <!-- Sidebar Navigation -->
        <aside class="w-60 bg-[#161B22] border-r border-[#2B3543] shrink-0 p-4 space-y-2 hidden md:block">
            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 px-3 mb-2">MODUL CRM</div>
            
            <a href="{{ route('admin.units.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.units.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                <i class="fa-solid fa-building text-sm"></i>
                <span>Manajemen Unit</span>
            </a>

            <a href="{{ route('admin.leads.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.leads.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-users text-sm text-blue-400"></i>
                    <span>Manajemen Leads</span>
                </div>
                @php
                    $unreadLeadCount = \App\Models\Lead::where('status', 'Baru')->count();
                @endphp
                @if($unreadLeadCount > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-500 text-white rounded-full">
                        {{ $unreadLeadCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.surveys.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.surveys.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-calendar-check text-sm text-purple-400"></i>
                    <span>Survei Lapangan</span>
                </div>
                @php
                    $tomorrowSurveyCount = \App\Models\Survey::where('status', 'Terjadwal')
                        ->whereDate('survey_date', \Carbon\Carbon::tomorrow())
                        ->count();
                @endphp
                @if($tomorrowSurveyCount > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-500 text-white rounded-full" title="Survei H-1 Besok">
                        {{ $tomorrowSurveyCount }}
                    </span>
                @endif
            </a>

            @if(auth()->check() && auth()->user()->isSuperAdmin())
                <div class="pt-4 text-[10px] font-bold uppercase tracking-widest text-amber-500/80 px-3 mb-1">
                    {{ auth()->user()->isManagingDirector() ? 'MANAGING DIRECTOR' : 'SUPERADMIN ONLY' }}
                </div>
                
                <a href="{{ route('admin.acquisitions.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.acquisitions.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                    <i class="fa-solid fa-bullseye text-sm text-rose-400"></i>
                    <span>Target Unit</span>
                </a>

                <a href="{{ route('admin.cms.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.cms.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                    <i class="fa-solid fa-sliders text-sm text-[#C5A038]"></i>
                    <span>CMS Company Profile</span>
                </a>

                <a href="{{ route('admin.developers.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.developers.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                    <i class="fa-solid fa-handshake text-sm text-amber-400"></i>
                    <span>Mitra Developer</span>
                </a>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                    <i class="fa-solid fa-users-gear text-sm"></i>
                    <span>Manajemen User</span>
                </a>

                <div class="pt-4 text-[10px] font-bold uppercase tracking-widest text-gray-400 px-3 mb-1">ANALITIK & LAPORAN</div>
                
                {{-- Executive Dashboard HANYA untuk Managing Director --}}
                @if(auth()->user()->isManagingDirector())
                    <a href="{{ route('admin.analytics.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.analytics.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                        <i class="fa-solid fa-chart-line text-sm text-cyan-400"></i>
                        <span>Executive Dashboard</span>
                    </a>
                @endif

                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.reports.*') ? 'bg-[#232B35] text-[#C5A038] border border-[#C5A038]/30 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-[#1F2731]' }} transition-all">
                    <i class="fa-solid fa-file-invoice text-sm text-emerald-400"></i>
                    <span>Generator Laporan</span>
                </a>
            @endif
        </aside>

        <!-- Main Content Dark Area -->
        <main class="flex-grow p-4 sm:p-6 lg:p-8 min-w-0">
            <!-- Flash Message -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-950/50 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-950/50 border border-red-500/40 text-red-300 text-xs flex items-center gap-3 shadow-lg">
                    <i class="fa-solid fa-circle-exclamation text-red-400 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
