<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Support;

use PHPUnit\Framework\TestCase;
use Rafeeq\Scheduler\Support\Config;
use RuntimeException;

final class ConfigTest extends TestCase
{
    public function test_it_reads_nested_values_with_dot_notation(): void
    {
        $config = new Config(['prayer' => ['city' => 'Cairo']]);

        $this->assertSame('Cairo', $config->get('prayer.city'));
    }

    public function test_it_returns_the_default_for_missing_keys(): void
    {
        $config = new Config([]);

        $this->assertSame('fallback', $config->get('missing.key', 'fallback'));
    }

    public function test_require_throws_when_a_value_is_missing(): void
    {
        $this->expectException(RuntimeException::class);

        (new Config([]))->require('bot_token');
    }

    public function test_require_returns_present_values(): void
    {
        $config = new Config(['bot_token' => 'abc']);

        $this->assertSame('abc', $config->require('bot_token'));
    }
}
