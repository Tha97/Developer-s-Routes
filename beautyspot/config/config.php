<?php
/**
 * BeautySpot — main configuration file.
 * Fill these in with your own hosting details before going live.
 */

// ---- Database (get these from your hosting cPanel -> MySQL Databases) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'beautyspot');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// ---- Maps & geocoding ----
// BeautySpot uses Leaflet + OpenStreetMap for maps, and OpenStreetMap's free
// Nominatim service (proxied through api/geocode.php) for address search.
// Both are free with no API key, no billing, and no signup required.
// See README.md if you later want to switch to a paid provider for higher volume.

// ---- Site ----
define('SITE_NAME', 'BeautySpot');
define('SITE_URL', 'https://yourdomain.co.za'); // no trailing slash
define('UPLOAD_MAX_BYTES', 5 * 1024 * 1024); // 5MB per image

// ---- Home page ----
// The big picture at the top of the home page comes from this profile. Use any part of the business
// name (e.g. 'Tsholofelo'). It shows that profile's cover photo (or its latest portfolio photo).
define('HERO_MAIN_PROFILE', 'Tsholofelo');

// ---- Logo (optional) ----
// Path to your logo image, relative to the site root. Leave this out and BeautySpot will use
// assets/img/logo.png (or any image placed directly in the assets/ folder) automatically.
// define('SITE_LOGO', 'assets/Untitleddesign-removebg-preview.png');

// ---- Legal details (shown on the Terms and Conditions and Privacy Policy pages) ----
// Fill these in. Anything left empty shows as a highlighted [placeholder] on /terms.php and /privacy.php.
// Until your company is registered, use your own full legal name — the pages say "trading as BeautySpot".
// When the company is registered, change LEGAL_NAME to the company name and add LEGAL_REG_NO.
define('LEGAL_NAME', '');      // Your full legal name (or registered company name once you have one)
define('LEGAL_REG_NO', '');    // Company registration number — leave empty until you have one
define('LEGAL_ADDRESS', '');   // Your physical address
define('CONTACT_EMAIL', '');   // Where users can reach you about the Terms, privacy requests and complaints
define('CONTACT_PHONE', '');

// ---- Session / security ----
// Generate your own random string for this (any 32+ char random string works).
define('APP_SECRET', 'change-this-to-a-random-string');

date_default_timezone_set('Africa/Johannesburg');

error_reporting(E_ALL);
ini_set('display_errors', '0'); // set to '1' temporarily while debugging locally
