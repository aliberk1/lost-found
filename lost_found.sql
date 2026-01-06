-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 24 Ara 2025, 04:18:44
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `lost_found`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint(20) NOT NULL,
  `user_a` bigint(20) NOT NULL,
  `user_b` bigint(20) NOT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_by_a` tinyint(1) DEFAULT 0,
  `deleted_by_b` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `conversations`
--

INSERT INTO `conversations` (`id`, `user_a`, `user_b`, `item_id`, `created_at`, `updated_at`, `deleted_by_a`, `deleted_by_b`) VALUES
(19, 1, 11, 28, '2025-12-23 19:09:10', '2025-12-23 19:09:10', 0, 0),
(20, 11, 20, 28, '2025-12-23 19:11:39', '2025-12-23 19:13:40', 0, 0),
(21, 1, 13, 43, '2025-12-23 20:07:09', '2025-12-23 20:07:37', 0, 0),
(22, 1, 24, 42, '2025-12-23 20:07:50', '2025-12-23 20:08:18', 0, 0),
(23, 1, 22, 41, '2025-12-23 20:08:26', '2025-12-23 20:09:11', 0, 0),
(24, 1, 21, 40, '2025-12-23 20:09:26', '2025-12-23 20:09:26', 0, 0),
(25, 1, 20, 39, '2025-12-23 20:09:31', '2025-12-23 20:09:56', 0, 0),
(26, 13, 21, 43, '2025-12-23 20:51:29', '2025-12-23 20:51:39', 0, 0),
(27, 21, 24, 42, '2025-12-23 20:51:45', '2025-12-23 20:52:00', 0, 0),
(28, 19, 21, 38, '2025-12-23 20:52:10', '2025-12-23 20:52:43', 0, 0),
(29, 15, 21, 32, '2025-12-23 20:52:50', '2025-12-23 20:53:04', 0, 0),
(30, 12, 21, 29, '2025-12-23 20:53:17', '2025-12-23 20:53:49', 0, 0),
(31, 1, 18, NULL, '2025-12-23 21:55:35', '2025-12-23 21:55:35', 0, 0),
(32, 1, 12, 29, '2025-12-23 21:57:35', '2025-12-23 21:57:35', 0, 0),
(33, 22, 23, 41, '2025-12-24 00:05:34', '2025-12-24 00:05:41', 0, 0),
(34, 1, 17, 34, '2025-12-24 01:23:14', '2025-12-24 01:23:40', 0, 0),
(35, 1, 25, 50, '2025-12-24 01:27:54', '2025-12-24 01:28:04', 0, 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `items`
--

CREATE TABLE `items` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `category` enum('Elektronik','Kimlik','Cüzdan','Anahtar','Giyim','Diğer') NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` enum('OPEN','RESERVED','RESOLVED') NOT NULL DEFAULT 'OPEN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reserved_by` bigint(20) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `owner_notified` tinyint(1) NOT NULL DEFAULT 0,
  `reserved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `items`
--

INSERT INTO `items` (`id`, `user_id`, `title`, `description`, `category`, `location`, `status`, `created_at`, `reserved_by`, `approved`, `owner_notified`, `reserved_at`) VALUES
(28, 11, 'Lost Electric Guitar', 'Brown electric guitar lost with its carrying case.', 'Elektronik', 'Eskişehir – Tepebaşı', 'OPEN', '2025-12-23 14:27:00', NULL, 1, 0, NULL),
(29, 12, 'Found Wireless Earbuds Charging Case', 'White charging case with earbuds inside.', 'Elektronik', 'Istanbul – Bakırköy', 'OPEN', '2025-12-23 14:27:44', NULL, 1, 0, '2025-12-24 00:57:36'),
(31, 14, 'Found Fountain Pen', 'Dark-colored fountain pen found in a café.\r\n', 'Diğer', 'Konya – Selçuklu', 'RESERVED', '2025-12-23 14:29:35', 1, 1, 0, NULL),
(32, 15, 'Lost White Cat', 'Small  British white cat,  calm and friendly.', 'Diğer', 'Ankara – Çankaya', 'OPEN', '2025-12-23 14:31:05', NULL, 1, 0, NULL),
(33, 16, 'Lost Black Notebook', 'Black-covered notebook containing personal notes.', 'Diğer', 'Istanbul – Kadıköy', 'OPEN', '2025-12-23 14:32:39', NULL, 1, 0, NULL),
(34, 17, 'Found Game Console', 'Game console with white and blue controller found in a bag.', 'Elektronik', 'Ankara – Etimesgut', 'RESERVED', '2025-12-23 14:33:20', 1, 1, 0, NULL),
(35, 18, 'Lost Golden Foot', 'I lost my golden foot, which was very precious to me.', 'Diğer', 'Spain-Barcelona', 'OPEN', '2025-12-23 14:39:12', NULL, 1, 0, '2025-12-24 01:51:23'),
(37, 23, 'I Lost My Love', 'I lost my girlfriend, who had black hair and was about 1.65 meters tall.', 'Diğer', 'Argentina-Boca Center', 'OPEN', '2025-12-23 14:45:17', NULL, 1, 0, NULL),
(38, 19, 'Found Smartwatch', 'Smartwatch with light-colored strap.', 'Elektronik', 'Trabzon – Ortahisar', 'RESERVED', '2025-12-23 14:46:19', 21, 1, 0, NULL),
(39, 20, 'Lost Puppy', 'Small puppy with brown tones. Looks well cared for.', 'Diğer', 'Kocaeli – İzmit', 'OPEN', '2025-12-23 18:45:23', NULL, 1, 0, NULL),
(40, 21, 'Found Tablet', 'Pink tablet found without a case. Screen intact.', 'Elektronik', '    368 Paris Hill Ave.Brooklyn, NY 11238  ', 'OPEN', '2025-12-23 18:46:56', NULL, 1, 0, NULL),
(41, 22, 'Found Public Transport Card', 'Red transportation card', 'Kimlik', 'Istanbul – Mecidiyeköy', 'RESERVED', '2025-12-23 18:49:14', 23, 1, 0, NULL),
(42, 24, 'Lost Passport', 'German passport. Urgently needed.\r\n', 'Kimlik', 'John F. Kennedy International Airport', 'RESERVED', '2025-12-23 18:54:04', 1, 1, 1, '2025-12-24 00:49:29'),
(43, 13, 'Lost Iphone', 'Smartphone with orange back cover.', 'Elektronik', 'Adana – Seyhan', 'RESERVED', '2025-12-23 18:56:16', 19, 1, 1, NULL),
(50, 1, 'Lost Car Key', 'I lost my car key audı mark', 'Anahtar', 'Istanbul – Kadıköy', 'RESOLVED', '2025-12-23 23:35:17', NULL, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `item_images`
--

CREATE TABLE `item_images` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `item_images`
--

INSERT INTO `item_images` (`id`, `item_id`, `image_path`, `created_at`) VALUES
(1, 15, '/lost_found/public/uploads/img_693ed2bbd5f9f3.15037720.png', '2025-12-14 15:07:39'),
(2, 16, '/lost_found/public/uploads/img_693ed3f8d05ca7.63587450.jpg', '2025-12-14 15:12:56'),
(3, 16, '/lost_found/public/uploads/img_693ed3f8d0f888.42588360.jpg', '2025-12-14 15:12:56'),
(4, 16, '/lost_found/public/uploads/img_693ed3f8d1b028.35147055.jpg', '2025-12-14 15:12:56'),
(5, 16, '/lost_found/public/uploads/img_693ed3f8d22c38.94219785.jpg', '2025-12-14 15:12:56'),
(6, 16, '/lost_found/public/uploads/img_693ed3f8d28401.66324539.jpg', '2025-12-14 15:12:56'),
(7, 17, '/lost_found/public/uploads/img_693ee2ce79cff0.17501701.jpg', '2025-12-14 16:16:14'),
(8, 17, '/lost_found/public/uploads/img_693ee2ce7a29d7.51882163.jpg', '2025-12-14 16:16:14'),
(9, 17, '/lost_found/public/uploads/img_693ee2ce7aa2b1.98171841.jpg', '2025-12-14 16:16:14'),
(10, 17, '/lost_found/public/uploads/img_693ee2ce7b06e2.62799039.jpg', '2025-12-14 16:16:14'),
(11, 17, '/lost_found/public/uploads/img_693ee2ce7b6100.88701944.jpg', '2025-12-14 16:16:14'),
(12, 18, '/lost_found/public/uploads/img_693ee5d02d8779.13853972.jpg', '2025-12-14 16:29:04'),
(13, 18, '/lost_found/public/uploads/img_693ee5d02de692.96264682.jpg', '2025-12-14 16:29:04'),
(14, 18, '/lost_found/public/uploads/img_693ee5d02e56d9.79091342.jpg', '2025-12-14 16:29:04'),
(15, 19, '/lost_found/public/uploads/img_693ee62fe03c55.79935447.jpg', '2025-12-14 16:30:39'),
(16, 19, '/lost_found/public/uploads/img_693ee62fe0a239.58685435.jpg', '2025-12-14 16:30:39'),
(17, 19, '/lost_found/public/uploads/img_693ee62fe12755.21214980.jpg', '2025-12-14 16:30:39'),
(18, 19, '/lost_found/public/uploads/img_693ee62fe19873.05521065.jpg', '2025-12-14 16:30:39'),
(19, 20, '/lost_found/public/uploads/img_693ee672bc0778.07663292.jpg', '2025-12-14 16:31:46'),
(20, 20, '/lost_found/public/uploads/img_693ee672bc66e8.20256444.jpg', '2025-12-14 16:31:46'),
(21, 21, '/lost_found/public/uploads/img_693ee6c81cdf89.74276641.jpg', '2025-12-14 16:33:12'),
(22, 21, '/lost_found/public/uploads/img_693ee6c81d71e6.27978970.jpg', '2025-12-14 16:33:12'),
(23, 22, '/lost_found/public/uploads/img_693ee7099923f6.20497964.jpg', '2025-12-14 16:34:17'),
(24, 23, '/lost_found/public/uploads/img_693ee7fa1b6740.34004530.jpg', '2025-12-14 16:38:18'),
(25, 24, '/lost_found/public/uploads/img_69400cedba83d1.05924477.jpg', '2025-12-15 13:28:13'),
(26, 25, '/lost_found/public/uploads/img_69400f0eec4c57.91515814.jpg', '2025-12-15 13:37:18'),
(27, 26, '/lost_found/public/uploads/img_69406ed54a7980.43843617.jpg', '2025-12-15 20:25:57'),
(29, 28, '/lost_found/public/uploads/img_694aa6b4c540d9.68940566.jpg', '2025-12-23 14:27:00'),
(30, 29, '/lost_found/public/uploads/img_694aa6e0d1b5b9.43489127.jpg', '2025-12-23 14:27:44'),
(31, 30, '/lost_found/public/uploads/img_694aa716dd1414.47263145.jpg', '2025-12-23 14:28:38'),
(32, 30, '/lost_found/public/uploads/img_694aa716dd7574.31312063.jpg', '2025-12-23 14:28:38'),
(33, 31, '/lost_found/public/uploads/img_694aa74f40be69.45976789.jpg', '2025-12-23 14:29:35'),
(34, 32, '/lost_found/public/uploads/img_694aa7a914db26.40566314.jpg', '2025-12-23 14:31:05'),
(35, 32, '/lost_found/public/uploads/img_694aa7a91542f3.39205453.jpg', '2025-12-23 14:31:05'),
(36, 33, '/lost_found/public/uploads/img_694aa80786bda9.10731674.jpg', '2025-12-23 14:32:39'),
(37, 34, '/lost_found/public/uploads/img_694aa830163d87.34452227.jpg', '2025-12-23 14:33:20'),
(38, 35, '/lost_found/public/uploads/img_694aa990f3e9c0.23226182.jpg', '2025-12-23 14:39:12'),
(39, 37, '/lost_found/public/uploads/img_694aaafda224c7.71776838.jpg', '2025-12-23 14:45:17'),
(40, 38, '/lost_found/public/uploads/img_694aab3bd88b38.40588304.jpg', '2025-12-23 14:46:19'),
(45, 39, '/lost_found/public/uploads/img_694ae3436b9968.87403331.jpg', '2025-12-23 18:45:23'),
(46, 39, '/lost_found/public/uploads/img_694ae3436c0639.01249311.jpg', '2025-12-23 18:45:23'),
(47, 40, '/lost_found/public/uploads/img_694ae3a0c99412.23740607.jpg', '2025-12-23 18:46:56'),
(48, 41, '/lost_found/public/uploads/img_694ae42a5ae8d6.63385546.jpg', '2025-12-23 18:49:14'),
(49, 42, '/lost_found/public/uploads/img_694ae54c050ce6.82086899.jpg', '2025-12-23 18:54:04'),
(50, 43, '/lost_found/public/uploads/img_694ae5d02ed829.84244426.jpg', '2025-12-23 18:56:16'),
(51, 43, '/lost_found/public/uploads/img_694ae5d02f50d4.80422622.jpg', '2025-12-23 18:56:16'),
(54, 44, '/lost_found/public/uploads/img_694af9dc0fa4e8.31359426.jpg', '2025-12-23 20:21:48'),
(55, 27, '/lost_found/public/uploads/img_694afaf68b62a4.65453970.jpg', '2025-12-23 20:26:30'),
(57, 51, '/lost_found/public/uploads/img_694b2d6becda43.41421809.jpg', '2025-12-24 00:01:47'),
(58, 50, '/lost_found/public/uploads/img_694b3932a6e0f5.87446372.jpg', '2025-12-24 00:52:02');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) NOT NULL,
  `conversation_id` bigint(20) NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `body`, `created_at`, `read_at`) VALUES
(76, 20, 20, 'Hello! I found an electric guitar near Tepebaşı yesterday. I saw your post and wanted to check if it might be yours.', '2025-12-23 19:11:44', '2025-12-23 19:12:08'),
(77, 20, 11, 'Hi! Yes, I lost my electric guitar recently. Can you describe it?', '2025-12-23 19:12:17', '2025-12-23 19:12:45'),
(78, 20, 20, 'Sure. It’s a brown electric guitar with a soft black case. There is a small scratch near the volume knob.', '2025-12-23 19:12:54', '2025-12-23 19:13:09'),
(79, 20, 11, 'That sounds exactly like mine. The scratch is very distinctive.', '2025-12-23 19:13:17', '2025-12-23 19:13:39'),
(80, 20, 20, 'Great! I found it close to a bus stop around the evening.', '2025-12-23 19:13:40', NULL),
(81, 21, 1, 'Hello I found your phone. Can you descrıbe more', '2025-12-23 20:07:37', '2025-12-23 21:57:53'),
(82, 22, 1, 'Hello I found your passport. Can you descrıbe more than', '2025-12-23 20:08:18', '2025-12-24 01:07:33'),
(83, 23, 1, 'Hello melih, I was lost my transport card can you gıve me any tıme pls', '2025-12-23 20:09:11', NULL),
(84, 25, 1, 'hello jeff ı found your pupy can you descrıbe more', '2025-12-23 20:09:56', NULL),
(85, 26, 21, 'hello serenay ı found you phone', '2025-12-23 20:51:39', '2025-12-23 21:57:51'),
(86, 27, 21, 'Hi Ali ı found your passport', '2025-12-23 20:52:00', '2025-12-24 01:07:32'),
(87, 28, 21, 'my smartwacth can you sen me any tıme', '2025-12-23 20:52:43', '2025-12-24 00:22:11'),
(88, 29, 21, 'ı found your cat can you decrıbe more than detaıls', '2025-12-23 20:53:04', NULL),
(89, 30, 21, 'my eardbuds please can you transport me', '2025-12-23 20:53:49', NULL),
(90, 33, 23, 'hello ı found you card', '2025-12-24 00:05:41', NULL),
(91, 34, 1, 'hello Albert thıs console my console', '2025-12-24 01:23:40', '2025-12-24 01:26:47'),
(92, 35, 25, 'hi ı found key car', '2025-12-24 01:28:04', '2025-12-24 01:28:14');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Ali Berk Geyik', 'aliberkgeyik1256@gmail.com', '$2y$10$jZd14PWlKYBoDyl52JXWqeXSPAcePpIQXzkS/QoHBAziATSMrDtfu', 'ADMIN', '2025-10-01 12:43:24'),
(11, 'Cem Yılmaz', 'cemyilmaz@gmail.com', '$2y$10$39Dd2ImkwMkR92i15HDVSe8W7vIXp4MaLzsCKJ7VxbC66QE2XW1FG', 'USER', '2025-12-23 13:03:58'),
(12, 'Kenan İmirzalıoğlu', 'kenan@gmail.com', '$2y$10$YMiOQrlFLcf1a3xw3Er.deazvrBGEHufqbLS6Ykb.O6qbMYkvAx.i', 'USER', '2025-12-23 13:04:37'),
(13, 'Serenay Sarıkaya', 'serenay@gmail.com', '$2y$10$rM74CIM55z0Brmk8Pt2a4.Lo5DOs7AGnMzc8rheUMrxBpczpHB9gS', 'USER', '2025-12-23 13:05:04'),
(14, 'Kıvanç Tatlıtuğ', 'kivanc@gmail.com', '$2y$10$VUKvj3crGQjnSZyUINoSiu2j///zMIvfk2Xz.ogE5WhXAKigvANly', 'USER', '2025-12-23 13:05:27'),
(15, 'Ajda Pekkan', 'ajda@gmail.com', '$2y$10$c8NVddDki.YrT6.3u0XfL.0Tj0hKc4rAdXN1FcRo8bRBmpm0MadSC', 'USER', '2025-12-23 13:05:42'),
(16, 'Elon Musk', 'elon@gmail.com', '$2y$10$bW3g9UyOaddT59.cNnfO8OUgumaOHfmKLq42KGNa3FExlyolUZi0a', 'USER', '2025-12-23 13:08:24'),
(17, 'Albert Einstein', 'albert@gmail.com', '$2y$10$UUwv9R8lB6WuJ9609fW6He.hABICqm5P7YavGrLzH5rsvlm42EBZS', 'USER', '2025-12-23 13:08:45'),
(18, 'Lionel Messi', 'messi@gmail.com', '$2y$10$Du7LTS8EYBWy2cRB8F7TF.K5bikmrDuud3Rga/quOJYeFnI.vpScS', 'USER', '2025-12-23 13:09:06'),
(19, 'Nikola Tesla', 'tesla@gmail.com', '$2y$10$3JrBCZdIfxNspeKeaRZCductbYAmMenrHFzYh/RcOL.exWyZ0uEEG', 'USER', '2025-12-23 13:09:26'),
(20, 'Jeff Bezos', 'jeff@gmail.com', '$2y$10$1bVOUrEYGxbkYflubu9LXeStu0trtO9F2pnf9WW5q0Ab/GxTpYRni', 'USER', '2025-12-23 13:09:43'),
(21, 'Berkay Çevirici', 'berkay@gmail.com', '$2y$10$pgOJ1OO0hPGuG3IRnK6vTe68EImBfb6vji6sRG8EHdVR8/dNkhZSO', 'USER', '2025-12-23 13:47:33'),
(22, 'Melih Polat', 'melih@gmail.com', '$2y$10$APzrod0vCpOhI3hzS6hgPeGk4FIV3yfcMqE4iLIoHHQ0F57r5cdLy', 'USER', '2025-12-23 13:48:37'),
(23, 'Mauro İcardi', 'icardi@gmail.com', '$2y$10$5Dm4gs.C2x6FZ1w4vtpFKeX/cj7R8cOV9yctVtQcseFC/MAtI.pkW', 'USER', '2025-12-23 13:49:21'),
(24, 'Ali Koç', 'alikoc@gmail.com', '$2y$10$vhdS2APrCGKk9mH0c/2FTORXUHTZ5N0cz7L3WY.GGHaa9TsIFc.lG', 'USER', '2025-12-23 13:49:40'),
(25, 'Yusuf', 'yusuf@gmail.com', '$2y$10$2.cIaRHHBX3ZMmP0SSp8TutitckI7zKZP6e6D2cdYZZS32APTfMWe', 'USER', '2025-12-24 01:27:37');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conv_user_b` (`user_b`),
  ADD KEY `fk_conv_item` (`item_id`),
  ADD KEY `idx_conv_users_item` (`user_a`,`user_b`,`item_id`);

--
-- Tablo için indeksler `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_items_reserved_by` (`reserved_by`);

--
-- Tablo için indeksler `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Tablo için indeksler `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_msg_sender` (`sender_id`),
  ADD KEY `idx_msg_conv_created` (`conversation_id`,`created_at`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Tablo için AUTO_INCREMENT değeri `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Tablo için AUTO_INCREMENT değeri `item_images`
--
ALTER TABLE `item_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Tablo için AUTO_INCREMENT değeri `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `fk_conv_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_conv_user_a` FOREIGN KEY (`user_a`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_user_b` FOREIGN KEY (`user_b`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_reserved_by` FOREIGN KEY (`reserved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_conv` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
