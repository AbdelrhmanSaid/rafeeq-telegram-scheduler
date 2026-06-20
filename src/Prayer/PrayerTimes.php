<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Prayer;

use InvalidArgumentException;
use Rafeeq\Scheduler\Cache\Cache;
use Rafeeq\Scheduler\Http\HttpClient;
use Rafeeq\Scheduler\Support\Clock;
use RuntimeException;

/**
 * Fetches daily prayer times from the AlAdhan API, cached per day.
 */
final class PrayerTimes
{
    private const CACHE_KEY = 'prayer-times';

    public function __construct(
        private readonly HttpClient $http,
        private readonly Cache $cache,
        private readonly Clock $clock,
        private readonly string $city = 'Cairo',
        private readonly string $country = 'EG',
    ) {
    }

    /**
     * Return the time for a single prayer (e.g. "Fajr", "Maghrib").
     *
     * @throws InvalidArgumentException If the prayer name is unknown.
     */
    public function time(string $prayer): string
    {
        $timings = $this->timings();

        if (!isset($timings[$prayer])) {
            throw new InvalidArgumentException(sprintf('Unknown prayer: %s', $prayer));
        }

        return $timings[$prayer];
    }

    /**
     * Return all prayer timings for the given date (defaults to today).
     *
     * Results are cached for the day to avoid repeated API calls.
     *
     * @return array<string, string>
     *
     * @throws RuntimeException If the API response cannot be parsed.
     */
    public function timings(?string $date = null): array
    {
        $date ??= $this->clock->format('d-m-Y');

        $cached = $this->cache->get(self::CACHE_KEY);
        if (is_array($cached) && ($cached['date'] ?? null) === $date && !empty($cached['timings'])) {
            return $cached['timings'];
        }

        $timings = $this->fetch($date);

        $this->cache->put(self::CACHE_KEY, ['date' => $date, 'timings' => $timings]);

        return $timings;
    }

    /**
     * @return array<string, string>
     *
     * @throws RuntimeException
     */
    private function fetch(string $date): array
    {
        $url = sprintf(
            'https://api.aladhan.com/v1/timingsByCity/%s?city=%s&country=%s',
            rawurlencode($date),
            rawurlencode($this->city),
            rawurlencode($this->country),
        );

        $timings = $this->http->get($url)->json()['data']['timings'] ?? null;

        if (!is_array($timings)) {
            throw new RuntimeException('Failed to get prayer times from the API.');
        }

        return $timings;
    }
}
