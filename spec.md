# ROLE

Kamu adalah senior full-stack engineer (Laravel). Bangun website CRM + Company Profile
untuk "PT Wastu Pratama Wijaya" (bisnis akuisisi & pemasaran properti/unit) yang lengkap,
rapi, dan siap dikembangkan. Sistem dirancang SEDERHANA: tanpa API WhatsApp resmi,
komunikasi WhatsApp memakai WhatsApp Business App secara manual, dan CRM menjadi pusat
pencatatan.

# KONTEKS BISNIS

Perusahaan mencari & mengakuisisi unit properti dari developer, lalu memasarkannya lewat
website dan grup WhatsApp. Konsumen menghubungi lewat WhatsApp Business, lalu difilter,
di-follow up, survei lapangan, dan pembayaran. Semua data leads dan status unit terpusat
di CRM dan tampil real-time di dashboard. Perubahan di dashboard otomatis tampil di
website company profile.

# TECH STACK

- Laravel Terbaru, PHP 8.2+, MySQL
- Frontend: Blade + Tailwind CSS + Alpine.js, Livewire 3 untuk komponen interaktif
- Auth: Laravel Breeze, role sederhana (kolom `role` enum: superadmin, admin) atau
  spatie/laravel-permission
- Chart: Chart.js. Export: laravel-excel dan dompdf
- Real-time: Livewire polling 15-30 detik
- Queue & scheduler: database queue + Laravel Scheduler
- Dev environment: Laravel Valet (domain .test)
- Gunakan Eloquent, FormRequest, Policy, Service class, Observer, Seeder, Factory

# ROLE & HAK AKSES (hanya 2 role)

1. SUPERADMIN (gabungan Managing Director + IT Support)
    - Akses penuh ke semua modul
    - Menetapkan strategi & target akuisisi, menyetujui kandidat properti potensial
    - Monitoring proses akuisisi, evaluasi kinerja & efisiensi tim
    - Menerima laporan mingguan/bulanan otomatis
    - Manajemen user (buat/nonaktifkan Admin), pengaturan sistem, activity log, backup
2. ADMIN (gabungan Admin + Marketing)
    - CRUD unit/listing, data developer, update status unit
    - Menghubungi developer & membuat permintaan cek ketersediaan
    - Mengelola leads, interaksi, follow-up, survei lapangan, pembayaran
    - Mengelola konten company profile (banner, layanan, FAQ, testimoni, kontak)
    - Tidak bisa: mengelola user, mengubah target & pengaturan sistem, menghapus
      permanen data, approval kandidat properti
3. DEVELOPER (tanpa akun)
    - Menerima link unik berisi token (kedaluwarsa, sekali pakai) untuk konfirmasi
      ketersediaan unit: Tersedia / Tidak Tersedia / Booking, catatan, dan masa berlaku
4. KONSUMEN (publik, tanpa login)
    - Melihat listing, mengisi form minat, lanjut chat WhatsApp

# STRATEGI WHATSAPP (TANPA API)

Tidak ada integrasi API WhatsApp. Gunakan pendekatan berikut:

1. Click-to-chat (wa.me): tombol "Hubungi via WhatsApp" di setiap listing membuka chat
   ke nomor WA Business perusahaan dengan pesan terisi otomatis, contoh:
   "Halo, saya tertarik dengan unit {kode_unit} ({nama_unit}). Ref: {kode_lead}"
2. Form minat di website (nama, no WA, unit diminati) membuat lead otomatis di CRM
   dengan kode referensi unik (format L-0001), lalu redirect ke wa.me.
3. Fitur WhatsApp Business App yang dipakai secara manual oleh Admin, dan CRM
   menyediakan PANDUAN SETUP-nya di halaman "Panduan WhatsApp":
    - Label: Baru, Tertarik, Tidak Tertarik, Follow-up, Survei, Closing
      (disamakan persis dengan status lead di CRM)
    - Quick replies: template FAQ umum
    - Pesan sambutan & pesan di luar jam kerja
    - Katalog: berisi listing unit
    - Backup chat ke Google Drive (bawaan WA Business)
4. CRM menyediakan fitur pendukung manual:
    - Generator teks listing siap-salin (+ tombol "Salin" & "Buka WhatsApp")
      untuk dipublikasi ke grup WA
    - Pengingat "belum ada respon dalam X jam, publikasi ulang" per listing
    - Template FAQ umum (CRUD) dengan tombol "Salin" dan "Kirim via wa.me"
    - Form "Catat Interaksi WhatsApp": tempel ringkasan chat, pilih klasifikasi
      (Tertarik/Tidak Tertarik), dan opsi tautan arsip Google Drive (URL manual)
    - Tombol "Chat Lead" di detail lead (wa.me/{nomor}?text={template})
5. Buat interface/Service class `WhatsAppGateway` dengan implementasi default
   `ClickToChatGateway`, agar bila nanti ingin memakai API (Fonnte/WAHA/Cloud API)
   cukup membuat implementasi baru tanpa mengubah logika bisnis.

# MODUL & FITUR

## A. Company Profile (Website Publik)

Halaman: Beranda, Tentang Kami, Visi & Misi, Layanan, Daftar Properti, Detail Properti,
Kemitraan Developer, Testimoni, FAQ, Kontak.

- Semua konten dikelola dari dashboard (CMS ringan).
- Listing menampilkan unit dari database CRM: galeri foto, harga, lokasi, tipe, luas,
  spesifikasi, status (Tersedia/Booking/Terjual), tombol WhatsApp, form minat.
- Filter & pencarian: lokasi, tipe, rentang harga, status. SEO-friendly (slug, meta tag,
  sitemap).
- SINKRONISASI DASHBOARD -> PROFILE: setiap perubahan unit, status unit, atau konten
  profil di dashboard langsung tercermin di website publik (Eloquent Observer + cache
  tag yang di-invalidate otomatis). Unit Terjual diberi label "Terjual" atau disembunyikan
  sesuai pengaturan.

## B. Dashboard Internal

- Kartu ringkasan: total leads, leads baru hari ini, leads aktif, survei terjadwal,
  unit tersedia/booking/terjual, konversi lead -> survei -> pembayaran.
- Grafik: leads per hari/minggu/bulan, leads per sumber (website, grup WA, manual),
  funnel konversi, status unit, progres terhadap target akuisisi.
- Notifikasi & pengingat: unit hampir habis masa berlaku, follow-up jatuh tempo,
  survei H-1, konfirmasi developer masuk, listing belum direspon.
- Tampilan Superadmin: KPI, evaluasi tim, kesehatan sistem.
  Tampilan Admin: leads, unit, tugas harian.
- Data real-time lewat Livewire polling.

## C. Strategi & Target Akuisisi (Superadmin)

- CRUD target akuisisi (periode, jumlah unit, nilai, wilayah) dengan progres otomatis.
- Kandidat properti potensial: Admin mengajukan -> Superadmin Setujui/Tolak + catatan.
- Penugasan pencarian unit ke Admin dan timeline status akuisisi.
- Evaluasi kinerja & efisiensi tim (KPI, perbandingan antar periode).

## D. Developer & Ketersediaan Unit

- CRUD data developer (kontak, kerja sama, status).
- Tombol "Minta Cek Ketersediaan" pada unit: sistem membuat Availability Request dengan
  token unik dan menghasilkan teks pesan + link konfirmasi, yang dikirim Admin ke
  developer via WhatsApp (tombol wa.me).
- Halaman publik konfirmasi developer (akses via token): pilih status, catatan, masa
  berlaku, lalu submit.
- Setelah konfirmasi masuk: notifikasi ke Admin -> Admin update status unit -> sistem
  memperbarui dashboard & website, dan membuat teks "update stok" siap-salin untuk
  dibagikan ke grup/tim.

## E. Manajemen Listing / Unit

- CRUD unit: kode, nama, developer, lokasi, tipe, harga, luas, spesifikasi, galeri,
  status, masa berlaku, publish/unpublish.
- Riwayat perubahan status (audit trail: siapa, kapan, dari-ke).

## F. Manajemen Leads & Interaksi

- Alur status lead: Baru -> Tertarik / Tidak Tertarik -> Follow-up -> Survei Terjadwal
  -> Survei Selesai -> Pengajuan Pembayaran -> Closing / Batal.
- Sumber lead: website, grup WhatsApp, WhatsApp langsung, manual.
- Tidak Tertarik -> sarankan template FAQ umum untuk dikirim.
- Interaksi: log catatan, ringkasan chat, tautan arsip Google Drive, riwayat status.
- Survei lapangan: tanggal, unit, PIC, status, hasil; pengingat H-1 di dashboard.
- Pembayaran: jumlah, metode, bukti (upload), status verifikasi.
- Pencarian & filter lead, export Excel.

## G. Laporan

- Laporan mingguan & bulanan otomatis (Scheduler): leads, konversi, status unit, progres
  target. Format PDF & Excel, tampil di dashboard Superadmin, opsional dikirim email.
- Tabel ringkasan harian (daily_snapshots) untuk laporan cepat.

## H. Administrasi Sistem (Superadmin)

- Manajemen user (Admin), pengaturan sistem (nomor WA bisnis, template pesan,
  durasi pengingat respon, tampilkan/sembunyikan unit terjual), activity log,
  backup database terjadwal.

# SKEMA DATABASE (minimal)

users (kolom role), developers, units, unit_photos, unit_status_histories,
availability_requests (token, expires_at), acquisition_targets, acquisition_candidates,
leads, lead_interactions, lead_status_histories, surveys, payments, faq_templates,
whatsapp_templates, listing_publications (untuk pengingat publikasi ulang), reports,
daily_snapshots, site_settings, pages/sections, testimonials, team_members, faqs,
activity_logs, notifications. Sertakan relasi, foreign key, index, soft delete bila perlu.

# ALUR UTAMA YANG HARUS TERIMPLEMENTASI

1. Superadmin menetapkan target -> Admin mengajukan kandidat -> Superadmin menyetujui.
2. Admin minta cek ketersediaan -> developer konfirmasi via link token -> Admin update
   status unit -> dashboard & website ikut berubah.
3. Admin publikasi listing ke grup WA (teks siap-salin) -> belum ada respon dalam X jam:
   muncul pengingat publikasi ulang.
4. Konsumen lihat listing / klik WA -> form minat -> lead masuk CRM (kode ref) -> chat
   WhatsApp Business -> Admin klasifikasi (Tertarik / Tidak Tertarik).
   Tidak tertarik -> template FAQ. Tertarik -> catat interaksi + tautan arsip Drive ->
   follow-up -> survei -> pengajuan pembayaran.
5. Dashboard terupdate otomatis -> laporan mingguan/bulanan -> Superadmin evaluasi kinerja.
6. Setiap perubahan data di dashboard tersinkron ke website company profile.

# UI/UX

- Responsif (mobile-first), modern, bersih. Sidebar dinamis sesuai role, breadcrumb,
  tabel dengan search/sort/filter, toast notifikasi, validasi pesan bahasa Indonesia.
- Seluruh teks Bahasa Indonesia (locale id, timezone Asia/Jakarta).

# KUALITAS KODE

- Controller tipis, logika di Service/Action class, Policy untuk otorisasi per role.
- FormRequest, eager loading (hindari N+1), pagination.
- Seeder demo: 1 Superadmin, 2 Admin, 20 unit, 50 leads, developer, konten profil.
- Feature test: sinkron status unit -> website, alur lead, konfirmasi developer via token
  (termasuk token kedaluwarsa), otorisasi Admin vs Superadmin.
- README: instalasi dengan Valet (`valet link`, `.env`, `php artisan migrate --seed`,
  `npm run dev`), akun demo, panduan setup WhatsApp Business App, arsitektur.

# CARA KERJA

Kerjakan bertahap dan berhenti di akhir tiap fase untuk konfirmasi:
Fase 1: setup project, auth, 2 role, layout dashboard, migration & seeder.
Fase 2: modul Unit + Company Profile publik + sinkronisasi dashboard -> website.
Fase 3: modul Leads, form minat + kode ref, interaksi, template FAQ, survei, pembayaran,
fitur pendukung WhatsApp (teks siap-salin, tombol wa.me).
Fase 4: modul Developer, link token konfirmasi, notifikasi & pengingat.
Fase 5: modul Superadmin (target, kandidat, evaluasi), dashboard analitik, laporan.
Fase 6: halaman Panduan WhatsApp, testing, README, polishing.
Jika ada hal ambigu, buat asumsi yang masuk akal, tulis asumsinya, lalu lanjut.
