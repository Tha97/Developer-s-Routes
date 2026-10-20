-- Run this ONLY if you already imported an earlier schema.sql and have live data.
-- Fresh installs: just import sql/schema.sql instead.
-- Records when each new user agreed to the Terms and Conditions and Privacy Policy. Registration still works without it,
-- but you will not have a record of consent until you add this column.

ALTER TABLE users
    ADD COLUMN terms_accepted_at DATETIME DEFAULT NULL AFTER phone;
