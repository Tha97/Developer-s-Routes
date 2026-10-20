-- Run this ONLY if you already imported the original schema.sql and have live data.
-- If you're starting fresh, just import sql/schema.sql — you don't need this file.

ALTER TABLE stylist_profiles
    ADD COLUMN tagline VARCHAR(160) DEFAULT NULL AFTER business_name,
    ADD COLUMN categories VARCHAR(100) DEFAULT NULL AFTER tagline,
    ADD COLUMN accent_color VARCHAR(20) NOT NULL DEFAULT 'teal' AFTER categories,
    MODIFY COLUMN specialties VARCHAR(500) DEFAULT NULL;

-- Existing profiles predate categories; default them all to "hair" so they don't disappear
-- from search. Professionals can update this from their dashboard at any time.
UPDATE stylist_profiles SET categories = 'hair' WHERE categories IS NULL;
