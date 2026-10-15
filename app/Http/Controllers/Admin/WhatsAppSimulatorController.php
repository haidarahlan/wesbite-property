<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\WhatsAppGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsAppSimulatorController extends Controller
{
    /**
     * Tampilkan halaman simulator WhatsApp.
     */
    public function index()
    {
        $messages = WhatsAppMessage::latest()->paginate(15);
        $provider = config('services.whatsapp.default_provider', 'fake');

        return view('admin.whatsapp.simulator', compact('messages', 'provider'));
    }

    /**
     * Kirim simulasi pesan masuk palsu (Inbound Message).
     */
    public function simulateInbound(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $messageId = 'sim_' . Str::random(12);

        $msg = WhatsAppMessage::create([
            'message_id' => $messageId,
            'direction' => 'inbound',
            'phone_number' => $phone,
            'message_body' => $request->message,
            'status' => 'received',
            'gateway_provider' => 'fake',
            'payload' => [
                'simulated_by' => auth()->user()->name ?? 'Admin',
                'raw_input' => $request->all(),
            ],
        ]);

        // Proses Klasifikasi Otomatis (Sub-Fase 3B)
        $classifier = app(\App\Services\MessageClassifierService::class);
        $result = $classifier->processIncomingMessage($msg);

        $notif = "Pesan masuk diproses! Klasifikasi: [{$result['classification']}].";
        if ($result['classification'] === 'Tertarik') {
            $notif .= " Lead {$result['lead']->reference_code} berhasil dibuat/diperbarui di CRM!";
        } else {
            $notif .= " Auto-reply FAQ berhasil dikirimkan ke konsumen!";
        }

        return back()->with('success', $notif);
    }

    /**
     * Uji kirim pesan keluar (Outbound Message) via Gateway aktif.
     */
    public function testSend(Request $request)
    {
        $request->validate([
            'to' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        $gateway = WhatsAppGatewayManager::create();
        $result = $gateway->send($request->to, $request->message);

        // Catat ke whatsapp_messages
        WhatsAppMessage::create([
            'message_id' => $result['message_id'] ?? ('out_' . Str::random(12)),
            'direction' => 'outbound',
            'phone_number' => preg_replace('/[^0-9]/', '', $request->to),
            'message_body' => $request->message,
            'status' => $result['success'] ? 'sent' : 'failed',
            'gateway_provider' => $result['provider'] ?? 'fake',
            'payload' => $result,
        ]);

        if ($result['success']) {
            return back()->with('success', "Pesan keluar berhasil dikirim ke {$request->to} via Gateway [{$result['provider']}]!");
        }

        return back()->with('error', "Gagal mengirim pesan: " . ($result['error'] ?? 'Unknown error'));
    }
}
