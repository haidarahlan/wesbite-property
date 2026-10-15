@extends('layouts.public')

@section('title', 'Daftar Properti Pilihan - Wastu Property')
@section('meta_description', 'Cari dan temukan rumah tinggal, villa, tanah kavling, dan kos eksklusif di Jogja, Magelang, Solo, Bali dengan harga terbaik.')

@section('content')
<div class="pt-28 pb-16 max-w-6xl mx-auto px-6 space-y-8">
    
    <!-- Header -->
    <div class="text-center max-w-xl mx-auto">
        <div class="inline-flex items-center gap-2 justify-center mb-2">
            <span class="w-8 h-0.5 bg-[#C5A038]"></span>
            <span class="text-xs font-bold uppercase tracking-widest text-[#C5A038]">LISTING PROPERTI</span>
            <span class="w-8 h-0.5 bg-[#C5A038]"></span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 font-serif">Katalog Properti Terverifikasi</h1>
        <p class="text-xs text-gray-500 mt-2">Temukan hunian idaman atau aset investasi properti dengan lokasi strategis.</p>
    </div>

    <!-- Search & Filter Form -->
    <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm">
        <form action="{{ url('/properti') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-gray-600 mb-1">Cari Kata Kunci</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode unit, nama perumahan..."
                       class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-[#C5A038]">
            </div>

            <div>
                <label class="block font-semibold text-gray-600 mb-1">Lokasi / Kota</label>
                <select name="city" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 focus:outline-none">
                    <option value="all">Semua Kota</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-600 mb-1">Tipe Properti</label>
                <select name="type" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 focus:outline-none">
                    <option value="all">Semua Tipe</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#C5A038] hover:bg-[#B08C2B] text-white font-semibold flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Units Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
        @forelse($units as $unit)
            <x-property-card 
                :code="$unit->code"
                :title="$unit->title"
                :price="$unit->formatted_price"
                :priceNumeric="(int)$unit->price"
                :location="($unit->district ? $unit->district . ', ' : '') . $unit->city"
                :type="$unit->type"
                :status="$unit->status"
                :beds="$unit->bedrooms"
                :baths="$unit->bathrooms"
                :buildingArea="$unit->building_area"
                :landArea="$unit->land_area"
                :imageUrl="$unit->primary_photo_url"
                :whatsappNumber="$whatsappNumber"
            />
        @empty
            <div class="col-span-3 py-16 text-center">
                <p class="text-xs text-gray-500">Tidak ada unit properti yang cocok dengan kriteria filter Anda.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center pt-6">
        {{ $units->links() }}
    </div>

</div>
@endsection
