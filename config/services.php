<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // WhatsApp Gateway Services (Fase 3A)
    'whatsapp' => [
        'default_provider' => env('WA_GATEWAY_PROVIDER', 'fake'), // fake, fonnte, waha
        'webhook_secret' => env('WA_WEBHOOK_SECRET', 'wastu-secret-token-2026'),
        'fonnte' => [
            'url' => env('FONNTE_URL', 'https://api.fonnte.com'),
            'token' => env('FONNTE_TOKEN', ''),
        ],
        'waha' => [
            'url' => env('WAHA_URL', 'http://localhost:3000'),
            'session' => env('WAHA_SESSION', 'default'),
        ],
    ],

];
