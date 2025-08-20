-- GameTopUp Premium Database Schema
-- This file contains the complete database schema for the game top-up platform

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS gametopup CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE gametopup;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Games table
CREATE TABLE IF NOT EXISTS `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game packages table
CREATE TABLE IF NOT EXISTS `game_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `diamonds` int(11) NOT NULL,
  `price_bdt` decimal(10,2) NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  KEY `code` (`code`),
  CONSTRAINT `game_packages_game_id_fk` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Currencies table
CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `exchange_rate` decimal(10,6) NOT NULL DEFAULT 1.000000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment methods table
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `game_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `player_id` varchar(255) NOT NULL,
  `server_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'BDT',
  `payment_method` varchar(100) NOT NULL,
  `status` enum('pending','processing','completed','cancelled','failed') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`),
  KEY `game_id` (`game_id`),
  KEY `package_id` (`package_id`),
  KEY `status` (`status`),
  CONSTRAINT `orders_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_game_id_fk` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  CONSTRAINT `orders_package_id_fk` FOREIGN KEY (`package_id`) REFERENCES `game_packages` (`id`)
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

-- Insert default data
INSERT INTO `currencies` (`code`, `name`, `symbol`, `exchange_rate`) VALUES
('BDT', 'Bangladeshi Taka', '৳', 1.000000),
('USD', 'US Dollar', '$', 0.0091),
('EUR', 'Euro', '€', 0.0084),
('INR', 'Indian Rupee', '₹', 0.76),
('PKR', 'Pakistani Rupee', '₨', 2.52);

INSERT INTO `payment_methods` (`name`, `code`, `icon`, `description`, `sort_order`) VALUES
('Credit Card', 'credit_card', 'fas fa-cc-visa', 'Pay with credit or debit card', 1),
('Mobile Banking', 'mobile_banking', 'fas fa-mobile-alt', 'Pay with mobile banking', 2),
('E-Wallet', 'e_wallet', 'fas fa-wallet', 'Pay with digital wallet', 3),
('Cryptocurrency', 'crypto', 'fas fa-coins', 'Pay with cryptocurrency', 4);

INSERT INTO `games` (`name`, `slug`, `description`, `icon`, `banner_image`, `sort_order`) VALUES
('Free Fire', 'freefire', 'Battle Royale mobile game', 'fas fa-fire', NULL, 1),
('Mobile Legends', 'mlbb', 'MOBA mobile game', 'fas fa-crown', NULL, 2),
('PUBG Mobile', 'pubg', 'Battle Royale mobile game', 'fas fa-crosshairs', NULL, 3),
('Call of Duty', 'cod', 'FPS mobile game', 'fas fa-skull', NULL, 4),
('Genshin Impact', 'genshin', 'Action RPG mobile game', 'fas fa-wind', NULL, 5),
('Valorant', 'valorant', 'Tactical FPS game', 'fas fa-bolt', NULL, 6),
('League of Legends', 'lol', 'MOBA PC game', 'fas fa-fist-raised', NULL, 7),
('Fortnite', 'fortnite', 'Battle Royale game', 'fas fa-umbrella-beach', NULL, 8);

-- Insert sample packages for Free Fire
INSERT INTO `game_packages` (`game_id`, `name`, `code`, `diamonds`, `price_bdt`, `is_featured`, `sort_order`) VALUES
(1, '25 Diamonds', 'd25', 25, 50.00, 0, 1),
(1, '50 Diamonds', 'd50', 50, 100.00, 0, 2),
(1, '115 Diamonds', 'd115', 115, 220.00, 1, 3),
(1, '230 Diamonds', 'd230', 230, 420.00, 0, 4),
(1, '610 Diamonds', 'd610', 610, 1000.00, 0, 5);

-- Insert sample packages for Mobile Legends
INSERT INTO `game_packages` (`game_id`, `name`, `code`, `diamonds`, `price_bdt`, `is_featured`, `sort_order`) VALUES
(2, '50 Diamonds', 'd50', 50, 120.00, 0, 1),
(2, '100 Diamonds', 'd100', 100, 240.00, 0, 2),
(2, '200 Diamonds', 'd200', 200, 460.00, 1, 3),
(2, '500 Diamonds', 'd500', 500, 1100.00, 0, 4);

-- Insert sample packages for PUBG Mobile
INSERT INTO `game_packages` (`game_id`, `name`, `code`, `diamonds`, `price_bdt`, `is_featured`, `sort_order`) VALUES
(3, '100 UC', 'd100', 100, 110.00, 0, 1),
(3, '250 UC', 'd250', 250, 270.00, 0, 2),
(3, '500 UC', 'd500', 500, 530.00, 1, 3),
(3, '1000 UC', 'd1000', 1000, 1050.00, 0, 4);

-- Insert default settings
INSERT INTO `settings` (`key`, `value`) VALUES
('site_name', 'GameTopUp Premium'),
('site_description', 'Fast and secure game top-up center'),
('maintenance_mode', 'off'),
('default_currency', 'BDT'),
('logo_url', ''),
('banner_slider_enabled', 'on'),
('newsletter_enabled', 'on');

-- Create admin user (password: admin123)
INSERT INTO `users` (`email`, `password`, `name`, `is_verified`, `is_admin`) VALUES
('admin@gametopup.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 1, 1);