<?php

namespace App\Console\Commands;

use App\Models\DailySnapshot;
use App\Models\Lead;
use App\Models\Survey;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDailySnapshotCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reports:generate-daily-snapshot';

    /**
     * The console command description.
     */
    protected $description = 'Kompilasi ringkasan data harian (leads, survei, stok unit, portfolio) ke tabel daily_snapshots';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();
        $this->info("Menghasilkan snapshot harian untuk tanggal {$today->toDateString()}...");

        $totalLeads = Lead::count();
        $newLeads = Lead::whereDate('created_at', $today)->count();
        $surveysScheduled = Survey::whereDate('survey_date', $today)->count();
        $surveysCompleted = Survey::where('status', 'Selesai')->whereDate('updated_at', $today)->count();

        $unitsAvailable = Unit::where('status', 'Tersedia')->count();
        $unitsBooked = Unit::where('status', 'Booking')->count();
        $unitsSold = Unit::where('status', 'Terjual')->count();
        $portfolioValue = (float) Unit::where('status', 'Tersedia')->sum('price');

        // Breakdown sumber lead
        $leadsBySource = Lead::selectRaw('source, count(*) as count')
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        DailySnapshot::updateOrCreate(
            ['snapshot_date' => $today->toDateString()],
            [
                'total_leads' => $totalLeads,
                'new_leads' => $newLeads,
                'surveys_scheduled' => $surveysScheduled,
                'surveys_completed' => $surveysCompleted,
                'units_available' => $unitsAvailable,
                'units_booked' => $unitsBooked,
                'units_sold' => $unitsSold,
                'total_portfolio_value' => $portfolioValue,
                'metrics_breakdown' => [
                    'leads_by_source' => $leadsBySource,
                ],
            ]
        );

        $this->info("Snapshot harian berhasil disimpan.");
        return Command::SUCCESS;
    }
}
