<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="<?= SITE_URL ?>/index.php" class="logo" style="font-size:1.25rem;"><?= site_logo_html() ?></a>
                <p style="max-width:32ch;margin-top:10px;color:#8A9896;">Discover trusted local hair, make-up and nail professionals — all in one place. Real people. Real reviews. Local talent.</p>
            </div>
            <nav class="footer-nav">
                <a href="<?= SITE_URL ?>/discover.php">Discover</a>
                <a href="<?= SITE_URL ?>/index.php#for-professionals">For Professionals</a>
                <a href="<?= SITE_URL ?>/index.php#how-it-works">How it works</a>
                <a href="<?= SITE_URL ?>/terms.php">Terms and Conditions</a>
                <a href="<?= SITE_URL ?>/privacy.php">Privacy Policy</a>
            </nav>
            <!-- Update these three links to your real social pages (or remove any you don't use). -->
            <div class="footer-social">
                <a href="#" aria-label="Instagram" title="Add your Instagram link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="#" aria-label="Facebook" title="Add your Facebook link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="#" aria-label="TikTok" title="Add your TikTok link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                </a>
            </div>
        </div>
        <hr class="rule" style="border-color:rgba(255,255,255,0.12);">
        <div class="footer-bottom" style="margin-top:20px;">
            <p style="margin:0;">&copy; <?= date('Y') ?> BeautySpot. All rights reserved. &nbsp;·&nbsp; <a href="<?= SITE_URL ?>/terms.php">Terms</a> &nbsp;·&nbsp; <a href="<?= SITE_URL ?>/privacy.php">Privacy</a></p>
            <p style="margin:0;">🇿🇦 South Africa &nbsp;|&nbsp; Local talent. Real beauty.</p>
        </div>
    </div>
</footer>
<script>
(function () {
    var btn = document.getElementById('nav-toggle');
    var header = document.querySelector('.site-header');
    if (!btn || !header) return;
    function setOpen(open) {
        header.classList.toggle('nav-open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    }
    btn.addEventListener('click', function () { setOpen(!header.classList.contains('nav-open')); });
    document.querySelectorAll('#site-nav a').forEach(function (a) { a.addEventListener('click', function () { setOpen(false); }); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
    window.addEventListener('resize', function () { if (window.innerWidth > 960) setOpen(false); });
})();
</script>
</body>
</html>
