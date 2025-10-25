<?php

/**
 * Date function wrapper relative to the global timestamp
 *
 * @param string $format The format of the date
 * @param int|null $baseTimestamp The timestamp to use, defaults to the current time
 * @return string The current date
 */
function _date(string $format, ?int $baseTimestamp = null): string
{
    global $timestamp;

    return date($format, $baseTimestamp ?? $timestamp);
}
