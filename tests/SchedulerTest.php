<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests;

use PHPUnit\Framework\TestCase;
use Rafeeq\Scheduler\Channel\ChannelRegistry;
use Rafeeq\Scheduler\Message\MessageLoader;
use Rafeeq\Scheduler\Scheduler;
use Rafeeq\Scheduler\Tests\Support\Factory;
use Rafeeq\Scheduler\Tests\Support\RecordingChannel;
use Rafeeq\Scheduler\Tests\Support\SpyLogger;

final class SchedulerTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/scheduler-test-' . uniqid();
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . '/*.php') ?: [] as $file) {
            unlink($file);
        }

        rmdir($this->directory);
    }

    public function test_it_only_dispatches_due_messages(): void
    {
        $this->writeMessage('due', due: 'true');
        $this->writeMessage('not-due', due: 'false');

        $channel = new RecordingChannel('telegram');
        $logger = new SpyLogger();

        $this->scheduler($channel, $logger)->run();

        $this->assertCount(1, $channel->sent);
        $this->assertSame('due', $channel->sent[0]->key());
        $this->assertSame(['Message "due" sent successfully via telegram'], $logger->successes);
        $this->assertSame([], $logger->failures);
    }

    public function test_it_logs_a_failure_without_stopping_other_messages(): void
    {
        $this->writeMessage('first', due: 'true');
        $this->writeMessage('second', due: 'true');

        $channel = new RecordingChannel('telegram', shouldFail: true);
        $logger = new SpyLogger();

        $this->scheduler($channel, $logger)->run();

        $this->assertCount(2, $logger->failures);
        $this->assertSame([], $logger->successes);
    }

    public function test_it_records_a_failure_for_unknown_channels(): void
    {
        $this->writeMessage('orphan', due: 'true', channels: "'sms'");

        $logger = new SpyLogger();
        $this->scheduler(new RecordingChannel('telegram'), $logger)->run();

        $this->assertCount(1, $logger->failures);
        $this->assertStringContainsString('Unknown channel: sms', $logger->failures[0]);
    }

    private function scheduler(RecordingChannel $channel, SpyLogger $logger): Scheduler
    {
        return new Scheduler(
            new MessageLoader($this->directory),
            new ChannelRegistry([$channel]),
            $logger,
            Factory::context(),
        );
    }

    private function writeMessage(string $key, string $due, string $channels = "'telegram'"): void
    {
        $contents = <<<PHP
        <?php

        use Rafeeq\Scheduler\Message\Context;

        return [
            'channels' => [$channels],
            'title' => 'Title',
            'message' => 'Body',
            'due' => fn (Context \$context) => $due,
        ];
        PHP;

        file_put_contents($this->directory . '/' . $key . '.php', $contents);
    }
}
