<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Support;

use Rafeeq\Scheduler\Message\Context;
use Rafeeq\Scheduler\Prayer\PrayerTimes;
use Rafeeq\Scheduler\Support\Clock;

/**
 * Convenience builders for wiring up collaborators in tests.
 */
final class Factory
{
    public static function clock(int $timestamp = 1_781_077_800): Clock
    {
        return new Clock($timestamp);
    }

    public static function context(?Clock $clock = null, ?PrayerTimes $prayer = null): Context
    {
        $clock ??= self::clock();

        return new Context($clock, $prayer ?? self::prayerTimes($clock));
    }

    public static function prayerTimes(?Clock $clock = null): PrayerTimes
    {
        $clock ??= self::clock();

        return new PrayerTimes(new FakeHttpClient(), new ArrayCache(), $clock);
    }
}
