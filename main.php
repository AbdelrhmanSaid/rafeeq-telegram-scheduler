<?php

// Set the max execution time and memory limit to unlimited
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

// Set the default timezone to Cairo
date_default_timezone_set('Africa/Cairo');

// Persist the timestamp to make sure all the functions are relative to the current
// time, so long-running scripts are not affected by the time difference
$timestamp = time();

// Load the config and messages
$config = require_once __DIR__ . '/config.php';
$messages = require_once __DIR__ . '/messages.php';

// Load all utils files
foreach (glob(__DIR__ . '/utils/*.php') as $file) {
    require_once $file;
}

// Get all messages that should be sent
$messagesToSend = [];
foreach ($messages as $key => $message) {
    if (isset($message['due']) && is_callable($message['due'])) {
        $dueFunction = $message['due'];

        // Call the due function to check if message should be sent
        if ($dueFunction($timestamp)) {
            $messagesToSend[$key] = $message;
        }
    }
}

// Send all messages that should be sent
foreach ($messagesToSend as $key => $message) {
    $channels = is_array($message['channels']) ? $message['channels'] : [$message['channels']];

    foreach ($channels as $channel) {
        try {
            // Check if the definition has "before" callable function
            if (isset($message['before']) && is_callable($message['before'])) {
                call_user_func($message['before']);
            }

            // Send the message
            match ($channel) {
                'telegram' => sendTelegramMessage($message['title'], $message['message']),
                'onesignal' => sendOneSignalMessage($message['title'], $message['message']),
                default => throw new Exception('Invalid channel'),
            };

            // Check if the definition has "after" callable function
            if (isset($message['after']) && is_callable($message['after'])) {
                call_user_func($message['after']);
            }

            logMessage(true, sprintf('Message "%s" sent successfully', $key));
        } catch (Exception $e) {
            logMessage(false, sprintf('Message "%s" failed to send, error: %s', $key, $e->getMessage()));
        }
    }
}
