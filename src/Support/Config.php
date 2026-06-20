<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Support;

use RuntimeException;

/**
 * Read-only application configuration with dot-notation access.
 *
 * Replaces the old `global $config` array: dependencies now receive exactly the
 * values they need instead of reaching into a shared global.
 */
final class Config
{
    public function __construct(private readonly array $values)
    {
    }

    /**
     * Build a config object from a PHP file that returns an array.
     *
     * @throws RuntimeException If the file is missing or does not return an array.
     */
    public static function fromFile(string $path): self
    {
        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Config file not found: %s', $path));
        }

        $values = require $path;

        if (!is_array($values)) {
            throw new RuntimeException(sprintf('Config file must return an array: %s', $path));
        }

        return new self($values);
    }

    /**
     * Fetch a value using dot notation (e.g. "prayer.city"), falling back to $default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->values;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Fetch a required value, throwing if it is missing or empty.
     *
     * @throws RuntimeException If the key is absent or its value is empty.
     */
    public function require(string $key): mixed
    {
        $value = $this->get($key);

        if ($value === null || $value === '') {
            throw new RuntimeException(sprintf('Missing required config value: %s', $key));
        }

        return $value;
    }
}
