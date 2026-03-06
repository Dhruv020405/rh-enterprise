-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

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
-- Table structure for table `brand_clients`
--

CREATE TABLE `brand_clients` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `type` enum('partner','client') NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand_clients`
--

INSERT INTO `brand_clients` (`id`, `name`, `logo`, `type`, `status`, `sort_order`, `created_at`) VALUES
(4, 'Bonvario', '1772650670_3497.jpg', 'partner', 1, 1, '2026-03-04 18:57:50'),
(5, 'Wanshsin', '1772651037_9285.png', 'partner', 1, 2, '2026-03-04 19:03:57'),
(7, 'Mitsubishi Electric', '1772782574_8119.png', 'partner', 1, 5, '2026-03-06 07:02:39'),
(8, 'Schneider Electric', '1772781623_7026.png', 'partner', 1, 6, '2026-03-06 07:03:38'),
(9, 'Delta', '1772781423_8210.png', 'partner', 1, 7, '2026-03-06 07:03:52'),
(10, 'SPG', '1772781697_8430.png', 'partner', 1, 12, '2026-03-06 07:04:03'),
(11, 'SICK', '1772781718_3027.png', 'partner', 1, 13, '2026-03-06 07:04:15'),
(12, 'Lubi', '1772781983_5358.jpg', 'partner', 1, 4, '2026-03-06 07:04:26'),
(13, 'SIEMENS', '1772782129_5269.png', 'partner', 1, 8, '2026-03-06 07:04:49'),
(14, 'Nidec', '1772782220_1650.png', 'partner', 1, 3, '2026-03-06 07:05:08'),
(15, 'YASKAWA', '1772782265_7184.png', 'partner', 1, 10, '2026-03-06 07:05:37'),
(16, 'INOVANCE', '1772782310_8045.png', 'partner', 1, 14, '2026-03-06 07:05:50'),
(17, 'OMRON', '1772782353_2435.png', 'partner', 1, 9, '2026-03-06 07:06:03'),
(18, 'Panasonic', '1772782407_5255.png', 'partner', 1, 11, '2026-03-06 07:06:18'),
(19, 'Baumer', '1772782460_2172.png', 'partner', 1, 15, '2026-03-06 07:06:29');

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
(15, NULL, 'Electric Motor', 'electric-motor', '1772789964_6399.png', 1, 1, '2026-03-03 07:12:43'),
(21, NULL, 'Gear Box', 'gear-box', '1772790014_4145.png', 1, 5, '2026-03-03 08:20:39'),
(22, 21, 'Worm Gear Box', 'worm-gear-box', '1772783235_7246.jpg', 1, 1, '2026-03-03 08:27:31'),
(24, 21, 'Helical Gear Box', 'helical-gear-box', '1772782997_3228.png', 1, 2, '2026-03-03 08:32:37'),
(25, 21, 'Planetary Gear Box', 'planetary-gear-box', '1772784596_7399.png', 1, 3, '2026-03-03 08:39:41');

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
(1, NULL, 'Helmet', 'Dhruv Pandya', 'pandyadhruvgaming@gmail.com', '7016515414', 'msengg.in', 'sdacaavevd z', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1, '2026-03-03 07:00:36'),
(2, NULL, 'Planetary Gear Box', 'Dhruv Pandya', 'admin@gmail.com', '6352340841', 'msengg.in', 'Ab gallery PC par vertical (left side mein choti images aur right mein badi image) dikhegi, aur Mobile par horizontal (badi image upar aur choti swipeable images niche) dikhegi. Saath hi maine Hover Effect bhi add kar diya hai taaki PC par image par mouse le jaate hi badi image change ho jaye, theek Amazon ki tarah.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1, '2026-03-04 14:13:22'),
(3, 25, 'BL Series', 'Dhruv Pandya', 'dhruvpandya0204@gmail.com', '7016515414', 'msengg.in', 'a,jfsbcaiANck asC', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1, '2026-03-05 11:20:07'),
(7, 25, 'BL Series', 'Dhruv Pandya', 'dhruvpandya0204@gmail.com', '7016515414', 'msengg.in', 'hguoh jabfjiabrij fjbqajzb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1, '2026-03-05 20:59:22');

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
(24, 22, 'BLM Series', 'blm-series', 'Motor-mounted aluminium worm gearbox designed for compact drive solutions with direct motor coupling and reliable torque transmission.', 'The BLM Motor Mounted Worm Gearbox is engineered for space-saving industrial drive applications where direct motor integration and smooth power transmission are essential. It features a high-quality aluminium die-cast housing combined with a precision worm and worm wheel assembly for efficient torque output and long service life.\r\n\r\nDesigned with IEC motor mounting flange compatibility, this gearbox allows direct motor coupling, eliminating the need for additional alignment components. The compact construction ensures reduced vibration, quiet operation, and simplified installation.\r\n\r\nAvailable in multiple centre distances ranging from 30 mm to 150 mm, it supports a wide range of reduction ratios suitable for various industrial speed control requirements. The unit is pre-lubricated for low maintenance and continuous operation.', '1772535548_2982.png', 1, 1, '2026-03-03 08:28:40'),
(25, 22, 'BL Series', 'bl-series', 'Aluminium Casing Worm Gear Reducer with motor mounting flange, compact design, and multiple centre distance options for efficient power transmission.', 'This Aluminium Casing Worm Gear Reducer is designed for smooth torque transmission and space-saving installation. Featuring a motor mounting flange configuration, it offers easy integration with electric motors and is available in multiple centre distances. The lightweight aluminium housing ensures durability, corrosion resistance, and efficient heat dissipation, making it ideal for industrial drive applications.', '1772535670_2063.png', 1, 2, '2026-03-03 08:28:56'),
(26, 22, 'BVF Series', 'bvf-series', 'Compact aluminium worm gearbox designed for smooth torque transmission, low noise operation, and multiple mounting configurations.', 'The BVF Type Aluminium Worm Gearbox is engineered for compact industrial drive applications where space-saving design and reliable torque output are essential. Manufactured with high-quality die-cast aluminium housing, it ensures durability, corrosion resistance, and efficient heat dissipation.\r\n\r\nIt is available in centre distances of 30mm, 44mm, and 49mm, offering versatile mounting options and compatibility with IEC motor adaptors. Pre-filled with synthetic lubrication oil for long service life, this gearbox delivers high efficiency, stable performance, and low maintenance operation.', '1772534208_1648.png', 1, 3, '2026-03-03 08:29:13'),
(27, 22, 'BW Series', 'bw-series', 'Heavy-duty aluminium worm gearbox offering higher torque capacity and robust performance for medium industrial applications.', 'The BW Type Aluminium Worm Gearbox is designed for applications requiring higher torque transmission and stronger load handling capacity. Built with durable aluminium alloy housing and precision-engineered worm shaft and wheel assembly, it ensures long operational life and consistent efficiency.\r\n\r\nAvailable in centre distances of 63mm, 75mm, and 86mm, it supports multiple mounting positions and IEC motor integration. With synthetic oil lubrication and optimized gear profile design, it provides reduced noise, improved efficiency, and reliable performance under continuous industrial operation.', '1772534751_2890.png', 1, 4, '2026-03-03 08:29:31'),
(29, 24, 'BR Series', 'br-series', 'Helical gearbox designed for smooth power transmission, high efficiency, and reliable performance in industrial drive systems.', 'The BR Helical Gear Box is designed to provide efficient and reliable power transmission in various industrial applications. It uses precision-engineered helical gears that ensure smooth operation with reduced vibration and noise. The inline configuration allows easy installation in different drive systems. Its robust housing construction offers strength and durability for continuous operation. The gearbox delivers consistent torque and stable performance even under heavy load conditions. It is suitable for a wide range of machinery requiring dependable speed reduction. The compact design also helps in efficient use of installation space.', '1772784460_6751.png', 1, 1, '2026-03-03 08:33:43'),
(30, 24, 'BK Series', 'bk-series', 'Bevel helical gearbox designed for right-angle power transmission with high torque and reliable industrial performance.', 'The BK Bevel Helical Gear Box is engineered for applications that require power transmission at a 90-degree angle. It combines bevel and helical gear technology to deliver high torque with efficient performance. The precision gear design ensures smooth operation with reduced vibration and noise. Its strong housing construction provides durability and stability during continuous operation. The gearbox is suitable for heavy-duty industrial environments where reliable motion control is required. It also allows flexible mounting options for easy integration with different machinery. This makes it ideal for a wide range of industrial drive systems.', '1772784307_9388.png', 1, 2, '2026-03-03 08:34:15'),
(31, 24, 'BF Series', 'bf-series', 'Parallel shaft helical gearbox designed for high torque transmission and efficient performance in industrial drive systems.', 'The BF Parallel Shaft Helical Gear Box is engineered for heavy-duty industrial applications that require reliable torque transmission and compact installation. Its parallel shaft configuration allows efficient power flow while maintaining stable performance under load. The gearbox is designed with precision helical gears that ensure smooth and quiet operation. The robust housing construction provides durability and long service life even in demanding environments. It supports continuous operation and consistent output performance. The design also allows easy integration with various industrial drive systems. This makes it suitable for a wide range of material handling and automation equipment.', '1772784201_5753.png', 1, 3, '2026-03-03 08:34:29'),
(33, 25, 'VRL Series', 'vrl-series', 'Inline planetary gearbox designed for high precision, compact size, and reliable performance in automation and servo applications.', 'The VRL Planetary Gear Box is designed to deliver high torque density and precise motion control in industrial automation systems. Its inline planetary configuration ensures compact design and efficient power transmission. The gearbox uses precision helical gearing that provides smooth, quiet operation and improved accuracy. Its robust internal construction allows stable performance even under demanding operating conditions. The design supports high dynamic loads and continuous operation in industrial environments. With low backlash characteristics, it is suitable for applications where precision and positioning accuracy are important. The gearbox can be easily integrated with servo motors and automation equipment.', '1772784961_2508.png', 1, 1, '2026-03-06 08:16:01'),
(34, 25, 'VRB Series', 'vrb-series', 'Inline planetary gearbox designed for high positional accuracy and smooth performance in automation and motion control applications.', 'The VRB Planetary Gear Box is designed for applications that require high positional accuracy and dynamic performance. Its inline planetary configuration ensures efficient power transmission with a compact and rigid structure. The gearbox offers smooth and stable operation, making it suitable for precision-driven systems. Its robust internal design supports reliable performance under continuous industrial use. The unit is easy to install due to its simple mounting design and compatibility with different drive systems. It also supports high-speed operation with consistent torque output. This makes it ideal for modern automation and motion control applications.', '1772785133_5645.png', 1, 2, '2026-03-06 08:18:35'),
(35, 25, 'EVL Series', 'evl-series', 'Right-angle planetary gearbox designed for compact installation and high torque performance in automation systems.', 'The EVL Planetary Gear Box is designed for applications where space limitations require a right-angle drive configuration. It combines planetary gearing with bevel gear technology to deliver smooth power transmission and high torque density. The compact design allows the motor to be mounted at a 90-degree angle, saving installation space. Its robust internal construction ensures reliable performance under continuous industrial operation. Precision gearing helps achieve smooth and stable motion with minimal vibration. The gearbox is suitable for dynamic automation environments where accuracy and durability are important. It can be easily integrated with servo motors and industrial automation systems.', '1772785867_8478.png', 1, 4, '2026-03-06 08:31:07'),
(36, 25, 'EVB Series', 'evb-series', 'Right-angle planetary gearbox designed for compact installation, high positional accuracy, and reliable automation performance.', 'The EVB Planetary Gear Box is designed for applications that require precise motion control and compact right-angle drive configuration. Its planetary gear system ensures efficient power transmission with smooth and stable operation. The right-angle design allows the motor to be mounted at 90 degrees, helping save installation space. The gearbox is built with a robust internal structure that supports high dynamic loads and continuous industrial operation. Its compact mounting style allows easy installation on various machines and automation systems. The design also ensures consistent torque delivery and reliable performance. This makes it suitable for precision automation and motion control applications.', '1772786187_2451.png', 1, 3, '2026-03-06 08:36:03'),
(37, 15, 'Break Motors', 'break-motors', 'Electric motors with integrated braking system designed for precise stopping and controlled motion.', 'Brake Motors are specially designed motors equipped with an electromagnetic braking system. They provide instant stopping capability and precise motion control in industrial machines. These motors ensure safety and accuracy in applications where quick stopping is required. Their robust construction supports heavy duty industrial operation with reliable performance. The braking mechanism reduces machine downtime and improves operational safety. They are suitable for applications that require controlled movement and positioning.', '1772789718_7556.png', 1, 0, '2026-03-06 09:31:26'),
(38, 15, 'Single Phase Motors', 'single-phase-motors', 'Compact and efficient single phase motors designed for light-duty industrial and commercial applications.', 'Single Phase Motors are ideal for applications where three phase power supply is not available. These motors are designed to provide reliable performance with simple installation and operation. Their compact design allows easy integration into different machines and equipment. They offer stable speed and dependable operation for light and medium duty applications. The motors are built with durable components to ensure long service life. They are commonly used in small industrial machines and commercial equipment.', '1772789728_7491.png', 1, 0, '2026-03-06 09:32:29'),
(39, 15, 'Three Phase Motors', 'three-phase-motors', 'High-efficiency three phase motors designed for continuous industrial operation and reliable power transmission.', 'Three Phase Motors are widely used in industrial machinery where high power and efficiency are required. These motors are designed with robust construction to deliver reliable performance in demanding operating conditions. They provide smooth operation with stable speed and high torque output. The motors are suitable for continuous duty applications and support efficient energy consumption. Their durable design ensures long service life with minimal maintenance. They are compatible with various mounting options and industrial drive systems.', '1772789741_8761.png', 1, 0, '2026-03-06 09:33:27');

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
(13, 26, 'Ideal for conveyors, packaging machines, food processing equipment, bottling units, and light-duty automation systems.', '2026-03-03 11:13:40'),
(14, 27, 'Suitable for material handling systems, heavy conveyors, mixers, elevators, and industrial machinery.', '2026-03-03 11:14:42'),
(15, 24, 'Ideal for conveyors, packaging machines, material handling systems, mixers, and automation equipment.', '2026-03-03 11:22:08'),
(17, 25, 'Suitable for conveyors, packaging machines, material handling systems, food processing units, and industrial automation equipment.', '2026-03-03 15:28:41'),
(20, 31, 'Used in conveyors, material handling equipment, and industrial machinery.', '2026-03-06 08:03:21'),
(21, 30, 'Used in elevators, conveyors, mixers, and material handling equipment.', '2026-03-06 08:05:07'),
(22, 29, 'Used in conveyors, mixers, packaging machines, and industrial processing equipment.', '2026-03-06 08:07:40'),
(23, 33, 'Used in packaging machines, automation systems, robotics, and material handling equipment.', '2026-03-06 08:16:02'),
(25, 34, 'Used in packaging machines, automation equipment, assembly lines, and belt drive systems.', '2026-03-06 08:18:53'),
(26, 35, 'Used in packaging machines, robotics, automation systems, and material handling equipment.', '2026-03-06 08:31:07'),
(28, 36, 'Used in packaging machines, automation systems, belt drive mechanisms, and assembly line equipment.', '2026-03-06 08:36:27'),
(32, 37, 'Used in hoists, cranes, conveyors, lifts, and automation machinery.', '2026-03-06 09:35:18'),
(33, 38, 'Used in small machinery, fans, pumps, compressors, and workshop equipment.', '2026-03-06 09:35:29'),
(34, 39, 'Used in pumps, compressors, conveyors, machine tools, and industrial machinery', '2026-03-06 09:35:41');

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
(14, 26, 'Lightweight aluminium housing, multiple mounting options, maintenance-free lubrication, and smooth low-noise operation.', '2026-03-03 11:13:40'),
(15, 27, 'High torque capacity, aluminium alloy housing, flexible mounting positions, and long-life synthetic lubrication.', '2026-03-03 11:14:42'),
(16, 24, 'Compact aluminium housing with IEC motor flange, smooth torque transmission, low noise, and maintenance-free lubrication.', '2026-03-03 11:22:08'),
(19, 25, 'Compact aluminium housing, motor flange mounting, multiple centre sizes, and reliable worm gear reduction performance.', '2026-03-03 15:28:41'),
(24, 31, 'High torque transmission', '2026-03-06 08:03:21'),
(25, 31, 'Compact parallel shaft design', '2026-03-06 08:03:21'),
(26, 30, '90° right-angle power transmission', '2026-03-06 08:05:07'),
(27, 30, 'High torque output capability', '2026-03-06 08:05:07'),
(28, 29, 'High efficiency power transmission', '2026-03-06 08:07:40'),
(29, 29, 'Smooth and low-noise operation', '2026-03-06 08:07:40'),
(30, 33, 'High torque density in compact design', '2026-03-06 08:16:02'),
(31, 33, 'Low backlash for precise motion control', '2026-03-06 08:16:02'),
(34, 34, 'High positional accuracy', '2026-03-06 08:18:53'),
(35, 34, 'Compact inline planetary design', '2026-03-06 08:18:53'),
(36, 35, '90° right-angle drive configuration', '2026-03-06 08:31:07'),
(37, 35, 'High torque density with compact design', '2026-03-06 08:31:07'),
(40, 36, '90° right-angle planetary drive', '2026-03-06 08:36:27'),
(41, 36, 'High positional accuracy', '2026-03-06 08:36:27'),
(48, 37, 'Built-in electromagnetic braking system', '2026-03-06 09:35:18'),
(49, 37, 'Quick stopping and precise motion control', '2026-03-06 09:35:18'),
(50, 38, 'Compact and easy to install', '2026-03-06 09:35:29'),
(51, 38, 'Reliable performance for light duty applications', '2026-03-06 09:35:29'),
(52, 39, 'High efficiency and stable performance', '2026-03-06 09:35:41'),
(53, 39, 'Suitable for continuous industrial operation', '2026-03-06 09:35:41');

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
(8, 25, '1772529178_4875.png', '2026-03-03 09:12:58'),
(10, 26, '1772535501_2570.png', '2026-03-03 10:58:21');

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
-- Indexes for table `brand_clients`
--
ALTER TABLE `brand_clients`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `brand_clients`
--
ALTER TABLE `brand_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `product_applications`
--
ALTER TABLE `product_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `product_features`
--
ALTER TABLE `product_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
