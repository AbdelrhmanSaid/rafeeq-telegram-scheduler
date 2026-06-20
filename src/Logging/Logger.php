<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Logging;

/**
 * Records the outcome of message delivery attempts.
 */
interface Logger
{
    /**
     * Record a successful delivery.
     */
    public function success(string $message): void;

    /**
     * Record a failed delivery.
     */
    public function failure(string $message): void;
}
