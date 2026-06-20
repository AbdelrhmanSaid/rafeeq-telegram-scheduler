<?php

/**
 * Send a message to a Telegram chat
 *
 * @param array $message The message to send
 * @param array $options The options to send
 * @return array The result of the message send
 * @throws RuntimeException If the request fails or Telegram returns an error
 */
function sendTelegramMessage(array $message, array $options = []): array
{
    global $config;

    $chatId = $config['chat_id'];
    $botToken = $config['bot_token'];

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $text = sprintf('%s%s%s', $message['title'], PHP_EOL . PHP_EOL, $message['message']);

    $data = array_merge($options, [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML'
    ]);

    // Add the url to the data if it exists
    if (isset($message['url'])) {
        $data['reply_markup'] = json_encode([
            'inline_keyboard' => [
                [['text' => 'من هنا', 'url' => $message['url']]],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        throw new RuntimeException("Failed to send Telegram message: $err");
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
