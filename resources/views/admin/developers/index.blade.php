@extends('layouts.admin')

@section('title', 'Manajemen Mitra Developer')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ openCreateModal: false, selectedDev: null, openEditModal: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">Mitra Developer</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-handshake text-[#C5A038]"></i>
                <span>Manajemen Mitra Developer</span>
            </h1>
            <p class="text-xs text-gray-400">Kelola daftar pengembang / pemilik properti untuk koordinasi listing dan cek ketersediaan unit stok.</p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="openCreateModal = true"
                    class="px-4 py-2.5 bg-[#C5A038] hover:bg-[#b08d2e] text-white text-xs font-semibold rounded-xl shadow-lg shadow-[#C5A038]/20 flex items-center gap-2 transition-all cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Developer Baru</span>
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-gray-400 mb-1">Total Mitra Developer</div>
            <div class="text-xl font-black text-white">{{ $totalDevelopers }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Pengembang terdaftar dalam sistem</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-emerald-400 mb-1">Kerja Sama Aktif</div>
            <div class="text-xl font-black text-emerald-400">{{ $activeDevelopers }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Siap menerima cek ketersediaan</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-amber-400 mb-1">Penjajakan / Nonaktif</div>
            <div class="text-xl font-black text-amber-400">{{ $inactiveDevelopers }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Status kerja sama dalam proses</div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.developers.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <label class="block text-[11px] text-gray-400 mb-1">Cari Developer (Nama / PT / Kontak)</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama developer..."
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white placeholder-gray-500 focus:border-[#C5A038] focus:outline-none pl-8">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-gray-500"></i>
                </div>
            </div>

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">Status Kerja Sama</label>
                <select name="status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    <option value="all">Semua Status</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Dalam Penjajakan" {{ request('status') === 'Dalam Penjajakan' ? 'selected' : '' }}>Dalam Penjajakan</option>
                    <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#2B3543] hover:bg-[#384455] text-white font-semibold rounded-xl transition-all">
                    Terapkan
                </button>
                <a href="{{ route('admin.developers.index') }}" class="px-3 py-2 bg-[#161B22] hover:bg-[#232B35] text-gray-400 hover:text-white rounded-xl border border-[#2B3543] transition-all" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Developer -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-[11px] text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Nama Developer & Perusahaan</th>
                        <th class="py-3 px-4">Kontak WhatsApp / HP</th>
                        <th class="py-3 px-4">Email & Alamat</th>
                        <th class="py-3 px-4">Status Kerja Sama</th>
                        <th class="py-3 px-4">Jumlah Listing Unit</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543]/60">
                    @forelse($developers as $dev)
                        <tr class="hover:bg-[#232B35]/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-white text-xs">{{ $dev->name }}</div>
                                <div class="text-[11px] text-[#C5A038] font-medium">{{ $dev->company_name ?? '-' }}</div>
                            </td>

                            <td class="py-3 px-4">
                                <div class="font-mono text-gray-200">{{ $dev->phone }}</div>
                                <a href="https://wa.me/{{ $dev->phone }}" target="_blank" class="text-[10px] text-emerald-400 hover:underline inline-flex items-center gap-1 mt-0.5">
                                    <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                                </a>
                            </td>

                            <td class="py-3 px-4">
                                <div class="text-gray-300">{{ $dev->email ?? '-' }}</div>
                                <div class="text-[10px] text-gray-500 truncate max-w-[200px]">{{ $dev->address ?? '-' }}</div>
                            </td>

                            <td class="py-3 px-4">
                                @php
                                    $stClass = match($dev->status) {
                                        'Aktif' => 'bg-emerald-950/60 text-emerald-300 border-emerald-500/30',
                                        'Dalam Penjajakan' => 'bg-amber-950/60 text-amber-300 border-amber-500/30',
                                        default => 'bg-gray-800 text-gray-400 border-gray-600',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $stClass }}">
                                    {{ $dev->status }}
                                </span>
                            </td>

                            <td class="py-3 px-4">
                                <a href="{{ route('admin.units.index', ['developer_id' => $dev->id]) }}" 
                                   class="px-2.5 py-1 rounded-lg bg-[#161B22] hover:bg-[#232B35] text-[#C5A038] hover:text-white font-bold border border-[#2B3543] transition-all inline-flex items-center gap-1.5"
                                   title="Klik untuk melihat semua listing unit milik developer ini">
                                    <i class="fa-solid fa-building text-[10px]"></i>
                                    <span>{{ $dev->units_count }} Unit</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-gray-500"></i>
                                </a>
                            </td>

                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button @click="selectedDev = {{ $dev->toJson() }}; openEditModal = true"
                                            class="p-1.5 rounded-lg bg-[#232B35] hover:bg-[#C5A038] hover:text-white text-gray-300 border border-[#2B3543] transition-all"
                                            title="Edit Data Developer">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    @if($dev->units_count == 0)
                                        <form action="{{ route('admin.developers.destroy', $dev->id) }}" method="POST" onsubmit="return confirm('Hapus data developer ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-[#232B35] hover:bg-red-600 hover:text-white text-gray-400 border border-[#2B3543] transition-all" title="Hapus Developer">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <i class="fa-solid fa-handshake-slash text-3xl mb-2 text-gray-600 block"></i>
                                Belum ada data mitra developer yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($developers->hasPages())
            <div class="p-4 bg-[#161B22] border-t border-[#2B3543]">
                {{ $developers->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL 1: TAMBAH DEVELOPER BARU -->
    <div x-show="openCreateModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openCreateModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-plus text-[#C5A038]"></i>
                    <span>Tambah Mitra Developer Baru</span>
                </h3>
                <button @click="openCreateModal = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('admin.developers.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Nama PIC Developer <span class="text-red-400">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Bpk. Haryanto"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Nama Perusahaan / PT / CV</label>
                        <input type="text" name="company_name" placeholder="Contoh: PT Graha Sentosa"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Nomor WhatsApp <span class="text-red-400">*</span></label>
                        <input type="text" name="phone" required placeholder="081234567890"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Email Kontak</label>
                        <input type="email" name="email" placeholder="kontak@grahasentosa.com"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Status Kerja Sama</label>
                    <select name="status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        <option value="Aktif">Aktif</option>
                        <option value="Dalam Penjajakan">Dalam Penjajakan</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Alamat Kantor / Domisili</label>
                    <textarea name="address" rows="2" placeholder="Alamat kantor developer..."
                              class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2B3543]">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 bg-[#2B3543] hover:bg-[#384455] text-gray-300 rounded-xl font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-xl font-semibold shadow-md">
                        Simpan Developer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EDIT DEVELOPER -->
    <div x-show="openEditModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openEditModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-[#C5A038]"></i>
                    <span>Edit Mitra Developer</span>
                </h3>
                <button @click="openEditModal = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <template x-if="selectedDev">
                <form :action="'{{ url('/admin/developers') }}/' + selectedDev.id" method="POST" class="space-y-3.5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Nama PIC Developer <span class="text-red-400">*</span></label>
                            <input type="text" name="name" x-model="selectedDev.name" required
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Nama Perusahaan / PT / CV</label>
                            <input type="text" name="company_name" x-model="selectedDev.company_name"
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Nomor WhatsApp <span class="text-red-400">*</span></label>
                            <input type="text" name="phone" x-model="selectedDev.phone" required
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Email Kontak</label>
                            <input type="email" name="email" x-model="selectedDev.email"
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Status Kerja Sama</label>
                        <select name="status" x-model="selectedDev.status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="Aktif">Aktif</option>
                            <option value="Dalam Penjajakan">Dalam Penjajakan</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Alamat Kantor / Domisili</label>
                        <textarea name="address" x-model="selectedDev.address" rows="2"
                                  class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2B3543]">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-[#2B3543] hover:bg-[#384455] text-gray-300 rounded-xl font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-xl font-semibold shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
