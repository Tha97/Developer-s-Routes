# BeautySpot — PHP Edition

A self-hosted beauty professional discovery platform for hairstylists, barbers, make-up artists and nail technicians: customizable profiles, portfolio uploads, map + radius search, bookings, favorites and reviews. Plain PHP + MySQL — no framework, no monthly platform fee, and you own every line of it.

## What's included

- **Client + professional accounts** (register, login, forgot/reset password)
- **Three professional categories**, and a professional can belong to more than one:
  - **Hairstylist / Barber**: Fades, Braids, Locs & Dreadlocks, Weaves & Extensions, Colour & Balayage, Relaxers, Natural Hair Care, Bridal Hair, Kids' Hair, Men's Cuts, Afro Hair, Silk Press, Wigs & Wig Installs, Cornrows, Hair Treatments
  - **Make-up Artist**: Bridal, Editorial & Fashion, Special FX, Airbrush, Glam, Natural/"No-Makeup" Look, Photoshoot, Theatrical & Stage, Prosthetic, HD, Matric Dance & Prom, Drag, Editorial Runway, Makeup Lessons
  - **Nail Technician**: Manicure, Pedicure, Gel Nails, Acrylic Nails, Dip Powder, Nail Art, French Tips, Ombré Nails, Nail Extensions, BIAB, Polygel, Nail Repair
- **Customizable profiles**: tagline, bio, price range, avatar/cover photo, and a choice of professional accent colors (Teal, Gold, Wine, Navy, Charcoal, Rose) that re-themes their public profile page
- **Portfolio uploads** with captions
- **Discover page**: keyword + category filter + free map radius search (drag pin, autocomplete address) — powered by Leaflet + OpenStreetMap, no API key or billing needed
- **Bookings**: the **Book now** button on a profile opens a booking form (`book.php`). Clients (who log in or sign up first, then come straight back to the form) pick a service, date and time and send a request. The professional is emailed and sees it in their dashboard, where they confirm or decline it; the client is emailed the answer. No payments are taken — it is a request only. Emails use the host's built-in mail, which some hosts delay or filter, so the dashboard is the reliable place to check.
- **Favorites**: clients can save professionals
- **Reviews**: 1–5 star ratings with comments
- **Mobile-friendly**: hamburger menu, touch-sized buttons, phone-friendly forms and booking cards, a 2-column portfolio, and a sticky "Book now / Call" bar on profile pages (tested from 320px phones up to desktop)

## Requirements (any standard shared host works)

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- `mod_rewrite` and `mod_headers` (both are on by default on cPanel-style hosting)
- The PHP `curl` extension (on by default on virtually all shared hosting) — used by the free geocoding proxy
- **No Google account, API key or billing required** — mapping runs entirely on free, open services (see below)

## 1. Set up the database

1. In cPanel (or your host's panel), create a MySQL database and a database user, and add that user to the database with all privileges.
2. Open **phpMyAdmin**, select your new database, go to **Import**, and upload `sql/schema.sql`. This creates all the tables (including the `categories`, `tagline` and `accent_color` columns used by the new profile customization).

   If you'd already deployed the original SalonSpot schema and have live data, don't re-import — instead run `sql/migrations/001_beautyspot_categories.sql`, which adds the new columns without touching existing rows.

## 2. Configure the app

Open `config/config.php` and fill in:

```php
define('DB_HOST', 'localhost');       // usually 'localhost' on shared hosting
define('DB_NAME', 'your_db_name');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

define('SITE_URL', 'https://yourdomain.co.za'); // no trailing slash
define('APP_SECRET', 'any-long-random-string');
```

There's nothing to configure for maps — no key, no signup, no billing. See "How the free mapping works" below for details.

## 3. Upload the files

Upload the entire contents of this folder to your hosting account (usually `public_html/` or a subdomain folder) via FTP, SFTP, or your host's File Manager. Keep the folder structure exactly as-is.

## 4. Set folder permissions

The `uploads/` folder (and its subfolders) need to be writable by PHP so stylists can upload photos:

```
chmod -R 755 uploads/
```

Most hosts default to this already; if uploads fail, try `775`.

## 5. You're live

Visit your domain. Register a stylist account to test the onboarding flow (profile → map pin → portfolio upload), and a client account to test discovery, favorites, and booking.

## Project structure

```
config/         Database + site configuration
includes/       Shared PHP (auth, helpers, header/footer templates)
includes/site.css   The whole site stylesheet (delivered inline by PHP, so it can never be served stale)
includes/js/        JavaScript for the Discover map and the location picker (also delivered inline)
assets/img/         Put your own logo here as logo.png (or .svg/.webp/.jpg) — it replaces the text logo everywhere.
                    (A logo placed directly in assets/ also works, or point SITE_LOGO in config/config.php at it.)
uploads/        User-uploaded images (avatars, covers, portfolio)
api/            JSON endpoints: stylist search, and the free geocoding proxy
dashboard/      Stylist-only dashboard pages (profile edit, portfolio, bookings)
sql/schema.sql  Full database schema — import this first
```

Everything is plain procedural PHP with PDO + prepared statements (no ORM, no Composer dependencies required), so it will run on virtually any shared PHP host without extra setup.

## How the free mapping works

BeautySpot never needs a Google Maps API key or any billing account. Instead:

- **Map display**: [Leaflet.js](https://leafletjs.com) (an open-source mapping library) draws the map, loaded from a free CDN. Map tiles come from [OpenStreetMap](https://www.openstreetmap.org), a free, community-maintained map — the small "© OpenStreetMap contributors" credit on the map is their one requirement, and it's already included.
- **Address search / autocomplete**: powered by [Nominatim](https://nominatim.org), OpenStreetMap's free geocoding service. Rather than calling it directly from the browser, `api/geocode.php` proxies the request through your own server — this correctly identifies BeautySpot to Nominatim (as their usage policy asks) and avoids browser CORS issues.
- **Distance search**: once a professional's address is geocoded to a latitude/longitude, the Discover page's radius search runs entirely in your own MySQL database (`api/search-stylists.php` uses the Haversine formula) — no external calls needed for every search.

**Usage policy note**: Nominatim's free tier is meant for reasonable, moderate use (their guidance is roughly one request per second, which is far more than a small-to-medium local directory will ever hit from real users typing addresses). If BeautySpot ever grows into very high-volume territory, you have two easy upgrade paths without changing the user experience: point `api/geocode.php` at a paid provider instead — [LocationIQ](https://locationiq.com) and [OpenCage](https://opencagedata.com) both have generous free tiers (thousands of requests/day) that don't require a credit card to sign up for, and both offer a very similar JSON API, or run your own Nominatim instance.

## Terms and Conditions & Privacy Policy

Two legal pages are included: `/terms.php` (Terms and Conditions) and `/privacy.php` (Privacy Policy, written for South Africa's POPIA). Both are linked from the footer and from the sign-up form. New users must tick an agreement box when they register, and the time they accepted is saved (`users.terms_accepted_at`).

Before going live:
1. Fill in the **Legal details** block in `config/config.php` (your full legal name, address, contact email and phone). Anything left empty shows as a highlighted placeholder on both pages. While the company isn't registered, use your own name — the pages read "trading as BeautySpot". When it is registered, change `LEGAL_NAME` and add `LEGAL_REG_NO`.
2. If you already imported an older schema, run `sql/migrations/002_terms_accepted.sql` to add the consent column (registration works without it, but no consent time is recorded).
3. Register yourself as the Information Officer with the Information Regulator (free, online at inforegulator.org.za).
4. Have a South African attorney review both pages. Update them (and the "last updated" date inside each file) if you add payments, analytics, advertising or email marketing.

## Home page picture

The large picture at the top of the home page comes from the profile named in `HERO_MAIN_PROFILE` in `config/config.php` (any part of the business name, e.g. `Tsholofelo`). It shows that profile's cover photo, or its latest portfolio photo if there's no cover. It is a single picture. If no profile matches the name, the newest profile with a photo is used instead.

## Notes & things worth doing next

- **Email**: password resets use PHP's built-in `mail()`. Most shared hosts support this out of the box, but deliverability is often better if you switch to your host's SMTP relay or a transactional email service later.
- **Payments**: there's no payment processing — bookings are request/confirm only, same as a phone call. If you want deposits later, PayFast is the common South African gateway to integrate.
- **Image optimization**: uploaded images aren't resized server-side. For a lot of traffic, consider adding a resize step (GD or Imagick, both usually available on shared hosting) so large phone photos don't slow down the discover page.
- **Backups**: since this is now fully yours, set up scheduled MySQL backups (most cPanel hosts have a one-click backup tool) — nothing to lose to a subscription lapsing anymore.
