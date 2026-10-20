-- BeautySpot database schema
-- Import this file via phpMyAdmin (or `mysql -u user -p dbname < schema.sql`) before using the site.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------
-- users: both stylists and clients share this table, split by role
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role ENUM('stylist', 'client') NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    terms_accepted_at DATETIME DEFAULT NULL, -- when the user agreed to the Terms and Conditions and Privacy Policy
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- stylist_profiles: one per stylist/barber user
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS stylist_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    business_name VARCHAR(150) NOT NULL,
    tagline VARCHAR(160) DEFAULT NULL,     -- short customizable headline shown under the business name
    categories VARCHAR(100) DEFAULT NULL,  -- comma-separated: "hair,makeup,nails"
    accent_color VARCHAR(20) NOT NULL DEFAULT 'teal', -- customizes the public profile's accent color
    bio TEXT,
    specialties VARCHAR(500) DEFAULT NULL, -- comma-separated tags drawn from the category's specialty catalog
    price_range VARCHAR(50) DEFAULT NULL,  -- e.g. "R150 - R450"
    address VARCHAR(255) DEFAULT NULL,
    suburb VARCHAR(120) DEFAULT NULL,
    city VARCHAR(120) DEFAULT NULL,
    lat DECIMAL(10,7) DEFAULT NULL,
    lng DECIMAL(10,7) DEFAULT NULL,
    avatar_path VARCHAR(255) DEFAULT NULL,
    cover_path VARCHAR(255) DEFAULT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_stylist_user (user_id),
    CONSTRAINT fk_stylist_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_stylist_location (lat, lng)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- portfolio_images: uploaded work samples per stylist
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS portfolio_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stylist_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_portfolio_stylist FOREIGN KEY (stylist_id) REFERENCES stylist_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- favorites: clients saving stylists
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    stylist_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favorite (client_id, stylist_id),
    CONSTRAINT fk_fav_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_fav_stylist FOREIGN KEY (stylist_id) REFERENCES stylist_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- bookings: simple request/confirm flow (no payments)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    stylist_id INT UNSIGNED NOT NULL,
    requested_date DATE NOT NULL,
    requested_time TIME NOT NULL,
    service_note VARCHAR(500) DEFAULT NULL,
    status ENUM('pending','confirmed','declined','cancelled') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_booking_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_stylist FOREIGN KEY (stylist_id) REFERENCES stylist_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- reviews: left by a client for a stylist
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stylist_id INT UNSIGNED NOT NULL,
    client_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL, -- 1-5
    comment VARCHAR(600) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_review (stylist_id, client_id),
    CONSTRAINT fk_review_stylist FOREIGN KEY (stylist_id) REFERENCES stylist_profiles(id) ON DELETE CASCADE,
    CONSTRAINT fk_review_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- password_resets: forgot-password tokens
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
