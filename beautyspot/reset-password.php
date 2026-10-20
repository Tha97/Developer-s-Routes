<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Set a new password';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$errors = [];
$done = false;

$stmt = db()->prepare('SELECT pr.id, pr.user_id, pr.expires_at FROM password_resets pr WHERE pr.token = ? ORDER BY pr.id DESC LIMIT 1');
$stmt->execute([$token]);
$reset = $stmt->fetch();

$valid = $reset && strtotime($reset['expires_at']) > time();

if (!$token || !$valid) {
    $errors[] = 'This reset link is invalid or has expired. Please request a new one.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    csrf_check();
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $reset['user_id']]);
        db()->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$reset['user_id']]);
        $done = true;
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container">
    <div class="auth-shell">
        <h1>Set a new password</h1>

        <?php foreach ($errors as $err): ?>
            <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <?php if ($done): ?>
            <div class="alert alert-success">Your password has been updated.</div>
            <a href="<?= SITE_URL ?>/login.php" class="btn btn-primary btn-block">Log in</a>
        <?php elseif ($valid): ?>
            <form method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="field">
                    <label for="password">New password</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>
                <div class="field">
                    <label for="confirm_password">Confirm new password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update password</button>
            </form>
        <?php else: ?>
            <a href="<?= SITE_URL ?>/forgot-password.php" class="btn btn-primary btn-block">Request a new link</a>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
