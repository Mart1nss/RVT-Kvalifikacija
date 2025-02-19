-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for elevate-reads
CREATE DATABASE IF NOT EXISTS `elevate-reads` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `elevate-reads`;

-- Dumping structure for table elevate-reads.audit_logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `affected_item_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affected_item_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_admin_id_foreign` (`admin_id`),
  CONSTRAINT `audit_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.audit_logs: ~187 rows (approximately)
INSERT INTO `audit_logs` (`id`, `admin_id`, `action`, `action_type`, `description`, `affected_item_id`, `affected_item_name`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'Uploaded book', 'book', 'Uploaded new book', '1', 'Use Case', '2025-02-19 17:43:53', '2025-02-19 17:43:53'),
	(2, NULL, 'Uploaded book', 'book', 'Uploaded new book', '2', 'Test', '2025-02-19 17:44:49', '2025-02-19 17:44:49'),
	(3, NULL, 'Uploaded book', 'book', 'Uploaded new book', '3', 'Dummy Pdf', '2025-02-19 17:46:40', '2025-02-19 17:46:40'),
	(4, NULL, 'Uploaded book', 'book', 'Uploaded new book', '4', 'Atomic Habits', '2025-02-19 17:53:31', '2025-02-19 17:53:31'),
	(5, NULL, 'Uploaded book', 'book', 'Uploaded new book', '5', 'Smart Thinking', '2025-02-19 17:53:44', '2025-02-19 17:53:44'),
	(6, NULL, 'Uploaded book', 'book', 'Uploaded new book', '6', 'Master Your Thinking', '2025-02-19 17:53:59', '2025-02-19 17:53:59'),
	(7, NULL, 'Uploaded book', 'book', 'Uploaded new book', '7', 'Never Split The Difference', '2025-02-19 17:54:21', '2025-02-19 17:54:21'),
	(8, NULL, 'Uploaded book', 'book', 'Uploaded new book', '8', '100M Offers', '2025-02-19 17:54:34', '2025-02-19 17:54:34'),
	(9, NULL, 'Uploaded book', 'book', 'Uploaded new book', '9', 'Deep Work', '2025-02-19 17:54:53', '2025-02-19 17:54:53'),
	(10, NULL, 'Uploaded book', 'book', 'Uploaded new book', '10', 'Mind Management, Not Time Management', '2025-02-19 17:55:09', '2025-02-19 17:55:09'),
	(11, NULL, 'Uploaded book', 'book', 'Uploaded new book', '11', 'Goals', '2025-02-19 17:55:24', '2025-02-19 17:55:24'),
	(12, NULL, 'Uploaded book', 'book', 'Uploaded new book', '12', 'Great CEOs Are Lazy', '2025-02-19 17:55:42', '2025-02-19 17:55:42'),
	(13, NULL, 'Uploaded book', 'book', 'Uploaded new book', '13', 'The Unfair Advantage', '2025-02-19 17:55:58', '2025-02-19 17:55:58'),
	(14, NULL, 'Uploaded book', 'book', 'Uploaded new book', '14', 'Rich Dad Poor Dad', '2025-02-19 17:56:24', '2025-02-19 17:56:24'),
	(15, NULL, 'Uploaded book', 'book', 'Uploaded new book', '15', 'Think And Grow Rich', '2025-02-19 17:56:35', '2025-02-19 17:56:35'),
	(16, NULL, 'Uploaded book', 'book', 'Uploaded new book', '16', 'The Science Of Getting Rich', '2025-02-19 17:56:46', '2025-02-19 17:56:46'),
	(17, NULL, 'Uploaded book', 'book', 'Uploaded new book', '17', 'How to Win Friends and Influence People', '2025-02-19 17:57:20', '2025-02-19 17:57:20'),
	(18, NULL, 'Uploaded book', 'book', 'Uploaded new book', '18', 'The Power of Now', '2025-02-19 17:58:17', '2025-02-19 17:58:17'),
	(19, NULL, 'Uploaded book', 'book', 'Uploaded new book', '19', 'Man’s Search for Himself', '2025-02-19 17:58:33', '2025-02-19 17:58:33'),
	(20, NULL, 'Sent notification', 'notification', 'alo', NULL, 'Notification to all', '2025-02-19 18:01:18', '2025-02-19 18:01:18'),
	(21, NULL, 'Changed user role for', 'user', 'Changed user2\'s role from \'user\' to \'admin\'', '3', 'user2', '2025-02-19 18:03:11', '2025-02-19 18:03:11'),
	(22, NULL, 'Changed user role for', 'user', 'Changed user2\'s role from \'admin\' to \'user\'', '3', 'user2', '2025-02-19 18:03:15', '2025-02-19 18:03:15'),
	(23, NULL, 'Changed user role for', 'user', 'Changed janis\'s role from \'user\' to \'admin\'', '1', 'janis', '2025-02-19 18:03:17', '2025-02-19 18:03:17'),
	(24, 1, 'Deleted user', 'user', 'Deleted user account', '4', 'wzqz', '2025-02-19 18:03:53', '2025-02-19 18:03:53');

-- Dumping structure for table elevate-reads.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.categories: ~7 rows (approximately)
INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'Psychology', '2025-02-19 17:42:27', '2025-02-19 17:42:27'),
	(2, 'Sales & Negotiation', '2025-02-19 17:42:27', '2025-02-19 17:42:27'),
	(3, 'Productivity', '2025-02-19 17:42:27', '2025-02-19 17:42:27'),
	(4, 'Business & Career', '2025-02-19 17:42:27', '2025-02-19 17:42:27'),
	(5, 'Money & Investments', '2025-02-19 17:42:27', '2025-02-19 17:42:27'),
	(6, 'Health & Wellness', '2025-02-19 17:42:27', '2025-02-19 17:42:27'),
	(7, 'History', '2025-02-19 17:42:27', '2025-02-19 17:42:27'),
	(8, 'Relationships & Communication', '2025-02-19 17:42:27', '2025-02-19 17:42:27'),
	(9, 'Spirituality & Philosophy', '2025-02-19 17:42:27', '2025-02-19 17:42:27');

-- Dumping structure for table elevate-reads.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table elevate-reads.favorites
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `favorites_user_id_foreign` (`user_id`),
  KEY `favorites_product_id_foreign` (`product_id`),
  CONSTRAINT `favorites_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.favorites: ~2 rows (approximately)

-- Dumping structure for table elevate-reads.forums
CREATE TABLE IF NOT EXISTS `forums` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forums_user_id_foreign` (`user_id`),
  CONSTRAINT `forums_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.forums: ~8 rows (approximately)
INSERT INTO `forums` (`id`, `title`, `description`, `user_id`, `created_at`, `updated_at`) VALUES
	(2, 'janis', 'apraksts23', 1, '2025-02-19 18:04:11', '2025-02-19 18:04:11');

-- Dumping structure for table elevate-reads.forum_replies
CREATE TABLE IF NOT EXISTS `forum_replies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `forum_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_replies_forum_id_foreign` (`forum_id`),
  KEY `forum_replies_user_id_foreign` (`user_id`),
  CONSTRAINT `forum_replies_forum_id_foreign` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.forum_replies: ~3 rows (approximately)
INSERT INTO `forum_replies` (`id`, `content`, `forum_id`, `user_id`, `created_at`, `updated_at`) VALUES
	(2, 'tests', 2, 1, '2025-02-19 18:04:16', '2025-02-19 18:04:16');

-- Dumping structure for table elevate-reads.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.migrations: ~19 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(2, '2019_08_19_000000_create_failed_jobs_table', 1),
	(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(4, '2024_04_02_000001_create_categories_table', 1),
	(5, '2024_04_03_000000_create_products_table', 1),
	(6, '2024_05_03_000000_create_users_table', 1),
	(7, '2024_05_03_000003_create_reviews_table', 1),
	(8, '2024_05_03_000004_create_favorites_table', 1),
	(9, '2024_05_03_000005_create_read_later_table', 1),
	(10, '2024_05_03_000006_create_notifications_table', 1),
	(11, '2024_05_03_000007_create_notes_table', 1),
	(12, '2024_05_03_000008_create_user_preferences_table', 1),
	(13, '2024_05_03_000009_create_tickets_table', 1),
	(14, '2024_05_03_000010_create_ticket_responses_table', 1),
	(15, '2024_05_03_000011_create_audit_logs_table', 1),
	(16, '2024_05_03_000011_create_sent_notifications_table', 1),
	(17, '2025_02_19_140433_create_forums_table', 1),
	(18, '2025_02_19_140441_create_forum_replies_table', 1),
	(19, '2025_03_20_000000_create_notification_reads_table', 1);

-- Dumping structure for table elevate-reads.notes
CREATE TABLE IF NOT EXISTS `notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `note_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notes_user_id_foreign` (`user_id`),
  KEY `notes_product_id_foreign` (`product_id`),
  CONSTRAINT `notes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.notes: ~2 rows (approximately)

-- Dumping structure for table elevate-reads.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.notifications: ~87 rows (approximately)
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
	('2142085a-d2fe-46e4-babf-f7cdf0367f66', 'App\\Notifications\\AdminBroadcastNotification', 'App\\Models\\User', 1, '{"message":"alo","sent_notification_id":1}', '2025-02-19 18:03:47', '2025-02-19 18:01:18', '2025-02-19 18:03:47'),
	('90a48356-2165-4299-951a-4b162c0b205a', 'App\\Notifications\\AdminBroadcastNotification', 'App\\Models\\User', 2, '{"message":"alo","sent_notification_id":1}', NULL, '2025-02-19 18:01:18', '2025-02-19 18:01:18'),
	('a9dd63c4-feb2-472f-958b-75888c87536a', 'App\\Notifications\\AdminBroadcastNotification', 'App\\Models\\User', 4, '{"message":"alo","sent_notification_id":1}', '2025-02-19 18:01:21', '2025-02-19 18:01:18', '2025-02-19 18:01:21'),
	('cda0afac-6b1d-4a0b-a38c-03d529067ffe', 'App\\Notifications\\AdminBroadcastNotification', 'App\\Models\\User', 3, '{"message":"alo","sent_notification_id":1}', NULL, '2025-02-19 18:01:18', '2025-02-19 18:01:18');

-- Dumping structure for table elevate-reads.notification_reads
CREATE TABLE IF NOT EXISTS `notification_reads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `sent_notification_id` bigint unsigned NOT NULL,
  `read_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_reads_user_id_sent_notification_id_unique` (`user_id`,`sent_notification_id`),
  KEY `notification_reads_sent_notification_id_foreign` (`sent_notification_id`),
  CONSTRAINT `notification_reads_sent_notification_id_foreign` FOREIGN KEY (`sent_notification_id`) REFERENCES `sent_notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.notification_reads: ~41 rows (approximately)

-- Dumping structure for table elevate-reads.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table elevate-reads.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table elevate-reads.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.products: ~1 rows (approximately)
INSERT INTO `products` (`id`, `title`, `author`, `category_id`, `file`, `created_at`, `updated_at`, `is_public`) VALUES
	(1, 'Use Case', 'Es', 7, '9f792e9ee26db4a7ea577400483267a2.pdf', '2025-02-19 17:43:53', '2025-02-19 17:43:53', 1),
	(2, 'Test', 'test', 7, 'dbb57cedba7b40a23145d26ec09f3be6.pdf', '2025-02-19 17:44:49', '2025-02-19 17:44:49', 1),
	(3, 'Dummy Pdf', 'Dummy', 1, '2c86e2aa7eb4cb4db70379e28fab9b52.pdf', '2025-02-19 17:46:40', '2025-02-19 17:46:40', 1),
	(4, 'Atomic Habits', 'James Clear', 1, '98c38785803fd51f030f68ba62e51f0a.pdf', '2025-02-19 17:53:31', '2025-02-19 17:53:31', 1),
	(5, 'Smart Thinking', 'Matthew Allen', 1, 'ff9fdc6e03b8c1fafe8032f06884397b.pdf', '2025-02-19 17:53:44', '2025-02-19 17:53:44', 1),
	(6, 'Master Your Thinking', 'Thibaut Meurisse', 1, 'a25073b89e2ea942773e3bc0d142102c.pdf', '2025-02-19 17:53:59', '2025-02-19 17:53:59', 1),
	(7, 'Never Split The Difference', 'Chris Voss', 2, 'ea4ee991524beff20fbbd4681e99f136.pdf', '2025-02-19 17:54:21', '2025-02-19 17:54:21', 1),
	(8, '100M Offers', 'Alex Hormozi', 2, '8489d214cb481c84a167f94904061d00.pdf', '2025-02-19 17:54:34', '2025-02-19 17:54:34', 1),
	(9, 'Deep Work', 'Cal Newport', 3, '64c30498e6908069f3755401fef4897a.pdf', '2025-02-19 17:54:53', '2025-02-19 17:54:53', 1),
	(10, 'Mind Management, Not Time Management', 'David Kadavy', 3, '9cba079eac825990bac678e1cfbf1e50.pdf', '2025-02-19 17:55:09', '2025-02-19 17:55:09', 1),
	(11, 'Goals', 'Brian Tracy', 3, '50708794e0a9a97baaa1fa693a532ef3.pdf', '2025-02-19 17:55:24', '2025-02-19 17:55:24', 1),
	(12, 'Great CEOs Are Lazy', 'Jim Schleckser', 4, 'f34616237ba434a9bff332c4bae7a908.pdf', '2025-02-19 17:55:42', '2025-02-19 17:55:42', 1),
	(13, 'The Unfair Advantage', 'Ash Ali & Hasan Kubba', 4, '093fd2a45c21ed6d4de072f8ff0ee158.pdf', '2025-02-19 17:55:58', '2025-02-19 17:55:58', 1),
	(14, 'Rich Dad Poor Dad', 'Robert Kiyosaki', 5, 'c99eb1094ce5a17ada370d371e91e049.pdf', '2025-02-19 17:56:24', '2025-02-19 17:56:24', 1),
	(15, 'Think And Grow Rich', 'Napoleon Hill', 5, 'feaea54dd94d48d219ae31a6d1611584.pdf', '2025-02-19 17:56:35', '2025-02-19 17:56:35', 1),
	(16, 'The Science Of Getting Rich', 'Wallace D. Wattles', 5, 'd2f2b4ffbfde583902e5c0436ef6d308.pdf', '2025-02-19 17:56:46', '2025-02-19 17:56:46', 1),
	(17, 'How to Win Friends and Influence People', 'Dale Carnegie', 8, 'd76698b5b610e94f93d48b6f83f636a5.pdf', '2025-02-19 17:57:20', '2025-02-19 17:57:20', 1),
	(18, 'The Power of Now', 'Eckhart Tolle', 9, '38248395221d9623352d41c262827258.pdf', '2025-02-19 17:58:17', '2025-02-19 17:58:17', 1),
	(19, 'Man’s Search for Himself', 'Rollo May', 9, '688eb290b89b1e8c15cbdf75fa034ff5.pdf', '2025-02-19 17:58:33', '2025-02-19 17:58:33', 1);

-- Dumping structure for table elevate-reads.read_later
CREATE TABLE IF NOT EXISTS `read_later` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `read_later_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `read_later_product_id_foreign` (`product_id`),
  CONSTRAINT `read_later_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `read_later_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.read_later: ~9 rows (approximately)

-- Dumping structure for table elevate-reads.reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `review_score` int unsigned NOT NULL,
  `review_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.reviews: ~1 rows (approximately)
INSERT INTO `reviews` (`id`, `review_score`, `review_text`, `created_at`, `updated_at`, `user_id`, `product_id`) VALUES
	(2, 4, 'tests123', '2025-02-19 18:04:52', '2025-02-19 18:04:52', 1, 19);

-- Dumping structure for table elevate-reads.sent_notifications
CREATE TABLE IF NOT EXISTS `sent_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sent_notifications_sender_id_foreign` (`sender_id`),
  CONSTRAINT `sent_notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.sent_notifications: ~20 rows (approximately)

-- Dumping structure for table elevate-reads.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `assigned_admin_id` bigint unsigned DEFAULT NULL,
  `resolved_by` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_ticket_id_unique` (`ticket_id`),
  KEY `tickets_user_id_foreign` (`user_id`),
  KEY `tickets_assigned_admin_id_foreign` (`assigned_admin_id`),
  KEY `tickets_resolved_by_foreign` (`resolved_by`),
  CONSTRAINT `tickets_assigned_admin_id_foreign` FOREIGN KEY (`assigned_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.tickets: ~9 rows (approximately)

-- Dumping structure for table elevate-reads.ticket_responses
CREATE TABLE IF NOT EXISTS `ticket_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin_response` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_responses_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_responses_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_responses_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ticket_responses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.ticket_responses: ~14 rows (approximately)

-- Dumping structure for table elevate-reads.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usertype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_online` timestamp NULL DEFAULT NULL,
  `has_genre_preference_set` tinyint(1) NOT NULL DEFAULT '0',
  `last_read_book_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_last_read_book_id_foreign` (`last_read_book_id`),
  CONSTRAINT `users_last_read_book_id_foreign` FOREIGN KEY (`last_read_book_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.users: ~4 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `usertype`, `remember_token`, `created_at`, `updated_at`, `last_online`, `has_genre_preference_set`, `last_read_book_id`) VALUES
	(1, 'janis', 'janis@example.com', '2025-02-19 17:42:28', '$2y$12$JJ4qG43RxYtWyOG2l.GVYuEi/bDoHSUIyyGwFojrsyY/KL6M.Bed2', 'admin', NULL, '2025-02-19 17:42:28', '2025-02-19 18:05:13', '2025-02-19 18:05:13', 1, 19),
	(2, 'peters', 'peters@example.com', '2025-02-19 17:42:28', '$2y$12$7v0YcrXOjWASREs8EF66Cui2aXpWItUWt1oZd.MrsODqM/mbVoArK', 'user', NULL, '2025-02-19 17:42:28', '2025-02-19 17:42:28', NULL, 0, NULL),
	(3, 'user2', 'user2@example.com', '2025-02-19 17:42:28', '$2y$12$2c0mQXlnjl/YPwc1blEC/Om0W.RymanUip5zCTHMac0d3qflYZyPq', 'user', NULL, '2025-02-19 17:42:28', '2025-02-19 18:03:15', NULL, 0, NULL);

-- Dumping structure for table elevate-reads.user_preferences
CREATE TABLE IF NOT EXISTS `user_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_preferences_user_id_foreign` (`user_id`),
  KEY `user_preferences_category_id_foreign` (`category_id`),
  CONSTRAINT `user_preferences_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table elevate-reads.user_preferences: ~3 rows (approximately)
INSERT INTO `user_preferences` (`id`, `user_id`, `category_id`, `created_at`, `updated_at`) VALUES
	(4, 1, 5, '2025-02-19 18:03:38', '2025-02-19 18:03:38'),
	(5, 1, 2, '2025-02-19 18:03:38', '2025-02-19 18:03:38'),
	(6, 1, 1, '2025-02-19 18:03:38', '2025-02-19 18:03:38');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
