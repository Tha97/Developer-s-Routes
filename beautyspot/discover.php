<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Discover beauty professionals';

$initialQ = $_GET['q'] ?? '';
$initialLoc = $_GET['loc'] ?? '';
$initialRadius = $_GET['radius'] ?? '10';
$initialCategory = $_GET['category'] ?? '';
$categories = professional_categories();

include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-bottom:24px;">
    <div class="container">
        <div class="section-head" style="margin-bottom:22px;">
            <div>
                <span class="eyebrow">Discover</span>
                <h1 style="font-size:clamp(1.8rem,3.5vw,2.6rem);">Find a beauty professional near you</h1>
            </div>
        </div>

        <div class="search-bar search-bar-wide">
            <div class="field">
                <label for="q">🔍 What are you after?</label>
                <input type="text" id="q" value="<?= e($initialQ) ?>" placeholder="e.g. fades, bridal makeup, gel nails">
            </div>
            <div class="field">
                <label for="category">Category</label>
                <select id="category">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $code => $label): ?>
                        <option value="<?= e($code) ?>" <?= $initialCategory === $code ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="loc">📍 Suburb or city</label>
                <input type="text" id="loc" value="<?= e($initialLoc) ?>" placeholder="e.g. Sandton">
            </div>
            <div class="field">
                <label for="radius">📏 Radius</label>
                <select id="radius">
                    <option value="5" <?= $initialRadius === '5' ? 'selected' : '' ?>>5 km</option>
                    <option value="10" <?= $initialRadius === '10' ? 'selected' : '' ?>>10 km</option>
                    <option value="25" <?= $initialRadius === '25' ? 'selected' : '' ?>>25 km</option>
                    <option value="50" <?= $initialRadius === '50' ? 'selected' : '' ?>>50 km</option>
                </select>
            </div>
            <button type="button" class="btn btn-primary" id="search-btn">Search</button>
        </div>
    </div>
</section>

<section class="section" style="padding-top:12px;">
    <div class="container">
        <div class="discover-layout">
            <div id="map-canvas"></div>
            <div class="discover-results">
                <p id="result-count" class="result-count">Searching...</p>
                <div id="results-list" class="results-list"></div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
const SITE_URL = <?= json_encode(SITE_URL) ?>;
const INITIAL = { q: <?= json_encode($initialQ) ?>, loc: <?= json_encode($initialLoc) ?>, radius: <?= json_encode($initialRadius) ?>, category: <?= json_encode($initialCategory) ?> };
</script>
<script><?= inline_js('includes/js/discover.js') ?></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
