<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Your favorites';
$user = require_role('client');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $stylistId = (int) ($_POST['stylist_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $redirectTo = $_POST['redirect'] ?? '/favorites.php';

    if ($action === 'add') {
        $stmt = db()->prepare('INSERT IGNORE INTO favorites (client_id, stylist_id) VALUES (?, ?)');
        $stmt->execute([$user['id'], $stylistId]);
        flash('success', 'Saved to your favorites.');
    } elseif ($action === 'remove') {
        $stmt = db()->prepare('DELETE FROM favorites WHERE client_id = ? AND stylist_id = ?');
        $stmt->execute([$user['id'], $stylistId]);
        flash('success', 'Removed from your favorites.');
    }

    redirect($redirectTo);
}

$stmt = db()->prepare(
    "SELECT sp.id, sp.business_name, sp.categories, sp.suburb, sp.city, sp.specialties, sp.avatar_path, sp.price_range,
            COALESCE(sp.cover_path, (SELECT image_path FROM portfolio_images WHERE stylist_id = sp.id ORDER BY created_at DESC LIMIT 1)) AS cover_shot
     FROM favorites f
     JOIN stylist_profiles sp ON sp.id = f.stylist_id
     WHERE f.client_id = ?
     ORDER BY f.created_at DESC"
);
$stmt->execute([$user['id']]);
$favorites = $stmt->fetchAll();
foreach ($favorites as &$s) {
    $s['rating'] = average_rating((int) $s['id']);
}
unset($s);

include __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <div><span class="eyebrow">Saved</span><h1 style="font-size:clamp(1.8rem,3.5vw,2.6rem);">Your favorites</h1></div>
        </div>

        <?php if (empty($favorites)): ?>
            <div class="empty-state">
                <p>You haven't saved any stylists yet.</p>
                <a href="<?= SITE_URL ?>/discover.php" class="btn btn-brass">Discover professionals</a>
            </div>
        <?php else: ?>
            <div class="stylist-grid">
                <?php foreach ($favorites as $s): ?>
                    <a class="stylist-card" href="<?= SITE_URL ?>/stylist-profile.php?id=<?= (int) $s['id'] ?>">
                        <div class="thumb">
                            <?php if ($s['cover_shot']): ?><img src="<?= SITE_URL . '/' . e($s['cover_shot']) ?>" alt=""><?php endif; ?>
                            <?php $cls = category_labels($s['categories'] ?? ''); if ($cls): ?><span class="category-badge"><?= e($cls[0]) ?></span><?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="card-top">
                                <?php if (!empty($s['avatar_path'])): ?><img src="<?= SITE_URL . '/' . e($s['avatar_path']) ?>" alt="" width="40" height="40" style="border-radius:50%;object-fit:cover;" class="card-avatar"><?php endif; ?>
                                <div>
                                    <h3><?= e($s['business_name']) ?></h3>
                                    <div class="location">📍 <?= e(trim(($s['suburb'] ?? '') . (($s['suburb'] && $s['city']) ? ', ' : '') . ($s['city'] ?? ''))) ?: 'South Africa' ?></div>
                                </div>
                            </div>
                            <?php if ($s['specialties']): ?><div class="tags"><?= e($s['specialties']) ?></div><?php endif; ?>
                            <div class="card-footer">
                                <?php if ($s['rating']['avg']): ?><span class="rating">★ <?= e((string) $s['rating']['avg']) ?> (<?= (int) $s['rating']['total'] ?>)</span><?php else: ?><span></span><?php endif; ?>
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
<?php include __DIR__ . '/includes/footer.php'; ?>
