<?php
declare(strict_types=1);

function loadZohoConfig(): array
{
    $configPath = __DIR__ . DIRECTORY_SEPARATOR . 'zoho-config.php';
    if (!is_file($configPath)) {
        return ['enabled' => false];
    }

    $config = require $configPath;
    return is_array($config) ? $config : ['enabled' => false];
}

function splitLeadName(string $name): array
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $parts = array_values(array_filter($parts, static fn (string $part): bool => $part !== ''));

    if (count($parts) <= 1) {
        return ['', $parts[0] ?? 'Unknown'];
    }

    $lastName = array_pop($parts);
    return [implode(' ', $parts), $lastName];
}

function zohoRequest(string $url, array $headers, ?array $payload = null): array
{
    $curl = curl_init($url);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 20,
    ];

    if ($payload !== null) {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = json_encode($payload, JSON_UNESCAPED_SLASHES);
    }

    curl_setopt_array($curl, $options);
    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);

    if ($body === false) {
        throw new RuntimeException($error !== '' ? $error : 'Zoho request failed.');
    }

    $decoded = json_decode($body, true);
    return [
        'status' => $status,
        'body' => is_array($decoded) ? $decoded : ['raw' => $body],
    ];
}

function getZohoAccessToken(array $config): string
{
    foreach (['accounts_domain', 'client_id', 'client_secret', 'refresh_token'] as $key) {
        if (empty($config[$key])) {
            throw new RuntimeException("Missing Zoho config value: {$key}");
        }
    }

    $query = http_build_query([
        'refresh_token' => $config['refresh_token'],
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'],
        'grant_type' => 'refresh_token',
    ]);

    $curl = curl_init(rtrim($config['accounts_domain'], '/') . '/oauth/v2/token?' . $query);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 20,
    ]);
    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);

    if ($body === false) {
        throw new RuntimeException($error !== '' ? $error : 'Zoho token request failed.');
    }

    $response = [
        'status' => $status,
        'body' => json_decode($body, true) ?: [],
    ];

    if ($response['status'] < 200 || $response['status'] >= 300 || empty($response['body']['access_token'])) {
        throw new RuntimeException('Unable to get Zoho access token.');
    }

    return (string)$response['body']['access_token'];
}

function sendLeadToZoho(array $lead, array $config): array
{
    if (empty($config['enabled'])) {
        return ['sent' => false, 'reason' => 'Zoho integration is disabled.'];
    }

    foreach (['api_domain', 'module', 'field_map'] as $key) {
        if (empty($config[$key])) {
            throw new RuntimeException("Missing Zoho config value: {$key}");
        }
    }

    [$firstName, $lastName] = splitLeadName((string)$lead['name']);
    $fields = $config['field_map'];
    $userId = ($config['user_id_source'] ?? 'email') === 'phone'
        ? preg_replace('/\D+/', '', (string)($lead['country_code'] . $lead['phone']))
        : strtolower((string)$lead['email']);

    $record = [
        $fields['user_id'] => $userId,
        $fields['last_name'] => $lastName,
        $fields['company'] => $lead['company'] !== '' ? $lead['company'] : 'Not provided',
        $fields['phone'] => trim($lead['country_code'] . ' ' . $lead['phone']),
        $fields['email'] => $lead['email'],
        $fields['lead_source'] => $config['lead_source'] ?? 'Website',
    ];

    if ($firstName !== '' && !empty($fields['first_name'])) {
        $record[$fields['first_name']] = $firstName;
    }

    if ($lead['service'] !== '' && !empty($fields['service'])) {
        $record[$fields['service']] = $lead['service'];
    }

    if (!empty($fields['message'])) {
        $record[$fields['message']] = trim((string)$lead['message']);
    }

    $accessToken = getZohoAccessToken($config);
    $url = rtrim($config['api_domain'], '/') . '/crm/v8/' . rawurlencode((string)$config['module']) . '/upsert';
    $payload = [
        'data' => [$record],
        'duplicate_check_fields' => $config['duplicate_check_fields'] ?? ['User_ID'],
    ];
    $response = zohoRequest($url, [
        'Authorization: Zoho-oauthtoken ' . $accessToken,
        'Content-Type: application/json',
    ], $payload);

    if ($response['status'] < 200 || $response['status'] >= 300) {
        throw new RuntimeException('Zoho rejected the lead submission.');
    }

    return ['sent' => true, 'response' => $response['body']];
}
