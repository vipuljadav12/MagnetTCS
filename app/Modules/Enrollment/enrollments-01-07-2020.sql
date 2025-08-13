-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2020 at 01:38 PM
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
-- Database: `leanfrogmagnet`
--

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(10) UNSIGNED NOT NULL,
  `district_id` int(11) NOT NULL,
  `school_year` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmation_style` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_grades_by` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `begning_date` date NOT NULL,
  `ending_date` date NOT NULL,
  `perk_birthday_cut_off` date NOT NULL,
  `kindergarten_birthday_cut_off` date NOT NULL,
  `first_grade_birthday_cut_off` date NOT NULL,
  `status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Y/N/T',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `district_id`, `school_year`, `confirmation_style`, `import_grades_by`, `begning_date`, `ending_date`, `perk_birthday_cut_off`, `kindergarten_birthday_cut_off`, `first_grade_birthday_cut_off`, `status`, `created_at`, `updated_at`) VALUES
(3, 2, '2019-2020', 'new common style', 'SD', '2020-07-13', '2020-07-29', '2020-07-14', '2020-07-22', '2020-07-22', 'Y', '2020-07-01 01:31:47', '2020-07-01 03:19:54'),
(4, 3, '2019-2020', 'new common style', 'SD', '2020-07-13', '2020-07-29', '2020-07-14', '2020-07-22', '2020-07-22', 'Y', '2020-07-01 01:31:53', '2020-07-01 01:31:53'),
(5, 1, '2019-2020', 'confirmation style 4', 'SD', '2020-07-13', '2020-07-29', '2020-07-14', '2020-07-22', '2020-07-22', 'Y', '2020-07-01 01:32:01', '2020-07-01 04:44:01'),
(6, 1, '2020-2021', 'confirmation style 5', 'SD', '2020-07-15', '2020-07-20', '2020-07-31', '2020-09-28', '2020-09-24', 'N', '2020-07-01 03:23:28', '2020-07-01 04:44:14'),
(7, 1, '2018-2019', 'confirmation style 1', 'SD', '2020-07-08', '2020-07-07', '2020-07-21', '2020-07-20', '2020-07-22', 'T', '2020-07-01 04:15:08', '2020-07-01 04:15:34'),
(9, 1, '2016-2017', 'confirmation style 1', 'SD', '2020-07-01', '2020-07-02', '2020-07-03', '2020-07-04', '2020-07-05', 'Y', '2020-07-01 04:25:55', '2020-07-01 05:49:56'),
(10, 1, '2018-2019', 'confirmation style 3', 'SD', '2020-07-30', '2020-07-19', '2020-07-31', '2020-07-21', '2020-07-11', 'N', '2020-07-01 04:43:36', '2020-07-01 04:50:42'),
(11, 1, '2017-2018', 'confirmation style 2', 'SD', '2020-07-15', '2020-07-20', '2020-07-30', '2020-07-21', '2020-07-22', 'Y', '2020-07-01 04:47:27', '2020-07-01 04:47:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollments_district_id_foreign` (`district_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `district` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
