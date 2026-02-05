-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2026 at 06:02 AM
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
-- Database: `business_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE `advertisements` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `days_duration` int(11) DEFAULT NULL,
  `cost_per_day` decimal(10,2) DEFAULT 100.00,
  `total_cost` decimal(10,2) DEFAULT 0.00,
  `is_paid` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advertisements`
--

INSERT INTO `advertisements` (`id`, `company_id`, `title`, `description`, `image_path`, `link_url`, `start_date`, `end_date`, `days_duration`, `cost_per_day`, `total_cost`, `is_paid`, `status`, `admin_notes`, `created_at`, `updated_at`, `approved_by`, `approved_at`) VALUES
(3, 11, 'Parul University', 'MCA\r\nMBA\r\nMCOM', 'uploads/advertisements/ad_1769748318_697c375ea003f.jpeg', 'https://paruluniversity.ac.in/master_2026_gsn/?utm_source=google_search_ls_brand&utm_medium=Brand_Gujarat&utm_campaign=LS_Google_Search_Brand_2026_Gujarat_26thSept25&utm_adgroup=Brand_Core&utm_term=parul%20university&utm_network=g&utm_matchtype=e&utm_devi', '2026-01-30', '2026-03-01', 30, 100.00, 3000.00, 1, 'approved', NULL, '2026-01-28 04:49:09', '2026-01-30 05:15:33', 1, '2026-01-28 04:49:49'),
(4, 11, 'Darshan Uni', 'MCA', 'uploads/advertisements/ad_1769748175_697c36cfc9386.jpeg', 'https://darshan.ac.in/', '2026-01-30', '2026-03-01', 30, 100.00, 3000.00, 1, 'approved', NULL, '2026-01-28 05:27:53', '2026-01-30 05:15:33', 1, '2026-01-28 05:28:20'),
(5, 14, 'MKBU', 'MCA \r\nBCA', 'uploads/ads/ad_697c324bb77b87.42687342.png', 'https://mkbhavuni.edu.in/mkbhavuniweb/', '2026-01-30', '2026-03-01', 30, 100.00, 3000.00, 1, 'approved', NULL, '2026-01-30 04:23:39', '2026-01-30 05:15:33', 1, '2026-01-30 04:24:56');

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`id`, `user_id`, `name`, `category`, `address`, `phone`, `description`, `image`) VALUES
(1, 1, 'Spice Villa Restaurant', 'Restaurant', 'Near City Mall, Main Road', '9876543210', 'best restro', 'download.jpeg'),
(4, 1, 'Shree Krishna Grocery Store', 'Grocery', 'Patel Nagar, Sector 4', '9823456712', 'Local grocery shop providing daily household essentials.', 'grocery.jpg'),
(5, 1, 'QuickFix Mobile Repair', 'Repair Service', 'Shop No. 12, Bus Stand Road', '9027060323', 'Professional mobile phone repair services.', 'mobile_repair.jpg'),
(6, 1, 'Royal Punjabi Dhaba', 'Restaurant', 'Highway Road, Opposite Petrol Pump', '9765432109', 'Authentic Punjabi food with family dining.', 'dhaba.jpg'),
(7, 1, 'Bright Future Coaching Classes', 'Education', '2nd Floor, Shyam Complex', '9812345678', 'Coaching center for school and competitive exams.', 'coaching.jpg'),
(8, 1, 'City Care Medical Store', 'Pharmacy', 'Near Civil Hospital', '9900123456', 'Medical store offering medicines and healthcare products.', 'medical.jpg'),
(9, 1, 'SmartTech Computer Services', 'IT Services', 'Office No. 5, Tech Plaza', '9876001234', 'Computer repair and software installation services.', 'computer.jpg'),
(10, 1, 'Green Leaf Pure Veg Restaurant', 'Restaurant', 'Market Area, Central Square', '9745612300', 'Pure vegetarian restaurant serving healthy meals.', 'veg_restaurant.jpg'),
(11, 1, 'Prime Real Estate', 'Real Estate', '1st Floor, Royal Tower', '9887766554', 'Property buying, selling, and rental services.', 'real_estate.jpg'),
(12, 1, 'Vision Digital Marketing', 'Digital Services', 'Startup Hub, Business Park', '9696969696', 'SEO, social media marketing, and branding solutions.', 'digital_marketing.jpg'),
(14, 1, 'Elegant Salon & Spa', 'Beauty & Wellness', 'Shop No. 8, Silver Complex, Main Market', '9765438899', 'Professional salon and spa providing hair, skin, and relaxation services.', 'salon.jpg'),
(18, 11, 'IT Hub Software Solution', 'It company', 'bhagvati circle', '9048545645', 'All type of development', 'uploads/696dbc4aaa2ff_1768799306.png');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `business_name`, `name`, `email`, `phone`, `message`, `is_read`, `created_at`) VALUES
(3, 'IT Hub Software Solution', 'ronak', 'jethva@gmail.com', '9048545645', 'my business website', 1, '2026-01-19 05:09:14'),
(5, 'IT Hub Software Solution', 'Ronak', 'jethvaronak98@gmail.com', '9876543210', 'develop site', 1, '2026-02-05 04:38:20'),
(6, 'Shree Krishna Grocery Store', 'raj', 'Raj123@gmail.com', '9048545645', 'buy fruites', 1, '2026-02-05 04:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','company') DEFAULT 'company'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$keDJiwlap1qTHLWXsx5bt.vL5qvqJcbDW7qhLyos0dkPgPdeGfhEW', 'admin'),
(11, 'IT Hub Software', '$2y$10$QL5VgF2XBI/0Ue4h3AyI9eOiWve9xQBnJPAkao0m7PMdYLaR3vC6G', 'company'),
(12, 'ronakj', '$2y$10$5gF0gaW9I661iS/.Re4mMeEsx2YBezi.IT4Hk7JgtUu0n/TXKo7qK', 'admin'),
(14, 'Uni', '$2y$10$/3knJz9kcI8n4wpqBNNj8.CWvZVgHQn2kdeo2sxR1ZDpeEO0nqcJy', 'company');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_company_id` (`company_id`);

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD CONSTRAINT `advertisements_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `advertisements_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `businesses`
--
ALTER TABLE `businesses`
  ADD CONSTRAINT `businesses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
