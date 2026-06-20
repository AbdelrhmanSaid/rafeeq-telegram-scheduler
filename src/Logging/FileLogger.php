<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Logging;

/**
 * Appends timestamped delivery outcomes to a log file.
 */
final class FileLogger implements Logger
{
    public function __construct(private readonly string $file)
    {
    }

    public function success(string $message): void
    {
        $this->write('Success', $message);
    }

    public function failure(string $message): void
    {
        $this->write('Failed', $message);
    }

    private function write(string $status, string $message): void
    {
        $line = sprintf('[%s] Status: %s, Message: %s', date('Y-m-d H:i:s'), $status, $message);

        file_put_contents($this->file, $line . "\n", FILE_APPEND);
    }
}
