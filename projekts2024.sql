-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 11, 2024 at 11:26 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projekts2024`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`, `updated_at`) VALUES
(32, 3, 1, '2024-04-10 13:26:25', '2024-04-10 13:26:25'),
(35, 2, 1, '2024-04-12 19:13:14', '2024-04-12 19:13:14'),
(50, 1, 5, '2024-06-06 15:19:50', '2024-06-06 15:19:50'),
(51, 1, 6, '2024-06-06 17:08:43', '2024-06-06 17:08:43'),
(53, 1, 7, '2024-06-11 09:25:29', '2024-06-11 09:25:29');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(48, '2014_10_12_000000_create_users_table', 1),
(49, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(50, '2019_08_19_000000_create_failed_jobs_table', 1),
(51, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(52, '2024_03_03_193145_create_books_table', 1),
(53, '2024_03_10_172136_create_products_table', 1),
(54, '2024_03_27_180708_create_notes_table', 1),
(55, '2024_04_09_111812_create_favorites_table', 2),
(57, '2024_04_10_115301_create_notifications_table', 3),
(58, '2024_04_12_184947_create_notes_table', 4),
(59, '2024_04_30_210037_create_reviews_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` bigint UNSIGNED NOT NULL,
  `note_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `note_text`, `created_at`, `updated_at`, `user_id`, `product_id`) VALUES
(6, 'Visus projektus var sadalīt divās kategorijās: produkts un pasūtījums. Produkta gadījumā izstrādātājs pats \nizvirza sistēmas prasības ar mērķi izstrādāt sistēmu, ko kāds vēlēsies nopirkt. Pasūtījuma gadījumā ir otrādi – \nprasības definē pasūtītājs. hz\n\nksistējošo sistēmu analīze ir būtiska, jo informācijas tehnoloģija tiek plaši pielietota cilvēku ikdienā, gandrīz\nvisās tautsaimniecības jomās tiek kaut kas izmantots no tās, tāpēc, veicot līdzīgu sistēmu analīzi, var iegūt\npieredzi un idejas. Izstrādājot produktu, jārēķinās ar līdzīgiem tirgū esošajiem produktiem, nosakot sava un \nkonkurentu produkta vājās un stiprās puses. Šim mērķim var noderēt SVID analīze (stipro un vājo pušu, iespēju\nun draudu analīze), kas paredz aizpildīt tabulu ar četrām nodaļām (skat. 1. tabulu). SVID tabulā jāatzīmē gan \niekšējie, gan ārējie faktori. Ja sistēma tiek realizēta pēc pasūtījuma, pasūtītājs var minēt līdzīgas sistēmas un \npaskaidrot, kāpēc eksistējošās sistēmas viņu neapmierina. Tirgū esošās sistēmas var izmantot ideju \nģenerēšanai un labu/ sliktu piemēru analīzei.', '2024-04-12 17:09:36', '2024-04-12 19:13:03', 2, 1),
(7, 'k', '2024-04-12 17:12:19', '2024-04-12 19:08:27', 1, 1),
(16, 'font-family: sans-serif;\nfont-weight: 800;\ntext-transform: uppercase;font-family: sans-serif;\nfont-weight: 800;\ntext-transform: uppercase;font-family: sans-serif;\nfont-weight: 800;\ntext-transform: uppercase;font-family: sans-serif;\nfont-weight: 800;\ntext-transform: uppercase;font-family: sans-serif;\nfont-weight: 800;\ntext-transform: uppercase;font-family: sans-serif;\nfont-weight: 800;\ntext-transform: uppercase;', '2024-06-11 09:23:08', '2024-06-11 09:23:08', 1, 12);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'yaya', 1, '2024-04-10 10:18:23', '2024-04-10 11:15:44'),
(2, 2, 'yaya', 1, '2024-04-10 10:18:24', '2024-04-10 12:07:21'),
(5, 1, 'baigi liels teksts kipa', 1, '2024-04-10 10:28:41', '2024-04-10 11:14:06'),
(6, 2, 'baigi liels teksts kipa', 1, '2024-04-10 10:28:41', '2024-04-10 12:07:22'),
(7, 1, 'vel lielaks teksts par ieprieksejo tekstu', 1, '2024-04-10 10:29:27', '2024-04-10 11:14:03'),
(8, 2, 'vel lielaks teksts par ieprieksejo tekstu', 1, '2024-04-10 10:29:27', '2024-04-10 11:47:59'),
(9, 1, 'testings', 1, '2024-04-10 11:29:51', '2024-04-10 11:30:00'),
(10, 2, 'testings', 1, '2024-04-10 11:29:51', '2024-04-10 11:51:58'),
(11, 1, 'yoyo wassup', 1, '2024-04-10 14:45:13', '2024-04-10 15:38:44'),
(12, 2, 'yoyo wassup', 1, '2024-04-10 14:45:13', '2024-04-12 16:35:48'),
(13, 3, 'yoyo wassup', 1, '2024-04-10 14:45:13', '2024-04-10 14:45:59'),
(14, 1, 'another one', 1, '2024-04-10 14:45:50', '2024-04-12 17:11:39'),
(15, 2, 'another one', 1, '2024-04-10 14:45:50', '2024-04-12 18:38:57'),
(16, 3, 'another one', 0, '2024-04-10 14:45:50', '2024-04-10 14:45:50'),
(17, 1, 'zinojums', 1, '2024-04-10 15:39:17', '2024-04-12 17:11:40'),
(18, 2, 'zinojums', 1, '2024-04-10 15:39:17', '2024-04-12 18:38:58'),
(19, 3, 'zinojums', 0, '2024-04-10 15:39:17', '2024-04-10 15:39:17'),
(20, 1, 'attention', 1, '2024-04-12 18:38:05', '2024-04-12 18:38:08'),
(21, 2, 'attention', 1, '2024-04-12 18:38:05', '2024-04-12 18:57:33'),
(22, 3, 'attention', 0, '2024-04-12 18:38:05', '2024-04-12 18:38:05'),
(23, 1, 'hai', 1, '2024-04-12 18:59:10', '2024-04-12 18:59:12'),
(24, 2, 'hai', 1, '2024-04-12 18:59:10', '2024-05-20 16:40:29'),
(25, 3, 'hai', 0, '2024-04-12 18:59:10', '2024-04-12 18:59:10'),
(26, 1, 'aaaaa', 1, '2024-04-12 19:07:12', '2024-05-07 12:04:34'),
(27, 2, 'aaaaa', 1, '2024-04-12 19:07:12', '2024-05-20 16:40:28'),
(28, 3, 'aaaaa', 0, '2024-04-12 19:07:12', '2024-04-12 19:07:12'),
(29, 1, 'tests', 1, '2024-05-20 16:44:56', '2024-05-31 14:11:52'),
(30, 2, 'tests', 0, '2024-05-20 16:44:56', '2024-05-20 16:44:56'),
(31, 3, 'tests', 0, '2024-05-20 16:44:56', '2024-05-20 16:44:56'),
(32, 7, 'tests', 0, '2024-05-20 16:44:56', '2024-05-20 16:44:56'),
(33, 1, 'nigger', 1, '2024-06-06 16:07:52', '2024-06-06 16:08:29'),
(34, 2, 'nigger', 1, '2024-06-06 16:07:52', '2024-06-10 17:23:27'),
(35, 3, 'nigger', 0, '2024-06-06 16:07:52', '2024-06-06 16:07:52'),
(36, 7, 'nigger', 0, '2024-06-06 16:07:52', '2024-06-06 16:07:52');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_image` blob,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `author`, `category`, `cover_image`, `file`, `created_at`, `updated_at`) VALUES
(1, 'testings', 'nezinams1', 'pirma kateg', NULL, '1591d18339c4d11dee8c7a05c1c4be3b.pdf', '2024-03-27 16:11:35', '2024-06-10 16:57:31'),
(5, '48 Laws of Power', 'Robert Greene', 'Psychology', NULL, '36d8b1f16797c1cc65164769a2f69772.pdf', '2024-05-07 20:26:23', '2024-05-07 20:26:23'),
(6, 'Deep Work', 'Newport', 'Productivity', NULL, '2d9a157478ebdd9640968a7a885f3929.pdf', '2024-05-07 20:27:12', '2024-05-07 20:27:12'),
(7, 'Atomic Habits', 'James Clear', 'Psychology', NULL, '729a66f87a5a6ceb910e60038fca86f8.pdf', '2024-05-07 20:28:10', '2024-05-07 20:28:10'),
(9, 'Never Split The Difference', 'Chris Voss', 'Sales/Negotiation', NULL, '3c4bb0d07d82869f893fb25eafeef807.pdf', '2024-06-10 17:18:15', '2024-06-10 17:18:15'),
(12, 'Mind Mangement not Time Management', 'David Kadavy', 'Productivity', NULL, '8542c08616de1298209a959e40c162e0.pdf', '2024-06-11 09:11:19', '2024-06-11 09:11:19'),
(13, 'Goals', 'Brian Tracy', 'Productivity', NULL, '1dfe2ee6cede66529ce8a3d0157d678f.pdf', '2024-06-11 09:16:53', '2024-06-11 09:16:53'),
(14, '$100M Offers', 'Alex Hormozi', 'Business/Career', NULL, '6e8bc9a3f2dfa3940625c2504771a2e5.pdf', '2024-06-11 09:18:07', '2024-06-11 09:18:07'),
(15, 'Unfair Advantage', 'Ash Ali & Hasan Kubba', 'Business/Career', NULL, '01d1ae7dd8c22721d25b3d8e3927de44.pdf', '2024-06-11 09:19:19', '2024-06-11 09:19:19'),
(16, 'Mans Search For Himself', 'Rollo May', 'Psychology', NULL, '7601b38bcf3d3afde7b2235815cb1fc8.pdf', '2024-06-11 09:20:06', '2024-06-11 09:20:06'),
(17, 'Rich Dad Poor Dad', 'Robert T. Kiyosaki', 'Money/Investments', NULL, 'edffca456cb5ee1807c936a3abab73fc.pdf', '2024-06-11 09:21:10', '2024-06-11 09:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `review_score` int NOT NULL,
  `review_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `review_score`, `review_text`, `created_at`, `updated_at`, `user_id`, `product_id`) VALUES
(2, 1, 'nepatiik', '2024-04-30 19:42:44', '2024-04-30 19:42:44', 1, 1),
(5, 4, '#pdfContainer {\r\n    position: relative;\r\n    width: 100%;\r\n    height: 0;\r\n    padding-bottom: 60%; \r\n    overflow: hidden;\r\n}\r\n\r\n#pdfFrame {\r\n    position: absolute;\r\n    top: 0;\r\n    left: 0;\r\n    width: 100%;\r\n    height: 100%;\r\n}#pdfContainer {\r\n    position: relative;\r\n    width: 100%;\r\n    height: 0;\r\n    padding-bottom: 60%; \r\n    overflow: hidden;\r\n}\r\n\r\n#pdfFrame {\r\n    position: absolute;\r\n    top: 0;\r\n    left: 0;\r\n    width: 100%;\r\n    height: 100%;\r\n}#pdfContainer {\r\n    position: relative;\r\n    width: 100%;\r\n    height: 0;\r\n    padding-bottom: 60%; \r\n    overflow: hidden;\r\n}\r\n\r\n#pdfFrame {\r\n    position: absolute;\r\n    top: 0;\r\n    left: 0;\r\n    width: 100%;\r\n    height: 100%;\r\n}#pdfContainer {\r\n    position: relative;\r\n    width: 100%;\r\n    height: 0;\r\n    padding-bottom: 60%; \r\n    overflow: hidden;\r\n}\r\n\r\n#pdfFrame {\r\n    position: absolute;\r\n    top: 0;\r\n    left: 0;\r\n    width: 100%;\r\n    height: 100%;\r\n}#pdfContainer {\r\n    position: relative;\r\n    width: 100%;\r\n    height: 0;\r\n    padding-bottom: 60%; \r\n    overflow: hidden;\r\n}\r\n\r\n#pdfFrame {\r\n    position: absolute;\r\n    top: 0;\r\n    left: 0;\r\n    width: 100%;\r\n    height: 100%;\r\n}#pdfContainer {\r\n    position: relative;\r\n    width: 100%;\r\n    height: 0;\r\n    padding-bottom: 60%; \r\n    overflow: hidden;\r\n}\r\n\r\n#pdfFrame {\r\n    position: absolute;\r\n    top: 0;\r\n    left: 0;\r\n    width: 100%;\r\n    height: 100%;\r\n}#pdfContainer {\r\n    position: relative;\r\n    width: 100%;\r\n    height: 0;\r\n    padding-bottom: 60%; \r\n    overflow: hidden;\r\n}\r\n\r\n#pdfFrame {\r\n    position: absolute;\r\n    top: 0;\r\n    left: 0;\r\n    width: 100%;\r\n    height: 100%;\r\n}#pdfContainer {\r\n    position: relative;\r\n    width: 100%;\r\n    height: 0;\r\n    padding-bottom: 60%; \r\n    overflow: hidden;\r\n}\r\n\r\n#pdfFrame {\r\n    position: absolute;\r\n    top: 0;\r\n    left: 0;\r\n    width: 100%;\r\n    height: 100%;\r\n}', '2024-06-03 17:44:50', '2024-06-03 17:44:50', 2, 5),
(7, 4, 'TESTTTTT', '2024-06-06 17:08:52', '2024-06-06 17:08:52', 1, 6),
(8, 5, 'qqqqqqq', '2024-06-06 17:19:54', '2024-06-06 17:19:54', 1, 6),
(12, 4, 'asdasdasd', '2024-06-10 17:28:35', '2024-06-10 17:28:35', 1, 9),
(13, 4, 'display: flex;\r\n      bottom: 0;\r\n      left: 0;\r\n      margin-bottom: 10px;      display: flex;\r\n      bottom: 0;\r\n      left: 0;\r\n      margin-bottom: 10px;      display: flex;\r\n      bottom: 0;\r\n      left: 0;\r\n      margin-bottom: 10px;      display: flex;\r\n      bottom: 0;\r\n      left: 0;\r\n      margin-bottom: 10px;      display: flex;\r\n      bottom: 0;\r\n      left: 0;\r\n      margin-bottom: 10px;', '2024-06-11 08:55:30', '2024-06-11 08:55:30', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usertype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `usertype`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, '$2y$12$B7cL7FKKW0vAWPgK2oqbbebZzsqcL3UdjO1OesCKi59K8zMuCPERq', 'admin', NULL, '2024-03-27 16:10:47', '2024-03-27 16:10:47'),
(2, 'User', 'user@gmail.com', NULL, '$2y$12$8hbdBZqqsykGyEmGA4GysOGxcRdJh1cdxqpGc0ChZkSeZKEgYQYcK', 'user', NULL, '2024-03-27 16:13:28', '2024-03-27 16:13:28'),
(3, 'tests', 'tests@gmail.com', NULL, '$2y$12$/yZ.b0ly30paxns1fhS01OiLCCtnZwT0Hb4L/Q1bxGuzNNwTtxXXS', 'admin', NULL, '2024-04-10 12:11:32', '2024-04-30 20:08:33'),
(7, 'qqqq', 'qqqq@gmail.com', NULL, '$2y$12$GvxLW4zOCxd3AyPjzYBrHez4I9j6X6JGeifN1KuI5kK5mPysLsQei', 'user', NULL, '2024-05-19 15:12:44', '2024-05-19 15:12:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favorites_user_id_foreign` (`user_id`),
  ADD KEY `favorites_product_id_foreign` (`product_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notes_user_id_foreign` (`user_id`),
  ADD KEY `notes_product_id_foreign` (`product_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_product_id_foreign` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
