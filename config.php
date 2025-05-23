<?php

$messages = glob(__DIR__ . '/messages/*.html');
$messages = array_map(function ($message) {
    return basename($message, '.html');
}, $messages);

$schedule = [];
foreach ($messages as $message) {
    $time = str_replace(['-', '.html'], '', $message);
    $schedule[$time] = file_get_contents(__DIR__ . "/messages/{$message}.html");
}

return [
    'bot_token' => 'YOUR_BOT_TOKEN_HERE',
    'chat_id' => 'YOUR_CHAT_ID_HERE',
    'schedule' => $schedule,
];
