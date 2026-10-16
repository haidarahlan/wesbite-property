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
        .signature-section { margin-top: 50px; display: flex; justify-content: flex-end; }
        .signature-box { text-align: center; width: 220px; }
        .signature-line { margin-top: 70px; border-bottom: 1px solid #222; }
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
        </div>
        <div class="card">
            <div class="card-label">Survei Lapangan</div>
            <div class="card-value">{{ $surveys->where('status', 'Selesai')->count() }} / {{ $surveys->count() }} Agenda</div>
        </div>
        <div class="card">
            <div class="card-label">Unit Closing Terjual</div>
            <div class="card-value">{{ $soldUnits->count() }} Properti</div>
        </div>
    </div>

    <!-- Tabel Rekap Leads -->
    <h3 style="font-size: 13px; margin-bottom: 8px; border-left: 3px solid #C5A038; padding-left: 8px;">
        1. Ringkasan Prospek & Leads Masuk (Periode Terpilih)
    </h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Ref</th>
                <th>Nama Konsumen</th>
                <th>Nomor WhatsApp</th>
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
                    <td style="text-transform: capitalize;">{{ $ld->source }}</td>
                    <td>{{ $ld->status }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center; color: #999;">Tidak ada data prospek.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Mengetahui -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Sleman, {{ now()->translatedFormat('d F Y') }}</div>
            <div style="margin-top: 5px; font-weight: bold;">Managing Director</div>
            <div class="signature-line"></div>
            <div style="font-weight: bold; margin-top: 5px;">Haidar</div>
            <div style="font-size: 10px; color: #666;">PT Wastu Pratama Wijaya</div>
        </div>
    </div>

</body>
</html>
