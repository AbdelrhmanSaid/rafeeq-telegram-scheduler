<?php

/**
 * Log a message to the log file
 *
 * @param bool $success
 * @param string $message
 * @return void
 */
function logMessage(bool $success, string $message): void {
    $file = dirname(__DIR__) . '/logs.txt';

    $logMessage = sprintf('[%s] Status: %s, Message: %s', date('Y-m-d H:i:s'), $success ? 'Success' : 'Failed', $message);
    file_put_contents($file, $logMessage . "\n", FILE_APPEND);
}