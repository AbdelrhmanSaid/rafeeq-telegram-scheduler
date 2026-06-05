<?php

$messageFiles = glob(__DIR__ . '/messages/*.php');
$messages = [];

foreach ($messageFiles as $file) {
    $messageConfig = require $file;

    $key = basename($file, '.php');
    $messages[$key] = $messageConfig;
}

return $messages;
