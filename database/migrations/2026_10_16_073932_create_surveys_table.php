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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('survey_date');
            $table->enum('status', ['Terjadwal', 'Selesai', 'Dibatalkan', 'Dijadwal Ulang'])->default('Terjadwal');
            $table->text('notes')->nullable(); // Titik kumpul / catatan persiapan
            $table->text('result')->nullable(); // Hasil / rangkuman survei setelah selesai
            $table->timestamp('reminder_sent_at')->nullable(); // Penanda pengingat H-1 terkirim
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
