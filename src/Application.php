<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler;

use Rafeeq\Scheduler\Cache\JsonFileCache;
use Rafeeq\Scheduler\Channel\ChannelRegistry;
use Rafeeq\Scheduler\Channel\OneSignalChannel;
use Rafeeq\Scheduler\Channel\TelegramChannel;
use Rafeeq\Scheduler\Http\CurlHttpClient;
use Rafeeq\Scheduler\Logging\FileLogger;
use Rafeeq\Scheduler\Message\Context;
use Rafeeq\Scheduler\Message\MessageLoader;
use Rafeeq\Scheduler\Prayer\PrayerTimes;
use Rafeeq\Scheduler\Support\Clock;
use Rafeeq\Scheduler\Support\Config;

/**
 * Composition root: wires the whole object graph from configuration.
 *
 * This is the one place that knows how the pieces fit together. Keeping the
 * wiring here (rather than in a heavyweight container) keeps the rest of the
 * code free of bootstrapping concerns while staying easy to follow.
 */
final class Application
{
    private function __construct()
    {
    }

    /**
     * Build a ready-to-run scheduler from a config file.
     */
    public static function boot(?string $configPath = null): Scheduler
    {
        $root = dirname(__DIR__);
        $config = Config::fromFile($configPath ?? $root . '/config.php');

        date_default_timezone_set($config->get('timezone', 'Africa/Cairo'));

        $clock = Clock::now();
        $http = new CurlHttpClient();

        $prayer = new PrayerTimes(
            http: $http,
            cache: new JsonFileCache($config->get('cache_path', $root . '/cache')),
            clock: $clock,
            city: $config->get('prayer.city', 'Cairo'),
            country: $config->get('prayer.country', 'EG'),
        );

        $channels = new ChannelRegistry([
            new TelegramChannel($http, $config->require('bot_token'), $config->require('chat_id')),
            new OneSignalChannel($http, $config->require('onesignal_app_id'), $config->require('onesignal_rest_api_key')),
        ]);

        return new Scheduler(
            messages: new MessageLoader($config->get('messages_path', $root . '/messages')),
            channels: $channels,
            logger: new FileLogger($config->get('log_file', $root . '/logs.txt')),
            context: new Context($clock, $prayer),
        );
    }
}
