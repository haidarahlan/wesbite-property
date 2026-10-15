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
        // 1. Site Settings (Pengaturan Umum, Nomor WA, Hide Terjual, dsb)
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, boolean, json
            $table->string('group')->default('general'); // general, contact, social, listing
            $table->timestamps();
        });

        // 2. Sections / Pages Konten (Hero banner, Tentang Kami, Visi Misi, Kemitraan)
        Schema::create('company_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique(); // hero, about, vision_mission, partnership
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_path')->nullable();
            $table->json('extra_data')->nullable(); // JSON data fleksibel
            $table->timestamps();
        });

        // 3. Layanan Kami
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-solid fa-house');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Testimoni Konsumen / Mitra
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_role')->nullable(); // e.g. Pembeli Casa de Borobudur, Mitra Developer
            $table->string('client_photo')->nullable();
            $table->text('testimony');
            $table->integer('rating')->default(5);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. FAQ (Tanya Jawab)
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('Umum'); // Umum, KPR, Legalitas
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Anggota Tim (Team Members)
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position'); // e.g. Managing Director, Marketing Lead
            $table->string('photo_path')->nullable();
            $table->string('phone')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('services');
        Schema::dropIfExists('company_sections');
        Schema::dropIfExists('site_settings');
    }
};
