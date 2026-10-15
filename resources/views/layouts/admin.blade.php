<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard - CRM Wastu Property')</title>

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CDN for development fallback -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            wastu: {
                                gold: '#C5A038',
                                'gold-hover': '#B08C2B',
                                dark: '#1E242B',
                            }
                        },
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#1E242B] text-gray-300 hidden md:flex flex-col shrink-0">
            <!-- Brand -->
            <div class="h-16 flex items-center gap-3 px-6 border-b border-gray-800">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-[#C5A038]">
                    <i class="fa-solid fa-house-chimney text-sm"></i>
                </div>
                <div>
                    <span class="text-xs font-bold tracking-widest text-[#C5A038] uppercase block leading-none">WASTU</span>
                    <span class="text-[10px] text-gray-400 font-medium tracking-wider">CRM & CMS</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-grow p-4 space-y-1.5 text-xs font-medium">
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-amber-500/10 text-amber-400 font-semibold">
                    <i class="fa-solid fa-chart-pie text-sm"></i>
                    <span>Ringkasan Dashboard</span>
                </a>
                <a href="{{ url('/admin/units') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-gray-800/60 transition-colors">
                    <i class="fa-solid fa-building text-sm"></i>
                    <span>Manajemen Unit</span>
                </a>
                <a href="{{ url('/admin/cms') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-gray-800/60 transition-colors">
                    <i class="fa-solid fa-newspaper text-sm"></i>
                    <span>CMS Company Profile</span>
                </a>
                <a href="{{ url('/') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-gray-800/60 transition-colors">
                    <i class="fa-solid fa-globe text-sm"></i>
                    <span>Lihat Web Publik &nearr;</span>
                </a>
            </nav>

            <!-- User Info Bar -->
            <div class="p-4 border-t border-gray-800 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-xs text-white font-bold">
                    A
                </div>
                <div class="flex-grow">
                    <span class="text-xs font-semibold text-white block">Administrator</span>
                    <span class="text-[10px] text-gray-400">admin@wastuproperty.co.id</span>
                </div>
            </div>
        </aside>

        <!-- Main Body -->
        <div class="flex-grow flex flex-col min-w-0">
            <!-- Topbar Header -->
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6">
                <h1 class="text-sm font-bold text-gray-800">@yield('page_title', 'Dashboard')</h1>
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" target="_blank" class="text-xs font-medium text-amber-700 hover:underline">
                        Buka Website Publik &nearr;
                    </a>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="p-6 flex-grow">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
