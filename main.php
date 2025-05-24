<?php

// Set the max execution time and memory limit to unlimited
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

// Set the default timezone to Cairo
date_default_timezone_set('Africa/Cairo');

// Persist the timestamp to make sure all the functions are relative to the current
// time, so long-running scripts are not affected by the time difference
$timetamp = time();

// Load the config file
$config = require_once __DIR__ . '/config.php';

// Load all utils files
foreach (glob(__DIR__ . '/utils/*.php') as $file) {
    require_once $file;
}

// Get all messages that should be sent
$messagesToSend = [];
foreach ($config['messages'] as $key => $message) {
    if (isset($message['due']) && is_callable($message['due'])) {
        $dueFunction = $message['due'];

        // Call the due function to check if message should be sent
        if ($dueFunction($timetamp)) {
            $messagesToSend[$key] = $message;
        }
    }
}

// Send all messages that should be sent
foreach ($messagesToSend as $key => $message) {
    // Check if the defintion has "before" callable function
    if (isset($message['before']) && is_callable($message['before'])) {
        call_user_func($message['before']);
    }

    // Send the message
    $result = sendTelegramMessage($message['message']);

    // Check if the defintion has "after" callable function
    if (isset($message['after']) && is_callable($message['after'])) {
        call_user_func($message['after']);
    }

    // Prepare the log message
    $status = $result['ok'] ? 'Success' : 'Failed';
    $error = $result['error_code'] ?? 'N/A';
    $logMessage = sprintf('[%s] Message: %s, Status: %s, Error: %s', date('Y-m-d H:i:s'), $key, $status, $error);

    // Log the result
    file_put_contents(__DIR__ . '/logs.txt', $logMessage . "\n", FILE_APPEND);
}
