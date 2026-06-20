<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Cache;

/**
 * A minimal key/value cache contract.
 *
 * Implementations let us swap storage (file, memory, …) without touching the
 * services that depend on caching.
 */
interface Cache
{
    /**
     * Fetch a cached value, or null when the key is unknown.
     */
    public function get(string $key): mixed;

    /**
     * Store a value under the given key.
     */
    public function put(string $key, mixed $value): void;
}
