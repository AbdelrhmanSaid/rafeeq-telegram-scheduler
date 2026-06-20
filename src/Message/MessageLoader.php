<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Message;

use InvalidArgumentException;

/**
 * Loads message definitions from a directory of PHP files.
 *
 * Each file returns a definition array and is turned into a {@see Message},
 * keyed by its filename. Dropping a new file into the directory is all it takes
 * to add a scheduled message.
 */
final class MessageLoader
{
    public function __construct(private readonly string $directory)
    {
    }

    /**
     * Load and build every message in the directory.
     *
     * @return list<Message>
     *
     * @throws InvalidArgumentException If a definition file is malformed.
     */
    public function all(): array
    {
        $messages = [];

        foreach ($this->files() as $file) {
            $definition = require $file;

            if (!is_array($definition)) {
                throw new InvalidArgumentException(sprintf('Message file must return an array: %s', $file));
            }

            $messages[] = Message::fromArray(basename($file, '.php'), $definition);
        }

        return $messages;
    }

    /**
     * @return list<string>
     */
    private function files(): array
    {
        $files = glob(rtrim($this->directory, '/') . '/*.php');

        return $files === false ? [] : $files;
    }
}
