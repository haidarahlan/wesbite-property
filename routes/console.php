<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengingat Survei Lapangan H-1 (Fase 3D) - Berjalan otomatis setiap pagi jam 08:00 WIB
Schedule::command('surveys:send-reminders')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Generator Snapshot Harian Laporan (Fase 5B) - Berjalan setiap malam jam 23:59 WIB
Schedule::command('reports:generate-daily-snapshot')
    ->dailyAt('23:59')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

