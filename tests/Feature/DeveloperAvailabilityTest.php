<?php

namespace Tests\Feature;

use App\Models\AvailabilityRequest;
use App\Models\Developer;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeveloperAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Admin dapat membuat permintaan cek ketersediaan (token 48 jam dan teks WhatsApp dibuat).
     */
    public function test_admin_can_request_unit_availability_check(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $developer = Developer::create([
            'name' => 'Bpk. Haryanto',
            'company_name' => 'PT Graha Sentosa',
            'phone' => '6281234567899',
            'status' => 'Aktif',
        ]);

        $unit = Unit::create([
            'code' => 'WP-DEV-01',
            'title' => 'Vila Indah Borobudur',
            'slug' => 'vila-indah-borobudur',
            'developer_id' => $developer->id,
            'type' => 'Villa',
            'city' => 'Magelang',
            'district' => 'Borobudur',
            'province' => 'Jawa Tengah',
            'price' => 1200000000,
            'status' => 'Tersedia',
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/units/{$unit->id}/check-availability");

        $response->assertSessionHas('availability_success');
        $this->assertDatabaseHas('availability_requests', [
            'unit_id' => $unit->id,
            'developer_id' => $developer->id,
            'status' => 'Menunggu',
            'current_unit_status' => 'Tersedia',
        ]);
    }

    /**
     * Test 2: Developer dapat mengakses tautan token publik tanpa login dan submit update ketersediaan unit.
     */
    public function test_developer_can_confirm_availability_via_token_without_login(): void
    {
        $developer = Developer::create([
            'name' => 'Ibu Ratna',
            'company_name' => 'CV Mega Land',
            'phone' => '6281987654321',
            'status' => 'Aktif',
        ]);

        $unit = Unit::create([
            'code' => 'WP-DEV-02',
            'title' => 'Cluster Mega Asri',
            'slug' => 'cluster-mega-asri',
            'developer_id' => $developer->id,
            'type' => 'Rumah',
            'city' => 'Sleman',
            'district' => 'Depok',
            'province' => 'DI Yogyakarta',
            'price' => 750000000,
            'status' => 'Tersedia',
            'is_published' => true,
        ]);

        $availRequest = AvailabilityRequest::create([
            'unit_id' => $unit->id,
            'developer_id' => $developer->id,
            'token' => 'token-test-secret-12345',
            'current_unit_status' => 'Tersedia',
            'status' => 'Menunggu',
            'expires_at' => Carbon::now()->addHours(48),
        ]);

        // Akses halaman publik tanpa login
        $this->get("/cek-ketersediaan/{$availRequest->token}")
            ->assertStatus(200)
            ->assertSee('Konfirmasi Ketersediaan Unit Saat Ini');

        // Submit update status ke "Booking"
        $response = $this->post("/cek-ketersediaan/{$availRequest->token}", [
            'confirmed_unit_status' => 'Booking',
            'notes' => 'Ada konsumen baru transfer UTJ booking kavling A3.',
        ]);

        $response->assertSessionHas('confirmed_success');

        // Status unit di database harus ikut terupdate ke Booking
        $unit->refresh();
        $this->assertEquals('Booking', $unit->status);

        // Audit trail history harus tercatat
        $this->assertDatabaseHas('unit_status_histories', [
            'unit_id' => $unit->id,
            'from_status' => 'Tersedia',
            'to_status' => 'Booking',
        ]);
    }

    /**
     * Test 3: Token yang sudah kedaluwarsa dilarang untuk submit konfirmasi.
     */
    public function test_expired_token_cannot_confirm_availability(): void
    {
        $developer = Developer::create([
            'name' => 'Bpk. Budi',
            'company_name' => 'PT Cipta Karya',
            'phone' => '6281122334455',
        ]);

        $unit = Unit::create([
            'code' => 'WP-DEV-03',
            'title' => 'Ruko Sentra Niaga',
            'slug' => 'ruko-sentra-niaga',
            'developer_id' => $developer->id,
            'type' => 'Ruko',
            'city' => 'Semarang',
            'district' => 'Banyumanik',
            'province' => 'Jawa Tengah',
            'price' => 900000000,
            'status' => 'Tersedia',
            'is_published' => true,
        ]);

        // Token sudah expired 2 jam yang lalu
        $availRequest = AvailabilityRequest::create([
            'unit_id' => $unit->id,
            'developer_id' => $developer->id,
            'token' => 'token-expired-555',
            'current_unit_status' => 'Tersedia',
            'status' => 'Menunggu',
            'expires_at' => Carbon::now()->subHours(2),
        ]);

        $response = $this->post("/cek-ketersediaan/{$availRequest->token}", [
            'confirmed_unit_status' => 'Terjual',
        ]);

        $response->assertSessionHas('error');
        $unit->refresh();
        $this->assertEquals('Tersedia', $unit->status); // Status tidak boleh berubah
    }
}
