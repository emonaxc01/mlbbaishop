INSERT INTO products (name, slug, sku, image_url, price, stock, status, description, created_at, updated_at) VALUES
 ('Free Fire Diamonds', 'free-fire-diamonds', 'FF-BASE', NULL, 50.00, 9999, 1, 'Top up diamonds for Free Fire', NOW(), NOW()),
 ('Mobile Legends Diamonds', 'mlbb-diamonds', 'MLBB-BASE', NULL, 120.00, 9999, 1, 'Top up diamonds for MLBB', NOW(), NOW()),
 ('PUBG UC', 'pubg-uc', 'PUBG-BASE', NULL, 110.00, 9999, 1, 'Top up UC for PUBG', NOW(), NOW()),
 ('Valorant Points', 'valorant-points', 'VAL-BASE', NULL, 150.00, 9999, 1, 'Top up Valorant Points', NOW(), NOW()),
 ('Genshin Crystals', 'genshin-crystals', 'GEN-BASE', NULL, 100.00, 9999, 1, 'Top up Genesis Crystals', NOW(), NOW());

INSERT INTO product_variations (product_id, name, sku, price, stock, status, created_at, updated_at)
SELECT p.id, v.name, v.sku, v.price, 9999, 1, NOW(), NOW()
FROM products p
JOIN (
  SELECT 'free-fire-diamonds' slug, '25 Diamonds' name, 'FF-25' sku, 50.00 price UNION ALL
  SELECT 'free-fire-diamonds', '50 Diamonds', 'FF-50', 100.00 UNION ALL
  SELECT 'free-fire-diamonds', '115 Diamonds', 'FF-115', 220.00 UNION ALL
  SELECT 'free-fire-diamonds', '230 Diamonds', 'FF-230', 420.00 UNION ALL
  SELECT 'free-fire-diamonds', '610 Diamonds', 'FF-610', 1000.00 UNION ALL

  SELECT 'mlbb-diamonds', '50 Diamonds', 'MLBB-50', 120.00 UNION ALL
  SELECT 'mlbb-diamonds', '100 Diamonds', 'MLBB-100', 240.00 UNION ALL
  SELECT 'mlbb-diamonds', '200 Diamonds', 'MLBB-200', 460.00 UNION ALL
  SELECT 'mlbb-diamonds', '500 Diamonds', 'MLBB-500', 1100.00 UNION ALL

  SELECT 'pubg-uc', '100 UC', 'PUBG-100', 110.00 UNION ALL
  SELECT 'pubg-uc', '250 UC', 'PUBG-250', 270.00 UNION ALL
  SELECT 'pubg-uc', '500 UC', 'PUBG-500', 530.00 UNION ALL
  SELECT 'pubg-uc', '1000 UC', 'PUBG-1000', 1050.00 UNION ALL

  SELECT 'valorant-points', '125 Points', 'VAL-125', 150.00 UNION ALL
  SELECT 'valorant-points', '400 Points', 'VAL-400', 450.00 UNION ALL
  SELECT 'valorant-points', '1000 Points', 'VAL-1000', 1100.00 UNION ALL
  SELECT 'valorant-points', '2050 Points', 'VAL-2050', 2200.00 UNION ALL

  SELECT 'genshin-crystals', '60 Crystals', 'GEN-60', 100.00 UNION ALL
  SELECT 'genshin-crystals', '300 Crystals', 'GEN-300', 450.00 UNION ALL
  SELECT 'genshin-crystals', '980 Crystals', 'GEN-980', 1400.00 UNION ALL
  SELECT 'genshin-crystals', '1980 Crystals', 'GEN-1980', 2700.00
) v ON v.slug = p.slug;
