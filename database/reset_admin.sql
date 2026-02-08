-- Reset/Create admin user with default credentials
-- This script ensures the admin user exists with the correct default password
-- Password: 12101991Qq!
-- Run this if you need to reset the admin password to default

-- Delete existing admin user (if any)
DELETE FROM users WHERE username = 'admin';

-- Create admin user with default credentials
-- Password hash for: 12101991Qq!
INSERT INTO users (email, username, hashed_password, role, is_active, is_superuser)
VALUES (
    'admin@example.com',
    'admin',
    '$2b$12$y4QVNPhuZfpLp1.xM6.NSeDnpD6I/wm.dSOXGrxV.HtXj6izHJLPa',
    'admin',
    TRUE,
    TRUE
);
