<?php

$message = <<<HTML
🕌 يوم الجمعة المبارك 🕌

عن أبي هريرة رضي الله عنه أن رسول الله صلى الله عليه وسلم قال: (خير يوم طلعت عليه الشمس يوم الجمعة، فيه خُلِق آدم، وفيه أُدخل الجنة، وفيه أُخرج منها، ولا تقوم الساعة إلا في يوم الجمعة).

اللهم صل وسلم على نبينا محمد ✨
HTML;

return [
    'due' => function() {
        $currentDay = date('l'); // Full day name (e.g., 'Friday')
        $currentTime = date('H:i');

        // Send on Friday at 12:00 (noon)
        return $currentDay === 'Friday' && $currentTime === '12:00';
    },
    'message' => $message,
];
