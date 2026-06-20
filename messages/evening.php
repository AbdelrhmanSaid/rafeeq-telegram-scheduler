<?php

declare(strict_types=1);

use Rafeeq\Scheduler\Message\Context;

return [
    'channels' => ['telegram', 'onesignal'],

    'title' => 'أذكار المساء',
    'message' => 'عن أبي موسى الأشعري رضي الله عنه أن رسول الله صلى الله عليه وسلم، قال: (مثل الذي يذكر ربه والذي لا يذكر ربه، مثل الحيِّ والميت) متفق عليه',
    'url' => 'https://www.rafeeq.me/azkar/evening',

    'due' => function (Context $context) {
        $maghrib = $context->prayer->time('Maghrib');
        $notification = strtotime('-1 hour', strtotime($maghrib));

        return $context->clock->format('H:i', $notification) === $context->clock->format('H:i');
    },
];
