<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Channel;

use Rafeeq\Scheduler\Http\HttpClient;
use Rafeeq\Scheduler\Message\Message;
use RuntimeException;

/**
 * Delivers messages to a Telegram chat via the Bot API.
 */
final class TelegramChannel implements Channel
{
    public function __construct(
        private readonly HttpClient $http,
        private readonly string $botToken,
        private readonly string $chatId,
    ) {
    }

    public function name(): string
    {
        return 'telegram';
    }

    public function send(Message $message): void
    {
        $data = array_merge($message->optionsFor($this->name()), [
            'chat_id' => $this->chatId,
            'text' => sprintf('%s%s%s', $message->title(), PHP_EOL . PHP_EOL, $message->body()),
            'parse_mode' => 'HTML',
        ]);

        if ($message->url() !== null) {
            $data['reply_markup'] = json_encode([
                'inline_keyboard' => [
                    [['text' => 'من هنا', 'url' => $message->url()]],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $url = sprintf('https://api.telegram.org/bot%s/sendMessage', $this->botToken);
        $result = $this->http->postForm($url, $data)->json();

        if (empty($result['ok'])) {
            throw new RuntimeException(sprintf(
                'Telegram API error (%s): %s',
                $result['error_code'] ?? 'unknown',
                $result['description'] ?? 'unknown error',
            ));
        }
    }
}
