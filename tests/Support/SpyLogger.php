<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Support;

use Rafeeq\Scheduler\Logging\Logger;

/**
 * A {@see Logger} that records what it was told, for assertions.
 */
final class SpyLogger implements Logger
{
    /** @var list<string> */
    public array $successes = [];

    /** @var list<string> */
    public array $failures = [];

    public function success(string $message): void
    {
        $this->successes[] = $message;
    }

    public function failure(string $message): void
    {
        $this->failures[] = $message;
    }
}
