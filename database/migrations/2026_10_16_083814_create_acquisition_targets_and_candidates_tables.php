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
        // 1. Tabel Target Akuisisi (Ditetapkan oleh Superadmin)
        Schema::create('acquisition_targets', function (Blueprint $table) {
            $table->id();
            $table->string('period_name'); // Contoh: Q4 2026, Semester 1 2027
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('target_units')->default(10);
            $table->decimal('target_value', 15, 2)->default(0); // Target estimasi total nilai properti
            $table->string('target_region')->nullable(); // Contoh: Magelang & Borobudur
            $table->enum('status', ['Aktif', 'Selesai', 'Kedaluwarsa'])->default('Aktif');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Tabel Kandidat Akuisisi Properti (Diajukan oleh Admin/Marketing -> Direview Superadmin)
        Schema::create('acquisition_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->nullable()->constrained('acquisition_targets')->nullOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('property_type'); // Rumah, Vila, Tanah, Ruko
            $table->decimal('estimated_price', 15, 2)->default(0);
            $table->string('city');
            $table->string('district')->nullable();
            $table->text('address')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_phone')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Diajukan', 'Disetujui', 'Ditolak'])->default('Diajukan');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('converted_unit_id')->nullable()->constrained('units')->nullOnDelete(); // Jika sudah disetujui & dijadikan listing
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acquisition_candidates');
        Schema::dropIfExists('acquisition_targets');
    }
};
