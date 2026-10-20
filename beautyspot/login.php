<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Log in';

$next = safe_next($_GET['next'] ?? $_POST['next'] ?? '');

if (current_user()) {
    redirect($next ?: '/dashboard.php');
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors[] = 'Incorrect email or password.';
    } else {
        login_user($user);
        redirect($next ?: '/dashboard.php');
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container">
    <div class="auth-shell">
        <h1>Welcome back</h1>
        <p style="color:#6B6151;margin-bottom:24px;">Log in to manage your profile or bookings.</p>

        <?php foreach ($errors as $err): ?>
            <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </form>
        <p class="auth-switch"><a href="<?= SITE_URL ?>/forgot-password.php">Forgot your password?</a></p>
        <p class="auth-switch">New to BeautySpot? <a href="<?= SITE_URL ?>/register.php<?= $next ? '?next=' . rawurlencode($next) : '' ?>">Create an account</a></p>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
