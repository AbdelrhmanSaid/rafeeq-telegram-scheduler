<?php

return [
    'channels' => ['telegram', 'onesignal'],

    'title' => 'أذكار الصباح',
    'message' => 'من فوائد قراءة أذكار الصباح دوام الصلة بالله تعالى والأنس به وبمعيّته، وتحصيل كرامة ثناءه في الملأ الأعلى، ورفعة الدرجات في الجنة، وتكفير الذنوب والخطايا، والحفظ من مصارع السوء وفجأة النقم، والحرز من شر كلّ شيءٍ الله آخذ بناصيته',
    'url' => 'https://www.rafeeq.me/azkar/morning',

    'due' => function () {
        $fajr = getPrayerTimes()['Fajr'];
        $notification = strtotime('+15 minutes', strtotime($fajr));

        return _date('H:i', $notification) === _date('H:i');
    },
];
