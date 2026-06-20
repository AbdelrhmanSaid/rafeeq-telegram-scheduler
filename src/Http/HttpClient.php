<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Http;

use RuntimeException;

/**
 * A minimal HTTP client contract shared by every outbound integration.
 *
 * Channels and providers depend on this interface rather than a concrete
 * transport, which keeps them decoupled from cURL and trivial to test.
 */
interface HttpClient
{
    /**
     * Perform a GET request.
     *
     * @param array<string, string> $headers
     *
     * @throws RuntimeException On a transport-level failure.
     */
    public function get(string $url, array $headers = []): HttpResponse;

    /**
     * POST a URL-encoded form body.
     *
     * @param array<string, mixed>  $data
     * @param array<string, string> $headers
     *
     * @throws RuntimeException On a transport-level failure.
     */
    public function postForm(string $url, array $data, array $headers = []): HttpResponse;

    /**
     * POST a JSON body.
     *
     * @param array<string, mixed>  $data
     * @param array<string, string> $headers
     *
     * @throws RuntimeException On a transport-level failure.
     */
    public function postJson(string $url, array $data, array $headers = []): HttpResponse;
}
