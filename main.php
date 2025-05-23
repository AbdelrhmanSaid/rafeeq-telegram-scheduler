<?php

date_default_timezone_set('Africa/Cairo');

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
 * Check if any message should be sent based on their due callable functions
 *
 * @param array $messages Array of message configurations with 'due' callable and 'message' properties
 * @return array|false Array with messages to send or false if no messages should be sent
 */
function getMessagesToSend($messages)
{
    $messagesToSend = [];

    foreach ($messages as $messageConfig) {
        if (isset($messageConfig['due']) && is_callable($messageConfig['due'])) {
            $dueFunction = $messageConfig['due'];
            
            // Call the due function to check if message should be sent
            if ($dueFunction()) {
                $messagesToSend[] = $messageConfig['message'];
            }
        }
    }

    return empty($messagesToSend) ? false : $messagesToSend;
}

$messagesToSend = getMessagesToSend($config['messages']);

if ($messagesToSend) {
    foreach ($messagesToSend as $message) {
        $result = sendTelegramMessage(
            $config['bot_token'],
            $config['chat_id'],
            $message
        );

        file_put_contents(
            __DIR__ . '/logs.txt',
            date('Y-m-d H:i:s') . " - Message sent: " . substr($message, 0, 50) . "..., Result: {$result}\n",
            FILE_APPEND
        );

        echo "Message sent successfully!\n";
    }
} else {
    echo "No messages due for current time.\n";
}
