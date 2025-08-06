<?php

/**
 * Get the prayer times for a specific city and country
 *
 * @param string $city The city to get the prayer times for
 * @param string $country The country to get the prayer times for
 * @param string|null $date The date to get the prayer times for
 * @return array The prayer times
 */
function getPrayerTimes(string $city = 'Cairo', string $country = 'EG', ?string $date = null): array
{
    $date = $date ?? date('Y-m-d');
    $endpoint = "https://api.aladhan.com/v1/timingsByCity/$date?city=$city&country=$country";

    $response = file_get_contents($endpoint);
    $data = json_decode($response, true);

    return $data['data']['timings'];
}
