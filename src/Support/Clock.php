<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Support;

/**
 * A frozen point in time.
 *
 * The scheduler captures the current timestamp once at boot and reuses it for
 * every "is this message due?" check, so a slow run never straddles a minute
 * boundary and sends (or skips) a message by accident.
 */
final class Clock
{
    public function __construct(private readonly int $timestamp)
    {
    }

    /**
     * Create a clock frozen at the current time.
     */
    public static function now(): self
    {
        return new self(time());
    }

    /**
     * The frozen timestamp this clock represents.
     */
    public function timestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * Format the frozen time (or an arbitrary timestamp) using date() syntax.
     */
    public function format(string $format, ?int $timestamp = null): string
    {
        return date($format, $timestamp ?? $this->timestamp);
    }
}
