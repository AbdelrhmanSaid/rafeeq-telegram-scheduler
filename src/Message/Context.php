<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Message;

use Rafeeq\Scheduler\Prayer\PrayerTimes;
use Rafeeq\Scheduler\Support\Clock;

/**
 * The dependencies handed to a message's `due` closure.
 *
 * Message definitions stay declarative and testable: instead of reaching for
 * globals, a `due` callback receives the frozen clock and the prayer-times
 * service it needs.
 */
final class Context
{
    public function __construct(
        public readonly Clock $clock,
        public readonly PrayerTimes $prayer,
    ) {
    }
}
