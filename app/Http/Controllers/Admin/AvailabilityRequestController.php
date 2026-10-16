<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityRequest;
use App\Models\SiteSetting;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AvailabilityRequestController extends Controller
{
    /**
     * Admin membuat permintaan cek ketersediaan stok ke developer.
     * Menghasilkan token rahasia & link publik (berlaku 48 jam) serta teks siap salin/kirim WhatsApp.
     */
    public function store(Request $request, Unit $unit)
    {
        if (!$unit->developer) {
            return back()->with('error', 'Unit ini belum memiliki developer terkait. Silakan tautkan developer terlebih dahulu.');
        }

        $token = Str::random(48);
        $expiresAt = Carbon::now()->addHours(48);

        $availRequest = AvailabilityRequest::create([
            'unit_id' => $unit->id,
            'developer_id' => $unit->developer_id,
            'requested_by' => auth()->id(),
            'token' => $token,
            'current_unit_status' => $unit->status,
            'status' => 'Menunggu',
            'expires_at' => $expiresAt,
        ]);

        $confirmUrl = route('public.developer.confirm', $token);
        $devPhone = $unit->developer->phone;
        $devName = $unit->developer->name;

        // Teks WhatsApp Cek Stok Siap Kirim
        $waMessage = "Yth. {$devName} ({$unit->developer->company_name}),\n\n"
            . "Kami dari Manajemen PT Wastu Pratama Wijaya ingin melakukan pengecekan ketersediaan / update status unit listing:\n"
            . "🏢 *Properti:* {$unit->title}\n"
            . "🏷️ *Kode Unit:* {$unit->code}\n"
            . "📍 *Status Terakhir:* {$unit->status}\n\n"
            . "Mohon kesediaan Bapak/Ibu untuk mengonfirmasi ketersediaan terkini melalui tautan berikut (berlaku 48 jam):\n"
            . "👉 {$confirmUrl}\n\n"
            . "Terima kasih atas kerja sama yang baik.";

        $waLink = "https://wa.me/{$devPhone}?text=" . rawurlencode($waMessage);

        return back()->with('availability_success', [
            'token' => $token,
            'confirm_url' => $confirmUrl,
            'wa_link' => $waLink,
            'wa_message' => $waMessage,
            'dev_name' => $devName,
            'dev_phone' => $devPhone,
        ]);
    }
}
