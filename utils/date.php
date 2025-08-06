<?php

/**
 * Date function wrapper relative to the global timestamp
 *
 * @param string $format The format of the date
 * @return string The current date
 */
function _date(string $format): string
{
    global $timestamp;

    return date($format, $timestamp);
}
