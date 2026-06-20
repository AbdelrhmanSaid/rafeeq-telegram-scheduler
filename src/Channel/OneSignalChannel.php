<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Channel;

use Rafeeq\Scheduler\Http\HttpClient;
use Rafeeq\Scheduler\Message\Message;
use RuntimeException;

/**
 * Delivers messages as push notifications to all OneSignal subscribers.
 */
final class OneSignalChannel implements Channel
{
    public function __construct(
        private readonly HttpClient $http,
        private readonly string $appId,
        private readonly string $restApiKey,
    ) {
    }

    public function name(): string
    {
        return 'onesignal';
    }

    public function send(Message $message): void
    {
        $data = array_merge([
            'app_id' => $this->appId,
            'included_segments' => ['All'],
            'headings' => ['en' => $this->plainText($message->title())],
            'contents' => ['en' => $this->plainText($message->body())],
        ], $message->optionsFor($this->name()));

        if ($message->url() !== null) {
            $data['url'] = $message->url();
        }

        $response = $this->http->postJson('https://onesignal.com/api/v1/notifications', $data, [
            'Authorization' => 'Basic ' . $this->restApiKey,
        ]);

        $result = $response->json();

        if (!$response->successful() || !empty($result['errors'])) {
            $errors = $result['errors'] ?? ['HTTP status ' . $response->status];
            $description = is_array($errors) ? implode(', ', $errors) : (string) $errors;

            throw new RuntimeException(sprintf('OneSignal API error: %s', $description));
        }
    }

    /**
     * Strip HTML and decode entities, since push notifications are plain text.
     */
    private function plainText(string $value): string
    {
        return html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
    }
}
