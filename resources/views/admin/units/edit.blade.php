@extends('layouts.admin')

@section('title', 'Edit Unit: ' . $unit->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Title -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Manajemen Unit</a>
                <span>/</span>
                <a href="{{ route('admin.units.show', $unit->id) }}" class="hover:text-[#C5A038]">{{ $unit->code }}</a>
                <span>/</span>
                <span class="text-gray-200">Edit</span>
            </div>
            <h1 class="text-xl font-bold text-white">Edit Data Unit Properti</h1>
        </div>
        <a href="{{ route('admin.units.show', $unit->id) }}" class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl border border-[#2B3543]">
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
    <form action="{{ route('admin.units.update', $unit->id) }}" method="POST" enctype="multipart/form-data" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Data Utama -->
        <div>
            <h3 class="text-sm font-bold text-[#C5A038] uppercase tracking-wider mb-4 border-b border-[#2B3543] pb-2">
                1. Identitas & Informasi Unit
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kode Unit <span class="text-red-400">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $unit->code) }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Nama / Judul Unit <span class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $unit->title) }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Developer Mitra</label>
                    <select name="developer_id" class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-300 focus:outline-none">
                        <option value="">Pilih Developer (Opsional)</option>
                        @foreach($developers as $dev)
                            <option value="{{ $dev->id }}" {{ old('developer_id', $unit->developer_id) == $dev->id ? 'selected' : '' }}>{{ $dev->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Tipe Properti <span class="text-red-400">*</span></label>
                    <select name="type" required class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-300 focus:outline-none">
                        <option value="Rumah Tinggal" {{ old('type', $unit->type) == 'Rumah Tinggal' ? 'selected' : '' }}>Rumah Tinggal</option>
                        <option value="Vila" {{ old('type', $unit->type) == 'Vila' ? 'selected' : '' }}>Vila & Resort</option>
                        <option value="Tanah" {{ old('type', $unit->type) == 'Tanah' ? 'selected' : '' }}>Tanah Kavling</option>
                        <option value="Kos-kosan" {{ old('type', $unit->type) == 'Kos-kosan' ? 'selected' : '' }}>Kost Eksklusif</option>
                        <option value="Ruko" {{ old('type', $unit->type) == 'Ruko' ? 'selected' : '' }}>Ruko / Komersial</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Harga (Rp) <span class="text-red-400">*</span></label>
                    <input type="number" name="price" value="{{ old('price', (int)$unit->price) }}" required min="0" step="1000"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 font-mono focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Status Ketersediaan <span class="text-red-400">*</span></label>
                    <select name="status" required class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-300 focus:outline-none">
                        <option value="Tersedia" {{ old('status', $unit->status) == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Booking" {{ old('status', $unit->status) == 'Booking' ? 'selected' : '' }}>Booking</option>
                        <option value="Terjual" {{ old('status', $unit->status) == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-300 mb-1">Catatan Perubahan Status (Riwayat)</label>
                    <input type="text" name="status_notes" placeholder="Tulis alasan jika mengubah status (misal: Konsumen telah membayar booking fee)..."
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-300 mb-1 flex items-center justify-between">
                        <span>Link Listing Eksternal / Website Luar <span class="text-gray-400 font-normal">(Opsional)</span></span>
                        <span class="text-[10px] text-gray-400 font-normal">Rumah123, Lamudi, OLX, Web Developer, dll</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-link absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="url" name="external_url" value="{{ old('external_url', $unit->external_url) }}" 
                               placeholder="https://www.rumah123.com/properti/... atau https://lamudi.co.id/..."
                               class="w-full pl-9 pr-4 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 placeholder-gray-500 text-xs focus:outline-none focus:border-[#C5A038]">
                    </div>
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
                    <input type="text" name="province" value="{{ old('province', $unit->province) }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kota / Kabupaten <span class="text-red-400">*</span></label>
                    <input type="text" name="city" value="{{ old('city', $unit->city) }}" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kecamatan (Subdistrict)</label>
                    <input type="text" name="district" value="{{ old('district', $unit->district) }}"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div class="sm:col-span-3">
                    <label class="block font-semibold text-gray-300 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" class="w-full px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">{{ old('address', $unit->address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Bagian 3: Spesifikasi Fisik -->
        <div>
            <h3 class="text-sm font-bold text-[#C5A038] uppercase tracking-wider mb-4 border-b border-[#2B3543] pb-2">
                3. Spesifikasi Bangunan
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kamar Tidur</label>
                    <input type="number" name="bedrooms" value="{{ old('bedrooms', $unit->bedrooms) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Kamar Mandi</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms', $unit->bathrooms) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Luas Tanah (m²)</label>
                    <input type="number" name="land_area" value="{{ old('land_area', $unit->land_area) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Luas Bangunan (m²)</label>
                    <input type="number" name="building_area" value="{{ old('building_area', $unit->building_area) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                </div>

                <div class="col-span-2">
                    <label class="block font-semibold text-gray-300 mb-1">Status Publikasi</label>
                    <label class="inline-flex items-center gap-2 mt-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $unit->is_published) ? 'checked' : '' }} class="rounded bg-[#14181E] border-[#2B3543] text-[#C5A038]">
                        <span class="text-gray-300 text-xs">Tayangkan langsung di Website Publik Wastu Property</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Bagian 4: Kelola Galeri Foto yang Ada -->
        <div>
            <h3 class="text-sm font-bold text-[#C5A038] uppercase tracking-wider mb-4 border-b border-[#2B3543] pb-2">
                4. Galeri Foto Properti
            </h3>
            
            @if($unit->photos->count())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    @foreach($unit->photos as $photo)
                        <div class="relative group aspect-video rounded-xl overflow-hidden border border-[#2B3543] bg-[#14181E]">
                            <img src="{{ $photo->url }}" class="w-full h-full object-cover">
                            
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <label class="cursor-pointer bg-white/20 hover:bg-white/40 text-white text-[10px] px-2 py-1 rounded">
                                    <input type="radio" name="primary_photo_id" value="{{ $photo->id }}" {{ $photo->is_primary ? 'checked' : '' }}> Utama
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <label class="block font-semibold text-gray-300 mb-1 text-xs">Tambah Foto Baru</label>
            <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                   class="block w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#C5A038] file:text-white hover:file:bg-[#B08C2B] cursor-pointer">
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-[#2B3543] flex items-center justify-end gap-3">
            <a href="{{ route('admin.units.show', $unit->id) }}" class="px-5 py-2.5 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-semibold rounded-xl shadow-lg transition-all hover:scale-105">
                Simpan Perubahan Unit
            </button>
        </div>
    </form>
</div>
@endsection
