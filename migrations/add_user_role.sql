-- Add 'role' column to users table for role-based access control
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user';
-- To manually set an admin, run:
-- UPDATE users SET role = 'admin' WHERE id = [ADMIN_USER_ID];
