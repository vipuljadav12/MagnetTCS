-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2020 at 10:11 AM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.1.33

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
-- Table structure for table `district`
--

CREATE TABLE `district` (
  `id` int(10) NOT NULL,
  `district_slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zipcode` varchar(6) NOT NULL,
  `district_logo` text NOT NULL,
  `magnet_program_logo` text NOT NULL,
  `district_url` text DEFAULT NULL,
  `theme_color` varchar(7) NOT NULL,
  `magnet_point_contact` varchar(255) NOT NULL,
  `magnet_point_contact_title` varchar(255) NOT NULL,
  `magnet_point_contact_email` varchar(255) NOT NULL,
  `magnet_point_contact_phone` varchar(15) NOT NULL,
  `desegregation_compliance` enum('Yes','No') NOT NULL,
  `zone_requirements` enum('Yes','No') NOT NULL,
  `birthday_cutoff_requirement` enum('Yes','No') NOT NULL,
  `sis_connection` enum('iNow','Power schools') NOT NULL,
  `school_sis_contact` varchar(255) NOT NULL,
  `school_sis_contact_title` varchar(255) NOT NULL,
  `school_sis_contact_email` varchar(255) NOT NULL,
  `school_sis_contact_phone` varchar(255) NOT NULL,
  `zone_api` enum('Yes','No') NOT NULL,
  `billing_start_date` date NOT NULL DEFAULT current_timestamp(),
  `billing_end_date` date DEFAULT NULL,
  `notify_renewal_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` enum('Y','N','T') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `district`
--

INSERT INTO `district` (`id`, `district_slug`, `name`, `short_name`, `address`, `city`, `state`, `zipcode`, `district_logo`, `magnet_program_logo`, `district_url`, `theme_color`, `magnet_point_contact`, `magnet_point_contact_title`, `magnet_point_contact_email`, `magnet_point_contact_phone`, `desegregation_compliance`, `zone_requirements`, `birthday_cutoff_requirement`, `sis_connection`, `school_sis_contact`, `school_sis_contact_title`, `school_sis_contact_email`, `school_sis_contact_phone`, `zone_api`, `billing_start_date`, `billing_end_date`, `notify_renewal_date`, `created_at`, `updated_at`, `status`) VALUES
(7605, 'id-ipsum-aspernatur-', 'Hiroko Parker', 'Cade Wolf', '21', '10', '4', '55798', 'id-ipsum-aspernatur-_logo.png', 'id-ipsum-aspernatur-_magnet_program_logo.png', NULL, 'Ea veli', '17', '8', 'nive@mailinator.net', '+15227922043', 'Yes', 'Yes', 'Yes', 'Power schools', '24', '19', 'cexev@mailinator.net', '+14526684874', 'Yes', '1970-01-01', '1970-01-01', '2020-06-28', '2020-06-07 06:16:17', '2020-06-08 12:22:14', 'Y'),
(7606, 'pkk', 'pkk', 'Hillary Sawyer', '4', '11', '2', '69210', 'pkk_logo.png', 'pkk_magnet_program_logo.png', NULL, 'Sequi r', '16', '13', 'rygaqo@mailinator.net', '+16676853058', 'Yes', 'Yes', 'Yes', 'iNow', '6', '9', 'qaniki@mailinator.com', '+13869128979', 'Yes', '1970-01-01', '1970-01-01', '2020-06-15', '2020-06-08 03:38:30', '2020-06-08 03:40:44', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `eligibiility`
--

CREATE TABLE `eligibiility` (
  `id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `district_id` int(11) NOT NULL DEFAULT 1,
  `store_for` enum('DO','MS') DEFAULT NULL,
  `status` enum('Y','N','T') NOT NULL DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eligibiility`
--

INSERT INTO `eligibiility` (`id`, `template_id`, `name`, `type`, `district_id`, `store_for`, `status`, `created_at`, `updated_at`) VALUES
(22, 5, 'writing 1', NULL, 1, 'DO', 'Y', '2020-06-15 05:09:51', '2020-06-15 05:09:51'),
(23, 1, 'inter 1', NULL, 1, 'DO', 'Y', '2020-06-15 05:10:20', '2020-06-15 05:10:20'),
(24, 6, 'au 2`', NULL, 1, 'DO', 'Y', '2020-06-15 05:15:53', '2020-06-15 05:15:53'),
(25, 7, 'com1', NULL, 1, 'DO', 'Y', '2020-06-15 05:58:54', '2020-06-15 05:58:54'),
(26, 1, 'iiner 2', NULL, 1, 'DO', 'Y', '2020-06-15 15:51:33', '2020-06-15 15:51:33');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_content`
--

CREATE TABLE `eligibility_content` (
  `id` int(11) NOT NULL,
  `eligibility_id` int(11) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eligibility_content`
--

INSERT INTO `eligibility_content` (`id`, `eligibility_id`, `content`, `created_at`, `updated_at`) VALUES
(12, 12, '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"Y\",\"N\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"Y\"}', '2020-06-10 09:05:45', '2020-06-10 09:05:45'),
(13, 13, '{\"eligibility_type\":{\"type\":\"NR\",\"YN\":[null,null],\"NR\":[\"1\",\"2\",\"3\",\"4\"]},\"allow_spreadsheet\":\"Y\"}', '2020-06-10 10:59:10', '2020-06-10 10:59:10'),
(14, 14, '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"e\",\"en\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"Y\"}', '2020-06-10 11:00:14', '2020-06-10 11:00:14'),
(15, 15, '{\"eligibility_type\":{\"type\":\"NR\",\"YN\":[null,null],\"NR\":[\"sdfdd\",\"sdfsd\",\"sdf\",\"sdf\"]},\"allow_spreadsheet\":\"Y\"}', '2020-06-10 11:01:14', '2020-06-10 11:01:14'),
(16, 16, '{\"eligibility_type\":{\"type\":\"YN\",\"option\":[\"sdkjnf\",\"kjndfknd\",null,null,null,null]},\"allow_spreadsheet\":\"Y\"}', '2020-06-10 13:57:05', '2020-06-10 13:57:05'),
(17, 22, '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"p\",\"k\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 05:09:51', '2020-06-15 05:09:51'),
(18, 23, '{\"eligibility_type\":{\"type\":\"NR\",\"YN\":[null,null],\"NR\":[\"q\",\"q\",\"q\",\"q\"]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 05:10:20', '2020-06-15 05:10:20'),
(19, 24, '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"a\",\"w\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"N\"}', '2020-06-15 05:15:53', '2020-06-15 05:15:53'),
(20, 25, '{\"eligibility_type\":{\"type\":\"NR\",\"YN\":[null,null],\"NR\":[\"1\",\"2\",\"3\",\"4\"]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 05:58:54', '2020-06-15 05:58:54'),
(21, 26, '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"Y\",\"N\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 15:51:34', '2020-06-15 15:51:34');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_template`
--

CREATE TABLE `eligibility_template` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `type` varchar(2000) NOT NULL DEFAULT '0',
  `content_html` text NOT NULL,
  `district_id` int(11) NOT NULL DEFAULT 1,
  `status` enum('Y','N','T') DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eligibility_template`
--

INSERT INTO `eligibility_template` (`id`, `name`, `type`, `content_html`, `district_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Interview Score', '0', 'interview', 1, 'Y', NULL, NULL),
(2, 'Grades', '0', '', 2, 'Y', NULL, NULL),
(3, 'Academic Grade Calculation', '0', '', 1, 'Y', NULL, NULL),
(4, 'Recommendation Form', '0', '', 1, 'Y', NULL, NULL),
(5, 'Writing Prompt', '0', 'writing_prompt', 1, 'Y', NULL, NULL),
(6, 'Audition', '0', 'audition', 1, 'Y', NULL, NULL),
(7, 'Committee Score', '0', 'committee_score', 1, 'Y', NULL, NULL),
(8, 'Conduct Disciplinary Info', '0', '', 1, 'Y', NULL, NULL),
(9, 'Special Ed Indicators', '0', '', 1, 'Y', NULL, NULL),
(10, 'Standardized Testing', '0', '', 1, 'Y', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `id` int(10) NOT NULL,
  `district_id` int(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `applicant_filter1` varchar(255) DEFAULT NULL,
  `applicant_filter2` varchar(255) DEFAULT NULL,
  `applicant_filter3` varchar(255) DEFAULT NULL,
  `grade_lavel` text DEFAULT NULL,
  `parent_submission_form` varchar(255) DEFAULT NULL,
  `priority` text DEFAULT NULL,
  `committee_score` varchar(255) DEFAULT NULL,
  `audition_score` varchar(255) DEFAULT NULL,
  `rating_priority` varchar(255) DEFAULT NULL,
  `lottery_number` varchar(255) DEFAULT NULL,
  `combine_score` varchar(255) DEFAULT NULL,
  `final_score` varchar(255) DEFAULT NULL,
  `selection_method` varchar(255) DEFAULT NULL,
  `selection_by` varchar(255) DEFAULT NULL,
  `seat_availability_enter_by` varchar(255) DEFAULT NULL,
  `basic_method_only` enum('Y','N') NOT NULL,
  `combined_scoring` enum('Y','N') NOT NULL,
  `combined_eligibility` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` enum('Y','N','T') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`id`, `district_id`, `name`, `applicant_filter1`, `applicant_filter2`, `applicant_filter3`, `grade_lavel`, `parent_submission_form`, `priority`, `committee_score`, `audition_score`, `rating_priority`, `lottery_number`, `combine_score`, `final_score`, `selection_method`, `selection_by`, `seat_availability_enter_by`, `basic_method_only`, `combined_scoring`, `combined_eligibility`, `created_at`, `updated_at`, `status`) VALUES
(111, NULL, 'prog', 'prog-1', 'prog-2', 'prog-3', 'PreK,K,7,8', 'MCPSS Magnet Form', '1,2', '1', '5', '2', '3', '4', '6', 'Home Zone Placement', 'Application Filter 1', 'Calculation', 'Y', 'Y', 'Sum Scores', '2020-06-16 08:06:16', '2020-06-16 08:06:16', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `program_eligibility`
--

CREATE TABLE `program_eligibility` (
  `id` int(10) NOT NULL,
  `program_id` int(10) DEFAULT NULL,
  `eligibility_type` varchar(255) DEFAULT NULL,
  `determination_method` varchar(255) DEFAULT NULL,
  `eligibility_define` varchar(255) DEFAULT NULL,
  `assigned_eigibility_name` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `grade_lavel_or_recommendation_by` text DEFAULT NULL,
  `status` enum('Y','N','T') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `program_eligibility`
--

INSERT INTO `program_eligibility` (`id`, `program_id`, `eligibility_type`, `determination_method`, `eligibility_define`, `assigned_eigibility_name`, `weight`, `grade_lavel_or_recommendation_by`, `status`, `created_at`, `updated_at`) VALUES
(306, 111, '5', 'Basic', 'right', 'writing 1', '20', 'K', 'Y', '2020-06-16 08:05:17', '2020-06-16 08:07:16'),
(307, 111, '6', 'Basic', 'right', 'au 2`', '30', '7', 'Y', '2020-06-16 08:05:17', '2020-06-16 08:07:16'),
(308, 111, '7', 'Combined', 'right', 'com1', '40', '8', 'Y', '2020-06-16 08:05:17', '2020-06-16 08:07:16'),
(309, 111, '1', NULL, 'close', NULL, NULL, '', 'N', '2020-06-16 08:07:16', '2020-06-16 08:07:16');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2020-06-02 00:00:00', '2020-06-02 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE `school` (
  `id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `address` text NOT NULL,
  `program` text NOT NULL,
  `open_enrollment` varchar(10) NOT NULL,
  `name_as_per_sis` varchar(50) DEFAULT NULL,
  `status` enum('Y','N','T') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`id`, `district_id`, `name`, `grade`, `address`, `program`, `open_enrollment`, `name_as_per_sis`, `status`, `created_at`, `updated_at`) VALUES
(5, 9, 'test', '100', '100', 'Magnet - Middle', '2021-2022', NULL, 'Y', '2020-05-29 08:04:30', '2020-06-01 05:40:58'),
(6, 7557, 'test', '100', '100', 'Magnet - Middle', '2021-2022', NULL, 'Y', '2020-05-29 08:05:27', '2020-06-01 05:37:43'),
(7, 6, 'testing', '100', '100', 'Magnet - Middle', '2021-2022', NULL, 'Y', '2020-05-29 08:06:25', '2020-05-31 23:06:34'),
(8, 8, 'high', '12', '12', 'Magnet - Elementary', '2021-2022', NULL, 'Y', '2020-05-31 22:26:26', '2020-05-31 22:27:49'),
(9, 7555, 'as', '1', '1', 'Magnet - Middle', '2020-2021', NULL, 'Y', '2020-06-01 02:16:49', '2020-06-01 02:19:57'),
(10, 7545, 'miulti national high school', '12', '12', 'Magnet - Middle', '2020-2021', NULL, 'Y', '2020-06-01 03:03:04', '2020-06-01 05:37:27'),
(11, 7555, 'name', '21', '131313', 'Magnet - Elementary', '2020-2021', NULL, 'Y', '2020-06-01 04:51:29', '2020-06-01 04:58:55'),
(12, 7570, 'hello', '0000000000', '9999999999', 'Magnet - Elementary', '2021-2022', NULL, 'Y', '2020-06-01 05:36:45', '2020-06-01 05:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Y','N','T') COLLATE utf8mb4_unicode_ci DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `first_name`, `last_name`, `email`, `profile`, `password`, `remember_token`, `district_id`, `status`, `created_at`, `updated_at`) VALUES
(20, 1, 'admin', 'pk', 'pradip', 'admin@gmail.com', '20_profile.png', '$2y$10$8YixHIVsJHbQG74wxWbxeeW5eZas0S5QmPInO/05tcLlseKFMwmju', 'HImziLWmqucVa0VfXUuSOtVU8hNgLdcQEsje7F691PMv0ctwfQihT6wmADyD', '5', 'Y', '2020-06-21 18:30:00', '2020-06-10 06:32:15'),
(21, 1, 'pkpk', 'pk', 'pkk', 'admin@admin.com', NULL, '$2y$10$mmyXPnCE09wxjKxu8LnanOuxRDBack2naCdPW54fL8JvxukKRD65y', NULL, '1', 'Y', '2020-06-01 07:56:25', '2020-06-05 06:47:19'),
(27, 1, 'YokoMurphy', 'Yokomn', 'Murphy', 'puxe@mailinator.net', NULL, '$2y$10$cZ4UfGFugNpS8KKXemxR3u2vDu7Yotfssn9VEY4vtkSomnY0JXkjK', NULL, '1', 'Y', '2020-06-05 04:12:33', '2020-06-05 04:29:15'),
(28, 1, 'MarvinKasimir', 'Marvin', 'Kasimir', 'fudehuvu@mailinator.com', NULL, '$2y$10$0e0HzLtZU7v91l/uNVLz0Obdz2B7KLHZU/jtMYYI2jPH7GoMY9cp.', NULL, '1', 'T', '2020-06-05 04:23:26', '2020-06-08 06:48:09'),
(29, 1, 'DaltonNoel', 'Dalton', 'Noel', 'goteh@mailinator.com', NULL, '$2y$10$EOv3NLWctW7ZyNIpLG1oAu0sXLp8YdQqLblxyEI9RLDfLTh33ZIBi', NULL, '1', 'Y', '2020-06-05 04:24:00', '2020-06-08 06:50:02'),
(30, 1, 'EvelynJustine', 'Evelyn', 'Justine', 'lasucy@mailinator.net', NULL, '$2y$10$ma.YZVI.74dbm1QtpVkw2uVd6x0tUH5.ARvxRVaFCcJKXKd4AT4wm', NULL, '1', 'T', '2020-06-05 04:24:21', '2020-06-07 22:10:52'),
(31, 1, 'RanaAhmed', 'Rana', 'Ahmed', 'vebuxo@mailinator.net', NULL, '$2y$10$s4WVYga9wc2TeQYAot/vp.c7kuhvI8CwsYywS6WKKmtI1.NZ4qUwO', NULL, '1', 'Y', '2020-06-05 06:13:24', '2020-06-05 06:13:24'),
(32, 1, 'CiaranpkOmar', 'Ciaranpkpppppppppppppppppppppppp', 'Omar', 'xynojaq@mailinator.com', NULL, '$2y$10$IEe9rLq5bg/XrL/47tqkpucAEiXl77v7imst4Yp6IDF3mwXLxKuxG', NULL, '1', 'Y', '2020-06-05 06:13:48', '2020-06-08 06:48:15'),
(33, 1, 'RhodaDanielle', 'pk', 'Danielle', 'cyqu@mailinator.net', NULL, '$2y$10$dAhNpPNA5vjEStlzOEZOfO0JZOUex8RaV5.RObT21woMW7qPWx/..', NULL, '1', 'T', '2020-06-05 07:00:08', '2020-06-05 07:00:41'),
(34, 1, 'IolaMargaret', 'p', 'k', 'sudoduvis@mailinator.net', NULL, '$2y$10$QQ4aoIFjDcieZtaCN0cgsuzQyxAHchtG29WlcWrTcqQFrWW.68Cqy', NULL, '1', 'T', '2020-06-07 22:05:07', '2020-06-07 22:05:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `district_url` (`district_url`) USING HASH;

--
-- Indexes for table `eligibiility`
--
ALTER TABLE `eligibiility`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eligibility_content`
--
ALTER TABLE `eligibility_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eligibility_template`
--
ALTER TABLE `eligibility_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_eligibility`
--
ALTER TABLE `program_eligibility`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7607;

--
-- AUTO_INCREMENT for table `eligibiility`
--
ALTER TABLE `eligibiility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `eligibility_content`
--
ALTER TABLE `eligibility_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `eligibility_template`
--
ALTER TABLE `eligibility_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `program_eligibility`
--
ALTER TABLE `program_eligibility`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=310;

--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
