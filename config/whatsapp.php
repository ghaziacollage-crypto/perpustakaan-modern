<?php

declare(strict_types=1);

return [
    'config_path' => env('WHATSAPP_CONFIG_PATH', base_path('guest_watoken9132a5eeb5a05-config.json')),
    'base_url' => env('WHATSAPP_API_BASE_URL'),
    'api_token' => env('WHATSAPP_API_TOKEN'),
    'session_id' => env('WHATSAPP_SESSION_ID'),
    'sender' => env('WHATSAPP_SENDER'),
    'send_message_path' => env('WHATSAPP_SEND_MESSAGE_PATH', '/send-message'),
    'health_paths' => array_filter(array_map('trim', explode(',', env('WHATSAPP_HEALTH_PATHS', '/health,/status')))),
];
