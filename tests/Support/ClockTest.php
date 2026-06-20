<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Support;

use PHPUnit\Framework\TestCase;
use Rafeeq\Scheduler\Support\Clock;

final class ClockTest extends TestCase
{
    public function test_it_formats_its_frozen_timestamp(): void
    {
        date_default_timezone_set('UTC');

        // 2026-06-20 09:30:00 UTC
        $clock = new Clock(1_781_947_800);

        $this->assertSame('2026-06-20 09:30', $clock->format('Y-m-d H:i'));
    }

    public function test_it_exposes_the_frozen_timestamp(): void
    {
        $clock = new Clock(123_456);

        $this->assertSame(123_456, $clock->timestamp());
    }
}
