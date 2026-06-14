<?php

declare(strict_types=1);

return [
    'base_url' => env('WHATSAPP_API_BASE_URL'),
    'api_token' => env('WHATSAPP_API_TOKEN'),
    'session_id' => env('WHATSAPP_SESSION_ID'),
    'sender' => env('WHATSAPP_SENDER'),
    'is_active' => env('WHATSAPP_IS_ACTIVE', true),
    'reminder_days' => env('WHATSAPP_REMINDER_DAYS', 1),
    'send_message_path' => env('WHATSAPP_SEND_MESSAGE_PATH', '/send-message'),
    'health_paths' => array_filter(array_map('trim', explode(',', env('WHATSAPP_HEALTH_PATHS', '/health,/status')))),
];
