CREATE TABLE IF NOT EXISTS currencies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code CHAR(3) NOT NULL UNIQUE,
  symbol VARCHAR(8) NOT NULL,
  rate DECIMAL(18,8) NOT NULL DEFAULT 1.0,
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO currencies (code, symbol, rate, is_default, enabled, created_at, updated_at) VALUES
 ('BDT','৳',1.0,1,1,NOW(),NOW()),
 ('USD','$',0.0091,0,1,NOW(),NOW()),
 ('EUR','€',0.0084,0,1,NOW(),NOW()),
 ('INR','₹',0.76,0,1,NOW(),NOW()),
 ('PKR','₨',2.52,0,1,NOW(),NOW())
ON DUPLICATE KEY UPDATE rate=VALUES(rate);
