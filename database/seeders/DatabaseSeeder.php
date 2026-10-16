<?php

namespace Database\Seeders;

use App\Models\Developer;
use App\Models\Unit;
use App\Models\UnitPhoto;
use App\Models\UnitStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Default 3 Role (Managing Director, Superadmin, Admin Marketing)
        $managingDirector = User::updateOrCreate(
            ['email' => 'director@wastuproperty.co.id'],
            [
                'name' => 'Managing Director',
                'password' => Hash::make('123456'),
                'role' => 'managing_director',
                'phone' => '081122334455',
                'is_active' => true,
            ]
        );

        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@wastuproperty.co.id'],
            [
                'name' => 'Superadmin Operasional',
                'password' => Hash::make('123456'),
                'role' => 'superadmin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@wastuproperty.co.id'],
            [
                'name' => 'Marketing Officer (Admin)',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'phone' => '081298765432',
                'is_active' => true,
            ]
        );

        // 2. Data Developer Mitra
        $dev1 = Developer::create([
            'name' => 'PT Borobudur Graha Utama',
            'company_name' => 'Borobudur Land Development',
            'phone' => '081398711223',
            'email' => 'marketing@borobudurland.id',
            'address' => 'Jl. Pariwisata No. 8, Magelang',
            'status' => 'Aktif',
        ]);

        $dev2 = Developer::create([
            'name' => 'PT Sleman Pratama Makmur',
            'company_name' => 'Green Hills Group',
            'phone' => '081277889900',
            'email' => 'info@greenhills.co.id',
            'address' => 'Condongcatur, Sleman, DIY',
            'status' => 'Aktif',
        ]);

        // 3. Demo Unit Properti (Sesuai Referensi Gambar Dashboard)
        $unitsData = [
            [
                'code' => 'WP-BORO-01',
                'title' => 'Casa de Borobudur 2',
                'developer_id' => $dev1->id,
                'province' => 'Jawa Tengah',
                'city' => 'Magelang',
                'district' => 'Borobudur',
                'address' => 'Jl. Balaputradewa, Borobudur',
                'latitude' => -7.6079,
                'longitude' => 110.2038,
                'type' => 'Rumah Tinggal',
                'price' => 650000000,
                'land_area' => 120,
                'building_area' => 85,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'certificate' => 'SHM (Sertifikat Hak Milik)',
                'electricity' => '2200 VA',
                'status' => 'Tersedia',
                'is_published' => true,
                'photo' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'code' => 'WP-SLM-04',
                'title' => 'Green Hills Residence Ambarukmo',
                'developer_id' => $dev2->id,
                'province' => 'DI Yogyakarta',
                'city' => 'Sleman',
                'district' => 'Depok',
                'address' => 'Jl. Green Hills Ambarukmo No. A14, Dabag',
                'latitude' => -7.7700,
                'longitude' => 110.3950,
                'type' => 'Rumah Tinggal',
                'price' => 1250000000,
                'land_area' => 150,
                'building_area' => 130,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'certificate' => 'SHM',
                'electricity' => '3500 VA',
                'status' => 'Tersedia',
                'is_published' => true,
                'photo' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'code' => 'WP-BTL-02',
                'title' => 'Villa Etnik Kasongan Heritage',
                'developer_id' => $dev1->id,
                'province' => 'DI Yogyakarta',
                'city' => 'Bantul',
                'district' => 'Kasihan',
                'address' => 'Kawasan Sentra Gerabah Kasongan',
                'latitude' => -7.8300,
                'longitude' => 110.3300,
                'type' => 'Vila',
                'price' => 890000000,
                'land_area' => 180,
                'building_area' => 110,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'certificate' => 'SHM',
                'electricity' => '2200 VA',
                'status' => 'Booking',
                'is_published' => true,
                'photo' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'code' => 'WP-SLM-09',
                'title' => 'Pondok Indah Ngagilik Asri',
                'developer_id' => $dev2->id,
                'province' => 'DI Yogyakarta',
                'city' => 'Sleman',
                'district' => 'Ngagilik',
                'address' => 'Jl. Kaliurang KM 9, Ngagilik',
                'latitude' => -7.7200,
                'longitude' => 110.4000,
                'type' => 'Rumah Tinggal',
                'price' => 780000000,
                'land_area' => 110,
                'building_area' => 75,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'certificate' => 'SHM',
                'electricity' => '2200 VA',
                'status' => 'Tersedia',
                'is_published' => true,
                'photo' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'code' => 'WP-SLM-08',
                'title' => 'Kost Eksklusif Pogung UGM 12 Kamar',
                'developer_id' => $dev2->id,
                'province' => 'DI Yogyakarta',
                'city' => 'Sleman',
                'district' => 'Depok',
                'address' => 'Pogung Baru Blok C, Sleman',
                'latitude' => -7.7600,
                'longitude' => 110.3750,
                'type' => 'Kos-kosan',
                'price' => 3200000000,
                'land_area' => 250,
                'building_area' => 280,
                'bedrooms' => 12,
                'bathrooms' => 12,
                'certificate' => 'SHM',
                'electricity' => '5500 VA',
                'status' => 'Terjual',
                'is_published' => true,
                'photo' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'code' => 'WP-JOG-09',
                'title' => 'Kavling Premium Kotabaru Heritage',
                'developer_id' => null,
                'province' => 'DI Yogyakarta',
                'city' => 'Yogyakarta',
                'district' => 'Gondokusuman',
                'address' => 'Jl. I Dewa Nyoman Oka, Kotabaru',
                'latitude' => -7.7820,
                'longitude' => 110.3700,
                'type' => 'Tanah',
                'price' => 2100000000,
                'land_area' => 320,
                'building_area' => 0,
                'bedrooms' => 0,
                'bathrooms' => 0,
                'certificate' => 'SHM',
                'electricity' => null,
                'status' => 'Tersedia',
                'is_published' => true,
                'photo' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($unitsData as $data) {
            $photoUrl = $data['photo'];
            unset($data['photo']);

            $unit = Unit::create($data);

            // Buat foto utama
            UnitPhoto::create([
                'unit_id' => $unit->id,
                'photo_path' => $photoUrl,
                'is_primary' => true,
                'sort_order' => 0,
            ]);

            // Buat riwayat status awal
            UnitStatusHistory::create([
                'unit_id' => $unit->id,
                'user_id' => $superadmin->id,
                'from_status' => null,
                'to_status' => $unit->status,
                'notes' => 'Unit pertama kali didaftarkan oleh Superadmin.',
            ]);
        }

        // 4. Seeder Aturan Filter Keyword (Sub-Fase 3B)
        $filterRules = [
            ['keyword' => 'tertarik', 'classification' => 'Tertarik', 'weight' => 2],
            ['keyword' => 'harga', 'classification' => 'Tertarik', 'weight' => 1],
            ['keyword' => 'ketersediaan', 'classification' => 'Tertarik', 'weight' => 1],
            ['keyword' => 'survei', 'classification' => 'Tertarik', 'weight' => 2],
            ['keyword' => 'lokasi', 'classification' => 'Tertarik', 'weight' => 1],
            ['keyword' => 'brosur', 'classification' => 'Tertarik', 'weight' => 1],
            ['keyword' => 'kpr', 'classification' => 'Tertarik', 'weight' => 1],
            ['keyword' => 'beli', 'classification' => 'Tertarik', 'weight' => 2],
            ['keyword' => 'mahal', 'classification' => 'Tidak Tertarik', 'weight' => 2],
            ['keyword' => 'batal', 'classification' => 'Tidak Tertarik', 'weight' => 3],
            ['keyword' => 'tidak jadi', 'classification' => 'Tidak Tertarik', 'weight' => 3],
            ['keyword' => 'belum berminat', 'classification' => 'Tidak Tertarik', 'weight' => 3],
            ['keyword' => 'salah sambung', 'classification' => 'Tidak Tertarik', 'weight' => 3],
        ];

        foreach ($filterRules as $rule) {
            \App\Models\MessageFilterRule::firstOrCreate(
                ['keyword' => $rule['keyword']],
                $rule
            );
        }

        // 5. Template Auto-Reply FAQ Default (Sub-Fase 3B)
        \App\Models\FaqTemplate::firstOrCreate(
            ['code' => 'FAQ-DEFAULT'],
            [
                'title' => 'Jawaban FAQ Otomatis Umum',
                'template_message' => "Halo! Terima kasih telah menghubungi PT Wastu Pratama Wijaya.\n\nJika saat ini Anda belum menemukan unit yang sesuai atau memiliki pertanyaan seputar:\n1. Konsultasi & Simulasi KPR Bank\n2. Cek Legalitas Sertifikat (SHM/Girik)\n3. Titip Jual Properti Anda\n\nTim Marketing kami siap membantu kapan saja. Silakan kunjungi website kami di wastuproperty.co.id untuk melihat katalog properti lainnya.",
                'is_default' => true,
            ]
        );
    }
}
