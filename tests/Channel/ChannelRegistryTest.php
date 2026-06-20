<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Channel;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Rafeeq\Scheduler\Channel\Channel;
use Rafeeq\Scheduler\Channel\ChannelRegistry;
use Rafeeq\Scheduler\Message\Message;

final class ChannelRegistryTest extends TestCase
{
    public function test_it_registers_and_resolves_channels_by_name(): void
    {
        $channel = $this->channel('telegram');
        $registry = new ChannelRegistry([$channel]);

        $this->assertTrue($registry->has('telegram'));
        $this->assertSame($channel, $registry->get('telegram'));
    }

    public function test_it_reports_unknown_channels(): void
    {
        $registry = new ChannelRegistry();

        $this->assertFalse($registry->has('sms'));
    }

    public function test_it_throws_when_resolving_an_unknown_channel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ChannelRegistry())->get('sms');
    }

    private function channel(string $name): Channel
    {
        return new class ($name) implements Channel {
            public function __construct(private readonly string $name)
            {
            }

            public function name(): string
            {
                return $this->name;
            }

            public function send(Message $message): void
            {
            }
        };
    }
}
