<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\MessageFilterRule;
use App\Models\Survey;
use App\Models\Unit;
use App\Models\User;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppLeadSurveyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed filter rules standar
        MessageFilterRule::create([
            'keyword' => 'harga',
            'classification' => 'Tertarik',
            'weight' => 2,
            'is_active' => true,
        ]);

        MessageFilterRule::create([
            'keyword' => 'survei',
            'classification' => 'Tertarik',
            'weight' => 3,
            'is_active' => true,
        ]);

        MessageFilterRule::create([
            'keyword' => 'lowongan',
            'classification' => 'Tidak Tertarik',
            'weight' => 3,
            'is_active' => true,
        ]);
    }

    /**
     * Test 1: Pesan WhatsApp dengan keyword tertarik membuat lead baru dan notifikasi.
     */
    public function test_incoming_interested_message_creates_lead(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $payload = [
            'from' => '6281234567890',
            'message' => 'Halo min, mau tanya info harga dan jadwal survei lokasi ya',
            'id' => 'msg-test-01',
        ];

        $response = $this->postJson('/webhook/whatsapp', $payload);
        $response->assertStatus(200);

        // Pastikan lead tercatat
        $this->assertDatabaseHas('leads', [
            'phone' => '6281234567890',
            'status' => 'Tertarik',
        ]);

        // Pastikan pesan tercatat
        $this->assertDatabaseHas('whatsapp_messages', [
            'phone_number' => '6281234567890',
            'direction' => 'inbound',
        ]);
    }

    /**
     * Test 2: Pesan WhatsApp tidak tertarik memicu balasan auto-reply FAQ tanpa membuat status Tertarik.
     */
    public function test_incoming_uninterested_message_triggers_autoreply(): void
    {
        $payload = [
            'from' => '6289999888877',
            'message' => 'Apakah ada lowongan kerja di kantor wastu property?',
            'id' => 'msg-test-02',
        ];

        $response = $this->postJson('/webhook/whatsapp', $payload);
        $response->assertStatus(200);

        // Pastikan ada pesan keluar (auto-reply) ke nomor tersebut
        $this->assertDatabaseHas('whatsapp_messages', [
            'phone_number' => '6289999888877',
            'direction' => 'outbound',
        ]);
    }

    /**
     * Test 3: Pesan berulang dari nomor yang sama tidak membuat lead ganda.
     */
    public function test_duplicate_incoming_message_does_not_duplicate_lead(): void
    {
        $payload = [
            'from' => '628111222333',
            'message' => 'Tanya harga rumah',
            'id' => 'msg-test-03',
        ];

        $this->postJson('/webhook/whatsapp', $payload);
        $this->postJson('/webhook/whatsapp', array_merge($payload, ['id' => 'msg-test-04', 'message' => 'Berapa cicilan per bulannya?']));

        $leadCount = Lead::where('phone', '628111222333')->count();
        $this->assertEquals(1, $leadCount);
    }

    /**
     * Test 4: Otorisasi Role - Admin & Superadmin dapat mengakses manajemen lead & survei, guest dilarang.
     */
    public function test_lead_and_survey_routes_require_authentication(): void
    {
        $response = $this->get('/admin/leads');
        $response->assertRedirect('/login');

        $responseSurvey = $this->get('/admin/surveys');
        $responseSurvey->assertRedirect('/login');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin/leads')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/surveys')->assertStatus(200);
    }

    /**
     * Test 5: Command pengingat survei H-1 mengirim pesan WA dan mencatat reminder_sent_at.
     */
    public function test_survey_reminder_command_sends_whatsapp_h1(): void
    {
        $lead = Lead::create([
            'reference_code' => 'L-9999',
            'name' => 'Pak Bambang',
            'phone' => '628777666555',
            'status' => 'Survei Terjadwal',
        ]);

        $unit = Unit::create([
            'code' => 'WP-TEST-01',
            'title' => 'Rumah Nyaman Test',
            'slug' => 'rumah-nyaman-test',
            'type' => 'Rumah',
            'city' => 'Magelang',
            'district' => 'Borobudur',
            'province' => 'Jawa Tengah',
            'price' => 500000000,
            'status' => 'Tersedia',
            'is_published' => true,
        ]);

        // Buat survei untuk besok jam 10:00 pagi
        $survey = Survey::create([
            'lead_id' => $lead->id,
            'unit_id' => $unit->id,
            'survey_date' => Carbon::tomorrow()->setTime(10, 0),
            'status' => 'Terjadwal',
            'notes' => 'Gerbang utama perumahan',
        ]);

        $this->artisan('surveys:send-reminders')
            ->assertSuccessful();

        $survey->refresh();
        $this->assertNotNull($survey->reminder_sent_at);

        // Pastikan tercatat pesan outbound pengingat ke konsumen
        $this->assertDatabaseHas('whatsapp_messages', [
            'lead_id' => $lead->id,
            'phone_number' => '628777666555',
            'direction' => 'outbound',
        ]);
    }
}
