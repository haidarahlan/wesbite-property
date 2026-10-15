@extends('layouts.admin')

@section('title', 'Aturan Klasifikasi & Auto-Reply FAQ')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ tab: 'rules' }">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">WhatsApp Gateway</span>
                <span>/</span>
                <span class="text-gray-200">Aturan & Auto-Reply</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-brain text-[#C5A038]"></i>
                <span>Aturan Klasifikasi Pesan & Auto-Reply FAQ</span>
            </h1>
            <p class="text-xs text-gray-400">Atur kata kunci untuk mendeteksi konsumen Tertarik vs Tidak Tertarik, serta kelola template jawaban otomatis FAQ.</p>
        </div>
        <a href="{{ route('admin.whatsapp.simulator') }}" class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl border border-[#2B3543] flex items-center gap-2">
            <i class="fa-brands fa-whatsapp text-emerald-400"></i>
            <span>Buka Simulator WA &nearr;</span>
        </a>
    </div>

    <!-- Tab Selector -->
    <div class="flex items-center gap-2 border-b border-[#2B3543] pb-2 text-xs font-semibold">
        <button @click="tab = 'rules'" 
                :class="tab === 'rules' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-filter"></i>
            <span>Aturan Kata Kunci ({{ $rules->count() }})</span>
        </button>
        <button @click="tab = 'templates'" 
                :class="tab === 'templates' ? 'bg-[#C5A038] text-white shadow-md' : 'text-gray-400 hover:text-white bg-[#1F2731]'"
                class="px-4 py-2 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-reply-all"></i>
            <span>Template Auto-Reply FAQ ({{ $faqTemplates->count() }})</span>
        </button>
    </div>

    <!-- TAB 1: ATURAN KATA KUNCI (RULES) -->
    <div x-show="tab === 'rules'" class="space-y-6">
        <!-- Form Tambah Rule Baru -->
        <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-[#2B3543] pb-3">
                <i class="fa-solid fa-plus text-[#C5A038]"></i>
                <span>Tambah Aturan Kata Kunci Baru</span>
            </h3>

            <form action="{{ route('admin.whatsapp.rules.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs items-end">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-300 mb-1">Kata Kunci (Keyword)</label>
                    <input type="text" name="keyword" placeholder="Contoh: berminat, jadwal survei, cicilan, mahal..." required
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Klasifikasi Target</label>
                    <select name="classification" required class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none">
                        <option value="Tertarik">Tertarik (Buat Lead)</option>
                        <option value="Tidak Tertarik">Tidak Tertarik (Auto-Reply FAQ)</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-[#C5A038] hover:bg-[#B08C2B] text-white font-bold rounded-xl shadow-lg transition-all">
                        Simpan Aturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Aturan Kata Kunci -->
        <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-gray-400 font-semibold uppercase">
                    <tr>
                        <th class="py-3.5 px-4">Kata Kunci</th>
                        <th class="py-3.5 px-4">Klasifikasi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543] text-gray-300">
                    @forelse($rules as $r)
                        <tr class="hover:bg-[#28323E]/50">
                            <td class="py-3.5 px-4 font-mono font-bold text-white">
                                "{{ $r->keyword }}"
                            </td>
                            <td class="py-3.5 px-4">
                                @if($r->classification === 'Tertarik')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-thumbs-up mr-1"></i> Tertarik
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-950 text-red-400 border border-red-500/30">
                                        <i class="fa-solid fa-thumbs-down mr-1"></i> Tidak Tertarik
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($r->is_active)
                                    <span class="text-emerald-400 text-[11px]"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                                @else
                                    <span class="text-gray-500 text-[11px]"><i class="fa-solid fa-circle-xmark"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <form action="{{ route('admin.whatsapp.rules.toggle', $r->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-lg bg-[#14181E] text-gray-400 hover:text-white" title="Ubah Status">
                                        <i class="fa-solid fa-power-off text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.whatsapp.rules.destroy', $r->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aturan kata kunci ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-[#14181E] text-red-400 hover:text-red-300" title="Hapus">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">Belum ada aturan kata kunci.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: TEMPLATE AUTO-REPLY FAQ -->
    <div x-show="tab === 'templates'" class="space-y-6" style="display: none;">
        <!-- Form Tambah Template Baru -->
        <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-[#2B3543] pb-3">
                <i class="fa-solid fa-pen-nib text-[#C5A038]"></i>
                <span>Kelola Template Pesan Auto-Reply FAQ</span>
            </h3>

            <form action="{{ route('admin.whatsapp.templates.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-300 mb-1">Kode Template (Unik)</label>
                        <input type="text" name="code" placeholder="Contoh: FAQ-LEGALITAS" required
                               class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 font-mono uppercase focus:outline-none focus:border-[#C5A038]">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-300 mb-1">Judul / Kategori Balasan</label>
                        <input type="text" name="title" placeholder="Contoh: Jawaban Umum Konsumen Ragu" required
                               class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-300 mb-1">Isi Pesan Balasan Otomatis WhatsApp</label>
                    <textarea name="template_message" rows="4" required placeholder="Tulis format pesan balasan ramah yang menjawab keraguan konsumen..."
                              class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-gray-200 focus:outline-none focus:border-[#C5A038]"></textarea>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-gray-300">
                        <input type="checkbox" name="is_default" value="1" class="rounded bg-[#14181E] border-[#2B3543] text-[#C5A038]">
                        <span>Jadikan sebagai template balasan utama (Default)</span>
                    </label>

                    <button type="submit" class="py-2.5 px-6 bg-[#C5A038] hover:bg-[#B08C2B] text-white font-bold rounded-xl shadow-lg transition-all">
                        Simpan Template FAQ
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar Template FAQ -->
        <div class="space-y-3">
            @foreach($faqTemplates as $tpl)
                <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-5 shadow-sm space-y-2 text-xs">
                    <div class="flex items-center justify-between border-b border-[#2B3543] pb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-amber-400">{{ $tpl->code }}</span>
                            <span class="font-bold text-white">{{ $tpl->title }}</span>
                            @if($tpl->is_default)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#C5A038] text-white">Default Balasan</span>
                            @endif
                        </div>
                        <form action="{{ route('admin.whatsapp.templates.destroy', $tpl->id) }}" method="POST" onsubmit="return confirm('Hapus template ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 p-1"><i class="fa-solid fa-trash text-xs"></i></button>
                        </form>
                    </div>
                    <p class="text-gray-300 whitespace-pre-line bg-[#14181E] p-3 rounded-xl border border-[#2B3543]/60 leading-relaxed font-sans">{{ $tpl->template_message }}</p>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
