<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');
$lat = isset($_GET['lat']) && $_GET['lat'] !== '' ? (float) $_GET['lat'] : null;
$lng = isset($_GET['lng']) && $_GET['lng'] !== '' ? (float) $_GET['lng'] : null;
$radius = isset($_GET['radius']) ? (float) $_GET['radius'] : 10;
$category = trim($_GET['category'] ?? '');

$sql = "SELECT sp.id, sp.business_name, sp.tagline, sp.categories, sp.bio, sp.specialties, sp.price_range, sp.suburb, sp.city, sp.lat, sp.lng, sp.avatar_path,
               COALESCE(sp.cover_path, (SELECT image_path FROM portfolio_images WHERE stylist_id = sp.id ORDER BY created_at DESC LIMIT 1)) AS cover_shot
        FROM stylist_profiles sp
        WHERE sp.is_published = 1";
$params = [];

if ($q !== '') {
    $sql .= ' AND (sp.business_name LIKE ? OR sp.specialties LIKE ? OR sp.bio LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($category !== '' && array_key_exists($category, professional_categories())) {
    $sql .= ' AND FIND_IN_SET(?, sp.categories)';
    $params[] = $category;
}

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$results = [];
foreach ($rows as $row) {
    $distance = null;
    if ($lat !== null && $lng !== null && $row['lat'] !== null && $row['lng'] !== null) {
        $distance = distance_km($lat, $lng, (float) $row['lat'], (float) $row['lng']);
        if ($distance > $radius) {
            continue; // outside the requested radius
        }
    }

    $rating = average_rating((int) $row['id']);

    $results[] = [
        'id' => (int) $row['id'],
        'business_name' => $row['business_name'],
        'tagline' => $row['tagline'],
        'category_labels' => category_labels($row['categories']),
        'specialties' => $row['specialties'],
        'price_range' => $row['price_range'],
        'suburb' => $row['suburb'],
        'city' => $row['city'],
        'lat' => $row['lat'] !== null ? (float) $row['lat'] : null,
        'lng' => $row['lng'] !== null ? (float) $row['lng'] : null,
        'cover_shot' => $row['cover_shot'] ? SITE_URL . '/' . $row['cover_shot'] : null,
        'avatar_path' => $row['avatar_path'] ? SITE_URL . '/' . $row['avatar_path'] : null,
        'distance_km' => $distance !== null ? round($distance, 1) : null,
        'rating' => $rating['avg'],
        'review_count' => $rating['total'],
        'profile_url' => SITE_URL . '/stylist-profile.php?id=' . (int) $row['id'],
    ];
}

// If searching by distance, show nearest first; otherwise newest first is already the SQL order.
if ($lat !== null && $lng !== null) {
    usort($results, fn($a, $b) => ($a['distance_km'] ?? 999999) <=> ($b['distance_km'] ?? 999999));
}

echo json_encode(['results' => $results, 'count' => count($results)]);
