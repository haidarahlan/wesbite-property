@extends('layouts.admin')

@section('title', 'Jadwal Survei Lapangan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ openCreateModal: false, selectedSurvey: null, openResultModal: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">Survei Lapangan</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-calendar-check text-[#C5A038]"></i>
                <span>Jadwal Survei Lapangan</span>
            </h1>
            <p class="text-xs text-gray-400">
                @if(auth()->user()->isSuperAdmin())
                    Monitoring seluruh jadwal survei tim marketing, kelola penugasan PIC, dan kirim pengingat H-1.
                @else
                    Daftar agenda kunjungan survei lapangan yang ditugaskan kepada Anda. Silakan update hasil kunjungan setelah survei selesai.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if(auth()->user()->isSuperAdmin())
                <button @click="openCreateModal = true"
                        class="px-4 py-2.5 bg-[#C5A038] hover:bg-[#b08d2e] text-white text-xs font-semibold rounded-xl shadow-lg shadow-[#C5A038]/20 flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Jadwalkan Survei Baru</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Ringkasan Statistik Survei -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-gray-400 mb-1">Total Jadwal Survei</div>
            <div class="text-xl font-black text-white">{{ $totalSurveys }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Seluruh agenda survei tercatat</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-purple-400 mb-1">Akan Datang (Terjadwal)</div>
            <div class="text-xl font-black text-purple-400">{{ $scheduledSurveys }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Perlu didampingi PIC</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-amber-400 mb-1">Jadwal Besok (H-1)</div>
            <div class="text-xl font-black text-amber-400">{{ $tomorrowSurveys }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Penerima pengingat WhatsApp</div>
        </div>
        <div class="bg-[#1F2731] border border-[#2B3543] p-4 rounded-2xl">
            <div class="text-[11px] font-medium text-emerald-400 mb-1">Survei Selesai</div>
            <div class="text-xl font-black text-emerald-400">{{ $completedSurveys }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Sudah ada hasil kunjungan</div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.surveys.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block text-[11px] text-gray-400 mb-1">Cari (Nama Konsumen / Kode Unit)</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau unit..."
                           class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white placeholder-gray-500 focus:border-[#C5A038] focus:outline-none pl-8">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-gray-500"></i>
                </div>
            </div>

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">Periode Waktu</label>
                <select name="timeframe" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    <option value="">Semua Waktu</option>
                    <option value="today" {{ request('timeframe') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="tomorrow" {{ request('timeframe') === 'tomorrow' ? 'selected' : '' }}>Besok (H-1)</option>
                    <option value="upcoming" {{ request('timeframe') === 'upcoming' ? 'selected' : '' }}>Mendatang</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">Status Pelaksanaan</label>
                <select name="status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    <option value="all">Semua Status</option>
                    @foreach(['Terjadwal', 'Selesai', 'Dibatalkan', 'Dijadwal Ulang'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#2B3543] hover:bg-[#384455] text-white font-semibold rounded-xl transition-all">
                    Terapkan
                </button>
                <a href="{{ route('admin.surveys.index') }}" class="px-3 py-2 bg-[#161B22] hover:bg-[#232B35] text-gray-400 hover:text-white rounded-xl border border-[#2B3543] transition-all" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Survei -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-300">
                <thead class="bg-[#161B22] border-b border-[#2B3543] text-[11px] text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Waktu Survei</th>
                        <th class="py-3 px-4">Konsumen (Lead)</th>
                        <th class="py-3 px-4">Unit Properti</th>
                        <th class="py-3 px-4">PIC Pendamping</th>
                        <th class="py-3 px-4">Status & Pengingat</th>
                        <th class="py-3 px-4">Catatan / Hasil Kunjungan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2B3543]/60">
                    @forelse($surveys as $survey)
                        @php
                            $isTomorrow = $survey->survey_date->isTomorrow();
                            $isToday = $survey->survey_date->isToday();
                        @endphp
                        <tr class="hover:bg-[#232B35]/50 transition-colors {{ $isTomorrow ? 'bg-amber-500/5' : '' }}">
                            <td class="py-3 px-4">
                                <div class="font-bold text-white text-xs flex items-center gap-1.5">
                                    <i class="fa-regular fa-clock text-[#C5A038]"></i>
                                    <span>{{ $survey->survey_date->translatedFormat('d M Y') }}</span>
                                </div>
                                <div class="text-[11px] text-[#C5A038] font-bold mt-0.5">
                                    Pukul {{ $survey->survey_date->format('H:i') }} WIB
                                </div>
                                @if($isTomorrow)
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                        Survei Besok (H-1)
                                    </span>
                                @elseif($isToday)
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse">
                                        Hari Ini
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4">
                                <div class="font-bold text-white">{{ $survey->lead->name ?? 'Tanpa Nama' }}</div>
                                <div class="text-[11px] text-gray-400 font-mono">{{ $survey->lead->phone }}</div>
                                <a href="{{ route('admin.leads.show', $survey->lead_id) }}" class="text-[10px] text-[#C5A038] hover:underline flex items-center gap-1 mt-0.5">
                                    <span>Ref: {{ $survey->lead->reference_code }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
                                </a>
                            </td>

                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-amber-500/10 text-[#C5A038] font-bold text-[11px] border border-[#C5A038]/30 inline-block">
                                    {{ $survey->unit->code }}
                                </span>
                                <div class="text-[11px] text-gray-300 font-medium truncate max-w-[160px] mt-0.5">
                                    {{ $survey->unit->title }}
                                </div>
                                <div class="text-[10px] text-gray-500">{{ $survey->unit->city }}</div>
                            </td>

                            <td class="py-3 px-4">
                                <div class="text-gray-200 font-medium">{{ $survey->pic->name ?? 'Belum Ditugaskan' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $survey->pic ? ucfirst($survey->pic->role) : '-' }}</div>
                            </td>

                            <td class="py-3 px-4">
                                @php
                                    $stColor = match($survey->status) {
                                        'Terjadwal' => 'bg-purple-950/60 text-purple-300 border-purple-500/30',
                                        'Selesai' => 'bg-emerald-950/60 text-emerald-300 border-emerald-500/30',
                                        'Dibatalkan' => 'bg-red-950/60 text-red-300 border-red-500/30',
                                        'Dijadwal Ulang' => 'bg-amber-950/60 text-amber-300 border-amber-500/30',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $stColor }} inline-block">
                                    {{ $survey->status }}
                                </span>

                                <div class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1">
                                    @if($survey->reminder_sent_at)
                                        <i class="fa-solid fa-check-double text-emerald-400"></i>
                                        <span>Pengingat terkirim ({{ $survey->reminder_sent_at->format('d/m H:i') }})</span>
                                    @else
                                        <i class="fa-regular fa-clock text-gray-500"></i>
                                        <span>Belum ada pengingat</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3 px-4 max-w-[220px]">
                                @if($survey->result)
                                    <div class="text-xs text-emerald-300 bg-emerald-950/30 p-2 rounded-xl border border-emerald-500/20">
                                        <div class="text-[9px] font-bold text-emerald-400 uppercase">Hasil:</div>
                                        <p class="line-clamp-2">{{ $survey->result }}</p>
                                    </div>
                                @elseif($survey->notes)
                                    <div class="text-[11px] text-gray-400 italic">
                                        <span class="text-gray-500 font-medium">Titik Kumpul:</span> {{ $survey->notes }}
                                    </div>
                                @else
                                    <span class="text-gray-500 italic text-[11px]">Belum ada catatan</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Kirim Pengingat Manual -->
                                    <form action="{{ route('admin.surveys.manual-reminder', $survey->id) }}" method="POST" onsubmit="return confirm('Kirim pengingat WhatsApp ke konsumen sekarang?')">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg bg-[#232B35] hover:bg-emerald-600 hover:text-white text-emerald-400 border border-[#2B3543] transition-all" title="Kirim Pengingat WA Manual">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                        </button>
                                    </form>

                                    <!-- Tombol Isi Hasil Survei & Update Status -->
                                    <button @click="selectedSurvey = {{ $survey->toJson() }}; openResultModal = true"
                                            class="p-1.5 rounded-lg bg-[#232B35] hover:bg-[#C5A038] hover:text-white text-gray-300 border border-[#2B3543] transition-all"
                                            title="Update Status / Input Hasil">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.surveys.destroy', $survey->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal survei ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-[#232B35] hover:bg-red-600 hover:text-white text-gray-400 border border-[#2B3543] transition-all" title="Hapus Jadwal">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <i class="fa-solid fa-calendar-xmark text-3xl mb-2 text-gray-600 block"></i>
                                Belum ada jadwal survei lapangan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($surveys->hasPages())
            <div class="p-4 bg-[#161B22] border-t border-[#2B3543]">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL 1: JADWALKAN SURVEI BARU -->
    <div x-show="openCreateModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openCreateModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus text-[#C5A038]"></i>
                    <span>Jadwalkan Survei Lapangan Baru</span>
                </h3>
                <button @click="openCreateModal = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('admin.surveys.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Pilih Calon Pembeli (Lead) <span class="text-red-400">*</span></label>
                    <select name="lead_id" required class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        <option value="">-- Pilih Konsumen --</option>
                        @foreach($leads as $ld)
                            <option value="{{ $ld->id }}">
                                {{ $ld->name ?? 'Tanpa Nama' }} ({{ $ld->phone }}) - [{{ $ld->reference_code }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Unit Properti yang Akan Dikunjungi <span class="text-red-400">*</span></label>
                    <select name="unit_id" required class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        <option value="">-- Pilih Unit Properti --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">[{{ $unit->code }}] {{ $unit->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Tanggal & Jam Kunjungan <span class="text-red-400">*</span></label>
                        <input type="datetime-local" name="survey_date" required min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">PIC Pendamping</label>
                        <select name="pic_user_id" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-300 font-semibold mb-1">Titik Kumpul / Catatan Persiapan</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Bertemu di Alfamart Borobudur jam 09.45, siapkan brosur denah tipe 36..."
                              class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                </div>

                <div class="p-3 bg-emerald-950/40 border border-emerald-500/30 rounded-xl text-[11px] text-emerald-300 flex items-start gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-sm mt-0.5"></i>
                    <div>
                        <span class="font-bold">Konfirmasi Otomatis:</span> Setelah disimpan, sistem otomatis mengirim pesan konfirmasi agenda survei ke nomor WhatsApp konsumen.
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2B3543]">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 bg-[#2B3543] hover:bg-[#384455] text-gray-300 rounded-xl font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white rounded-xl font-semibold shadow-md">
                        Jadwalkan & Kirim WA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: UPDATE STATUS & INPUT HASIL SURVEI -->
    <div x-show="openResultModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openResultModal = false" class="bg-[#1F2731] border border-[#2B3543] rounded-2xl max-w-lg w-full p-6 text-xs text-gray-300 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-[#2B3543] pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-[#C5A038]"></i>
                    <span>Update Status & Catatan Hasil Survei</span>
                </h3>
                <button @click="openResultModal = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <template x-if="selectedSurvey">
                <form :action="'{{ url('/admin/surveys') }}/' + selectedSurvey.id" method="POST" class="space-y-3.5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Status Pelaksanaan</label>
                            <select name="status" x-model="selectedSurvey.status" class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                                <option value="Terjadwal">Terjadwal</option>
                                <option value="Selesai">Selesai (Kunjungan Berhasil)</option>
                                <option value="Dijadwal Ulang">Dijadwal Ulang</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 font-semibold mb-1">Tanggal & Jam</label>
                            <input type="datetime-local" name="survey_date" :value="selectedSurvey.survey_date ? selectedSurvey.survey_date.replace(' ', 'T').substring(0, 16) : ''" required
                                   class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Hasil & Rangkuman Kunjungan Konsumen</label>
                        <textarea name="result" x-model="selectedSurvey.result" rows="3" placeholder="Contoh: Konsumen sangat cocok dengan tata ruang, berminat booking tipe 36 dan mengajukan KPR BTN..."
                                  class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-300 font-semibold mb-1">Catatan Tambahan / Titik Kumpul</label>
                        <textarea name="notes" x-model="selectedSurvey.notes" rows="2"
                                  class="w-full bg-[#161B22] border border-[#2B3543] rounded-xl px-3 py-2 text-white focus:border-[#C5A038] focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#2B3543]">
                        <button type="button" @click="openResultModal = false" class="px-4 py-2 bg-[#2B3543] hover:bg-[#384455] text-gray-300 rounded-xl font-semibold">
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
