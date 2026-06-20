<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Support;

use Rafeeq\Scheduler\Cache\Cache;

/**
 * A throwaway in-memory {@see Cache} for tests.
 */
final class ArrayCache implements Cache
{
    /** @param array<string, mixed> $items */
    public function __construct(private array $items = [])
    {
    }

    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function put(string $key, mixed $value): void
    {
        $this->items[$key] = $value;
    }
}
