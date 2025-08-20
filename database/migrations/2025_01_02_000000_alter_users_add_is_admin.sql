ALTER TABLE users
  ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER is_verified;

CREATE INDEX idx_users_is_admin ON users(is_admin);
