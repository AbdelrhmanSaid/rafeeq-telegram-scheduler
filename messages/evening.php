<?php

return [
    'channels' => ['telegram', 'onesignal'],

    'title' => 'أذكار المساء',
    'message' => 'عن أبي موسى الأشعري رضي الله عنه أن رسول الله صلى الله عليه وسلم، قال: (مثل الذي يذكر ربه والذي لا يذكر ربه، مثل الحيِّ والميت) متفق عليه',
    'url' => 'https://www.rafeeq.me/azkar/evening',

    'due' => function () {
        $maghrib = getPrayerTimes()['Maghrib'];
        $notification = strtotime('-1 hour', strtotime($maghrib));

        return _date('H:i', $notification) === _date('H:i');
    },
];
