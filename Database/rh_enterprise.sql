-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 11:02 AM
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
-- Database: `rh_enterprise`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(3, 'Dhruv', 'admin@rhentp.in', '$2y$10$arKiRE/liYL9erMjd2oIfu2aB2KKX.ZJZNvr53GLdP7376nm6We7O', '2026-03-02 11:05:10');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `image`, `status`, `sort_order`, `created_at`) VALUES
(15, NULL, 'Electric Motor', 'electric-motor', NULL, 1, 1, '2026-03-03 07:12:43'),
(17, 15, 'Three Phase Motors', 'three-phase-motors', NULL, 1, 2, '2026-03-03 07:16:52'),
(18, 15, 'Single Phase Motors', 'single-phase-motors', NULL, 1, 3, '2026-03-03 07:17:07'),
(19, 15, 'Break Motors', 'break-motors', NULL, 1, 4, '2026-03-03 07:17:21'),
(21, NULL, 'Gear Box', 'gear-box', NULL, 1, 5, '2026-03-03 08:20:39'),
(22, 21, 'Worm Gear Box', 'worm-gear-box', NULL, 1, 8, '2026-03-03 08:27:31'),
(24, 21, 'Helical Gear Box', 'helical-gear-box', NULL, 1, 6, '2026-03-03 08:32:37'),
(25, 21, 'Planetary Gear Box', 'planetary-gear-box', NULL, 1, 7, '2026-03-03 08:39:41');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `product_id`, `product_name`, `name`, `email`, `phone`, `company`, `message`, `ip_address`, `user_agent`, `status`, `created_at`) VALUES
(1, NULL, 'Helmet', 'Dhruv Pandya', 'dhr@gmail.com', '7016515414', 'msengg.in', 'sdacaavevd z', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 0, '2026-03-03 07:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `main_image`, `status`, `sort_order`, `created_at`) VALUES
(20, 19, 'Break Motors', 'break-motors', '3 Phase Brake Motor with manual hand release lever, 3000 RPM speed, and reliable braking performance for industrial machinery.', 'The 3 Phase Brake Motor is designed for applications requiring controlled stopping and high operational safety. It features a 2-pole 3000 RPM configuration, integrated electromagnetic brake, and ±6% voltage tolerance for stable performance. Available from 0.18 kW to 7.5 kW, it is suitable for cranes, conveyors, elevators, and automation systems.', '1772523294_4517.jpeg', 1, 0, '2026-03-03 07:26:24'),
(21, 18, 'Single Phase Motors', 'single-phase-motors', 'Single Phase Motor (230V) with capacitor start/run design, aluminum body, and high starting torque for reliable performance.', 'IT is a 230V single-phase motor with capacitor start and run system, designed for smooth operation and high starting torque. Built with aluminum housing and IEC standard compliance, it offers low noise, low vibration, and dependable performance for small and medium machinery applications.', '1772525814_9328.jpeg', 1, 0, '2026-03-03 08:16:54'),
(22, 17, 'Three Phase Motors', 'three-phase-motors', '3 Phase Brake Motor with manual hand release lever, 3000 RPM speed, and reliable braking performance for industrial machinery.', 'This 3 Phase Brake Motor is designed for applications requiring controlled stopping and enhanced safety. It features a 2-pole 3000 RPM configuration, integrated electromagnetic braking system, and ±6% voltage tolerance for stable operation. Available in multiple power ratings, it delivers strong starting torque, high efficiency, and dependable performance for demanding industrial environments.', '1772525966_5752.jpeg', 1, 0, '2026-03-03 08:19:26'),
(24, 22, 'BLM Series', 'blm-series', '', '', NULL, 1, 0, '2026-03-03 08:28:40'),
(25, 22, 'BL Series', 'bl-series', 'Aluminium Casing Worm Gear Reducer with motor mounting flange, compact design, and multiple centre distance options for efficient power transmission.', 'This Aluminium Casing Worm Gear Reducer is designed for smooth torque transmission and space-saving installation. Featuring a motor mounting flange configuration, it offers easy integration with electric motors and is available in multiple centre distances. The lightweight aluminium housing ensures durability, corrosion resistance, and efficient heat dissipation, making it ideal for industrial drive applications.', '1772528852_1692.png', 1, 0, '2026-03-03 08:28:56'),
(26, 22, 'BVF Series', 'bvf-series', '', '', NULL, 1, 0, '2026-03-03 08:29:13'),
(27, 22, 'BW Series', 'bw-series', '', '', NULL, 1, 0, '2026-03-03 08:29:31'),
(29, 24, 'BR Series', 'br-series', '', '', NULL, 1, 0, '2026-03-03 08:33:43'),
(30, 24, 'BK Series', 'bk-series', '', '', NULL, 1, 0, '2026-03-03 08:34:15'),
(31, 24, 'BF Series', 'bf-series', '', '', NULL, 1, 0, '2026-03-03 08:34:29'),
(32, 25, 'Planetary Gear Box', 'planetary-gear-box', '', '', NULL, 1, 0, '2026-03-03 08:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `product_applications`
--

CREATE TABLE `product_applications` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `application_text` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_applications`
--

INSERT INTO `product_applications` (`id`, `product_id`, `application_text`, `created_at`) VALUES
(5, 20, 'Ideal for cranes, hoists, conveyors, elevators, injection moulding machines, packaging systems, and various material handling and automation equipment.', '2026-03-03 08:05:13'),
(6, 21, 'Suitable for air compressors, pumps, fans, medical equipment, and small industrial machines.', '2026-03-03 08:16:54'),
(7, 22, 'Suitable for cranes, hoists, conveyors, elevators, packaging machines, and industrial automation systems.', '2026-03-03 08:19:26'),
(11, 25, 'Suitable for conveyors, packaging machines, material handling systems, food processing units, and industrial automation equipment.', '2026-03-03 09:12:58');

-- --------------------------------------------------------

--
-- Table structure for table `product_features`
--

CREATE TABLE `product_features` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `feature_text` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_features`
--

INSERT INTO `product_features` (`id`, `product_id`, `feature_text`, `created_at`) VALUES
(6, 20, 'High-efficiency 3-phase brake motor with integrated electromagnetic braking system, manual hand release lever, 3000 RPM speed, strong starting torque, and ±6% voltage tolerance for reliable industrial performance.', '2026-03-03 08:05:13'),
(7, 21, '230V single-phase motor with dual capacitor system, high starting torque, low vibration, and aluminum body construction.', '2026-03-03 08:16:54'),
(8, 22, 'Integrated electromagnetic brake with manual release, high starting torque, and stable 415V three-phase operation.', '2026-03-03 08:19:26'),
(12, 25, 'Compact aluminium housing, motor flange mounting, multiple centre sizes, and reliable worm gear reduction performance.', '2026-03-03 09:12:58');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`, `created_at`) VALUES
(5, 25, '1772529178_7090.png', '2026-03-03 09:12:58'),
(6, 25, '1772529178_8897.png', '2026-03-03 09:12:58'),
(7, 25, '1772529178_1508.png', '2026-03-03 09:12:58'),
(8, 25, '1772529178_4875.png', '2026-03-03 09:12:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category_parent` (`parent_id`),
  ADD KEY `idx_category_slug` (`slug`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_product_slug` (`slug`),
  ADD KEY `idx_product_category` (`category_id`);

--
-- Indexes for table `product_applications`
--
ALTER TABLE `product_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_features`
--
ALTER TABLE `product_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `product_applications`
--
ALTER TABLE `product_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_features`
--
ALTER TABLE `product_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_applications`
--
ALTER TABLE `product_applications`
  ADD CONSTRAINT `product_applications_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_features`
--
ALTER TABLE `product_features`
  ADD CONSTRAINT `product_features_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
