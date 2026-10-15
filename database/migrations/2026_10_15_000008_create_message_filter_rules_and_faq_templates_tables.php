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
        // 1. Tabel Aturan Kata Kunci Klasifikasi Pesan (Tertarik vs Tidak Tertarik)
        Schema::create('message_filter_rules', function (Blueprint $table) {
            $table->id();
            $table->string('keyword'); // misal: harga, brosur, survei, tertarik, mahal, batal
            $table->enum('classification', ['Tertarik', 'Tidak Tertarik']);
            $table->integer('weight')->default(1); // Bobot skor
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Template FAQ Auto-reply (Khusus Konsumen Tidak Tertarik / Butuh Info Umum)
        Schema::create('faq_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. FAQ-GENERAL, FAQ-LEGALITAS
            $table->string('title');
            $table->longText('template_message');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 3. Kolom klasifikasi pada tabel whatsapp_messages
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->string('classification')->nullable()->after('status'); // Tertarik, Tidak Tertarik
            $table->boolean('auto_replied')->default(false)->after('classification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn(['classification', 'auto_replied']);
        });
        Schema::dropIfExists('faq_templates');
        Schema::dropIfExists('message_filter_rules');
    }
};
