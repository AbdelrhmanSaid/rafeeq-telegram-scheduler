<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Channel;

use PHPUnit\Framework\TestCase;
use Rafeeq\Scheduler\Channel\TelegramChannel;
use Rafeeq\Scheduler\Http\HttpResponse;
use Rafeeq\Scheduler\Message\Context;
use Rafeeq\Scheduler\Message\Message;
use Rafeeq\Scheduler\Tests\Support\FakeHttpClient;
use RuntimeException;

final class TelegramChannelTest extends TestCase
{
    public function test_it_posts_the_message_to_the_telegram_api(): void
    {
        $http = (new FakeHttpClient())->queue(new HttpResponse(200, '{"ok":true}'));
        $channel = new TelegramChannel($http, 'BOT', 'CHAT');

        $channel->send($this->message(url: 'https://example.com'));

        $request = $http->requests[0];
        $this->assertStringContainsString('/botBOT/sendMessage', $request['url']);
        $this->assertSame('CHAT', $request['data']['chat_id']);
        $this->assertStringContainsString('Title', $request['data']['text']);
        $this->assertStringContainsString('Body', $request['data']['text']);
        $this->assertArrayHasKey('reply_markup', $request['data']);
    }

    public function test_it_omits_the_keyboard_when_there_is_no_url(): void
    {
        $http = (new FakeHttpClient())->queue(new HttpResponse(200, '{"ok":true}'));

        (new TelegramChannel($http, 'BOT', 'CHAT'))->send($this->message());

        $this->assertArrayNotHasKey('reply_markup', $http->requests[0]['data']);
    }

    public function test_it_throws_on_a_telegram_api_error(): void
    {
        $http = (new FakeHttpClient())->queue(
            new HttpResponse(400, '{"ok":false,"error_code":400,"description":"Bad Request"}')
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Telegram API error (400): Bad Request');

        (new TelegramChannel($http, 'BOT', 'CHAT'))->send($this->message());
    }

    private function message(?string $url = null): Message
    {
        return Message::fromArray('test', [
            'channels' => ['telegram'],
            'title' => 'Title',
            'message' => 'Body',
            'url' => $url,
            'due' => fn (Context $context) => true,
        ]);
    }
}
