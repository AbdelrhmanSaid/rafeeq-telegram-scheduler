<?php

/**
 * Send a push notification to all OneSignal subscribers.
 *
 * @param string $title The notification title to send
 * @param string $message The notification message to send
 * @param array $options Additional OneSignal notification options
 * @return array The decoded OneSignal API response
 * @throws RuntimeException If the request fails or OneSignal returns an error
 */
function sendOneSignalMessage(string $title, string $message, array $options = []): array
{
    global $config;

    $appId = $config['onesignal_app_id'];
    $restApiKey = $config['onesignal_rest_api_key'];
    $url = 'https://onesignal.com/api/v1/notifications';

    $data = array_merge([
        'app_id' => $appId,
        'included_segments' => ['All'],
        'headings' => [
            'en' => html_entity_decode(strip_tags($title), ENT_QUOTES, 'UTF-8'),
        ],
        'contents' => [
            'en' => html_entity_decode(strip_tags($message), ENT_QUOTES, 'UTF-8'),
        ],
    ], $options);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $restApiKey,
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        throw new RuntimeException("Failed to send OneSignal message: $err");
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $result = json_decode($response, true);

    if (!is_array($result)) {
        throw new RuntimeException('Failed to send OneSignal message: invalid JSON response');
    }

    if ($code < 200 || $code >= 300 || !empty($result['errors'])) {
        $errors = $result['errors'] ?? ['HTTP status ' . $code];
        $description = is_array($errors) ? implode(', ', $errors) : (string) $errors;
        throw new RuntimeException("OneSignal API error: $description");
    }

    return $result;
}
