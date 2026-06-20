<?php

declare(strict_types=1);

namespace Rafeeq\Scheduler\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Rafeeq\Scheduler\Cache\JsonFileCache;

final class JsonFileCacheTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/cache-test-' . uniqid();
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . '/*.json') ?: [] as $file) {
            unlink($file);
        }

        if (is_dir($this->directory)) {
            rmdir($this->directory);
        }
    }

    public function test_it_round_trips_values_and_creates_the_directory(): void
    {
        $cache = new JsonFileCache($this->directory);

        $cache->put('prayer-times', ['date' => '20-06-2026', 'timings' => ['Fajr' => '03:00']]);

        $this->assertSame(
            ['date' => '20-06-2026', 'timings' => ['Fajr' => '03:00']],
            $cache->get('prayer-times'),
        );
    }

    public function test_it_returns_null_for_unknown_keys(): void
    {
        $this->assertNull((new JsonFileCache($this->directory))->get('missing'));
    }
}
