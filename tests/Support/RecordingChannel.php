<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Support;

use Rafeeq\Scheduler\Channel\Channel;
use Rafeeq\Scheduler\Message\Message;
use RuntimeException;

/**
 * A {@see Channel} that records the messages it was asked to deliver, and can
 * be told to fail to exercise error handling.
 */
final class RecordingChannel implements Channel
{
    /** @var list<Message> */
    public array $sent = [];

    public function __construct(
        private readonly string $name = 'telegram',
        private readonly bool $shouldFail = false,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function send(Message $message): void
    {
        if ($this->shouldFail) {
            throw new RuntimeException('boom');
        }

        $this->sent[] = $message;
    }
}
