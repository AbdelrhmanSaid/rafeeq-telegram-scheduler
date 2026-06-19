<?php

$message = <<<HTML
عن أبي موسى الأشعري رضي الله عنه أن رسول الله صلى الله عليه وسلم، قال: (مثل الذي يذكر ربه والذي لا يذكر ربه، مثل الحيِّ والميت) متفق عليه.

إقرأ الآن أذكار المساء من هنا 👇

<a href="https://www.rafeeq.me/azkar/evening">https://www.rafeeq.me/azkar/evening</a>
HTML;

return [
    'channels' => ['telegram', 'onesignal'],
    'title' => '✨ أذكار المساء ✨',
    'due' => function () {
        $maghrib = getPrayerTimes()['Maghrib'];
        $notification = strtotime('-1 hour', strtotime($maghrib));

        return _date('H:i', $notification) === _date('H:i');
    },
    'message' => $message,
];
