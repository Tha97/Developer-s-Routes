<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#172326">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' . SITE_NAME : SITE_NAME ?></title>
<meta name="description" content="Discover and book South Africa's best hairstylists, barbers, make-up artists and nail technicians, browse real portfolios, and find talent near you.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Public+Sans:wght@400;500;600;700&display=swap">
<style><?= inline_css('includes/site.css') ?></style>
<?php if (!empty($accentOverrideCss)): ?>
<style><?= $accentOverrideCss ?></style>
<?php endif; ?>
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="<?= SITE_URL ?>/index.php" class="logo"><?= site_logo_html() ?></a>
        <button type="button" class="nav-toggle" id="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="site-nav">
            <span></span><span></span><span></span>
        </button>
        <div class="site-nav" id="site-nav">
        <ul class="nav-links">
            <li><a href="<?= SITE_URL ?>/discover.php">Discover</a></li>
            <li><a href="<?= SITE_URL ?>/index.php#for-professionals">For Professionals</a></li>
            <li><a href="<?= SITE_URL ?>/index.php#how-it-works">How it works</a></li>
            <?php if (!empty($__current_user)): ?>
                <li><a href="<?= SITE_URL ?>/dashboard.php">Dashboard</a></li>
                <?php if ($__current_user['role'] === 'client'): ?>
                    <li><a href="<?= SITE_URL ?>/favorites.php">Favorites</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <div class="nav-cta">
            <?php if (!empty($__current_user)): ?>
                <span class="nav-hello" style="font-size:0.88rem;opacity:0.85;">Hi, <?= e(explode(' ', $__current_user['name'])[0]) ?></span>
                <a href="<?= SITE_URL ?>/logout.php" class="btn btn-outline btn-pill btn-sm" style="border-color:#B9AF9C;color:var(--paper);">Log out</a>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/login.php" class="btn btn-outline btn-pill btn-sm" style="border-color:#B9AF9C;color:var(--paper);">Log in</a>
                <a href="<?= SITE_URL ?>/register.php" class="btn btn-brass btn-pill btn-sm">Join BeautySpot</a>
            <?php endif; ?>
        </div>
        </div>
    </div>
</header>
<?php $flashError = flash('error'); $flashSuccess = flash('success'); ?>
<?php if ($flashError || $flashSuccess): ?>
<div class="container" style="padding-top:24px;">
    <?php if ($flashError): ?><div class="alert alert-error"><?= e($flashError) ?></div><?php endif; ?>
    <?php if ($flashSuccess): ?><div class="alert alert-success"><?= e($flashSuccess) ?></div><?php endif; ?>
</div>
<?php endif; ?>
