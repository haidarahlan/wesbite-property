@extends('layouts.admin')

@section('page_title', 'Manajemen Unit Properti')

@section('content')
<div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Daftar Unit & Listing</h2>
            <p class="text-xs text-gray-500">Kelola ketersediaan, spesifikasi, dan galeri unit properti.</p>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-semibold rounded-xl shadow-sm transition-all">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Unit Baru</span>
        </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center gap-3">
        <div class="flex-grow min-w-[200px] relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Cari kode unit, nama atau developer..." 
                   class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-[#C5A038]">
        </div>
        <select class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:outline-none">
            <option>Semua Status</option>
            <option>Tersedia</option>
            <option>Booking</option>
            <option>Terjual</option>
        </select>
        <select class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:outline-none">
            <option>Semua Tipe</option>
            <option>Rumah Tinggal</option>
            <option>Vila</option>
            <option>Tanah</option>
            <option>Kos-kosan</option>
        </select>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-500 uppercase font-semibold">
                <tr>
                    <th class="py-3 px-4">Unit</th>
                    <th class="py-3 px-4">Tipe & Lokasi</th>
                    <th class="py-3 px-4">Harga</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Publish</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                <tr class="hover:bg-amber-50/30 transition-colors">
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=100&q=80" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 block">Casa de Borobudur 2</span>
                                <span class="text-[10px] text-gray-400 font-mono">WP-BORO-01</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="block font-medium">Rumah Tinggal</span>
                        <span class="text-[10px] text-gray-400">Borobudur, Magelang</span>
                    </td>
                    <td class="py-3 px-4 font-bold text-gray-900">Rp 650.000.000</td>
                    <td class="py-3 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Tersedia</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">Published</span>
                    </td>
                    <td class="py-3 px-4 text-right space-x-2">
                        <button class="text-gray-400 hover:text-amber-600"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
