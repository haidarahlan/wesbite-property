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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('developer_id')->nullable()->constrained('developers')->nullOnDelete();

            // Lokasi
            $table->string('province')->default('DI Yogyakarta');
            $table->string('city'); // e.g. Sleman, Magelang, Bantul
            $table->string('district')->nullable(); // e.g. Depok, Ngagilik, Borobudur
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Spesifikasi Properti
            $table->string('type'); // Rumah Tinggal, Vila, Tanah, Kos-kosan, Ruko
            $table->decimal('price', 15, 2);
            $table->integer('land_area')->nullable(); // Luas Tanah (m2)
            $table->integer('building_area')->nullable(); // Luas Bangunan (m2)
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->string('certificate')->nullable(); // SHM, HGB, Girik/Letter C
            $table->string('electricity')->nullable(); // 1300 VA, 2200 VA
            $table->text('specifications')->nullable(); // JSON atau teks rincian
            $table->longText('description')->nullable();

            // Status & Publikasi
            $table->enum('status', ['Tersedia', 'Booking', 'Terjual'])->default('Tersedia');
            $table->boolean('is_published')->default(true);
            $table->date('valid_until')->nullable(); // Masa berlaku ketersediaan

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
