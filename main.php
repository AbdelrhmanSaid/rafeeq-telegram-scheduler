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
    foreach ($message['channels'] as $channel) {
        try {
            $options = $message['options'][$channel] ?? [];

            match ($channel) {
                'telegram' => sendTelegramMessage($message, $options),
                'onesignal' => sendOneSignalMessage($message, $options),
                default => throw new Exception('Invalid channel'),
            };

            logMessage(true, sprintf('Message "%s" sent successfully', $key));
        } catch (Exception $e) {
            logMessage(false, sprintf('Message "%s" failed to send, error: %s', $key, $e->getMessage()));
        }
    }
}
