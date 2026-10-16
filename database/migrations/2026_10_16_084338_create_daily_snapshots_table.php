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
        Schema::create('daily_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date')->unique(); // Tanggal snapshot harian
            $table->integer('total_leads')->default(0);
            $table->integer('new_leads')->default(0);
            $table->integer('surveys_scheduled')->default(0);
            $table->integer('surveys_completed')->default(0);
            $table->integer('units_available')->default(0);
            $table->integer('units_booked')->default(0);
            $table->integer('units_sold')->default(0);
            $table->decimal('total_portfolio_value', 16, 2)->default(0); // Total nilai listing aktif
            $table->json('metrics_breakdown')->nullable(); // Rincian per sumber channel atau tipe unit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_snapshots');
    }
};
