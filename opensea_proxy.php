<?php
// Désactive l'affichage d'erreurs HTML pour ne pas casser le JSON renvoyé.
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(0);

/**
 * Proxy vers l'API OpenSea pour contourner les limitations CORS et utiliser la clé API.
 * Usage : opensea_proxy.php?owner=0x...&limit=10
 */

// Change this clé API si nécessaire
$apiKey = 'a4fb4c2e618841aab5d42b8b78d4b2ec';

// Si ni cURL ni OpenSSL ne sont disponibles, on ne peut pas récupérer l'API OpenSea.
if (!function_exists('curl_init') && !extension_loaded('openssl')) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => 'PHP missing required extension',
        'message' => 'Enable either the curl extension or the openssl extension to allow HTTPS requests.',
        'php_extensions' => [
            'curl' => function_exists('curl_init'),
            'openssl' => extension_loaded('openssl'),
        ],
    ]);
    exit;
}

// filter_input may not work in CLI environment; fall back to $_GET when needed.
$owner = filter_input(INPUT_GET, 'owner', FILTER_SANITIZE_STRING);
if (!$owner && isset($_GET['owner'])) {
    $owner = filter_var($_GET['owner'], FILTER_SANITIZE_STRING);
}

$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT, ['options' => ['default' => 12, 'min_range' => 1, 'max_range' => 50]]);
if ($limit === false && isset($_GET['limit'])) {
    $limit = filter_var($_GET['limit'], FILTER_VALIDATE_INT, ['options' => ['default' => 12, 'min_range' => 1, 'max_range' => 50]]);
}

$cursor = filter_input(INPUT_GET, 'cursor', FILTER_SANITIZE_STRING);
if (!$cursor && isset($_GET['cursor'])) {
    $cursor = filter_var($_GET['cursor'], FILTER_SANITIZE_STRING);
}

if (!$owner) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'owner parameter required']);
    exit;
}

$baseUrl = 'https://api.opensea.io/api/v2/accounts/' . $owner . '/nfts';
$params = http_build_query(array_filter([
    'limit' => $limit,
]));

$headers = [
    "Accept: application/json",
    "X-API-KEY: $apiKey",
];

$response = false;
$httpCode = 0;
$curlErr = '';

$fullUrl = $baseUrl . '?' . $params;

if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);
} else {
    // Fallback in environments where cURL is not available.
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers) . "\r\n",
            'ignore_errors' => true,
            'timeout' => 10,
        ],
    ]);

    $response = @file_get_contents($fullUrl, false, $context);

    // In PHP 8.3+, $http_response_header is deprecated; use http_get_last_response_headers() if available.
    $responseHeaders = [];
    if (function_exists('http_get_last_response_headers')) {
        $responseHeaders = http_get_last_response_headers() ?: [];
    }

    // Extract HTTP status code from response headers
    if (!empty($responseHeaders) && preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $responseHeaders[0], $m)) {
        $httpCode = (int)$m[1];
    }


    if ($response === false) {
        $curlErr = 'file_get_contents failed';
    }
}

header('Content-Type: application/json');

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Curl error', 'message' => $curlErr]);
    exit;
}

// If OpenSea returned an error page (HTML), extract a message.
$trimmed = trim($response);
if ($httpCode !== 200) {
    http_response_code($httpCode);
    $decoded = json_decode($trimmed, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo json_encode(['error' => 'OpenSea API error', 'data' => $decoded]);
    } else {
        echo json_encode([ 'error' => 'OpenSea API returned non-JSON response', 'http_code' => $httpCode, 'body_snippet' => substr($trimmed, 0, 400) ]);
    }
    exit;
}

// Validate JSON before forwarding.
$decoded = json_decode($trimmed, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(502);
    echo json_encode([ 'error' => 'Invalid JSON received from OpenSea', 'body_snippet' => substr($trimmed, 0, 400) ]);
    exit;
}

// Forward the valid JSON response from OpenSea.
echo $trimmed;
