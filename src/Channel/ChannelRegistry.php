<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Channel;

use InvalidArgumentException;

/**
 * A lookup of available channels, keyed by name.
 */
final class ChannelRegistry
{
    /** @var array<string, Channel> */
    private array $channels = [];

    /**
     * @param iterable<Channel> $channels
     */
    public function __construct(iterable $channels = [])
    {
        foreach ($channels as $channel) {
            $this->register($channel);
        }
    }

    /**
     * Register (or replace) a channel by its name.
     */
    public function register(Channel $channel): void
    {
        $this->channels[$channel->name()] = $channel;
    }

    /**
     * Whether a channel with the given name is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->channels[$name]);
    }

    /**
     * Resolve a channel by name.
     *
     * @throws InvalidArgumentException If no channel is registered under that name.
     */
    public function get(string $name): Channel
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException(sprintf('Unknown channel: %s', $name));
        }

        return $this->channels[$name];
    }
}
