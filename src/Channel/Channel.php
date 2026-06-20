<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Channel;

use Rafeeq\Scheduler\Message\Message;
use RuntimeException;

/**
 * A delivery channel for messages (Telegram, OneSignal, …).
 *
 * Adding a new channel is as simple as implementing this interface and
 * registering it — no changes to the scheduler are required.
 */
interface Channel
{
    /**
     * The name used to reference this channel in a message definition.
     */
    public function name(): string;

    /**
     * Deliver the given message.
     *
     * @throws RuntimeException If delivery fails.
     */
    public function send(Message $message): void;
}
