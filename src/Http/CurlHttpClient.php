<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Http;

use RuntimeException;

/**
 * A thin cURL-backed {@see HttpClient}.
 *
 * Centralising the transport here keeps channels and providers free of cURL
 * boilerplate and gives us a single seam to swap or fake when testing.
 */
final class CurlHttpClient implements HttpClient
{
    public function __construct(private readonly int $timeout = 60)
    {
    }

    public function get(string $url, array $headers = []): HttpResponse
    {
        return $this->send($url, null, $headers);
    }

    public function postForm(string $url, array $data, array $headers = []): HttpResponse
    {
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';

        return $this->send($url, http_build_query($data), $headers);
    }

    public function postJson(string $url, array $data, array $headers = []): HttpResponse
    {
        $headers['Content-Type'] = 'application/json';

        return $this->send($url, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $headers);
    }

    /**
     * @param array<string, string> $headers
     *
     * @throws RuntimeException On a transport-level failure.
     */
    private function send(string $url, ?string $body, array $headers): HttpResponse
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        if ($headers !== []) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
        }

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException(sprintf('HTTP request to %s failed: %s', $url, $error));
        }

        return new HttpResponse($status, (string) $response);
    }

    /**
     * @param array<string, string> $headers
     *
     * @return list<string>
     */
    private function formatHeaders(array $headers): array
    {
        $formatted = [];

        foreach ($headers as $name => $value) {
            $formatted[] = sprintf('%s: %s', $name, $value);
        }

        return $formatted;
    }
}
