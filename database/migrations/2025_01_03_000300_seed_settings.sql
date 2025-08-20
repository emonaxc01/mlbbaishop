INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES
 ('site_title', 'GameTopUp Premium', NOW(), NOW()),
 ('maintenance_mode', 'off', NOW(), NOW()),
 ('site_logo_url', '', NOW(), NOW()),
 ('mail_host', '', NOW(), NOW()),
 ('mail_port', '', NOW(), NOW()),
 ('mail_username', '', NOW(), NOW()),
 ('mail_password', '', NOW(), NOW()),
 ('mail_from_address', '', NOW(), NOW()),
 ('mail_from_name', 'GameTopUp Premium', NOW(), NOW())
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);
