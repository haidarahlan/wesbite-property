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
        Schema::create('availability_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('developer_id')->constrained('developers')->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete(); // Admin peminta
            $table->string('token', 64)->unique(); // Token akses publik rahasia
            $table->string('current_unit_status'); // Status unit saat permintaan dikirim
            $table->enum('status', ['Menunggu', 'Terkonfirmasi', 'Kedaluwarsa'])->default('Menunggu');
            $table->string('confirmed_unit_status')->nullable(); // Status baru yang dipilih developer (Tersedia / Booking / Terjual)
            $table->text('notes')->nullable(); // Catatan dari developer saat konfirmasi
            $table->date('extended_valid_until')->nullable(); // Opsi perpanjangan masa berlaku listing unit
            $table->timestamp('expires_at'); // Masa berlaku token (default 48 jam)
            $table->timestamp('confirmed_at')->nullable(); // Waktu developer submit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_requests');
    }
};
