<?php
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_login();
$pageTitle = 'Dashboard';

if ($user['role'] === 'stylist') {
    $stmt = db()->prepare('SELECT * FROM stylist_profiles WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch();

    if (!$profile['is_published']) {
        redirect('/onboarding.php');
    }

    $portfolioCount = db()->prepare('SELECT COUNT(*) c FROM portfolio_images WHERE stylist_id = ?');
    $portfolioCount->execute([$profile['id']]);
    $portfolioCount = $portfolioCount->fetch()['c'];

    $favCount = db()->prepare('SELECT COUNT(*) c FROM favorites WHERE stylist_id = ?');
    $favCount->execute([$profile['id']]);
    $favCount = $favCount->fetch()['c'];

    $rating = average_rating($profile['id']);

    $stmt = db()->prepare(
        'SELECT b.id, b.requested_date, b.requested_time, b.service_note, b.status, u.name AS client_name, u.phone, u.email AS client_email
         FROM bookings b JOIN users u ON u.id = b.client_id
         WHERE b.stylist_id = ? ORDER BY b.requested_date DESC, b.requested_time DESC LIMIT 20'
    );
    $stmt->execute([$profile['id']]);
    $bookings = $stmt->fetchAll();

    include __DIR__ . '/includes/header.php';
    ?>
    <section class="section">
        <div class="container dash-shell">
            <nav class="dash-nav">
                <a href="#" class="active">Overview</a>
                <a href="<?= SITE_URL ?>/dashboard/edit-profile.php">Edit profile</a>
                <a href="<?= SITE_URL ?>/dashboard/portfolio.php">Portfolio</a>
            </nav>
            <div>
                <div class="page-head">
                    <div>
                        <h1>Welcome back, <?= e(explode(' ', $user['name'])[0]) ?></h1>
                        <p><?= e($profile['business_name']) ?></p>
                    </div>
                    <div class="page-head-actions">
                        <a href="<?= SITE_URL ?>/stylist-profile.php?id=<?= (int) $profile['id'] ?>" target="_blank" class="btn btn-outline btn-pill btn-sm">View public profile ↗</a>
                        <a href="<?= SITE_URL ?>/dashboard/edit-profile.php" class="btn btn-brass btn-pill btn-sm">Edit profile</a>
                    </div>
                </div>
                <div class="stat-row">
                    <div class="stat"><span class="stat-icon">🖼️</span><span class="num"><?= (int) $portfolioCount ?></span><span class="label">Portfolio photos</span></div>
                    <div class="stat"><span class="stat-icon">♥</span><span class="num"><?= (int) $favCount ?></span><span class="label">Times favourited</span></div>
                    <div class="stat"><span class="stat-icon">★</span><span class="num"><?= $rating['avg'] ?? '—' ?></span><span class="label"><?= $rating['total'] ?> review<?= $rating['total'] === 1 ? '' : 's' ?></span></div>
                </div>

                <div class="dash-card">
                    <h3>Booking requests</h3>
                    <?php if (empty($bookings)): ?>
                        <p class="muted">No booking requests yet. When a client taps <strong>Book now</strong> on your profile, their request appears here and we'll email you about it.</p>
                    <?php else: ?>
                        <div class="table-wrap"><table class="data-table">
                            <thead><tr><th>Client</th><th>Date</th><th>Time</th><th>Note</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td data-label="Client"><?= e($b['client_name']) ?><?php if ($b['phone']): ?><br><a href="tel:<?= e($b['phone']) ?>" class="booking-contact"><?= e($b['phone']) ?></a><?php endif; ?><?php if (!empty($b['client_email'])): ?><br><a href="mailto:<?= e($b['client_email']) ?>" class="booking-contact"><?= e($b['client_email']) ?></a><?php endif; ?></td>
                                    <td data-label="Date"><?= date('d M Y', strtotime($b['requested_date'])) ?></td>
                                    <td data-label="Time"><?= date('H:i', strtotime($b['requested_time'])) ?></td>
                                    <td data-label="Note"><?= e($b['service_note'] ?? '—') ?></td>
                                    <td data-label="Status"><span class="badge badge-<?= e($b['status']) ?>"><?= e(ucfirst($b['status'])) ?></span></td>
                                    <td data-label="">
                                        <?php if ($b['status'] === 'pending'): ?>
                                            <form method="post" action="<?= SITE_URL ?>/dashboard/update-booking.php" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-sm btn-primary">Confirm</button>
                                            </form>
                                            <form method="post" action="<?= SITE_URL ?>/dashboard/update-booking.php" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                                <input type="hidden" name="status" value="declined">
                                                <button type="submit" class="btn btn-sm btn-outline">Decline</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

// ---- Client dashboard ----
$stmt = db()->prepare(
    'SELECT b.id, b.requested_date, b.requested_time, b.service_note, b.status, sp.business_name, sp.id AS stylist_id
     FROM bookings b JOIN stylist_profiles sp ON sp.id = b.stylist_id
     WHERE b.client_id = ? ORDER BY b.requested_date DESC, b.requested_time DESC LIMIT 20'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

$favCount = db()->prepare('SELECT COUNT(*) c FROM favorites WHERE client_id = ?');
$favCount->execute([$user['id']]);
$favCount = $favCount->fetch()['c'];

include __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container dash-shell">
        <nav class="dash-nav">
            <a href="#" class="active">Overview</a>
            <a href="<?= SITE_URL ?>/favorites.php">Favorites</a>
            <a href="<?= SITE_URL ?>/discover.php">Discover stylists</a>
        </nav>
        <div>
            <div class="page-head">
                <div>
                    <h1>Welcome back, <?= e(explode(' ', $user['name'])[0]) ?></h1>
                    <p>Find and save your favourite beauty professionals.</p>
                </div>
                <div class="page-head-actions">
                    <a href="<?= SITE_URL ?>/discover.php" class="btn btn-brass btn-pill btn-sm">Discover professionals</a>
                </div>
            </div>
            <div class="stat-row">
                <div class="stat"><span class="stat-icon">♥</span><span class="num"><?= (int) $favCount ?></span><span class="label">Saved professionals</span></div>
                <div class="stat"><span class="stat-icon">📅</span><span class="num"><?= count($bookings) ?></span><span class="label">Booking requests</span></div>
            </div>

            <div class="dash-card">
                <h3>Your bookings</h3>
                <?php if (empty($bookings)): ?>
                    <p class="muted">No bookings yet. <a href="<?= SITE_URL ?>/discover.php" style="color:var(--brass-dark);font-weight:600;">Find a professional</a> and tap <strong>Book now</strong> on their profile to send a request.</p>
                <?php else: ?>
                    <div class="table-wrap"><table class="data-table">
                        <thead><tr><th>Stylist</th><th>Date</th><th>Time</th><th>Note</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td data-label="Professional"><a href="<?= SITE_URL ?>/stylist-profile.php?id=<?= $b['stylist_id'] ?>" style="color:var(--ink);font-weight:600;"><?= e($b['business_name']) ?></a></td>
                                <td data-label="Date"><?= date('d M Y', strtotime($b['requested_date'])) ?></td>
                                <td data-label="Time"><?= date('H:i', strtotime($b['requested_time'])) ?></td>
                                <td data-label="Note"><?= e($b['service_note'] ?? '—') ?></td>
                                <td data-label="Status"><span class="badge badge-<?= e($b['status']) ?>"><?= e(ucfirst($b['status'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
