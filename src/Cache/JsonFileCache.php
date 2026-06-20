<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Cache;

use RuntimeException;

/**
 * Persists each cache key as a JSON file inside a directory.
 */
final class JsonFileCache implements Cache
{
    public function __construct(private readonly string $directory)
    {
    }

    public function get(string $key): mixed
    {
        $path = $this->pathFor($key);

        if (!is_file($path)) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return $decoded === null ? null : $decoded;
    }

    public function put(string $key, mixed $value): void
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new RuntimeException(sprintf('Unable to create cache directory: %s', $this->directory));
        }

        file_put_contents($this->pathFor($key), json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function pathFor(string $key): string
    {
        $safeKey = preg_replace('/[^A-Za-z0-9_\-]/', '-', $key) ?? $key;

        return rtrim($this->directory, '/') . '/' . $safeKey . '.json';
    }
}
