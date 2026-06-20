<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler;

use Rafeeq\Scheduler\Channel\ChannelRegistry;
use Rafeeq\Scheduler\Logging\Logger;
use Rafeeq\Scheduler\Message\Context;
use Rafeeq\Scheduler\Message\Message;
use Rafeeq\Scheduler\Message\MessageLoader;
use Throwable;

/**
 * The orchestrator: loads messages, keeps the due ones, and dispatches each to
 * its channels, logging every outcome.
 *
 * It depends only on small, focused collaborators, so its behaviour can be
 * unit-tested with fakes and extended without modification.
 */
final class Scheduler
{
    public function __construct(
        private readonly MessageLoader $messages,
        private readonly ChannelRegistry $channels,
        private readonly Logger $logger,
        private readonly Context $context,
    ) {
    }

    /**
     * Run a single scheduling pass.
     */
    public function run(): void
    {
        foreach ($this->messages->all() as $message) {
            if ($message->isDue($this->context)) {
                $this->dispatch($message);
            }
        }
    }

    /**
     * Deliver a due message to each of its channels, isolating failures so one
     * channel (or message) never blocks the rest.
     */
    private function dispatch(Message $message): void
    {
        foreach ($message->channels() as $channelName) {
            try {
                $this->channels->get($channelName)->send($message);

                $this->logger->success(sprintf(
                    'Message "%s" sent successfully via %s',
                    $message->key(),
                    $channelName,
                ));
            } catch (Throwable $e) {
                $this->logger->failure(sprintf(
                    'Message "%s" failed to send via %s, error: %s',
                    $message->key(),
                    $channelName,
                    $e->getMessage(),
                ));
            }
        }
    }
}
