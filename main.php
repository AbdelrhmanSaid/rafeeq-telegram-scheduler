<?php

$config = require_once __DIR__ . '/config.php';

/**
 * Send a message to a Telegram chat
 *
 * @param string $botToken The bot token
 * @param string $chatId The chat ID
 * @param string $message The message to send
 * @return string The result of the message send
 */
function sendTelegramMessage($botToken, $chatId, $message)
{
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'method' => 'POST',
            'content' => http_build_query($data)
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}

/**
 * Check if a message should be sent based on the current time
 *
 * @param array $schedule The schedule of messages to send
 * @return string|false The message to send or false if no message should be sent
 */
function shouldSendMessage($schedule)
{
    $currentTime = strtolower(date('h-i-a'));

    foreach ($schedule as $time => $message) {
        if ($time === $currentTime) {
            return $message;
        }
    }

    return false;
}

$messageToSend = shouldSendMessage($config['schedule']);

if ($messageToSend) {
    $result = sendTelegramMessage(
        $config['bot_token'],
        $config['chat_id'],
        $messageToSend
    );

    file_put_contents(
        __DIR__ . '/logs.txt',
        date('Y-m-d H:i:s') . " - Message sent: {$messageToSend}, Result: {$result}\n",
        FILE_APPEND
    );

    echo "Message sent successfully!\n";
} else {
    echo "No messages scheduled for current time.\n";
}
