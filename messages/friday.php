<?php

declare(strict_types=1);

use Rafeeq\Scheduler\Message\Context;

return [
    'channels' => ['telegram', 'onesignal'],

    'title' => 'يوم الجمعة المبارك',
    'message' => 'عن أبي هريرة رضي الله عنه أن رسول الله صلى الله عليه وسلم قال: (خير يوم طلعت عليه الشمس يوم الجمعة، فيه خُلِق آدم، وفيه أُدخل الجنة، وفيه أُخرج منها، ولا تقوم الساعة إلا في يوم الجمعة)',

    'due' => fn (Context $context) => $context->clock->format('l') === 'Friday'
        && $context->clock->format('h:i a') === '12:00 pm',
];
