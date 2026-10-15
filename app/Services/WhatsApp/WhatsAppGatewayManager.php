<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use App\Models\SiteSetting;

class WhatsAppGatewayManager
{
    /**
     * Resolve instance gateway berdasarkan site_settings atau config services.
     */
    public static function create(?string $provider = null): WhatsAppGateway
    {
        if (!$provider) {
            $provider = SiteSetting::get('wa_gateway_provider', config('services.whatsapp.default_provider', 'fake'));
        }

        return match (strtolower($provider)) {
            'fonnte' => new FonnteGateway(),
            'waha' => new WahaGateway(),
            default => new FakeGateway(),
        };
    }
}
