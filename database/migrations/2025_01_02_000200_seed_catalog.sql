INSERT INTO products (code, name, status, created_at, updated_at) VALUES
 ('freefire','Free Fire',1,NOW(),NOW()),
 ('mlbb','Mobile Legends',1,NOW(),NOW()),
 ('pubg','PUBG Mobile',1,NOW(),NOW()),
 ('cod','Call of Duty',1,NOW(),NOW()),
 ('genshin','Genshin Impact',1,NOW(),NOW()),
 ('valorant','Valorant',1,NOW(),NOW()),
 ('lol','League of Legends',1,NOW(),NOW()),
 ('fortnite','Fortnite',1,NOW(),NOW());

-- seed packages per product
INSERT INTO packages (product_id, code, label, diamonds, price, created_at, updated_at)
SELECT p.id, x.code, x.label, x.diamonds, x.price, NOW(), NOW()
FROM products p
JOIN (
  SELECT 'freefire' AS product_code, 'd25' AS code, '25 Diamonds' AS label, 25 AS diamonds, 50 AS price
  UNION ALL SELECT 'freefire','d50','50 Diamonds',50,100
  UNION ALL SELECT 'freefire','d115','115 Diamonds',115,220
  UNION ALL SELECT 'freefire','d230','230 Diamonds',230,420
  UNION ALL SELECT 'freefire','d610','610 Diamonds',610,1000

  UNION ALL SELECT 'mlbb','d50','50 Diamonds',50,120
  UNION ALL SELECT 'mlbb','d100','100 Diamonds',100,240
  UNION ALL SELECT 'mlbb','d200','200 Diamonds',200,460
  UNION ALL SELECT 'mlbb','d500','500 Diamonds',500,1100

  UNION ALL SELECT 'pubg','d100','100 UC',100,110
  UNION ALL SELECT 'pubg','d250','250 UC',250,270
  UNION ALL SELECT 'pubg','d500','500 UC',500,530
  UNION ALL SELECT 'pubg','d1000','1000 UC',1000,1050

  UNION ALL SELECT 'cod','d100','100 CP',100,120
  UNION ALL SELECT 'cod','d200','200 CP',200,230
  UNION ALL SELECT 'cod','d500','500 CP',500,550
  UNION ALL SELECT 'cod','d1000','1000 CP',1000,1050

  UNION ALL SELECT 'genshin','d60','60 Crystals',60,100
  UNION ALL SELECT 'genshin','d300','300 Crystals',300,450
  UNION ALL SELECT 'genshin','d980','980 Crystals',980,1400
  UNION ALL SELECT 'genshin','d1980','1980 Crystals',1980,2700

  UNION ALL SELECT 'valorant','d125','125 Points',125,150
  UNION ALL SELECT 'valorant','d400','400 Points',400,450
  UNION ALL SELECT 'valorant','d1000','1000 Points',1000,1100
  UNION ALL SELECT 'valorant','d2050','2050 Points',2050,2200

  UNION ALL SELECT 'lol','d125','125 RP',125,130
  UNION ALL SELECT 'lol','d420','420 RP',420,430
  UNION ALL SELECT 'lol','d940','940 RP',940,950
  UNION ALL SELECT 'lol','d1650','1650 RP',1650,1700

  UNION ALL SELECT 'fortnite','d100','100 V-Bucks',100,110
  UNION ALL SELECT 'fortnite','d500','500 V-Bucks',500,520
  UNION ALL SELECT 'fortnite','d1000','1000 V-Bucks',1000,1020
  UNION ALL SELECT 'fortnite','d13500','13500 V-Bucks',13500,13500
) x ON x.product_code = p.code;
