# Rafeeq Telegram Scheduler

A PHP-based Telegram message scheduler that sends messages based on custom callable conditions.

## Project Structure

```
rafeeq-telegram-scheduler/
├── messages/           # Directory containing PHP message files
│   ├── morning.php     # Morning reminder message
│   ├── evening.php     # Evening reminder message
│   └── friday.php      # Friday special message
├── config.php          # Configuration file
├── main.php           # Main execution script
└── README.md          # This file
```

## Message Files Structure

Each message file in the `messages/` directory should be a PHP file that returns an array with the following structure:

```php
<?php

return [
    'due' => function() {
        // Callable function that returns true when message should be sent
        // You can use any PHP logic here (time, date, conditions, etc.)
        return condition_check();
    },
    'message' => "Your message content here"
];
```

### Examples

#### Time-based Message
```php
<?php

return [
    'due' => function() {
        $currentTime = date('H:i');
        return $currentTime === '09:00'; // Send at 9:00 AM
    },
    'message' => "Good morning reminder!"
];
```

#### Day-specific Message
```php
<?php

return [
    'due' => function() {
        $currentDay = date('l');
        $currentTime = date('H:i');
        
        // Send on Friday at noon
        return $currentDay === 'Friday' && $currentTime === '12:00';
    },
    'message' => "Friday special message!"
];
```

#### Complex Conditions
```php
<?php

return [
    'due' => function() {
        $hour = (int)date('H');
        $dayOfWeek = date('N'); // 1 (Monday) to 7 (Sunday)
        
        // Send on weekdays between 9 AM and 5 PM
        return ($dayOfWeek >= 1 && $dayOfWeek <= 5) && 
               ($hour >= 9 && $hour <= 17);
    },
    'message' => "Weekday work hours reminder!"
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