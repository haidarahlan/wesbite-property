@extends('layouts.admin')

@section('title', "Detail Lead - {$lead->reference_code}")

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ tab: 'chat' }">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.leads.index') }}" class="hover:text-[#C5A038]">Leads</a>
                <span>/</span>
                <span class="text-gray-200">{{ $lead->reference_code }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <span>{{ $lead->name ?? 'Prospek Tanpa Nama' }}</span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-[#C5A038]/20 text-[#C5A038] border border-[#C5A038]/40">
                        {{ $lead->reference_code }}
                    </span>
                </h1>
            </div>
            <p class="text-xs text-gray-400">Sumber: <span class="capitalize text-gray-300 font-semibold">{{ $lead->source }}</span> • Dibuat pada {{ $lead->created_at->format('d M Y H:i') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.leads.index') }}" class="px-3.5 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl border border-[#2B3543] flex items-center gap-2 transition-all">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>
            
            <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lead ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-gray-400 hover:text-red-400 bg-[#232B35] hover:bg-red-950/40 rounded-xl border border-[#2B3543] transition-all" title="Hapus Lead">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Layout Grid: Kiri Profil & Form Edit, Kanan Live WhatsApp Chat -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- KOLOM KIRI (5 Cols): Profil Lead, Unit Properti & Update Status -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Card Edit Data & Status -->
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center justify-between border-b border-[#2B3543] pb-3">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-[#C5A038]"></i>
                        <span>Informasi & Status Konsumen</span>
                    </span>
                    <a href="https://wa.me/{{ $lead->phone }}" target="_blank" class="text-xs text-emerald-400 hover:text-emerald-300 flex items-center gap-1 font-semibold">
                        <i class="fa-brands fa-whatsapp"></i> Chat Eksternal
                    </a>
                </h3>

                <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $lead->name) }}" required
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Nomor WhatsApp Konsumen</label>
                        <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" required
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Status Prospek</label>
                        <select name="status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none font-semibold">
                            @foreach(['Baru', 'Tertarik', 'Tidak Tertarik', 'Follow-up', 'Survei Terjadwal', 'Survei Selesai', 'Closing', 'Batal'] as $st)
                                <option value="{{ $st }}" {{ $lead->status === $st ? 'selected' : '' }}>
                                    ● {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Unit Properti yang Diminati</label>
                        <select name="unit_id" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="">-- Belum Memilih Unit Tertentu --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ $lead->unit_id == $unit->id ? 'selected' : '' }}>
                                    [{{ $unit->code }}] {{ $unit->title }} (Rp {{ number_format($unit->price / 1000000, 0, ',', '.') }} Jt)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Tugaskan PIC Marketing</label>
                        <select name="assigned_to" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $lead->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-medium mb-1">Catatan Internal CRM</label>
                        <textarea name="notes" rows="3" placeholder="Catatan preferensi, rencana pembayaran cash/KPR..."
                                  class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">{{ old('notes', $lead->notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[#C5A038] hover:bg-[#b08d2e] text-white font-bold rounded-xl transition-all shadow-md">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Card Ringkasan Unit Terkait (Jika Ada) -->
            @if($lead->unit)
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-[#2B3543] pb-2 text-xs">
                        <span class="text-gray-400 font-semibold uppercase">Detail Properti Terkait</span>
                        <a href="{{ route('public.property.detail', $lead->unit->slug) }}" target="_blank" class="text-[#C5A038] hover:underline flex items-center gap-1">
                            <span>Lihat di Web</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                    <div class="flex gap-3 items-center">
                        @if($lead->unit->photos->count() > 0)
                            <img src="{{ asset('storage/' . $lead->unit->photos->first()->photo_path) }}" alt="{{ $lead->unit->title }}" class="w-16 h-16 rounded-xl object-cover border border-[#2B3543]">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-[#161B22] flex items-center justify-center text-gray-600 border border-[#2B3543]">
                                <i class="fa-solid fa-building text-xl"></i>
                            </div>
                        @endif
                        <div class="text-xs">
                            <span class="px-2 py-0.5 rounded bg-[#C5A038]/20 text-[#C5A038] font-bold text-[10px]">{{ $lead->unit->code }}</span>
                            <h4 class="font-bold text-white mt-1">{{ $lead->unit->title }}</h4>
                            <div class="text-[#C5A038] font-black text-sm mt-0.5">Rp {{ number_format($lead->unit->price, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- KOLOM KANAN (7 Cols): WhatsApp Live Chat Timeline & Kirim Balasan -->
        <div class="lg:col-span-7 space-y-4">
            
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl shadow-sm overflow-hidden flex flex-col h-[650px]">
                
                <!-- Chat Window Header -->
                <div class="bg-[#161B22] border-b border-[#2B3543] p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm border border-emerald-500/30">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white flex items-center gap-2">
                                <span>{{ $lead->name ?? $lead->phone }}</span>
                                <span class="text-[10px] px-2 py-0.2 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-500/30 font-mono">
                                    {{ $lead->phone }}
                                </span>
                            </h4>
                            <span class="text-[10px] text-gray-400 flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
                                Terhubung via WhatsApp Gateway ({{ config('whatsapp.default', 'fake') }})
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('admin.whatsapp.simulator') }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-[11px] flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-gamepad text-emerald-400"></i>
                        <span>Buka Simulator</span>
                    </a>
                </div>

                <!-- Chat Messages Body (Scrollable) -->
                <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#11161d]" id="chatContainer">
                    @forelse($messages as $msg)
                        @if($msg->direction === 'inbound')
                            <!-- Pesan Masuk dari Konsumen (Kiri) -->
                            <div class="flex flex-col items-start max-w-[85%] sm:max-w-[75%]">
                                <div class="bg-[#1F2731] text-gray-200 border border-[#2B3543] rounded-2xl rounded-tl-none p-3.5 shadow-sm text-xs space-y-1">
                                    <div class="text-[10px] font-bold text-[#C5A038] flex items-center gap-1 mb-1">
                                        <i class="fa-solid fa-user text-[9px]"></i>
                                        <span>{{ $lead->name ?? 'Konsumen' }}</span>
                                    </div>
                                    <div class="whitespace-pre-line leading-relaxed text-gray-200">{{ $msg->message_body }}</div>
                                </div>
                                <div class="text-[10px] text-gray-500 mt-1 pl-2">
                                    {{ $msg->created_at->format('H:i') }} • Masuk
                                </div>
                            </div>
                        @else
                            <!-- Pesan Keluar dari CRM / Admin / Bot (Kanan) -->
                            <div class="flex flex-col items-end max-w-[85%] sm:max-w-[75%] ml-auto">
                                <div class="bg-emerald-950/80 text-emerald-100 border border-emerald-600/40 rounded-2xl rounded-tr-none p-3.5 shadow-sm text-xs space-y-1">
                                    <div class="text-[10px] font-bold text-emerald-400 flex items-center gap-1 mb-1 justify-end">
                                        <span>Wastu Property CRM</span>
                                        <i class="fa-solid fa-circle-check text-[9px]"></i>
                                    </div>
                                    <div class="whitespace-pre-line leading-relaxed">{{ $msg->message_body }}</div>
                                </div>
                                <div class="text-[10px] text-gray-500 mt-1 pr-2 flex items-center gap-1">
                                    <span>{{ $msg->created_at->format('H:i') }}</span>
                                    <span>•</span>
                                    <span class="text-emerald-400 capitalize">{{ $msg->status }}</span>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center text-gray-500 py-12">
                            <i class="fa-regular fa-comments text-4xl mb-3 text-gray-600"></i>
                            <p class="text-xs text-gray-400">Belum ada riwayat pesan WhatsApp untuk nomor ini.</p>
                            <p class="text-[11px] text-gray-500 mt-1">Gunakan form di bawah untuk mengirim pesan pembuka ke konsumen.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Chat Input Footer -->
                <div class="p-3 bg-[#161B22] border-t border-[#2B3543]">
                    <form action="{{ route('admin.leads.reply', $lead->id) }}" method="POST" class="flex gap-2">
                        @csrf
                        <div class="flex-1">
                            <textarea name="message" rows="2" required placeholder="Ketik pesan balasan WhatsApp ke konsumen..."
                                      class="w-full bg-[#1F2731] border border-[#2B3543] rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" 
                                class="px-5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl flex flex-col items-center justify-center gap-1 transition-all shadow-lg shadow-emerald-600/20 cursor-pointer">
                            <i class="fa-solid fa-paper-plane text-sm"></i>
                            <span class="text-[10px]">Kirim WA</span>
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatBox = document.getElementById("chatContainer");
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
@endsection
