<?php

/**
 * Date function wrapper relative to the global timestamp
 *
 * @param string $format The format of the date
 * @return string The current date
 */
function _date($format)
{
    global $timetamp;

    return date($format, $timetamp);
}
