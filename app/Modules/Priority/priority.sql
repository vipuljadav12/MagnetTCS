-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2020 at 01:14 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `magnet`
--

-- --------------------------------------------------------

--
-- Table structure for table `priorities`
--

CREATE TABLE `priorities` (
  `id` int(10) UNSIGNED NOT NULL,
  `district_id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Y/N',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `priorities`
--

INSERT INTO `priorities` (`id`, `district_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(44, 1, 'temp 2', 'N', '2020-06-12 03:09:57', '2020-06-12 03:10:41'),
(47, 1, 'temp 3', 'N', '2020-06-12 04:30:53', '2020-06-15 05:28:16'),
(59, 1, 'temp 1', 'Y', '2020-06-15 05:18:33', '2020-06-15 05:42:27'),
(60, 1, 'temp 4 four', 'Y', '2020-06-15 05:22:07', '2020-06-15 05:33:31');

-- --------------------------------------------------------

--
-- Table structure for table `priority_details`
--

CREATE TABLE `priority_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `priority_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sibling` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Y/N',
  `majority_race_in_home_zone_school` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Y/N',
  `current_enrollment_at_another_magnet_school` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Y/N',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `priority_details`
--

INSERT INTO `priority_details` (`id`, `priority_id`, `description`, `sibling`, `majority_race_in_home_zone_school`, `current_enrollment_at_another_magnet_school`, `created_at`, `updated_at`) VALUES
(43, 44, '22', 'Y', 'N', 'N', '2020-06-12 03:09:58', '2020-06-12 03:10:41'),
(44, 44, '33', 'Y', 'N', 'Y', '2020-06-12 03:09:58', '2020-06-12 03:10:41'),
(49, 47, 'p3', 'N', 'Y', 'N', '2020-06-12 04:30:53', '2020-06-12 04:57:42'),
(50, 47, 'p4', 'Y', 'Y', 'Y', '2020-06-12 04:30:53', '2020-06-12 04:57:42'),
(73, 59, 'asdaa', 'N', 'N', 'N', '2020-06-15 05:18:33', '2020-06-15 05:42:27'),
(74, 59, 'asdaa', 'N', 'Y', 'N', '2020-06-15 05:18:33', '2020-06-15 05:42:27'),
(75, 60, 'sdf 4', 'Y', 'Y', 'Y', '2020-06-15 05:22:08', '2020-06-15 05:33:31'),
(76, 60, 'gsd 4', 'Y', 'N', 'Y', '2020-06-15 05:22:08', '2020-06-15 05:33:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `priorities`
--
ALTER TABLE `priorities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `priority_details`
--
ALTER TABLE `priority_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `priority_details_priority_id_foreign` (`priority_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `priorities`
--
ALTER TABLE `priorities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `priority_details`
--
ALTER TABLE `priority_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `priority_details`
--
ALTER TABLE `priority_details`
  ADD CONSTRAINT `priority_details_priority_id_foreign` FOREIGN KEY (`priority_id`) REFERENCES `priorities` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
