<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Create your account';

$next = safe_next($_GET['next'] ?? $_POST['next'] ?? '');

if (current_user()) {
    redirect($next ?: '/dashboard.php');
}

$errors = [];
$name = $email = $phone = '';
$role = ($_GET['role'] ?? '') === 'stylist' ? 'stylist' : 'client';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $name = trim($_POST['name'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = in_array($_POST['role'] ?? '', ['stylist', 'client'], true) ? $_POST['role'] : 'client';

    if ($name === '' || strlen($name) < 2) $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if ($role === 'stylist' && !normalize_phone_digits($phone)) $errors[] = 'Beauty professionals need a valid phone number so clients can contact you.';
    if (empty($_POST['accept_terms'])) $errors[] = 'Please read and accept the Terms and Conditions and Privacy Policy to create an account.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email already exists.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            // Record when the user accepted the Terms and Conditions and Privacy Policy.
            $stmt = db()->prepare('INSERT INTO users (role, name, email, password_hash, phone, terms_accepted_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$role, $name, $email, $hash, $phone ?: null]);
        } catch (PDOException $ex) {
            // Older databases without the terms_accepted_at column: register anyway.
            $stmt = db()->prepare('INSERT INTO users (role, name, email, password_hash, phone) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$role, $name, $email, $hash, $phone ?: null]);
        }
        $userId = (int) db()->lastInsertId();

        if ($role === 'stylist') {
            $stmt = db()->prepare('INSERT INTO stylist_profiles (user_id, business_name) VALUES (?, ?)');
            $stmt->execute([$userId, $name . "'s Studio"]);
        }

        login_user(['id' => $userId]);
        flash('success', 'Welcome to BeautySpot!');
        redirect($role === 'stylist' ? '/onboarding.php' : ($next ?: '/discover.php'));
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container">
    <div class="auth-shell">
        <h1>Create your account</h1>
        <p style="color:#6B6151;margin-bottom:24px;">Join as a client to book, or a beauty professional (hair, make-up or nails) to get discovered.</p>

        <?php foreach ($errors as $err): ?>
            <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <div class="role-toggle">
                <label>
                    <input type="radio" name="role" value="client" <?= $role === 'client' ? 'checked' : '' ?>>
                    <span>I'm a client</span>
                </label>
                <label>
                    <input type="radio" name="role" value="stylist" <?= $role === 'stylist' ? 'checked' : '' ?>>
                    <span>I'm a beauty professional</span>
                </label>
            </div>
            <div class="field">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" value="<?= e($name) ?>" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
            </div>
            <div class="field">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="<?= e($phone) ?>">
            <div class="field-hint">Clients: optional. Beauty professionals: required — your phone number and email are shown on your public profile so clients can book you.</div>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8">
            </div>
            <div class="field">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
            </div>
            <label class="consent-label">
                <input type="checkbox" name="accept_terms" value="1" required <?= !empty($_POST['accept_terms']) ? 'checked' : '' ?>>
                <span>I have read and agree to the <a href="<?= SITE_URL ?>/terms.php" target="_blank" rel="noopener">Terms and Conditions</a> and the <a href="<?= SITE_URL ?>/privacy.php" target="_blank" rel="noopener">Privacy Policy</a>, including that a professional's phone number and email are shown on their public profile.</span>
            </label>
            <button type="submit" class="btn btn-primary btn-block">Create account</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="<?= SITE_URL ?>/login.php<?= $next ? '?next=' . rawurlencode($next) : '' ?>">Log in</a></p>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
