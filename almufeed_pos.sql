-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 09, 2026 at 11:53 AM
-- Server version: 9.5.0
-- PHP Version: 8.4.17

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
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `session` enum('morning','evening','night') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'morning',
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('present','absent','late','on_leave','half_day') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `branch_id`, `employee_id`, `date`, `session`, `check_in`, `check_out`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(45, 1, 4, '2025-09-16', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:18:07', '2025-09-16 02:18:07'),
(46, 1, 4, '2025-09-15', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:20:55', '2025-09-16 02:20:55'),
(47, 1, 4, '2025-09-14', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:21:37', '2025-09-16 02:21:37'),
(48, 1, 4, '2025-09-01', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:23:24', '2025-09-16 02:23:24'),
(49, 1, 4, '2025-09-02', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:23:58', '2025-09-16 02:23:58'),
(50, 1, 4, '2025-09-03', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:24:28', '2025-09-16 02:24:28'),
(51, 1, 4, '2025-09-04', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:25:13', '2025-09-16 02:25:13'),
(52, 1, 4, '2025-09-05', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:25:45', '2025-09-16 02:25:45'),
(53, 1, 4, '2025-09-06', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:26:08', '2025-09-16 02:26:08'),
(54, 1, 4, '2025-09-07', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:26:40', '2025-09-16 02:26:40'),
(55, 1, 4, '2025-09-08', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:27:10', '2025-09-16 02:27:10'),
(56, 1, 4, '2025-09-09', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:27:39', '2025-09-16 02:27:39'),
(57, 1, 4, '2025-09-10', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:28:08', '2025-09-16 02:28:08'),
(58, 1, 4, '2025-09-11', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:28:35', '2025-09-16 02:28:35'),
(59, 1, 4, '2025-09-12', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:29:10', '2025-09-16 02:29:10'),
(60, 1, 4, '2025-09-13', 'morning', NULL, NULL, 'present', '', '2025-09-16 02:29:36', '2025-09-16 02:29:36'),
(61, 1, 5, '2025-09-16', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:06:28', '2025-09-16 07:06:28'),
(62, 1, 5, '2025-09-15', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:07:14', '2025-09-16 07:07:14'),
(63, 1, 5, '2025-09-14', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:07:54', '2025-09-16 07:07:54'),
(64, 1, 5, '2025-09-13', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:09:33', '2025-09-16 07:09:33'),
(65, 1, 5, '2025-09-12', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:09:56', '2025-09-16 07:09:56'),
(66, 1, 5, '2025-09-11', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:10:19', '2025-09-16 07:10:19'),
(67, 1, 5, '2025-09-10', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:10:46', '2025-09-16 07:10:46'),
(68, 1, 5, '2025-09-09', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:11:20', '2025-09-16 07:11:20'),
(69, 1, 5, '2025-09-08', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:11:55', '2025-09-16 07:11:55'),
(70, 1, 5, '2025-09-06', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:12:44', '2025-09-16 07:12:44'),
(71, 1, 5, '2025-09-05', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:13:17', '2025-09-16 07:13:17'),
(72, 1, 5, '2025-09-03', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:13:42', '2025-09-16 07:13:42'),
(73, 1, 5, '2025-09-02', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:14:18', '2025-09-16 07:14:18'),
(74, 1, 5, '2025-09-01', 'morning', NULL, NULL, 'present', '', '2025-09-16 07:14:50', '2025-09-16 07:14:50'),
(75, 1, 4, '2025-09-17', 'morning', NULL, NULL, 'present', '', '2025-09-16 22:42:37', '2025-09-16 22:42:37'),
(76, 1, 4, '2025-09-18', 'morning', NULL, NULL, 'present', '', '2025-09-18 00:25:00', '2025-09-18 00:25:00'),
(77, 1, 4, '2025-09-19', 'morning', NULL, NULL, 'present', '', '2025-09-18 22:41:39', '2025-09-18 22:41:39'),
(78, 1, 4, '2025-09-22', 'morning', NULL, NULL, 'present', '', '2025-09-22 01:48:38', '2025-09-22 01:48:38'),
(79, 1, 4, '2025-09-20', 'morning', NULL, NULL, 'present', '', '2025-09-22 01:49:15', '2025-09-22 01:49:15'),
(80, 1, 4, '2025-09-21', 'morning', NULL, NULL, 'present', '', '2025-09-22 01:49:41', '2025-09-22 01:49:41'),
(81, 1, 4, '2025-09-23', 'morning', NULL, NULL, 'present', '', '2025-09-23 08:56:21', '2025-09-23 08:56:21'),
(82, 1, 4, '2025-09-24', 'morning', NULL, NULL, 'present', '', '2025-09-24 08:44:39', '2025-09-24 08:44:39'),
(83, 1, 4, '2025-09-25', 'morning', NULL, NULL, 'present', '', '2025-09-24 23:53:55', '2025-09-24 23:53:55'),
(84, 1, 4, '2025-09-26', 'morning', NULL, NULL, 'present', '', '2025-09-25 23:24:29', '2025-09-25 23:24:29'),
(85, 1, 4, '2025-09-27', 'morning', NULL, NULL, 'present', '', '2025-09-27 10:38:36', '2025-09-27 10:38:36'),
(86, 1, 4, '2025-09-28', 'morning', NULL, NULL, 'present', '', '2025-09-28 23:18:52', '2025-09-28 23:18:52'),
(87, 1, 4, '2025-09-29', 'morning', NULL, NULL, 'present', '', '2025-09-28 23:19:13', '2025-09-28 23:19:13'),
(88, 1, 5, '2025-12-21', 'morning', NULL, NULL, 'present', '', '2025-12-21 01:18:48', '2025-12-21 01:18:48'),
(89, 1, 5, '2025-12-20', 'morning', NULL, NULL, 'present', '', '2025-12-21 01:19:33', '2025-12-21 01:19:33'),
(90, 1, 4, '2025-12-21', 'morning', NULL, NULL, 'present', '', '2025-12-21 04:23:14', '2025-12-21 04:23:14'),
(91, 1, 5, '2025-12-22', 'morning', NULL, NULL, 'present', '', '2025-12-22 01:24:23', '2025-12-22 01:24:23'),
(92, 1, 4, '2025-12-22', 'morning', NULL, NULL, 'present', '', '2025-12-22 01:25:28', '2025-12-22 01:25:28'),
(93, 1, 4, '2026-03-08', 'morning', NULL, NULL, 'present', NULL, '2026-03-08 02:42:14', '2026-03-08 02:42:14'),
(94, 1, 5, '2026-03-08', 'morning', NULL, NULL, 'present', NULL, '2026-03-08 02:42:16', '2026-03-08 02:42:16');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_sessions`
--

CREATE TABLE `attendance_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `attendance_id` bigint UNSIGNED NOT NULL,
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
(71, 92, '09:00:00', '17:20:00', '2025-12-22 10:05:26', '2025-12-22 10:05:26'),
(72, 93, '07:42:00', '07:42:00', '2026-03-08 02:42:14', '2026-03-08 02:42:27'),
(73, 94, '07:42:00', '07:42:00', '2026-03-08 02:42:17', '2026-03-08 02:42:30');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `code`, `address`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Almufeed Saqafti Markaz', 'ASM', 'Main Branch', NULL, 1, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(2, 'Almufeed Shoes', 'QMSW3', 'House no 114 Q block Johar town Lahore', '03407853663', 1, '2026-03-08 03:26:13', '2026-03-08 23:09:17');

-- --------------------------------------------------------

--
-- Table structure for table `branch_product_stock`
--

CREATE TABLE `branch_product_stock` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `stock_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reorder_level` decimal(10,2) NOT NULL DEFAULT '10.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_product_stock`
--

INSERT INTO `branch_product_stock` (`id`, `branch_id`, `product_id`, `stock_quantity`, `reorder_level`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 42.00, 3.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(2, 1, 7, 283.00, 3.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(3, 1, 8, 6.00, 20.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(4, 1, 9, -9.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(5, 1, 10, 0.00, 2.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(6, 1, 11, 20.00, 20.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(7, 1, 12, 28.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(8, 1, 13, 0.00, 3.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(9, 1, 14, 78.00, 20.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(10, 1, 18, 43.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(11, 1, 19, 41.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(12, 1, 20, 64.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(13, 1, 21, 3.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(14, 1, 22, 0.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(15, 1, 23, 0.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(16, 1, 24, -45.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(17, 1, 25, 3.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(18, 1, 26, 4.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(19, 1, 27, 4.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(20, 1, 28, 1.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(21, 1, 29, 5.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(22, 1, 30, 3.95, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(23, 1, 31, 5.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(24, 1, 32, 4.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(25, 1, 33, 4.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(26, 1, 34, 4.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(27, 1, 35, 0.00, 2.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(28, 1, 36, 49.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(29, 1, 37, 477.00, 5.00, '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(30, 2, 1, 555.00, 3.00, '2026-03-08 03:26:13', '2026-03-08 22:09:50'),
(31, 2, 7, 5000.00, 3.00, '2026-03-08 03:26:13', '2026-03-08 22:09:41'),
(32, 2, 8, 5551.00, 20.00, '2026-03-08 03:26:13', '2026-03-08 22:10:12'),
(33, 2, 9, 555.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 22:09:59'),
(34, 2, 10, 0.00, 2.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(35, 2, 11, 0.00, 20.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(36, 2, 12, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(37, 2, 13, 0.00, 3.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(38, 2, 14, 0.00, 20.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(39, 2, 18, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(40, 2, 19, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(41, 2, 20, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(42, 2, 21, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(43, 2, 22, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(44, 2, 23, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(45, 2, 24, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(46, 2, 25, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(47, 2, 26, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(48, 2, 27, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(49, 2, 28, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(50, 2, 29, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(51, 2, 30, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(52, 2, 31, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(53, 2, 32, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(54, 2, 33, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(55, 2, 34, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(56, 2, 35, 0.00, 2.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(57, 2, 36, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(58, 2, 37, 0.00, 5.00, '2026-03-08 03:26:13', '2026-03-08 03:26:13'),
(59, 2, 38, 100.00, 10.00, '2026-03-08 23:02:59', '2026-03-08 23:02:59');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('almufeed_saqafti_markaz_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:27:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"view dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:15:\"manage products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:17:\"manage categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"process orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:16:\"manage employees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"manage reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:16:\"manage inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:17:\"manage attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"manage variants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:16:\"manage purchases\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:16:\"manage suppliers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:10:\"access pos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:12:\"manage roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:18:\"manage permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"assign roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:14:\"manage payroll\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:13:\"manage credit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:21:\"view credit dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:13:\"enable credit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:14:\"disable credit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:22:\"collect credit payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:21:\"view credit statement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:23:\"export credit statement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:19:\"view overdue report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:13:\"manage ledger\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:15:\"manage branches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:17:\"view all branches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}}s:5:\"roles\";a:5:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"manager\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"cashier\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:8:\"employee\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}}}', 1773096670);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `branch_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(4, 1, 'Quran Majeed', 'Quran Majeed', '2025-09-15 23:05:30', '2025-09-15 23:05:30'),
(5, 1, 'Hijab', 'Chadar, Abaya etc', '2025-09-17 07:42:51', '2025-09-17 07:42:51'),
(6, 1, 'Shohada Books', 'Books', '2025-09-21 05:48:21', '2025-09-21 05:48:21'),
(7, 1, 'Namaz o Manajat', 'Namaz Relaited Items', '2025-09-26 02:34:34', '2025-09-26 02:34:34'),
(8, 1, 'Saqafti Items', 'Islamic Cultural Items', '2025-10-01 01:31:02', '2025-10-01 01:31:02'),
(9, 2, 'Shoes', '', '2026-03-08 23:01:38', '2026-03-08 23:01:38');

-- --------------------------------------------------------

--
-- Table structure for table `credit_ledgers`
--

CREATE TABLE `credit_ledgers` (
  `id` bigint UNSIGNED NOT NULL,
  `ledger_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `total_debit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_credit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `opening_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `closing_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit_limit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','inactive','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_transaction_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `credit_transactions`
--

CREATE TABLE `credit_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit_ledger_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `payment_id` bigint UNSIGNED DEFAULT NULL,
  `transaction_type` enum('debit','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `balance_before` decimal(12,2) NOT NULL,
  `balance_after` decimal(12,2) NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `transaction_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_status` enum('pending','partial','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `items` json DEFAULT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `customer_type` enum('customer','reseller','wholesale') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `loyalty_points` decimal(10,2) NOT NULL DEFAULT '0.00',
  `barcode` varchar(355) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_enabled` tinyint(1) DEFAULT '0',
  `credit_limit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credit_due_days` int NOT NULL DEFAULT '30',
  `credit_start_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `branch_id`, `name`, `email`, `phone`, `address`, `customer_type`, `loyalty_points`, `barcode`, `credit_enabled`, `credit_limit`, `current_balance`, `credit_due_days`, `credit_start_date`, `created_at`, `updated_at`) VALUES
(3, 1, 'Walk-in Customer', NULL, NULL, NULL, 'customer', 0.00, NULL, 0, 0.00, 0.00, 30, NULL, '2025-09-14 12:13:09', '2025-09-14 12:13:09'),
(6, 1, 'Al Qaim Saqafti Markaz', '', '923260603214', 'Mian Channu', 'reseller', 0.00, 'CUST663827', 0, 0.00, 2036.00, 30, '2026-02-12', '2025-09-22 22:58:58', '2026-03-07 04:52:34'),
(7, 1, 'Kh. Arifa Kazmi', 'almufeed912@gmail.com', '03361514983', 'DinPur Srgodha', 'reseller', 0.00, 'CUST463349', 0, 0.00, -300.00, 30, NULL, '2025-10-01 06:33:30', '2026-03-08 01:36:35'),
(8, 1, 'Kh. Kainat Khan', 'Amt7212@gmail.com', '923205820103', 'Kohat Kpk', 'reseller', 0.00, 'CUST273740', 0, 0.00, 0.00, 30, NULL, '2025-10-02 03:31:33', '2026-02-08 15:02:35'),
(9, 1, 'Mol Hashim Irfani Sahb', 'Almufeed125@gmail.com', '03344466912', 'Bhutta Pur, Muzafargarh', 'customer', 0.00, 'CUST217906', 0, 0.00, 0.00, 30, NULL, '2025-12-13 12:06:27', '2026-02-08 15:02:33'),
(10, 1, 'Dr Aziz Fatima', '', '923325231957', 'Kallurkot', 'reseller', 0.00, '3333', 1, 100000.00, 0.00, 30, '2026-02-12', '2025-12-19 07:24:06', '2026-02-16 17:58:24');

-- --------------------------------------------------------

--
-- Table structure for table `ecommerce_sync_logs`
--

CREATE TABLE `ecommerce_sync_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `joining_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `branch_id`, `user_id`, `phone`, `address`, `salary`, `joining_date`, `created_at`, `updated_at`) VALUES
(4, 1, 6, '03457747789', 'Panjgirain', 5000.00, '2025-05-01', '2025-09-16 02:15:12', '2025-09-16 02:15:12'),
(5, 1, 7, '03417212469', 'Panjgirain', 12000.00, '2025-08-12', '2025-09-16 07:00:57', '2025-09-16 07:00:57');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_change` int NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED NOT NULL,
  `quantity` int DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `branch_id`, `purchase_id`, `product_id`, `action`, `quantity_change`, `notes`, `user_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(30, 1, NULL, 9, 'order_sale', -3, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(31, 1, NULL, 8, 'order_sale', -2, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(32, 1, NULL, 7, 'order_sale', -2, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(33, 1, NULL, 1, 'order_sale', -2, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(34, 1, NULL, 10, 'order_sale', -4, 'Stock reduced for Order #ORD-202509140002', 2, NULL, NULL, NULL, '2025-09-14 14:34:08', '2025-09-14 14:34:08'),
(35, 1, NULL, 1, 'order_sale', -5, 'Stock reduced for Order #ORD-202509140003', 2, NULL, NULL, NULL, '2025-09-14 14:42:34', '2025-09-14 14:42:34'),
(36, 1, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202509140004', 2, NULL, NULL, NULL, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(37, 1, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509140004', 2, NULL, NULL, NULL, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(38, 1, NULL, 9, 'order_sale', -1, 'Stock reduced for Order #ORD-202509140004', 2, NULL, NULL, NULL, '2025-09-14 14:48:26', '2025-09-14 14:48:26'),
(39, 1, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202509150001', 2, NULL, NULL, NULL, '2025-09-15 04:37:21', '2025-09-15 04:37:21'),
(40, 1, NULL, 7, 'order_sale', -3, 'Stock reduced for Order #ORD-202509150002', 2, NULL, NULL, NULL, '2025-09-15 06:32:26', '2025-09-15 06:32:26'),
(41, 1, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202509160001', 2, NULL, NULL, NULL, '2025-09-16 02:03:41', '2025-09-16 02:03:41'),
(42, 1, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509170001', 2, NULL, NULL, NULL, '2025-09-17 00:59:36', '2025-09-17 00:59:36'),
(43, 1, NULL, 11, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-17 07:48:38', '2025-09-17 07:48:38'),
(44, 1, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509180001', 2, NULL, NULL, NULL, '2025-09-18 03:38:13', '2025-09-18 03:38:13'),
(45, 1, NULL, 12, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-21 05:51:13', '2025-09-21 05:51:13'),
(46, 1, NULL, 13, 'initial', 16, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-21 06:14:54', '2025-09-21 06:14:54'),
(47, 1, NULL, 9, 'order_sale', -2, 'Stock reduced for Order #ORD-202509210001', 2, NULL, NULL, NULL, '2025-09-21 18:47:11', '2025-09-21 18:47:11'),
(48, 1, NULL, 1, 'order_sale', -3, 'Stock reduced for Order #ORD-202509210002', 2, NULL, NULL, NULL, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(49, 1, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210002', 2, NULL, NULL, NULL, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(50, 1, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210002', 2, NULL, NULL, NULL, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(51, 1, NULL, 9, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210002', 2, NULL, NULL, NULL, '2025-09-21 18:56:01', '2025-09-21 18:56:01'),
(52, 1, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210003', 2, NULL, NULL, NULL, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(53, 1, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210003', 2, NULL, NULL, NULL, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(54, 1, NULL, 9, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210003', 2, NULL, NULL, NULL, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(55, 1, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202509210003', 2, NULL, NULL, NULL, '2025-09-21 18:57:13', '2025-09-21 18:57:13'),
(56, 1, NULL, 14, 'initial', 99, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-21 23:11:45', '2025-09-21 23:11:45'),
(57, 1, NULL, 14, 'order_sale', -1, 'Stock reduced for Order #ORD-202509220001', 2, NULL, NULL, NULL, '2025-09-21 23:27:24', '2025-09-21 23:27:24'),
(58, 1, NULL, 12, 'order_sale', -3, 'Stock reduced for Order #ASM1', 2, NULL, NULL, NULL, '2025-09-22 07:37:25', '2025-09-22 07:37:25'),
(59, 1, NULL, 13, 'order_sale', -7, 'Stock reduced for Order #ASM2', 2, NULL, NULL, NULL, '2025-09-22 07:37:44', '2025-09-22 07:37:44'),
(60, 1, NULL, 12, 'order_sale', -1, 'Stock reduced for Order #ASM3', 2, NULL, NULL, NULL, '2025-09-22 07:38:15', '2025-09-22 07:38:15'),
(61, 1, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ASM4', 2, NULL, NULL, NULL, '2025-09-22 07:48:48', '2025-09-22 07:48:48'),
(65, 1, NULL, 18, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-26 23:10:41', '2025-09-26 23:10:41'),
(66, 1, NULL, 19, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-26 23:14:03', '2025-09-26 23:14:03'),
(67, 1, NULL, 20, 'initial', 75, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-09-26 23:16:59', '2025-09-26 23:16:59'),
(68, 1, NULL, 20, 'order_sale', -3, 'Stock reduced for Order #ORD-202509280001', 2, NULL, NULL, NULL, '2025-09-28 17:10:02', '2025-09-28 17:10:02'),
(69, 1, NULL, 20, 'order_sale', -4, 'Stock reduced for Order #ORD-202509280001', 2, NULL, NULL, NULL, '2025-09-28 17:19:44', '2025-09-28 17:19:44'),
(70, 1, NULL, 18, 'order_sale', -1, 'Stock reduced for Order #ORD-202509280001', 2, NULL, NULL, NULL, '2025-09-28 17:19:44', '2025-09-28 17:19:44'),
(71, 1, NULL, 18, 'order_sale', -1, 'Stock reduced for Order #ORD-202509290001', 2, NULL, NULL, NULL, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(72, 1, NULL, 20, 'order_sale', -1, 'Stock reduced for Order #ORD-202509290001', 2, NULL, NULL, NULL, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(73, 1, NULL, 19, 'order_sale', -1, 'Stock reduced for Order #ORD-202509290001', 2, NULL, NULL, NULL, '2025-09-29 01:23:53', '2025-09-29 01:23:53'),
(74, 1, NULL, 21, 'initial', 20, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-10-01 01:35:35', '2025-10-01 01:35:35'),
(75, 1, NULL, 21, 'order_sale', -1, 'Stock reduced for Order #ORD-202510010001', 2, NULL, NULL, NULL, '2025-10-01 06:35:06', '2025-10-01 06:35:06'),
(76, 1, NULL, 22, 'initial', 12, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-10-02 03:39:32', '2025-10-02 03:39:32'),
(77, 1, NULL, 22, 'order_sale', -2, 'Stock reduced for Order #ORD-202510020001', 2, NULL, NULL, NULL, '2025-10-02 03:41:43', '2025-10-02 03:41:43'),
(78, 1, NULL, 11, 'order_sale', -6, 'Stock reduced for Order #ORD-202510020001', 2, NULL, NULL, NULL, '2025-10-02 03:41:43', '2025-10-02 03:41:43'),
(85, 1, NULL, 18, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(86, 1, NULL, 22, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(87, 1, NULL, 21, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(88, 1, NULL, 20, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(89, 1, NULL, 19, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(90, 1, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202510030001', 2, NULL, NULL, NULL, '2025-10-03 16:58:31', '2025-10-03 16:58:31'),
(91, 1, NULL, 7, 'order_sale', -1, 'Stock reduced for Order #ORD-202511270001', 2, NULL, NULL, NULL, '2025-11-27 12:43:35', '2025-11-27 12:43:35'),
(92, 1, NULL, 23, 'initial', 10, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-13 11:45:20', '2025-12-13 11:45:20'),
(93, 1, NULL, 23, 'order_sale', -2, 'Stock reduced for Order #ORD-202512130001', 2, NULL, NULL, NULL, '2025-12-13 12:22:24', '2025-12-13 12:22:24'),
(94, 1, NULL, 24, 'initial', 60, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:23:14', '2025-12-20 02:23:14'),
(95, 1, NULL, 25, 'initial', 5, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:26:29', '2025-12-20 02:26:29'),
(96, 1, NULL, 26, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:29:10', '2025-12-20 02:29:10'),
(97, 1, NULL, 27, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:31:27', '2025-12-20 02:31:27'),
(98, 1, NULL, 28, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:32:44', '2025-12-20 02:32:44'),
(99, 1, NULL, 29, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:34:59', '2025-12-20 02:34:59'),
(100, 1, NULL, 30, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:36:14', '2025-12-20 02:36:14'),
(101, 1, NULL, 31, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:37:05', '2025-12-20 02:37:05'),
(102, 1, NULL, 32, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:37:56', '2025-12-20 02:37:56'),
(103, 1, NULL, 33, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:38:47', '2025-12-20 02:38:47'),
(104, 1, NULL, 34, 'initial', 0, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-20 02:41:17', '2025-12-20 02:41:17'),
(105, 1, NULL, 24, 'order_sale', -40, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(106, 1, NULL, 25, 'order_sale', -1, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(107, 1, NULL, 26, 'order_sale', -1, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(108, 1, NULL, 27, 'order_sale', -1, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(109, 1, NULL, 28, 'order_sale', -1, 'Stock reduced for Order #ORD-202512200001', 2, NULL, NULL, NULL, '2025-12-20 02:47:50', '2025-12-20 02:47:50'),
(110, 1, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202512210001', 7, NULL, NULL, NULL, '2025-12-21 01:24:31', '2025-12-21 01:24:31'),
(111, 1, NULL, 35, 'initial', 0, 'Initial stock entry', 7, NULL, NULL, NULL, '2025-12-21 01:47:00', '2025-12-21 01:47:00'),
(112, 1, NULL, 35, 'order_sale', -1, 'Stock reduced for Order #ORD-202512210002', 7, NULL, NULL, NULL, '2025-12-21 01:49:09', '2025-12-21 01:49:09'),
(113, 1, NULL, 10, 'order_sale', -1, 'Stock reduced for Order #ORD-202512220001', 2, NULL, NULL, NULL, '2025-12-22 16:45:14', '2025-12-22 16:45:14'),
(114, 1, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202512220002', 2, NULL, NULL, NULL, '2025-12-22 16:49:53', '2025-12-22 16:49:53'),
(115, 1, NULL, 21, 'order_sale', -1, 'Stock reduced for Order #ORD-202512220003', 2, NULL, NULL, NULL, '2025-12-22 17:01:57', '2025-12-22 17:01:57'),
(116, 1, NULL, 8, 'order_sale', -1, 'Stock reduced for Order #ORD-202512220003', 2, NULL, NULL, NULL, '2025-12-22 17:01:57', '2025-12-22 17:01:57'),
(117, 1, NULL, 28, 'order_sale', -1, 'Stock reduced for Order #ORD-202512220004', 2, NULL, NULL, NULL, '2025-12-22 18:14:49', '2025-12-22 18:14:49'),
(118, 1, NULL, 33, 'order_sale', -1, 'Stock reduced for Order #ORD-202512220004', 2, NULL, NULL, NULL, '2025-12-22 18:14:49', '2025-12-22 18:14:49'),
(119, 1, NULL, 32, 'order_sale', -1, 'Stock reduced for Order #ORD-202512220004', 2, NULL, NULL, NULL, '2025-12-22 18:14:49', '2025-12-22 18:14:49'),
(120, 1, NULL, 28, 'order_sale', -2, 'Stock reduced for Order #ORD-202512220005', 2, NULL, NULL, NULL, '2025-12-22 18:15:08', '2025-12-22 18:15:08'),
(121, 1, NULL, 14, 'order_sale', -2, 'Stock reduced for Order #ORD-202512220006', 2, NULL, NULL, NULL, '2025-12-22 18:20:23', '2025-12-22 18:20:23'),
(122, 1, NULL, 36, 'initial', 50, 'Initial stock entry', 2, NULL, NULL, NULL, '2025-12-28 15:10:26', '2025-12-28 15:10:26'),
(123, 1, NULL, 25, 'order_sale', -1, 'Stock reduced for Order #ORD-202512280001', 2, NULL, NULL, NULL, '2025-12-28 15:37:17', '2025-12-28 15:37:17'),
(124, 1, NULL, 1, 'order_sale', -1, 'Stock reduced for Order #ORD-202601170001', 2, NULL, NULL, NULL, '2026-01-17 15:32:34', '2026-01-17 15:32:34'),
(125, 1, NULL, 13, 'order_sale', -1, 'Stock reduced for Order #ORD-202602050001', 2, NULL, NULL, NULL, '2026-02-04 20:32:21', '2026-02-04 20:32:21'),
(126, 1, NULL, 22, 'order_sale', -10, 'Stock reduced for Order #ORD-202602050002', 2, NULL, NULL, NULL, '2026-02-04 20:36:41', '2026-02-04 20:36:41'),
(127, 1, NULL, 20, 'order_sale', -2, 'Stock reduced for Order #ORD-202602050003', 2, NULL, NULL, NULL, '2026-02-04 20:37:26', '2026-02-04 20:37:26'),
(128, 1, NULL, 37, 'initial', 500, 'Initial stock entry', 2, NULL, NULL, NULL, '2026-02-08 14:00:39', '2026-02-08 14:00:39'),
(129, 1, NULL, 8, 'order_sale', -2, 'Stock reduced for Order #ORD-202602080001', 2, NULL, NULL, NULL, '2026-02-08 18:11:05', '2026-02-08 18:11:05'),
(130, 1, NULL, 13, 'order_sale', -2, 'Stock reduced for Order #ORD-202602080002', 2, NULL, NULL, NULL, '2026-02-08 18:16:29', '2026-02-08 18:16:29'),
(131, 1, NULL, 13, 'order_sale', -2, 'Stock reduced for Order #ORD-202602090001', 2, NULL, NULL, NULL, '2026-02-08 19:07:53', '2026-02-08 19:07:53'),
(132, 1, NULL, 13, 'order_sale', -4, 'Stock reduced for Order #ORD-202602090002', 2, NULL, NULL, NULL, '2026-02-08 19:15:47', '2026-02-08 19:15:47'),
(133, 1, NULL, 12, 'order_sale', -1, 'Stock reduced for Order #ORD-202602090002', 2, NULL, NULL, NULL, '2026-02-08 19:15:47', '2026-02-08 19:15:47'),
(134, 1, NULL, 12, 'order_sale', -1, 'Stock reduced for Order #ORD-202602090003', 2, NULL, NULL, NULL, '2026-02-08 19:16:15', '2026-02-08 19:16:15'),
(135, 1, NULL, 12, 'order_sale', -1, 'Stock reduced for Order #ORD-202602090004', 2, NULL, NULL, NULL, '2026-02-08 19:22:55', '2026-02-08 19:22:55'),
(136, 1, NULL, 12, 'order_sale', -1, 'Stock reduced for Order #ORD-202602090005', 2, NULL, NULL, NULL, '2026-02-08 19:36:20', '2026-02-08 19:36:20'),
(137, 1, NULL, 1, 'order_sale', -1, 'Stock reduced for Order #ORD-202602090006', 2, NULL, NULL, NULL, '2026-02-08 19:40:12', '2026-02-08 19:40:12'),
(138, 1, NULL, 1, 'order_sale', -1, 'Stock reduced for Order #ORD-202602090007', 2, NULL, NULL, NULL, '2026-02-08 19:45:28', '2026-02-08 19:45:28'),
(139, 1, NULL, 11, 'order_sale', -1, 'Stock reduced for Order #ORD-202602090008', 2, NULL, NULL, NULL, '2026-02-08 19:45:49', '2026-02-08 19:45:49'),
(140, 1, NULL, 12, 'order_sale', -1, 'Stock reduced for Order #ORD-202602090009', 2, NULL, NULL, NULL, '2026-02-09 16:41:26', '2026-02-09 16:41:26'),
(141, 1, NULL, 12, 'order_sale', -10, 'Stock reduced for Order #ORD-202602120001', 2, NULL, NULL, NULL, '2026-02-11 19:49:34', '2026-02-11 19:49:34'),
(142, 1, NULL, 11, 'order_sale', -11, 'Stock reduced for Order #ORD-202602120002', 2, NULL, NULL, NULL, '2026-02-11 19:55:10', '2026-02-11 19:55:10'),
(143, 1, NULL, 11, 'order_sale', -7, 'Stock reduced for Order #ORD-202602120003', 2, NULL, NULL, NULL, '2026-02-11 19:58:38', '2026-02-11 19:58:38'),
(144, 1, NULL, 11, 'order_sale', -2, 'Stock reduced for Order #ORD-202602120004', 2, NULL, NULL, NULL, '2026-02-11 20:02:58', '2026-02-11 20:02:58'),
(145, 1, NULL, 12, 'order_sale', -1, 'Stock reduced for Order #ORD-202602150001', 2, NULL, NULL, NULL, '2026-02-15 14:36:41', '2026-02-15 14:36:41'),
(146, 1, NULL, 21, 'order_sale', -8, 'Stock reduced for Order #ORD-202602150002', 2, NULL, NULL, NULL, '2026-02-15 14:37:24', '2026-02-15 14:37:24'),
(147, 1, NULL, 1, 'order_sale', -3, 'Stock reduced for Order #ORD-202602160001', 2, NULL, NULL, NULL, '2026-02-16 16:28:10', '2026-02-16 16:28:10'),
(148, 1, NULL, 1, 'add', 50, 'added 50 new items', 2, NULL, NULL, NULL, '2026-03-02 05:06:10', '2026-03-02 05:06:10'),
(149, 1, NULL, 7, 'add', 500, 'quantity updated', 2, NULL, NULL, NULL, '2026-03-02 05:08:16', '2026-03-02 05:08:16'),
(150, 1, NULL, 14, 'refund_return', 3, 'Restocked due to refund of Order #ORD-202603020007', 2, NULL, NULL, NULL, '2026-03-06 18:48:23', '2026-03-06 18:48:23'),
(151, 2, NULL, 7, 'add', 5000, '5', 2, NULL, NULL, NULL, '2026-03-08 22:09:41', '2026-03-08 22:09:41'),
(152, 2, NULL, 1, 'add', 555, '55', 2, NULL, NULL, NULL, '2026-03-08 22:09:50', '2026-03-08 22:09:50'),
(153, 2, NULL, 8, 'add', 5555, '555', 2, NULL, NULL, NULL, '2026-03-08 22:09:55', '2026-03-08 22:09:55'),
(154, 2, NULL, 9, 'add', 555, '555', 2, NULL, NULL, NULL, '2026-03-08 22:09:59', '2026-03-08 22:09:59'),
(155, 2, NULL, 38, 'initial', 100, 'Initial stock entry', 2, NULL, NULL, NULL, '2026-03-08 23:02:59', '2026-03-08 23:02:59');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_accounts`
--

CREATE TABLE `ledger_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `account_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_account_entries`
--

CREATE TABLE `ledger_account_entries` (
  `id` bigint UNSIGNED NOT NULL,
  `entry_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ledger_account_id` bigint UNSIGNED NOT NULL,
  `entry_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_entries`
--

CREATE TABLE `ledger_entries` (
  `id` bigint UNSIGNED NOT NULL,
  `entry_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entry_date` date NOT NULL,
  `account_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` bigint UNSIGNED DEFAULT NULL,
  `party_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ledger_entries`
--

INSERT INTO `ledger_entries` (`id`, `entry_number`, `entry_date`, `account_type`, `transaction_type`, `reference_type`, `reference_id`, `reference_number`, `description`, `debit`, `credit`, `payment_method`, `party_type`, `party_id`, `party_name`, `user_id`, `notes`, `created_at`, `updated_at`) VALUES
(90, 'LED-202603-0001', '2026-03-03', 'sales', 'sale', 'App\\Models\\Order', 110, 'ORD-202603030001', 'Sale - Order #ORD-202603030001', 0.00, 5100.00, 'cash', 'customer', 6, 'Al Qaim Saqafti Markaz', 2, NULL, '2026-03-02 20:58:24', '2026-03-02 20:58:24'),
(91, 'LED-202603-0002', '2026-03-06', 'sales', 'sale', 'App\\Models\\Order', 111, 'ORD-202603060001', 'Sale - Order #ORD-202603060001', 0.00, 200.00, 'cash', 'customer', 6, 'Al Qaim Saqafti Markaz', 2, NULL, '2026-03-05 19:27:09', '2026-03-05 19:27:09'),
(92, 'LED-202603-0003', '2026-03-06', 'sales', 'sale', 'App\\Models\\Order', 112, 'ORD-202603060002', 'Sale - Order #ORD-202603060002', 0.00, 6222.00, 'cash', 'customer', NULL, 'Walk-in Customer', 2, NULL, '2026-03-06 17:57:26', '2026-03-06 17:57:26'),
(93, 'LED-202603-0004', '2026-03-06', 'sales', 'sale', 'App\\Models\\Order', 113, 'ORD-202603060003', 'Sale - Order #ORD-202603060003', 0.00, 900.00, 'cash', 'customer', 6, 'Al Qaim Saqafti Markaz', 2, NULL, '2026-03-06 18:32:57', '2026-03-06 18:32:57'),
(94, 'LED-202603-0005', '2026-03-06', 'refunds', 'refund', 'App\\Models\\Refund', 1, 'ORD-202603020007', 'Refund for Order #ORD-202603020007', 2700.00, 0.00, 'cash', 'customer', 7, 'Kh. Arifa Kazmi', 2, NULL, '2026-03-06 18:48:23', '2026-03-06 18:48:23'),
(95, 'LED-202603-0006', '2026-03-07', 'sales', 'sale', 'App\\Models\\Order', 114, 'ORD-202603070001', 'Sale - Order #ORD-202603070001', 0.00, 6836.00, 'cash', 'customer', 6, 'Al Qaim Saqafti Markaz', 2, NULL, '2026-03-06 19:31:57', '2026-03-06 19:31:57'),
(96, 'LED-202603-0007', '2026-03-07', 'sales', 'sale', 'App\\Models\\Order', 115, 'ORD-202603070002', 'Sale - Order #ORD-202603070002', 0.00, 1250.00, 'cash', 'customer', 6, 'Al Qaim Saqafti Markaz', 2, NULL, '2026-03-07 02:46:08', '2026-03-07 02:46:08'),
(97, 'LED-202603-0008', '2026-03-07', 'sales', 'sale', 'App\\Models\\Order', 116, 'ORD-202603070003', 'Sale - Order #ORD-202603070003', 0.00, 1220.00, 'cash', 'customer', 6, 'Al Qaim Saqafti Markaz', 2, NULL, '2026-03-07 04:41:50', '2026-03-07 04:41:50'),
(98, 'LED-202603-0009', '2026-03-07', 'sales', 'sale', 'App\\Models\\Order', 117, 'ASM1272', 'Sale - Order #ASM1272', 0.00, 1632.00, 'cash', 'customer', 6, 'Al Qaim Saqafti Markaz', 2, NULL, '2026-03-07 04:52:34', '2026-03-07 04:52:34'),
(99, 'LED-202603-0010', '2026-03-07', 'sales', 'sale', 'App\\Models\\Order', 118, 'ASM1273', 'Sale - Order #ASM1273', 0.00, 250.00, 'cash', 'customer', 7, 'Kh. Arifa Kazmi', 2, NULL, '2026-03-07 12:56:58', '2026-03-07 12:56:58'),
(100, 'LED-202603-0011', '2026-03-08', 'sales', 'sale', 'App\\Models\\Order', 119, 'ASM1274', 'Sale - Order #ASM1274', 0.00, 123.00, 'cash', 'customer', NULL, 'Walk-in Customer', 2, NULL, '2026-03-08 03:28:47', '2026-03-08 03:28:47'),
(101, 'LED-202603-0012', '2026-03-09', 'sales', 'sale', 'App\\Models\\Order', 120, 'ASM1275', 'Sale - Order #ASM1275', 0.00, 400.00, 'cash', 'customer', NULL, 'Walk-in Customer', 2, NULL, '2026-03-08 22:10:12', '2026-03-08 22:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `login_histories`
--

CREATE TABLE `login_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `logout_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(34, '2025_09_14_183502_create_branches_table', 15),
(35, '2026_02_08_180627_create_units_table', 16),
(36, '2026_02_11_235547_create_credit_ledgers_table', 17),
(37, '2026_02_11_235612_create_credit_transactions_table', 18),
(38, '2026_02_12_001127_add_credit_permissions', 19),
(39, '2026_03_01_223544_create_ledger_entries_table', 20),
(40, '2026_03_01_234508_add_balance_columns_to_orders_table', 21),
(41, '2026_03_02_090458_add_payment_type_and_make_order_nullable_to_payments_table', 22),
(42, '2026_03_02_093300_create_ledger_accounts_table', 23),
(43, '2026_03_06_001436_change_stock_quantity_to_decimal_in_products_table', 24),
(44, '2026_03_08_200000_add_multi_branch_support', 25),
(45, '2026_03_08_233200_add_branch_id_to_purchases_and_inventory_logs', 26),
(46, '2026_03_09_033356_add_branch_id_to_customers_suppliers_products', 27),
(47, '2026_03_09_035707_add_branch_id_to_categories_table', 28);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(5, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 7);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_type` varchar(355) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `order_type` enum('pos','online') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pos',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivery_charges` decimal(10,2) DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `previous_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status` enum('pending','completed','cancelled','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '10.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dispatch_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_token` varchar(2555) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` enum('cash','credit','cod','jazzcash','easypaisa','bank','card') COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit_status` enum('none','pending','partial','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit_ledger_id` int UNSIGNED DEFAULT NULL,
  `credit_due_date` date DEFAULT NULL,
  `credit_paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit_remaining_amount` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `branch_id`, `order_number`, `customer_id`, `customer_type`, `user_id`, `order_type`, `subtotal`, `tax`, `discount`, `delivery_charges`, `weight`, `total`, `paid_amount`, `previous_balance`, `balance_amount`, `payment_method`, `payment_status`, `status`, `notes`, `tax_rate`, `created_at`, `updated_at`, `dispatch_method`, `receipt_token`, `tracking_id`, `payment_type`, `credit_status`, `credit_ledger_id`, `credit_due_date`, `credit_paid_amount`, `credit_remaining_amount`) VALUES
(8, 1, 'ORD-202506280001', NULL, NULL, NULL, 'pos', 10000.00, 1000.00, 0.00, NULL, NULL, 11000.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-06-28 07:58:39', '2025-06-28 07:58:39', NULL, 'bd628c22df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(9, 1, 'ORD-202506280002', NULL, NULL, NULL, 'pos', 20000.00, 2000.00, 0.00, NULL, NULL, 22000.00, 0.00, 0.00, 0.00, 'card', 'pending', 'completed', '', 10.00, '2025-06-28 07:59:52', '2025-06-28 07:59:52', NULL, 'bd6298c0df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(12, 1, 'ORD-202506280003', NULL, NULL, NULL, 'pos', 4000.00, 400.00, 0.00, NULL, NULL, 4400.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-06-28 08:04:37', '2025-06-28 08:04:37', NULL, 'bd629b0edf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(14, 1, 'ORD-202508140001', NULL, NULL, NULL, 'pos', 5200.00, 520.00, 0.00, NULL, NULL, 5720.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-08-14 13:48:24', '2025-08-14 13:48:24', NULL, 'bd629c8adf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(15, 1, 'ORD-202508140002', NULL, NULL, NULL, 'pos', 1200.00, 120.00, 0.00, NULL, NULL, 1320.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-08-14 15:01:28', '2025-08-14 15:01:28', NULL, 'bd629de8df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(16, 1, 'ORD-202508140003', NULL, NULL, NULL, 'pos', 6000.00, 600.00, 0.00, NULL, NULL, 6600.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-08-14 15:32:05', '2025-08-14 15:32:05', NULL, 'bd629f5adf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(17, 1, 'ORD-202508140004', NULL, NULL, NULL, 'pos', 6000.00, 600.00, 0.00, NULL, NULL, 6600.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-08-14 15:45:16', '2025-08-14 15:45:16', NULL, 'bd62a11cdf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(18, 1, 'ORD-202508140005', NULL, NULL, NULL, 'pos', 7200.00, 720.00, 0.00, NULL, NULL, 7920.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-08-14 16:04:41', '2025-08-14 16:04:41', NULL, 'bd62a27adf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(19, 1, 'ORD-202508150001', NULL, NULL, NULL, 'pos', 2000.00, 200.00, 0.00, NULL, NULL, 2200.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-08-15 18:18:43', '2025-08-15 18:18:43', NULL, 'bd62a3cedf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(20, 1, 'ORD-202509140001', 3, NULL, NULL, 'pos', 4750.00, 475.00, 0.00, NULL, NULL, 5225.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-14 13:23:33', '2025-09-14 13:23:33', NULL, 'bd62a522df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(21, 1, 'ORD-202509140002', NULL, NULL, 2, 'pos', 13550.00, 1355.00, 0.00, NULL, NULL, 14905.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-14 14:34:08', '2025-09-14 14:34:08', NULL, 'bd62a694df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(22, 1, 'ORD-202509140003', NULL, NULL, 2, 'pos', 7500.00, 750.00, 0.00, NULL, NULL, 8250.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-14 14:42:34', '2025-09-14 14:42:34', NULL, 'bd62a7fcdf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(23, 1, 'ORD-202509140004', 3, NULL, 2, 'pos', 4250.00, 425.00, 0.00, NULL, NULL, 4675.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-14 14:48:26', '2025-09-14 14:48:26', NULL, 'bd62a9dcdf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(24, 1, 'ORD-202509150001', NULL, NULL, 2, 'pos', 450.00, 45.00, 0.00, NULL, NULL, 495.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-15 04:37:21', '2025-09-15 04:37:21', NULL, 'bd62ab4edf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(25, 1, 'ORD-202509150002', 3, NULL, 2, 'pos', 7500.00, 750.00, 0.00, NULL, NULL, 8250.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-15 06:32:26', '2025-09-15 06:32:26', NULL, 'bd62aca2df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(26, 1, 'ORD-202509160001', NULL, NULL, 2, 'pos', 450.00, 45.00, 0.00, NULL, NULL, 495.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-16 02:03:41', '2025-09-16 02:03:41', NULL, 'bd62adf6df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(27, 1, 'ORD-202509170001', NULL, NULL, 2, 'pos', 100.00, 10.00, 0.00, NULL, NULL, 110.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-17 00:59:36', '2025-09-17 00:59:36', NULL, 'bd62af54df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(28, 1, 'ORD-202509180001', NULL, NULL, 2, 'pos', 100.00, 10.00, 0.00, NULL, NULL, 110.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 10.00, '2025-09-18 03:38:13', '2025-09-18 03:38:13', NULL, 'bd62b10cdf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(29, 1, 'ORD-202509210001', NULL, NULL, 2, 'pos', 800.00, 120.00, 0.00, NULL, NULL, 920.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 15.00, '2025-09-21 18:47:11', '2025-09-21 18:47:11', 'TCS', 'bd62b274df8711f0bd5a98a6786592ad', '2345342', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(30, 1, 'ORD-202509210002', NULL, NULL, 2, 'pos', 6799.00, 135.98, 0.00, NULL, NULL, 6934.98, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 2.00, '2025-09-21 18:56:01', '2025-09-21 18:56:01', 'Self Pickup', 'bd62b3d2df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(31, 1, 'ORD-202509210003', NULL, NULL, 2, 'pos', 3019.00, 60.38, 0.00, NULL, NULL, 3079.38, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 2.00, '2025-09-21 18:57:13', '2025-09-21 18:57:13', 'Self Pickup', 'bd62b56cdf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(32, 1, 'ORD-202509220001', NULL, NULL, 2, 'pos', 900.00, 56.25, 0.00, NULL, NULL, 956.25, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 6.25, '2025-09-21 23:27:24', '2025-09-21 23:27:24', 'Pak Post', 'bd62b6b6df8711f0bd5a98a6786592ad', 'RGL161038382', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(35, 1, 'ASM1', NULL, NULL, 2, 'pos', 750.00, 15.00, 0.00, NULL, NULL, 765.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 2.00, '2025-09-22 07:37:25', '2025-09-22 07:37:25', 'Pak Post', 'bd62b8a0df8711f0bd5a98a6786592ad', 'rte5345', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(36, 1, 'ASM2', NULL, NULL, 2, 'pos', 10493.00, 209.86, 0.00, NULL, NULL, 10702.86, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 2.00, '2025-09-22 07:37:44', '2025-09-22 07:37:44', 'Self Pickup', 'bd62ba30df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(37, 1, 'ASM3', NULL, NULL, 2, 'pos', 250.00, 5.00, 0.00, NULL, NULL, 255.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 2.00, '2025-09-22 07:38:15', '2025-09-22 07:38:15', 'Self Pickup', 'bd62bb84df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(38, 1, 'ASM4', NULL, NULL, 2, 'pos', 100.00, 2.00, 0.00, NULL, NULL, 102.00, 0.00, 0.00, 0.00, 'card', 'pending', 'completed', '', 2.00, '2025-09-22 07:48:48', '2025-09-22 07:48:48', 'Self Pickup', 'bd62bcd8df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(42, 1, 'ORD-202509290001', 6, NULL, 2, 'pos', 2250.00, 0.00, 50.00, 100.00, 0.50, 2300.00, 0.00, 0.00, 0.00, 'easypaisa', 'pending', 'completed', '', 0.00, '2025-09-29 01:23:53', '2025-09-29 01:23:53', 'Pak Post', 'bd62be36df8711f0bd5a98a6786592ad', '1213112', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(43, 1, 'ORD-202510010001', 7, NULL, 2, 'pos', 600.00, 0.00, 0.00, 150.00, 170.00, 750.00, 0.00, 0.00, 0.00, 'jazzcash', 'pending', 'completed', '', 0.00, '2025-10-01 06:35:06', '2025-10-01 06:35:06', 'Pak Post', 'bd62bfb2df8711f0bd5a98a6786592ad', 'RGL162035563', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(44, 1, 'ORD-202510020001', 8, NULL, 2, 'pos', 5300.00, 0.00, 0.00, 600.00, 2.00, 5900.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 0.00, '2025-10-02 03:41:43', '2025-10-02 03:41:43', 'TCS', 'bd62c11adf8711f0bd5a98a6786592ad', '774891300208', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(49, 1, 'ORD-202511270001', NULL, NULL, 2, 'pos', 1799.00, 35.98, 0.00, 0.00, 0.00, 1834.98, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 2.00, '2025-11-27 12:43:35', '2025-11-27 12:43:35', 'Self Pickup', 'bd62c278df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(50, 1, 'ORD-202512130001', NULL, NULL, 2, 'pos', 5998.00, 0.00, 1198.00, 200.00, 2.00, 5000.00, 0.00, 0.00, 0.00, 'bank', 'pending', 'completed', '', 0.00, '2025-12-13 12:22:24', '2025-12-13 12:22:24', 'TCS', 'bd62c3e0df8711f0bd5a98a6786592ad', '774891300325', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(51, 1, 'ORD-202512200001', 10, NULL, 2, 'pos', 90415.00, 0.00, 4000.00, 0.00, 96.24, 86415.00, 0.00, 0.00, 0.00, 'easypaisa', 'pending', 'completed', '', 0.00, '2025-12-20 02:47:50', '2025-12-20 02:47:50', 'Self Pickup', 'bd62c548df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(52, 1, 'ORD-202512210001', 3, NULL, 7, 'pos', 1299.00, 0.00, 99.00, 0.00, 0.00, 1200.00, 0.00, 0.00, 0.00, 'jazzcash', 'pending', 'completed', '', 0.00, '2025-12-21 01:24:31', '2025-12-21 01:24:31', 'Self Pickup', 'bd62c6badf8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(53, 1, 'ORD-202512210002', NULL, NULL, 7, 'pos', 800.00, 200.00, 0.00, 0.00, 1.57, 1000.00, 0.00, 0.00, 0.00, 'jazzcash', 'pending', 'completed', '', 25.00, '2025-12-21 01:49:09', '2025-12-21 01:49:09', 'Self Pickup', 'bd62c822df8711f0bd5a98a6786592ad', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(54, 1, 'ORD-202512220001', NULL, NULL, 2, 'pos', 1299.00, 0.00, 0.00, 0.00, 0.00, 1299.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 0.00, '2025-12-22 16:45:14', '2025-12-22 16:45:14', 'Self Pickup', '2dz5qqFTLjC9FwpbJYOEQFJoycs9m2p4', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(55, 1, 'ORD-202512220002', NULL, NULL, 2, 'pos', 100.00, 0.00, 0.00, 0.00, 0.00, 100.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 0.00, '2025-12-22 16:49:53', '2025-12-22 16:49:53', 'Self Pickup', 'Yh1MxBFfEnJp0HFTWfsbdZVVqlUSC14c', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(58, 1, 'ORD-202512220003', 10, NULL, 2, 'pos', 670.00, 0.00, 100.00, 100.00, 0.16, 670.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 0.00, '2025-12-22 17:01:57', '2025-12-22 17:01:57', 'TCS', 'kVoIhWsehkdPzsLDgnEKBi6BYoljCjnI', '23453452522', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(59, 1, 'ORD-202512220004', NULL, NULL, 2, 'pos', 480.00, 0.00, 0.00, 0.00, 0.21, 480.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 0.00, '2025-12-22 18:14:49', '2025-12-22 18:14:49', 'Self Pickup', 'kfn9gFu4Fsc1sx6kvdBN3l8CwMOeF37O', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(60, 1, 'ORD-202512220005', NULL, NULL, 2, 'pos', 300.00, 0.00, 0.00, 0.00, 0.10, 300.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 0.00, '2025-12-22 18:15:08', '2025-12-22 18:15:08', 'Self Pickup', 'Hb83Qw3iAXeZpBWlGsjsPKHq26tpjNqY', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(61, 1, 'ORD-202512220006', NULL, NULL, 2, 'pos', 2400.00, 0.00, 0.00, 0.00, 0.00, 2400.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 0.00, '2025-12-22 18:20:23', '2025-12-22 18:20:23', 'Self Pickup', 'iL2zeVEbvequVoHi4q6PdGg7W3WXw6ur', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(62, 1, 'ORD-202512280001', NULL, NULL, 2, 'pos', 80.00, 0.00, 19.00, 0.00, 0.04, 61.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', '', 0.00, '2025-12-28 15:37:17', '2025-12-28 15:37:17', 'Self Pickup', 'Io8IzCvOKk4uIf7z4h6m8tr4444QgpvH', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(63, 1, 'ORD-202601170001', NULL, NULL, 2, 'pos', 1500.00, 0.00, 0.00, 0.00, 1.00, 1500.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', '', 0.00, '2026-01-17 15:32:34', '2026-01-17 15:32:34', 'Self Pickup', '115GVrC18lNizWyU1hEx2wuK9sR5EonY', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(64, 1, 'ORD-202602050001', NULL, NULL, 2, 'pos', 1499.00, 0.00, 0.00, 0.00, 0.00, 1499.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-04 20:32:21', '2026-02-04 20:32:21', 'Self Pickup', '6E8lnLKIyIl9naSoSsnPRJRWVHSRXw4G', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(65, 1, 'ORD-202602050002', NULL, NULL, 2, 'pos', 1200.00, 12.00, 20.00, 0.00, 0.80, 1192.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 1.00, '2026-02-04 20:36:41', '2026-02-04 20:36:41', 'Self Pickup', 'a59ePbvNueZMdulAdmis3HYPBpKAS7Z1', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(66, 1, 'ORD-202602050003', NULL, NULL, 2, 'pos', 2300.00, 0.00, 10.00, 0.00, 3.00, 2290.00, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', NULL, 0.00, '2026-02-04 20:37:26', '2026-02-04 20:37:26', 'By Bus', '41UYLZXszx9rpPqJSrSjWFoWMzYVng18', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(67, 1, 'ORD-202602080001', NULL, NULL, 2, 'pos', 200.00, 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 18:11:05', '2026-02-08 18:11:05', 'Self Pickup', '2Bt9y4Hru8BgFvkC8qz6DPpb1AJebOEY', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(69, 1, 'ORD-202602080002', NULL, NULL, 2, 'pos', 2998.00, 0.00, 0.00, 0.00, 0.00, 2998.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 18:16:29', '2026-02-08 18:16:29', 'Self Pickup', 'HrKoxAX5LkMErwYXK4QgtkUX0V8CCdvl', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(70, 1, 'ORD-202602090001', NULL, NULL, 2, 'pos', 2998.00, 29.98, 0.00, 100.00, 0.00, 3127.98, 0.00, 0.00, 0.00, 'cod', 'pending', 'completed', NULL, 1.00, '2026-02-08 19:07:53', '2026-02-08 19:07:53', 'TCS', 'yYaQajujf8hIhuM3BIKZTAlEpd4onK5B', '35453535', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(71, 1, 'ORD-202602090002', NULL, NULL, 2, 'pos', 6246.00, 0.00, 0.00, 0.00, 0.00, 6246.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 19:15:47', '2026-02-08 19:15:47', 'Self Pickup', '7HhDY4qC8Fqkox8I9whzSIpRNP44gUS0', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(72, 1, 'ORD-202602090003', NULL, NULL, 2, 'pos', 250.00, 0.00, 0.00, 0.00, 0.00, 250.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 19:16:15', '2026-02-08 19:16:15', 'Self Pickup', 'yVVlODjuClhwVKNAa5zZKWnAG2G0ZHiA', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(73, 1, 'ORD-202602090004', NULL, NULL, 2, 'pos', 250.00, 0.00, 0.00, 0.00, 0.00, 250.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 19:22:55', '2026-02-08 19:22:55', 'Self Pickup', '1QMQsJw8tYgXTFzUACKiDqDtWitHHnOL', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(74, 1, 'ORD-202602090005', NULL, NULL, 2, 'pos', 250.00, 0.00, 0.00, 0.00, 0.00, 250.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 19:36:20', '2026-02-08 19:36:20', 'Self Pickup', 'BAfifVPnHMRpWsFbAqvd5ULciXDQLHk0', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(75, 1, 'ORD-202602090006', NULL, NULL, 2, 'pos', 1500.00, 0.00, 0.00, 0.00, 1.00, 1500.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 19:40:12', '2026-02-08 19:40:12', 'Self Pickup', 'm8k48zDnru75jX0tPQ8KHIC5MoRdapXt', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(76, 1, 'ORD-202602090007', 6, 'reseller', 2, 'pos', 1250.00, 0.00, 0.00, 0.00, 1.00, 1250.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 19:45:28', '2026-02-08 19:45:28', 'Self Pickup', '4sKxxBef5rrqwCk2TAM7cdBvlZ9NF6gd', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(77, 1, 'ORD-202602090008', NULL, 'wholesale', 2, 'pos', 1000.00, 0.00, 0.00, 0.00, 0.00, 1000.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-08 19:45:49', '2026-02-08 19:45:49', 'Self Pickup', 'HPyvr8fXRASTdNlHdtDUpYGT3bwesysu', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(78, 1, 'ORD-202602090009', 6, 'reseller', 2, 'pos', 200.00, 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-09 16:41:26', '2026-02-09 16:41:26', 'Self Pickup', 'I0xUTudOx74DSoSqcGdceaNU8UyBao9q', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(79, 1, 'ORD-202602120001', NULL, 'wholesale', 2, 'pos', 2500.00, 0.00, 0.00, 0.00, 0.00, 2500.00, 0.00, 0.00, 0.00, 'credit', 'pending', 'completed', NULL, 0.00, '2026-02-11 19:49:34', '2026-02-11 19:49:34', 'Self Pickup', 'Ii7B9UxxbKXqatsYPrJ9stDjnflko2A8', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(80, 1, 'ORD-202602120002', 10, 'reseller', 2, 'pos', 9350.00, 0.00, 0.00, 0.00, 0.00, 9350.00, 0.00, 0.00, 0.00, 'credit', 'pending', 'completed', NULL, 0.00, '2026-02-11 19:55:10', '2026-02-11 19:55:10', 'Self Pickup', 'ohHE86Pix4qrHVNhzHmrxLwSgcOQKmTf', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(81, 1, 'ORD-202602120003', 10, 'reseller', 2, 'pos', 5950.00, 0.00, 0.00, 0.00, 0.00, 5950.00, 0.00, 0.00, 0.00, 'credit', 'pending', 'completed', NULL, 0.00, '2026-02-11 19:58:38', '2026-02-11 19:58:38', 'Self Pickup', 'dlvki2zy3td5wvi9FSeNzsiNRud1nGPl', NULL, 'cash', 'pending', NULL, NULL, 0.00, 5950.00),
(82, 1, 'ORD-202602120004', 6, 'reseller', 2, 'pos', 1700.00, 0.00, 0.00, 0.00, 0.00, 1700.00, 0.00, 0.00, 0.00, 'credit', 'pending', 'completed', NULL, 0.00, '2026-02-11 20:02:58', '2026-02-11 20:02:58', 'By Bus', 'TkmpSpduVYousYfRb7W7Aey5KVbLeBDR', NULL, 'cash', 'pending', NULL, NULL, 0.00, 1700.00),
(83, 1, 'ORD-202602150001', 6, 'reseller', 2, 'pos', 200.00, 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 'credit', 'pending', 'completed', NULL, 0.00, '2026-02-15 14:36:41', '2026-02-15 14:36:41', 'Self Pickup', 'Xiv0etLm1MAyIAg3TUgRFQnQERd0w7Mc', NULL, 'cash', 'pending', NULL, NULL, 0.00, 200.00),
(84, 1, 'ORD-202602150002', 6, 'reseller', 2, 'pos', 4800.00, 0.00, 0.00, 0.00, 1.28, 4800.00, 0.00, 0.00, 0.00, 'credit', 'pending', 'completed', NULL, 0.00, '2026-02-15 14:37:24', '2026-02-15 14:37:24', 'By Bus', 'YyZ8z3OwWFLvMSkWtxHZnhyyb5kxgXR3', NULL, 'cash', 'pending', NULL, NULL, 0.00, 4800.00),
(85, 1, 'ORD-202602160001', 10, 'reseller', 2, 'pos', 3750.00, 0.00, 0.00, 0.00, 3.00, 3750.00, 0.00, 0.00, 0.00, 'credit', 'pending', 'completed', NULL, 0.00, '2026-02-16 16:28:10', '2026-02-16 16:28:10', 'Self Pickup', 'HbK9nazWjOTovmIMNn0z7qnlOzVJM9ci', NULL, 'cash', 'pending', NULL, NULL, 0.00, 3750.00),
(86, 1, 'ORD-202602160002', 10, 'reseller', 2, 'pos', 5000.00, 0.00, 0.00, 0.00, 4.00, 5000.00, 0.00, 0.00, 0.00, 'credit', 'pending', 'completed', NULL, 0.00, '2026-02-16 16:41:11', '2026-02-16 16:41:11', 'Self Pickup', 'JhPoVZucgK9DExt3uXKdjPzSY5G3FUDA', NULL, 'cash', 'pending', 5, '2026-03-18', 0.00, 5000.00),
(87, 1, 'ORD-202602160003', 10, 'reseller', 2, 'pos', 3600.00, 0.00, 0.00, 0.00, 0.96, 3600.00, 0.00, 0.00, 0.00, 'credit', 'paid', 'completed', NULL, 0.00, '2026-02-16 16:45:56', '2026-02-16 17:32:26', 'Self Pickup', 'bw7cJ1AX49DXTNQURJAhAcf78MqhGSYh', NULL, 'cash', 'paid', 6, '2026-03-18', 3600.00, 0.00),
(88, 1, 'ORD-202602160004', 6, 'reseller', 2, 'pos', 850.00, 0.00, 0.00, 0.00, 0.00, 850.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-16 17:07:44', '2026-02-16 17:07:44', 'Self Pickup', 'P0aq7FVkcF78kpIW2SNBZxw2D1x9OH06', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(89, 1, 'ORD-202602160005', NULL, 'walkin', 2, 'pos', 3950.00, 0.00, 0.00, 0.00, 1.20, 3950.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-16 17:11:40', '2026-02-16 17:11:40', 'Self Pickup', 'K06vRL09kT6IhlDZN7lPJHvvezZPwZ04', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(90, 1, 'ORD-202602160006', 3, 'customer', 2, 'pos', 2500.00, 0.00, 0.00, 0.00, 2.40, 2500.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-02-16 17:12:13', '2026-02-16 17:12:13', 'Self Pickup', 'J8SRf1XsZk4tBPDayAB8nzNCpeZ9b6iU', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(91, 1, 'ORD-202602160007', 10, 'reseller', 2, 'pos', 200.00, 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 'credit', 'paid', 'completed', NULL, 0.00, '2026-02-16 17:13:45', '2026-02-16 17:28:52', 'Self Pickup', '7qFTeZsGpaq6Vda0c1JC8qdnUzER1AWk', '', 'cash', 'paid', 6, '2026-03-18', 200.00, 0.00),
(92, 1, 'ORD-202602160008', 10, 'reseller', 2, 'pos', 200.00, 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 'credit', 'paid', 'completed', NULL, 0.00, '2026-02-16 17:13:47', '2026-02-16 17:28:52', 'Self Pickup', 'C2NoHEBPi4DomnUMwT5aYs6XwpfzfNj3', NULL, 'cash', 'paid', 6, '2026-03-18', 200.00, 0.00),
(93, 1, 'ORD-202602160009', 10, 'reseller', 2, 'pos', 900.00, 0.00, 0.00, 0.00, 0.00, 900.00, 0.00, 0.00, 0.00, 'credit', 'paid', 'completed', NULL, 0.00, '2026-02-16 17:37:03', '2026-02-16 17:44:21', 'Self Pickup', 'vr8QaxjDi7xHTQBfAOLY1WAb4lGOYRUn', '', 'cash', 'paid', 6, '2026-03-18', 900.00, 0.00),
(94, 1, 'ORD-202602160010', 10, 'reseller', 2, 'pos', 1800.00, 0.00, 0.00, 0.00, 0.00, 1800.00, 0.00, 0.00, 0.00, 'credit', 'paid', 'completed', NULL, 0.00, '2026-02-16 17:37:07', '2026-02-16 17:44:21', 'Self Pickup', '5yRGiX99cSSc2qUnUZtSNsWwf9xjwVmE', NULL, 'cash', 'paid', 6, '2026-03-18', 1800.00, 0.00),
(95, 1, 'ORD-202602160011', 10, 'reseller', 2, 'pos', 1600.00, 0.00, 0.00, 0.00, 2.30, 1600.00, 0.00, 0.00, 0.00, 'credit', 'paid', 'completed', NULL, 0.00, '2026-02-16 17:53:53', '2026-02-16 17:57:41', 'Self Pickup', 'bLCzFd17PulsDnV0abs75v7ha3IiHt2w', '', 'cash', 'paid', 7, '2026-03-18', 1600.00, 0.00),
(96, 1, 'ORD-202602160012', 10, 'reseller', 2, 'pos', 1600.00, 0.00, 0.00, 0.00, 2.30, 1600.00, 0.00, 0.00, 0.00, 'credit', 'paid', 'completed', NULL, 0.00, '2026-02-16 17:53:59', '2026-02-16 17:58:24', 'Self Pickup', 'EmN725rDUvuRx7Eun2shX5ZHxw1RtOUE', NULL, 'cash', 'paid', 7, '2026-03-18', 1600.00, 0.00),
(98, 1, 'ORD-202603020002', NULL, 'walkin', 2, 'pos', 1200.00, 0.00, 0.00, 0.00, 0.00, 1200.00, 1200.00, 0.00, 0.00, 'cash', 'pending', 'cancelled', NULL, 0.00, '2026-03-01 19:05:31', '2026-03-06 18:45:49', 'Self Pickup', 'XvdjwICxuNb2KfeBcPYrGJhHGwXyEaYG', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(99, 1, 'ORD-202603020003', NULL, 'walkin', 2, 'pos', 492.00, 0.00, 0.00, 0.00, 10.00, 492.00, 200.00, 0.00, 292.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-01 19:06:05', '2026-03-01 19:06:05', 'By Bus', 'voXImwRa2zh5qBQG2QgCdwg3VDmpEdYq', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(100, 1, 'ORD-202603020004', NULL, 'walkin', 2, 'pos', 492.00, 0.00, 0.00, 0.00, 10.00, 492.00, 492.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-01 19:06:05', '2026-03-01 19:06:05', 'By Bus', 'h2JhVtPcOcxxEklabzVhhf7IFGiXtahp', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(101, 1, 'ORD-202603020005', NULL, 'walkin', 2, 'pos', 6000.00, 0.00, 0.00, 0.00, 0.00, 6000.00, 2000.00, 0.00, 4000.00, 'cod', 'pending', 'completed', NULL, 0.00, '2026-03-01 19:23:05', '2026-03-01 19:23:05', 'Self Pickup', 'OHQBTQ0hL0lpIst5BSksnQn2uPOBtL3u', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(102, 1, 'ORD-202603020006', NULL, 'walkin', 2, 'pos', 6000.00, 0.00, 0.00, 0.00, 0.00, 6000.00, 6000.00, 0.00, 0.00, 'cod', 'pending', 'completed', NULL, 0.00, '2026-03-01 19:23:05', '2026-03-01 19:23:05', 'Self Pickup', 'xrgLKfDc9yrWRZf3eX5djpfXIdRTJhvO', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(103, 1, 'ORD-202603020007', 7, 'reseller', 2, 'pos', 2700.00, 0.00, 0.00, 0.00, 0.00, 2700.00, 3000.00, 0.00, 0.00, 'cash', 'pending', 'refunded', NULL, 0.00, '2026-03-02 04:26:25', '2026-03-06 18:48:23', 'Self Pickup', 'Wuema6SbrFYe8orRLcvjraubWRKJ8azF', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(104, 1, 'ORD-202603020008', 7, 'reseller', 2, 'pos', 2700.00, 0.00, 0.00, 0.00, 0.00, 2700.00, 2700.00, -300.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-02 04:26:25', '2026-03-02 04:26:25', 'Self Pickup', 'b1sBlj51mkKxPRQ7oofyUh7Ery2PUnf5', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(105, 1, 'ORD-202603020009', 6, 'reseller', 2, 'pos', 11196.00, 0.00, 0.00, 0.00, 4.00, 11196.00, 5000.00, -5000.00, 6196.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-02 04:27:30', '2026-03-08 02:21:15', 'Self Pickup', '38u9ycCHSAvn5m1cpzH4GmOnmEuTf7nz', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(106, 1, 'ORD-202603020010', 6, 'reseller', 2, 'pos', 11196.00, 0.00, 0.00, 0.00, 4.00, 11196.00, 11196.00, 1196.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-02 04:27:30', '2026-03-08 02:21:15', 'Self Pickup', 'Rz1TmQ4wWACuadvfb8bkm8CCIkjFVzDr', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(107, 1, 'ORD-202603020011', 6, 'reseller', 2, 'pos', 4500.00, 0.00, 0.00, 0.00, 4.80, 4500.00, 2000.00, 1196.00, 2500.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-02 04:28:40', '2026-03-08 02:21:15', 'Self Pickup', 'rQUi0XMEr4LwfWkDWReoEeZYgmbcfxt6', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(108, 1, 'ORD-202603020012', 6, 'reseller', 2, 'pos', 4500.00, 0.00, 0.00, 0.00, 4.80, 4500.00, 4500.00, 3696.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-02 04:28:40', '2026-03-08 02:21:15', 'Self Pickup', 'AtyVREhQDWA0ePNJFECFblW01KXxdAHs', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(109, 1, 'ORD-202603020013', 6, 'reseller', 2, 'pos', 2500.00, 0.00, 0.00, 0.00, 2.00, 2500.00, 2500.00, 3696.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-02 05:56:53', '2026-03-02 05:56:53', 'Self Pickup', 'HNaJNFxnqUhYmkL4kQw6yC6tbNhd4FsT', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(110, 1, 'ORD-202603030001', 6, 'reseller', 2, 'pos', 5000.00, 0.00, 0.00, 100.00, 4.00, 5100.00, 4000.00, 3696.00, 1100.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-02 20:58:24', '2026-03-02 20:58:24', 'TCS', 'CaFfsAagkqiA043REyqyx17afATnoVe6', '34234234', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(111, 1, 'ORD-202603060001', 6, 'reseller', 2, 'pos', 657.50, 0.00, 0.00, 0.00, 5.23, 657.50, 200.00, 4796.00, 457.50, 'cash', 'pending', 'completed', '', 0.00, '2026-03-05 19:27:09', '2026-03-05 19:27:53', 'Self Pickup', 'YxI08I3xnCWqcEqprqbRwM4ZyBG8fQpA', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(112, 1, 'ORD-202603060002', NULL, 'walkin', 2, 'pos', 6222.00, 0.00, 0.00, 0.00, 5.05, 6222.00, 6222.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-06 17:57:26', '2026-03-06 17:57:26', 'TCS', '2TSqyfFyQMpDSBY7IsoGo987s3In1n0N', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(113, 1, 'ORD-202603060003', 6, 'reseller', 2, 'pos', 900.00, 0.00, 0.00, 0.00, 0.00, 900.00, 6000.00, 5253.50, 0.00, 'cash', 'pending', 'completed', 'due date changed', 0.00, '2026-03-06 18:32:57', '2026-03-06 18:32:57', 'By Bus', 'TbTRwwUA7xkpIxuxt1CdPYnCsn2mmm4d', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(114, 1, 'ORD-202603070001', 6, 'reseller', 2, 'pos', 6800.00, 136.00, 100.00, 0.00, 15.00, 6836.00, 5000.00, 0.00, 1836.00, 'cash', 'pending', 'completed', 'order recieve', 2.00, '2026-03-06 19:31:57', '2026-03-06 19:31:57', 'Self Pickup', 'WPFURZ5uiA8TBJGPf8r3r8w6MtcPQheo', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(115, 1, 'ORD-202603070002', 6, 'reseller', 2, 'pos', 1250.00, 0.00, 0.00, 0.00, 1.00, 1250.00, 1250.00, 1836.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-07 02:46:08', '2026-03-07 02:46:08', 'Self Pickup', 'r0Fk6liQTvUlRx8gRA5JEPj4YXwIUYKG', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(116, 1, 'ORD-202603070003', 6, 'reseller', 2, 'pos', 1200.00, 20.00, 200.00, 200.00, 15.00, 1220.00, 1020.00, 1836.00, 200.00, 'cash', 'pending', 'completed', 'Notes', 2.00, '2026-03-07 04:41:50', '2026-03-07 04:41:50', 'TCS', 'EtWob7atWbLyl7cm2ITWZ7vW9VTazMfG', 'a4234234234', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(117, 1, 'ASM1272', 6, 'reseller', 2, 'pos', 1600.00, 32.00, 0.00, 0.00, 2.30, 1632.00, 1632.00, 2036.00, 0.00, 'cash', 'pending', 'completed', NULL, 2.00, '2026-03-07 04:52:34', '2026-03-07 04:52:34', 'TCS', '1VFFHFKvBZuJKAFBFcjTDX5eVrhaenSz', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(118, 1, 'ASM1273', 7, 'reseller', 2, 'pos', 200.00, 0.00, 0.00, 50.00, 1000.00, 250.00, 250.00, -300.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-07 12:56:58', '2026-03-08 02:21:15', 'TCS', '0KjKXe7E1zyKbnrQ9AAu5OS3RToscJXz', '2343344', 'cash', 'none', NULL, NULL, 0.00, 0.00),
(119, 1, 'ASM1274', NULL, 'walkin', 2, 'pos', 123.00, 0.00, 0.00, 0.00, 2.50, 123.00, 123.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-08 03:28:47', '2026-03-08 18:42:34', 'Self Pickup', 'ZOIIV5YzAkpkCcJwqiWrOZStgIXcwcmE', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00),
(120, 2, 'ASM1275', NULL, 'walkin', 2, 'pos', 400.00, 0.00, 0.00, 0.00, 0.00, 400.00, 400.00, 0.00, 0.00, 'cash', 'pending', 'completed', NULL, 0.00, '2026-03-08 22:10:12', '2026-03-08 22:10:12', 'Self Pickup', 'NWhMGPprXGnMZI6lI3bK6yHkd3ST5aDa', NULL, 'cash', 'none', NULL, NULL, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variant_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL,
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
(93, 53, 35, NULL, 1, 800.00, 800.00, '2025-12-21 01:49:09', '2025-12-21 01:49:09'),
(94, 54, 10, NULL, 1, 1299.00, 1299.00, '2025-12-22 16:45:14', '2025-12-22 16:45:14'),
(95, 55, 8, NULL, 1, 100.00, 100.00, '2025-12-22 16:49:53', '2025-12-22 16:49:53'),
(98, 58, 21, NULL, 1, 600.00, 600.00, '2025-12-22 17:01:57', '2025-12-22 17:01:57'),
(99, 58, 8, NULL, 1, 70.00, 70.00, '2025-12-22 17:01:57', '2025-12-22 17:01:57'),
(100, 59, 28, NULL, 1, 150.00, 150.00, '2025-12-22 18:14:49', '2025-12-22 18:14:49'),
(101, 59, 33, NULL, 1, 180.00, 180.00, '2025-12-22 18:14:49', '2025-12-22 18:14:49'),
(102, 59, 32, NULL, 1, 150.00, 150.00, '2025-12-22 18:14:49', '2025-12-22 18:14:49'),
(103, 60, 28, NULL, 2, 150.00, 300.00, '2025-12-22 18:15:08', '2025-12-22 18:15:08'),
(104, 61, 14, NULL, 2, 1200.00, 2400.00, '2025-12-22 18:20:23', '2025-12-22 18:20:23'),
(105, 62, 25, NULL, 1, 80.00, 80.00, '2025-12-28 15:37:17', '2025-12-28 15:37:17'),
(106, 63, 1, NULL, 1, 1500.00, 1500.00, '2026-01-17 15:32:34', '2026-01-17 15:32:34'),
(107, 64, 13, NULL, 1, 1499.00, 1499.00, '2026-02-04 20:32:21', '2026-02-04 20:32:21'),
(108, 65, 22, NULL, 10, 120.00, 1200.00, '2026-02-04 20:36:41', '2026-02-04 20:36:41'),
(109, 66, 20, NULL, 2, 1150.00, 2300.00, '2026-02-04 20:37:26', '2026-02-04 20:37:26'),
(110, 67, 8, NULL, 2, 100.00, 200.00, '2026-02-08 18:11:05', '2026-02-08 18:11:05'),
(112, 69, 13, NULL, 2, 1499.00, 2998.00, '2026-02-08 18:16:29', '2026-02-08 18:16:29'),
(113, 70, 13, NULL, 2, 1499.00, 2998.00, '2026-02-08 19:07:53', '2026-02-08 19:07:53'),
(114, 71, 13, NULL, 4, 1499.00, 5996.00, '2026-02-08 19:15:47', '2026-02-08 19:15:47'),
(115, 71, 12, NULL, 1, 250.00, 250.00, '2026-02-08 19:15:47', '2026-02-08 19:15:47'),
(116, 72, 12, NULL, 1, 250.00, 250.00, '2026-02-08 19:16:15', '2026-02-08 19:16:15'),
(117, 73, 12, NULL, 1, 250.00, 250.00, '2026-02-08 19:22:55', '2026-02-08 19:22:55'),
(118, 74, 12, NULL, 1, 250.00, 250.00, '2026-02-08 19:36:20', '2026-02-08 19:36:20'),
(119, 75, 1, NULL, 1, 1500.00, 1500.00, '2026-02-08 19:40:12', '2026-02-08 19:40:12'),
(120, 76, 1, NULL, 1, 1250.00, 1250.00, '2026-02-08 19:45:28', '2026-02-08 19:45:28'),
(121, 77, 11, NULL, 1, 1000.00, 1000.00, '2026-02-08 19:45:49', '2026-02-08 19:45:49'),
(122, 78, 12, NULL, 1, 200.00, 200.00, '2026-02-09 16:41:26', '2026-02-09 16:41:26'),
(123, 79, 12, NULL, 10, 250.00, 2500.00, '2026-02-11 19:49:34', '2026-02-11 19:49:34'),
(124, 80, 11, NULL, 11, 850.00, 9350.00, '2026-02-11 19:55:10', '2026-02-11 19:55:10'),
(125, 81, 11, NULL, 7, 850.00, 5950.00, '2026-02-11 19:58:38', '2026-02-11 19:58:38'),
(126, 82, 11, NULL, 2, 850.00, 1700.00, '2026-02-11 20:02:58', '2026-02-11 20:02:58'),
(127, 83, 12, NULL, 1, 200.00, 200.00, '2026-02-15 14:36:41', '2026-02-15 14:36:41'),
(128, 84, 21, NULL, 8, 600.00, 4800.00, '2026-02-15 14:37:24', '2026-02-15 14:37:24'),
(129, 85, 1, NULL, 3, 1250.00, 3750.00, '2026-02-16 16:28:10', '2026-02-16 16:28:10'),
(130, 86, 1, NULL, 4, 1250.00, 5000.00, '2026-02-16 16:41:11', '2026-02-16 16:41:11'),
(131, 87, 21, NULL, 6, 600.00, 3600.00, '2026-02-16 16:45:56', '2026-02-16 16:45:56'),
(132, 88, 11, NULL, 1, 850.00, 850.00, '2026-02-16 17:07:44', '2026-02-16 17:07:44'),
(133, 89, 11, NULL, 2, 1000.00, 2000.00, '2026-02-16 17:11:40', '2026-02-16 17:11:40'),
(134, 89, 18, NULL, 3, 650.00, 1950.00, '2026-02-16 17:11:40', '2026-02-16 17:11:40'),
(135, 90, 24, NULL, 1, 2500.00, 2500.00, '2026-02-16 17:12:13', '2026-02-16 17:12:13'),
(136, 91, 12, NULL, 1, 200.00, 200.00, '2026-02-16 17:13:45', '2026-02-16 17:13:45'),
(137, 92, 12, NULL, 1, 200.00, 200.00, '2026-02-16 17:13:47', '2026-02-16 17:13:47'),
(138, 93, 14, NULL, 1, 900.00, 900.00, '2026-02-16 17:37:03', '2026-02-16 17:37:03'),
(139, 94, 14, NULL, 2, 900.00, 1800.00, '2026-02-16 17:37:07', '2026-02-16 17:37:07'),
(140, 95, 19, NULL, 2, 800.00, 1600.00, '2026-02-16 17:53:53', '2026-02-16 17:53:53'),
(141, 96, 19, NULL, 2, 800.00, 1600.00, '2026-02-16 17:53:59', '2026-02-16 17:53:59'),
(143, 98, 14, NULL, 1, 1200.00, 1200.00, '2026-03-01 19:05:31', '2026-03-01 19:05:31'),
(144, 99, 37, NULL, 4, 123.00, 492.00, '2026-03-01 19:06:05', '2026-03-01 19:06:05'),
(145, 100, 37, NULL, 4, 123.00, 492.00, '2026-03-01 19:06:05', '2026-03-01 19:06:05'),
(146, 101, 14, NULL, 5, 1200.00, 6000.00, '2026-03-01 19:23:05', '2026-03-01 19:23:05'),
(147, 102, 14, NULL, 5, 1200.00, 6000.00, '2026-03-01 19:23:05', '2026-03-01 19:23:05'),
(148, 103, 14, NULL, 3, 900.00, 2700.00, '2026-03-02 04:26:25', '2026-03-02 04:26:25'),
(149, 104, 14, NULL, 3, 900.00, 2700.00, '2026-03-02 04:26:25', '2026-03-02 04:26:25'),
(150, 105, 23, NULL, 4, 2799.00, 11196.00, '2026-03-02 04:27:30', '2026-03-02 04:27:30'),
(151, 106, 23, NULL, 4, 2799.00, 11196.00, '2026-03-02 04:27:30', '2026-03-02 04:27:30'),
(152, 107, 24, NULL, 2, 2250.00, 4500.00, '2026-03-02 04:28:40', '2026-03-02 04:28:40'),
(153, 108, 24, NULL, 2, 2250.00, 4500.00, '2026-03-02 04:28:40', '2026-03-02 04:28:40'),
(154, 109, 1, NULL, 2, 1250.00, 2500.00, '2026-03-02 05:56:53', '2026-03-02 05:56:53'),
(155, 110, 1, NULL, 4, 1250.00, 5000.00, '2026-03-02 20:58:24', '2026-03-02 20:58:24'),
(157, 111, 37, NULL, 2, 100.00, 200.00, '2026-03-05 19:27:53', '2026-03-05 19:27:53'),
(158, 111, 34, NULL, 1, 300.00, 300.00, '2026-03-05 19:27:53', '2026-03-05 19:27:53'),
(159, 111, 30, NULL, 1, 150.00, 157.50, '2026-03-05 19:27:53', '2026-03-05 19:27:53'),
(160, 112, 19, NULL, 1, 950.00, 950.00, '2026-03-06 17:57:26', '2026-03-06 17:57:26'),
(161, 112, 18, NULL, 1, 650.00, 650.00, '2026-03-06 17:57:26', '2026-03-06 17:57:26'),
(162, 112, 14, NULL, 1, 1200.00, 1200.00, '2026-03-06 17:57:26', '2026-03-06 17:57:26'),
(163, 112, 1, NULL, 1, 1500.00, 1500.00, '2026-03-06 17:57:26', '2026-03-06 17:57:26'),
(164, 112, 7, NULL, 1, 1799.00, 1799.00, '2026-03-06 17:57:26', '2026-03-06 17:57:26'),
(165, 112, 37, NULL, 1, 123.00, 123.00, '2026-03-06 17:57:26', '2026-03-06 17:57:26'),
(166, 113, 14, NULL, 1, 900.00, 900.00, '2026-03-06 18:32:57', '2026-03-06 18:32:57'),
(167, 114, 37, NULL, 6, 100.00, 600.00, '2026-03-06 19:31:57', '2026-03-06 19:31:57'),
(168, 114, 7, NULL, 4, 1550.00, 6200.00, '2026-03-06 19:31:57', '2026-03-06 19:31:57'),
(169, 115, 1, NULL, 1, 1250.00, 1250.00, '2026-03-07 02:46:08', '2026-03-07 02:46:08'),
(170, 116, 37, NULL, 6, 200.00, 1200.00, '2026-03-07 04:41:50', '2026-03-07 04:41:50'),
(171, 117, 19, NULL, 2, 800.00, 1600.00, '2026-03-07 04:52:34', '2026-03-07 04:52:34'),
(172, 118, 36, NULL, 1, 200.00, 200.00, '2026-03-07 12:56:58', '2026-03-07 12:56:58'),
(173, 119, 37, NULL, 1, 123.00, 123.00, '2026-03-08 03:28:47', '2026-03-08 03:28:47'),
(174, 120, 8, NULL, 4, 100.00, 400.00, '2026-03-08 22:10:12', '2026-03-08 22:10:12');

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'order',
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `payment_number`, `payment_type`, `order_id`, `amount`, `payment_date`, `payment_method`, `reference_number`, `notes`, `method`, `reference`, `status`, `created_at`, `updated_at`, `customer_id`, `created_by`) VALUES
(8, 'PAY-202602-0001', 'order', 95, 1600.00, '2026-02-16', NULL, NULL, '', 'cash', '', 'completed', '2026-02-16 17:57:41', '2026-02-16 17:57:41', 10, 2),
(9, 'PAY-202602-0002', 'order', 96, 1600.00, '2026-02-16', NULL, NULL, '', 'cash', '', 'completed', '2026-02-16 17:58:24', '2026-02-16 17:58:24', 10, 2),
(10, 'PAY-202603-0001', 'order', 103, 3000.00, '2026-03-02', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-02 04:26:25', '2026-03-02 04:26:25', 7, 2),
(11, 'PAY-202603-0002', 'order', 104, 2700.00, '2026-03-02', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-02 04:26:25', '2026-03-02 04:26:25', 7, 2),
(12, 'PAY-202603-0003', 'order', 105, 5000.00, '2026-03-02', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-02 04:27:30', '2026-03-02 04:27:30', 6, 2),
(13, 'PAY-202603-0004', 'order', 106, 11196.00, '2026-03-02', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-02 04:27:30', '2026-03-02 04:27:30', 6, 2),
(14, 'PAY-202603-0005', 'order', 107, 2000.00, '2026-03-02', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-02 04:28:40', '2026-03-02 04:28:40', 6, 2),
(15, 'PAY-202603-0006', 'order', 108, 4500.00, '2026-03-02', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-02 04:28:40', '2026-03-02 04:28:40', 6, 2),
(16, 'PAY-202603-0007', 'khata', NULL, 5000.00, '2026-03-02', 'cash', 'KHATA-69A558B80727C', 'received ', 'cash', NULL, 'completed', '2026-03-02 04:30:32', '2026-03-02 04:30:32', 6, 2),
(17, 'PAY-202603-0008', 'order', 109, 2500.00, '2026-03-02', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-02 05:56:53', '2026-03-02 05:56:53', 6, 2),
(18, 'PAY-202603-0009', 'order', 110, 4000.00, '2026-03-03', 'cash', '34234234', NULL, 'cash', NULL, 'completed', '2026-03-02 20:58:24', '2026-03-02 20:58:24', 6, 2),
(19, 'PAY-202603-0010', 'order', 111, 200.00, '2026-03-06', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-05 19:27:09', '2026-03-05 19:27:09', 6, 2),
(20, 'PAY-202603-0011', 'order', 113, 6000.00, '2026-03-06', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-06 18:32:57', '2026-03-06 18:32:57', 6, 2),
(21, 'PAY-202603-0012', 'khata', NULL, 153.50, '2026-03-07', 'cash', 'KHATA-69AB6B953E92F', '', 'cash', NULL, 'completed', '2026-03-06 19:04:37', '2026-03-06 19:04:37', 6, 2),
(22, 'PAY-202603-0013', 'order', 114, 5000.00, '2026-03-07', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-06 19:31:57', '2026-03-06 19:31:57', 6, 2),
(23, 'PAY-202603-0014', 'order', 115, 1250.00, '2026-03-07', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-07 02:46:08', '2026-03-07 02:46:08', 6, 2),
(24, 'PAY-202603-0015', 'order', 116, 1020.00, '2026-03-07', 'cash', 'a4234234234', NULL, 'cash', NULL, 'completed', '2026-03-07 04:41:50', '2026-03-07 04:41:50', 6, 2),
(25, 'PAY-202603-0016', 'order', 117, 1632.00, '2026-03-07', 'cash', NULL, NULL, 'cash', NULL, 'completed', '2026-03-07 04:52:34', '2026-03-07 04:52:34', 6, 2),
(26, 'PAY-202603-0017', 'order', 118, 250.00, '2026-03-07', 'cash', '2343344', NULL, 'cash', NULL, 'completed', '2026-03-07 12:56:58', '2026-03-07 12:56:58', 7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `present_days` int NOT NULL DEFAULT '0',
  `absent_days` int NOT NULL DEFAULT '0',
  `late_days` int NOT NULL DEFAULT '0',
  `gross_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('unpaid','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `hourly_rate` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payrolls`
--

INSERT INTO `payrolls` (`id`, `branch_id`, `employee_id`, `month`, `year`, `present_days`, `absent_days`, `late_days`, `gross_salary`, `deductions`, `net_salary`, `status`, `created_at`, `updated_at`, `total_hours`, `hourly_rate`) VALUES
(37, 1, 4, 9, 2025, 29, 0, 0, 5000.00, -174.28, 5174.28, 'unpaid', '2025-09-16 02:22:31', '2025-10-03 15:43:21', 215.25, 24.04),
(38, 1, 5, 9, 2025, 14, 0, 0, 12000.00, 1898.08, 10101.92, 'unpaid', '2025-10-03 15:43:21', '2025-10-03 15:43:21', 175.10, 57.69),
(39, 1, 4, 3, 2026, 1, 0, 0, 5000.00, 5000.00, 0.00, 'paid', '2026-03-08 02:53:15', '2026-03-08 02:54:26', 0.00, 28.41),
(40, 1, 5, 3, 2026, 1, 0, 0, 12000.00, 12000.00, 0.00, 'paid', '2026-03-08 02:53:15', '2026-03-08 02:54:26', 0.00, 68.18);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(16, 'manage payroll', 'web', '2025-09-14 16:23:35', '2025-09-14 16:23:35'),
(17, 'manage credit', 'web', '2026-02-11 19:14:07', '2026-02-11 19:14:07'),
(18, 'view credit dashboard', 'web', '2026-02-11 19:14:07', '2026-02-11 19:14:07'),
(19, 'enable credit', 'web', '2026-02-11 19:14:07', '2026-02-11 19:14:07'),
(20, 'disable credit', 'web', '2026-02-11 19:14:07', '2026-02-11 19:14:07'),
(21, 'collect credit payment', 'web', '2026-02-11 19:14:07', '2026-02-11 19:14:07'),
(22, 'view credit statement', 'web', '2026-02-11 19:14:07', '2026-02-11 19:14:07'),
(23, 'export credit statement', 'web', '2026-02-11 19:14:07', '2026-02-11 19:14:07'),
(24, 'view overdue report', 'web', '2026-02-11 19:14:07', '2026-02-11 19:14:07'),
(25, 'manage ledger', 'web', '2026-03-01 23:01:26', '2026-03-01 23:01:26'),
(26, 'manage branches', 'web', '2026-03-08 03:24:16', '2026-03-08 03:24:16'),
(27, 'view all branches', 'web', '2026-03-08 03:24:16', '2026-03-08 03:24:16');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `resale_price` decimal(10,2) DEFAULT NULL,
  `wholesale_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `weight` decimal(8,4) DEFAULT NULL,
  `stock_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reorder_level` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `track_inventory` tinyint(1) NOT NULL DEFAULT '1',
  `rank` varchar(555) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `branch_id`, `category_id`, `name`, `barcode`, `description`, `price`, `sale_price`, `resale_price`, `wholesale_price`, `cost_price`, `weight`, `stock_quantity`, `reorder_level`, `image`, `is_active`, `created_at`, `updated_at`, `track_inventory`, `rank`, `unit_id`) VALUES
(1, 1, 4, 'Al Quran Al Kareem', '313', 'Tarjuma Allama Ali Naqi Naqan Sahb', 2000.00, 1500.00, 1250.00, 1200.00, 1030.00, 1.0000, 42.00, 3, 'products/8LRzHKiD2L0RSekKX5Qz6ZqXGnef72L8MWxFkNjW.jpg', 1, '2025-06-26 19:00:13', '2026-03-07 02:46:08', 1, 'Shelf3-Box-5', NULL),
(7, 1, 4, 'Balagh Ul Quran Samall', '314', 'Tarjuma Allama Sheikh Mohsin Ali Najfi', 2000.00, 1799.00, 1550.00, 1450.00, 1230.00, NULL, 283.00, 3, 'products/a0ngX7d4AmpBgAQZaeDjox4Hu2BKfFVXuoX4LP7m.jpg', 1, '2025-06-26 19:50:58', '2026-03-06 19:31:57', 1, NULL, NULL),
(8, 1, 4, 'Yasarnal Quran', '315', 'Quaida', 2000.00, 100.00, 70.00, 65.00, 1200.00, 0.0000, 6.00, 20, 'products/Z3RIBfaiHeUB4vhCRkyEFhu85nyZIy3X3xsd8MEG.jpg', 1, '2025-06-26 19:57:14', '2026-02-08 18:11:05', 1, '1', NULL),
(9, 1, 4, 'Quran Pocket Size', '316', 'With out Tarjuma', 400.00, 400.00, 300.00, 280.00, 240.00, NULL, -9.00, 5, 'products/9B9zeYdy460ja9ef4nBuJ2xqJXC2h36Zs9boxGgm.jpg', 1, '2025-06-26 19:57:15', '2025-12-20 02:41:53', 1, NULL, NULL),
(10, 1, 4, 'Quran Mubeen', '317', 'Tarjuma Allama M Hussain Akbar', NULL, 1299.00, 1099.00, 999.00, 730.00, NULL, 0.00, 2, 'products/jTcGBziyP0B4J3m3aGCjbd8MDkl9Kw2NSffGB5SE.jpg', 1, '2025-09-14 13:09:06', '2025-12-22 16:45:14', 1, NULL, NULL),
(11, 1, 5, 'Namaz Chadar Open Swiss-M', '318', 'Swiss Lawn Open Round, Irani Style Namaz Chadar ', NULL, 1000.00, 850.00, 790.00, 600.00, NULL, 20.00, 20, NULL, 1, '2025-09-17 07:48:38', '2026-02-16 17:11:40', 1, NULL, NULL),
(12, 1, 6, 'Syed e Muqawimat', '319', 'Book', NULL, 250.00, 200.00, 175.00, 148.00, NULL, 28.00, 5, 'products/0lrY4EqBs3Hc1Dt9gyuNy51dloKUyEvpbe8IBPe6.jpg', 1, '2025-09-21 05:51:13', '2026-02-16 17:13:47', 1, NULL, NULL),
(13, 1, 4, 'Anwar ul Quran (DiarySize)', '320', 'Quran Majeed', NULL, 1499.00, 1250.00, 1150.00, 1020.00, NULL, 0.00, 3, 'products/0pDjmL2TYVwLMkCpblZw4DUX9Gih0JtJ2q0VUm0C.jpg', 1, '2025-09-21 06:14:54', '2026-02-08 19:15:47', 1, NULL, NULL),
(14, 1, 5, 'Namaz Chadar Swiss Lawn Open-L', '321', 'Namaz Chadar open', NULL, 1200.00, 900.00, 800.00, 680.00, NULL, 78.00, 20, NULL, 1, '2025-09-21 23:11:45', '2026-03-06 18:48:23', 1, NULL, NULL),
(18, 1, 7, 'Valvet Jay Namaz Small', '322', 'Jay Namaz', NULL, 650.00, 500.00, 420.00, 270.00, 0.4000, 43.00, 5, 'products/7lylFr8NMcBF2rdL8t7Z0X28tY4TmJxTiOW6DHgJ.jpg', 1, '2025-09-26 23:10:41', '2026-03-06 17:57:26', 1, NULL, NULL),
(19, 1, 7, 'Valvet Jay Namaz Medium', '323', 'Jay Namaz', NULL, 950.00, 800.00, 700.00, 500.00, 1.1500, 41.00, 5, 'products/Yib1NaQbMQU0ZJBK2vpl5n4Ho2jVvaWvhBFnepHP.jpg', 1, '2025-09-26 23:14:03', '2026-03-07 04:52:34', 1, NULL, NULL),
(20, 1, 7, 'Valvet Jay Namaz Large', '324', 'Jay Namaz', NULL, 1150.00, 950.00, 890.00, 600.00, 1.5000, 64.00, 5, 'products/TFNLpua7XboEjyCNR8XZFqtC4c8qrnRrnXVyABi5.jpg', 1, '2025-09-26 23:16:59', '2026-02-04 20:37:26', 1, NULL, NULL),
(21, 1, 8, 'Printed Basiji', '325', 'Basiji Romal Printed', NULL, 700.00, 600.00, 550.00, 370.00, 0.1600, 3.00, 5, 'products/7kfonooKkoUt6DHuQstMSXb2CDBkoVVMMItQDPO9.jpg', 1, '2025-10-01 01:35:35', '2026-02-16 16:45:56', 1, NULL, NULL),
(22, 1, 7, 'Hadia Hussaini', '326', 'Sajda Gah Tasbeeh Box', NULL, 120.00, 100.00, 95.00, 83.00, 0.0800, 0.00, 5, 'products/iFqe5nsKIAxn23phM8SVUs5UXhlbTDvzFIb6CBeN.jpg', 1, '2025-10-02 03:39:32', '2026-02-04 20:36:41', 1, NULL, NULL),
(23, 1, 5, 'Irani Hijab Round Chadar ', ' 327  ', '', NULL, 2999.00, 2799.00, 2699.00, 2200.00, 1.0000, 0.00, 5, 'products/9pCAGDNcECQ4nb1ssB4iC93ywwHV8QXsDvui3uj6.jpg', 1, '2025-12-13 11:45:20', '2026-03-02 04:27:30', 1, NULL, NULL),
(24, 1, 4, 'Balagh ul Quran', '328', 'VIP', NULL, 2500.00, 2250.00, 2100.00, 1845.00, 2.4000, -45.00, 5, 'products/FvMpcH4v9yEq4dagigakRz2OCZIzay4SI4qD2frN.jpg', 1, '2025-12-20 02:23:14', '2026-03-08 02:45:32', 1, NULL, NULL),
(25, 1, 7, 'Dua e Tawasal', '329', 'Dua ', NULL, 80.00, 75.00, 65.00, 50.00, 0.0400, 3.00, 5, NULL, 1, '2025-12-20 02:26:29', '2025-12-28 15:37:17', 1, NULL, NULL),
(26, 1, 7, 'Dua e Kumail', '330', 'Dua', NULL, 120.00, 90.00, 80.00, 70.00, 0.0400, 4.00, 5, NULL, 1, '2025-12-20 02:29:10', '2025-12-20 02:47:50', 1, NULL, NULL),
(27, 1, 7, 'Dua e Joshan Kabeer', '331', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.1100, 4.00, 5, NULL, 1, '2025-12-20 02:31:27', '2025-12-20 02:47:50', 1, NULL, NULL),
(28, 1, 7, 'Dua e Noor', '332', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.0500, 1.00, 5, NULL, 1, '2025-12-20 02:32:44', '2025-12-22 18:15:08', 1, NULL, NULL),
(29, 1, 7, 'Ziarat e Aal e Yaseen', '333', 'Dua', NULL, 100.00, 80.00, 75.00, 70.00, 0.0400, 5.00, 5, NULL, 1, '2025-12-20 02:34:59', '2025-12-20 02:44:35', 1, NULL, NULL),
(30, 1, 7, 'Dua e Nudba', '334', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.0800, 3.95, 5, NULL, 1, '2025-12-20 02:36:14', '2026-03-05 19:27:53', 1, NULL, NULL),
(31, 1, 7, 'Ziarat e Nahia o Warisa', '335', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.0700, 5.00, 5, NULL, 1, '2025-12-20 02:37:05', '2025-12-20 02:44:35', 1, NULL, NULL),
(32, 1, 7, 'Majmoa Dua', '336', 'Dua', NULL, 150.00, 125.00, 120.00, 110.00, 0.0800, 4.00, 5, NULL, 1, '2025-12-20 02:37:56', '2025-12-22 18:14:49', 1, NULL, NULL),
(33, 1, 7, 'Ziarat e Ashoora o Arbaeen', '337', 'Dua', NULL, 180.00, 160.00, 145.00, 130.00, 0.0800, 4.00, 5, NULL, 1, '2025-12-20 02:38:47', '2025-12-22 18:14:49', 1, NULL, NULL),
(34, 1, 7, 'Taqibat e Namaz', '338', 'Dua', NULL, 300.00, 250.00, 230.00, 210.00, 0.1500, 4.00, 5, NULL, 1, '2025-12-20 02:41:17', '2026-03-05 19:27:53', 1, NULL, NULL),
(35, 1, 4, 'Quran al Kareem 150-L', '339', 'With out Translation Quran', NULL, 800.00, 700.00, 620.00, 575.00, 1.5700, 0.00, 2, 'products/SADKLHMhOXUjaErze5DObSRW7Z0BF1KqTnXhTdgW.jpg', 1, '2025-12-21 01:47:00', '2025-12-21 01:49:09', 1, NULL, NULL),
(36, 1, 5, 'abc', '1231', '123123123', NULL, 250.00, 200.00, 150.00, 120.00, 1000.0000, 49.00, 5, 'products/SDpy3Avy4mcRFtxL281e5mnuCZB4QPvEFSqQOQ01.png', 1, '2025-12-28 15:10:26', '2026-03-07 12:56:58', 1, '12', NULL),
(37, 1, 8, 'abc', '2121', '123123123', NULL, 123.00, 100.00, 90.00, 150.00, 2.5000, 476.00, 5, 'products/vSA5GnzOdAghPpAErYu4qHlM4gPytyaIJx7SZ7hy.png', 1, '2026-02-08 14:00:39', '2026-03-08 03:28:47', 1, '123', '2'),
(38, 2, 9, 'Jogers', '3333', '', NULL, 1890.00, 1680.00, 1575.00, 1500.00, 2.0000, 100.00, 10, 'products/cNyxxWyj3DnbYxzo2as7bl5LOCdlTjAO66ZzPcXx.png', 1, '2026-03-08 23:02:59', '2026-03-08 23:02:59', 1, 'Shelf3-Box-5', '12');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variant_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variant_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('paid','partial','unpaid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `purchase_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `branch_id`, `supplier_id`, `invoice_number`, `total_amount`, `paid_amount`, `payment_status`, `purchase_date`, `notes`, `created_at`, `updated_at`) VALUES
(9, 1, 6, 'INV-Z6PXXTRI', 5400.00, 5400.00, 'paid', '2025-12-18', '', '2025-12-20 02:44:35', '2025-12-20 02:44:35');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
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
(24, 9, 34, 5, 210.00, 1050.00, '2025-12-20 02:44:35', '2025-12-20 02:44:35');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`id`, `order_id`, `user_id`, `amount`, `reason`, `status`, `created_at`, `updated_at`) VALUES
(1, 103, 2, 2700.00, 'no reason', 'completed', '2026-03-06 18:48:23', '2026-03-06 18:48:23');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(4, 'employee', 'web', '2025-06-23 18:41:58', '2025-06-23 18:41:58'),
(5, 'super_admin', 'web', '2026-03-08 22:45:47', '2026-03-08 22:45:47');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(6, 2),
(7, 2),
(8, 2),
(10, 2),
(12, 2),
(1, 3),
(2, 3),
(6, 3),
(12, 3),
(1, 4),
(8, 4),
(1, 5),
(2, 5),
(3, 5),
(4, 5),
(5, 5),
(6, 5),
(7, 5),
(8, 5),
(9, 5),
(10, 5),
(11, 5),
(12, 5),
(13, 5),
(14, 5),
(15, 5),
(16, 5),
(17, 5),
(18, 5),
(19, 5),
(20, 5),
(21, 5),
(22, 5),
(23, 5),
(24, 5),
(25, 5),
(26, 5),
(27, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('8kzI1UVJyEZC7TgdnLrFcciGLYQSF2JefVkPNzGZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOG9nR3BIVlVBRHNsMDJvWEYyRmdiUFczUVNSVWlVVlBrUTRvdVRtcCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2JyYW5jaGVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773043005),
('bVIek99PwhLgr51czHuOo0F0W2mhBi4zNijuM6Rb', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoid21wSDNYR0NOYjdabVlHWnREbU9PYkVUTTRyeG4yM2N2SG5DOHdIbyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Byb2R1Y3RzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773043005),
('dJI9ZnsUaip4a75YNqZeQrhilitnsX6K4k7kMpcP', NULL, '127.0.0.1', 'curl/8.7.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSVFEZzB4TjJwREo5eGtjWXBMNjMzQ1JwN3JreEZvbkM1eVdjM3FzMyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjE1OiJhZG1pbi5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773010374),
('MuNB75UkJZLPofMXoZ4aRNB69IiRlKecGQOB6hNR', 2, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMTVOS3A3bmxhUEl5TmxPeWNHNTM1ODJkUzJmSzBwbU16c3BiaTk1UiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9icmFuY2hlcyI7czo1OiJyb3V0ZSI7czoyMDoiYWRtaW4uYnJhbmNoZXMuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6OToiYnJhbmNoX2lkIjtpOjE7fQ==', 1773014026);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `branch_id`, `name`, `email`, `phone`, `address`, `company_name`, `created_at`, `updated_at`) VALUES
(6, 1, 'Alamdar Bhai', '', '+923335234311', 'Islam Abad', 'M Ali Book Depot ISB', '2025-06-27 19:56:46', '2025-12-20 02:13:48'),
(7, 1, 'M Ali (Balagh ul Quran)', '', '+923455396506', 'Jamia Al Kosar Islamabad', 'Al Koser Balagh ul QUran', '2025-12-20 02:15:46', '2025-12-20 02:15:46');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `abbreviation`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Piece', 'pcs', 'Single item or unit', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(2, 'Dozen', 'dz', '12 pieces', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(3, 'Kilogram', 'kg', 'Weight measurement', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(4, 'Gram', 'g', 'Weight measurement', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(5, 'Liter', 'L', 'Volume measurement', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(6, 'Milliliter', 'mL', 'Volume measurement', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(7, 'Meter', 'm', 'Length measurement', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(8, 'Centimeter', 'cm', 'Length measurement', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(9, 'Box', 'box', 'Complete box', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(10, 'Packet', 'pkt', 'Packet of items', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(11, 'Carton', 'ctn', 'Carton box', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(12, 'Pair', 'pr', 'Two items together', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(13, 'Set', 'set', 'Complete set of items', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(14, 'Bottle', 'btl', 'Bottle container', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(15, 'Can', 'can', 'Can container', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(16, 'Roll', 'roll', 'Roll of material', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(17, 'Sheet', 'sheet', 'Single sheet', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(18, 'Bag', 'bag', 'Bag of items', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(19, 'Pack', 'pack', 'Pack of items', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32'),
(20, 'Unit', 'unit', 'Generic unit', 1, '2026-02-08 13:37:32', '2026-02-08 13:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` enum('admin','manager','cashier','employee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cashier',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `branch_id`, `name`, `email`, `password`, `user_type`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 2, 'Furqan', 'furqan7853@gmail.com', '$2y$12$mYzJdgj.YYiHDCBEaQF00OPVEwbr9IKm80PQsLr5Dy.3M2O2LPTJi', 'cashier', NULL, NULL, '2025-06-23 18:31:02', '2026-03-08 23:08:57'),
(2, NULL, 'Admin', 'admin@gmail.com', '$2y$12$Yh./6bb2oRHY2PEYi.4AgeJeIP98hV1uqd5hd6nSd88GPj4jW0IDe', 'admin', '2025-09-14 19:07:43', '3Awn6b66A1wTNGG7xhm4Aail8SXMx5xpvbuqPVm5h6qbbC6FWDSYObIWJjbl', '2025-09-14 19:07:43', '2026-03-08 22:47:18'),
(6, 1, 'Naseem Abbas', '7747789@gmail.com', '$2y$12$u1j0/LESIZL/kCFGiNj1RuWmR7PGyMHFckInoVJ9wSqcCCCHnWdUG', 'cashier', NULL, 'M5n3V13tctWsGzKvmeP3HMNlJ8hbsGZDHUKciEPZ4dyKkjpNtuQXIp737gCm', '2025-09-16 02:15:12', '2025-09-16 02:15:12'),
(7, 1, 'Ali Kumail', '7212469@gmail.com', '$2y$12$odTKHGvMJcn0G3Hbyt1Y0.F9MGNPdFB9guDh75X/OZr1u/8tR2lfa', 'cashier', NULL, 'XlvJV4E2SzmYiLvmu6W4BmBUdb0VMmp1tXPFUmeg3ME3c9JbuQkXoc3KR02B', '2025-09-16 07:00:57', '2025-09-16 07:01:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_date_session` (`employee_id`,`date`,`session`),
  ADD KEY `attendances_branch_id_foreign` (`branch_id`);

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
-- Indexes for table `branch_product_stock`
--
ALTER TABLE `branch_product_stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_product_stock_branch_id_product_id_unique` (`branch_id`,`product_id`),
  ADD KEY `branch_product_stock_product_id_foreign` (`product_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `credit_ledgers`
--
ALTER TABLE `credit_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `credit_ledgers_ledger_number_unique` (`ledger_number`),
  ADD KEY `credit_ledgers_customer_id_index` (`customer_id`),
  ADD KEY `credit_ledgers_status_index` (`status`);

--
-- Indexes for table `credit_transactions`
--
ALTER TABLE `credit_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `credit_transactions_transaction_number_unique` (`transaction_number`),
  ADD KEY `credit_transactions_payment_id_foreign` (`payment_id`),
  ADD KEY `credit_transactions_created_by_foreign` (`created_by`),
  ADD KEY `credit_transactions_customer_id_index` (`customer_id`),
  ADD KEY `credit_transactions_credit_ledger_id_index` (`credit_ledger_id`),
  ADD KEY `credit_transactions_order_id_index` (`order_id`),
  ADD KEY `credit_transactions_transaction_type_index` (`transaction_type`),
  ADD KEY `credit_transactions_payment_status_index` (`payment_status`),
  ADD KEY `credit_transactions_due_date_index` (`due_date`),
  ADD KEY `credit_transactions_transaction_date_index` (`transaction_date`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_branch_id_foreign` (`branch_id`);

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
  ADD KEY `employees_user_id_foreign` (`user_id`),
  ADD KEY `employees_branch_id_foreign` (`branch_id`);

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
  ADD KEY `inventory_logs_user_id_foreign` (`user_id`),
  ADD KEY `inventory_logs_branch_id_foreign` (`branch_id`);

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
-- Indexes for table `ledger_accounts`
--
ALTER TABLE `ledger_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ledger_accounts_account_code_unique` (`account_code`),
  ADD KEY `ledger_accounts_created_by_foreign` (`created_by`);

--
-- Indexes for table `ledger_account_entries`
--
ALTER TABLE `ledger_account_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ledger_account_entries_entry_number_unique` (`entry_number`),
  ADD KEY `ledger_account_entries_created_by_foreign` (`created_by`),
  ADD KEY `ledger_account_entries_ledger_account_id_entry_date_index` (`ledger_account_id`,`entry_date`);

--
-- Indexes for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ledger_entries_entry_number_unique` (`entry_number`),
  ADD KEY `ledger_entries_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  ADD KEY `ledger_entries_user_id_foreign` (`user_id`),
  ADD KEY `ledger_entries_entry_date_account_type_index` (`entry_date`,`account_type`),
  ADD KEY `ledger_entries_transaction_type_index` (`transaction_type`),
  ADD KEY `ledger_entries_party_type_party_id_index` (`party_type`,`party_id`);

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
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `credit_ledger_id` (`credit_ledger_id`),
  ADD KEY `orders_branch_id_index` (`branch_id`);

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
  ADD KEY `payments_order_id_foreign` (`order_id`),
  ADD KEY `payments_customer_id_foreign` (`customer_id`),
  ADD KEY `payments_created_by_foreign` (`created_by`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payrolls_employee_id_foreign` (`employee_id`),
  ADD KEY `payrolls_branch_id_foreign` (`branch_id`);

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
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_branch_id_foreign` (`branch_id`);

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
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`),
  ADD KEY `purchases_branch_id_foreign` (`branch_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `suppliers_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_branch_id_foreign` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branch_product_stock`
--
ALTER TABLE `branch_product_stock`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `credit_ledgers`
--
ALTER TABLE `credit_ledgers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `credit_transactions`
--
ALTER TABLE `credit_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ecommerce_sync_logs`
--
ALTER TABLE `ecommerce_sync_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_accounts`
--
ALTER TABLE `ledger_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ledger_account_entries`
--
ALTER TABLE `ledger_account_entries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `login_histories`
--
ALTER TABLE `login_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD CONSTRAINT `attendance_sessions_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branch_product_stock`
--
ALTER TABLE `branch_product_stock`
  ADD CONSTRAINT `branch_product_stock_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branch_product_stock_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `credit_ledgers`
--
ALTER TABLE `credit_ledgers`
  ADD CONSTRAINT `credit_ledgers_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `credit_transactions`
--
ALTER TABLE `credit_transactions`
  ADD CONSTRAINT `credit_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `credit_transactions_credit_ledger_id_foreign` FOREIGN KEY (`credit_ledger_id`) REFERENCES `credit_ledgers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `credit_transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `credit_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `credit_transactions_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ecommerce_sync_logs`
--
ALTER TABLE `ecommerce_sync_logs`
  ADD CONSTRAINT `ecommerce_sync_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
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
  ADD CONSTRAINT `inventory_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_logs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_logs_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ledger_accounts`
--
ALTER TABLE `ledger_accounts`
  ADD CONSTRAINT `ledger_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ledger_account_entries`
--
ALTER TABLE `ledger_account_entries`
  ADD CONSTRAINT `ledger_account_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ledger_account_entries_ledger_account_id_foreign` FOREIGN KEY (`ledger_account_id`) REFERENCES `ledger_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  ADD CONSTRAINT `ledger_entries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
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
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD CONSTRAINT `payrolls_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
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
  ADD CONSTRAINT `purchases_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
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

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
