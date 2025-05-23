# Rafeeq Telegram Scheduler

A PHP-based Telegram message scheduler that sends messages based on custom callable conditions.

## Message Files Structure

Each message file in the `messages/` directory should be a PHP file that returns an array with the following structure:

```php
<?php

return [
    'due' => fn () => true,
    'message' => 'Your message content here'
];
```

## Configuration

1. Copy your bot token and chat ID to `config.php`
2. Add your message files to the `messages/` directory
3. Run `php main.php` to check and send due messages

## Usage

```bash
php main.php
```

The script will:
1. Load all PHP files from the `messages/` directory
2. Execute each message's `due` callable function
3. Send messages where the `due` function returns `true`
4. Log sent messages to `logs.txt`

## Features

- **Flexible Scheduling**: Use any PHP logic for message timing
- **Multiple Messages**: Can send multiple messages if multiple conditions are met
- **Logging**: All sent messages are logged with timestamps
- **HTML Support**: Messages support HTML formatting for Telegram
- **Easy Extension**: Simply add new PHP files to add more scheduled messages 