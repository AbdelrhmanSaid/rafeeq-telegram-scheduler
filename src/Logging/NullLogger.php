<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Logging;

/**
 * A logger that discards everything — handy for tests and dry runs.
 */
final class NullLogger implements Logger
{
    public function success(string $message): void
    {
    }

    public function failure(string $message): void
    {
    }
}
