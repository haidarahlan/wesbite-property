<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Leads Table (Untuk relasi foreign key whatsapp_messages)
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code')->unique(); // Format: L-0001
            $table->string('name')->nullable();
            $table->string('phone')->index(); // Nomor WA konsumen
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('source')->default('whatsapp'); // whatsapp, website, manual
            $table->enum('status', [
                'Baru',
                'Tertarik',
                'Tidak Tertarik',
                'Follow-up',
                'Survei Terjadwal',
                'Survei Selesai',
                'Closing',
                'Batal',
            ])->default('Baru');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // PIC Admin/Marketing
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. WhatsApp Messages Table (Menyimpan seluruh chat masuk/keluar)
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('message_id')->nullable()->unique();
            $table->enum('direction', ['inbound', 'outbound'])->default('inbound');
            $table->string('phone_number')->index();
            $table->longText('message_body');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'received'])->default('received');
            $table->string('gateway_provider')->default('fake');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('leads');
    }
};
