@extends('layouts.admin')

@section('title', 'Tambah Unit Properti Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Title -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Manajemen Unit</a>
                <span>/</span>
                <span class="text-gray-200">Tambah Unit</span>
            </div>
            <h1 class="text-xl font-bold text-white">Form Input Unit Properti Baru</h1>
        </div>
        <a href="{{ route('admin.units.index') }}" class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl border border-[#2B3543]">
            Kembali
        </a>
    </div>

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-950/60 border border-red-500/40 text-red-300 text-xs">
            <span class="font-bold block mb-1">Terdapat kesalahan pengisian data:</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <form action="{{ route('admin.units.store') }}" method="POST" enctype="multipart/form-data" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 sm:p-8 space-y-6">
        @csrf

        <!-- Bagian 1: Data Utama -->
        <div>
            <h3 class="text-sm font-bold text-[#C5A038] uppercase tracking-wider mb-4 border-b border-[#2B3543] pb-2">
                1. Identitas & Informasi Unit
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kode Unit <span class="text-red-400">*</span></label>
                    <input type="text" name="code" value="{{ old('code', 'WP-' . strtoupper(Str::random(5))) }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Nama / Judul Unit <span class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Casa de Borobudur 2 Tipe Limasan" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Developer Mitra</label>
                    <select name="developer_id" class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-300 focus:outline-none">
                        <option value="">Pilih Developer (Opsional)</option>
                        @foreach($developers as $dev)
                            <option value="{{ $dev->id }}" {{ old('developer_id') == $dev->id ? 'selected' : '' }}>{{ $dev->name }} ({{ $dev->company_name ?? 'Independen' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Tipe Properti <span class="text-red-400">*</span></label>
                    <select name="type" required class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-300 focus:outline-none">
                        <option value="Rumah Tinggal" {{ old('type') == 'Rumah Tinggal' ? 'selected' : '' }}>Rumah Tinggal</option>
                        <option value="Vila" {{ old('type') == 'Vila' ? 'selected' : '' }}>Vila & Resort</option>
                        <option value="Tanah" {{ old('type') == 'Tanah' ? 'selected' : '' }}>Tanah Kavling</option>
                        <option value="Kos-kosan" {{ old('type') == 'Kos-kosan' ? 'selected' : '' }}>Kost Eksklusif</option>
                        <option value="Ruko" {{ old('type') == 'Ruko' ? 'selected' : '' }}>Ruko / Komersial</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Harga (Rp) <span class="text-red-400">*</span></label>
                    <input type="number" name="price" value="{{ old('price') }}" placeholder="Contoh: 650000000" required min="0" step="1000"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 font-mono focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Status Ketersediaan <span class="text-red-400">*</span></label>
                    <select name="status" required class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-300 focus:outline-none">
                        <option value="Tersedia" {{ old('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Booking" {{ old('status') == 'Booking' ? 'selected' : '' }}>Booking</option>
                        <option value="Terjual" {{ old('status') == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Lokasi -->
        <div>
            <h3 class="text-sm font-bold text-[#C5A038] uppercase tracking-wider mb-4 border-b border-[#2B3543] pb-2">
                2. Lokasi & Titik Koordinat Peta
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Provinsi <span class="text-red-400">*</span></label>
                    <input type="text" name="province" value="{{ old('province', 'DI Yogyakarta') }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kota / Kabupaten <span class="text-red-400">*</span></label>
                    <input type="text" name="city" value="{{ old('city', 'Sleman') }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kecamatan (Subdistrict)</label>
                    <input type="text" name="district" value="{{ old('district', 'Depok') }}"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div class="sm:col-span-3">
                    <label class="block font-semibold text-gray-300 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" class="w-full px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Latitude (Koordinat)</label>
                    <input type="text" name="latitude" value="{{ old('latitude', '-7.7600') }}" placeholder="-7.7600"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 font-mono focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Longitude (Koordinat)</label>
                    <input type="text" name="longitude" value="{{ old('longitude', '110.3800') }}" placeholder="110.3800"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 font-mono focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Masa Berlaku Ketersediaan</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until') }}"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Bagian 3: Spesifikasi Fisik -->
        <div>
            <h3 class="text-sm font-bold text-[#C5A038] uppercase tracking-wider mb-4 border-b border-[#2B3543] pb-2">
                3. Spesifikasi Bangunan & Fasilitas
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kamar Tidur</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms', 3) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kamar Mandi</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms', 2) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Luas Tanah (m²)</label>
                    <input type="number" name="land_area" value="{{ old('land_area', 120) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Luas Bangunan (m²)</label>
                    <input type="number" name="building_area" value="{{ old('building_area', 85) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Sertifikat</label>
                    <input type="text" name="certificate" value="{{ old('certificate', 'SHM (Sertifikat Hak Milik)') }}" placeholder="SHM / HGB"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Daya Listrik</label>
                    <input type="text" name="electricity" value="{{ old('electricity', '2200 VA') }}" placeholder="1300 VA / 2200 VA"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div class="col-span-2">
                    <label class="block font-semibold text-gray-300 mb-1">Status Publikasi</label>
                    <label class="inline-flex items-center gap-2 mt-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', 1) ? 'checked' : '' }} class="rounded bg-[#14181E] border-[#2B3543] text-[#C5A038]">
                        <span class="text-gray-300 text-xs">Tayangkan langsung di Website Publik Wastu Property</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Bagian 4: Upload Galeri Multi-Foto -->
        <div>
            <h3 class="text-sm font-bold text-[#C5A038] uppercase tracking-wider mb-4 border-b border-[#2B3543] pb-2">
                4. Galeri Foto Properti (Multi Foto)
            </h3>
            <div class="space-y-3">
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                       class="block w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#C5A038] file:text-white hover:file:bg-[#B08C2B] cursor-pointer">
                <p class="text-[11px] text-gray-400">Pilih satu atau beberapa foto sekaligus (JPG, PNG, WebP maksimal 5MB per file). Foto pertama otomatis menjadi foto utama.</p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-[#2B3543] flex items-center justify-end gap-3">
            <a href="{{ route('admin.units.index') }}" class="px-5 py-2.5 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-semibold rounded-xl shadow-lg transition-all hover:scale-105">
                Simpan Unit Properti
            </button>
        </div>
    </form>
</div>
@endsection
