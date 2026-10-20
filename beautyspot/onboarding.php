<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Set up your profile';
$user = require_role('stylist');

$stmt = db()->prepare('SELECT * FROM stylist_profiles WHERE user_id = ?');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

$categories = professional_categories();
$specialtyCatalog = specialty_catalog();
$accentPresets = accent_color_presets();

$selectedCategories = $profile['categories'] ? explode(',', $profile['categories']) : [];
$selectedSpecialties = $profile['specialties'] ? array_map('trim', explode(',', $profile['specialties'])) : [];
$selectedAccent = $profile['accent_color'] ?? 'teal';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $businessName = trim($_POST['business_name'] ?? '');
    $tagline = trim($_POST['tagline'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $postedCategories = array_intersect($_POST['categories'] ?? [], array_keys($categories));
    $postedSpecialties = $_POST['specialties'] ?? [];
    $priceRange = trim($_POST['price_range'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $suburb = trim($_POST['suburb'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $lat = $_POST['lat'] !== '' ? (float) $_POST['lat'] : null;
    $lng = $_POST['lng'] !== '' ? (float) $_POST['lng'] : null;
    $accentColor = array_key_exists($_POST['accent_color'] ?? '', $accentPresets) ? $_POST['accent_color'] : 'teal';

    if (!empty($_FILES['avatar']['name'])) {
        try {
            $avatarPath = handle_image_upload($_FILES['avatar'], 'avatars');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
    if (!empty($_FILES['cover']['name'])) {
        try {
            $coverPath = handle_image_upload($_FILES['cover'], 'covers');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    // Only keep specialty tags that actually belong to one of the catalogs for the selected categories.
    $validSpecialties = [];
    foreach ($postedCategories as $cat) {
        $validSpecialties = array_merge($validSpecialties, $specialtyCatalog[$cat] ?? []);
    }
    $cleanSpecialties = array_values(array_intersect($postedSpecialties, $validSpecialties));

    if ($businessName === '') $errors[] = 'Please enter a business or studio name.';
    if (empty($postedCategories)) $errors[] = 'Please choose at least one category (hair, make-up or nails).';
    if (!$lat || !$lng) $errors[] = 'Please choose your location from the address search so clients can find you on the map.';

    if (!$errors) {
        $sql = 'UPDATE stylist_profiles SET business_name=?, tagline=?, categories=?, accent_color=?, bio=?, specialties=?, price_range=?, address=?, suburb=?, city=?, lat=?, lng=?, is_published=1';
        $params = [
            $businessName, $tagline ?: null, implode(',', $postedCategories), $accentColor,
            $bio, implode(', ', $cleanSpecialties), $priceRange, $address, $suburb, $city, $lat, $lng,
        ];
        if (isset($avatarPath)) { $sql .= ', avatar_path=?'; $params[] = $avatarPath; }
        if (isset($coverPath)) { $sql .= ', cover_path=?'; $params[] = $coverPath; }
        $sql .= ' WHERE user_id=?';
        $params[] = $user['id'];

        db()->prepare($sql)->execute($params);
        flash('success', "You're live on BeautySpot! Add some portfolio photos to start getting noticed.");
        redirect('/dashboard.php');
    }

    // Preserve the user's in-progress selections if validation failed.
    $selectedCategories = $postedCategories;
    $selectedSpecialties = $postedSpecialties;
    $selectedAccent = $accentColor;
}

include __DIR__ . '/includes/header.php';
?>
<div class="container">
    <div style="max-width:680px;margin:48px auto;">
        <h1>Welcome — let's set up your profile</h1>
        <p style="color:#6B6151;">This takes about two minutes. You can edit everything later from your dashboard.</p>

        <?php foreach ($errors as $err): ?>
            <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form method="post" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="business_name">Business / studio name</label>
                <input type="text" id="business_name" name="business_name" value="<?= e($profile['business_name'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label for="tagline">Tagline <span style="font-weight:400;color:#8F8570;">(shown under your name on your profile)</span></label>
                <input type="text" id="tagline" name="tagline" maxlength="160" placeholder="e.g. Precision fades &amp; classic barbering since 2015" value="<?= e($profile['tagline'] ?? '') ?>">
            </div>

            <div class="field">
                <label>What do you do? <span style="font-weight:400;color:#8F8570;">(choose all that apply)</span></label>
                <div class="category-toggle">
                    <?php foreach ($categories as $code => $label): ?>
                        <label class="category-toggle-option">
                            <input type="checkbox" name="categories[]" value="<?= e($code) ?>" class="category-checkbox" data-category="<?= e($code) ?>" <?= in_array($code, $selectedCategories, true) ? 'checked' : '' ?>>
                            <span><?= e($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php foreach ($specialtyCatalog as $catCode => $tags): ?>
                <div class="specialty-group" data-category-group="<?= e($catCode) ?>" style="display:<?= in_array($catCode, $selectedCategories, true) ? 'block' : 'none' ?>;">
                    <label><?= e($categories[$catCode]) ?> specialties</label>
                    <div class="tag-grid">
                        <?php foreach ($tags as $tag): ?>
                            <label class="tag-option">
                                <input type="checkbox" name="specialties[]" value="<?= e($tag) ?>" <?= in_array($tag, $selectedSpecialties, true) ? 'checked' : '' ?>>
                                <span><?= e($tag) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="field">
                <label for="bio">About you</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Tell clients about your style, experience and what makes your chair worth the trip."><?= e($profile['bio'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label for="price_range">Price range</label>
                <input type="text" id="price_range" name="price_range" placeholder="R150 – R450" value="<?= e($profile['price_range'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="cover">Cover photo <span style="font-weight:400;color:#8F8570;">(the banner image at the top of your profile)</span></label>
                <?php if (!empty($profile['cover_path'])): ?>
                    <img src="<?= SITE_URL . '/' . e($profile['cover_path']) ?>" style="width:100%;max-width:360px;border-radius:4px;margin-bottom:8px;display:block;">
                <?php endif; ?>
                <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp">
                <div class="field-hint">JPG, PNG or WEBP, max 5MB. Landscape photos work best here.</div>
            </div>
            <div class="field">
                <label for="avatar">Profile photo <span style="font-weight:400;color:#8F8570;">(shown as your small profile picture)</span></label>
                <?php if (!empty($profile['avatar_path'])): ?>
                    <img src="<?= SITE_URL . '/' . e($profile['avatar_path']) ?>" width="64" height="64" style="border-radius:50%;object-fit:cover;" class="avatar-preview">
                <?php endif; ?>
                <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
            </div>

            <div class="field">
                <label>Profile color <span style="font-weight:400;color:#8F8570;">(customize how your profile page looks)</span></label>
                <div class="accent-swatches">
                    <?php foreach ($accentPresets as $key => $preset): ?>
                        <label class="accent-swatch">
                            <input type="radio" name="accent_color" value="<?= e($key) ?>" <?= $selectedAccent === $key ? 'checked' : '' ?>>
                            <span class="swatch-dot" style="background:<?= e($preset['base']) ?>;"></span>
                            <span class="swatch-label"><?= e($preset['label']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="field">
                <label for="address_search">Find your location</label>
                <input type="text" id="address_search" placeholder="Start typing your salon's address...">
                <div class="field-hint">This sets the pin clients see on the map. Drag the marker to fine-tune.</div>
            </div>
            <div id="map-canvas" style="margin-bottom:18px;"></div>

            <input type="hidden" name="address" id="address" value="<?= e($profile['address'] ?? '') ?>">
            <input type="hidden" name="suburb" id="suburb" value="<?= e($profile['suburb'] ?? '') ?>">
            <input type="hidden" name="city" id="city" value="<?= e($profile['city'] ?? '') ?>">
            <input type="hidden" name="lat" id="lat" value="<?= e((string)($profile['lat'] ?? '')) ?>">
            <input type="hidden" name="lng" id="lng" value="<?= e((string)($profile['lng'] ?? '')) ?>">

            <button type="submit" class="btn btn-primary btn-block">Publish my profile</button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.category-checkbox').forEach(function (box) {
    box.addEventListener('change', function () {
        const group = document.querySelector('.specialty-group[data-category-group="' + this.dataset.category + '"]');
        if (group) group.style.display = this.checked ? 'block' : 'none';
    });
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>const SITE_URL = <?= json_encode(SITE_URL) ?>;</script>
<script><?= inline_js('includes/js/location-picker.js') ?></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
