<?php

declare(strict_types=1);

use Rafeeq\Scheduler\Message\Context;

return [
    'channels' => ['telegram', 'onesignal'],

    'title' => 'تذكير صيام الخميس',
    'message' => 'عن أبي هريرة رضي الله عنه قال: قال رسول الله صلى الله عليه وسلم: (تُعرضُ الأعمالُ يومَ الإثنين والخميسِ فأُحِبُّ أن يُعرضَ عملي وأنا صائمٌ).',

    'due' => fn (Context $context) => $context->clock->format('l') === 'Wednesday'
        && $context->clock->format('h:i a') === '10:00 pm',
];
