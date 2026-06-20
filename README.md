# Rafeeq Telegram Scheduler

A small, extensible PHP scheduler that sends messages to **Telegram** and **OneSignal**
based on custom "due" conditions — for example, *"15 minutes after Fajr"* or
*"every Friday at noon"*.

## Architecture

The project follows a layered, dependency-injected design. Nothing reaches for
global state; every collaborator is small, focused, and built around an
interface so it can be swapped or tested in isolation.

```
bin/scheduler          Executable entry point (php bin/scheduler)
main.php               Backwards-compatible shim → bin/scheduler
config.php             Your secrets & location (copied from config.example.php)
messages/              One file per scheduled message (declarative definitions)
src/
  Application.php      Composition root — wires the whole object graph
  Scheduler.php        Orchestrator — loads, filters by "due", dispatches, logs
  Support/
    Clock.php          A frozen point in time (no more global $timestamp)
    Config.php         Read-only config with dot-notation access
  Http/
    HttpClient.php     Transport interface
    CurlHttpClient.php cURL implementation
    HttpResponse.php   Immutable status + body
  Cache/               Cache interface + JsonFileCache
  Logging/             Logger interface + FileLogger / NullLogger
  Prayer/
    PrayerTimes.php    Prayer-time lookups (AlAdhan API), cached per day
  Message/
    Message.php        Immutable message value object
    MessageLoader.php  Builds messages from the messages/ directory
    Context.php        Dependencies handed to a message's "due" closure
  Channel/
    Channel.php        Delivery-channel interface
    ChannelRegistry.php Name → channel lookup
    TelegramChannel.php
    OneSignalChannel.php
tests/                 PHPUnit unit tests
```

### Why it's easy to extend

- **Add a channel** (e.g. SMS, email): implement `Channel`, then register it in
  `Application::boot()`. The scheduler needs no changes.
- **Add a message**: drop a new file into `messages/`. No code changes.
- **Swap an integration**: every dependency (HTTP, cache, logger) is an
  interface, so alternatives slot in without touching call sites.

## Installation

```bash
composer install
cp config.example.php config.php   # then fill in your credentials
```

## Configuration

`config.php` returns a plain array:

```php
return [
    'bot_token' => '...',
    'chat_id' => '...',
    'onesignal_app_id' => '...',
    'onesignal_rest_api_key' => '...',
    'timezone' => 'Africa/Cairo',
    'prayer' => ['city' => 'Cairo', 'country' => 'EG'],
];
```

## Message files

Each file in `messages/` returns a definition array. The `due` closure receives a
`Context` exposing the frozen `Clock` and the `PrayerTimes` service:

```php
<?php

use Rafeeq\Scheduler\Message\Context;

return [
    'channels' => ['telegram', 'onesignal'],
    'title' => 'Title shown in bold',
    'message' => 'Body content (HTML supported on Telegram)',
    'url' => 'https://example.com',          // optional — adds an inline button
    'options' => [                            // optional — per-channel API options
        'telegram' => ['disable_notification' => true],
    ],
    'due' => fn (Context $context) =>
        $context->clock->format('l') === 'Friday'
        && $context->clock->format('h:i a') === '12:00 pm',
];
```

## Usage

```bash
php bin/scheduler      # or: composer schedule, or: php main.php
```

The scheduler will:

1. Load every message definition from `messages/`.
2. Evaluate each message's `due` closure against the frozen clock.
3. Deliver due messages to each of their channels.
4. Append the outcome of every attempt to `logs.txt`.

Run it once a minute via cron:

```cron
* * * * * php /path/to/rafeeq-telegram-scheduler/bin/scheduler
```

## Tests

```bash
composer test          # or: vendor/bin/phpunit
```

## Features

- **Flexible scheduling** — any PHP logic decides when a message is due.
- **Pluggable channels** — Telegram and OneSignal out of the box; add more by
  implementing a single interface.
- **Prayer-time aware** — schedule relative to prayer times, cached per day.
- **Resilient delivery** — a failure on one channel never blocks the rest.
- **Logging** — every attempt is recorded with a timestamp.
- **Tested** — the decoupled design is covered by a PHPUnit suite.
