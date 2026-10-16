<?php

namespace App\Http\Controllers;

use App\Models\AvailabilityRequest;
use App\Models\UnitStatusHistory;
use App\Models\User;
use App\Notifications\DeveloperConfirmedAvailabilityNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeveloperConfirmationController extends Controller
{
    /**
     * Tampilkan halaman publik konfirmasi ketersediaan untuk Developer (Berdasarkan Token).
     * Tidak memerlukan login.
     */
    public function show(string $token)
    {
        $availRequest = AvailabilityRequest::with(['unit.photos', 'developer'])
            ->where('token', $token)
            ->firstOrFail();

        $isExpired = $availRequest->isExpired();
        $isConfirmed = $availRequest->isConfirmed();

        return view('public.developer.confirm', compact('availRequest', 'isExpired', 'isConfirmed'));
    }

    /**
     * Submit konfirmasi ketersediaan unit oleh Developer.
     * Mengupdate status unit, audit trail, notifikasi admin, dan update website secara instan.
     */
    public function submit(Request $request, string $token)
    {
        $availRequest = AvailabilityRequest::with(['unit', 'developer'])
            ->where('token', $token)
            ->firstOrFail();

        if ($availRequest->isExpired()) {
            return back()->with('error', 'Masa berlaku tautan konfirmasi ini telah kedaluwarsa. Silakan hubungi tim Wastu Property.');
        }

        if ($availRequest->isConfirmed()) {
            return back()->with('error', 'Permintaan ini sudah dikonfirmasi sebelumnya.');
        }

        $validated = $request->validate([
            'confirmed_unit_status' => ['required', Rule::in(['Tersedia', 'Booking', 'Terjual'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'extended_valid_until' => ['nullable', 'date', 'after:today'],
        ], [
            'confirmed_unit_status.required' => 'Silakan pilih status ketersediaan unit saat ini.',
        ]);

        $unit = $availRequest->unit;
        $oldStatus = $unit->status;
        $newStatus = $validated['confirmed_unit_status'];

        // 1. Update data AvailabilityRequest
        $availRequest->update([
            'status' => 'Terkonfirmasi',
            'confirmed_unit_status' => $newStatus,
            'notes' => $validated['notes'],
            'extended_valid_until' => $validated['extended_valid_until'] ?? null,
            'confirmed_at' => now(),
        ]);

        // 2. Update status unit dan masa berlaku di sistem & website
        $unitUpdateData = ['status' => $newStatus];
        if (!empty($validated['extended_valid_until'])) {
            $unitUpdateData['valid_until'] = $validated['extended_valid_until'];
        }
        $unit->update($unitUpdateData);

        // 3. Catat Riwayat Audit Trail Status Unit (Fase 2A)
        UnitStatusHistory::create([
            'unit_id' => $unit->id,
            'user_id' => $availRequest->requested_by, // Mengaitkan ke admin peminta
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => "Konfirmasi Mandiri oleh Developer ({$availRequest->developer->name}): " . ($validated['notes'] ?? 'Tanpa catatan tambahan.'),
        ]);

        // 4. Kirim Notifikasi In-App Dashboard ke Admin & Superadmin
        $admins = User::where('is_active', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new DeveloperConfirmedAvailabilityNotification($availRequest));
        }

        // 5. Generate teks WhatsApp "Update Stok Unit" siap-salin untuk dibagikan ke tim marketing
        $shareText = "📢 *UPDATE STOK UNIT RESMI DEVELOPER*\n\n"
            . "🏢 *Unit:* {$unit->title} ({$unit->code})\n"
            . "👤 *Developer:* {$availRequest->developer->company_name} ({$availRequest->developer->name})\n"
            . "🔄 *Status Baru:* {$newStatus}\n"
            . ($validated['notes'] ? "📝 *Catatan:* {$validated['notes']}\n" : "")
            . "🕒 *Waktu Update:* " . now()->translatedFormat('d F Y - H:i') . " WIB\n\n"
            . "Status di sistem CRM dan website publik telah tersinkronisasi otomatis.";

        return back()->with('confirmed_success', [
            'status' => $newStatus,
            'notes' => $validated['notes'],
            'share_text' => $shareText,
        ]);
    }
}
