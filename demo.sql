-- Demo SQL file for GameTopUp Premium E-commerce Platform
-- This file contains the complete database schema and sample data

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS gametopup CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE gametopup;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add admin column to users (if not exists)
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `is_admin` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_verified`;

-- Orders table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_items` int(11) NOT NULL DEFAULT 0,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add totals columns to orders (if not exists)
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `status`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `total_items` int(11) NOT NULL DEFAULT 0 AFTER `total_amount`;
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending' AFTER `total_items`;

-- Order items table
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `variation_id` (`variation_id`),
  CONSTRAINT `order_items_order_id_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order notes table
CREATE TABLE IF NOT EXISTS `order_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_notes_order_id_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `sku` varchar(100) DEFAULT NULL,
  `status` enum('active','draft','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product variations table
CREATE TABLE IF NOT EXISTS `product_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('size','color','other') NOT NULL DEFAULT 'other',
  `value` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `sku` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `status` (`status`),
  CONSTRAINT `product_variations_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product meta table
CREATE TABLE IF NOT EXISTS `product_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_meta_unique` (`product_id`,`meta_key`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_meta_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Variation meta table
CREATE TABLE IF NOT EXISTS `variation_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variation_id` int(11) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variation_meta_unique` (`variation_id`,`meta_key`),
  KEY `variation_id` (`variation_id`),
  CONSTRAINT `variation_meta_variation_id_fk` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Currencies table
CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `rate` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment methods table
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pages meta table (for SEO)
CREATE TABLE IF NOT EXISTS `pages_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(100) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_meta_unique` (`page`,`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data

-- Sample admin user (password: admin123)
INSERT INTO `users` (`email`, `password`, `is_verified`, `is_admin`, `wallet_balance`) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, 0.00);

-- Sample currencies
INSERT INTO `currencies` (`code`, `name`, `symbol`, `rate`, `is_default`) VALUES
('USD', 'US Dollar', '$', 1.0000, 1),
('EUR', 'Euro', '€', 0.8500, 0),
('GBP', 'British Pound', '£', 0.7300, 0),
('JPY', 'Japanese Yen', '¥', 110.0000, 0);

-- Sample payment methods
INSERT INTO `payment_methods` (`name`, `code`, `description`, `is_active`) VALUES
('Cash on Delivery', 'cod', 'Pay when you receive your order', 1),
('Test Payment', 'testpay', 'Test payment gateway for development', 1),
('Credit Card', 'creditcard', 'Pay with credit or debit card', 1);

-- Sample products
INSERT INTO `products` (`name`, `description`, `image`, `price`, `stock`, `sku`, `status`) VALUES
('Steam Gift Card', 'Add funds to your Steam wallet for games, software, and more', 'steam-card.jpg', 25.00, 100, 'STEAM-25', 'active'),
('PlayStation Plus', 'Access online multiplayer, monthly games, and exclusive discounts', 'ps-plus.jpg', 59.99, 50, 'PSPLUS-12M', 'active'),
('Xbox Game Pass', 'Access hundreds of high-quality games for one low monthly price', 'xbox-gamepass.jpg', 14.99, 75, 'XBOX-GP-MONTH', 'active'),
('Nintendo eShop Card', 'Add funds to your Nintendo account for digital games and DLC', 'nintendo-card.jpg', 20.00, 80, 'NINTENDO-20', 'active'),
('Roblox Robux', 'Purchase Robux to buy items, accessories, and more in Roblox', 'roblox-robux.jpg', 9.99, 200, 'ROBLOX-800', 'active');

-- Sample product variations
INSERT INTO `product_variations` (`product_id`, `name`, `type`, `value`, `price`, `stock`, `sku`, `status`) VALUES
(1, 'Steam Card Amount', 'other', '10 USD', 10.00, 50, 'STEAM-10', 'active'),
(1, 'Steam Card Amount', 'other', '25 USD', 25.00, 100, 'STEAM-25', 'active'),
(1, 'Steam Card Amount', 'other', '50 USD', 50.00, 75, 'STEAM-50', 'active'),
(2, 'PS Plus Duration', 'other', '1 Month', 9.99, 100, 'PSPLUS-1M', 'active'),
(2, 'PS Plus Duration', 'other', '3 Months', 24.99, 80, 'PSPLUS-3M', 'active'),
(2, 'PS Plus Duration', 'other', '12 Months', 59.99, 50, 'PSPLUS-12M', 'active'),
(3, 'Game Pass Duration', 'other', '1 Month', 14.99, 75, 'XBOX-GP-1M', 'active'),
(3, 'Game Pass Duration', 'other', '3 Months', 44.99, 60, 'XBOX-GP-3M', 'active'),
(4, 'Nintendo Card Amount', 'other', '10 USD', 10.00, 100, 'NINTENDO-10', 'active'),
(4, 'Nintendo Card Amount', 'other', '20 USD', 20.00, 80, 'NINTENDO-20', 'active'),
(4, 'Nintendo Card Amount', 'other', '50 USD', 50.00, 60, 'NINTENDO-50', 'active'),
(5, 'Robux Amount', 'other', '400 Robux', 4.99, 150, 'ROBLOX-400', 'active'),
(5, 'Robux Amount', 'other', '800 Robux', 9.99, 200, 'ROBLOX-800', 'active'),
(5, 'Robux Amount', 'other', '1700 Robux', 19.99, 100, 'ROBLOX-1700', 'active');

-- Sample product meta
INSERT INTO `product_meta` (`product_id`, `meta_key`, `meta_value`) VALUES
(1, 'platform', 'PC'),
(1, 'region', 'Global'),
(1, 'delivery_time', 'Instant'),
(2, 'platform', 'PlayStation'),
(2, 'region', 'Global'),
(2, 'delivery_time', 'Instant'),
(3, 'platform', 'Xbox'),
(3, 'region', 'Global'),
(3, 'delivery_time', 'Instant'),
(4, 'platform', 'Nintendo'),
(4, 'region', 'Global'),
(4, 'delivery_time', 'Instant'),
(5, 'platform', 'Roblox'),
(5, 'region', 'Global'),
(5, 'delivery_time', 'Instant');

-- Sample settings
INSERT INTO `settings` (`key`, `value`) VALUES
('site_title', 'GameTopUp Premium'),
('site_description', 'Premium gaming top-up and gift cards'),
('maintenance_mode', '0'),
('logo_path', ''),
('smtp_host', ''),
('smtp_port', '587'),
('smtp_username', ''),
('smtp_password', ''),
('smtp_from_address', ''),
('smtp_from_name', 'GameTopUp Premium'),
('default_currency', 'USD'),
('default_payment_method', 'testpay');

-- Sample pages meta (SEO)
INSERT INTO `pages_meta` (`page`, `meta_key`, `meta_value`) VALUES
('home', 'title', 'GameTopUp Premium - Gaming Gift Cards & Top-Ups'),
('home', 'description', 'Get instant delivery of gaming gift cards, top-ups, and digital codes for Steam, PlayStation, Xbox, Nintendo, and more.'),
('home', 'keywords', 'gaming, gift cards, top-up, steam, playstation, xbox, nintendo, robux');

-- Sample orders (optional - for testing)
INSERT INTO `orders` (`user_id`, `order_number`, `status`, `total_amount`, `total_items`, `payment_status`) VALUES
(1, 'ORD-2025-001', 'completed', 25.00, 1, 'paid'),
(1, 'ORD-2025-002', 'pending', 59.99, 1, 'pending');

-- Sample order items
INSERT INTO `order_items` (`order_id`, `product_id`, `variation_id`, `quantity`, `price`, `total`) VALUES
(1, 1, 2, 1, 25.00, 25.00),
(2, 2, 6, 1, 59.99, 59.99);

-- Sample order notes
INSERT INTO `order_notes` (`order_id`, `note`, `is_internal`) VALUES
(1, 'Order completed successfully. Code delivered via email.', 0),
(1, 'Customer requested instant delivery', 1),
(2, 'Payment pending - waiting for confirmation', 1);