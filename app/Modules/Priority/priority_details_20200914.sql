-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2020 at 11:15 AM
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
-- Table structure for table `priority_details`
--

CREATE TABLE `priority_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `priority_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sibling` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Y/N',
  `majority_race_in_home_zone_school` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Y/N',
  `sort` int(11) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `priority_details`
--

INSERT INTO `priority_details` (`id`, `priority_id`, `description`, `sibling`, `majority_race_in_home_zone_school`, `sort`, `created_at`, `updated_at`) VALUES
(1, 93, 'a', 'N', 'N', 1, '2020-09-14 07:18:01', '2020-09-14 07:19:24'),
(2, 93, 'b', 'N', 'Y', 1, '2020-09-14 07:18:01', '2020-09-14 07:19:24'),
(3, 94, 'sdf', 'N', 'N', 1, '2020-09-14 07:27:35', '2020-09-14 07:27:35'),
(4, 94, 'sdf', 'N', 'Y', 1, '2020-09-14 07:27:35', '2020-09-14 07:27:35'),
(5, 95, 'sdfsdf', 'Y', 'Y', 1, '2020-09-14 07:28:03', '2020-09-14 07:28:03'),
(6, 95, 'sdgsd', 'N', 'N', 1, '2020-09-14 07:28:03', '2020-09-14 07:28:03'),
(7, 96, 'asdf', 'Y', 'N', 1, '2020-09-14 07:28:36', '2020-09-14 07:28:36'),
(8, 96, 'asfas', 'N', 'Y', 1, '2020-09-14 07:28:37', '2020-09-14 07:28:37'),
(9, 97, 'sdfgs', 'Y', 'N', 1, '2020-09-14 07:33:31', '2020-09-14 09:08:26'),
(10, 97, 'sdfsd', 'N', 'Y', 1, '2020-09-14 07:33:31', '2020-09-14 09:08:26'),
(11, 98, 'hjk', 'Y', 'Y', 1, '2020-09-14 09:09:05', '2020-09-14 09:11:25'),
(12, 98, 'sdf', 'Y', 'N', 1, '2020-09-14 09:09:05', '2020-09-14 09:11:25'),
(13, 99, '567', 'N', 'Y', 1, '2020-09-14 09:12:24', '2020-09-14 09:12:44'),
(14, 99, '567', 'N', 'N', 1, '2020-09-14 09:12:24', '2020-09-14 09:12:44');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `priority_details`
--
ALTER TABLE `priority_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
