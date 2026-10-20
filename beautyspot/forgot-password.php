<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Forgot password';

$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim(strtolower($_POST['email'] ?? ''));

    $stmt = db()->prepare('SELECT id, name FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Always show the same message, whether or not the account exists (avoid leaking which emails are registered).
    $sent = true;

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $stmt = db()->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $token, $expires]);

        $resetLink = SITE_URL . '/reset-password.php?token=' . $token;
        $subject = 'Reset your BeautySpot password';
        $body = "Hi {$user['name']},\n\nClick the link below to reset your BeautySpot password. This link expires in 1 hour.\n\n{$resetLink}\n\nIf you didn't request this, you can ignore this email.";
        $headers = 'From: no-reply@' . parse_url(SITE_URL, PHP_URL_HOST);

        // mail() requires your host to have outbound mail configured (most shared hosting does).
        @mail($email, $subject, $body, $headers);
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container">
    <div class="auth-shell">
        <h1>Reset your password</h1>
        <?php if ($sent): ?>
            <div class="alert alert-success">If an account exists for that email, we've sent a reset link. Check your inbox (and spam folder).</div>
        <?php else: ?>
            <p style="color:#6B6151;margin-bottom:24px;">Enter your email and we'll send you a reset link.</p>
            <form method="post" novalidate>
                <?= csrf_field() ?>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send reset link</button>
            </form>
        <?php endif; ?>
        <p class="auth-switch"><a href="<?= SITE_URL ?>/login.php">Back to log in</a></p>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
