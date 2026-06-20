<?php

declare(strict_types=1);

/**
 * Backwards-compatible entry point.
 *
 * The real logic lives in src/. This shim keeps `php main.php` working for
 * existing cron jobs; new setups can call `bin/scheduler` (or `composer schedule`).
 */
require __DIR__ . '/bin/scheduler';
