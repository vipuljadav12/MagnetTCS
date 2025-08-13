/*
MySQL Data Transfer
Source Host: localhost
Source Database: leanfrogmagnet
Target Host: localhost
Target Database: leanfrogmagnet
Date: 6/27/2020 6:32:50 PM
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for district
-- ----------------------------
DROP TABLE IF EXISTS `district`;
CREATE TABLE `district` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `district_slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `address` text,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zipcode` varchar(6) DEFAULT NULL,
  `district_logo` text,
  `magnet_program_logo` text,
  `district_url` text,
  `theme_color` varchar(7) DEFAULT NULL,
  `magnet_point_contact` varchar(255) DEFAULT NULL,
  `magnet_point_contact_title` varchar(255) DEFAULT NULL,
  `magnet_point_contact_email` varchar(255) DEFAULT NULL,
  `magnet_point_contact_phone` varchar(15) DEFAULT NULL,
  `desegregation_compliance` enum('Yes','No') DEFAULT NULL,
  `zone_requirements` enum('Yes','No') DEFAULT NULL,
  `birthday_cutoff_requirement` enum('Yes','No') DEFAULT NULL,
  `sis_connection` enum('iNow','Power schools') DEFAULT NULL,
  `school_sis_contact` varchar(255) DEFAULT NULL,
  `school_sis_contact_title` varchar(255) DEFAULT NULL,
  `school_sis_contact_email` varchar(255) DEFAULT NULL,
  `school_sis_contact_phone` varchar(255) DEFAULT NULL,
  `zone_api` enum('Yes','No') DEFAULT NULL,
  `internal_zone_api_url` varchar(255) DEFAULT NULL,
  `internal_zone_point_contact` varchar(255) DEFAULT NULL,
  `internal_zone_point_title` varchar(255) DEFAULT NULL,
  `internal_zone_point_email` varchar(255) DEFAULT NULL,
  `internal_zone_point_phone` varchar(255) DEFAULT NULL,
  `external_organization_name` varchar(255) DEFAULT NULL,
  `external_organization_url` varchar(255) DEFAULT NULL,
  `external_organization_point_contact` varchar(255) DEFAULT NULL,
  `external_organization_point_title` varchar(255) DEFAULT NULL,
  `external_organization_point_email` varchar(255) DEFAULT NULL,
  `external_organization_point_phone` varchar(255) DEFAULT NULL,
  `billing_start_date` date DEFAULT NULL,
  `billing_end_date` date DEFAULT NULL,
  `notify_renewal_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` enum('Y','N','T') DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for district_copy
-- ----------------------------
DROP TABLE IF EXISTS `district_copy`;
CREATE TABLE `district_copy` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `district_slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `address` text,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zipcode` varchar(6) DEFAULT NULL,
  `district_logo` text,
  `magnet_program_logo` text,
  `district_url` text,
  `theme_color` varchar(7) DEFAULT NULL,
  `magnet_point_contact` varchar(255) DEFAULT NULL,
  `magnet_point_contact_title` varchar(255) DEFAULT NULL,
  `magnet_point_contact_email` varchar(255) DEFAULT NULL,
  `magnet_point_contact_phone` varchar(15) DEFAULT NULL,
  `desegregation_compliance` enum('Yes','No') DEFAULT NULL,
  `zone_requirements` enum('Yes','No') DEFAULT NULL,
  `birthday_cutoff_requirement` enum('Yes','No') DEFAULT NULL,
  `sis_connection` enum('iNow','Power schools') DEFAULT NULL,
  `school_sis_contact` varchar(255) DEFAULT NULL,
  `school_sis_contact_title` varchar(255) DEFAULT NULL,
  `school_sis_contact_email` varchar(255) DEFAULT NULL,
  `school_sis_contact_phone` varchar(255) DEFAULT NULL,
  `zone_api` enum('Yes','No') DEFAULT NULL,
  `billing_start_date` date DEFAULT NULL,
  `billing_end_date` date DEFAULT NULL,
  `notify_renewal_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` enum('Y','N','T') DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for eligibiility
-- ----------------------------
DROP TABLE IF EXISTS `eligibiility`;
CREATE TABLE `eligibiility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `district_id` int(11) NOT NULL DEFAULT '1',
  `store_for` enum('DO','MS') DEFAULT NULL,
  `status` enum('Y','N','T') NOT NULL DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for eligibility_content
-- ----------------------------
DROP TABLE IF EXISTS `eligibility_content`;
CREATE TABLE `eligibility_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eligibility_id` int(11) DEFAULT NULL,
  `content` longtext,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for eligibility_template
-- ----------------------------
DROP TABLE IF EXISTS `eligibility_template`;
CREATE TABLE `eligibility_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `type` varchar(2000) NOT NULL DEFAULT '0',
  `content_html` text NOT NULL,
  `district_id` int(11) NOT NULL DEFAULT '1',
  `status` enum('Y','N','T') DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for form
-- ----------------------------
DROP TABLE IF EXISTS `form`;
CREATE TABLE `form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` text,
  `description` text,
  `thank_you_url` text,
  `thank_you_msg` text,
  `to_mail` varchar(255) DEFAULT NULL,
  `show_logo` enum('y','n') NOT NULL,
  `captcha` enum('y','n') NOT NULL,
  `form_source_code` longtext,
  `status` enum('y','n','t') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for grade
-- ----------------------------
DROP TABLE IF EXISTS `grade`;
CREATE TABLE `grade` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for migrations_copy
-- ----------------------------
DROP TABLE IF EXISTS `migrations_copy`;
CREATE TABLE `migrations_copy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for priorities
-- ----------------------------
DROP TABLE IF EXISTS `priorities`;
CREATE TABLE `priorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `district_id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Y/N',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for priority_details
-- ----------------------------
DROP TABLE IF EXISTS `priority_details`;
CREATE TABLE `priority_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priority_id` int(10) unsigned NOT NULL,
  `description` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sibling` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Y/N',
  `majority_race_in_home_zone_school` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Y/N',
  `current_enrollment_at_another_magnet_school` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'Y/N',
  `sort` int(11) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `priority_details_priority_id_foreign` (`priority_id`),
  CONSTRAINT `priority_details_priority_id_foreign` FOREIGN KEY (`priority_id`) REFERENCES `priorities` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for program
-- ----------------------------
DROP TABLE IF EXISTS `program`;
CREATE TABLE `program` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `district_id` int(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `applicant_filter1` varchar(255) DEFAULT NULL,
  `applicant_filter2` varchar(255) DEFAULT NULL,
  `applicant_filter3` varchar(255) DEFAULT NULL,
  `grade_lavel` text,
  `parent_submission_form` varchar(255) DEFAULT NULL,
  `priority` text,
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
  `status` enum('Y','N','T') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for program_eligibility
-- ----------------------------
DROP TABLE IF EXISTS `program_eligibility`;
CREATE TABLE `program_eligibility` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `program_id` int(10) DEFAULT NULL,
  `eligibility_type` varchar(255) DEFAULT NULL,
  `determination_method` varchar(255) DEFAULT NULL,
  `eligibility_define` varchar(255) DEFAULT NULL,
  `assigned_eigibility_name` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `grade_lavel_or_recommendation_by` text,
  `status` enum('Y','N','T') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for role_copy
-- ----------------------------
DROP TABLE IF EXISTS `role_copy`;
CREATE TABLE `role_copy` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for school
-- ----------------------------
DROP TABLE IF EXISTS `school`;
CREATE TABLE `school` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `district_id` int(10) NOT NULL,
  `grade_id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `magnet` varchar(5) DEFAULT NULL,
  `zoning_api_name` varchar(255) DEFAULT NULL,
  `sis_name` varchar(255) DEFAULT NULL,
  `status` enum('Y','N','T') DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for stores
-- ----------------------------
DROP TABLE IF EXISTS `stores`;
CREATE TABLE `stores` (
  `id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for stores_copy
-- ----------------------------
DROP TABLE IF EXISTS `stores_copy`;
CREATE TABLE `stores_copy` (
  `id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tenants_copy
-- ----------------------------
DROP TABLE IF EXISTS `tenants_copy`;
CREATE TABLE `tenants_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `subdomain` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Y','N','T') COLLATE utf8mb4_unicode_ci DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for users_copy
-- ----------------------------
DROP TABLE IF EXISTS `users_copy`;
CREATE TABLE `users_copy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Y','N','T') COLLATE utf8mb4_unicode_ci DEFAULT 'Y',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `district` VALUES ('1', 'huntsville-city-school', 'Huntsville City school', 'HCS', '1217  Deer Ridge Drive', 'Newark', 'New Jersey', '07102', 'huntsville-city-school_logo.png', 'huntsville-city-school_magnet_program_logo.png', null, '#8da8fe', 'Nancy Brown', 'Mrs', 'nancybrownus8@gmail.com', '1234567890', 'Yes', 'Yes', 'Yes', 'iNow', 'Sis Nancy.', 'Mrs.', 'nancybrownus8@sis.com', '1234567880', 'Yes', 'www.interlaldistrict.com', 'Tom', 'Mr.', 'internal@edf.com', '1234567890', 'External Contact', 'www.external.com', 'Dick.', 'Mr', 'external@edf.com', '12345678900', '2020-06-30', '2020-08-20', '2020-06-24', '2020-06-10 07:20:19', '2020-06-17 13:55:52', 'Y');
INSERT INTO `district` VALUES ('2', 'tuscaloosa-city-school', 'Tuscaloosa City School', 'TCS', '356 Lincoln Street', 'Waltham', 'MA', '02451', 'tuscaloosa-city-school_logo.png', 'tuscaloosa-city-school_magnet_program_logo.png', null, '#d43f3f', 'Mr Principal', 'Mr', 'principal@tcs.com', '9876543210', 'No', 'No', 'No', 'iNow', 'Tom', 'Mr', 'tom@sis.com', '1234567890', 'No', null, null, null, null, null, null, null, null, null, null, null, '2020-06-10', '2020-07-10', '2020-06-17', '2020-06-10 14:21:44', '2020-06-10 14:21:44', 'Y');
INSERT INTO `district` VALUES ('3', 'mcps', 'Mobile County Public Schools', 'MCPS', 'Lincon Street', 'AL', 'AL', '02451', 'mcps_logo.png', 'mcps_magnet_program_logo.png', null, '#f1c96d', 'Mr. James Brown', 'PM', 'james@gmail.com', '7816007337', 'No', 'No', 'No', 'Power schools', 'Mr Shanley', 'CEO', 'shanley@gmail.com', '7816007338', 'Yes', 'www.mcpsapi.com', 'Mr. Brown', 'VP', 'brown@gmail.com', '7816007339', 'Acme Inc', 'www.acme.com', 'Mr Byron', 'CMO', 'byron@gmail.com', '781600740', '2020-06-30', '2020-07-01', '2020-07-13', '2020-06-15 17:32:31', '2020-06-15 17:32:31', 'Y');
INSERT INTO `district_copy` VALUES ('1', 'huntsville-city-school', 'Huntsville City school', 'HSC', '1217  Deer Ridge Drive', 'Newark', 'New Jersey', '07102', 'huntsville-city-school_logo.png', 'huntsville-city-school_magnet_program_logo.png', 'huntsville-city-school', '#281883', 'HSC-contact', 'HSC -Title', 'pepuqikyxe@mailinator.com', '4585850124', 'Yes', 'Yes', 'Yes', 'iNow', 'SIS-HSC', 'HSC-SIS-TITLE', 'cofusyryzy@mailinator.com', '3871783978', 'Yes', '2020-06-03', '2020-06-24', '2020-06-10', '2020-06-03 12:16:52', '2020-06-03 12:16:52', 'Y');
INSERT INTO `district_copy` VALUES ('2', 'tuscaloosa-city-school', 'Tuscaloosa City School', 'TCS', '1217  Deer Ridge Drive', 'Newark', 'New Jersey', '07102', 'tuscaloosa-city-school_logo.png', '', 'tuscaloosa-city-school', '#281883', '', '', '', '', 'Yes', 'Yes', 'Yes', 'iNow', '', '', '', '', 'Yes', null, null, null, null, null, 'Y');
INSERT INTO `district_copy` VALUES ('3', 'mobile-county-public-school', 'Mobile County Public School', 'MCPS', '3670  Java Lane', 'Augusta', 'South Carolina', '30902', 'mobile-county-public-school_logo.png', 'mobile-county-public-school_magnet_program_logo.png', 'mobile-county-public-school', '#6ac10e', 'MCPS-Contact', 'MCPS-Title', 'mcps_contact@gmail.com', '7878987898', 'Yes', 'Yes', 'Yes', 'Power schools', 'SIS-MSPC-Contact', 'SIS-MSPC-Title', 'MCPS@gmail.com', '8585812798', 'Yes', '2020-06-03', '2020-06-18', '2020-06-11', '2020-06-04 07:26:23', '2020-06-04 07:26:23', 'Y');
INSERT INTO `district_copy` VALUES ('4', 'dothan-public-school', 'Dothan Public School', 'DPS', '356 Lincoln Street', 'Waltham', 'MA', '02451', 'dothan-public-school_logo.png', 'dothan-public-school_magnet_program_logo.png', 'dothan-public-school', '#c7c445', 'Mr Principal', 'Mr', 'principal@dothanschool.com', '1234567890', 'Yes', 'Yes', 'Yes', 'iNow', null, null, null, null, 'Yes', '2020-06-17', '2020-07-17', '2020-06-16', '2020-06-09 11:29:03', '2020-06-09 11:29:03', 'Y');
INSERT INTO `eligibiility` VALUES ('1', '1', 'Interview Score 1', null, '1', 'MS', 'Y', '2020-06-10 07:41:26', '2020-06-10 12:53:24');
INSERT INTO `eligibiility` VALUES ('2', '5', 'Writing Prompt 1', null, '2', 'MS', 'Y', '2020-06-10 07:42:57', '2020-06-23 07:44:08');
INSERT INTO `eligibiility` VALUES ('3', '7', 'CS - 1', null, '1', 'DO', 'Y', '2020-06-10 12:22:07', '2020-06-10 12:22:07');
INSERT INTO `eligibiility` VALUES ('4', '1', 'Interview Option - HCS', null, '1', 'MS', 'Y', '2020-06-10 13:54:46', '2020-06-10 13:54:46');
INSERT INTO `eligibiility` VALUES ('5', '1', 'Interview score HSC', null, '1', 'MS', 'Y', '2020-06-15 08:35:03', '2020-06-15 08:35:03');
INSERT INTO `eligibiility` VALUES ('6', '0', 'Recommednation Form Edit', null, '1', 'DO', 'Y', '2020-06-15 09:56:36', '2020-06-24 09:23:37');
INSERT INTO `eligibiility` VALUES ('7', '1', 'IS - 1', null, '3', 'MS', 'Y', '2020-06-15 17:36:19', '2020-06-15 17:36:19');
INSERT INTO `eligibiility` VALUES ('8', '1', 'IS - 2', null, '3', 'MS', 'Y', '2020-06-15 17:37:31', '2020-06-15 17:37:31');
INSERT INTO `eligibiility` VALUES ('9', '6', 'AU - 1', null, '3', 'MS', 'Y', '2020-06-15 17:38:23', '2020-06-15 17:38:23');
INSERT INTO `eligibiility` VALUES ('10', '6', 'AU - 2', null, '3', 'DO', 'Y', '2020-06-15 17:39:12', '2020-06-15 17:39:12');
INSERT INTO `eligibiility` VALUES ('11', '10', 'ST-1', null, '0', 'MS', 'Y', '2020-06-19 10:30:21', '2020-06-23 07:52:17');
INSERT INTO `eligibiility` VALUES ('12', '8', 'CDI - 1 edited', null, '0', 'MS', 'Y', '2020-06-19 13:15:30', '2020-06-23 10:53:01');
INSERT INTO `eligibiility` VALUES ('13', '8', 'CDDI 2', null, '0', null, 'Y', '2020-06-22 04:42:00', '2020-06-22 04:42:00');
INSERT INTO `eligibiility` VALUES ('14', '2', 'Academic Grade Type 1', null, '1', 'DO', 'Y', '2020-06-22 09:12:40', '2020-06-23 15:37:07');
INSERT INTO `eligibility_content` VALUES ('3', '3', '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"Eligible\",\"Not Eligible\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"Y\"}', '2020-06-10 12:22:07', '2020-06-10 12:22:07');
INSERT INTO `eligibility_content` VALUES ('7', '1', '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"Approved\",\"Not Approved\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"N\"}', '2020-06-10 12:53:24', '2020-06-10 12:53:24');
INSERT INTO `eligibility_content` VALUES ('11', '4', '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"School\",\"College\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"N\"}', '2020-06-10 13:54:46', '2020-06-10 13:54:46');
INSERT INTO `eligibility_content` VALUES ('12', '5', '{\"eligibility_type\":{\"type\":\"NR\",\"YN\":[null,null],\"NR\":[\"A\",\"B\",\"C\",\"D\"]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 08:35:03', '2020-06-15 08:35:03');
INSERT INTO `eligibility_content` VALUES ('15', '7', '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"Eligible\",\"Not Eligible\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 17:36:19', '2020-06-15 17:36:19');
INSERT INTO `eligibility_content` VALUES ('16', '8', '{\"eligibility_type\":{\"type\":\"NR\",\"YN\":[null,null],\"NR\":[\"100-90\",\"89-80\",\"79-70\",\"69-60\"]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 17:37:31', '2020-06-15 17:37:31');
INSERT INTO `eligibility_content` VALUES ('17', '9', '{\"eligibility_type\":{\"type\":\"YN\",\"YN\":[\"Audition Pass\",\"Audition Fail\"],\"NR\":[null,null,null,null]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 17:38:23', '2020-06-15 17:38:23');
INSERT INTO `eligibility_content` VALUES ('18', '10', '{\"eligibility_type\":{\"type\":\"NR\",\"YN\":[null,null],\"NR\":[\"Interview Appear\",\"Interview Not Appear\",\"Fail in Interview\",\"Pass in Interview\"]},\"allow_spreadsheet\":\"Y\"}', '2020-06-15 17:39:12', '2020-06-15 17:39:12');
INSERT INTO `eligibility_content` VALUES ('28', '13', '{\"scoring\":{\"type\":null,\"method\":null,\"YN\":[null,null],\"NR\":[null,null,null,null]}}', '2020-06-22 04:42:00', '2020-06-22 04:42:00');
INSERT INTO `eligibility_content` VALUES ('33', '2', '{\"eligibility_type\":{\"type\":\"NR\",\"YN\":[\"Yes\",\"No\"],\"NR\":[\"9\",\"8\",\"7\",\"6\",\"5\"]},\"allow_spreadsheet\":\"Y\"}', '2020-06-23 07:44:08', '2020-06-23 07:44:08');
INSERT INTO `eligibility_content` VALUES ('34', '11', '{\"scoring\":{\"type\":\"SC\",\"method\":\"YN\",\"YN\":[\"A\",\"B\"],\"NR\":[\"A\",\"b\",\"C\",\"D\",\"E\"],\"CO\":\"RS\"},\"subjects\":[\"re\",\"eng\",\"sci\",\"ss\",\"o\"]}', '2020-06-23 07:52:17', '2020-06-23 07:52:17');
INSERT INTO `eligibility_content` VALUES ('35', '12', '{\"scoring\":{\"type\":\"SC\",\"method\":\"NA\",\"YN\":[\"Granted\",\"Denied\"],\"NR\":[\"A\",\"B\",\"C\",\"D\",\"E\"]}}', '2020-06-23 10:53:01', '2020-06-23 10:53:01');
INSERT INTO `eligibility_content` VALUES ('46', '14', '{\"academic_grade\":\"STD\",\"academic_term\":\"SEM\",\"subjects\":[\"eng\",\"math\",\"ss\"],\"terms_pulled\":[\"1\",\"3\",\"4\"]}', '2020-06-23 15:37:07', '2020-06-23 15:37:07');
INSERT INTO `eligibility_content` VALUES ('49', '6', '{\"subjects\":[\"math\",\"ss\",\"o\"],\"calc_score\":\"1\",\"header\":{\"1\":{\"name\":\"Maths\",\"questions\":{\"1\":{\"name\":\"Question 1 header 1\",\"options\":{\"1\":\"Hrupa\",\"2\":\"Ehmaul\"},\"points\":{\"1\":\"AkRkRhab\",\"2\":\"Akla\"}}}},\"2\":{\"name\":\"English\",\"questions\":{\"1\":{\"name\":\"question 1 header 2\",\"options\":{\"1\":\"Uyuaa iktbb\"},\"points\":{\"1\":\"MAmu\"}},\"2\":{\"name\":\"question 2 header 2\",\"options\":{\"1\":\"TEst\",\"2\":\"Tla\"},\"points\":{\"1\":\"Htaan\",\"2\":\"Lnaaa\"}},\"3\":{\"name\":\"question 3 header 2\",\"options\":{\"1\":\"Tth\",\"2\":\"Gkn taa  ua\",\"3\":\"Oaf ikggrehba\"},\"points\":{\"1\":\"Vkatetgm\",\"2\":\"AaOuahlge ar\",\"3\":\"Eaah\"}}}},\"3\":{\"name\":\"Science\",\"questions\":{\"1\":{\"name\":\"Uyta\",\"options\":{\"1\":\"Me\",\"2\":\"Ayneugwa\"},\"points\":{\"1\":\"Onbaho\",\"2\":\"U aalinea g\"}},\"2\":{\"name\":\"Tribno edited\",\"options\":{\"1\":\"Ieegg lfnaal rb\",\"2\":\"Oh\",\"3\":\"Saeunmhln t\",\"4\":\"Kahgrawh\'vu\",\"5\":\"A edited\"},\"points\":{\"1\":\"Ia\",\"2\":\"Hunpakttlp\",\"3\":\"Phnatao iu\",\"4\":\"Aaaaul nR a A\",\"5\":\"NugbCh hbh\"}}}}}}', '2020-06-24 09:23:37', '2020-06-24 09:23:37');
INSERT INTO `eligibility_template` VALUES ('1', 'Interview Score', '1', 'interview', '1', 'Y', '2020-06-05 12:13:27', '2020-06-05 12:13:27');
INSERT INTO `eligibility_template` VALUES ('2', 'Academic Grades', '1', 'academic_grades', '1', 'Y', '2020-06-05 12:13:27', '2020-06-05 12:13:27');
INSERT INTO `eligibility_template` VALUES ('3', 'Academic Grade Calculation', '1', 'academic_grade_calculation', '1', 'T', '2020-06-05 12:13:27', '2020-06-09 12:42:17');
INSERT INTO `eligibility_template` VALUES ('4', 'Recommendation Form', '1', '', '1', 'T', '2020-06-05 12:13:27', '2020-06-05 12:13:27');
INSERT INTO `eligibility_template` VALUES ('5', 'Writing Prompt', '1', 'writing_prompt', '1', 'Y', '2020-06-05 12:13:28', '2020-06-05 12:13:28');
INSERT INTO `eligibility_template` VALUES ('6', 'Audition', '1', 'audition', '1', 'Y', '2020-06-05 12:13:28', '2020-06-05 12:13:28');
INSERT INTO `eligibility_template` VALUES ('7', 'Committee Score', '1', 'committee_score', '1', 'Y', '2020-06-05 12:13:28', '2020-06-05 12:13:28');
INSERT INTO `eligibility_template` VALUES ('8', 'Conduct Disciplinary Info', '1', 'conduct_disciplinary', '1', 'Y', '2020-06-05 12:13:28', '2020-06-10 06:09:58');
INSERT INTO `eligibility_template` VALUES ('9', 'Special Ed Indicators', '1', '', '1', 'T', '2020-06-05 12:13:28', '2020-06-05 12:13:28');
INSERT INTO `eligibility_template` VALUES ('10', 'Standardized Testing', '1', 'standardized_testing', '1', 'Y', '2020-06-05 12:13:28', '2020-06-05 12:13:28');
INSERT INTO `form` VALUES ('27', null, 'hugevaboka', 'est-magna-qui-dolor', 'Ipsam facilis reicie', 'mypama', 'Alias labore sed ali', 'tyxoh@fgh.hj', 'y', 'y', '<div class=\"card\">\r\n                                    <div class=\"card-header\" contenteditable=\"true\">Dolores enim est rat.</div>\r\n                                    <div class=\"card-body input_container\" id=\"input_container\">\r\n                                        \r\n                                    <div class=\"form-group row\">\r\n<label class=\"control-label col-12 col-md-5\" contenteditable=\"true\">Vel sed est voluptas.</label>\r\n<div class=\"col-12 col-md-6 col-xl-6\">\r\n<input type=\"text\" class=\"form-control\">\r\n</div>\r\n<div class=\"col-md-1\"><span class=\"close\"><i class=\"fa fa-window-close\" aria-hidden=\"true\"></i></span></div></div><div class=\"form-group row checkbox\" id=\"checkbox\">\r\n<label class=\"control-label col-12 col-md-5\" contenteditable=\"true\">Lorem nobis dolorum .</label>\r\n<div class=\"col-12 col-md-6 col-xl-6 checkbox_container\">\r\n<div class=\"custom-control custom-checkbox d-inline\">\r\n<input value=\"\" type=\"checkbox\" class=\"custom-control-input\" id=\"checkbox3\" name=\"\" style=\"height: auto !important;\">\r\n<label for=\"checkbox3\" class=\"custom-control-label\" contenteditable=\"true\">Nostrud labore excep.</label>\r\n</div>\r\n<div class=\"custom-control custom-checkbox d-inline\">\r\n<input value=\"\" type=\"checkbox\" class=\"custom-control-input\" id=\"checkbox4\" name=\"\" style=\"height: auto !important;\">\r\n<label for=\"checkbox4\" class=\"custom-control-label\" contenteditable=\"true\">Nisi saepe et tenetu.</label>\r\n</div>\r\n</div>\r\n<div class=\"col-md-1\"><span class=\"close\"><i class=\"fa fa-window-close\" aria-hidden=\"true\"></i></span></div>\r\n</div><div class=\"form-group row\">\r\n<label class=\"control-label col-12 col-md-5\" contenteditable=\"true\">Debitis delectus, ip.</label>\r\n<div class=\"col-12 col-md-6 col-xl-6\">\r\n<input type=\"text\" class=\"form-control\">\r\n</div>\r\n<div class=\"col-md-1\"><span class=\"close\"><i class=\"fa fa-window-close\" aria-hidden=\"true\"></i></span></div></div><div class=\"form-group row checkbox\" id=\"checkbox\">\r\n<label class=\"control-label col-12 col-md-5\" contenteditable=\"true\">In obcaecati sunt qu.</label>\r\n<div class=\"col-12 col-md-6 col-xl-6 checkbox_container\">\r\n<div class=\"custom-control custom-checkbox d-inline\">\r\n<input value=\"\" type=\"checkbox\" class=\"custom-control-input\" id=\"checkbox5\" name=\"\" style=\"height: auto !important;\">\r\n<label for=\"checkbox5\" class=\"custom-control-label\" contenteditable=\"true\">Consequat. Voluptate.</label>\r\n</div>\r\n<div class=\"custom-control custom-checkbox d-inline\">\r\n<input value=\"\" type=\"checkbox\" class=\"custom-control-input\" id=\"checkbox6\" name=\"\" style=\"height: auto !important;\">\r\n<label for=\"checkbox6\" class=\"custom-control-label\" contenteditable=\"true\">Sit, nulla quis ipsu.</label>\r\n</div>\r\n</div>\r\n<div class=\"col-md-1\"><span class=\"close\"><i class=\"fa fa-window-close\" aria-hidden=\"true\"></i></span></div>\r\n</div><div class=\"form-group row\">\r\n<label class=\"control-label col-12 col-md-5\" contenteditable=\"true\">Ad fugiat, architect.</label>\r\n<div class=\"col-12 col-md-6 col-xl-6\">\r\n<textarea cols=\"40\" style=\"resize: none\">\r\n</textarea>\r\n</div>\r\n<div class=\"col-md-1\"><span class=\"close\"><i class=\"fa fa-window-close\" aria-hidden=\"true\"></i></span></div>\r\n</div></div>\r\n                                </div>', 'y', '2020-06-23 01:06:21', '2020-06-23 01:06:21');
INSERT INTO `form` VALUES ('28', '0', 'Eaam', 'aano-edited-sd', 'Uku nujestu olkocav otpo ece lumovvu pehmo ej meg ofovat ajopaz bilju vohna oz verhi haluzto lekaw. Kofu su ru tapwe najuc ta icwit vaoroni kole vuwma tiso ob tu pikde dut ih al. Odi fizmo ofifki zam hutozaat nadludec si kunmog fimosju zeew riwapi nuto zuhar bi ruvfocik emfi labude. Wuho lulhoh jac depsi wuw owi kemujehof age socpohwog capla mi tuvbiima. Mo wihtepizo ec na ic so', 'CwiDmglahvag', 'Vor kes pev ahivi kih up egup am vonguso zibiwum uktoh dupdat ti iroka. Duvkel se ra luddeh dujkiase r vumdieg zeij jad wuz hiffo', 'mail@temp.cmn', 'y', 'y', '<div class=\"card\">\r\n                                    <div class=\"card-header\" contenteditable=\"true\">Step 1 - Please enter your student\'s requested information</div>\r\n                                    <div class=\"card-body input_container\" id=\"input_container\">\r\n                                        \r\n                                    <div class=\"form-group row\">\r\n<label class=\"control-label col-12 col-md-5\" contenteditable=\"true\">Label : </label>\r\n<div class=\"col-12 col-md-6 col-xl-6\">\r\n<input type=\"text\" class=\"form-control\">\r\n</div>\r\n<div class=\"col-md-1\"><span class=\"close\"><i class=\"fa fa-window-close\" aria-hidden=\"true\"></i></span></div></div><div class=\"form-group row checkbox\" id=\"checkbox\">\r\n<label class=\"control-label col-12 col-md-5\" contenteditable=\"true\">Label : </label>\r\n<div class=\"col-12 col-md-6 col-xl-6 checkbox_container\">\r\n<div class=\"custom-control custom-checkbox d-inline\">\r\n<input value=\"\" type=\"checkbox\" class=\"custom-control-input\" id=\"checkbox3\" name=\"\" style=\"height: auto !important;\">\r\n<label for=\"checkbox3\" class=\"custom-control-label\" contenteditable=\"true\"> label</label>\r\n</div>\r\n<div class=\"custom-control custom-checkbox d-inline\">\r\n<input value=\"\" type=\"checkbox\" class=\"custom-control-input\" id=\"checkbox4\" name=\"\" style=\"height: auto !important;\">\r\n<label for=\"checkbox4\" class=\"custom-control-label\" contenteditable=\"true\"> label</label>\r\n</div>\r\n</div>\r\n<div class=\"col-md-1\"><span class=\"close\"><i class=\"fa fa-window-close\" aria-hidden=\"true\"></i></span></div>\r\n</div></div>\r\n                                </div>', 'y', '2020-06-24 11:06:32', '2020-06-24 11:06:36');
INSERT INTO `grade` VALUES ('1', 'PreK');
INSERT INTO `grade` VALUES ('2', 'K');
INSERT INTO `grade` VALUES ('3', '1');
INSERT INTO `grade` VALUES ('4', '2');
INSERT INTO `grade` VALUES ('5', '3');
INSERT INTO `grade` VALUES ('6', '4');
INSERT INTO `grade` VALUES ('7', '5');
INSERT INTO `grade` VALUES ('9', '7');
INSERT INTO `grade` VALUES ('10', '8');
INSERT INTO `grade` VALUES ('11', '9');
INSERT INTO `grade` VALUES ('12', '10');
INSERT INTO `grade` VALUES ('13', '11');
INSERT INTO `grade` VALUES ('14', '12');
INSERT INTO `priorities` VALUES ('77', '1', 'First', 'Y', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priorities` VALUES ('78', '1', 'Second', 'Y', '2020-06-18 04:27:38', '2020-06-18 07:41:07');
INSERT INTO `priority_details` VALUES ('1', '77', 'Priority 1', 'Y', 'Y', 'Y', '1', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priority_details` VALUES ('2', '77', 'Priority 2', 'N', 'Y', 'N', '1', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priority_details` VALUES ('3', '77', 'Priority 3', 'Y', 'Y', 'Y', '1', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priority_details` VALUES ('4', '77', 'Priority 4', 'N', 'N', 'N', '1', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priority_details` VALUES ('5', '77', 'Priority 5', 'Y', 'N', 'Y', '1', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priority_details` VALUES ('6', '77', 'Priority 6', 'N', 'Y', 'N', '1', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priority_details` VALUES ('7', '77', 'Priority 7', 'Y', 'N', 'Y', '1', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priority_details` VALUES ('8', '77', 'Priority 8', 'N', 'N', 'N', '1', '2020-06-17 17:40:26', '2020-06-18 07:40:49');
INSERT INTO `priority_details` VALUES ('9', '78', 'Ooobf tp a', 'N', 'Y', 'Y', '1', '2020-06-18 04:27:38', '2020-06-18 07:41:07');
INSERT INTO `priority_details` VALUES ('10', '78', 'sdfs', 'Y', 'N', 'N', '1', '2020-06-18 04:27:38', '2020-06-18 07:41:08');
INSERT INTO `priority_details` VALUES ('11', '78', 'Aep Edit', 'Y', 'N', 'Y', '1', '2020-06-18 04:27:38', '2020-06-18 07:41:08');
INSERT INTO `program` VALUES ('1', '0', 'Lee School of Creative and Performing Arts', 'Lee Creative and Perfoming Arts-Media Arts', 'Creative Writing', null, 'K,1', 'MCPSS Magnet Form', '1', '3', '6', '2', '4', '5', '1', 'Racial Composition', 'Program Name', 'Calculation', 'Y', 'Y', null, '2020-06-15 10:06:47', '2020-06-15 10:06:47', 'Y');
INSERT INTO `program` VALUES ('2', '0', 'AaaolnouKia', 'Ene', 'A', 'Nma l', 'K,6,12', 'MPS Magnet Form', '', null, null, null, null, null, null, null, null, null, 'Y', 'Y', null, '2020-06-16 10:06:39', '2020-06-16 10:06:39', 'Y');
INSERT INTO `program` VALUES ('3', '0', 'Test Pragram', 'Test Group Filter 1', 'Test Group Filter 2', 'Test Group Filter 3', '2,3,8', 'MCPSS Magnet Form', '1,2,3', null, null, null, null, null, null, null, null, null, 'Y', 'Y', null, '2020-06-17 10:06:07', '2020-06-17 10:06:07', 'Y');
INSERT INTO `program` VALUES ('4', '0', 'Test Program 2', 'Band (Instrumental)', 'Creative Writing', null, 'PreK,K,6', 'MCPSS Magnet Form', '2,3', null, null, null, null, null, null, null, null, null, 'Y', 'Y', null, '2020-06-17 10:06:11', '2020-06-17 10:06:11', 'Y');
INSERT INTO `program` VALUES ('5', '0', 'Test Program 3', 'Lee Creative and Perfoming Arts-Media Arts', 'Choral Music', 'Classical Guitar', '12', 'MPS Magnet Form', '1', null, null, null, null, null, null, null, null, null, 'Y', 'Y', null, '2020-06-17 10:06:27', '2020-06-17 10:06:27', 'Y');
INSERT INTO `program` VALUES ('6', '1', 'Tovm mtDi', 'Ohr', 'Ia   a', 'Masen', 'PreK,1,3,4,7,8,10,11,12', 'MPS Magnet Form', '78', null, null, null, null, null, null, null, null, null, 'Y', 'Y', null, '2020-06-18 11:06:13', '2020-06-18 11:06:13', 'Y');
INSERT INTO `program_eligibility` VALUES ('1', '1', 'interview_score', 'interview_score', null, 'Interview Score 2', '50%', 'PreK', 'Y', '2020-06-15 10:16:47', '2020-06-15 10:16:47');
INSERT INTO `program_eligibility` VALUES ('2', '1', 'committee_score', 'committee_score', null, 'Committee Score 2', '50%', 'PreK', 'Y', '2020-06-15 10:16:47', '2020-06-15 10:16:47');
INSERT INTO `program_eligibility` VALUES ('3', '3', '1', 'Basic', 'close', 'Interview Score 1', '', '3', 'Y', '2020-06-17 10:28:20', '2020-06-17 10:37:07');
INSERT INTO `program_eligibility` VALUES ('4', '3', '5', 'Combined', 'close', 'Writing Prompt 1', '', '', 'Y', '2020-06-17 10:28:20', '2020-06-17 10:37:07');
INSERT INTO `program_eligibility` VALUES ('5', '3', '6', 'Basic', 'close', 'AU - 1', '', '', 'N', '2020-06-17 10:28:20', '2020-06-17 10:37:07');
INSERT INTO `program_eligibility` VALUES ('6', '3', '7', 'Basic', 'close', 'CS - 1', '', '2', 'N', '2020-06-17 10:28:20', '2020-06-17 10:37:07');
INSERT INTO `program_eligibility` VALUES ('7', '6', '1', null, 'close', null, '', '', 'N', '2020-06-18 11:02:02', '2020-06-18 11:02:13');
INSERT INTO `program_eligibility` VALUES ('8', '6', '7', null, 'close', null, '', '', 'N', '2020-06-18 11:02:02', '2020-06-18 11:02:13');
INSERT INTO `role` VALUES ('1', 'super admin', '2019-05-27 12:11:12', '2020-05-27 12:11:13');
INSERT INTO `role` VALUES ('2', 'admin', '2020-05-27 12:11:34', '2020-05-27 12:11:35');
INSERT INTO `role_copy` VALUES ('1', 'super admin', '2019-05-27 12:11:12', '2020-05-27 12:11:13');
INSERT INTO `role_copy` VALUES ('2', 'admin', '2020-05-27 12:11:34', '2020-05-27 12:11:35');
INSERT INTO `school` VALUES ('4', '1', '2,3', 'TCS School', 'Yes', 'TEST', 'ss', 'Y', '2020-06-15 17:11:53', '2020-06-15 14:11:30');
INSERT INTO `school` VALUES ('5', '0', '1,4,6,11,12,13,14', 'Teasta edited', 'Yes', 'HCS Zoning API Name if different', 'HSC_SIS_Name', 'Y', '2020-06-15 14:09:28', '2020-06-16 04:06:25');
INSERT INTO `school` VALUES ('6', '3', '11,12,13,14', 'Mobile School', 'Yes', 'Mobile School 1.1', 'Mobile School 1.2', 'Y', '2020-06-15 17:53:17', '2020-06-15 17:53:17');
INSERT INTO `school` VALUES ('7', '0', '2,9,10,11', 'Brock Hampton', 'Yes', 'duvepivy', 'jeqavyse', 'T', '2020-06-16 04:03:28', '2020-06-16 04:06:15');
INSERT INTO `stores` VALUES ('1', 'Huntville City Schools', 'HSC', 'hsc', '2020-05-26 17:20:35', '2020-05-26 17:20:38');
INSERT INTO `stores_copy` VALUES ('1', 'Huntville City Schools', 'HSC', 'hsc', '2020-05-26 17:20:35', '2020-05-26 17:20:38');
INSERT INTO `tenants_copy` VALUES ('1', 'Huntsville City school', 'huntsville-city-school', '2020-06-03 12:12:41', '2020-06-03 12:12:41');
INSERT INTO `tenants_copy` VALUES ('2', 'Tuscaloosa city school', 'tuscaloosa-city-school', '2020-06-03 12:12:41', '2020-06-03 12:12:41');
INSERT INTO `users` VALUES ('1', '1', 'admin', 'Admin', 'Magnet', 'admin@magnet.com', '1_profile.png', '$2y$10$0YVK651zKIZUhPw5NdQ9V.ozk9HNVW80XdOdnnZ1b2hq32wgmA.E6', 'WpqlMpQO6u27Jo6ia6XveyhpliGrMKjNmS0vKsvm0v9QmNWHn2tCFcmns0Ka', '0', 'Y', '2020-05-26 08:21:04', '2020-06-15 05:34:41');
INSERT INTO `users` VALUES ('25', '1', 'MaulikVadgama', 'Maulik', 'Vadgama', 'maulik@iwebsquare.com', null, '$2y$10$FWuOuzdFiAe8iWznES4dYO5JimHjNVTWI5S0tcI7Sx48wHcqeyXiS', null, '0', 'Y', '2020-06-10 03:10:11', '2020-06-10 03:10:11');
INSERT INTO `users` VALUES ('26', '1', 'HCSAdmin', 'HCS', 'Admin', 'nancybrownus8@gmail.com', '26_profile.png', '$2y$10$XxIzXQ/25YZOLHjvwVB/JuxtA9T1nWF17vaJQxjuGvBv3gUiptt5O', 'wqSAGO8AP1Xpdgv2miDKPPaStBFdx5eeMpPpbbNfnG6Nrdo0RGuAZ5pt0g9J', '1', 'Y', '2020-06-10 07:32:54', '2020-06-10 07:39:03');
INSERT INTO `users_copy` VALUES ('1', '1', null, 'Admin', 'Magnet', 'admin@magnet.com', null, '$2y$10$0YVK651zKIZUhPw5NdQ9V.ozk9HNVW80XdOdnnZ1b2hq32wgmA.E6', 'stfZClTwfnrCnGrO1go1E4bprLyMWFpwJ7KpMWkIn8t1VPMzJqxPBidZ6Yb2', '0', 'Y', '2020-05-26 08:21:04', '2020-05-26 08:21:04');
INSERT INTO `users_copy` VALUES ('19', '2', 'JohnDoe', 'John', 'Doe', 'huntsville-city-school@gmail.com', null, '$2y$10$90Ww8pT3uINdUzLwrw9tluywXDKfsiVVGxuHSLDMaawxOv54YN39e', null, '1', 'Y', '2020-05-29 13:13:27', '2020-05-29 13:13:27');
INSERT INTO `users_copy` VALUES ('20', '2', 'tuscaloosa', 'Tuscaloosa', '', 'tuscaloosa-city-school@gmail.com', null, '$2y$10$90Ww8pT3uINdUzLwrw9tluywXDKfsiVVGxuHSLDMaawxOv54YN39e', null, '2', 'Y', null, null);
INSERT INTO `users_copy` VALUES ('22', '1', 'NancyBrown', 'Nancy', 'Brown', 'nancybrownus8@gmail.com', null, '$2y$10$jH.lPhhLG6VYlmUJjjNy0eE9mKMigcRiqlQm6l0FNEdtK.6wYkgMG', null, '3', 'Y', '2020-06-09 11:21:16', '2020-06-09 11:21:16');
