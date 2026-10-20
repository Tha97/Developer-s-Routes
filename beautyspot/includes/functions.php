<?php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . SITE_URL . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_check(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Your session expired. Please go back and try again.');
    }
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

/**
 * Handle an uploaded image: validates type/size and moves it into $subdir under /uploads.
 * Returns the relative path (e.g. "uploads/portfolio/xxxx.jpg") or throws an Exception.
 */
function handle_image_upload(array $file, string $subdir): string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('No file was uploaded.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload failed. Please try a different file.');
    }
    if ($file['size'] > UPLOAD_MAX_BYTES) {
        throw new Exception('That image is too large. Max size is ' . (UPLOAD_MAX_BYTES / 1024 / 1024) . 'MB.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        throw new Exception('Please upload a JPG, PNG or WEBP image.');
    }

    $ext = $allowed[$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destDir = __DIR__ . '/../uploads/' . $subdir . '/';
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $destPath = $destDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new Exception('Could not save the uploaded file.');
    }

    return 'uploads/' . $subdir . '/' . $filename;
}

/**
 * Haversine distance in kilometres between two lat/lng points.
 */
function distance_km(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

function average_rating(int $stylistId): array
{
    $stmt = db()->prepare('SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM reviews WHERE stylist_id = ?');
    $stmt->execute([$stylistId]);
    $row = $stmt->fetch();
    return [
        'avg' => $row['avg_rating'] ? round((float) $row['avg_rating'], 1) : null,
        'total' => (int) $row['total'],
    ];
}

/**
 * Normalise a South African phone number to international digits (no "+").
 * "082 123 4567" -> "27821234567", "+27 82 123 4567" -> "27821234567".
 */
function normalize_phone_digits(?string $raw): ?string
{
    $digits = preg_replace('/\D+/', '', (string) $raw);
    if ($digits === '') return null;
    if (strpos($digits, '00') === 0) $digits = substr($digits, 2);
    if ($digits[0] === '0') $digits = '27' . substr($digits, 1);
    return strlen($digits) >= 9 ? $digits : null;
}

function whatsapp_link(?string $raw, string $message = ''): ?string
{
    $digits = normalize_phone_digits($raw);
    if (!$digits) return null;
    return 'https://wa.me/' . $digits . ($message !== '' ? '?text=' . rawurlencode($message) : '');
}

function tel_link(?string $raw): ?string
{
    $digits = normalize_phone_digits($raw);
    return $digits ? 'tel:+' . $digits : null;
}

/**
 * Read the site stylesheet and return it minified, ready to be printed inside <style>.
 * Delivering CSS from PHP (instead of as a separate static file) means it is always exactly the
 * version that was uploaded with the PHP files — it can never be served stale by the browser,
 * a CDN or the host's static-file cache.
 */
function inline_css(string $relativePath): string
{
    $file = __DIR__ . '/../' . ltrim($relativePath, '/');
    $css = is_file($file) ? file_get_contents($file) : '';
    $css = preg_replace('!/\*.*?\*/!s', '', $css);          // comments
    $css = preg_replace('/\s+/', ' ', $css);                 // whitespace runs
    $css = preg_replace('/\s*([{};,])\s*/', '$1', $css);    // around { } ; ,
    $css = preg_replace('/:\s+/', ':', $css);                // after property colons
    return trim($css);
}

/** Read a JavaScript file for inlining in a <script> tag (same reasoning as inline_css). */
function inline_js(string $relativePath): string
{
    $file = __DIR__ . '/../' . ltrim($relativePath, '/');
    $js = is_file($file) ? file_get_contents($file) : '';
    return str_ireplace('</script', '<\\/script', $js);
}

/**
 * The BeautySpot logo, always printed with a full web address so it works from every folder
 * (home page, /dashboard/, etc). Looked up in this order:
 *   1. SITE_LOGO in config/config.php          (e.g. 'assets/my-logo.png')
 *   2. assets/img/logo.png|svg|webp|jpg
 *   3. assets/logo.png|svg|webp|jpg
 *   4. the first image file found directly inside assets/
 * If nothing is found the text logo is shown.
 */
function site_logo_html(): string
{
    $root = __DIR__ . '/../';
    $candidates = [];
    if (defined('SITE_LOGO') && SITE_LOGO) $candidates[] = ltrim(SITE_LOGO, '/');
    foreach (['assets/img/logo', 'assets/logo'] as $base) {
        foreach (['svg', 'png', 'webp', 'jpg', 'jpeg'] as $ext) $candidates[] = $base . '.' . $ext;
    }
    $found = glob($root . 'assets/*.{svg,png,webp,jpg,jpeg,SVG,PNG,WEBP,JPG,JPEG}', GLOB_BRACE) ?: [];
    sort($found);
    foreach ($found as $path) $candidates[] = 'assets/' . basename($path);

    foreach ($candidates as $rel) {
        if (is_file($root . $rel)) {
            return '<img class="logo-img" src="' . SITE_URL . '/' . rawurlencode_path($rel) . '?v=' . filemtime($root . $rel) . '" alt="' . e(SITE_NAME) . '" height="40">';
        }
    }
    return '<span class="logo-mark">🌿</span>Beauty<span>Spot</span>';
}

/** Encode each part of a path (spaces etc.) but keep the slashes. */
function rawurlencode_path(string $path): string
{
    return implode('/', array_map('rawurlencode', explode('/', $path)));
}


/**
 * Legal details for the Terms and Privacy Policy pages. They come from config/config.php
 * (LEGAL_NAME, LEGAL_ADDRESS, CONTACT_EMAIL, CONTACT_PHONE, LEGAL_REG_NO). Anything not filled in shows
 * as a highlighted [placeholder] so it can't be missed.
 */
function legal_value(string $const, string $placeholder): string
{
    $value = defined($const) ? trim((string) constant($const)) : '';
    return $value !== '' ? e($value) : '<mark class="placeholder">[' . e($placeholder) . ']</mark>';
}

/** The "Terms | Privacy" switcher shown at the top of each legal page. */
function legal_tabs(string $active): string
{
    $items = ['terms' => ['Terms and Conditions', '/terms.php'], 'privacy' => ['Privacy Policy', '/privacy.php']];
    $html = '<nav class="legal-tabs" aria-label="Legal pages">';
    foreach ($items as $key => [$label, $path]) {
        $html .= '<a href="' . SITE_URL . $path . '"' . ($key === $active ? ' class="active" aria-current="page"' : '') . '>' . e($label) . '</a>';
    }
    return $html . '</nav>';
}


/**
 * A "come back here after logging in" path. Only plain paths on this site are allowed
 * (must start with a single "/"), so it can never be used to send people to another website.
 */
function safe_next(?string $next): string
{
    $next = (string) $next;
    if ($next === '' || $next[0] !== '/' || strpos($next, '//') === 0 || strpos($next, '\\') !== false || preg_match('/[\r\n]/', $next)) {
        return '';
    }
    return $next;
}

/** Send a plain-text email from the site (best effort: shared hosts sometimes delay or filter mail). */
function send_site_mail(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
    $host = parse_url(SITE_URL, PHP_URL_HOST) ?: 'localhost';
    $headers = [
        'From: ' . SITE_NAME . ' <no-reply@' . $host . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
    ];
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) $headers[] = 'Reply-To: ' . $replyTo;
    $subject = '=?UTF-8?B?' . base64_encode(preg_replace('/[\r\n]+/', ' ', $subject)) . '?=';
    return @mail($to, $subject, $body, implode("\r\n", $headers));
}
