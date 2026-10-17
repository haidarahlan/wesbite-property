<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Pemasaran - PT Wastu Pratama Wijaya</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #222; margin: 40px; }
        .header { border-bottom: 2px solid #C5A038; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
        .logo-title h1 { margin: 0; font-size: 20px; color: #161B22; }
        .logo-title p { margin: 3px 0 0; color: #666; font-size: 11px; }
        .report-meta { text-align: right; font-size: 11px; color: #555; }
        .summary-box { display: flex; gap: 15px; margin-bottom: 25px; }
        .card { flex: 1; border: 1px solid #ddd; padding: 12px; border-radius: 8px; background: #fdfdfd; }
        .card-label { font-size: 10px; color: #777; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .card-value { font-size: 18px; font-weight: bold; color: #161B22; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 11px; }
        th { background: #f4f4f4; border-bottom: 2px solid #ddd; padding: 8px; text-align: left; font-weight: bold; }
        td { border-bottom: 1px solid #eee; padding: 8px; }
        .signature-section { margin-top: 40px; display: flex; justify-content: flex-end; }
        .signature-box { text-align: center; width: 220px; }
        .signature-img-wrapper { height: 75px; display: flex; align-items: center; justify-content: center; margin: 5px 0; }
        .signature-img-wrapper img { max-height: 70px; max-width: 170px; object-fit: contain; }
        .signature-line { border-bottom: 1px solid #222; margin-top: 0; }
        .signature-box .empty-space { height: 60px; }
        @media print {
            body { margin: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #C5A038; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <!-- Kop Surat Resmi -->
    <div class="header">
        <div class="logo-title">
            <h1>PT WASTU PRATAMA WIJAYA</h1>
            <p>Integrated Property Management & Marketing Agency</p>
            <p>Jl. Palagan Tentara Pelajar Km. 9, Sleman, D.I. Yogyakarta | Telp: (0274) 123456</p>
        </div>
        <div class="report-meta">
            <strong>LAPORAN KINERJA PEMASARAN</strong><br>
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}<br>
            Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <div class="card">
            <div class="card-label">Total Leads Masuk</div>
            <div class="card-value">{{ $leads->count() }} Konsumen</div>
            <div style="font-size: 10px; color: #666; margin-top: 4px;">{{ $leads->whereNotNull('assigned_to')->count() }} ditangani PIC</div>
        </div>
        <div class="card">
            <div class="card-label">Survei Lapangan</div>
            <div class="card-value">{{ $surveys->where('status', 'Selesai')->count() }} / {{ $surveys->count() }} Agenda</div>
            <div style="font-size: 10px; color: #666; margin-top: 4px;">Realisasi kunjungan konsumen</div>
        </div>
        <div class="card">
            <div class="card-label">Target Unit Penjualan</div>
            <div class="card-value">{{ $acquisitionTargets->sum('target_units') }} Unit Target</div>
            <div style="font-size: 10px; color: #666; margin-top: 4px;">{{ $closingLeads->count() }} konsumen closing tercapai</div>
        </div>
        <div class="card">
            <div class="card-label">Closing Penjualan</div>
            <div class="card-value">{{ $closingLeads->count() }} Closing ({{ $soldUnits->count() }} Unit)</div>
            <div style="font-size: 10px; color: #666; margin-top: 4px;">Est. Rp {{ number_format($totalClosingValue, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Bagian 1: Laporan Target & Realisasi Penjualan Unit -->
    <h3 style="font-size: 13px; margin-bottom: 8px; border-left: 3px solid #C5A038; padding-left: 8px;">
        1. Rencana & Realisasi Target Penjualan Unit Properti
    </h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Target Periode</th>
                <th>Wilayah Fokus</th>
                <th style="text-align: center;">Target Unit</th>
                <th style="text-align: center;">Realisasi Closing</th>
                <th>Target Nilai Closing</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($acquisitionTargets as $index => $target)
                @php
                    $soldCount = $target->sold_units_count;
                    $pct = $target->units_progress_percentage;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $target->period_name }}</strong></td>
                    <td>{{ $target->target_region ?? 'Semua Wilayah' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $target->target_units }} Unit</td>
                    <td style="text-align: center; font-weight: bold; color: #0f766e;">{{ $soldCount }} Unit ({{ $pct }}%)</td>
                    <td>{{ $target->target_value ? 'Rp ' . number_format($target->target_value, 0, ',', '.') : '-' }}</td>
                    <td>{{ $target->status }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align: center; color: #999;">Tidak ada data target unit penjualan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bagian 2: Tabel Rekap Leads & PIC Marketing -->
    <h3 style="font-size: 13px; margin-bottom: 8px; border-left: 3px solid #C5A038; padding-left: 8px; margin-top: 25px;">
        2. Ringkasan Prospek & Leads Masuk (Periode Terpilih)
    </h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Ref</th>
                <th>Nama Konsumen</th>
                <th>Nomor WhatsApp</th>
                <th>Unit Minat</th>
                <th>PIC Marketing</th>
                <th>Sumber</th>
                <th>Status Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leads as $index => $ld)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $ld->reference_code }}</strong></td>
                    <td>{{ $ld->name ?? '-' }}</td>
                    <td>{{ $ld->phone }}</td>
                    <td>{{ $ld->unit?->title ?? ($ld->unit?->code ?? '-') }}</td>
                    <td><strong>{{ $ld->assignedUser?->name ?? 'Belum Ditugaskan' }}</strong></td>
                    <td style="text-transform: capitalize;">{{ $ld->source }}</td>
                    <td>{{ $ld->status }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align: center; color: #999;">Tidak ada data prospek.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Mengetahui -->
    @php
        $director = \App\Models\User::where('role', 'managing_director')->first() ?? auth()->user();
    @endphp
    <div class="signature-section">
        <div class="signature-box">
            <div>Sleman, {{ now()->translatedFormat('d F Y') }}</div>
            <div style="margin-top: 5px; font-weight: bold;">Managing Director</div>
            
            @if($director && $director->signature_url)
                <div class="signature-img-wrapper">
                    <img src="{{ $director->signature_url }}" alt="Tanda Tangan Digital">
                </div>
            @else
                <div class="empty-space"></div>
            @endif

            <div class="signature-line"></div>
            <div style="font-weight: bold; margin-top: 5px;">{{ $director->name ?? 'Managing Director' }}</div>
            <div style="font-size: 10px; color: #666;">PT Wastu Pratama Wijaya</div>
        </div>
    </div>

</body>
</html>
