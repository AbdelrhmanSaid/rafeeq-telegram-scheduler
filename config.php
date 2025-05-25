<?php

// Load all PHP message files
$messageFiles = glob(__DIR__ . '/messages/*.php');
$messages = [];

foreach ($messageFiles as $file) {
    $messageConfig = require $file;

    $key = basename($file, '.php');
    $messages[$key] = $messageConfig;
}

return [
    'bot_token' => 'YOUR_BOT_TOKEN_HERE',
    'chat_id' => 'YOUR_CHAT_ID_HERE',
    'messages' => $messages,
];
