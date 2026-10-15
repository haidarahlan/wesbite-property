@props([
    'code' => 'WST-001',
    'title' => 'Casa de Borobudur 2',
    'price' => 'Rp 650.000.000',
    'priceNumeric' => 650000000,
    'location' => 'Borobudur, Magelang',
    'type' => 'Rumah Tinggal',
    'status' => 'Tersedia', // Tersedia, Booking, Terjual
    'beds' => 3,
    'baths' => 2,
    'buildingArea' => 85,
    'landArea' => 120,
    'imageUrl' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
    'whatsappNumber' => '6281234567890'
])

@php
    $statusColor = match($status) {
        'Tersedia' => 'bg-emerald-500 text-white',
        'Booking' => 'bg-amber-500 text-white',
        'Terjual' => 'bg-red-500 text-white',
        default => 'bg-gray-500 text-white'
    };

    $waMessage = urlencode("Halo Wastu Property, saya tertarik dengan unit {$code} ({$title}).");
@endphp

<div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
    <!-- Image & Status Badge -->
    <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
        <img src="{{ $imageUrl }}" 
             alt="{{ $title }}" 
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        
        <!-- Status Badge -->
        <div class="absolute top-3 left-3 flex items-center gap-1.5">
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider {{ $statusColor }} shadow-sm">
                {{ $status }}
            </span>
            <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white shadow-sm">
                {{ $code }}
            </span>
        </div>

        <!-- Property Type Tag -->
        <div class="absolute bottom-3 left-3">
            <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-white/90 backdrop-blur-sm text-gray-800 shadow-sm">
                {{ $type }}
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="p-5 flex flex-col flex-grow">
        <!-- Location -->
        <div class="flex items-center gap-1 text-xs text-gray-500 mb-1.5">
            <i class="fa-solid fa-location-dot text-amber-600"></i>
            <span>{{ $location }}</span>
        </div>

        <!-- Title -->
        <h3 class="text-base font-bold text-gray-900 group-hover:text-[#C5A038] transition-colors line-clamp-1 mb-2">
            <a href="{{ url('/properti/' . Str::slug($title) . '-' . strtolower($code)) }}">
                {{ $title }}
            </a>
        </h3>

        <!-- Price -->
        <div class="mb-4">
            <span class="text-xs text-gray-400 font-medium block">Mulai Dari</span>
            <span class="text-lg font-extrabold text-[#C5A038] tracking-tight">{{ $price }}</span>
        </div>

        <!-- Specification Icons -->
        <div class="grid grid-cols-4 gap-2 pt-3 border-t border-gray-100 text-center text-xs text-gray-600 mb-5">
            <div class="flex flex-col items-center">
                <i class="fa-solid fa-bed text-gray-400 mb-1"></i>
                <span class="font-bold text-gray-800">{{ $beds }}</span>
                <span class="text-[10px] text-gray-400">K. Tidur</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fa-solid fa-bath text-gray-400 mb-1"></i>
                <span class="font-bold text-gray-800">{{ $baths }}</span>
                <span class="text-[10px] text-gray-400">K. Mandi</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fa-solid fa-ruler-combined text-gray-400 mb-1"></i>
                <span class="font-bold text-gray-800">{{ $buildingArea }}m²</span>
                <span class="text-[10px] text-gray-400">Bangunan</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fa-solid fa-vector-square text-gray-400 mb-1"></i>
                <span class="font-bold text-gray-800">{{ $landArea }}m²</span>
                <span class="text-[10px] text-gray-400">Tanah</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-auto grid grid-cols-2 gap-2 pt-2">
            <a href="{{ url('/properti/' . Str::slug($title) . '-' . strtolower($code)) }}" 
               class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                Detail Unit
            </a>
            
            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $waMessage }}" 
               target="_blank"
               class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all shadow-sm shadow-emerald-600/20">
                <i class="fa-brands fa-whatsapp mr-1.5 text-sm"></i> WhatsApp
            </a>
        </div>
    </div>
</div>
