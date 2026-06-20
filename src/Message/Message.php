<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Message;

use Closure;
use InvalidArgumentException;

/**
 * An immutable, self-describing scheduled message.
 *
 * A message knows its content, which channels should deliver it, any
 * per-channel options, and — crucially — whether it is due right now.
 */
final class Message
{
    /**
     * @param list<string>                $channels Channel names that should deliver this message.
     * @param array<string, array<mixed>> $options  Per-channel delivery options, keyed by channel name.
     * @param Closure(Context): bool      $due      Predicate deciding if the message should be sent now.
     */
    public function __construct(
        private readonly string $key,
        private readonly string $title,
        private readonly string $body,
        private readonly array $channels,
        private readonly ?string $url,
        private readonly array $options,
        private readonly Closure $due,
    ) {
    }

    /**
     * Build a message from a definition array (as returned by files in messages/).
     *
     * @param array<string, mixed> $definition
     *
     * @throws InvalidArgumentException If the definition is malformed.
     */
    public static function fromArray(string $key, array $definition): self
    {
        foreach (['title', 'message', 'channels', 'due'] as $required) {
            if (!array_key_exists($required, $definition)) {
                throw new InvalidArgumentException(sprintf('Message "%s" is missing the "%s" key.', $key, $required));
            }
        }

        if (!$definition['due'] instanceof Closure) {
            throw new InvalidArgumentException(sprintf('Message "%s" must define "due" as a closure.', $key));
        }

        return new self(
            key: $key,
            title: (string) $definition['title'],
            body: (string) $definition['message'],
            channels: array_values((array) $definition['channels']),
            url: isset($definition['url']) ? (string) $definition['url'] : null,
            options: $definition['options'] ?? [],
            due: $definition['due'],
        );
    }

    public function key(): string
    {
        return $this->key;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function url(): ?string
    {
        return $this->url;
    }

    /**
     * @return list<string>
     */
    public function channels(): array
    {
        return $this->channels;
    }

    /**
     * Delivery options for a specific channel, or an empty array if none.
     *
     * @return array<mixed>
     */
    public function optionsFor(string $channel): array
    {
        return $this->options[$channel] ?? [];
    }

    /**
     * Evaluate whether this message should be sent right now.
     */
    public function isDue(Context $context): bool
    {
        return (bool) ($this->due)($context);
    }
}
