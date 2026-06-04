<?php

/**
 * Send a message to a Telegram chat
 *
 * @param string $message The message to send
 * @param array $options The options to send
 * @return array The result of the message send
 * @throws RuntimeException If the request fails or Telegram returns an error
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

    $httpOptions = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'method' => 'POST',
            'content' => http_build_query($data)
        ],
    ];

    $context = stream_context_create($httpOptions);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        $error = error_get_last();
        $message = $error['message'] ?? 'Unknown HTTP error';
        throw new RuntimeException("Failed to send Telegram message: $message");
    }

    $result = json_decode($response, true);

    if (!is_array($result)) {
        throw new RuntimeException('Failed to send Telegram message: invalid JSON response');
    }

    if (empty($result['ok'])) {
        $code = $result['error_code'] ?? 'unknown';
        $description = $result['description'] ?? 'unknown error';
        throw new RuntimeException("Telegram API error ($code): $description");
    }

    return $result;
}