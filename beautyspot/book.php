<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT sp.*, u.name AS owner_name, u.email AS owner_email
     FROM stylist_profiles sp JOIN users u ON u.id = sp.user_id
     WHERE sp.id = ? AND sp.is_published = 1'
);
$stmt->execute([$id]);
$stylist = $stmt->fetch();

if (!$stylist) {
    http_response_code(404);
    $pageTitle = 'Professional not found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="empty-state"><p>This profile doesn\'t exist or is not published.</p><a class="btn btn-brass" href="' . SITE_URL . '/discover.php">Back to Discover</a></div></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$bookUrlPath = '/book.php?id=' . $id;
$user = current_user();

// Visitors must be logged in as a client. After logging in or registering they come straight back here.
if (!$user) {
    flash('error', 'Please log in, or create a free client account, to send a booking request.');
    redirect('/login.php?next=' . rawurlencode($bookUrlPath));
}

$pageTitle = 'Book ' . $stylist['business_name'];
$accent = accent_color($stylist['accent_color'] ?? 'teal');
$accentOverrideCss = ":root { --brass: {$accent['base']}; --brass-dark: {$accent['dark']}; }";

$isClient = $user['role'] === 'client';
$isOwner = (int) $user['id'] === (int) $stylist['user_id'];

$location = trim(($stylist['suburb'] ?? '') . (($stylist['suburb'] && $stylist['city']) ? ', ' : '') . ($stylist['city'] ?? ''));
$services = array_values(array_filter(array_map('trim', explode(',', (string) $stylist['specialties']))));

$errors = [];
$values = ['service' => '', 'date' => '', 'time' => '', 'note' => '', 'phone' => $user['phone'] ?? ''];

if ($isClient && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $values['service'] = trim($_POST['service'] ?? '');
    $values['date'] = trim($_POST['requested_date'] ?? '');
    $values['time'] = trim($_POST['requested_time'] ?? '');
    $values['note'] = trim($_POST['note'] ?? '');
    $values['phone'] = trim($_POST['phone'] ?? '');

    $when = null;
    $d = DateTime::createFromFormat('!Y-m-d', $values['date']);
    $t = DateTime::createFromFormat('!H:i', $values['time']);
    if (!$d || $d->format('Y-m-d') !== $values['date']) {
        $errors[] = 'Please choose the date you would like.';
    } elseif (!$t || $t->format('H:i') !== $values['time']) {
        $errors[] = 'Please choose the time you would like.';
    } else {
        $when = new DateTime($values['date'] . ' ' . $values['time']);
        if ($when <= new DateTime()) {
            $errors[] = 'Please choose a date and time in the future.';
        } elseif ($when > new DateTime('+1 year')) {
            $errors[] = 'Please choose a date within the next year.';
        }
    }

    if ($values['service'] !== '' && $values['service'] !== 'Other' && !in_array($values['service'], $services, true)) {
        $values['service'] = '';
    }
    if (mb_strlen($values['note']) > 300) {
        $errors[] = 'Please keep your note under 300 characters.';
    }
    // A phone number lets the professional reach you. Use the one on your account, or ask for one.
    if (!normalize_phone_digits($values['phone'])) {
        $errors[] = 'Please add a valid phone number so the professional can reach you.';
    }
    if ($isOwner) {
        $errors[] = 'You cannot book your own profile.';
    }

    if (!$errors) {
        // Light spam protection: at most 5 requests per hour from one account.
        $stmt = db()->prepare('SELECT COUNT(*) FROM bookings WHERE client_id = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
        $stmt->execute([$user['id']]);
        if ((int) $stmt->fetchColumn() >= 5) {
            $errors[] = 'You have sent a lot of requests in a short time. Please wait a little before sending another.';
        }
    }
    if (!$errors) {
        $stmt = db()->prepare(
            "SELECT COUNT(*) FROM bookings WHERE client_id = ? AND stylist_id = ? AND requested_date = ? AND requested_time = ? AND status IN ('pending','confirmed')"
        );
        $stmt->execute([$user['id'], $id, $values['date'], $values['time'] . ':00']);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'You have already sent this professional a request for that date and time.';
        }
    }

    if (!$errors) {
        $request = trim(($values['service'] !== '' ? $values['service'] : '') . (($values['service'] !== '' && $values['note'] !== '') ? ' — ' : '') . $values['note']);
        $request = mb_substr($request, 0, 500);

        db()->prepare('INSERT INTO bookings (client_id, stylist_id, requested_date, requested_time, service_note) VALUES (?, ?, ?, ?, ?)')
            ->execute([$user['id'], $id, $values['date'], $values['time'], $request !== '' ? $request : null]);

        // Save the phone number on the account if it was missing or changed.
        if ($values['phone'] !== ($user['phone'] ?? '')) {
            db()->prepare('UPDATE users SET phone = ? WHERE id = ?')->execute([$values['phone'], $user['id']]);
        }

        // Tell the professional by email (best effort — they will also see it in their dashboard).
        $body = "Hi {$stylist['owner_name']},\n\n"
              . "{$user['name']} has sent you a booking request on BeautySpot.\n\n"
              . 'Preferred date: ' . $when->format('l, j F Y') . "\n"
              . 'Preferred time: ' . $when->format('H:i') . "\n"
              . 'Request: ' . ($request !== '' ? $request : 'Not specified') . "\n\n"
              . "Client: {$user['name']}\n"
              . "Phone: {$values['phone']}\n"
              . "Email: {$user['email']}\n\n"
              . 'Open your dashboard to confirm or decline it: ' . SITE_URL . "/dashboard.php\n\n"
              . '— ' . SITE_NAME;
        send_site_mail($stylist['owner_email'], 'New booking request from ' . $user['name'], $body, $user['email']);

        flash('success', 'Your booking request has been sent to ' . $stylist['business_name'] . '. You will see their answer here in your dashboard, and they may also contact you by phone or WhatsApp.');
        redirect('/dashboard.php');
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <a href="<?= SITE_URL ?>/stylist-profile.php?id=<?= $id ?>" class="back-link">← Back to <?= e($stylist['business_name']) ?></a>
        <div class="page-head" style="margin-top:14px;">
            <div>
                <h1>Request a booking</h1>
                <p>Tell <?= e($stylist['business_name']) ?> when you would like to come in.</p>
            </div>
        </div>

        <?php if (!$isClient): ?>
            <div class="dash-card" style="max-width:640px;">
                <h3>Booking requests are for client accounts</h3>
                <p class="muted">You are logged in as a beauty professional. To book someone else, log out and create a free client account.</p>
                <a href="<?= SITE_URL ?>/stylist-profile.php?id=<?= $id ?>" class="btn btn-outline btn-pill btn-sm">Back to the profile</a>
            </div>
        <?php else: ?>
        <div class="booking-layout">
            <form method="post" class="dash-card booking-form" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $id ?>">

                <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

                <?php if ($services): ?>
                    <div class="field">
                        <label for="service">What would you like?</label>
                        <select id="service" name="service">
                            <option value="">Choose a service (optional)</option>
                            <?php foreach ($services as $sv): ?>
                                <option value="<?= e($sv) ?>" <?= $values['service'] === $sv ? 'selected' : '' ?>><?= e($sv) ?></option>
                            <?php endforeach; ?>
                            <option value="Other" <?= $values['service'] === 'Other' ? 'selected' : '' ?>>Something else (describe below)</option>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="booking-datetime">
                    <div class="field">
                        <label for="requested_date">Preferred date</label>
                        <input type="date" id="requested_date" name="requested_date" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+1 year')) ?>" value="<?= e($values['date']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="requested_time">Preferred time</label>
                        <input type="time" id="requested_time" name="requested_time" step="900" value="<?= e($values['time']) ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label for="note">Anything they should know? <span class="optional">(optional)</span></label>
                    <textarea id="note" name="note" rows="3" maxlength="300" placeholder="e.g. Skin fade with a beard line-up. I have sensitive skin."><?= e($values['note']) ?></textarea>
                </div>

                <div class="field">
                    <label for="phone">Your phone number</label>
                    <input type="tel" id="phone" name="phone" value="<?= e($values['phone']) ?>" placeholder="082 123 4567" required>
                    <div class="field-hint">So <?= e($stylist['business_name']) ?> can confirm with you.</div>
                </div>

                <p class="booking-share">Booking as <strong><?= e($user['name']) ?></strong> (<?= e($user['email']) ?>). When you send this request, your name, phone number and email are shared with <?= e($stylist['business_name']) ?>. See our <a href="<?= SITE_URL ?>/privacy.php" target="_blank" rel="noopener">Privacy Policy</a>.</p>

                <button type="submit" class="btn btn-brass btn-pill btn-block">📅 Send booking request</button>
            </form>

            <aside class="booking-summary">
                <?php if ($stylist['cover_path']): ?>
                    <div class="booking-summary-cover"><img src="<?= SITE_URL . '/' . e($stylist['cover_path']) ?>" alt=""></div>
                <?php endif; ?>
                <div class="booking-summary-body">
                    <div class="booking-summary-top">
                        <?php if ($stylist['avatar_path']): ?>
                            <img src="<?= SITE_URL . '/' . e($stylist['avatar_path']) ?>" alt="" width="52" height="52" style="border-radius:50%;object-fit:cover;" class="card-avatar booking-avatar">
                        <?php endif; ?>
                        <div>
                            <h3><?= e($stylist['business_name']) ?></h3>
                            <div class="location">📍 <?= e($location) ?: 'South Africa' ?></div>
                        </div>
                    </div>
                    <?php foreach (category_labels($stylist['categories'] ?? '') as $cl): ?><span class="category-badge"><?= e($cl) ?></span><?php endforeach; ?>
                    <?php if ($stylist['price_range']): ?><p class="booking-price">💰 Typical prices: <?= e($stylist['price_range']) ?></p><?php endif; ?>
                    <ol class="booking-steps">
                        <li>Send your request.</li>
                        <li><?= e($stylist['business_name']) ?> confirms or declines it. You'll see the answer in your dashboard.</li>
                        <li>You pay the professional directly. BeautySpot doesn't take payments.</li>
                    </ol>
                </div>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
