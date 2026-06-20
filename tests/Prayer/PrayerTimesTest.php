<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Prayer;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Rafeeq\Scheduler\Http\HttpResponse;
use Rafeeq\Scheduler\Prayer\PrayerTimes;
use Rafeeq\Scheduler\Tests\Support\ArrayCache;
use Rafeeq\Scheduler\Tests\Support\Factory;
use Rafeeq\Scheduler\Tests\Support\FakeHttpClient;

final class PrayerTimesTest extends TestCase
{
    public function test_it_fetches_and_caches_timings_for_the_day(): void
    {
        $clock = Factory::clock();
        $http = (new FakeHttpClient())->queue(new HttpResponse(200, json_encode([
            'data' => ['timings' => ['Fajr' => '03:00', 'Maghrib' => '19:45']],
        ])));
        $cache = new ArrayCache();

        $prayer = new PrayerTimes($http, $cache, $clock);

        $this->assertSame('03:00', $prayer->time('Fajr'));
        // A second lookup must hit the cache, not the network.
        $this->assertSame('19:45', $prayer->time('Maghrib'));
        $this->assertCount(1, $http->requests);
    }

    public function test_it_serves_a_same_day_cache_without_calling_the_api(): void
    {
        $clock = Factory::clock();
        $cache = new ArrayCache([
            'prayer-times' => ['date' => $clock->format('d-m-Y'), 'timings' => ['Fajr' => '04:15']],
        ]);

        $prayer = new PrayerTimes(new FakeHttpClient(), $cache, $clock);

        $this->assertSame('04:15', $prayer->time('Fajr'));
    }

    public function test_it_rejects_unknown_prayers(): void
    {
        $cache = new ArrayCache([
            'prayer-times' => ['date' => Factory::clock()->format('d-m-Y'), 'timings' => ['Fajr' => '04:15']],
        ]);

        $this->expectException(InvalidArgumentException::class);

        (new PrayerTimes(new FakeHttpClient(), $cache, Factory::clock()))->time('Nope');
    }
}
