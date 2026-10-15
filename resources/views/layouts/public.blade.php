<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Wastu Property - Agen Properti Profesional Indonesia | PT. Wastu Pratama Wijaya')</title>
    <meta name="description" content="@yield('meta_description', 'Wastu Property (PT. Wastu Pratama Wijaya) - Agen Properti Profesional melayani jual beli, titip jual, jasa developer di Jogja, Jakarta, Bali, Malang.')">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Tailwind & App Styles -->
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
                                'gold-light': '#FDF8EA',
                                dark: '#1E242B',
                                charcoal: '#2C353F',
                                sand: '#F5F2EB'
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
    @endif

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-serif-title {
            font-family: 'Playfair Display', serif;
        }
        /* Custom gold gradient for footer and badges */
        .bg-wastu-gradient {
            background: linear-gradient(180deg, #F9E59B 0%, #F5DE88 35%, #F0D472 70%, #E8C858 100%);
        }
        .glass-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-[#C5A038] selection:text-white flex flex-col min-h-screen">

    <!-- Header / Floating Navbar -->
    @include('components.navbar')

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    @stack('scripts')
</body>
</html>
