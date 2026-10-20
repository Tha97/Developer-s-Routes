<?php
/**
 * Free geocoding proxy — forwards address search / reverse-geocoding requests to
 * OpenStreetMap's Nominatim service (https://nominatim.org), so BeautySpot never
 * needs a paid Google Maps API key.
 *
 * We proxy through our own server (rather than calling Nominatim straight from the
 * browser) for two reasons: it lets us send a proper identifying User-Agent as
 * Nominatim's usage policy asks for, and it avoids any browser CORS issues.
 *
 * Nominatim's usage policy (https://operations.osmfoundation.org/policies/nominatim/)
 * allows reasonable use for free, but asks that you: identify your application,
 * stay under ~1 request/second, and cache results where possible. This is plenty
 * for a small-to-medium directory. If you outgrow it, LocationIQ and OpenCage both
 * offer generous free tiers with the same kind of API and no credit card required.
 */

require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Identify ourselves to Nominatim, as their usage policy requests.
$userAgent = 'BeautySpot/1.0 (' . SITE_URL . ')';

function nominatim_fetch(string $url, string $userAgent): ?array
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => ['Accept-Language: en'],
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
    } else {
        // Fallback for hosts without curl (rare, but some very old shared hosting setups).
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: {$userAgent}\r\nAccept-Language: en\r\n",
                'timeout' => 8,
            ],
        ]);
        $body = @file_get_contents($url, false, $context);
    }

    if ($body === false || $body === null) {
        return null;
    }
    $decoded = json_decode($body, true);
    return is_array($decoded) ? $decoded : null;
}

if ($action === 'search') {
    $q = trim($_GET['q'] ?? '');
    if ($q === '') {
        echo json_encode(['results' => []]);
        exit;
    }

    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
        'format' => 'jsonv2',
        'addressdetails' => 1,
        'countrycodes' => 'za', // South Africa only — adjust/remove if you expand beyond SA
        'limit' => 5,
        'q' => $q,
    ]);

    $data = nominatim_fetch($url, $userAgent);
    $results = [];
    foreach ($data ?? [] as $row) {
        $addr = $row['address'] ?? [];
        $results[] = [
            'display_name' => $row['display_name'] ?? '',
            'lat' => (float) $row['lat'],
            'lng' => (float) $row['lon'],
            'suburb' => $addr['suburb'] ?? $addr['neighbourhood'] ?? $addr['quarter'] ?? '',
            'city' => $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['municipality'] ?? '',
        ];
    }

    echo json_encode(['results' => $results]);
    exit;
}

if ($action === 'reverse') {
    $lat = (float) ($_GET['lat'] ?? 0);
    $lng = (float) ($_GET['lng'] ?? 0);

    $url = 'https://nominatim.openstreetmap.org/reverse?' . http_build_query([
        'format' => 'jsonv2',
        'addressdetails' => 1,
        'lat' => $lat,
        'lon' => $lng,
    ]);

    $row = nominatim_fetch($url, $userAgent);
    $addr = $row['address'] ?? [];

    echo json_encode([
        'display_name' => $row['display_name'] ?? '',
        'suburb' => $addr['suburb'] ?? $addr['neighbourhood'] ?? $addr['quarter'] ?? '',
        'city' => $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['municipality'] ?? '',
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
