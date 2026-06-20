<?php

declare(strict_types=1);

return [
    // Telegram configuration
    'bot_token' => 'YOUR_BOT_TOKEN_HERE',
    'chat_id' => 'YOUR_CHAT_ID_HERE',

    // OneSignal configuration
    'onesignal_app_id' => 'YOUR_ONESIGNAL_APP_ID_HERE',
    'onesignal_rest_api_key' => 'YOUR_ONESIGNAL_REST_API_KEY_HERE',

    // Timezone used for all scheduling decisions
    'timezone' => 'Africa/Cairo',

    // Location used to look up prayer times
    'prayer' => [
        'city' => 'Cairo',
        'country' => 'EG',
    ],
];
