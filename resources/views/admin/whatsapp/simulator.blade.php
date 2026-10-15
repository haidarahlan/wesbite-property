@extends('layouts.admin')

@section('title', 'WhatsApp Gateway Simulator - PT Wastu Pratama Wijaya')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">WhatsApp Gateway</span>
                <span>/</span>
                <span class="text-gray-200">Simulator</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                <span>Simulator WhatsApp Gateway (Lokal / Demo)</span>
            </h1>
            <p class="text-xs text-gray-400">Gunakan simulator ini untuk menguji alur pesan masuk/keluar konsumen tanpa nomor WhatsApp asli.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-[#232B35] border border-[#2B3543] text-xs font-mono text-emerald-400 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Provider: {{ strtoupper($provider) }}</span>
            </span>
        </div>
    </div>

    <!-- Simulator Forms: 2 Columns (Inbound vs Outbound) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Form 1: Simulasi Pesan Masuk (Konsumen -> Sistem) -->
        <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-2 text-sm font-bold text-white border-b border-[#2B3543] pb-3">
                <i class="fa-solid fa-arrow-down-left text-blue-400"></i>
                <span>1. Simulasi Pesan Masuk (Inbound Webhook)</span>
            </div>
            <p class="text-xs text-gray-400">Simulasikan seorang konsumen mengirim pesan WA ke nomor bisnis perusahaan.</p>

            <form action="{{ route('admin.whatsapp.simulate-inbound') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Nomor WA Pengirim (Konsumen)</label>
                    <input type="text" name="phone" value="6281299887766" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 font-mono focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Isi Pesan WhatsApp</label>
                    <textarea name="message" rows="3" required placeholder="Contoh: Halo saya tertarik dengan unit Casa de Borobudur, apakah bisa jadwalkan survei?"
                              class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">Halo Wastu Property, saya tertarik dengan unit WP-BORO-01 (Casa de Borobudur 2). Apakah unitnya masih tersedia dan berapa harganya?</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Pesan Masuk Palsu</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Form 2: Uji Coba Kirim Pesan Keluar (Sistem -> Konsumen) -->
        <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-2 text-sm font-bold text-white border-b border-[#2B3543] pb-3">
                <i class="fa-solid fa-arrow-up-right text-emerald-400"></i>
                <span>2. Uji Kirim Pesan Keluar (Outbound Gateway)</span>
            </div>
            <p class="text-xs text-gray-400">Kirim balasan chat ke konsumen melalui Gateway yang sedang aktif.</p>

            <form action="{{ route('admin.whatsapp.test-send') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Nomor WA Tujuan</label>
                    <input type="text" name="to" value="6281299887766" required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 font-mono focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Isi Pesan Balasan</label>
                    <textarea name="message" rows="3" required placeholder="Halo, unit tersebut masih tersedia..."
                              class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">Halo Bapak/Ibu, terima kasih telah menghubungi Wastu Property. Unit Casa de Borobudur 2 saat ini masih tersedia. Apakah Anda memiliki waktu untuk survei lapangan?</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span>Kirim via Gateway WhatsApp</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Table: Log Riwayat Pesan WhatsApp Masuk/Keluar (Tabel whatsapp_messages) -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-[#2B3543]">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-[#C5A038]"></i>
                    <span>Log Pesan WhatsApp (whatsapp_messages)</span>
                </h3>
                <p class="text-xs text-gray-400">Semua chat masuk dan keluar tersimpan di database secara terpusat.</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-[#2B3543]">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-gray-400 font-semibold uppercase">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Arah</th>
                        <th class="py-3 px-4">Nomor WhatsApp</th>
                        <th class="py-3 px-4">Isi Pesan</th>
                        <th class="py-3 px-4">Klasifikasi Sistem</th>
                        <th class="py-3 px-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543] text-gray-300">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-[#28323E]/50">
                            <td class="py-3 px-4 text-gray-400 font-mono text-[11px] whitespace-nowrap">
                                {{ $msg->created_at->translatedFormat('d M Y, H:i:s') }}
                            </td>
                            <td class="py-3 px-4">
                                @if($msg->direction === 'inbound')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-950 text-blue-400 border border-blue-500/30">
                                        <i class="fa-solid fa-arrow-down-left mr-1"></i> MASUK
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-arrow-up-right mr-1"></i> KELUAR
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-[#C5A038] font-bold">
                                +{{ $msg->phone_number }}
                            </td>
                            <td class="py-3 px-4 max-w-md">
                                <p class="line-clamp-2 text-gray-200">{{ $msg->message_body }}</p>
                                @if($msg->lead_id)
                                    <span class="text-[10px] font-mono text-emerald-400 block mt-0.5">
                                        <i class="fa-solid fa-user-check"></i> Terhubung ke Lead #{{ $msg->lead_id }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-[10px]">
                                @if($msg->classification === 'Tertarik')
                                    <span class="px-2 py-0.5 rounded font-bold bg-emerald-950 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-thumbs-up mr-1"></i> TERTARIK
                                    </span>
                                @elseif($msg->classification === 'Tidak Tertarik')
                                    <span class="px-2 py-0.5 rounded font-bold bg-red-950 text-red-400 border border-red-500/30">
                                        <i class="fa-solid fa-thumbs-down mr-1"></i> TIDAK TERTARIK
                                    </span>
                                    @if($msg->auto_replied)
                                        <span class="text-[9px] text-amber-400 block mt-0.5">Auto-Reply Sent</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">{{ $msg->classification ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $msg->status === 'received' || $msg->status === 'sent' ? 'bg-emerald-950 text-emerald-400' : 'bg-gray-800 text-gray-400' }}">
                                    {{ strtoupper($msg->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Belum ada riwayat pesan WhatsApp di sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $messages->links() }}
        </div>
    </div>

</div>
@endsection
