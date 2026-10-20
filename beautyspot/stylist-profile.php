<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare(
    'SELECT sp.*, u.name AS owner_name, u.phone AS owner_phone, u.email AS owner_email FROM stylist_profiles sp
     JOIN users u ON u.id = sp.user_id
     WHERE sp.id = ? AND sp.is_published = 1'
);
$stmt->execute([$id]);
$stylist = $stmt->fetch();

if (!$stylist) {
    http_response_code(404);
    $pageTitle = 'Stylist not found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="empty-state"><p>This profile doesn\'t exist or is not published.</p><a class="btn btn-brass" href="' . SITE_URL . '/discover.php">Back to Discover</a></div></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $stylist['business_name'];

$stmt = db()->prepare('SELECT * FROM portfolio_images WHERE stylist_id = ? ORDER BY created_at DESC');
$stmt->execute([$id]);
$portfolio = $stmt->fetchAll();

$rating = average_rating($id);

$stmt = db()->prepare(
    'SELECT r.rating, r.comment, r.created_at, u.name FROM reviews r JOIN users u ON u.id = r.client_id
     WHERE r.stylist_id = ? ORDER BY r.created_at DESC LIMIT 10'
);
$stmt->execute([$id]);
$reviews = $stmt->fetchAll();

$user = current_user();
$accent = accent_color($stylist['accent_color'] ?? 'teal');
$accentOverrideCss = ":root { --brass: {$accent['base']}; --brass-dark: {$accent['dark']}; }";

$isFavorited = false;
if ($user && $user['role'] === 'client') {
    $stmt = db()->prepare('SELECT id FROM favorites WHERE client_id = ? AND stylist_id = ?');
    $stmt->execute([$user['id'], $id]);
    $isFavorited = (bool) $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'review') {
    csrf_check();
    if (!$user || $user['role'] !== 'client') {
        flash('error', 'Please log in as a client to leave a review.');
        redirect('/stylist-profile.php?id=' . $id);
    }
    $rating = max(1, min(5, (int) ($_POST['rating'] ?? 0)));
    $comment = trim($_POST['comment'] ?? '');

    $stmt = db()->prepare(
        'INSERT INTO reviews (stylist_id, client_id, rating, comment) VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)'
    );
    $stmt->execute([$id, $user['id'], $rating, $comment ?: null]);
    flash('success', 'Thanks for your review!');
    redirect('/stylist-profile.php?id=' . $id);
}

include __DIR__ . '/includes/header.php';
?>

<?php if (!empty($stylist['cover_path'])): ?>
    <div class="profile-cover">
        <img src="<?= SITE_URL . '/' . e($stylist['cover_path']) ?>" alt="<?= e($stylist['business_name']) ?> cover photo">
    </div>
<?php endif; ?>

<section class="section" style="padding-bottom:0;<?= !empty($stylist['cover_path']) ? 'padding-top:0;' : '' ?>">
    <div class="container">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;">
            <div style="display:flex;gap:18px;align-items:flex-start;">
                <?php if (!empty($stylist['avatar_path'])): ?>
                    <img src="<?= SITE_URL . '/' . e($stylist['avatar_path']) ?>" alt="<?= e($stylist['business_name']) ?>" width="76" height="76" style="border-radius:50%;object-fit:cover;" class="profile-avatar<?= !empty($stylist['cover_path']) ? ' overlap' : '' ?>">
                <?php endif; ?>
                <div class="profile-identity<?= !empty($stylist['cover_path']) ? ' has-cover' : '' ?>">
                <span class="eyebrow" style="color:var(--brass-dark);font-weight:600;font-size:0.85rem;">
                    <?= e(trim(($stylist['suburb'] ?? '') . (($stylist['suburb'] && $stylist['city']) ? ', ' : '') . ($stylist['city'] ?? ''))) ?: 'South Africa' ?>
                </span>
                <h1 class="profile-name"><?= e($stylist['business_name']) ?></h1>
                <?php if (!empty($stylist['tagline'])): ?><p style="font-style:italic;color:#6B6151;margin-top:-6px;"><?= e($stylist['tagline']) ?></p><?php endif; ?>
                <?php if ($rating['avg']): ?>
                    <p style="color:var(--brass-dark);font-weight:600;margin-bottom:0;">★ <?= e((string) $rating['avg']) ?> (<?= (int) $rating['total'] ?> review<?= $rating['total'] === 1 ? '' : 's' ?>)</p>
                <?php endif; ?>
                </div>
            </div>
            <?php if ($user && $user['role'] === 'client'): ?>
                <form method="post" action="<?= SITE_URL ?>/favorites.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="stylist_id" value="<?= $id ?>">
                    <input type="hidden" name="action" value="<?= $isFavorited ? 'remove' : 'add' ?>">
                    <input type="hidden" name="redirect" value="/stylist-profile.php?id=<?= $id ?>">
                    <button type="submit" class="btn <?= $isFavorited ? 'btn-outline' : 'btn-brass' ?>">
                        <?= $isFavorited ? '♥ Saved' : '♡ Save to favorites' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($stylist['bio']): ?><p style="margin-top:18px;max-width:70ch;"><?= nl2br(e($stylist['bio'])) ?></p><?php endif; ?>

        <?php $catLabels = category_labels($stylist['categories'] ?? ''); ?>
        <?php if ($catLabels): ?>
            <div style="margin:14px 0 2px;">
                <?php foreach ($catLabels as $cl): ?><span class="category-badge"><?= e($cl) ?></span><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($stylist['specialties']): ?><div class="tags" style="color:var(--brass-dark);margin-top:8px;"><?= e($stylist['specialties']) ?></div><?php endif; ?>
        <?php if ($stylist['price_range']): ?><p style="font-weight:600;margin-top:12px;">💰 Typical prices: <?= e($stylist['price_range']) ?></p><?php endif; ?>

        <?php
        $bookMessage = 'Hi ' . $stylist['business_name'] . ', I found you on BeautySpot and would like to book an appointment.';
        // Contact details come straight from the account the professional registered with.
        $displayPhone = $stylist['owner_phone'] ?? '';
        $displayWhatsapp = $displayPhone;
        $contactEmail = $stylist['owner_email'] ?? '';
        $waLink = whatsapp_link($displayWhatsapp, $bookMessage);
        $telLink = tel_link($displayPhone);
        $mailLink = $contactEmail ? 'mailto:' . $contactEmail . '?subject=' . rawurlencode('Booking enquiry from BeautySpot') . '&body=' . rawurlencode($bookMessage) : null;
        // "Book now" opens the booking request form (not WhatsApp). The owner doesn't need it on their own profile.
        $isOwner = $user && (int) $user['id'] === (int) $stylist['user_id'];
        $bookLink = $isOwner ? '' : SITE_URL . '/book.php?id=' . (int) $stylist['id'];
        ?>
        <?php if ($bookLink || $waLink || $telLink || $mailLink): ?>
            <div class="contact-card">
                <div class="contact-actions">
                    <?php if ($bookLink): ?><a href="<?= e($bookLink) ?>" class="btn btn-brass btn-pill">📅 Book now</a><?php endif; ?>
                    <?php if ($waLink): ?><a href="<?= e($waLink) ?>" class="btn btn-whatsapp btn-pill" target="_blank" rel="noopener">💬 WhatsApp</a><?php endif; ?>
                    <?php if ($telLink): ?><a href="<?= e($telLink) ?>" class="btn btn-outline btn-pill">📞 Call</a><?php endif; ?>
                    <?php if ($mailLink): ?><a href="<?= e($mailLink) ?>" class="btn btn-outline btn-pill">✉️ Email</a><?php endif; ?>
                </div>
                <ul class="contact-list">
                    <?php if ($telLink): ?><li><span>📞 Phone</span><a href="<?= e($telLink) ?>"><?= e($displayPhone) ?></a></li><?php endif; ?>
                    <?php if ($waLink): ?><li><span>💬 WhatsApp</span><a href="<?= e($waLink) ?>" target="_blank" rel="noopener"><?= e($displayWhatsapp) ?></a></li><?php endif; ?>
                    <?php if ($contactEmail): ?><li><span>✉️ Email</span><a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a></li><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php if (!empty($bookLink)): ?>
<style>@media (max-width: 700px) { body { padding-bottom: 80px; } }</style>
<div class="mobile-cta">
    <a href="<?= e($bookLink) ?>" class="btn btn-brass btn-pill">📅 Book now</a>
    <?php if (!empty($telLink)): ?><a href="<?= e($telLink) ?>" class="btn btn-outline btn-pill">📞 Call</a><?php endif; ?>
</div>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="section-head"><h2 style="font-size:1.6rem;">Portfolio</h2></div>
        <?php if (empty($portfolio)): ?>
            <div class="empty-state"><p>No portfolio photos uploaded yet.</p></div>
        <?php else: ?>
            <div class="portfolio-grid">
                <?php foreach ($portfolio as $p): ?>
                    <figure>
                        <a href="<?= SITE_URL . '/' . e($p['image_path']) ?>" target="_blank" rel="noopener"><img src="<?= SITE_URL . '/' . e($p['image_path']) ?>" alt="<?= e($p['caption'] ?: 'Portfolio photo') ?>" loading="lazy"></a>
                        <?php if ($p['caption']): ?><figcaption><?= e($p['caption']) ?></figcaption><?php endif; ?>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($stylist['lat'] && $stylist['lng']): ?>
<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="section-head"><h2 style="font-size:1.6rem;">Location</h2></div>
        <?php if ($stylist['address']): ?><p style="margin-bottom:14px;"><?= e($stylist['address']) ?></p><?php endif; ?>
        <div id="map-canvas" style="height:320px;"></div>
    </div>
</section>
<?php endif; ?>

<section class="section" style="padding-top:0;">
    <div class="container">
        <div style="max-width:760px;">
            <div class="section-head"><h2 style="font-size:1.6rem;">Reviews</h2></div>
            <?php if (empty($reviews)): ?>
                <p style="color:#6B6151;">No reviews yet.</p>
            <?php else: ?>
                <?php foreach ($reviews as $r): ?>
                    <div style="border-bottom:1px solid var(--line);padding:16px 0;">
                        <div style="font-weight:700;color:var(--brass-dark);">★ <?= (int) $r['rating'] ?>/5 — <?= e($r['name']) ?></div>
                        <?php if ($r['comment']): ?><p style="margin-top:6px;"><?= e($r['comment']) ?></p><?php endif; ?>
                        <div style="font-size:0.78rem;color:#8F8570;"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($user && $user['role'] === 'client'): ?>
                <div class="dash-card" style="margin-top:24px;">
                    <h3 style="font-size:1.1rem;">Leave a review</h3>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="review">
                        <div class="field">
                            <label for="rating">Rating</label>
                            <select id="rating" name="rating" required>
                                <option value="5">★★★★★ Excellent</option>
                                <option value="4">★★★★ Good</option>
                                <option value="3">★★★ Average</option>
                                <option value="2">★★ Below average</option>
                                <option value="1">★ Poor</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="comment">Comment (optional)</label>
                            <textarea id="comment" name="comment" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline btn-block">Submit review</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($stylist['lat'] && $stylist['lng']): ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
const pos = [<?= (float) $stylist['lat'] ?>, <?= (float) $stylist['lng'] ?>];
const map = L.map('map-canvas').setView(pos, 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
}).addTo(map);
const icon = L.icon({
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41],
});
L.marker(pos, { icon }).addTo(map).bindTooltip(<?= json_encode($stylist['business_name']) ?>);
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
