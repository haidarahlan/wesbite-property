@extends('layouts.admin')

@section('title', 'Manajemen Leads & Prospek')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ openCreateModal: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">CRM Leads</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-users text-[#C5A038]"></i>
                <span>Manajemen Leads & Prospek</span>
            </h1>
            <p class="text-xs text-gray-400">Pantau dan kelola seluruh calon pembeli yang masuk dari WhatsApp Gateway, website publik, maupun offline.</p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="openCreateModal = true"
                    class="px-4 py-2.5 bg-[#C5A038] hover:bg-[#b08d2e] text-white text-xs font-semibold rounded-xl shadow-lg shadow-[#C5A038]/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Lead Manual</span>
            </button>
        </div>
    </div>

    <!-- Ringkasan Metrik / Statistik Lead -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-gray-400 mb-1">Total Leads</div>
            <div class="text-xl font-black text-white">{{ $totalLeads }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Seluruh prospek tercatat</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-blue-400 mb-1">Baru Masuk</div>
            <div class="text-xl font-black text-blue-400">{{ $newLeads }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Perlu direspon</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-emerald-400 mb-1">Tertarik (Valid)</div>
            <div class="text-xl font-black text-emerald-400">{{ $interestedLeads }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Lolos klasifikasi WA</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-amber-400 mb-1">Sedang Follow-up</div>
            <div class="text-xl font-black text-amber-400">{{ $followUpLeads }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Tahap interaksi aktif</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl col-span-2 sm:col-span-1">
            <div class="text-[11px] font-medium text-purple-400 mb-1">Tahap Survei</div>
            <div class="text-xl font-black text-purple-400">{{ $surveyLeads }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Jadwal survei lapangan</div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block text-[11px] text-gray-400 mb-1">Cari Lead (Nama, No HP, Kode)</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau nomor..."
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white placeholder-gray-400 focus:border-[#C5A038] focus:outline-none pl-8">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-gray-400"></i>
                </div>
            </div>

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">Filter Status</label>
                <select name="status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    <option value="all">Semua Status</option>
                    @foreach(['Baru', 'Tertarik', 'Tidak Tertarik', 'Follow-up', 'Survei Terjadwal', 'Survei Selesai', 'Closing', 'Batal'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">Filter Sumber Lead</label>
                <select name="source" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    <option value="all">Semua Sumber</option>
                    <option value="whatsapp" {{ request('source') === 'whatsapp' ? 'selected' : '' }}>WhatsApp Gateway</option>
                    <option value="website" {{ request('source') === 'website' ? 'selected' : '' }}>Website Publik</option>
                    <option value="manual" {{ request('source') === 'manual' ? 'selected' : '' }}>Input Manual</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#2B3543] hover:bg-[#384455] text-white font-semibold rounded-xl transition-all">
                    Terapkan
                </button>
                <a href="{{ route('admin.leads.index') }}" class="px-3 py-2 bg-[#161B22] hover:bg-[#232B35] text-gray-400 hover:text-white rounded-xl border border-[#2B3543] transition-all" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Leads -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-[11px] text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Ref & Konsumen</th>
                        <th class="py-3 px-4">Kontak WhatsApp</th>
                        <th class="py-3 px-4">Unit Minat</th>
                        <th class="py-3 px-4">Sumber</th>
                        <th class="py-3 px-4">Status Lead</th>
                        <th class="py-3 px-4">PIC Marketing</th>
                        <th class="py-3 px-4">Waktu Masuk</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543]/60">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-[#232B35]/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-white text-xs">{{ $lead->reference_code }}</div>
                                <div class="text-gray-300 font-medium">{{ $lead->name ?? 'Belum ada nama' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-mono text-gray-200">{{ $lead->phone }}</div>
                                <a href="https://wa.me/{{ $lead->phone }}" target="_blank" class="text-[10px] text-emerald-400 hover:underline inline-flex items-center gap-1">
                                    <i class="fa-brands fa-whatsapp"></i> Chat Langsung
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                @if($lead->unit)
                                    <span class="inline-block px-2 py-0.5 rounded-md bg-amber-500/10 text-[#C5A038] font-bold text-[11px] border border-[#C5A038]/30">
                                        {{ $lead->unit->code }}
                                    </span>
                                    <div class="text-[11px] text-gray-400 truncate max-w-[150px]">{{ $lead->unit->title }}</div>
                                @else
                                    <span class="text-gray-400 italic">Belum spesifik</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($lead->source === 'whatsapp')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-300 border border-emerald-500/30">
                                        WhatsApp
                                    </span>
                                @elseif($lead->source === 'website')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-950/60 text-blue-300 border border-blue-500/30">
                                        Website
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-800 text-gray-300 border border-gray-600">
                                        Manual
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $statusClasses = [
                                        'Baru' => 'bg-blue-950/60 text-blue-300 border-blue-500/30',
                                        'Tertarik' => 'bg-emerald-950/60 text-emerald-300 border-emerald-500/30',
                                        'Tidak Tertarik' => 'bg-red-950/60 text-red-300 border-red-500/30',
                                        'Follow-up' => 'bg-amber-950/60 text-amber-300 border-amber-500/30',
                                        'Survei Terjadwal' => 'bg-purple-950/60 text-purple-300 border-purple-500/30',
                                        'Survei Selesai' => 'bg-indigo-950/60 text-indigo-300 border-indigo-500/30',
                                        'Closing' => 'bg-teal-950/60 text-teal-300 border-teal-500/30',
                                        'Batal' => 'bg-gray-800 text-gray-400 border-gray-600',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $statusClasses[$lead->status] ?? 'bg-gray-800 text-gray-300' }}">
                                    {{ $lead->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-gray-300">{{ $lead->assignedUser->name ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-gray-400 text-[11px]">
                                {{ $lead->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.leads.show', $lead->id) }}" 
                                   class="px-3 py-1.5 bg-[#232B35] hover:bg-[#C5A038] hover:text-white text-gray-200 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Detail & Bukti Chat</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <i class="fa-solid fa-inbox text-3xl mb-2 text-gray-400 block"></i>
                                Belum ada data lead yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="p-4 bg-[#161B22] border-t border-[#2B3543]">
                {{ $leads->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Lead Manual -->
    <div x-show="openCreateModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openCreateModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-[#C5A038]"></i>
                    <span>Tambah Lead Prospek Manual</span>
                </h3>
                <button @click="openCreateModal = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('admin.leads.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Nama Konsumen <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Andi Wijaya"
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Nomor WhatsApp <span class="text-red-400">*</span></label>
                    <input type="text" name="phone" required placeholder="081234567890"
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Unit Properti</label>
                        <select name="unit_id" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">[{{ $unit->code }}] {{ $unit->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Sumber Prospek</label>
                        <select name="source" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="manual">Manual / Offline</option>
                            <option value="telepon">Telepon Langsung</option>
                            <option value="walk_in">Walk-in Kantor</option>
                            <option value="whatsapp">WhatsApp Gateway</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Status Awal</label>
                        <select name="status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="Baru">Baru</option>
                            <option value="Tertarik">Tertarik</option>
                            <option value="Follow-up">Follow-up</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Tugaskan ke (PIC)</label>
                        <select name="assigned_to" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="">-- Pilih PIC --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2.5" placeholder="Catatan preferensi budget konsumen atau riwayat pertemuan..."
                              class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2B3543]">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 bg-[#2B3543] hover:bg-[#384455] text-gray-300 rounded-xl font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-xl font-semibold shadow-md">
                        Simpan Lead
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
