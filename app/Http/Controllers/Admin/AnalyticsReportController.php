<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcquisitionCandidate;
use App\Models\AcquisitionTarget;
use App\Models\DailySnapshot;
use App\Models\Lead;
use App\Models\Survey;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsReportController extends Controller
{
    public function __construct()
    {
        // Akses analitik dan laporan hanya untuk Superadmin dan Managing Director
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses Analitik & Laporan.');
        }
    }

    /**
     * Executive Dashboard: Visualisasi Funnel CRM, Metrik KPI, & Grafik Efektivitas Channel.
     * HANYA BISA DIAKSES OLEH MANAGING DIRECTOR.
     */
    public function dashboard(Request $request)
    {
        if (!auth()->user()->isManagingDirector()) {
            abort(403, 'Akses Terbatas: Executive Dashboard & Metrik Finansial hanya dapat diakses oleh Managing Director.');
        }

        // 1. Funnel Konversi CRM (BPMN Pipeline)
        $totalLeads = Lead::count();
        $interestedLeads = Lead::whereIn('status', ['Tertarik', 'Follow-up', 'Survei Terjadwal', 'Survei Selesai', 'Closing'])->count();
        $scheduledSurveys = Survey::count();
        $completedSurveys = Survey::where('status', 'Selesai')->count();
        $closedUnits = Unit::where('status', 'Terjual')->count();

        // Persentase Funnel
        $interestRate = $totalLeads > 0 ? round(($interestedLeads / $totalLeads) * 100, 1) : 0;
        $surveyRate = $interestedLeads > 0 ? round(($completedSurveys / $interestedLeads) * 100, 1) : 0;
        $closingRate = $totalLeads > 0 ? round(($closedUnits / $totalLeads) * 100, 1) : 0;

        // 2. Efektivitas Channel / Sumber Leads
        $leadSources = Lead::selectRaw('source, count(*) as count')
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        // 3. Status Portfolio Unit Saat Ini
        $unitsAvailable = Unit::where('status', 'Tersedia')->count();
        $unitsBooked = Unit::where('status', 'Booking')->count();
        $unitsSold = Unit::where('status', 'Terjual')->count();
        $totalPortfolioValue = (float) Unit::where('status', 'Tersedia')->sum('price');
        $soldPortfolioValue = (float) Unit::where('status', 'Terjual')->sum('price');

        // 4. Performa Tim Marketing (PIC)
        $marketingStaff = User::where('is_active', true)
            ->withCount([
                'assignedLeads',
                'surveys as completed_surveys_count' => function ($q) {
                    $q->where('status', 'Selesai');
                }
            ])
            ->get();

        // 5. Tren 7 Hari Terakhir dari Daily Snapshots
        $recentSnapshots = DailySnapshot::orderBy('snapshot_date', 'asc')
            ->limit(7)
            ->get();

        return view('admin.analytics.dashboard', compact(
            'totalLeads',
            'interestedLeads',
            'scheduledSurveys',
            'completedSurveys',
            'closedUnits',
            'interestRate',
            'surveyRate',
            'closingRate',
            'leadSources',
            'unitsAvailable',
            'unitsBooked',
            'unitsSold',
            'totalPortfolioValue',
            'soldPortfolioValue',
            'marketingStaff',
            'recentSnapshots'
        ));
    }

    /**
     * Halaman Generator Laporan Resmi & Ekspor Dokumen.
     */
    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $parsedStart = Carbon::parse($startDate)->startOfDay();
        $parsedEnd = Carbon::parse($endDate)->endOfDay();

        // Ambil Data Berdasarkan Rentang Tanggal
        $leads = Lead::with(['unit', 'assignedUser'])
            ->whereBetween('created_at', [$parsedStart, $parsedEnd])
            ->latest()
            ->get();

        $surveys = Survey::with(['lead', 'unit', 'pic'])
            ->whereBetween('survey_date', [$parsedStart, $parsedEnd])
            ->latest('survey_date')
            ->get();

        // Leads yang Closing pada rentang tanggal
        $closingLeads = Lead::with(['unit', 'assignedUser'])
            ->where('status', 'Closing')
            ->where(function ($q) use ($parsedStart, $parsedEnd) {
                $q->whereBetween('updated_at', [$parsedStart, $parsedEnd])
                  ->orWhereBetween('created_at', [$parsedStart, $parsedEnd]);
            })
            ->latest()
            ->get();

        $closingUnitIds = $closingLeads->pluck('unit_id')->filter()->unique();

        // Unit yang berstatus 'Terjual' ATAU unit dari leads yang berhasil closing
        $soldUnits = Unit::where(function ($q) use ($parsedStart, $parsedEnd, $closingUnitIds) {
            $q->where(function ($sub) use ($parsedStart, $parsedEnd) {
                $sub->where('status', 'Terjual')
                    ->whereBetween('updated_at', [$parsedStart, $parsedEnd]);
            })->orWhereIn('id', $closingUnitIds);
        })->latest()->get();

        // Data Target & Usulan Akuisisi Unit
        $acquisitionTargets = AcquisitionTarget::with(['createdBy', 'candidates'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere('status', 'Aktif');
            })
            ->latest()
            ->get();

        $acquisitionCandidates = AcquisitionCandidate::with(['target', 'submitter'])
            ->whereBetween('created_at', [$parsedStart, $parsedEnd])
            ->latest()
            ->get();

        return view('admin.analytics.reports', compact(
            'startDate',
            'endDate',
            'leads',
            'surveys',
            'soldUnits',
            'closingLeads',
            'acquisitionTargets',
            'acquisitionCandidates'
        ));
    }

    /**
     * Ekspor Data Leads ke CSV / Excel Format.
     */
    public function exportLeadsCsv(Request $request): StreamedResponse
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $leads = Lead::with(['unit', 'assignedUser'])
            ->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->latest()
            ->get();

        $fileName = 'Laporan-Leads-Wastu-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($leads) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kode Ref', 'Nama Konsumen', 'No. WhatsApp', 'Unit Minat', 'Sumber', 'Status', 'PIC Marketing', 'Tanggal Masuk', 'Catatan']);

            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->reference_code,
                    $lead->name ?? '-',
                    $lead->phone,
                    $lead->unit?->title ?? '-',
                    $lead->source,
                    $lead->status,
                    $lead->assignedUser?->name ?? 'Belum Ditugaskan',
                    $lead->created_at->format('Y-m-d H:i'),
                    $lead->notes,
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Format Siap Cetak (Print-Friendly / PDF Preview) untuk Managing Director.
     */
    public function printReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $parsedStart = Carbon::parse($startDate)->startOfDay();
        $parsedEnd = Carbon::parse($endDate)->endOfDay();

        $leads = Lead::with(['unit', 'assignedUser'])
            ->whereBetween('created_at', [$parsedStart, $parsedEnd])
            ->latest()
            ->get();

        $surveys = Survey::with(['lead', 'unit', 'pic'])
            ->whereBetween('survey_date', [$parsedStart, $parsedEnd])
            ->latest('survey_date')
            ->get();

        // Leads yang Closing pada rentang tanggal
        $closingLeads = Lead::with(['unit', 'assignedUser'])
            ->where('status', 'Closing')
            ->where(function ($q) use ($parsedStart, $parsedEnd) {
                $q->whereBetween('updated_at', [$parsedStart, $parsedEnd])
                  ->orWhereBetween('created_at', [$parsedStart, $parsedEnd]);
            })
            ->latest()
            ->get();

        $closingUnitIds = $closingLeads->pluck('unit_id')->filter()->unique();

        // Unit yang berstatus 'Terjual' ATAU unit dari leads yang berhasil closing
        $soldUnits = Unit::where(function ($q) use ($parsedStart, $parsedEnd, $closingUnitIds) {
            $q->where(function ($sub) use ($parsedStart, $parsedEnd) {
                $sub->where('status', 'Terjual')
                    ->whereBetween('updated_at', [$parsedStart, $parsedEnd]);
            })->orWhereIn('id', $closingUnitIds);
        })->latest()->get();

        $totalClosingValue = (float) $soldUnits->sum('price');

        // Target Akuisisi Unit untuk dokumen cetak Managing Director
        $acquisitionTargets = AcquisitionTarget::with(['createdBy', 'candidates'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere('status', 'Aktif');
            })
            ->latest()
            ->get();

        $acquisitionCandidates = AcquisitionCandidate::with(['target', 'submitter'])
            ->whereBetween('created_at', [$parsedStart, $parsedEnd])
            ->latest()
            ->get();

        return view('admin.analytics.print', compact(
            'startDate',
            'endDate',
            'leads',
            'surveys',
            'soldUnits',
            'closingLeads',
            'totalClosingValue',
            'acquisitionTargets',
            'acquisitionCandidates'
        ));
    }
}
