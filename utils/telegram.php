<?php

/**
 * Send a message to a Telegram chat
 *
 * @param string $message The message to send
 * @param array $options The options to send
 * @return array The result of the message send
 */
function sendTelegramMessage(string $message, array $options = []): array
{
    global $config;

    $chatId = $config['chat_id'];
    $botToken = $config['bot_token'];

    $url = "https://api.telegram.org/bot$botToken/sendMessage";

    $data = array_merge($options, [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]);

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'method' => 'POST',
            'content' => http_build_query($data)
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return json_decode($result, true);
}