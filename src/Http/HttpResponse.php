<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Http;

use RuntimeException;

/**
 * An immutable HTTP response: status code plus raw body.
 */
final class HttpResponse
{
    public function __construct(
        public readonly int $status,
        public readonly string $body,
    ) {
    }

    /**
     * Whether the response carries a 2xx status code.
     */
    public function successful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    /**
     * Decode the body as a JSON array.
     *
     * @throws RuntimeException If the body is not valid JSON.
     */
    public function json(): array
    {
        $decoded = json_decode($this->body, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Expected a JSON response but could not decode the body.');
        }

        return $decoded;
    }
}
