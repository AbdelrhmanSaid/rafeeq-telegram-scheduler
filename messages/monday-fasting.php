<?php

$message = <<<HTML
🌙 تذكير صيام الاثنين 🌙

عَنْ أَبِي قَتَادَةَ الْأَنْصَارِيِّ رَضِيَ اللَّهُ عَنْهُ أَنَّ رَسُولَ اللَّهِ صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ سُئِلَ عَنْ صَوْمِ الِاثْنَيْنِ فَقَالَ : (فِيهِ وُلِدْتُ وَفِيهِ أُنْزِلَ عَلَيَّ)
HTML;

return [
    'due' => fn () => _date('l') === 'Sunday' && _date('h:i a') === '10:00 pm',
    'message' => $message,
];
