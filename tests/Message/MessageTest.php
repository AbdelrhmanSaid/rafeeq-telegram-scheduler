<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Message;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Rafeeq\Scheduler\Message\Context;
use Rafeeq\Scheduler\Message\Message;
use Rafeeq\Scheduler\Tests\Support\Factory;

final class MessageTest extends TestCase
{
    public function test_it_builds_from_a_definition_array(): void
    {
        $message = Message::fromArray('morning', [
            'channels' => ['telegram'],
            'title' => 'Title',
            'message' => 'Body',
            'url' => 'https://example.com',
            'options' => ['telegram' => ['disable_notification' => true]],
            'due' => fn (Context $context) => true,
        ]);

        $this->assertSame('morning', $message->key());
        $this->assertSame('Title', $message->title());
        $this->assertSame('Body', $message->body());
        $this->assertSame('https://example.com', $message->url());
        $this->assertSame(['telegram'], $message->channels());
        $this->assertSame(['disable_notification' => true], $message->optionsFor('telegram'));
        $this->assertSame([], $message->optionsFor('onesignal'));
    }

    public function test_it_rejects_definitions_missing_required_keys(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Message::fromArray('broken', ['title' => 'Title']);
    }

    public function test_is_due_evaluates_the_closure_with_context(): void
    {
        $context = Factory::context();

        $due = Message::fromArray('due', $this->definition(fn (Context $context) => true));
        $notDue = Message::fromArray('not-due', $this->definition(fn (Context $context) => false));

        $this->assertTrue($due->isDue($context));
        $this->assertFalse($notDue->isDue($context));
    }

    /**
     * @param \Closure(Context): bool $due
     *
     * @return array<string, mixed>
     */
    private function definition(\Closure $due): array
    {
        return [
            'channels' => ['telegram'],
            'title' => 'Title',
            'message' => 'Body',
            'due' => $due,
        ];
    }
}
