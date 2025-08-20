CREATE TABLE IF NOT EXISTS payment_methods (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(64) NOT NULL UNIQUE,
  name VARCHAR(191) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  config JSON NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO payment_methods (code, name, enabled, created_at, updated_at) VALUES
 ('credit_card','Credit Card',1,NOW(),NOW()),
 ('mobile_banking','Mobile Banking',1,NOW(),NOW()),
 ('e_wallet','E-Wallet',1,NOW(),NOW()),
 ('crypto','Cryptocurrency',1,NOW(),NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);
