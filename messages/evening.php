<?php

$message = <<<HTML
✨ أذكار المساء ✨

عن أبي موسى الأشعري رضي الله عنه أن رسول الله صلى الله عليه وسلم، قال: (مثل الذي يذكر ربه والذي لا يذكر ربه، مثل الحيِّ والميت) متفق عليه.

إقرأ الآن أذكار المساء من هنا 👇

<a href="https://www.rafeeq.me/azkar/2">https://www.rafeeq.me/azkar/2</a>
HTML;

return [
    'due' => fn () => date('h:i a') === '7:30 pm',
    'message' => $message,
];
