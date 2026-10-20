<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pageTitle = 'Portfolio';
$user = require_role('stylist');

$stmt = db()->prepare('SELECT * FROM stylist_profiles WHERE user_id = ?');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'upload' && !empty($_FILES['photo']['name'])) {
        try {
            $path = handle_image_upload($_FILES['photo'], 'portfolio');
            $caption = trim($_POST['caption'] ?? '');
            $stmt = db()->prepare('INSERT INTO portfolio_images (stylist_id, image_path, caption) VALUES (?, ?, ?)');
            $stmt->execute([$profile['id'], $path, $caption ?: null]);
            flash('success', 'Photo added to your portfolio.');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $photoId = (int) ($_POST['photo_id'] ?? 0);
        $stmt = db()->prepare('SELECT image_path FROM portfolio_images WHERE id = ? AND stylist_id = ?');
        $stmt->execute([$photoId, $profile['id']]);
        $photo = $stmt->fetch();
        if ($photo) {
            db()->prepare('DELETE FROM portfolio_images WHERE id = ?')->execute([$photoId]);
            $fullPath = __DIR__ . '/../' . $photo['image_path'];
            if (is_file($fullPath)) @unlink($fullPath);
            flash('success', 'Photo removed.');
        }
    }

    if (!$errors) {
        redirect('/dashboard/portfolio.php');
    }
}

$stmt = db()->prepare('SELECT * FROM portfolio_images WHERE stylist_id = ? ORDER BY created_at DESC');
$stmt->execute([$profile['id']]);
$photos = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
    <div class="container dash-shell">
        <nav class="dash-nav">
            <a href="<?= SITE_URL ?>/dashboard.php">Overview</a>
            <a href="<?= SITE_URL ?>/dashboard/edit-profile.php">Edit profile</a>
            <a href="#" class="active">Portfolio</a>
        </nav>
        <div>
            <h1 style="font-size:2rem;">Portfolio</h1>
            <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

            <form method="post" enctype="multipart/form-data" class="dash-card">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="upload">
                <div class="field">
                    <label for="photo">Add a photo</label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" required>
                    <div class="field-hint">JPG, PNG or WEBP, max 5MB.</div>
                </div>
                <div class="field">
                    <label for="caption">Caption (optional)</label>
                    <input type="text" id="caption" name="caption" placeholder="e.g. Balayage + curtain fringe">
                </div>
                <button type="submit" class="btn btn-primary">Upload photo</button>
            </form>

            <?php if (empty($photos)): ?>
                <div class="empty-state"><p>No photos yet — add your first to start getting discovered.</p></div>
            <?php else: ?>
                <div class="portfolio-grid">
                    <?php foreach ($photos as $p): ?>
                        <figure>
                            <img src="<?= SITE_URL . '/' . e($p['image_path']) ?>" alt="<?= e($p['caption'] ?: '') ?>">
                            <figcaption style="display:flex;justify-content:space-between;align-items:center;">
                                <span><?= e($p['caption'] ?: '') ?></span>
                                <form method="post" onsubmit="return confirm('Remove this photo?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline">Remove</button>
                                </form>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
