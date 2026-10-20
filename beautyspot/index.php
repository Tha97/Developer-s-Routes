<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Find your next beauty favourite';

// Pull a handful of published, recently-joined professionals to feature.
$stmt = db()->query(
    "SELECT sp.id, sp.business_name, sp.categories, sp.suburb, sp.city, sp.specialties, sp.avatar_path, sp.price_range,
            COALESCE(sp.cover_path, (SELECT image_path FROM portfolio_images WHERE stylist_id = sp.id ORDER BY created_at DESC LIMIT 1)) AS cover_shot
     FROM stylist_profiles sp
     WHERE sp.is_published = 1
     ORDER BY sp.created_at DESC
     LIMIT 6"
);
$featured = $stmt->fetchAll();
foreach ($featured as &$s) {
    $s['rating'] = average_rating((int) $s['id']);
}
unset($s);

// Hero picture. A single photo from one chosen profile (set HERO_MAIN_PROFILE in config/config.php —
// any part of the business name works). It shows that profile's cover photo, or its latest portfolio photo.
// If no profile matches, the newest profile that has a photo is used instead.
$heroKeyword = defined('HERO_MAIN_PROFILE') ? HERO_MAIN_PROFILE : 'Tsholofelo';
$photoSql = "COALESCE(sp.cover_path, (SELECT image_path FROM portfolio_images WHERE stylist_id = sp.id ORDER BY created_at DESC LIMIT 1), sp.avatar_path)";

$heroStmt = db()->prepare(
    "SELECT sp.id, sp.business_name, sp.categories, $photoSql AS photo
     FROM stylist_profiles sp
     WHERE sp.is_published = 1 AND $photoSql IS NOT NULL
     ORDER BY (sp.business_name LIKE ?) DESC, sp.created_at DESC LIMIT 1"
);
$heroStmt->execute(['%' . $heroKeyword . '%']);
$heroPhoto = $heroStmt->fetch() ?: null;

$categories = professional_categories();

include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero-grid">
        <div>
            <div class="hero-eyebrow">Hair / Make-up / Nails</div>
            <h1>Find your next beauty favourite.</h1>
            <p class="lead">Discover trusted local hair, make-up and nail professionals — all in one place. Real people. Real reviews. Local talent.</p>
            <div class="hero-actions">
                <a href="<?= SITE_URL ?>/discover.php" class="btn btn-brass btn-pill">Discover professionals →</a>
                <a href="<?= SITE_URL ?>/register.php" class="btn btn-outline btn-pill" style="border-color:#B9AF9C;color:var(--paper);">List your work</a>
            </div>
        </div>
        <div class="hero-collage">
            <div class="hero-collage-main">
                <?php if ($heroPhoto): ?>
                    <a href="<?= SITE_URL ?>/stylist-profile.php?id=<?= (int) $heroPhoto['id'] ?>" aria-label="<?= e($heroPhoto['business_name']) ?>"><img src="<?= SITE_URL . '/' . e($heroPhoto['photo']) ?>" alt="<?= e($heroPhoto['business_name']) ?>"></a>
                    <?php $heroCats = category_labels($heroPhoto['categories'] ?? ''); if ($heroCats): ?>
                        <span class="hero-collage-badge"><?= e($heroCats[0]) ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div class="container search-bar-wrap">
    <div class="search-bar">
        <div class="field">
            <label for="q">🔍 What are you after?</label>
            <input type="text" id="q" placeholder="e.g. fades, braids, colour">
        </div>
        <div class="field">
            <label for="loc">📍 Suburb or city</label>
            <input type="text" id="loc" placeholder="e.g. Sandton">
        </div>
        <div class="field">
            <label for="radius">📏 Radius</label>
            <select id="radius">
                <option value="5">5 km</option>
                <option value="10" selected>10 km</option>
                <option value="25">25 km</option>
                <option value="50">50 km</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" onclick="goDiscover()">Search</button>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Local beauty professionals</span>
                <h2>Discover beauty talent near you</h2>
                <p style="margin-top:6px;">Browse real portfolios from local hairstylists, barbers, make-up artists and nail technicians. Find the right professional for your style and budget.</p>
            </div>
        </div>

        <div class="category-pill-row">
            <a href="<?= SITE_URL ?>/discover.php" class="category-pill active">All</a>
            <?php foreach ($categories as $code => $label): ?>
                <a href="<?= SITE_URL ?>/discover.php?category=<?= e($code) ?>" class="category-pill">
                    <?= $code === 'hair' ? '✂️' : ($code === 'makeup' ? '💄' : '💅') ?> <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($featured)): ?>
            <div class="empty-state">
                <p>No professionals have published a profile yet — be the first.</p>
                <a href="<?= SITE_URL ?>/register.php" class="btn btn-brass">Create your profile</a>
            </div>
        <?php else: ?>
        <div class="stylist-grid">
            <?php foreach ($featured as $s): ?>
                <a class="stylist-card" href="<?= SITE_URL ?>/stylist-profile.php?id=<?= (int) $s['id'] ?>">
                    <div class="thumb">
                        <?php if ($s['cover_shot']): ?>
                            <img src="<?= SITE_URL . '/' . e($s['cover_shot']) ?>" alt="<?= e($s['business_name']) ?>">
                        <?php endif; ?>
                        <?php $cls = category_labels($s['categories'] ?? ''); if ($cls): ?>
                            <span class="category-badge"><?= e($cls[0]) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="card-top">
                            <?php if (!empty($s['avatar_path'])): ?>
                                <img src="<?= SITE_URL . '/' . e($s['avatar_path']) ?>" alt="" width="40" height="40" style="border-radius:50%;object-fit:cover;" class="card-avatar">
                            <?php endif; ?>
                            <div>
                                <h3><?= e($s['business_name']) ?></h3>
                                <div class="location">📍 <?= e(trim(($s['suburb'] ?? '') . (($s['suburb'] && $s['city']) ? ', ' : '') . ($s['city'] ?? ''))) ?: 'South Africa' ?></div>
                            </div>
                        </div>
                        <?php if ($s['specialties']): ?><div class="tags"><?= e($s['specialties']) ?></div><?php endif; ?>
                        <div class="card-footer">
                            <?php if ($s['rating']['avg']): ?>
                                <span class="rating">★ <?= e((string) $s['rating']['avg']) ?> (<?= (int) $s['rating']['total'] ?>)</span>
                            <?php else: ?>
                                <span></span>
                            <?php endif; ?>
                            <?php if ($s['price_range']): ?><span class="price"><?= e($s['price_range']) ?></span><?php endif; ?>
                        </div>
                        <div style="margin-top:10px;"><span class="view-link">View profile →</span></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="section" id="how-it-works" style="padding-top:0;">
    <div class="container">
        <div class="section-head"><div><span class="eyebrow">Getting started</span><h2>How it works</h2></div></div>
        <div class="how-it-works-grid">
            <div class="how-step">
                <div class="how-step-num">1</div>
                <h3 style="font-size:1.1rem;">Search your area</h3>
                <p>Tell us what you're after and where you are — we'll show hair, make-up and nail professionals near you, sorted by distance.</p>
            </div>
            <div class="how-step">
                <div class="how-step-num">2</div>
                <h3 style="font-size:1.1rem;">Browse real portfolios</h3>
                <p>See actual work, specialties, prices and reviews from other clients before you decide who to book.</p>
            </div>
            <div class="how-step">
                <div class="how-step-num">3</div>
                <h3 style="font-size:1.1rem;">Book or reach out directly</h3>
                <p>Send a booking request, or message or call your chosen professional straight from their profile — no middleman, no commission.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" id="for-professionals" style="padding-top:0;">
    <div class="container">
        <div class="promo-banner">
            <div class="promo-banner-visual">💇‍♀️💄💅</div>
            <div class="promo-banner-body">
                <div class="eyebrow">Are you a beauty professional?</div>
                <h2>Showcase your talent.</h2>
                <p>Get discovered by local clients and grow your business — create a free profile with your portfolio, location and prices in under five minutes.</p>
                <a href="<?= SITE_URL ?>/register.php" class="btn btn-brass btn-pill">Create your profile — it's free →</a>
            </div>
        </div>
    </div>
</section>

<script>
function goDiscover() {
    const q = document.getElementById('q').value;
    const loc = document.getElementById('loc').value;
    const radius = document.getElementById('radius').value;
    const params = new URLSearchParams({ q, loc, radius });
    window.location.href = '<?= SITE_URL ?>/discover.php?' + params.toString();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
