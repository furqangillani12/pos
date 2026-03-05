-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 22, 2025 at 10:33 PM
-- Server version: 10.6.23-MariaDB-cll-lve
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `almufeed_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `session` enum('morning','evening','night') NOT NULL DEFAULT 'morning',
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('present','absent','late','on_leave','half_day') NOT NULL DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `employee_id`, `date`, `session`, `check_in`, `check_out`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(45, 4, '2025-09-16', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:18:07', '2025-09-16 02:18:07'),
(46, 4, '2025-09-15', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:20:55', '2025-09-16 02:20:55'),
(47, 4, '2025-09-14', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:21:37', '2025-09-16 02:21:37'),
(48, 4, '2025-09-01', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:23:24', '2025-09-16 02:23:24'),
(49, 4, '2025-09-02', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:23:58', '2025-09-16 02:23:58'),
(50, 4, '2025-09-03', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:24:28', '2025-09-16 02:24:28'),
(51, 4, '2025-09-04', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:25:13', '2025-09-16 02:25:13'),
(52, 4, '2025-09-05', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:25:45', '2025-09-16 02:25:45'),
(53, 4, '2025-09-06', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:26:08', '2025-09-16 02:26:08'),
(54, 4, '2025-09-07', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:26:40', '2025-09-16 02:26:40'),
(55, 4, '2025-09-08', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:27:10', '2025-09-16 02:27:10'),
(56, 4, '2025-09-09', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:27:39', '2025-09-16 02:27:39'),
(57, 4, '2025-09-10', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:28:08', '2025-09-16 02:28:08'),
(58, 4, '2025-09-11', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:28:35', '2025-09-16 02:28:35'),
(59, 4, '2025-09-12', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:29:10', '2025-09-16 02:29:10'),
(60, 4, '2025-09-13', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:29:36', '2025-09-16 02:29:36'),
(61, 5, '2025-09-16', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:06:28', '2025-09-16 07:06:28'),
(62, 5, '2025-09-15', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:07:14', '2025-09-16 07:07:14'),
(63, 5, '2025-09-14', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:07:54', '2025-09-16 07:07:54'),
(64, 5, '2025-09-13', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:09:33', '2025-09-16 07:09:33'),
(65, 5, '2025-09-12', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:09:56', '2025-09-16 07:09:56'),
(66, 5, '2025-09-11', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:10:19', '2025-09-16 07:10:19'),
(67, 5, '2025-09-10', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:10:46', '2025-09-16 07:10:46'),
(68, 5, '2025-09-09', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:11:20', '2025-09-16 07:11:20'),
(69, 5, '2025-09-08', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:11:55', '2025-09-16 07:11:55'),
(70, 5, '2025-09-06', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:12:44', '2025-09-16 07:12:44'),
(71, 5, '2025-09-05', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:13:17', '2025-09-16 07:13:17'),
(72, 5, '2025-09-03', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:13:42', '2025-09-16 07:13:42'),
(73, 5, '2025-09-02', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:14:18', '2025-09-16 07:14:18'),
(74, 5, '2025-09-01', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:14:50', '2025-09-16 07:14:50'),
(75, 4, '2025-09-17', 'morning', NULL, NULL, 'present', '', '2025-09-16 22:42:37', '2025-09-16 22:42:37'),
(76, 4, '2025-09-18', 'morning', NULL, NULL, 'present', '', '2025-09-18 00:25:00', '2025-09-18 00:25:00'),
(77, 4, '2025-09-19', 'morning', NULL, NULL, 'present', '', '2025-09-18 22:41:39', '2025-09-18 22:41:39'),
(78, 4, '2025-09-22', 'morning', NULL, NULL, 'present', '', '2025-09-22 01:48:38', '2025-09-22 01:48:38'),
(79, 4, '2025-09-20', 'morning', NULL, NULL, 'present', '', '2025-09-22 01:49:15', '2025-09-22 01:49:15'),
(80, 4, '2025-09-21', 'morning', NULL, NULL, 'present', '', '2025-09-22 01:49:41', '2025-09-22 01:49:41'),
(81, 4, '2025-09-23', 'morning', NULL, NULL, 'present', '', '2025-09-23 08:56:21', '2025-09-23 08:56:21'),
(82, 4, '2025-09-24', 'morning', NULL, NULL, 'present', '', '2025-09-24 08:44:39', '2025-09-24 08:44:39'),
(83, 4, '2025-09-25', 'morning', NULL, NULL, 'present', '', '2025-09-24 23:53:55', '2025-09-24 23:53:55'),
(84, 4, '2025-09-26', 'morning', NULL, NULL, 'present', '', '2025-09-25 23:24:29', '2025-09-25 23:24:29'),
(85, 4, '2025-09-27', 'morning', NULL, NULL, 'present', '', '2025-09-27 10:38:36', '2025-09-27 10:38:36'),
(86, 4, '2025-09-28', 'morning', NULL, NULL, 'present', '', '2025-09-28 23:18:52', '2025-09-28 23:18:52'),
(87, 4, '2025-09-29', 'morning', NULL, NULL, 'present', '', '2025-09-28 23:19:13', '2025-09-28 23:19:13'),
(88, 5, '2025-12-21', 'morning', NULL, NULL, 'present', '', '2025-12-21 01:18:48', '2025-12-21 01:18:48'),
(89, 5, '2025-12-20', 'morning', NULL, NULL, 'present', '', '2025-12-21 01:19:33', '2025-12-21 01:19:33'),
(90, 4, '2025-12-21', 'morning', NULL, NULL, 'present', '', '2025-12-21 04:23:14', '2025-12-21 04:23:14'),
(91, 5, '2025-12-22', 'morning', NULL, NULL, 'present', '', '2025-12-22 01:24:23', '2025-12-22 01:24:23'),
(92, 4, '2025-12-22', 'morning', NULL, NULL, 'present', '', '2025-12-22 01:25:28', '2025-12-22 01:25:28');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_sessions`
--

CREATE TABLE `attendance_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attendance_id` bigint(20) UNSIGNED NOT NULL,
  `check_in` time NOT NULL,
  `check_out` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_sessions`
--

INSERT INTO `attendance_sessions` (`id`, `attendance_id`, `check_in`, `check_out`, `created_at`, `updated_at`) VALUES
(21, 45, '08:00:00', '14:52:00', '2025-09-16 02:18:07', '2025-09-16 09:52:25'),
(22, 46, '08:00:00', '18:00:00', '2025-09-16 02:20:55', '2025-09-16 02:20:55'),
(23, 47, '08:00:00', '18:00:00', '2025-09-16 02:21:37', '2025-09-16 02:21:37'),
(24, 48, '08:00:00', '18:23:00', '2025-09-16 02:23:24', '2025-09-16 02:23:24'),
(25, 49, '08:00:00', '18:24:00', '2025-09-16 02:23:58', '2025-09-16 02:23:58'),
(26, 50, '08:00:00', '18:24:00', '2025-09-16 02:24:28', '2025-09-16 02:24:28'),
(27, 51, '08:00:00', '18:00:00', '2025-09-16 02:25:13', '2025-09-16 02:25:13'),
(28, 52, '08:00:00', '18:00:00', '2025-09-16 02:25:45', '2025-09-16 02:25:45'),
(29, 53, '08:00:00', NULL, '2025-09-16 02:26:08', '2025-09-16 02:26:08'),
(30, 54, '08:00:00', '18:00:00', '2025-09-16 02:26:40', '2025-09-16 02:26:40'),
(31, 55, '08:00:00', '18:00:00', '2025-09-16 02:27:10', '2025-09-16 02:27:10'),
(32, 56, '08:00:00', '18:00:00', '2025-09-16 02:27:39', '2025-09-16 02:27:39'),
(33, 57, '08:00:00', '18:00:00', '2025-09-16 02:28:08', '2025-09-16 02:28:08'),
(34, 58, '08:00:00', '18:00:00', '2025-09-16 02:28:35', '2025-09-16 02:28:35'),
(35, 59, '08:00:00', '18:00:00', '2025-09-16 02:29:10', '2025-09-16 02:29:10'),
(36, 60, '08:00:00', '18:00:00', '2025-09-16 02:29:36', '2025-09-16 02:29:36'),
(37, 60, '08:00:00', '18:00:00', '2025-09-16 02:36:14', '2025-09-16 02:36:14'),
(38, 61, '08:00:00', NULL, '2025-09-16 07:06:28', '2025-09-16 07:06:28'),
(39, 62, '08:00:00', '19:30:00', '2025-09-16 07:07:14', '2025-09-16 07:07:14'),
(40, 63, '08:10:00', '19:15:00', '2025-09-16 07:07:54', '2025-09-16 07:07:54'),
(41, 61, '08:00:00', '19:35:00', '2025-09-16 07:08:28', '2025-09-16 07:08:28'),
(42, 61, '08:00:00', '19:35:00', '2025-09-16 07:08:30', '2025-09-16 07:08:30'),
(43, 61, '07:50:00', '19:20:00', '2025-09-16 07:08:57', '2025-09-16 07:08:57'),
(44, 64, '08:02:00', '19:25:00', '2025-09-16 07:09:33', '2025-09-16 07:09:33'),
(45, 65, '08:02:00', '19:05:00', '2025-09-16 07:09:56', '2025-09-16 07:09:56'),
(46, 66, '07:55:00', '19:15:00', '2025-09-16 07:10:19', '2025-09-16 07:10:19'),
(47, 67, '07:58:00', '17:35:00', '2025-09-16 07:10:46', '2025-09-16 07:10:46'),
(48, 68, '08:01:00', '19:15:00', '2025-09-16 07:11:20', '2025-09-16 07:11:20'),
(49, 69, '07:45:00', '19:30:00', '2025-09-16 07:11:55', '2025-09-16 07:11:55'),
(50, 70, '08:00:00', '17:50:00', '2025-09-16 07:12:44', '2025-09-16 07:12:44'),
(51, 71, '07:48:00', '18:40:00', '2025-09-16 07:13:17', '2025-09-16 07:13:17'),
(52, 72, '08:05:00', '18:45:00', '2025-09-16 07:13:42', '2025-09-16 07:13:42'),
(53, 73, '08:02:00', '19:05:00', '2025-09-16 07:14:18', '2025-09-16 07:14:18'),
(54, 74, '08:01:00', '17:05:00', '2025-09-16 07:14:50', '2025-09-16 07:14:50'),
(55, 75, '08:20:00', '14:50:00', '2025-09-16 22:42:37', '2025-09-17 09:50:47'),
(56, 76, '08:00:00', NULL, '2025-09-18 00:25:00', '2025-09-18 00:25:00'),
(57, 77, '08:00:00', NULL, '2025-09-18 22:41:39', '2025-09-18 22:41:39'),
(58, 78, '08:00:00', NULL, '2025-09-22 01:48:38', '2025-09-22 01:48:38'),
(59, 81, '08:00:00', '18:00:00', '2025-09-23 08:56:21', '2025-09-23 08:56:21'),
(60, 82, '08:00:00', '18:00:00', '2025-09-24 08:44:39', '2025-09-24 08:44:39'),
(61, 83, '08:00:00', NULL, '2025-09-24 23:53:55', '2025-09-24 23:53:55'),
(62, 84, '08:00:00', '14:02:00', '2025-09-25 23:24:29', '2025-09-26 09:02:11'),
(63, 84, '09:00:00', '18:00:00', '2025-09-26 09:04:32', '2025-09-26 09:04:32'),
(64, 85, '08:00:00', '18:00:00', '2025-09-27 10:38:36', '2025-09-27 10:38:36'),
(65, 87, '08:00:00', '13:40:00', '2025-09-28 23:19:13', '2025-09-29 08:40:08'),
(66, 88, '08:30:00', '06:20:00', '2025-12-21 01:18:48', '2025-12-21 01:20:30'),
(67, 88, '08:30:00', NULL, '2025-12-21 01:20:55', '2025-12-21 01:20:55'),
(68, 90, '00:15:00', NULL, '2025-12-21 04:23:14', '2025-12-21 04:23:14'),
(69, 91, '08:30:00', NULL, '2025-12-22 01:24:23', '2025-12-22 01:24:23'),
(70, 92, '09:00:00', NULL, '2025-12-22 01:25:28', '2025-12-22 01:25:28'),
(71, 92, '09:00:00', '17:20:00', '2025-12-22 10:05:26', '2025-12-22 10:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('almufeed_saqafti_markaz_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:16:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"view dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:15:\"manage products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:17:\"manage categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"process orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:16:\"manage employees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"manage reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:16:\"manage inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:17:\"manage attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"manage variants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:16:\"manage purchases\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:16:\"manage suppliers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:10:\"access pos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:12:\"manage roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:18:\"manage permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"assign roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:14:\"manage payroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"manager\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"cashier\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:8:\"employee\";s:1:\"c\";s:3:\"web\";}}}', 1766472261);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(4, 'Quran Majeed', 'Quran Majeed', '2025-09-15 23:05:30', '2025-09-15 23:05:30'),
(5, 'Hijab', 'Chadar, Abaya etc', '2025-09-17 07:42:51', '2025-09-17 07:42:51'),
(6, 'Shohada Books', 'Books', '2025-09-21 05:48:21', '2025-09-21 05:48:21'),
(7, 'Namaz o Manajat', 'Namaz Relaited Items', '2025-09-26 02:34:34', '2025-09-26 02:34:34'),
(8, 'Saqafti Items', 'Islamic Cultural Items', '2025-10-01 01:31:02', '2025-10-01 01:31:02');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `customer_type` enum('customer','reseller','wholesale') NOT NULL DEFAULT 'customer',
  `loyalty_points` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `customer_type`, `loyalty_points`, `created_at`, `updated_at`) VALUES
(3, 'Walk-in Customer', NULL, NULL, NULL, 'customer', 0.00, '2025-09-14 12:13:09', '2025-09-14 12:13:09'),
(6, 'Al Qaim Saqafti Markaz', '', '923260603214', 'Mian Channu', 'reseller', 0.00, '2025-09-22 22:58:58', '2025-09-22 22:58:58'),
(7, 'Kh. Arifa Kazmi', 'almufeed912@gmail.com', '03361514983', 'DinPur Srgodha', 'reseller', 0.00, '2025-10-01 06:33:30', '2025-10-01 06:33:30'),
(8, 'Kh. Kainat Khan', 'Amt7212@gmail.com', '923205820103', 'Kohat Kpk', 'reseller', 0.00, '2025-10-02 03:31:33', '2025-10-02 03:31:33'),
(9, 'Mol Hashim Irfani Sahb', 'Almufeed125@gmail.com', '03344466912', 'Bhutta Pur, Muzafargarh', 'customer', 0.00, '2025-12-13 12:06:27', '2025-12-13 12:06:27'),
(10, 'Dr Aziz Fatima', '', '923325231957', 'Kallurkot', 'reseller', 0.00, '2025-12-19 07:24:06', '2025-12-19 07:24:06');

-- --------------------------------------------------------

--
-- Table structure for table `ecommerce_sync_logs`
--

CREATE TABLE `ecommerce_sync_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `joining_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `phone`, `address`, `salary`, `joining_date`, `created_at`, `updated_at`) VALUES
(4, 6, '03457747789', 'Panjgirain', 5000.00, '2025-05-01', '2025-09-16 02:15:12', '2025-09-16 02:15:12'),
(5, 7, '03417212469', 'Panjgirain', 12000.00, '2025-08-12', '2025-09-16 07:00:57', '2025-09-16 07:00:57');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `quantity_change` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `purchase_id`, `product_id`, `action`, `quantity_change`, `notes`, `user_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(30, NULL, 9, 'order_sale', -3, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(31, NULL, 8, 'order_sale', -2, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(32, NULL, 7, 'order_sale', -2, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(33, NULL, 1, 'order_sale', -2, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(34, NULL, 10, 'order_sale', -4, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(35, NULL, 1, 'order_sale', -5, 'Stock reduced for Order #ORD-202509140003', 2, NULL, NULL, NULL, '2025-09-14 14:42:34', '2025-09-14 14:42:34'),
(36, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202509140004', 2, NULL, NULL, NULL, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(37, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509140004', 2, NULL, NULL, NULL, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(38, NULL, 9, 'order_sale', -1, 'Stock reduced for Order #ORD-202509140004', 2, NULL, NULL, NULL, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(39, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202509150001', 2, NULL, NULL, NULL, '2025-09-15 04:37:21', '2025-09-15 04:37:21'),
(40, NULL, 7, 'order_sale', -3, 'Stock reduced for Order #ORD-202509150002', 2, NULL, NULL, NULL, '2025-09-15 06:32:26', '2025-09-15 06:32:26'),
(41, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202509160001', 2, NULL, NULL, NULL, '2025-09-16 02:03:41', '2025-09-16 02:03:41'),
(42, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509170001', 2, NULL, NULL, NULL, '2025-09-17 00:59:36', '2025-09-17 00:59:36'),
(43, NULL, 11, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-17 07:48:38', '2025-09-17 07:48:38'),
(44, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509180001', 2, NULL, NULL, NULL, '2025-09-18 03:38:13', '2025-09-18 03:38:13'),
(45, NULL, 12, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-21 05:51:13', '2025-09-21 05:51:13'),
(46, NULL, 13, 'initial', 16, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-21 06:14:54', '2025-09-21 06:14:54'),
(47, NULL, 9, 'order_sale', -2, 'Stock reduced for Order #ORD-202509210001', 2, NULL, NULL, NULL, '2025-09-21 18:47:11', '2025-09-21 18:47:11'),
(48, NULL, 1, 'order_sale', -3, 'Stock reduced for Order #ORD-202509210002', 2, NULL, NULL, NULL, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(49, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210002', 2, NULL, NULL, NULL, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(50, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210002', 2, NULL, NULL, NULL, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(51, NULL, 9, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210002', 2, NULL, NULL, NULL, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(52, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210003', 2, NULL, NULL, NULL, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(53, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210003', 2, NULL, NULL, NULL, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(54, NULL, 9, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210003', 2, NULL, NULL, NULL, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(55, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210003', 2, NULL, NULL, NULL, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(56, NULL, 14, 'initial', 99, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-21 23:11:45', '2025-09-21 23:11:45'),
(57, NULL, 14, 'order_sale', -1, 'Stock reduced for Order #ORD-202509220001', 2, NULL, NULL, NULL, '2025-09-21 23:27:24', '2025-09-21 23:27:24'),
(58, NULL, 12, 'order_sale', -3, 'Stock reduced for Order #ASM1', 2, NULL, NULL, NULL, '2025-09-22 07:37:25', '2025-09-22 07:37:25'),
(59, NULL, 13, 'order_sale', -7, 'Stock reduced for Order #ASM2', 2, NULL, NULL, NULL, '2025-09-22 07:37:44', '2025-09-22 07:37:44'),
(60, NULL, 12, 'order_sale', -1, 'Stock reduced for Order #ASM3', 2, NULL, NULL, NULL, '2025-09-22 07:38:15', '2025-09-22 07:38:15'),
(61, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ASM4', 2, NULL, NULL, NULL, '2025-09-22 07:48:48', '2025-09-22 07:48:48'),
(65, NULL, 18, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-26 23:10:41', '2025-09-26 23:10:41'),
(66, NULL, 19, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-26 23:14:03', '2025-09-26 23:14:03'),
(67, NULL, 20, 'initial', 75, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-26 23:16:59', '2025-09-26 23:16:59'),
(68, NULL, 20, 'order_sale', -3, 'Stock reduced for Order #ORD-202509280001', 2, NULL, NULL, NULL, '2025-09-28 17:10:02', '2025-09-28 17:10:02'),
(69, NULL, 20, 'order_sale', -4, 'Stock reduced for Order #ORD-202509280001', 2, NULL, NULL, NULL, '2025-09-28 17:19:44', '2025-09-28 17:19:44'),
(70, NULL, 18, 'order_sale', -1, 'Stock reduced for Order #ORD-202509280001', 2, NULL, NULL, NULL, '2025-09-28 17:19:44', '2025-09-28 17:19:44'),
(71, NULL, 18, 'order_sale', -1, 'Stock reduced for Order #ORD-202509290001', 2, NULL, NULL, NULL, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(72, NULL, 20, 'order_sale', -1, 'Stock reduced for Order #ORD-202509290001', 2, NULL, NULL, NULL, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(73, NULL, 19, 'order_sale', -1, 'Stock reduced for Order #ORD-202509290001', 2, NULL, NULL, NULL, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(74, NULL, 21, 'initial', 20, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-10-01 01:35:35', '2025-10-01 01:35:35'),
(75, NULL, 21, 'order_sale', -1, 'Stock reduced for Order #ORD-202510010001', 2, NULL, NULL, NULL, '2025-10-01 06:35:06', '2025-10-01 06:35:06'),
(76, NULL, 22, 'initial', 12, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-10-02 03:39:32', '2025-10-02 03:39:32'),
(77, NULL, 22, 'order_sale', -2, 'Stock reduced for Order #ORD-202510020001', 2, NULL, NULL, NULL, '2025-10-02 03:41:43', '2025-10-02 03:41:43'),
(78, NULL, 11, 'order_sale', -6, 'Stock reduced for Order #ORD-202510020001', 2, NULL, NULL, NULL, '2025-10-02 03:41:43', '2025-10-02 03:41:43'),
(85, NULL, 18, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(86, NULL, 22, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(87, NULL, 21, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(88, NULL, 20, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(89, NULL, 19, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(90, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(91, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202511270001', 2, NULL, NULL, NULL, '2025-11-27 12:43:35', '2025-11-27 12:43:35'),
(92, NULL, 23, 'initial', 10, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-13 11:45:20', '2025-12-13 11:45:20'),
(93, NULL, 23, 'order_sale', -2, 'Stock reduced for Order #ORD-202512130001', 2, NULL, NULL, NULL, '2025-12-13 12:22:24', '2025-12-13 12:22:24'),
(94, NULL, 24, 'initial', 60, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:23:14', '2025-12-20 02:23:14'),
(95, NULL, 25, 'initial', 5, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:26:29', '2025-12-20 02:26:29'),
(96, NULL, 26, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:29:10', '2025-12-20 02:29:10'),
(97, NULL, 27, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:31:27', '2025-12-20 02:31:27'),
(98, NULL, 28, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:32:44', '2025-12-20 02:32:44'),
(99, NULL, 29, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:34:59', '2025-12-20 02:34:59'),
(100, NULL, 30, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:36:14', '2025-12-20 02:36:14'),
(101, NULL, 31, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:37:05', '2025-12-20 02:37:05'),
(102, NULL, 32, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:37:56', '2025-12-20 02:37:56'),
(103, NULL, 33, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:38:47', '2025-12-20 02:38:47'),
(104, NULL, 34, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:41:17', '2025-12-20 02:41:17'),
(105, NULL, 24, 'order_sale', -40, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(106, NULL, 25, 'order_sale', -1, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(107, NULL, 26, 'order_sale', -1, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(108, NULL, 27, 'order_sale', -1, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(109, NULL, 28, 'order_sale', -1, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(110, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202512210001', 7, NULL, NULL, NULL, '2025-12-21 01:24:31', '2025-12-21 01:24:31'),
(111, NULL, 35, 'initial', 0, 'Initial stock entry', 7, NULL, NULL, NULL, '2025-12-21 01:47:00', '2025-12-21 01:47:00'),
(112, NULL, 35, 'order_sale', -1, 'Stock reduced for Order #ORD-202512210002', 7, NULL, NULL, NULL, '2025-12-21 01:49:09', '2025-12-21 01:49:09');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_histories`
--

CREATE TABLE `login_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` text NOT NULL,
  `login_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `logout_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_23_220555_create_employees_table', 1),
(5, '2025_06_23_220621_create_categories_table', 1),
(6, '2025_06_23_220726_create_products_table', 1),
(7, '2025_06_23_220735_create_product_variants_table', 1),
(8, '2025_06_23_221036_create_customers_table', 1),
(9, '2025_06_23_221056_create_suppliers_table', 1),
(10, '2025_06_23_221315_create_orders_table', 1),
(11, '2025_06_23_221318_create_order_items_table', 1),
(12, '2025_06_23_221439_create_purchases_table', 1),
(13, '2025_06_23_221453_create_purchase_items_table', 1),
(14, '2025_06_23_221602_create_inventory_logs_table', 1),
(15, '2025_06_23_221638_create_expenses_table', 1),
(16, '2025_06_23_221758_create_ecommerce_sync_logs_table', 1),
(17, '2025_06_23_233533_create_permission_tables', 2),
(18, '2025_06_23_234217_create_shifts_table', 3),
(19, '2025_06_23_234436_create_attendances_table', 3),
(20, '2025_06_23_235426_create_login_histories_table', 3),
(21, '2025_06_24_203557_add_role_to_users_table', 4),
(22, '2025_06_27_000328_add_action_and_details_to_inventory_logs_table', 5),
(23, '2025_06_28_010904_create_payments_table', 6),
(24, '2025_06_28_010959_create_refunds_table', 6),
(25, '2025_06_28_011024_create_receipts_table', 6),
(26, '2025_06_28_111040_add_tax_rate_to_orders_table', 7),
(27, '2025_06_28_112028_update_orders_table_with_defaults', 8),
(28, '2025_06_28_125555_add_track_inventory_to_products_table', 9),
(29, '2025_09_12_222416_create_payrolls_table', 10),
(30, '2025_09_12_233404_create_attendance_sessions_table', 11),
(31, '2025_09_12_234200_update_attendances_table_add_session_and_halfday', 12),
(32, '2025_09_14_161559_add_hours_to_payrolls_table', 13),
(33, '2025_09_14_164337_add_prices_and_customer_type', 14),
(34, '2025_09_14_183502_create_branches_table', 15);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_type` enum('pos','online') NOT NULL DEFAULT 'pos',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_charges` decimal(10,2) DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','cancelled','refunded') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 10.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dispatch_method` varchar(255) DEFAULT NULL,
  `tracking_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `user_id`, `order_type`, `subtotal`, `tax`, `discount`, `delivery_charges`, `weight`, `total`, `payment_method`, `status`, `notes`, `tax_rate`, `created_at`, `updated_at`, `dispatch_method`, `tracking_id`) VALUES
(8, 'ORD-202506280001', NULL, NULL, 'pos', 10000.00, 1000.00, 0.00, NULL, NULL, 11000.00, 'cash', 'completed', '', 10.00, '2025-06-28 07:58:39', '2025-06-28 07:58:39', NULL, NULL),
(9, 'ORD-202506280002', NULL, NULL, 'pos', 20000.00, 2000.00, 0.00, NULL, NULL, 22000.00, 'card', 'completed', '', 10.00, '2025-06-28 07:59:52', '2025-06-28 07:59:52', NULL, NULL),
(12, 'ORD-202506280003', NULL, NULL, 'pos', 4000.00, 400.00, 0.00, NULL, NULL, 4400.00, 'cash', 'completed', '', 10.00, '2025-06-28 08:04:37', '2025-06-28 08:04:37', NULL, NULL),
(14, 'ORD-202508140001', NULL, NULL, 'pos', 5200.00, 520.00, 0.00, NULL, NULL, 5720.00, 'cash', 'completed', '', 10.00, '2025-08-14 13:48:24', '2025-08-14 13:48:24', NULL, NULL),
(15, 'ORD-202508140002', NULL, NULL, 'pos', 1200.00, 120.00, 0.00, NULL, NULL, 1320.00, 'cash', 'completed', '', 10.00, '2025-08-14 15:01:28', '2025-08-14 15:01:28', NULL, NULL),
(16, 'ORD-202508140003', NULL, NULL, 'pos', 6000.00, 600.00, 0.00, NULL, NULL, 6600.00, 'cash', 'completed', '', 10.00, '2025-08-14 15:32:05', '2025-08-14 15:32:05', NULL, NULL),
(17, 'ORD-202508140004', NULL, NULL, 'pos', 6000.00, 600.00, 0.00, NULL, NULL, 6600.00, 'cash', 'completed', '', 10.00, '2025-08-14 15:45:16', '2025-08-14 15:45:16', NULL, NULL),
(18, 'ORD-202508140005', NULL, NULL, 'pos', 7200.00, 720.00, 0.00, NULL, NULL, 7920.00, 'cash', 'completed', '', 10.00, '2025-08-14 16:04:41', '2025-08-14 16:04:41', NULL, NULL),
(19, 'ORD-202508150001', NULL, NULL, 'pos', 2000.00, 200.00, 0.00, NULL, NULL, 2200.00, 'cash', 'completed', '', 10.00, '2025-08-15 18:18:43', '2025-08-15 18:18:43', NULL, NULL),
(20, 'ORD-202509140001', 3, NULL, 'pos', 4750.00, 475.00, 0.00, NULL, NULL, 5225.00, 'cash', 'completed', '', 10.00, '2025-09-14 13:23:33', '2025-09-14 13:23:33', NULL, NULL),
(21, 'ORD-202509140002', NULL, 2, 'pos', 13550.00, 1355.00, 0.00, NULL, NULL, 14905.00, 'cash', 'completed', '', 10.00, '2025-09-14 14:34:08', '2025-09-14 14:34:08', NULL, NULL),
(22, 'ORD-202509140003', NULL, 2, 'pos', 7500.00, 750.00, 0.00, NULL, NULL, 8250.00, 'cash', 'completed', '', 10.00, '2025-09-14 14:42:34', '2025-09-14 14:42:34', NULL, NULL),
(23, 'ORD-202509140004', 3, 2, 'pos', 4250.00, 425.00, 0.00, NULL, NULL, 4675.00, 'cash', 'completed', '', 10.00, '2025-09-14 14:48:26', '2025-09-14 14:48:26', NULL, NULL),
(24, 'ORD-202509150001', NULL, 2, 'pos', 450.00, 45.00, 0.00, NULL, NULL, 495.00, 'cash', 'completed', '', 10.00, '2025-09-15 04:37:21', '2025-09-15 04:37:21', NULL, NULL),
(25, 'ORD-202509150002', 3, 2, 'pos', 7500.00, 750.00, 0.00, NULL, NULL, 8250.00, 'cash', 'completed', '', 10.00, '2025-09-15 06:32:26', '2025-09-15 06:32:26', NULL, NULL),
(26, 'ORD-202509160001', NULL, 2, 'pos', 450.00, 45.00, 0.00, NULL, NULL, 495.00, 'cash', 'completed', '', 10.00, '2025-09-16 02:03:41', '2025-09-16 02:03:41', NULL, NULL),
(27, 'ORD-202509170001', NULL, 2, 'pos', 100.00, 10.00, 0.00, NULL, NULL, 110.00, 'cash', 'completed', '', 10.00, '2025-09-17 00:59:36', '2025-09-17 00:59:36', NULL, NULL),
(28, 'ORD-202509180001', NULL, 2, 'pos', 100.00, 10.00, 0.00, NULL, NULL, 110.00, 'cash', 'completed', '', 10.00, '2025-09-18 03:38:13', '2025-09-18 03:38:13', NULL, NULL),
(29, 'ORD-202509210001', NULL, 2, 'pos', 800.00, 120.00, 0.00, NULL, NULL, 920.00, 'cash', 'completed', '', 15.00, '2025-09-21 18:47:11', '2025-09-21 18:47:11', 'TCS', '2345342'),
(30, 'ORD-202509210002', NULL, 2, 'pos', 6799.00, 135.98, 0.00, NULL, NULL, 6934.98, 'cash', 'completed', '', 2.00, '2025-09-21 18:56:01', '2025-09-21 18:56:01', 'Self Pickup', NULL),
(31, 'ORD-202509210003', NULL, 2, 'pos', 3019.00, 60.38, 0.00, NULL, NULL, 3079.38, 'cash', 'completed', '', 2.00, '2025-09-21 18:57:13', '2025-09-21 18:57:13', 'Self Pickup', NULL),
(32, 'ORD-202509220001', NULL, 2, 'pos', 900.00, 56.25, 0.00, NULL, NULL, 956.25, 'cash', 'completed', '', 6.25, '2025-09-21 23:27:24', '2025-09-21 23:27:24', 'Pak Post', 'RGL161038382'),
(35, 'ASM1', NULL, 2, 'pos', 750.00, 15.00, 0.00, NULL, NULL, 765.00, 'cash', 'completed', '', 2.00, '2025-09-22 07:37:25', '2025-09-22 07:37:25', 'Pak Post', 'rte5345'),
(36, 'ASM2', NULL, 2, 'pos', 10493.00, 209.86, 0.00, NULL, NULL, 10702.86, 'cash', 'completed', '', 2.00, '2025-09-22 07:37:44', '2025-09-22 07:37:44', 'Self Pickup', NULL),
(37, 'ASM3', NULL, 2, 'pos', 250.00, 5.00, 0.00, NULL, NULL, 255.00, 'cash', 'completed', '', 2.00, '2025-09-22 07:38:15', '2025-09-22 07:38:15', 'Self Pickup', NULL),
(38, 'ASM4', NULL, 2, 'pos', 100.00, 2.00, 0.00, NULL, NULL, 102.00, 'card', 'completed', '', 2.00, '2025-09-22 07:48:48', '2025-09-22 07:48:48', 'Self Pickup', NULL),
(42, 'ORD-202509290001', 6, 2, 'pos', 2250.00, 0.00, 50.00, 100.00, 0.50, 2300.00, 'easypaisa', 'completed', '', 0.00, '2025-09-29 01:23:53', '2025-09-29 01:23:53', 'Pak Post', '1213112'),
(43, 'ORD-202510010001', 7, 2, 'pos', 600.00, 0.00, 0.00, 150.00, 170.00, 750.00, 'jazzcash', 'completed', '', 0.00, '2025-10-01 06:35:06', '2025-10-01 06:35:06', 'Pak Post', 'RGL162035563'),
(44, 'ORD-202510020001', 8, 2, 'pos', 5300.00, 0.00, 0.00, 600.00, 2.00, 5900.00, 'cod', 'completed', '', 0.00, '2025-10-02 03:41:43', '2025-10-02 03:41:43', 'TCS', '774891300208'),
(49, 'ORD-202511270001', NULL, 2, 'pos', 1799.00, 35.98, 0.00, 0.00, 0.00, 1834.98, 'cod', 'completed', '', 2.00, '2025-11-27 12:43:35', '2025-11-27 12:43:35', 'Self Pickup', NULL),
(50, 'ORD-202512130001', NULL, 2, 'pos', 5998.00, 0.00, 1198.00, 200.00, 2.00, 5000.00, 'bank', 'completed', '', 0.00, '2025-12-13 12:22:24', '2025-12-13 12:22:24', 'TCS', '774891300325'),
(51, 'ORD-202512200001', 10, 2, 'pos', 90415.00, 0.00, 4000.00, 0.00, 96.24, 86415.00, 'easypaisa', 'completed', '', 0.00, '2025-12-20 02:47:50', '2025-12-20 02:47:50', 'Self Pickup', NULL),
(52, 'ORD-202512210001', 3, 7, 'pos', 1299.00, 0.00, 99.00, 0.00, 0.00, 1200.00, 'jazzcash', 'completed', '', 0.00, '2025-12-21 01:24:31', '2025-12-21 01:24:31', 'Self Pickup', NULL),
(53, 'ORD-202512210002', NULL, 7, 'pos', 800.00, 200.00, 0.00, 0.00, 1.57, 1000.00, 'jazzcash', 'completed', '', 25.00, '2025-12-21 01:49:09', '2025-12-21 01:49:09', 'Self Pickup', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `variant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variant_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(8, 8, 1, NULL, 5, 2000.00, 10000.00, '2025-06-28 07:58:39', '2025-06-28 07:58:39'),
(9, 9, 7, NULL, 10, 2000.00, 20000.00, '2025-06-28 07:59:52', '2025-06-28 07:59:52'),
(12, 12, 7, NULL, 2, 2000.00, 4000.00, '2025-06-28 08:04:37', '2025-06-28 08:04:37'),
(14, 14, 9, NULL, 3, 400.00, 1200.00, '2025-08-14 13:48:24', '2025-08-14 13:48:24'),
(15, 14, 8, NULL, 1, 2000.00, 2000.00, '2025-08-14 13:48:24', '2025-08-14 13:48:24'),
(16, 14, 1, NULL, 1, 2000.00, 2000.00, '2025-08-14 13:48:24', '2025-08-14 13:48:24'),
(17, 15, 9, NULL, 3, 400.00, 1200.00, '2025-08-14 15:01:28', '2025-08-14 15:01:28'),
(18, 16, 1, NULL, 2, 2000.00, 4000.00, '2025-08-14 15:32:05', '2025-08-14 15:32:05'),
(19, 16, 7, NULL, 1, 2000.00, 2000.00, '2025-08-14 15:32:05', '2025-08-14 15:32:05'),
(20, 17, 8, NULL, 2, 2000.00, 4000.00, '2025-08-14 15:45:16', '2025-08-14 15:45:16'),
(21, 17, 7, NULL, 1, 2000.00, 2000.00, '2025-08-14 15:45:16', '2025-08-14 15:45:16'),
(22, 18, 7, NULL, 1, 2000.00, 2000.00, '2025-08-14 16:04:41', '2025-08-14 16:04:41'),
(23, 18, 8, NULL, 1, 2000.00, 2000.00, '2025-08-14 16:04:41', '2025-08-14 16:04:41'),
(24, 18, 9, NULL, 3, 400.00, 1200.00, '2025-08-14 16:04:41', '2025-08-14 16:04:41'),
(25, 18, 1, NULL, 1, 2000.00, 2000.00, '2025-08-14 16:04:41', '2025-08-14 16:04:41'),
(26, 19, 8, NULL, 1, 2000.00, 2000.00, '2025-08-15 18:18:43', '2025-08-15 18:18:43'),
(27, 20, 8, NULL, 2, 1500.00, 3000.00, '2025-09-14 13:23:33', '2025-09-14 13:23:33'),
(28, 20, 9, NULL, 1, 250.00, 250.00, '2025-09-14 13:23:33', '2025-09-14 13:23:33'),
(29, 20, 1, NULL, 1, 1500.00, 1500.00, '2025-09-14 13:23:33', '2025-09-14 13:23:33'),
(30, 21, 9, NULL, 3, 250.00, 750.00, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(31, 21, 8, NULL, 2, 1500.00, 3000.00, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(32, 21, 7, NULL, 2, 2500.00, 5000.00, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(33, 21, 1, NULL, 2, 1500.00, 3000.00, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(34, 21, 10, NULL, 4, 450.00, 1800.00, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(35, 22, 1, NULL, 5, 1500.00, 7500.00, '2025-09-14 14:42:34', '2025-09-14 14:42:34'),
(36, 23, 7, NULL, 1, 2500.00, 2500.00, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(37, 23, 8, NULL, 1, 1500.00, 1500.00, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(38, 23, 9, NULL, 1, 250.00, 250.00, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(39, 24, 10, NULL, 1, 450.00, 450.00, '2025-09-15 04:37:21', '2025-09-15 04:37:21'),
(40, 25, 7, NULL, 3, 2500.00, 7500.00, '2025-09-15 06:32:26', '2025-09-15 06:32:26'),
(41, 26, 10, NULL, 1, 450.00, 450.00, '2025-09-16 02:03:41', '2025-09-16 02:03:41'),
(42, 27, 8, NULL, 1, 100.00, 100.00, '2025-09-17 00:59:36', '2025-09-17 00:59:36'),
(43, 28, 8, NULL, 1, 100.00, 100.00, '2025-09-18 03:38:13', '2025-09-18 03:38:13'),
(44, 29, 9, NULL, 2, 400.00, 800.00, '2025-09-21 18:47:11', '2025-09-21 18:47:11'),
(45, 30, 1, NULL, 3, 1500.00, 4500.00, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(46, 30, 7, NULL, 1, 1799.00, 1799.00, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(47, 30, 8, NULL, 1, 100.00, 100.00, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(48, 30, 9, NULL, 1, 400.00, 400.00, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(49, 31, 7, NULL, 1, 1550.00, 1550.00, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(50, 31, 8, NULL, 1, 70.00, 70.00, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(51, 31, 9, NULL, 1, 300.00, 300.00, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(52, 31, 10, NULL, 1, 1099.00, 1099.00, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(53, 32, 14, NULL, 1, 900.00, 900.00, '2025-09-21 23:27:24', '2025-09-21 23:27:24'),
(56, 35, 12, NULL, 3, 250.00, 750.00, '2025-09-22 07:37:25', '2025-09-22 07:37:25'),
(57, 36, 13, NULL, 7, 1499.00, 10493.00, '2025-09-22 07:37:44', '2025-09-22 07:37:44'),
(58, 37, 12, NULL, 1, 250.00, 250.00, '2025-09-22 07:38:15', '2025-09-22 07:38:15'),
(59, 38, 8, NULL, 1, 100.00, 100.00, '2025-09-22 07:48:48', '2025-09-22 07:48:48'),
(64, 42, 18, NULL, 1, 500.00, 500.00, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(65, 42, 20, NULL, 1, 950.00, 950.00, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(66, 42, 19, NULL, 1, 800.00, 800.00, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(67, 43, 21, NULL, 1, 600.00, 600.00, '2025-10-01 06:35:06', '2025-10-01 06:35:06'),
(68, 44, 22, NULL, 2, 100.00, 200.00, '2025-10-02 03:41:43', '2025-10-02 03:41:43'),
(69, 44, 11, NULL, 6, 850.00, 5100.00, '2025-10-02 03:41:43', '2025-10-02 03:41:43'),
(85, 49, 7, NULL, 1, 1799.00, 1799.00, '2025-11-27 12:43:35', '2025-11-27 12:43:35'),
(86, 50, 23, NULL, 2, 2999.00, 5998.00, '2025-12-13 12:22:24', '2025-12-13 12:22:24'),
(87, 51, 24, NULL, 40, 2250.00, 90000.00, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(88, 51, 25, NULL, 1, 75.00, 75.00, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(89, 51, 26, NULL, 1, 90.00, 90.00, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(90, 51, 27, NULL, 1, 125.00, 125.00, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(91, 51, 28, NULL, 1, 125.00, 125.00, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(92, 52, 10, NULL, 1, 1299.00, 1299.00, '2025-12-21 01:24:31', '2025-12-21 01:24:31'),
(93, 53, 35, NULL, 1, 800.00, 800.00, '2025-12-21 01:49:09', '2025-12-21 01:49:09');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(255) NOT NULL DEFAULT 'cash',
  `reference` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `present_days` int(11) NOT NULL DEFAULT 0,
  `absent_days` int(11) NOT NULL DEFAULT 0,
  `late_days` int(11) NOT NULL DEFAULT 0,
  `gross_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `hourly_rate` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payrolls`
--

INSERT INTO `payrolls` (`id`, `employee_id`, `month`, `year`, `present_days`, `absent_days`, `late_days`, `gross_salary`, `deductions`, `net_salary`, `status`, `created_at`, `updated_at`, `total_hours`, `hourly_rate`) VALUES
(37, 4, 9, 2025, 29, 0, 0, 5000.00, -174.28, 5174.28, 'unpaid', '2025-09-16 02:22:31', '2025-10-03 15:43:21', 215.25, 24.04),
(38, 5, 9, 2025, 14, 0, 0, 12000.00, 1898.08, 10101.92, 'unpaid', '2025-10-03 15:43:21', '2025-10-03 15:43:21', 175.10, 57.69);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view dashboard', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(2, 'manage products', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(3, 'manage categories', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(4, 'process orders', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(5, 'manage employees', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(6, 'manage reports', 'web', '2025-06-23 18:41:58', '2025-08-15 18:13:42'),
(7, 'manage inventory', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(8, 'manage attendance', 'web', '2025-08-15 17:14:00', '2025-08-15 17:14:00'),
(9, 'manage variants', 'web', '2025-08-15 17:14:00', '2025-08-15 17:14:00'),
(10, 'manage purchases', 'web', '2025-08-15 17:14:00', '2025-08-15 17:14:00'),
(11, 'manage suppliers', 'web', '2025-08-15 17:14:00', '2025-08-15 17:14:00'),
(12, 'access pos', 'web', '2025-08-15 17:14:00', '2025-08-15 17:14:00'),
(13, 'manage roles', 'web', '2025-08-15 17:14:00', '2025-08-15 17:14:00'),
(14, 'manage permissions', 'web', '2025-08-15 17:14:00', '2025-08-15 17:14:00'),
(15, 'assign roles', 'web', '2025-08-15 17:14:00', '2025-08-15 17:14:00'),
(16, 'manage payroll', 'web', '2025-09-14 16:23:35', '2025-09-14 16:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `resale_price` decimal(10,2) DEFAULT NULL,
  `wholesale_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `reorder_level` int(11) NOT NULL DEFAULT 10,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `track_inventory` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `barcode`, `description`, `price`, `sale_price`, `resale_price`, `wholesale_price`, `cost_price`, `weight`, `stock_quantity`, `reorder_level`, `image`, `is_active`, `created_at`, `updated_at`, `track_inventory`) VALUES
(1, 4, 'Al Quran Al Kareem', '313', 'Tarjuma Allama Ali Naqi Naqan Sahb', 2000.00, 1500.00, 1250.00, 1200.00, 1030.00, NULL, -11, 3, 'products/8LRzHKiD2L0RSekKX5Qz6ZqXGnef72L8MWxFkNjW.jpg', 1, '2025-06-26 19:00:13', '2025-12-20 02:41:53', 1),
(7, 4, 'Balagh Ul Quran Samall', '314', 'Tarjuma Allama Sheikh Mohsin Ali Najfi', 2000.00, 1799.00, 1550.00, 1450.00, 1230.00, NULL, -212, 3, 'products/a0ngX7d4AmpBgAQZaeDjox4Hu2BKfFVXuoX4LP7m.jpg', 1, '2025-06-26 19:50:58', '2025-12-20 02:41:57', 1),
(8, 4, 'Yasarnal Quran', '315', 'Quaida', 2000.00, 100.00, 70.00, 65.00, 1200.00, NULL, 10, 20, 'products/Z3RIBfaiHeUB4vhCRkyEFhu85nyZIy3X3xsd8MEG.jpg', 1, '2025-06-26 19:57:14', '2025-12-20 02:41:53', 1),
(9, 4, 'Quran Pocket Size', '316', 'With out Tarjuma', 400.00, 400.00, 300.00, 280.00, 240.00, NULL, -9, 5, 'products/9B9zeYdy460ja9ef4nBuJ2xqJXC2h36Zs9boxGgm.jpg', 1, '2025-06-26 19:57:15', '2025-12-20 02:41:53', 1),
(10, 4, 'Quran Mubeen', '317', 'Tarjuma Allama M Hussain Akbar', NULL, 1299.00, 1099.00, 999.00, 730.00, NULL, 1, 2, 'products/jTcGBziyP0B4J3m3aGCjbd8MDkl9Kw2NSffGB5SE.jpg', 1, '2025-09-14 13:09:06', '2025-12-21 01:24:31', 1),
(11, 5, 'Namaz Chadar Open Swiss-M', '318', 'Swiss Lawn Open Round, Irani Style Namaz Chadar ', NULL, 1000.00, 850.00, 790.00, 600.00, NULL, 44, 20, NULL, 1, '2025-09-17 07:48:38', '2025-10-02 03:41:43', 1),
(12, 6, 'Syed e Muqawimat', '319', 'Book', NULL, 250.00, 200.00, 175.00, 148.00, NULL, 46, 5, 'products/0lrY4EqBs3Hc1Dt9gyuNy51dloKUyEvpbe8IBPe6.jpg', 1, '2025-09-21 05:51:13', '2025-09-22 07:38:15', 1),
(13, 4, 'Anwar ul Quran (DiarySize)', '320', 'Quran Majeed', NULL, 1499.00, 1250.00, 1150.00, 1020.00, NULL, 9, 3, 'products/0pDjmL2TYVwLMkCpblZw4DUX9Gih0JtJ2q0VUm0C.jpg', 1, '2025-09-21 06:14:54', '2025-09-22 07:37:44', 1),
(14, 5, 'Namaz Chadar Swiss Lawn Open-L', '321', 'Namaz Chadar open', NULL, 1200.00, 900.00, 800.00, 680.00, NULL, 98, 20, NULL, 1, '2025-09-21 23:11:45', '2025-09-21 23:27:24', 1),
(18, 7, 'Valvet Jay Namaz Small', '322', 'Jay Namaz', NULL, 650.00, 500.00, 420.00, 270.00, 0.40, 47, 5, 'products/7lylFr8NMcBF2rdL8t7Z0X28tY4TmJxTiOW6DHgJ.jpg', 1, '2025-09-26 23:10:41', '2025-10-03 16:58:31', 1),
(19, 7, 'Valvet Jay Namaz Medium', '323', 'Jay Namaz', NULL, 950.00, 800.00, 700.00, 500.00, 1.15, 48, 5, 'products/Yib1NaQbMQU0ZJBK2vpl5n4Ho2jVvaWvhBFnepHP.jpg', 1, '2025-09-26 23:14:03', '2025-10-03 16:58:31', 1),
(20, 7, 'Valvet Jay Namaz Large', '324', 'Jay Namaz', NULL, 1150.00, 950.00, 890.00, 600.00, 1.50, 66, 5, 'products/TFNLpua7XboEjyCNR8XZFqtC4c8qrnRrnXVyABi5.jpg', 1, '2025-09-26 23:16:59', '2025-10-03 16:58:31', 1),
(21, 8, 'Printed Basiji', '325', 'Basiji Romal Printed', NULL, 700.00, 600.00, 550.00, 370.00, 0.16, 18, 5, 'products/7kfonooKkoUt6DHuQstMSXb2CDBkoVVMMItQDPO9.jpg', 1, '2025-10-01 01:35:35', '2025-10-03 16:58:31', 1),
(22, 7, 'Hadia Hussaini', '326', 'Sajda Gah Tasbeeh Box', NULL, 120.00, 100.00, 95.00, 83.00, 0.08, 10, 5, 'products/iFqe5nsKIAxn23phM8SVUs5UXhlbTDvzFIb6CBeN.jpg', 1, '2025-10-02 03:39:32', '2025-10-03 16:58:31', 1),
(23, 5, 'Irani Hijab Round Chadar ', ' 327  ', '', NULL, 2999.00, 2799.00, 2699.00, 2200.00, 1.00, 8, 5, 'products/9pCAGDNcECQ4nb1ssB4iC93ywwHV8QXsDvui3uj6.jpg', 1, '2025-12-13 11:45:20', '2025-12-13 12:22:24', 1),
(24, 4, 'Balagh ul Quran', '328', 'VIP', NULL, 2500.00, 2250.00, 2100.00, 1845.00, 2.40, 20, 5, 'products/FvMpcH4v9yEq4dagigakRz2OCZIzay4SI4qD2frN.jpg', 1, '2025-12-20 02:23:14', '2025-12-20 02:47:50', 1),
(25, 7, 'Dua e Tawasal', '329', 'Dua ', NULL, 80.00, 75.00, 65.00, 50.00, 0.04, 4, 5, NULL, 1, '2025-12-20 02:26:29', '2025-12-20 02:47:50', 1),
(26, 7, 'Dua e Kumail', '330', 'Dua', NULL, 120.00, 90.00, 80.00, 70.00, 0.04, 4, 5, NULL, 1, '2025-12-20 02:29:10', '2025-12-20 02:47:50', 1),
(27, 7, 'Dua e Joshan Kabeer', '331', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.11, 4, 5, NULL, 1, '2025-12-20 02:31:27', '2025-12-20 02:47:50', 1),
(28, 7, 'Dua e Noor', '332', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.05, 4, 5, NULL, 1, '2025-12-20 02:32:44', '2025-12-20 02:47:50', 1),
(29, 7, 'Ziarat e Aal e Yaseen', '333', 'Dua', NULL, 100.00, 80.00, 75.00, 70.00, 0.04, 5, 5, NULL, 1, '2025-12-20 02:34:59', '2025-12-20 02:44:35', 1),
(30, 7, 'Dua e Nudba', '334', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.08, 5, 5, NULL, 1, '2025-12-20 02:36:14', '2025-12-20 02:44:35', 1),
(31, 7, 'Ziarat e Nahia o Warisa', '335', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.07, 5, 5, NULL, 1, '2025-12-20 02:37:05', '2025-12-20 02:44:35', 1),
(32, 7, 'Majmoa Dua', '336', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.08, 5, 5, NULL, 1, '2025-12-20 02:37:56', '2025-12-20 02:44:35', 1),
(33, 7, 'Ziarat e Ashoora o Arbaeen', '337', 'Dua', NULL, 180.00, 160.00, 145.00, 130.00, 0.08, 5, 5, NULL, 1, '2025-12-20 02:38:47', '2025-12-20 02:44:35', 1),
(34, 7, 'Taqibat e Namaz', '338', 'Dua', NULL, 300.00, 250.00, 230.00, 210.00, 0.15, 5, 5, NULL, 1, '2025-12-20 02:41:17', '2025-12-21 01:47:22', 1),
(35, 4, 'Quran al Kareem 150-L', '339', 'With out Translation Quran', NULL, 800.00, 700.00, 620.00, 575.00, 1.57, 0, 2, 'products/SADKLHMhOXUjaErze5DObSRW7Z0BF1KqTnXhTdgW.jpg', 1, '2025-12-21 01:47:00', '2025-12-21 01:49:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `variant_name` varchar(255) NOT NULL,
  `variant_value` varchar(255) NOT NULL,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('paid','partial','unpaid') NOT NULL DEFAULT 'unpaid',
  `purchase_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `invoice_number`, `total_amount`, `paid_amount`, `payment_status`, `purchase_date`, `notes`, `created_at`, `updated_at`) VALUES
(9, 6, 'INV-Z6PXXTRI', 5400.00, 5400.00, 'paid', '2025-12-18', '', '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(10, 7, 'INV-AHZNHJXC', 110700.00, 72700.00, 'partial', '2025-12-20', '', '2025-12-20 02:46:10', '2025-12-20 02:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(15, 9, 25, 5, 50.00, 250.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(16, 9, 26, 5, 70.00, 350.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(17, 9, 27, 5, 110.00, 550.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(18, 9, 28, 5, 110.00, 550.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(19, 9, 29, 5, 70.00, 350.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(20, 9, 30, 5, 110.00, 550.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(21, 9, 31, 5, 110.00, 550.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(22, 9, 32, 5, 110.00, 550.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(23, 9, 33, 5, 130.00, 650.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(24, 9, 34, 5, 210.00, 1050.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35'),
(25, 10, 24, 60, 1845.00, 110700.00, '2025-12-20 02:46:10', '2025-12-20 02:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `receipt_number` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(2, 'manager', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(3, 'cashier', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(4, 'employee', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(6, 1),
(6, 2),
(6, 3),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(8, 4),
(9, 1),
(10, 1),
(10, 2),
(11, 1),
(12, 1),
(12, 2),
(12, 3),
(13, 1),
(14, 1),
(15, 1),
(16, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('fnS7gxX2e2gbRv9flsr4CaqSJFRbbgYpgaH39IPQ', 2, '59.103.216.213', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWmxkY1ZRUUN5dWZqTDNEa3c0VmpQc2tGaG9ySUhudmJNUHFYOGF5ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vcG9zLmFsbXVmZWVkLmNvbS5way9wb3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1766436759),
('JnrzVZgSTMr2wHZE4joxViHZsTqznYonUxNL4o4J', 2, '154.80.72.225', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTVZCd0I0T0lPaUlXSUxaRnh2WGJ5SFVXNXJXekFDSnJkckZONmxwZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vcG9zLmFsbXVmZWVkLmNvbS5way9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1766400010),
('KKf3DhuUcfLGtp8kk7EBQk62M3XyvzGEvjLG2ClG', 6, '223.123.11.8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRm5lUEZJSUE0bDdjM2ZNZ1NQblE0anJSbHQ2Z09BSnpnaHRqcmo1VSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM4OiJodHRwczovL3Bvcy5hbG11ZmVlZC5jb20ucGsvYXR0ZW5kYW5jZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjY7fQ==', 1766415927),
('LpGSccEJ7S4qlWuzqi2bM4fuN5efNXCigpm3chGI', NULL, '154.81.232.200', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieFUzT0dwdXJsYUpBdVVPbndTYm5rdGRXMkNISW16bGFsdUtaSlpBRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vcG9zLmFsbXVmZWVkLmNvbS5wayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1766415658),
('Q3NW73AEDYNCpCWWfN6Xzi5ovCx86kJPktrhWGsK', NULL, '66.249.93.76', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiallSS2xEN0xVZFdTdk8zNVVOTVBEVmRLaFhnZkVOTHYwUko0RXltdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MzoiaHR0cHM6Ly9wb3MuYWxtdWZlZWQuY29tLnBrL2FkbWluL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQzOiJodHRwczovL3Bvcy5hbG11ZmVlZC5jb20ucGsvYWRtaW4vZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1766411293),
('SESyV2judgXkeyhPTomyWmdNhuI4rYPv3UJFYTVv', 2, '223.123.11.143', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSDFxQUNiWVNhY3Rlc0Y0c0tpNUZocExHWmwyTk5Wa3BoUjRuTUxVRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vcG9zLmFsbXVmZWVkLmNvbS5way9wcm9kdWN0cy9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1766415700),
('SM42NrSLt8dW0ChW5Rq9P079hTydwdN81N7TZnWS', NULL, '66.249.93.76', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic29IY2pobFJlWm5saGkyOGVOMENPZ29teE9MZkpMQXNGeEtPdjBpSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vcG9zLmFsbXVmZWVkLmNvbS5way9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1766411293),
('vK6IkvbHYsRDJIClLnI8T2gPhFJzTYzc3UM8NNgX', 2, '154.80.94.8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWXVyNEdNVmpSZUQ0SVhseWVhUkJFbFJvdGNlc0xvaTNZTGFjRDd6RSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vcG9zLmFsbXVmZWVkLmNvbS5way9hdHRlbmRhbmNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1766406959);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `email`, `phone`, `address`, `company_name`, `created_at`, `updated_at`) VALUES
(6, 'Alamdar Bhai', '', '+923335234311', 'Islam Abad', 'M Ali Book Depot ISB', '2025-06-27 19:56:46', '2025-12-20 02:13:48'),
(7, 'M Ali (Balagh ul Quran)', '', '+923455396506', 'Jamia Al Kosar Islamabad', 'Al Koser Balagh ul QUran', '2025-12-20 02:15:46', '2025-12-20 02:15:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','manager','cashier','employee') NOT NULL DEFAULT 'cashier',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Furqan', 'furqan7853@gmail.com', '$2y$12$oigNiCjBO2ZSNApzOXcJuuf9UaJK.EMuvlNIBOXDmJ3MJ8t84Aoj.', 'cashier', NULL, NULL, '2025-06-23 18:31:02', '2025-06-23 18:32:53'),
(2, 'Admin', 'admin@gmail.com', '$2y$12$Yh./6bb2oRHY2PEYi.4AgeJeIP98hV1uqd5hd6nSd88GPj4jW0IDe', 'admin', '2025-09-14 19:07:43', 'dkPGX2hcwkkcH4iMoVF8P3npPPB8kasxip0pivf8xZJepp20MDK70XWyGAJs', '2025-09-14 19:07:43', '2025-09-14 19:07:43'),
(6, 'Naseem Abbas', '7747789@gmail.com', '$2y$12$u1j0/LESIZL/kCFGiNj1RuWmR7PGyMHFckInoVJ9wSqcCCCHnWdUG', 'cashier', NULL, 'M5n3V13tctWsGzKvmeP3HMNlJ8hbsGZDHUKciEPZ4dyKkjpNtuQXIp737gCm', '2025-09-16 02:15:12', '2025-09-16 02:15:12'),
(7, 'Ali Kumail', '7212469@gmail.com', '$2y$12$odTKHGvMJcn0G3Hbyt1Y0.F9MGNPdFB9guDh75X/OZr1u/8tR2lfa', 'cashier', NULL, 'XlvJV4E2SzmYiLvmu6W4BmBUdb0VMmp1tXPFUmeg3ME3c9JbuQkXoc3KR02B', '2025-09-16 07:00:57', '2025-09-16 07:01:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_date_session` (`employee_id`,`date`,`session`);

--
-- Indexes for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_sessions_attendance_id_foreign` (`attendance_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_code_unique` (`code`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ecommerce_sync_logs`
--
ALTER TABLE `ecommerce_sync_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ecommerce_sync_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employees_user_id_foreign` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_logs_purchase_id_foreign` (`purchase_id`),
  ADD KEY `inventory_logs_product_id_foreign` (`product_id`),
  ADD KEY `inventory_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_histories`
--
ALTER TABLE `login_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_histories_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_variant_id_foreign` (`variant_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payrolls_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_barcode_unique` (`barcode`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchases_invoice_number_unique` (`invoice_number`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipts_receipt_number_unique` (`receipt_number`),
  ADD KEY `receipts_order_id_foreign` (`order_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `refunds_order_id_foreign` (`order_id`),
  ADD KEY `refunds_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shifts_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ecommerce_sync_logs`
--
ALTER TABLE `ecommerce_sync_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_histories`
--
ALTER TABLE `login_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD CONSTRAINT `attendance_sessions_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ecommerce_sync_logs`
--
ALTER TABLE `ecommerce_sync_logs`
  ADD CONSTRAINT `ecommerce_sync_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_logs_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_histories`
--
ALTER TABLE `login_histories`
  ADD CONSTRAINT `login_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
