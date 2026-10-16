<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Survey;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    /**
     * Tampilkan daftar survei lapangan.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Survey::with(['lead', 'unit', 'pic'])->latest('survey_date');

        // OPSI A: Jika Marketing/Admin biasa, hanya tampilkan survei yang ditugaskan kepada dirinya
        if (!$isSuperAdmin) {
            $query->where('pic_user_id', $user->id);
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter periode
        if ($request->filled('timeframe')) {
            if ($request->timeframe === 'today') {
                $query->whereDate('survey_date', Carbon::today());
            } elseif ($request->timeframe === 'tomorrow') {
                $query->whereDate('survey_date', Carbon::tomorrow());
            } elseif ($request->timeframe === 'upcoming') {
                $query->where('survey_date', '>=', Carbon::now());
            }
        }

        // Filter pencarian teks
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('lead', function ($lq) use ($search) {
                    $lq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%")
                       ->orWhere('reference_code', 'like', "%{$search}%");
                })->orWhereHas('unit', function ($uq) use ($search) {
                    $uq->where('code', 'like', "%{$search}%")
                       ->orWhere('title', 'like', "%{$search}%");
                });
            });
        }

        $surveys = $query->paginate(15)->withQueryString();

        // Metrik Ringkas (Sesuai cakupan akses user)
        $baseMetrics = Survey::query();
        if (!$isSuperAdmin) {
            $baseMetrics->where('pic_user_id', $user->id);
        }

        $totalSurveys = (clone $baseMetrics)->count();
        $scheduledSurveys = (clone $baseMetrics)->where('status', 'Terjadwal')->count();
        $tomorrowSurveys = (clone $baseMetrics)->where('status', 'Terjadwal')
            ->whereDate('survey_date', Carbon::tomorrow())
            ->count();
        $completedSurveys = (clone $baseMetrics)->where('status', 'Selesai')->count();

        // Konsumen hanya yang berstatus "Survei Terjadwal"
        $leads = Lead::where('status', 'Survei Terjadwal')
            ->select('id', 'name', 'phone', 'reference_code', 'unit_id')
            ->orderBy('name')
            ->get();
        $units = Unit::select('id', 'code', 'title')->orderBy('code')->get();
        $users = User::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('admin.surveys.index', compact(
            'surveys',
            'totalSurveys',
            'scheduledSurveys',
            'tomorrowSurveys',
            'completedSurveys',
            'leads',
            'units',
            'users'
        ));
    }

    /**
     * Jadwalkan survei baru untuk konsumen.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'survey_date' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'lead_id.required' => 'Konsumen/Lead wajib dipilih.',
            'unit_id.required' => 'Unit properti wajib dipilih.',
            'survey_date.required' => 'Tanggal & jam survei wajib ditentukan.',
            'survey_date.after' => 'Waktu survei harus di masa mendatang.',
        ]);

        $survey = Survey::create([
            'lead_id' => $validated['lead_id'],
            'unit_id' => $validated['unit_id'],
            'pic_user_id' => $validated['pic_user_id'],
            'survey_date' => $validated['survey_date'],
            'status' => 'Terjadwal',
            'notes' => $validated['notes'],
        ]);

        // Perbarui status lead menjadi "Survei Terjadwal"
        $lead = Lead::findOrFail($validated['lead_id']);
        $lead->update([
            'status' => 'Survei Terjadwal',
            'unit_id' => $validated['unit_id'],
            'assigned_to' => $validated['pic_user_id'] ?? $lead->assigned_to,
        ]);

        return redirect()->route('admin.surveys.index')
            ->with('success', "Jadwal survei berhasil dibuat untuk konsumen {$lead->name}.");
    }

    /**
     * Update hasil survei atau status pelaksanaan survei.
     */
    public function update(Request $request, Survey $survey)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $survey->pic_user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah survei milik PIC lain.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Terjadwal', 'Selesai', 'Dibatalkan', 'Dijadwal Ulang'])],
            'survey_date' => ['required', 'date'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'result' => ['nullable', 'string', 'max:2000'],
        ]);

        $survey->update($validated);

        // Update status lead jika status survei selesai atau batal
        if ($validated['status'] === 'Selesai') {
            $survey->lead->update(['status' => 'Survei Selesai']);
        } elseif ($validated['status'] === 'Dibatalkan') {
            $survey->lead->update(['status' => 'Batal']);
        } elseif ($validated['status'] === 'Terjadwal' || $validated['status'] === 'Dijadwal Ulang') {
            $survey->lead->update(['status' => 'Survei Terjadwal']);
        }

        return back()->with('success', 'Data survei dan hasil kunjungan berhasil diperbarui.');
    }

    /**
     * Hapus jadwal survei.
     */
    public function destroy(Survey $survey)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $survey->pic_user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus survei milik PIC lain.');
        }

        $survey->delete();
        return back()->with('success', 'Jadwal survei berhasil dihapus.');
    }
}
