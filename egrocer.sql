-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2026 at 08:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `egrocer`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` text NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `forgot_password_code` varchar(191) DEFAULT NULL,
  `fcm_id` varchar(191) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 => Active, 0 => Inactive',
  `login_at` timestamp NULL DEFAULT NULL,
  `last_active_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `role_id`, `created_by`, `forgot_password_code`, `fcm_id`, `remember_token`, `status`, `login_at`, `last_active_at`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'vkmittal4u@gmail.com', '$2y$10$Ae3OmiRrIpVx1KcafdDyJeTDSZIEgiGeWPlSe5zd.cx1kxEzA90xq', 1, 1, NULL, NULL, NULL, 1, NULL, NULL, '2026-01-19 09:35:16', '2026-01-19 09:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `admin_tokens`
--

CREATE TABLE `admin_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `fcm_token` varchar(255) NOT NULL,
  `platform` varchar(191) NOT NULL DEFAULT 'web'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_call_tracking`
--

CREATE TABLE `api_call_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `api_name` varchar(191) NOT NULL,
  `source` varchar(191) NOT NULL,
  `count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_call_tracking`
--

INSERT INTO `api_call_tracking` (`id`, `api_name`, `source`, `count`, `created_at`, `updated_at`) VALUES
(1, 'google_places_autocomplete', 'app', 0, '2026-01-19 09:33:54', '2026-01-19 09:33:54'),
(2, 'google_places_autocomplete', 'web', 0, '2026-01-19 09:33:54', '2026-01-19 09:33:54'),
(3, 'google_places_details', 'app', 0, '2026-01-19 09:33:55', '2026-01-19 09:33:55'),
(4, 'google_places_details', 'web', 0, '2026-01-19 09:33:55', '2026-01-19 09:33:55'),
(5, 'google_maps_geocoding', 'app', 0, '2026-01-19 09:33:55', '2026-01-19 09:33:55'),
(6, 'google_maps_geocoding', 'web', 0, '2026-01-19 09:33:55', '2026-01-19 09:33:55'),
(7, 'google_gemini', 'app', 0, '2026-01-19 09:33:55', '2026-01-19 09:33:55'),
(8, 'google_gemini', 'web', 0, '2026-01-19 09:33:55', '2026-01-19 09:33:55');

-- --------------------------------------------------------

--
-- Table structure for table `app_usages`
--

CREATE TABLE `app_usages` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` varchar(191) NOT NULL,
  `device_type` varchar(191) NOT NULL,
  `app_version` varchar(191) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `city_id` int(11) DEFAULT 0,
  `pincode_id` int(11) DEFAULT NULL,
  `name` text NOT NULL,
  `minimum_free_delivery_order_amount` int(11) NOT NULL DEFAULT 0,
  `delivery_charges` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `description` text NOT NULL,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `views_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Total view count for this blog',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_views`
--

CREATE TABLE `blog_views` (
  `blog_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `image` text NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `save_for_later` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_notifications`
--

CREATE TABLE `cart_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `name` varchar(191) NOT NULL DEFAULT '0',
  `slug` varchar(191) DEFAULT NULL,
  `subtitle` text NOT NULL,
  `image` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `product_rating` tinyint(4) NOT NULL DEFAULT 0,
  `web_image` text DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '0: Main Category, Other Sub category of id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `schema_markup` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `row_order`, `name`, `slug`, `subtitle`, `image`, `status`, `product_rating`, `web_image`, `parent_id`, `created_at`, `updated_at`, `meta_title`, `meta_keywords`, `schema_markup`, `meta_description`) VALUES
(1, 0, 'VEG', 'VEG', 'VEG', 'categories/1768815680_42622.png', 1, 0, '', 0, '2026-01-19 09:41:21', '2026-01-19 09:41:21', 'null', 'null', 'null', 'null');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `zone` varchar(191) NOT NULL,
  `state` varchar(191) NOT NULL,
  `formatted_address` varchar(191) NOT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `min_amount_for_free_delivery` varchar(191) DEFAULT NULL,
  `delivery_charge_method` varchar(191) DEFAULT NULL,
  `fixed_charge` decimal(11,2) NOT NULL DEFAULT 0.00,
  `per_km_charge` decimal(11,2) NOT NULL DEFAULT 0.00,
  `range_wise_charges` text DEFAULT NULL,
  `time_to_travel` int(11) NOT NULL DEFAULT 0,
  `geolocation_type` varchar(191) DEFAULT NULL,
  `radius` varchar(191) DEFAULT '0',
  `boundary_points` text DEFAULT NULL,
  `max_deliverable_distance` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `dial_code` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `logo` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `dial_code`, `code`, `logo`, `status`) VALUES
(1, 'Afghanistan', '+93', 'AF', NULL, 1),
(2, 'Aland Islands', '+358', 'AX', NULL, 1),
(3, 'Albania', '+355', 'AL', NULL, 1),
(4, 'Algeria', '+213', 'DZ', NULL, 1),
(5, 'AmericanSamoa', '+1684', 'AS', NULL, 1),
(6, 'Andorra', '+376', 'AD', NULL, 1),
(7, 'Angola', '+244', 'AO', NULL, 1),
(8, 'Anguilla', '+1264', 'AI', NULL, 1),
(9, 'Antarctica', '+672', 'AQ', NULL, 1),
(10, 'Antigua and Barbuda', '+1268', 'AG', NULL, 1),
(11, 'Argentina', '+54', 'AR', NULL, 1),
(12, 'Armenia', '+374', 'AM', NULL, 1),
(13, 'Aruba', '+297', 'AW', NULL, 1),
(14, 'Australia', '+61', 'AU', NULL, 1),
(15, 'Austria', '+43', 'AT', NULL, 1),
(16, 'Azerbaijan', '+994', 'AZ', NULL, 1),
(17, 'Bahamas', '+1242', 'BS', NULL, 1),
(18, 'Bahrain', '+973', 'BH', NULL, 1),
(19, 'Bangladesh', '+880', 'BD', NULL, 1),
(20, 'Barbados', '+1246', 'BB', NULL, 1),
(21, 'Belarus', '+375', 'BY', NULL, 1),
(22, 'Belgium', '+32', 'BE', NULL, 1),
(23, 'Belize', '+501', 'BZ', NULL, 1),
(24, 'Benin', '+229', 'BJ', NULL, 1),
(25, 'Bermuda', '+1441', 'BM', NULL, 1),
(26, 'Bhutan', '+975', 'BT', NULL, 1),
(27, 'Bolivia, Plurinational State of', '+591', 'BO', NULL, 1),
(28, 'Bosnia and Herzegovina', '+387', 'BA', NULL, 1),
(29, 'Botswana', '+267', 'BW', NULL, 1),
(30, 'Brazil', '+55', 'BR', NULL, 1),
(31, 'British Indian Ocean Territory', '+246', 'IO', NULL, 1),
(32, 'Brunei Darussalam', '+673', 'BN', NULL, 1),
(33, 'Bulgaria', '+359', 'BG', NULL, 1),
(34, 'Burkina Faso', '+226', 'BF', NULL, 1),
(35, 'Burundi', '+257', 'BI', NULL, 1),
(36, 'Cambodia', '+855', 'KH', NULL, 1),
(37, 'Cameroon', '+237', 'CM', NULL, 1),
(38, 'Canada', '+1', 'CA', NULL, 1),
(39, 'Cape Verde', '+238', 'CV', NULL, 1),
(40, 'Cayman Islands', '+345', 'KY', NULL, 1),
(41, 'Central African Republic', '+236', 'CF', NULL, 1),
(42, 'Chad', '+235', 'TD', NULL, 1),
(43, 'Chile', '+56', 'CL', NULL, 1),
(44, 'China', '+86', 'CN', NULL, 1),
(45, 'Christmas Island', '+61', 'CX', NULL, 1),
(46, 'Cocos (Keeling) Islands', '+61', 'CC', NULL, 1),
(47, 'Colombia', '+57', 'CO', NULL, 1),
(48, 'Comoros', '+269', 'KM', NULL, 1),
(49, 'Congo', '+242', 'CG', NULL, 1),
(50, 'Congo, The Democratic Republic of the Congo', '+243', 'CD', NULL, 1),
(51, 'Cook Islands', '+682', 'CK', NULL, 1),
(52, 'Costa Rica', '+506', 'CR', NULL, 1),
(53, 'Cote d\'Ivoire', '+225', 'CI', NULL, 1),
(54, 'Croatia', '+385', 'HR', NULL, 1),
(55, 'Cuba', '+53', 'CU', NULL, 1),
(56, 'Cyprus', '+357', 'CY', NULL, 1),
(57, 'Czech Republic', '+420', 'CZ', NULL, 1),
(58, 'Denmark', '+45', 'DK', NULL, 1),
(59, 'Djibouti', '+253', 'DJ', NULL, 1),
(60, 'Dominica', '+1767', 'DM', NULL, 1),
(61, 'Dominican Republic', '+1849', 'DO', NULL, 1),
(62, 'Ecuador', '+593', 'EC', NULL, 1),
(63, 'Egypt', '+20', 'EG', NULL, 1),
(64, 'El Salvador', '+503', 'SV', NULL, 1),
(65, 'Equatorial Guinea', '+240', 'GQ', NULL, 1),
(66, 'Eritrea', '+291', 'ER', NULL, 1),
(67, 'Estonia', '+372', 'EE', NULL, 1),
(68, 'Ethiopia', '+251', 'ET', NULL, 1),
(69, 'Falkland Islands (Malvinas)', '+500', 'FK', NULL, 1),
(70, 'Faroe Islands', '+298', 'FO', NULL, 1),
(71, 'Fiji', '+679', 'FJ', NULL, 1),
(72, 'Finland', '+358', 'FI', NULL, 1),
(73, 'France', '+33', 'FR', NULL, 1),
(74, 'French Guiana', '+594', 'GF', NULL, 1),
(75, 'French Polynesia', '+689', 'PF', NULL, 1),
(76, 'Gabon', '+241', 'GA', NULL, 1),
(77, 'Gambia', '+220', 'GM', NULL, 1),
(78, 'Georgia', '+995', 'GE', NULL, 1),
(79, 'Germany', '+49', 'DE', NULL, 1),
(80, 'Ghana', '+233', 'GH', NULL, 1),
(81, 'Gibraltar', '+350', 'GI', NULL, 1),
(82, 'Greece', '+30', 'GR', NULL, 1),
(83, 'Greenland', '+299', 'GL', NULL, 1),
(84, 'Grenada', '+1473', 'GD', NULL, 1),
(85, 'Guadeloupe', '+590', 'GP', NULL, 1),
(86, 'Guam', '+1671', 'GU', NULL, 1),
(87, 'Guatemala', '+502', 'GT', NULL, 1),
(88, 'Guernsey', '+44', 'GG', NULL, 1),
(89, 'Guinea', '+224', 'GN', NULL, 1),
(90, 'Guinea-Bissau', '+245', 'GW', NULL, 1),
(91, 'Guyana', '+595', 'GY', NULL, 1),
(92, 'Haiti', '+509', 'HT', NULL, 1),
(93, 'Holy See (Vatican City State)', '+379', 'VA', NULL, 1),
(94, 'Honduras', '+504', 'HN', NULL, 1),
(95, 'Hong Kong', '+852', 'HK', NULL, 1),
(96, 'Hungary', '+36', 'HU', NULL, 1),
(97, 'Iceland', '+354', 'IS', NULL, 1),
(98, 'India', '+91', 'IN', NULL, 1),
(99, 'Indonesia', '+62', 'ID', NULL, 1),
(100, 'Iran, Islamic Republic of Persian Gulf', '+98', 'IR', NULL, 1),
(101, 'Iraq', '+964', 'IQ', NULL, 1),
(102, 'Ireland', '+353', 'IE', NULL, 1),
(103, 'Isle of Man', '+44', 'IM', NULL, 1),
(104, 'Israel', '+972', 'IL', NULL, 1),
(105, 'Italy', '+39', 'IT', NULL, 1),
(106, 'Jamaica', '+1876', 'JM', NULL, 1),
(107, 'Japan', '+81', 'JP', NULL, 1),
(108, 'Jersey', '+44', 'JE', NULL, 1),
(109, 'Jordan', '+962', 'JO', NULL, 1),
(110, 'Kazakhstan', '+77', 'KZ', NULL, 1),
(111, 'Kenya', '+254', 'KE', NULL, 1),
(112, 'Kiribati', '+686', 'KI', NULL, 1),
(113, 'Korea, Democratic People\'s Republic of Korea', '+850', 'KP', NULL, 1),
(114, 'Korea, Republic of South Korea', '+82', 'KR', NULL, 1),
(115, 'Kuwait', '+965', 'KW', NULL, 1),
(116, 'Kyrgyzstan', '+996', 'KG', NULL, 1),
(117, 'Laos', '+856', 'LA', NULL, 1),
(118, 'Latvia', '+371', 'LV', NULL, 1),
(119, 'Lebanon', '+961', 'LB', NULL, 1),
(120, 'Lesotho', '+266', 'LS', NULL, 1),
(121, 'Liberia', '+231', 'LR', NULL, 1),
(122, 'Libyan Arab Jamahiriya', '+218', 'LY', NULL, 1),
(123, 'Liechtenstein', '+423', 'LI', NULL, 1),
(124, 'Lithuania', '+370', 'LT', NULL, 1),
(125, 'Luxembourg', '+352', 'LU', NULL, 1),
(126, 'Macao', '+853', 'MO', NULL, 1),
(127, 'Macedonia', '+389', 'MK', NULL, 1),
(128, 'Madagascar', '+261', 'MG', NULL, 1),
(129, 'Malawi', '+265', 'MW', NULL, 1),
(130, 'Malaysia', '+60', 'MY', NULL, 1),
(131, 'Maldives', '+960', 'MV', NULL, 1),
(132, 'Mali', '+223', 'ML', NULL, 1),
(133, 'Malta', '+356', 'MT', NULL, 1),
(134, 'Marshall Islands', '+692', 'MH', NULL, 1),
(135, 'Martinique', '+596', 'MQ', NULL, 1),
(136, 'Mauritania', '+222', 'MR', NULL, 1),
(137, 'Mauritius', '+230', 'MU', NULL, 1),
(138, 'Mayotte', '+262', 'YT', NULL, 1),
(139, 'Mexico', '+52', 'MX', NULL, 1),
(140, 'Micronesia, Federated States of Micronesia', '+691', 'FM', NULL, 1),
(141, 'Moldova', '+373', 'MD', NULL, 1),
(142, 'Monaco', '+377', 'MC', NULL, 1),
(143, 'Mongolia', '+976', 'MN', NULL, 1),
(144, 'Montenegro', '+382', 'ME', NULL, 1),
(145, 'Montserrat', '+1664', 'MS', NULL, 1),
(146, 'Morocco', '+212', 'MA', NULL, 1),
(147, 'Mozambique', '+258', 'MZ', NULL, 1),
(148, 'Myanmar', '+95', 'MM', NULL, 1),
(149, 'Namibia', '+264', 'NA', NULL, 1),
(150, 'Nauru', '+674', 'NR', NULL, 1),
(151, 'Nepal', '+977', 'NP', NULL, 1),
(152, 'Netherlands', '+31', 'NL', NULL, 1),
(153, 'Netherlands Antilles', '+599', 'AN', NULL, 1),
(154, 'New Caledonia', '+687', 'NC', NULL, 1),
(155, 'New Zealand', '+64', 'NZ', NULL, 1),
(156, 'Nicaragua', '+505', 'NI', NULL, 1),
(157, 'Niger', '+227', 'NE', NULL, 1),
(158, 'Nigeria', '+234', 'NG', NULL, 1),
(159, 'Niue', '+683', 'NU', NULL, 1),
(160, 'Norfolk Island', '+672', 'NF', NULL, 1),
(161, 'Northern Mariana Islands', '+1670', 'MP', NULL, 1),
(162, 'Norway', '+47', 'NO', NULL, 1),
(163, 'Oman', '+968', 'OM', NULL, 1),
(164, 'Pakistan', '+92', 'PK', NULL, 1),
(165, 'Palau', '+680', 'PW', NULL, 1),
(166, 'Palestinian Territory, Occupied', '+970', 'PS', NULL, 1),
(167, 'Panama', '+507', 'PA', NULL, 1),
(168, 'Papua New Guinea', '+675', 'PG', NULL, 1),
(169, 'Paraguay', '+595', 'PY', NULL, 1),
(170, 'Peru', '+51', 'PE', NULL, 1),
(171, 'Philippines', '+63', 'PH', NULL, 1),
(172, 'Pitcairn', '+872', 'PN', NULL, 1),
(173, 'Poland', '+48', 'PL', NULL, 1),
(174, 'Portugal', '+351', 'PT', NULL, 1),
(175, 'Puerto Rico', '+1939', 'PR', NULL, 1),
(176, 'Qatar', '+974', 'QA', NULL, 1),
(177, 'Romania', '+40', 'RO', NULL, 1),
(178, 'Russia', '+7', 'RU', NULL, 1),
(179, 'Rwanda', '+250', 'RW', NULL, 1),
(180, 'Reunion', '+262', 'RE', NULL, 1),
(181, 'Saint Barthelemy', '+590', 'BL', NULL, 1),
(182, 'Saint Helena, Ascension and Tristan Da Cunha', '+290', 'SH', NULL, 1),
(183, 'Saint Kitts and Nevis', '+1869', 'KN', NULL, 1),
(184, 'Saint Lucia', '+1758', 'LC', NULL, 1),
(185, 'Saint Martin', '+590', 'MF', NULL, 1),
(186, 'Saint Pierre and Miquelon', '+508', 'PM', NULL, 1),
(187, 'Saint Vincent and the Grenadines', '+1784', 'VC', NULL, 1),
(188, 'Samoa', '+685', 'WS', NULL, 1),
(189, 'San Marino', '+378', 'SM', NULL, 1),
(190, 'Sao Tome and Principe', '+239', 'ST', NULL, 1),
(191, 'Saudi Arabia', '+966', 'SA', NULL, 1),
(192, 'Senegal', '+221', 'SN', NULL, 1),
(193, 'Serbia', '+381', 'RS', NULL, 1),
(194, 'Seychelles', '+248', 'SC', NULL, 1),
(195, 'Sierra Leone', '+232', 'SL', NULL, 1),
(196, 'Singapore', '+65', 'SG', NULL, 1),
(197, 'Slovakia', '+421', 'SK', NULL, 1),
(198, 'Slovenia', '+386', 'SI', NULL, 1),
(199, 'Solomon Islands', '+677', 'SB', NULL, 1),
(200, 'Somalia', '+252', 'SO', NULL, 1),
(201, 'South Africa', '+27', 'ZA', NULL, 1),
(202, 'South Sudan', '+211', 'SS', NULL, 1),
(203, 'South Georgia and the South Sandwich Islands', '+500', 'GS', NULL, 1),
(204, 'Spain', '+34', 'ES', NULL, 1),
(205, 'Sri Lanka', '+94', 'LK', NULL, 1),
(206, 'Sudan', '+249', 'SD', NULL, 1),
(207, 'Suriname', '+597', 'SR', NULL, 1),
(208, 'Svalbard and Jan Mayen', '+47', 'SJ', NULL, 1),
(209, 'Swaziland', '+268', 'SZ', NULL, 1),
(210, 'Sweden', '+46', 'SE', NULL, 1),
(211, 'Switzerland', '+41', 'CH', NULL, 1),
(212, 'Syrian Arab Republic', '+963', 'SY', NULL, 1),
(213, 'Taiwan', '+886', 'TW', NULL, 1),
(214, 'Tajikistan', '+992', 'TJ', NULL, 1),
(215, 'Tanzania, United Republic of Tanzania', '+255', 'TZ', NULL, 1),
(216, 'Thailand', '+66', 'TH', NULL, 1),
(217, 'Timor-Leste', '+670', 'TL', NULL, 1),
(218, 'Togo', '+228', 'TG', NULL, 1),
(219, 'Tokelau', '+690', 'TK', NULL, 1),
(220, 'Tonga', '+676', 'TO', NULL, 1),
(221, 'Trinidad and Tobago', '+1868', 'TT', NULL, 1),
(222, 'Tunisia', '+216', 'TN', NULL, 1),
(223, 'Turkey', '+90', 'TR', NULL, 1),
(224, 'Turkmenistan', '+993', 'TM', NULL, 1),
(225, 'Turks and Caicos Islands', '+1649', 'TC', NULL, 1),
(226, 'Tuvalu', '+688', 'TV', NULL, 1),
(227, 'Uganda', '+256', 'UG', NULL, 1),
(228, 'Ukraine', '+380', 'UA', NULL, 1),
(229, 'United Arab Emirates', '+971', 'AE', NULL, 1),
(230, 'United Kingdom', '+44', 'GB', NULL, 1),
(231, 'United States', '+1', 'US', NULL, 1),
(232, 'Uruguay', '+598', 'UY', NULL, 1),
(233, 'Uzbekistan', '+998', 'UZ', NULL, 1),
(234, 'Vanuatu', '+678', 'VU', NULL, 1),
(235, 'Venezuela, Bolivarian Republic of Venezuela', '+58', 'VE', NULL, 1),
(236, 'Vietnam', '+84', 'VN', NULL, 1),
(237, 'Virgin Islands, British', '+1284', 'VG', NULL, 1),
(238, 'Virgin Islands, U.S.', '+1340', 'VI', NULL, 1),
(239, 'Wallis and Futuna', '+681', 'WF', NULL, 1),
(240, 'Yemen', '+967', 'YE', NULL, 1),
(241, 'Zambia', '+260', 'ZM', NULL, 1),
(242, 'Zimbabwe', '+263', 'ZW', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_boys`
--

CREATE TABLE `delivery_boys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `mobile` varchar(191) NOT NULL,
  `order_note` text DEFAULT NULL,
  `address` text NOT NULL,
  `bonus_type` int(11) DEFAULT 0 COMMENT '0 -> fixed/Salaried, 1 -> Commission',
  `bonus_percentage` double DEFAULT 0,
  `bonus_min_amount` double DEFAULT 0,
  `bonus_max_amount` double DEFAULT 0,
  `balance` double DEFAULT 0,
  `driving_license` text DEFAULT NULL,
  `national_identity_card` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `bank_account_number` text DEFAULT NULL,
  `bank_name` text DEFAULT NULL,
  `account_name` text DEFAULT NULL,
  `ifsc_code` text DEFAULT NULL,
  `other_payment_information` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_available` tinyint(4) NOT NULL DEFAULT 1,
  `fcm_id` varchar(191) DEFAULT NULL,
  `pincode_id` int(11) DEFAULT NULL,
  `cash_received` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_boy_notifications`
--

CREATE TABLE `delivery_boy_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_boy_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `message` text NOT NULL,
  `type` varchar(191) NOT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_boy_transactions`
--

CREATE TABLE `delivery_boy_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `delivery_boy_id` int(11) DEFAULT NULL,
  `type` text DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `status` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fcm_id` varchar(191) NOT NULL,
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` longtext NOT NULL,
  `type` varchar(191) NOT NULL,
  `type_id` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `title`, `message`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Unlock 20% Off – Just for You!', 'Hi [First Name],\nWe’re excited to offer you an exclusive 20% discount on our most popular items! Don’t miss out – this special offer is only valid until [Date].\n\nClick below to shop and save:\n[Link Here]\n\nHurry, your discount awaits!', 'Exclusive Discount Offer', '2026-01-19 09:33:27', '2026-01-19 09:33:27');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `status` char(191) DEFAULT '1',
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fund_transfers`
--

CREATE TABLE `fund_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_boy_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL COMMENT 'credit | debit',
  `opening_balance` double NOT NULL,
  `closing_balance` double NOT NULL,
  `amount` double NOT NULL,
  `status` varchar(191) NOT NULL,
  `message` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_date` date NOT NULL,
  `order_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `order_date` datetime NOT NULL,
  `phone_number` varchar(191) NOT NULL,
  `order_list` text NOT NULL,
  `email` varchar(191) NOT NULL,
  `discount` varchar(191) NOT NULL,
  `total_sale` varchar(191) NOT NULL,
  `shipping_charge` varchar(191) NOT NULL,
  `payment` text NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supported_language_id` int(11) DEFAULT 0,
  `system_type` int(11) NOT NULL COMMENT '1 => Customer App, 2 => Seller and delivery boy App, 3 => Website, 4 => Admin panel',
  `json_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`json_data`)),
  `display_name` varchar(191) DEFAULT NULL,
  `is_default` int(11) DEFAULT 0 COMMENT '0 => No, 1 => Yes',
  `status` int(11) DEFAULT 1 COMMENT '0 => Deactive, 1 => Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_tracking`
--

CREATE TABLE `live_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `tracked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mail_settings`
--

CREATE TABLE `mail_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type` int(11) NOT NULL COMMENT '0:user, 1:Admin',
  `user_id` int(11) NOT NULL,
  `order_status_id` int(11) NOT NULL,
  `mail_status` int(11) NOT NULL COMMENT '0:false, 1:true',
  `mobile_status` int(11) NOT NULL COMMENT '0:false, 1:true',
  `sms_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Disabled, 1: Enabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `extension` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `sub_directory` text NOT NULL,
  `size` text NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_05_03_000001_create_customer_columns', 1),
(9, '2019_05_03_000002_create_subscriptions_table', 1),
(10, '2019_05_03_000003_create_subscription_items_table', 1),
(11, '2019_08_19_000000_create_failed_jobs_table', 1),
(12, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(13, '2022_04_19_162005_create_admins_table', 1),
(14, '2022_04_19_162451_create_areas_table', 1),
(15, '2022_04_19_162712_create_carts_table', 1),
(16, '2022_04_19_162902_create_categories_table', 1),
(17, '2022_04_19_163557_create_cities_table', 1),
(18, '2022_04_19_163656_create_delivery_boys_table', 1),
(19, '2022_04_19_164613_create_delivery_boy_notifications_table', 1),
(20, '2022_04_19_164915_create_devices_table', 1),
(21, '2022_04_19_165016_create_faqs_table', 1),
(22, '2022_04_19_165227_create_favorites_table', 1),
(23, '2022_04_19_165315_create_fund_transfers_table', 1),
(24, '2022_04_19_165516_create_invoices_table', 1),
(25, '2022_04_19_165749_create_media_table', 1),
(26, '2022_04_19_165926_create_newsletters_table', 1),
(27, '2022_04_19_170143_create_notifications_table', 1),
(28, '2022_04_19_170450_create_offers_table', 1),
(29, '2022_04_19_170910_create_orders_table', 1),
(30, '2022_04_19_172151_create_order_bank_transfers_table', 1),
(31, '2022_04_19_172308_create_order_items_table', 1),
(32, '2022_04_19_172843_create_order_trackings_table', 1),
(33, '2022_04_20_151502_create_payments_table', 1),
(34, '2022_04_20_151611_create_payment_requests_table', 1),
(35, '2022_04_20_151716_create_pickup_locations_table', 1),
(36, '2022_04_20_151845_create_pincodes_table', 1),
(37, '2022_04_20_151954_create_products_table', 1),
(38, '2022_04_20_152511_create_product_variants_table', 1),
(39, '2022_04_20_152844_create_promo_codes_table', 1),
(40, '2022_04_20_153012_create_return_requests_table', 1),
(41, '2022_04_20_153129_create_sections_table', 1),
(42, '2022_04_20_153246_create_sellers_table', 1),
(43, '2022_04_20_154006_create_seller_commissions_table', 1),
(44, '2022_04_20_154147_create_seller_transactions_table', 1),
(45, '2022_04_20_154148_create_delivery_boy_transactions_table', 1),
(46, '2022_04_20_154343_create_seller_wallet_transactions_table', 1),
(47, '2022_04_20_154514_create_settings_table', 1),
(48, '2022_04_20_154550_create_sliders_table', 1),
(49, '2022_04_20_154642_create_social_media_table', 1),
(50, '2022_04_20_154726_create_sub_categories_table', 1),
(51, '2022_04_20_154923_create_taxes_table', 1),
(52, '2022_04_20_155025_create_time_slots_table', 1),
(53, '2022_04_20_155123_create_transactions_table', 1),
(54, '2022_04_20_155303_create_units_table', 1),
(55, '2022_04_20_155359_create_updates_table', 1),
(56, '2022_04_20_155800_create_user_addresses_table', 1),
(57, '2022_04_20_160100_create_wallet_transactions_table', 1),
(58, '2022_04_20_160235_create_withdrawal_requests_table', 1),
(59, '2022_05_23_061317_create_permission_categories_table', 1),
(60, '2022_05_23_165755_create_permission_tables', 1),
(61, '2022_06_04_070341_create_product_images_table', 1),
(62, '2022_06_04_103202_create_user_tokens_table', 1),
(63, '2022_07_05_174502_create_order_status_lists_table', 1),
(64, '2022_07_09_074747_create_panel_notifications_table', 1),
(65, '2022_08_16_180725_create_brands_table', 1),
(66, '2022_08_24_160823_create_countries_table', 1),
(67, '2022_10_01_055428_create_app_usages_table', 1),
(68, '2022_11_15_062504_create_sessions_table', 1),
(69, '2022_12_03_071819_add_remark_to_sellers_table', 1),
(70, '2022_12_03_071820_add_fssai_lic_no_to_sellers_table', 1),
(71, '2022_12_03_094442_add_remark_to_delivery_boys_table', 1),
(72, '2022_12_03_104000_create_mail_settings_table', 1),
(73, '2022_12_17_095005_create_admin_tokens_table', 1),
(74, '2022_12_27_113410_create_jobs_table', 1),
(75, '2022_19_01_060237_create_order_statuses_table', 1),
(76, '2023_01_23_122915_add_row_order_to_sections_table', 1),
(77, '2023_02_03_062618_add_type_link_to_notifications_table', 1),
(78, '2023_04_04_101932_add_bonus_fields_delivery_boys_table', 1),
(79, '2023_04_10_095427_add_delivery_boy_bonus_details_to_orders_table', 1),
(80, '2023_04_17_114556_add_remark_to_withdrawal_requests_table', 1),
(81, '2023_06_05_103829_create_supported_languages_table', 1),
(82, '2023_06_05_110120_create_languages_table', 1),
(83, '2023_07_12_091437_add_login_info_to_admins_table', 1),
(84, '2023_08_21_091438_add_fssai_lic_no_to_products_table', 1),
(85, '2023_08_25_091446_add_promo_code_id_to_orders_table', 1),
(86, '2023_08_25_091447_add_display_name_to_languages_table', 1),
(87, '2023_08_25_091448_make_alternate_mobile_nullable_in_user_addresses_table', 1),
(88, '2023_08_25_091449_add_reason_to_return_requests_table', 1),
(89, '2023_11_21_091448_create_product_ratings_table', 1),
(90, '2023_11_21_091449_create_rating_images_table', 1),
(91, '2023_11_22_091438_change_fssai_lic_no_to_sellers_table', 1),
(92, '2023_11_22_091439_change_balance_to_sellers_table', 1),
(93, '2024_02_08_071157_add_database_backup_to_permission_categories_table', 1),
(94, '2024_02_08_073229_add_database_backup_download_to_permissions_table', 1),
(95, '2024_02_13_073230_add_logo_to_countries_table', 1),
(96, '2024_02_13_073231_add_type_to_offers_table', 1),
(97, '2024_02_21_073231_add_transaction_to_wallet_transactions_table', 1),
(98, '2024_05_03_073231_chnage_transaction_to_wallet_transactions_table', 1),
(99, '2024_05_15_193349_update_category_slugs', 1),
(100, '2024_05_30_191339_add_receipt_image_to_withdrawal_requests_table', 1),
(101, '2024_06_05_185723_change_message_field_to_text_in_withdrawal_requests_table', 1),
(102, '2024_06_25_100707_add_platform_to_user_tokens_table', 1),
(103, '2024_06_25_130635_add_platform_to_admin_tokens_table', 1),
(104, '2024_07_13_115457_add_type_to_users_table', 1),
(105, '2024_07_17_164832_add_settings_login_data', 1),
(106, '2024_07_23_171435_create_tags_table', 1),
(107, '2024_07_23_182103_create_product_tag_table', 1),
(108, '2024_07_23_182126_populate_tags_and_product_tag_table', 1),
(109, '2024_07_29_111452_add_zone_to_cities_table', 1),
(110, '2024_07_29_174919_change_city_id_type_in_sellers_table', 1),
(111, '2024_08_11_142038_create_cart_notifications_table', 1),
(112, '2024_08_27_145716_create_sms_verifications_table', 1),
(113, '2024_08_31_115932_add_sms_status_to_mail_settings_table', 1),
(114, '2024_08_31_145736_create_sms_templates_table', 1),
(115, '2024_09_09_124731_remove_auth_id_from_users_table', 1),
(116, '2024_09_27_123118_add_fields_to_sections_table', 1),
(117, '2024_10_01_192703_create_live_tracking_table', 1),
(118, '2024_10_02_163103_alter_fixed_charge_and_per_km_charge_in_cities_table', 1),
(119, '2024_10_23_172210_add_email_verification_to_users_table', 1),
(120, '2024_10_28_160050_add_barcode_to_products_table', 1),
(121, '2025_01_09_145736_create_email_templates_table', 1),
(122, '2025_01_09_170143_create_emails_table', 1),
(123, '2025_02_05_225134_add_cancellation_reason_to_order_items_table', 1),
(124, '2025_03_03_191815_add_meta_fields_to_categories_table', 1),
(125, '2025_03_03_191815_add_meta_fields_to_products_table', 1),
(126, '2025_05_06_104350_create_pos_users_table', 1),
(127, '2025_05_06_104501_create_pos_orders_table', 1),
(128, '2025_05_06_104856_create_pos_order_items_table', 1),
(129, '2025_05_28_191221_create_web_seo_pages_table', 1),
(130, '2025_07_21_000000_add_additional_charges_to_orders_table', 1),
(131, '2025_07_28_add_original_total_to_orders_table', 1),
(132, '2025_07_29_add_remaining_final_to_orders_table', 1),
(133, '2025_08_13_000000_create_api_call_tracking_table', 1),
(134, '2025_10_13_165227_version_2_1_2', 1),
(135, '2025_10_24_165227_version_2_1_3', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletters`
--

CREATE TABLE `newsletters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(191) NOT NULL,
  `type_id` int(11) NOT NULL,
  `type_link` varchar(191) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('33b63cc361d43ea0674c8b4f6965f60f3d96a7c98ccd5e0e72e413d204515b211ccc726a7571d623', 1, 7, 'authToken', '[]', 0, '2026-01-19 16:43:13', '2026-01-19 16:43:13', '2027-01-19 16:43:13'),
('726926d4d90205a2599098bafe7584dd009772b34897ea1e122a266cacbf546e6da7027c39aba119', 1, 7, 'authToken', '[]', 0, '2026-01-19 09:39:42', '2026-01-19 09:39:42', '2027-01-19 09:39:42');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(191) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'eGrocer Personal Access Client', 'GEfotwOZpZthPixaQL20ykVlfCKvsg3s3wC6zy9C', NULL, 'http://localhost', 1, 0, 0, '2026-01-19 09:33:58', '2026-01-19 09:33:58'),
(2, NULL, 'eGrocer Password Grant Client', 'DVgCM2Ymz1Qch5ASIKxBWsyltziNIKkP3jaLRkOJ', 'users', 'http://localhost', 0, 1, 0, '2026-01-19 09:33:58', '2026-01-19 09:33:58'),
(3, NULL, 'eGrocer Personal Access Client', 'rI6Z8nYLjqemika5YHmscQNo2eXdBHwHzi6cIijd', NULL, 'http://localhost', 1, 0, 0, '2026-01-19 09:35:15', '2026-01-19 09:35:15'),
(4, NULL, 'eGrocer Password Grant Client', '22wyCPJcX9AbQUUAYXvskYnL82oa33OHrMastgY6', 'users', 'http://localhost', 0, 1, 0, '2026-01-19 09:35:15', '2026-01-19 09:35:15'),
(5, NULL, 'eGrocer Personal Access Client', 'B3pNRmvtAUuH70LqAAs6v7YJi2uTP4y6TCemANEo', NULL, 'http://localhost', 1, 0, 0, '2026-01-19 09:35:28', '2026-01-19 09:35:28'),
(6, NULL, 'eGrocer Password Grant Client', '9P1TT7mWZlAHuXVPd85GldatsZGxwbQZwRNbazJz', 'users', 'http://localhost', 0, 1, 0, '2026-01-19 09:35:28', '2026-01-19 09:35:28'),
(7, NULL, 'eGrocer Personal Access Client', 'WOB8eQR9XmWDa8LDHm9dWoXzeKhZWP6FVF8z8wto', NULL, 'http://localhost', 1, 0, 0, '2026-01-19 09:36:51', '2026-01-19 09:36:51'),
(8, NULL, 'eGrocer Password Grant Client', 'i6aPeulpI5GRZwqrrBHB9j6qGLBZ4h26w8iD1bPs', 'users', 'http://localhost', 0, 1, 0, '2026-01-19 09:36:51', '2026-01-19 09:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-01-19 09:33:58', '2026-01-19 09:33:58'),
(2, 3, '2026-01-19 09:35:15', '2026-01-19 09:35:15'),
(3, 5, '2026-01-19 09:35:28', '2026-01-19 09:35:28'),
(4, 7, '2026-01-19 09:36:51', '2026-01-19 09:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(191) NOT NULL,
  `position` varchar(191) NOT NULL,
  `section_position` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `type_id` varchar(191) NOT NULL,
  `offer_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_boy_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_boy_bonus_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Delivery boy bonus Details for bonus commission amount' CHECK (json_valid(`delivery_boy_bonus_details`)),
  `delivery_boy_bonus_amount` double DEFAULT NULL,
  `transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `orders_id` varchar(191) DEFAULT NULL,
  `otp` int(11) DEFAULT NULL,
  `mobile` varchar(191) NOT NULL,
  `order_note` text DEFAULT NULL,
  `total` double(8,2) NOT NULL,
  `delivery_charge` double(8,2) NOT NULL,
  `tax_amount` double(8,2) NOT NULL DEFAULT 0.00,
  `tax_percentage` double(8,2) NOT NULL DEFAULT 0.00,
  `wallet_balance` double(8,2) NOT NULL,
  `discount` double(8,2) NOT NULL DEFAULT 0.00,
  `additional_charges` text DEFAULT NULL,
  `promo_code_id` int(11) NOT NULL DEFAULT 0,
  `promo_code` varchar(191) DEFAULT NULL,
  `promo_discount` double(8,2) NOT NULL DEFAULT 0.00,
  `final_total` double(8,2) DEFAULT NULL,
  `payment_method` varchar(191) NOT NULL,
  `address` text NOT NULL,
  `latitude` varchar(191) NOT NULL,
  `longitude` varchar(191) NOT NULL,
  `delivery_time` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL,
  `active_status` varchar(191) NOT NULL,
  `order_type` enum('doorstep','selfpickup') NOT NULL DEFAULT 'doorstep',
  `pickup_address` text DEFAULT NULL,
  `order_from` int(11) DEFAULT 0,
  `pincode_id` int(11) DEFAULT 0,
  `address_id` int(11) NOT NULL DEFAULT 0,
  `area_id` int(11) DEFAULT NULL,
  `remaining_total` double(8,2) DEFAULT NULL,
  `remaining_final` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_bank_transfers`
--

CREATE TABLE `order_bank_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(11) NOT NULL,
  `attachment` longtext NOT NULL,
  `message` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `orders_id` varchar(191) NOT NULL,
  `product_name` text DEFAULT NULL,
  `variant_name` text DEFAULT NULL,
  `product_variant_id` int(11) NOT NULL,
  `delivery_boy_id` int(11) DEFAULT 0,
  `quantity` int(11) NOT NULL,
  `price` double(8,2) NOT NULL,
  `discounted_price` double NOT NULL,
  `tax_amount` double(8,2) NOT NULL DEFAULT 0.00,
  `tax_percentage` double(8,2) NOT NULL DEFAULT 0.00,
  `discount` double(8,2) NOT NULL DEFAULT 0.00,
  `sub_total` double(8,2) NOT NULL,
  `refund_amount` double(8,2) DEFAULT NULL,
  `status` varchar(191) NOT NULL,
  `active_status` varchar(191) NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `seller_id` int(11) NOT NULL,
  `is_credited` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` varchar(191) NOT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `status` varchar(191) NOT NULL,
  `created_by` int(11) NOT NULL COMMENT '0 - Script, if not 0 id of related table',
  `user_type` int(11) NOT NULL COMMENT '0 - Script, 1 - Admin, 2 - User',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_status_lists`
--

CREATE TABLE `order_status_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_status_lists`
--

INSERT INTO `order_status_lists` (`id`, `status`) VALUES
(1, 'Payment Pending'),
(2, 'Received'),
(3, 'Processed'),
(4, 'Shipped'),
(5, 'Out For Delivery'),
(6, 'Delivered'),
(7, 'Cancelled'),
(8, 'Returned'),
(9, 'Pending'),
(10, 'Ready for Pickup'),
(11, 'Picked Up');

-- --------------------------------------------------------

--
-- Table structure for table `order_trackings`
--

CREATE TABLE `order_trackings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `shiprocket_order_id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `courier_company_id` int(11) DEFAULT NULL,
  `awb_code` varchar(191) DEFAULT NULL,
  `tracking_url` varchar(191) DEFAULT NULL,
  `pickup_status` int(11) NOT NULL,
  `pickup_scheduled_date` varchar(191) NOT NULL,
  `pickup_token_number` varchar(191) NOT NULL,
  `status` int(11) NOT NULL,
  `others` varchar(191) NOT NULL,
  `pickup_generated_date` varchar(191) NOT NULL,
  `data` varchar(191) NOT NULL,
  `date` varchar(191) NOT NULL,
  `is_canceled` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `panel_notifications`
--

CREATE TABLE `panel_notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `txnid` varchar(191) NOT NULL,
  `payment_amount` decimal(8,2) NOT NULL,
  `payment_status` varchar(191) NOT NULL,
  `itemid` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_type` varchar(191) NOT NULL,
  `payment_address` varchar(191) NOT NULL,
  `amount_requested` int(11) NOT NULL,
  `remarks` varchar(191) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'manage_dashboard', 'web', 1, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(2, 'order_list', 'web', 2, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(3, 'order_update', 'web', 2, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(4, 'order_delete', 'web', 2, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(5, 'self_pickup_order_list', 'web', 2, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(6, 'self_pickup_order_update', 'web', 2, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(7, 'self_pickup_order_delete', 'web', 2, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(8, 'category_list', 'web', 3, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(9, 'category_create', 'web', 3, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(10, 'category_update', 'web', 3, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(11, 'category_delete', 'web', 3, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(12, 'manage_categories_order', 'web', 3, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(13, 'product_list', 'web', 4, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(14, 'product_create', 'web', 4, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(15, 'product_update', 'web', 4, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(16, 'product_delete', 'web', 4, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(17, 'manage_media', 'web', 4, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(18, 'manage_product_bulk_upload', 'web', 4, '2026-01-19 09:33:48', '2026-01-19 09:33:48'),
(19, 'manage_product_order', 'web', 4, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(20, 'approve_requests', 'web', 4, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(21, 'product_ratings', 'web', 4, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(22, 'taxes', 'web', 4, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(23, 'brands', 'web', 4, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(24, 'stock_management', 'web', 4, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(25, 'seller_list', 'web', 5, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(26, 'seller_create', 'web', 5, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(27, 'seller_update', 'web', 5, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(28, 'seller_delete', 'web', 5, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(29, 'seller_requests', 'web', 5, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(30, 'seller_wallet_transactions', 'web', 5, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(31, 'home_slider_image_list', 'web', 6, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(32, 'home_slider_image_create', 'web', 6, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(33, 'home_slider_image_update', 'web', 6, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(34, 'home_slider_image_delete', 'web', 6, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(35, 'new_offer_image_list', 'web', 7, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(36, 'new_offer_image_create', 'web', 7, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(37, 'new_offer_image_update', 'web', 7, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(38, 'new_offer_image_delete', 'web', 7, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(39, 'promo_code_list', 'web', 8, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(40, 'promo_code_create', 'web', 8, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(41, 'promo_code_update', 'web', 8, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(42, 'promo_code_delete', 'web', 8, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(43, 'return_request_list', 'web', 9, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(44, 'return_request_update', 'web', 9, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(45, 'return_request_delete', 'web', 9, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(46, 'withdrawal_request_list', 'web', 10, '2026-01-19 09:33:49', '2026-01-19 09:33:49'),
(47, 'withdrawal_request_update', 'web', 10, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(48, 'withdrawal_request_delete', 'web', 10, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(49, 'delivery_boy_list', 'web', 11, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(50, 'delivery_boy_create', 'web', 11, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(51, 'delivery_boy_update', 'web', 11, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(52, 'delivery_boy_delete', 'web', 11, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(53, 'fund_transfers_list', 'web', 11, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(54, 'fund_transfers_create', 'web', 11, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(55, 'cash_collection_list', 'web', 11, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(56, 'cash_collection_create', 'web', 11, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(57, 'notification_list', 'web', 12, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(58, 'notification_create', 'web', 12, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(59, 'notification_delete', 'web', 12, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(60, 'email_templates', 'web', 13, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(61, 'create_email_template', 'web', 13, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(62, 'delete_email_template', 'web', 13, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(63, 'manage_emails', 'web', 13, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(64, 'create_email', 'web', 13, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(65, 'delete_email', 'web', 13, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(66, 'manage_time_slots', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(67, 'time_slot_create', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(68, 'time_slot_update', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(69, 'time_slot_delete', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(70, 'manage_store_settings', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(71, 'manage_units', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(72, 'unit_create', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(73, 'unit_update', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(74, 'manage_payment_methods', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(75, 'manage_Notification_settings', 'web', 14, '2026-01-19 09:33:50', '2026-01-19 09:33:50'),
(76, 'manage_contact_us', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(77, 'manage_about_us', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(78, 'manage_privacy_policy', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(79, 'manage_privacy_policy_delivery_boy', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(80, 'manage_privacy_policy_manager_app', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(81, 'manage_privacy_policy_seller_app', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(82, 'manage_secret_key', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(83, 'manage_shipping_methods', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(84, 'manage_system_registration', 'web', 14, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(85, 'general_settings', 'web', 15, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(86, 'manage_social_media_list', 'web', 15, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(87, 'manage_social_media_create', 'web', 15, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(88, 'manage_social_media_delete', 'web', 15, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(89, 'manage_social_media_update', 'web', 15, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(90, 'blog_category_list', 'web', 16, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(91, 'blog_category_create', 'web', 16, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(92, 'blog_category_update', 'web', 16, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(93, 'blog_category_delete', 'web', 16, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(94, 'blog_list', 'web', 16, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(95, 'blog_create', 'web', 16, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(96, 'blog_update', 'web', 16, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(97, 'blog_delete', 'web', 16, '2026-01-19 09:33:51', '2026-01-19 09:33:51'),
(98, 'language_list', 'web', 22, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(99, 'language_create', 'web', 22, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(100, 'language_update', 'web', 22, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(101, 'language_delete', 'web', 22, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(102, 'country_list', 'web', 23, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(103, 'country_create', 'web', 23, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(104, 'country_update', 'web', 23, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(105, 'country_delete', 'web', 23, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(106, 'city_list', 'web', 17, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(107, 'city_create', 'web', 17, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(108, 'city_update', 'web', 17, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(109, 'city_delete', 'web', 17, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(110, 'manage_deliverable_area', 'web', 17, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(111, 'featured_section_list', 'web', 18, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(112, 'featured_section_create', 'web', 18, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(113, 'featured_section_update', 'web', 18, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(114, 'featured_section_delete', 'web', 18, '2026-01-19 09:33:52', '2026-01-19 09:33:52'),
(115, 'customer_list', 'web', 19, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(116, 'customer_update', 'web', 19, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(117, 'manage_wishlists', 'web', 19, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(118, 'transaction_list', 'web', 19, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(119, 'manage_customer_wallet', 'web', 19, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(120, 'product_request_list', 'web', 19, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(121, 'product_request_update', 'web', 19, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(122, 'product_sales_reports', 'web', 20, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(123, 'sales_reports', 'web', 20, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(124, 'faq_list', 'web', 21, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(125, 'faq_create', 'web', 21, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(126, 'faq_update', 'web', 21, '2026-01-19 09:33:53', '2026-01-19 09:33:53'),
(127, 'faq_delete', 'web', 21, '2026-01-19 09:33:53', '2026-01-19 09:33:53');

-- --------------------------------------------------------

--
-- Table structure for table `permission_categories`
--

CREATE TABLE `permission_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_categories`
--

INSERT INTO `permission_categories` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard', 'web', NULL, NULL),
(2, 'order', 'web', NULL, NULL),
(3, 'category', 'web', NULL, NULL),
(4, 'product', 'web', NULL, NULL),
(5, 'seller', 'web', NULL, NULL),
(6, 'home_slider_image', 'web', NULL, NULL),
(7, 'new_offer_image', 'web', NULL, NULL),
(8, 'promo_code', 'web', NULL, NULL),
(9, 'return_request', 'web', NULL, NULL),
(10, 'withdrawal_request', 'web', NULL, NULL),
(11, 'delivery_boy', 'web', NULL, NULL),
(12, 'send_notification', 'web', NULL, NULL),
(13, 'email_template', 'web', NULL, NULL),
(14, 'system', 'web', NULL, NULL),
(15, 'web_settings', 'web', NULL, NULL),
(16, 'blogs', 'web', NULL, NULL),
(17, 'location', 'web', NULL, NULL),
(18, 'featured_section', 'web', NULL, NULL),
(19, 'customer', 'web', NULL, NULL),
(20, 'report', 'web', NULL, NULL),
(21, 'faq', 'web', NULL, NULL),
(22, 'languages', 'web', NULL, NULL),
(23, 'countries', 'web', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pickup_locations`
--

CREATE TABLE `pickup_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) NOT NULL,
  `pickup_location` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `address` text NOT NULL,
  `address_2` text NOT NULL,
  `city` varchar(191) NOT NULL,
  `state` varchar(191) NOT NULL,
  `country` varchar(191) NOT NULL,
  `pin_code` varchar(191) NOT NULL,
  `latitude` varchar(191) NOT NULL,
  `longitude` varchar(191) NOT NULL,
  `verified` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pincodes`
--

CREATE TABLE `pincodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pincode` varchar(191) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_additional_charges`
--

CREATE TABLE `pos_additional_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pos_order_id` int(11) NOT NULL,
  `charge_name` varchar(191) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_orders`
--

CREATE TABLE `pos_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pos_user_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_order_items`
--

CREATE TABLE `pos_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pos_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_users`
--

CREATE TABLE `pos_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `row_order` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `tags` varchar(191) DEFAULT NULL,
  `tax_id` tinyint(4) DEFAULT 0,
  `brand_id` int(11) DEFAULT 0,
  `slug` varchar(191) NOT NULL,
  `category_id` int(11) NOT NULL,
  `indicator` tinyint(4) DEFAULT NULL COMMENT '0 - none | 1 - veg | 2 - non-veg',
  `manufacturer` varchar(191) DEFAULT NULL,
  `made_in` varchar(191) DEFAULT NULL,
  `return_status` tinyint(4) DEFAULT NULL,
  `cancelable_status` tinyint(4) DEFAULT NULL,
  `till_status` varchar(191) DEFAULT NULL,
  `image` text NOT NULL,
  `other_images` varchar(191) DEFAULT NULL,
  `description` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `is_approved` int(11) DEFAULT NULL,
  `return_days` int(11) NOT NULL DEFAULT 0,
  `type` text DEFAULT NULL,
  `is_unlimited_stock` int(11) NOT NULL DEFAULT 0 COMMENT '0 = Limited & 1 = Unlimited',
  `cod_allowed` tinyint(4) NOT NULL,
  `total_allowed_quantity` int(11) NOT NULL,
  `tax_included_in_price` tinyint(4) NOT NULL DEFAULT 0,
  `fssai_lic_no` varchar(191) NOT NULL,
  `barcode` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `schema_markup` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL DEFAULT 0,
  `image` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rate` tinyint(4) NOT NULL,
  `review` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_tag`
--

CREATE TABLE `product_tag` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '(1: Available, 0: Sold Out)',
  `measurement` double(8,2) NOT NULL,
  `price` double(11,2) NOT NULL,
  `discounted_price` double(11,2) NOT NULL DEFAULT 0.00,
  `stock` double(11,2) NOT NULL DEFAULT 0.00,
  `stock_unit_id` int(11) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `promo_code` varchar(191) NOT NULL,
  `message` varchar(191) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `no_of_users` int(11) NOT NULL,
  `minimum_order_amount` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `discount_type` varchar(191) NOT NULL,
  `max_discount_amount` int(11) NOT NULL,
  `repeat_usage` tinyint(4) NOT NULL COMMENT '1-allowed, 0-Not Allowed',
  `no_of_repeat_usage` int(11) NOT NULL DEFAULT 0 COMMENT 'if repeat_usage = allowed(1) else NULL',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-active, 0-deactive',
  `image` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rating_images`
--

CREATE TABLE `rating_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_rating_id` int(11) NOT NULL,
  `image` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_requests`
--

CREATE TABLE `return_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `delivery_boy_id` int(11) NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', NULL, NULL),
(2, 'Admin', 'web', NULL, NULL),
(3, 'Seller', 'web', NULL, NULL),
(4, 'Delivery Boy', 'web', NULL, NULL);

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
(2, 4),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(5, 1),
(5, 2),
(5, 3),
(6, 1),
(6, 2),
(6, 3),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(8, 3),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(13, 3),
(14, 1),
(14, 2),
(14, 3),
(15, 1),
(15, 2),
(15, 3),
(16, 1),
(16, 2),
(16, 3),
(17, 1),
(17, 2),
(17, 3),
(18, 1),
(18, 2),
(18, 3),
(19, 1),
(19, 2),
(19, 3),
(20, 1),
(20, 2),
(20, 3),
(21, 1),
(21, 2),
(21, 3),
(22, 1),
(22, 2),
(22, 3),
(23, 1),
(23, 2),
(23, 3),
(24, 1),
(24, 2),
(24, 3),
(25, 1),
(25, 2),
(26, 1),
(26, 2),
(27, 1),
(27, 2),
(28, 1),
(28, 2),
(29, 1),
(29, 2),
(30, 1),
(30, 2),
(31, 1),
(31, 2),
(32, 1),
(32, 2),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(38, 1),
(38, 2),
(39, 1),
(39, 2),
(40, 1),
(40, 2),
(41, 1),
(41, 2),
(42, 1),
(42, 2),
(43, 1),
(43, 2),
(43, 3),
(43, 4),
(44, 1),
(44, 2),
(44, 3),
(44, 4),
(45, 1),
(45, 2),
(45, 3),
(46, 1),
(46, 2),
(47, 1),
(47, 2),
(48, 1),
(48, 2),
(49, 1),
(49, 2),
(50, 1),
(50, 2),
(51, 1),
(51, 2),
(52, 1),
(52, 2),
(53, 1),
(53, 2),
(54, 1),
(54, 2),
(55, 1),
(55, 2),
(56, 1),
(56, 2),
(57, 1),
(57, 2),
(58, 1),
(58, 2),
(59, 1),
(59, 2),
(60, 1),
(60, 2),
(61, 1),
(61, 2),
(62, 1),
(62, 2),
(63, 1),
(63, 2),
(64, 1),
(64, 2),
(65, 1),
(65, 2),
(66, 1),
(66, 2),
(67, 1),
(67, 2),
(68, 1),
(68, 2),
(69, 1),
(69, 2),
(70, 1),
(70, 2),
(71, 1),
(71, 2),
(72, 1),
(72, 2),
(73, 1),
(73, 2),
(74, 1),
(74, 2),
(75, 1),
(75, 2),
(76, 1),
(76, 2),
(77, 1),
(77, 2),
(78, 1),
(78, 2),
(79, 1),
(79, 2),
(80, 1),
(80, 2),
(81, 1),
(81, 2),
(82, 1),
(82, 2),
(83, 1),
(83, 2),
(84, 1),
(84, 2),
(85, 1),
(85, 2),
(86, 1),
(86, 2),
(87, 1),
(87, 2),
(88, 1),
(88, 2),
(89, 1),
(89, 2),
(90, 1),
(90, 2),
(91, 1),
(91, 2),
(92, 1),
(92, 2),
(93, 1),
(93, 2),
(94, 1),
(94, 2),
(95, 1),
(95, 2),
(96, 1),
(96, 2),
(97, 1),
(97, 2),
(98, 1),
(98, 2),
(99, 1),
(99, 2),
(100, 1),
(100, 2),
(101, 1),
(101, 2),
(102, 1),
(102, 2),
(103, 1),
(103, 2),
(104, 1),
(104, 2),
(105, 1),
(105, 2),
(106, 1),
(106, 2),
(107, 1),
(107, 2),
(108, 1),
(108, 2),
(109, 1),
(109, 2),
(110, 1),
(110, 2),
(111, 1),
(111, 2),
(112, 1),
(112, 2),
(113, 1),
(113, 2),
(114, 1),
(114, 2),
(115, 1),
(115, 2),
(116, 1),
(116, 2),
(117, 1),
(117, 2),
(118, 1),
(118, 2),
(119, 1),
(119, 2),
(120, 1),
(120, 2),
(121, 1),
(121, 2),
(122, 1),
(122, 2),
(122, 3),
(122, 4),
(123, 1),
(123, 2),
(123, 3),
(123, 4),
(124, 1),
(124, 2),
(125, 1),
(125, 2),
(126, 1),
(126, 2),
(127, 1),
(127, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `title` varchar(191) NOT NULL,
  `short_description` varchar(191) NOT NULL,
  `product_type` varchar(191) NOT NULL,
  `product_ids` text DEFAULT NULL,
  `category_ids` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `position` varchar(191) NOT NULL,
  `style_app` varchar(191) NOT NULL,
  `banner_app` varchar(191) DEFAULT NULL,
  `style_web` varchar(191) NOT NULL,
  `banner_web` varchar(191) DEFAULT NULL,
  `background_color_for_light_theme` varchar(191) NOT NULL,
  `background_color_for_dark_theme` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `store_name` text DEFAULT NULL,
  `slug` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `mobile` text DEFAULT NULL,
  `balance` double NOT NULL DEFAULT 0,
  `store_url` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `store_description` text DEFAULT NULL,
  `street` text DEFAULT NULL,
  `pincode_id` int(11) DEFAULT NULL,
  `city_id` text DEFAULT NULL,
  `state` text DEFAULT NULL,
  `categories` text DEFAULT NULL,
  `account_number` text DEFAULT NULL,
  `bank_ifsc_code` text DEFAULT NULL,
  `account_name` text DEFAULT NULL,
  `bank_name` text DEFAULT NULL,
  `commission` int(11) DEFAULT 0,
  `status` tinyint(4) NOT NULL,
  `require_products_approval` tinyint(4) NOT NULL DEFAULT 0,
  `fcm_id` text DEFAULT NULL,
  `national_identity_card` text DEFAULT NULL,
  `address_proof` text DEFAULT NULL,
  `pan_number` text DEFAULT NULL,
  `tax_name` text DEFAULT NULL,
  `tax_number` text DEFAULT NULL,
  `customer_privacy` tinyint(4) DEFAULT 0,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `place_name` varchar(191) DEFAULT NULL,
  `formatted_address` varchar(191) DEFAULT NULL,
  `forgot_password_code` varchar(191) DEFAULT NULL,
  `view_order_otp` tinyint(4) NOT NULL DEFAULT 0,
  `assign_delivery_boy` tinyint(4) NOT NULL DEFAULT 0,
  `fssai_lic_no` varchar(191) DEFAULT NULL,
  `self_pickup_mode` tinyint(4) NOT NULL DEFAULT 0,
  `door_step_mode` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Enabled, 0=Disabled',
  `pickup_store_address` text DEFAULT NULL,
  `pickup_latitude` varchar(191) DEFAULT NULL,
  `pickup_longitude` varchar(191) DEFAULT NULL,
  `pickup_store_timings` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `change_order_status_delivered` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller_commissions`
--

CREATE TABLE `seller_commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `commission` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller_transactions`
--

CREATE TABLE `seller_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `type` text DEFAULT NULL,
  `txn_id` text DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `status` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller_wallet_transactions`
--

CREATE TABLE `seller_wallet_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `type` varchar(191) DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `message` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `variable` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `variable`, `value`) VALUES
(1, 'app_name', 'eGrocer'),
(2, 'support_number', ''),
(3, 'support_email', 'support@gmail.com'),
(4, 'logo', ''),
(5, 'purchase_code', ''),
(6, 'stripe_secret_key', ''),
(7, 'stripe_publishable_key', ''),
(8, 'stripe_webhook_secret_key', ''),
(9, 'currency', '₹'),
(10, 'currency_code', 'INR'),
(11, 'decimal_point', '2'),
(12, 'cod_payment_method', '1'),
(13, 'cod_mode', 'global');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `type_id` varchar(191) NOT NULL,
  `image` varchar(191) NOT NULL,
  `slider_url` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-active, 0-deactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_templates`
--

CREATE TABLE `sms_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sms_templates`
--

INSERT INTO `sms_templates` (`id`, `title`, `message`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Your Order Has Been Placed - Order #{order.id}', 'Dear [Customer Name], your order #[Order ID] has been successfully placed. You will receive updates as your order is processed. Thank you for shopping with us!', 'customer_place_order', '2026-01-19 09:33:26', NULL),
(2, 'Payment Pending for Order #{order.id}', 'Dear [Customer Name], your order [Order ID] is awaiting payment. Please complete the payment to process your order. If you need assistance, contact [Support Contact].', 'customer_order_payment_pending', '2026-01-19 09:33:26', NULL),
(3, 'Your Order Has Been Received - Order #{order.id}', 'Dear [Customer Name], we have received your order [Order ID] and it\'s now being processed. You will receive updates on shipping soon. Thank you for choosing [Store Name]!', 'customer_order_received', '2026-01-19 09:33:26', NULL),
(4, 'Your Order Has Been Processed - Order #{order.id}', 'Dear [Customer Name], your order [Order ID] has been processed and is ready for shipment. You will be notified once it\'s on the way. Thank you for shopping with us!', 'customer_order_processed', '2026-01-19 09:33:26', NULL),
(5, 'Your Order Has Been Shipped - Order #{order.id}', 'Dear [Customer Name], your order [Order ID] has been shipped. Thank you for choosing [Store Name]!', 'customer_order_shipped', '2026-01-19 09:33:26', NULL),
(6, 'Your Order is Out for Delivery - Order #{order.id}', 'Dear [Customer Name], your order [Order ID] is out for delivery and will reach you soon. Please be available to receive your package. Thank you!', 'customer_order_out_for_delivery', '2026-01-19 09:33:26', NULL),
(7, 'Your Order Has Been Delivered - Order #{order.id}', 'Dear [Customer Name], your order [Order ID] has been successfully delivered. We hope you enjoy your purchase. Thank you for shopping at [Store Name]!', 'customer_order_delivered', '2026-01-19 09:33:26', NULL),
(8, 'Your Order Has Been Cancelled - Order #{order.id}', 'Dear [Customer Name], your order Item [Product Name] from Order [Order ID] has been cancelled. If you have any questions or wish to place a new order, please contact [Support Contact].', 'customer_order_cancelled', '2026-01-19 09:33:26', NULL),
(9, 'Return Request Received for Order #{order.id}', 'Dear [Customer Name], we have received your return request for order Item [Product Name] from Order [Order ID]. Our team will review it and get back to you shortly with the next steps.', 'customer_order_return_request', '2026-01-19 09:33:26', NULL),
(10, 'Return Request Approved - Order #{order.id}', 'Dear [Customer Name], your return request for order Item [Product Name] from Order [Order ID] has been approved. The refund amount will be credited to your wallet. Thank you!', 'customer_order_confirm_return_request', '2026-01-19 09:33:26', NULL),
(11, 'Return Request Rejected - Order #{order.id}', 'Dear [Customer Name], your return request for order Item [Product Name] from Order [Order ID] has been rejected. Reason: [Reason]. If you have any questions, please contact our support team. Thank you for understanding.', 'customer_order_reject_return_request', '2026-01-19 09:33:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sms_verifications`
--

CREATE TABLE `sms_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(191) NOT NULL,
  `otp` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_media`
--

CREATE TABLE `social_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `icon` text NOT NULL,
  `link` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `stripe_id` varchar(191) NOT NULL,
  `stripe_status` varchar(191) NOT NULL,
  `stripe_price` varchar(191) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_items`
--

CREATE TABLE `subscription_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `stripe_id` varchar(191) NOT NULL,
  `stripe_product` varchar(191) NOT NULL,
  `stripe_price` varchar(191) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `row_order` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `subtitle` text NOT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supported_languages`
--

CREATE TABLE `supported_languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `code` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supported_languages`
--

INSERT INTO `supported_languages` (`id`, `name`, `code`, `type`) VALUES
(1, 'Afrikaans', 'af', 'ltr'),
(2, 'Amharic', 'am', 'ltr'),
(3, 'Arabic', 'ar', 'rtl'),
(4, 'Assamese', 'as', 'ltr'),
(5, 'Azerbaijani', 'az', 'ltr'),
(6, 'Belarusian', 'be', 'ltr'),
(7, 'Bulgarian', 'bg', 'ltr'),
(8, 'Bengali (Bangla)', 'bn', 'ltr'),
(9, 'Bosnian', 'bs', 'ltr'),
(10, 'Catalan (Valencian)', 'ca', 'ltr'),
(11, 'Czech', 'cs', 'ltr'),
(12, 'Welsh', 'cy', 'ltr'),
(13, 'Danish', 'da', 'ltr'),
(14, 'German', 'de', 'ltr'),
(15, 'Modern Greek', 'el', 'ltr'),
(16, 'English', 'en', 'ltr'),
(17, 'Spanish (Castilian)', 'es', 'ltr'),
(18, 'Estonian', 'et', 'ltr'),
(19, 'Basque', 'eu', 'ltr'),
(20, 'Persian', 'fa', 'rtl'),
(21, 'Finnish', 'fi', 'ltr'),
(22, 'Filipino (Pilipino)', 'fil', 'ltr'),
(23, 'French', 'fr', 'ltr'),
(24, 'Galician', 'gl', 'ltr'),
(25, 'Swiss German (Alemannic, Alsatian)', 'gsw', 'ltr'),
(26, 'Gujarati', 'gu', 'ltr'),
(27, 'Hebrew', 'he', 'rtl'),
(28, 'Hindi', 'hi', 'ltr'),
(29, 'Croatian', 'hr', 'ltr'),
(30, 'Hungarian', 'hu', 'ltr'),
(31, 'Armenian', 'hy', 'ltr'),
(32, 'Indonesian', 'id', 'ltr'),
(33, 'Icelandic', 'is', 'ltr'),
(34, 'Italian', 'it', 'ltr'),
(35, 'Japanese', 'ja', 'ltr'),
(36, 'Georgian', 'ka', 'ltr'),
(37, 'Kazakh', 'kk', 'ltr'),
(38, 'Khmer (Central Khmer)', 'km', 'ltr'),
(39, 'Kannada', 'kn', 'ltr'),
(40, 'Korean', 'ko', 'ltr'),
(41, 'Kirghiz (Kyrgyz)', 'ky', 'ltr'),
(42, 'Lao', 'lo', 'ltr'),
(43, 'Lithuanian', 'lt', 'ltr'),
(44, 'Latvian', 'lv', 'ltr'),
(45, 'Macedonian', 'mk', 'ltr'),
(46, 'Malayalam', 'ml', 'ltr'),
(47, 'Mongolian', 'mn', 'ltr'),
(48, 'Marathi', 'mr', 'ltr'),
(49, 'Malay', 'ms', 'ltr'),
(50, 'Burmese', 'my', 'ltr'),
(51, 'Norwegian Bokmål', 'nb', 'ltr'),
(52, 'Nepali', 'ne', 'ltr'),
(53, 'Dutch (Flemish)', 'nl', 'ltr'),
(54, 'Norwegian', 'no', 'ltr'),
(55, 'Oriya', 'or', 'ltr'),
(56, 'Panjabi (Punjabi)', 'pa', 'ltr'),
(57, 'Polish', 'pl', 'ltr'),
(58, 'Pushto (Pashto)', 'ps', 'rtl'),
(59, 'Portuguese', 'pt', 'ltr'),
(60, 'Romanian (Moldavian, Moldovan)', 'ro', 'ltr'),
(61, 'Russian', 'ru', 'ltr'),
(62, 'Sinhala (Sinhalese)', 'si', 'ltr'),
(63, 'Slovak', 'sk', 'ltr'),
(64, 'Slovenian', 'sl', 'ltr'),
(65, 'Albanian', 'sq', 'ltr'),
(66, 'Serbian', 'sr', 'ltr'),
(67, 'Swedish', 'sv', 'ltr'),
(68, 'Swahili', 'sw', 'ltr'),
(69, 'Tamil', 'ta', 'ltr'),
(70, 'Telugu', 'te', 'ltr'),
(71, 'Thai', 'th', 'ltr'),
(72, 'Tagalog', 'tl', 'ltr'),
(73, 'Turkish', 'tr', 'ltr'),
(74, 'Ukrainian', 'uk', 'ltr'),
(75, 'Urdu', 'ur', 'rtl'),
(76, 'Uzbek', 'uz', 'ltr'),
(77, 'Vietnamese', 'vi', 'ltr'),
(78, 'Chinese', 'zh', 'ltr'),
(79, 'Zulu', 'zu', 'ltr');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` text DEFAULT NULL,
  `percentage` double NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  `last_order_time` time NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-active, 0-deactive',
  `is_free_delivery` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `txn_id` varchar(191) NOT NULL,
  `payu_txn_id` varchar(191) DEFAULT NULL,
  `amount` double NOT NULL,
  `status` varchar(191) NOT NULL,
  `message` varchar(191) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `short_code` varchar(191) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `conversion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE `updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `email_verification_code` varchar(191) DEFAULT NULL,
  `profile` varchar(191) DEFAULT NULL,
  `country_code` varchar(191) NOT NULL DEFAULT '91',
  `mobile` varchar(191) NOT NULL,
  `balance` double NOT NULL DEFAULT 0,
  `referral_code` varchar(191) DEFAULT NULL,
  `friends_code` varchar(191) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `stripe_id` varchar(191) DEFAULT NULL,
  `pm_type` varchar(191) DEFAULT NULL,
  `pm_last_four` varchar(4) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `type` enum('email','google','apple','phone') NOT NULL DEFAULT 'phone'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `mobile` varchar(191) NOT NULL,
  `alternate_mobile` varchar(191) DEFAULT NULL,
  `address` text NOT NULL,
  `landmark` text NOT NULL,
  `area` varchar(191) NOT NULL,
  `pincode` varchar(191) NOT NULL,
  `city_id` varchar(191) NOT NULL,
  `city` varchar(191) NOT NULL,
  `state` varchar(191) NOT NULL,
  `country` varchar(191) NOT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT 0,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_product_requests`
--

CREATE TABLE `user_product_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `fcm_token` varchar(255) NOT NULL,
  `platform` varchar(191) NOT NULL DEFAULT 'web'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_tokens`
--

INSERT INTO `user_tokens` (`id`, `user_id`, `type`, `fcm_token`, `platform`) VALUES
(1, 0, 'customer', 'ca2-tuIFQ26ktQwj8BurKT:APA91bF7g4J5MV_l2WqeS6MruruJrR40iTLcNTNVHMjtuEkzuBlDM6mSVJ9w__RBPuVgWl_XWKWf-UP8CbpgfixOUwMs8M5WJ6MYmHBK4q3PjBWi4k57D48', 'android'),
(2, 0, 'customer', 'dLSn-gMjQCuhY3mXXw1f0C:APA91bGOdxAFMxEKJb77IeXGxeSlqyViZTF55QpDGEZ7tj_Cp4bO-rhhqfTQe8v5VzBy28LM7_W3tzNrc_OktZgmv9Tzmy2RH5MR7lgXoi7NM3fNeWr0taA', 'android');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(191) NOT NULL,
  `amount` double NOT NULL,
  `txn_id` varchar(191) DEFAULT NULL,
  `payment_type` varchar(191) DEFAULT NULL,
  `transaction_date` datetime NOT NULL DEFAULT '2026-01-19 09:33:25',
  `message` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_seo_pages`
--

CREATE TABLE `web_seo_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meta_title` varchar(191) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keyword` text DEFAULT NULL,
  `schema_markup` longtext DEFAULT NULL,
  `page_type` varchar(191) NOT NULL,
  `og_image` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_requests`
--

CREATE TABLE `withdrawal_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL COMMENT 'user, seller, delivery_boy',
  `type_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `message` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `remark` text DEFAULT NULL COMMENT 'This is store reject request',
  `receipt_image` varchar(191) DEFAULT NULL COMMENT 'If status: approved (1) then upload receipt as proof',
  `device_type` varchar(191) DEFAULT NULL COMMENT '0 => Web, 1 => Android, 2 => IOS',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_tokens`
--
ALTER TABLE `admin_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_call_tracking`
--
ALTER TABLE `api_call_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_call_tracking_api_name_source_unique` (`api_name`,`source`);

--
-- Indexes for table `app_usages`
--
ALTER TABLE `app_usages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blogs_slug_unique` (`slug`),
  ADD KEY `blogs_category_id_foreign` (`category_id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_categories_slug_unique` (`slug`);

--
-- Indexes for table `blog_views`
--
ALTER TABLE `blog_views`
  ADD UNIQUE KEY `blog_views_blog_id_ip_address_unique` (`blog_id`,`ip_address`),
  ADD KEY `blog_views_blog_id_ip_address_index` (`blog_id`,`ip_address`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_notifications`
--
ALTER TABLE `cart_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_notifications_user_id_foreign` (`user_id`),
  ADD KEY `cart_notifications_cart_id_foreign` (`cart_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_boys`
--
ALTER TABLE `delivery_boys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_boy_notifications`
--
ALTER TABLE `delivery_boy_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_boy_transactions`
--
ALTER TABLE `delivery_boy_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fund_transfers`
--
ALTER TABLE `fund_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `live_tracking`
--
ALTER TABLE `live_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `live_tracking_order_id_foreign` (`order_id`);

--
-- Indexes for table `mail_settings`
--
ALTER TABLE `mail_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `newsletters`
--
ALTER TABLE `newsletters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_bank_transfers`
--
ALTER TABLE `order_bank_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_user_id_index` (`user_id`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_status_lists`
--
ALTER TABLE `order_status_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_trackings`
--
ALTER TABLE `order_trackings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panel_notifications`
--
ALTER TABLE `panel_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `panel_notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission_categories`
--
ALTER TABLE `permission_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pickup_locations`
--
ALTER TABLE `pickup_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pincodes`
--
ALTER TABLE `pincodes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_additional_charges`
--
ALTER TABLE `pos_additional_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_orders`
--
ALTER TABLE `pos_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_order_items`
--
ALTER TABLE `pos_order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_users`
--
ALTER TABLE `pos_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_ratings_product_id_user_id_unique` (`product_id`,`user_id`);

--
-- Indexes for table `product_tag`
--
ALTER TABLE `product_tag`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_tag_product_id_tag_id_unique` (`product_id`,`tag_id`),
  ADD KEY `product_tag_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_images`
--
ALTER TABLE `rating_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_requests_order_item_id_unique` (`order_item_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seller_commissions`
--
ALTER TABLE `seller_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seller_transactions`
--
ALTER TABLE `seller_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seller_wallet_transactions`
--
ALTER TABLE `seller_wallet_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_templates`
--
ALTER TABLE `sms_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_verifications`
--
ALTER TABLE `sms_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_media`
--
ALTER TABLE `social_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscriptions_stripe_id_unique` (`stripe_id`),
  ADD KEY `subscriptions_user_id_stripe_status_index` (`user_id`,`stripe_status`);

--
-- Indexes for table `subscription_items`
--
ALTER TABLE `subscription_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_items_subscription_id_stripe_price_unique` (`subscription_id`,`stripe_price`),
  ADD UNIQUE KEY `subscription_items_stripe_id_unique` (`stripe_id`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supported_languages`
--
ALTER TABLE `supported_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tags_name_unique` (`name`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_index` (`user_id`),
  ADD KEY `transactions_order_id_index` (`order_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_stripe_id_index` (`stripe_id`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_product_requests`
--
ALTER TABLE `user_product_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_product_requests_customer_id_foreign` (`customer_id`),
  ADD KEY `user_product_requests_product_id_foreign` (`product_id`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_seo_pages`
--
ALTER TABLE `web_seo_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `web_seo_pages_page_type_unique` (`page_type`);

--
-- Indexes for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_tokens`
--
ALTER TABLE `admin_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_call_tracking`
--
ALTER TABLE `api_call_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `app_usages`
--
ALTER TABLE `app_usages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `area`
--
ALTER TABLE `area`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_notifications`
--
ALTER TABLE `cart_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

--
-- AUTO_INCREMENT for table `delivery_boys`
--
ALTER TABLE `delivery_boys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_boy_notifications`
--
ALTER TABLE `delivery_boy_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_boy_transactions`
--
ALTER TABLE `delivery_boy_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fund_transfers`
--
ALTER TABLE `fund_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_tracking`
--
ALTER TABLE `live_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mail_settings`
--
ALTER TABLE `mail_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `newsletters`
--
ALTER TABLE `newsletters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_bank_transfers`
--
ALTER TABLE `order_bank_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_status_lists`
--
ALTER TABLE `order_status_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_trackings`
--
ALTER TABLE `order_trackings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `permission_categories`
--
ALTER TABLE `permission_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pickup_locations`
--
ALTER TABLE `pickup_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pincodes`
--
ALTER TABLE `pincodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_additional_charges`
--
ALTER TABLE `pos_additional_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_orders`
--
ALTER TABLE `pos_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_order_items`
--
ALTER TABLE `pos_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_users`
--
ALTER TABLE `pos_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_tag`
--
ALTER TABLE `product_tag`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rating_images`
--
ALTER TABLE `rating_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_requests`
--
ALTER TABLE `return_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seller_commissions`
--
ALTER TABLE `seller_commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seller_transactions`
--
ALTER TABLE `seller_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seller_wallet_transactions`
--
ALTER TABLE `seller_wallet_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_templates`
--
ALTER TABLE `sms_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sms_verifications`
--
ALTER TABLE `sms_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_media`
--
ALTER TABLE `social_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_items`
--
ALTER TABLE `subscription_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supported_languages`
--
ALTER TABLE `supported_languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `updates`
--
ALTER TABLE `updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_product_requests`
--
ALTER TABLE `user_product_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_seo_pages`
--
ALTER TABLE `web_seo_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blog_views`
--
ALTER TABLE `blog_views`
  ADD CONSTRAINT `blog_views_blog_id_foreign` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_notifications`
--
ALTER TABLE `cart_notifications`
  ADD CONSTRAINT `cart_notifications_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `live_tracking`
--
ALTER TABLE `live_tracking`
  ADD CONSTRAINT `live_tracking_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `product_tag`
--
ALTER TABLE `product_tag`
  ADD CONSTRAINT `product_tag_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_product_requests`
--
ALTER TABLE `user_product_requests`
  ADD CONSTRAINT `user_product_requests_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_product_requests_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
