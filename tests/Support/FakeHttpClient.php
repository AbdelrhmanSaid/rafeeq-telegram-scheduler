<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Support;

use Rafeeq\Scheduler\Http\HttpClient;
use Rafeeq\Scheduler\Http\HttpResponse;
use RuntimeException;

/**
 * An in-memory {@see HttpClient} that returns queued responses and records the
 * requests it received. Lets us test channels and providers without network.
 */
final class FakeHttpClient implements HttpClient
{
    /** @var list<HttpResponse> */
    private array $responses = [];

    /** @var list<array{method: string, url: string, data: array<mixed>, headers: array<string, string>}> */
    public array $requests = [];

    public function queue(HttpResponse $response): self
    {
        $this->responses[] = $response;

        return $this;
    }

    public function get(string $url, array $headers = []): HttpResponse
    {
        return $this->record('GET', $url, [], $headers);
    }

    public function postForm(string $url, array $data, array $headers = []): HttpResponse
    {
        return $this->record('POST', $url, $data, $headers);
    }

    public function postJson(string $url, array $data, array $headers = []): HttpResponse
    {
        return $this->record('POST', $url, $data, $headers);
    }

    /**
     * @param array<mixed>          $data
     * @param array<string, string> $headers
     */
    private function record(string $method, string $url, array $data, array $headers): HttpResponse
    {
        $this->requests[] = compact('method', 'url', 'data', 'headers');

        if ($this->responses === []) {
            throw new RuntimeException('No queued HTTP response for ' . $url);
        }

        return array_shift($this->responses);
    }
}
