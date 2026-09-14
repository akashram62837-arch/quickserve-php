-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2026 at 08:56 PM
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
-- Database: `quickserve_newphp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `password`, `phone`, `profile_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin QuickServe', 'admin@gmail.com', '$2b$10$MztBZsvhjoH9wIXr9T9lyOvAO57ArzugUca9LpuukOi4p9ly5lQ8u', '9876543210', '', 1, '2026-07-30 10:09:57', '2026-07-30 10:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `booking_status` varchar(30) DEFAULT NULL,
  `payment_status` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `provider_id`, `service_id`, `booking_date`, `booking_time`, `address`, `amount`, `booking_status`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-08-01', '10:00 AM', 'Adajan, Surat', 500.00, 'Accepted', 'Paid', '2026-07-30 09:33:15', '2026-07-30 09:33:15'),
(2, 2, 2, 2, '2026-08-02', '11:00 AM', 'Vesu, Surat', 700.00, 'Completed', 'Paid', '2026-07-30 09:34:00', '2026-07-30 09:34:00'),
(3, 3, 3, 3, '2026-08-03', '02:00 PM', 'Katargam, Surat', 900.00, 'Pending', 'Pending', '2026-07-30 09:35:00', '2026-07-30 09:35:00'),
(4, 4, 4, 4, '2026-08-04', '04:00 PM', 'City Light, Surat', 600.00, 'Accepted', 'Paid', '2026-07-30 09:36:00', '2026-07-30 09:36:00'),
(5, 5, 5, 5, '2026-08-05', '09:00 AM', 'Piplod, Surat', 2500.00, 'Completed', 'Paid', '2026-07-30 09:37:00', '2026-07-30 09:37:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `description`, `status`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Plumber', 'Professional plumbing services for pipe repair, tap installation, leakage fixing, and bathroom fittings.', 1, 'plumber.png', '2026-07-30 08:55:28', '2026-07-30 08:55:28'),
(2, 'Electrician', 'Expert electrical services including wiring, switch installation, fan fitting, and electrical repairs.', 1, 'electrician.png', '2026-07-30 08:55:28', '2026-07-30 08:55:28'),
(3, 'Carpenter', 'Carpentry services for furniture repair, door installation, cupboard work, and wood polishing.', 1, 'carpenter.png', '2026-07-30 08:55:28', '2026-07-30 08:55:28'),
(4, 'Salon', 'Professional salon and grooming services including haircuts, facials, hair spa, and beauty treatments.', 1, 'salon.png', '2026-07-30 08:55:28', '2026-07-30 08:55:28'),
(5, 'Home Decor', 'Home decoration services including wallpaper installation, interior decoration, and curtain fitting.', 1, 'homedecor.png', '2026-07-30 08:55:28', '2026-07-30 08:55:28'),
(6, 'Painter', 'Painting services for homes, offices, walls, ceilings, and decorative finishes.', 1, 'painter.png', '2026-07-30 08:55:28', '2026-07-30 08:55:28'),
(7, 'AC Services', 'Air Conditioner Installation, Repair and Maintenance', 1, 'ac.png', '2026-08-06 06:03:13', '2026-08-06 06:03:13'),
(8, 'Washing Machine Services', 'Washing Machine Installation, Repair and Maintenance', 1, 'washing_machine.png', '2026-08-06 06:09:54', '2026-08-06 06:09:54');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `profile_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Aarav Sharma', 'aarav@gmail.com', 'aarav123', '9876500001', 'Adajan, Surat', '', 1, '2026-07-30 09:18:16', '2026-07-30 09:18:16'),
(2, 'Priya Verma', 'priya@gmail.com', 'priya123', '9876500002', 'Vesu, Surat', '', 1, '2026-07-30 09:19:00', '2026-07-30 09:19:00'),
(3, 'Karan Mehta', 'karan@gmail.com', 'karan123', '9876500003', 'Katargam, Surat', '', 1, '2026-07-30 09:20:00', '2026-07-30 09:20:00'),
(4, 'Sneha Iyer', 'sneha@gmail.com', 'sneha123', '9876500004', 'City Light, Surat', '', 1, '2026-07-30 09:21:00', '2026-07-30 09:21:00'),
(5, 'Vikram Singh', 'vikram@gmail.com', 'vikram123', '9876500005', 'Piplod, Surat', '', 1, '2026-07-30 09:22:00', '2026-07-30 09:22:00');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `customer_id`, `provider_id`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Booking Confirmed', 'Your booking has been confirmed successfully.', 0, '2026-07-30 10:00:21', '2026-07-30 10:00:21'),
(2, 2, 2, 'Booking Confirmed', 'Your fan installation booking has been confirmed.', 1, '2026-07-30 10:01:00', '2026-07-30 10:01:00'),
(3, 3, 3, 'Booking Pending', 'Your booking is awaiting provider confirmation.', 0, '2026-07-30 10:02:00', '2026-07-30 10:02:00'),
(4, 4, 4, 'Booking Confirmed', 'Your haircut appointment has been confirmed.', 1, '2026-07-30 10:03:00', '2026-07-30 10:03:00'),
(5, 5, 5, 'Payment Received', 'Your payment for wall painting has been received.', 0, '2026-07-30 10:04:00', '2026-07-30 10:04:00');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `payment_method` varchar(30) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(30) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `customer_id`, `payment_method`, `transaction_id`, `amount`, `payment_status`, `payment_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'UPI', 'TXN100001', 500.00, 'Paid', '2026-08-01 00:00:00', '2026-07-30 09:38:05', '2026-07-30 09:38:05'),
(2, 2, 2, 'Card', 'TXN100002', 700.00, 'Paid', '2026-08-02 00:00:00', '2026-07-30 09:39:00', '2026-07-30 09:39:00'),
(3, 3, 3, 'Cash', 'TXN100003', 900.00, 'Pending', '2026-08-03 00:00:00', '2026-07-30 09:40:00', '2026-07-30 09:40:00'),
(4, 4, 4, 'UPI', 'TXN100004', 600.00, 'Paid', '2026-08-04 00:00:00', '2026-07-30 09:41:00', '2026-07-30 09:41:00'),
(5, 5, 5, 'Card', 'TXN100005', 2500.00, 'Paid', '2026-08-05 00:00:00', '2026-07-30 09:42:00', '2026-07-30 09:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `aadhaar_number` varchar(20) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `full_name`, `email`, `password`, `phone`, `category_id`, `experience`, `address`, `profile_image`, `aadhaar_number`, `is_approved`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Rahul Patel', 'rahul@gmail.com', 'rahul123', '9876543210', 1, 5, 'Surat', '', '123412341231', 1, 1, '2026-07-30 09:05:00', '2026-07-30 09:05:00'),
(2, 'Suresh Kumar', 'suresh@gmail.com', 'suresh123', '9876543211', 2, 4, 'Varachha, Surat', '', '123412341232', 1, 1, '2026-07-30 09:05:00', '2026-07-30 09:05:00'),
(3, 'Manoj Joshi', 'manoj@gmail.com', 'manoj123', '9876543212', 3, 8, 'Katargam, Surat', '', '123412341233', 1, 1, '2026-07-30 09:05:00', '2026-07-30 09:05:00'),
(4, 'Pooja Nair', 'pooja@gmail.com', 'pooja123', '9876543213', 4, 3, 'City Light, Surat', '', '123412341234', 1, 1, '2026-07-30 09:05:00', '2026-07-30 09:05:00'),
(5, 'Deepak Rana', 'deepak@gmail.com', 'deepak123', '9876543214', 5, 6, 'Piplod, Surat', '', '123412341235', 1, 1, '2026-07-30 09:05:00', '2026-07-30 09:05:00'),
(6, 'Anita Desai', 'anita@gmail.com', 'anita123', '9876543215', 6, 7, 'Vesu, Surat', '', '123412341236', 1, 1, '2026-07-30 09:05:00', '2026-07-30 09:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `booking_id`, `customer_id`, `provider_id`, `service_id`, `rating`, `review`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 5, 'Excellent plumbing service. Very professional.', '2026-07-30 09:52:52', '2026-07-30 09:52:52'),
(2, 2, 2, 2, 2, 4, 'Good service, fan works perfectly now.', '2026-07-30 09:53:00', '2026-07-30 09:53:00'),
(3, 3, 3, 3, 3, 4, 'Decent work, slightly delayed but fine.', '2026-07-30 09:54:00', '2026-07-30 09:54:00'),
(4, 4, 4, 4, 4, 5, 'Loved the haircut, very skilled!', '2026-07-30 09:55:00', '2026-07-30 09:55:00'),
(5, 5, 5, 5, 5, 5, 'Amazing paint job, very neat and clean.', '2026-07-30 09:56:00', '2026-07-30 09:56:00');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `service_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `service_image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `provider_id`, `category_id`, `service_name`, `description`, `price`, `duration`, `service_image`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Pipe Leakage Repair', 'Repair leaking water pipes', 500.00, '1 Hour', 'pipe_repair.png', 1, '2026-07-30 09:10:36', '2026-07-30 09:10:36'),
(2, 2, 2, 'Fan Installation', 'Install ceiling fan', 700.00, '2 Hours', 'fan.png', 1, '2026-07-30 09:10:36', '2026-07-30 09:10:36'),
(3, 3, 3, 'Wooden Door Repair', 'Repair wooden door', 900.00, '2 Hours', 'door.png', 1, '2026-07-30 09:10:36', '2026-07-30 09:10:36'),
(4, 4, 4, 'Women\'s Haircut', 'Professional haircut', 600.00, '1 Hour', 'haircut.png', 1, '2026-07-30 09:10:36', '2026-07-30 09:10:36'),
(5, 5, 5, 'Wall Painting', 'Interior wall painting', 2500.00, '1 Day', 'painting.png', 1, '2026-07-30 09:10:36', '2026-07-30 09:10:36'),
(6, 6, 6, 'Deep House Cleaning', 'Complete house cleaning', 1800.00, '4 Hours', 'cleaning.png', 1, '2026-07-30 09:10:36', '2026-07-30 09:10:36'),
(7, NULL, 1, 'Tap Repair', 'Tap repair service', 300.00, '', 'taprepair.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(8, NULL, 1, 'Toilet Repair', 'Toilet repair service', 600.00, '', 'toiletrepair.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(9, NULL, 1, 'Water Tank Cleaning', 'Water tank cleaning', 1200.00, '', 'watertank.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(10, NULL, 1, 'Drain Cleaning', 'Drain cleaning service', 500.00, '', 'draincleaning.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(11, NULL, 2, 'Switch Board Repair', 'Switch board repair', 400.00, '', 'switchboard.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(12, NULL, 2, 'Wiring', 'House wiring', 1000.00, '', 'wiring.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(13, NULL, 2, 'MCB Replacement', 'MCB replacement', 600.00, '', 'mcb.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(14, NULL, 2, 'Light Installation', 'Light installation', 350.00, '', 'lightinstallation.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(15, NULL, 3, 'Furniture Repair', 'Furniture repair', 700.00, '', 'furniturerepair.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(16, NULL, 3, 'Door Installation', 'Door installation', 900.00, '', 'doorinstallation.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(17, NULL, 3, 'Window Repair', 'Window repair', 600.00, '', 'windowrepair.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(18, NULL, 3, 'Modular Furniture', 'Modular furniture work', 2000.00, '', 'modularfurniture.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(19, NULL, 3, 'Cabinet Repair', 'Cabinet repair', 800.00, '', 'cabinetrepair.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(20, NULL, 4, 'Hair Cut', 'Professional haircut', 300.00, '', 'haircut.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(21, NULL, 4, 'Hair Spa', 'Hair spa', 900.00, '', 'hairspa.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(22, NULL, 4, 'Facial', 'Facial service', 1000.00, '', 'facial.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(23, NULL, 4, 'Beard Styling', 'Beard styling', 250.00, '', 'beardstyling.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(24, NULL, 4, 'Makeup', 'Professional makeup', 2500.00, '', 'makeup.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(25, NULL, 6, 'Texture Painting', 'Texture painting', 3500.00, '', 'texturepainting.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(26, NULL, 6, 'Waterproof Paint', 'Waterproof paint', 2800.00, '', 'waterproofpaint.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(27, NULL, 6, 'Exterior Painting', 'Exterior painting', 5000.00, '', 'exteriorpainting.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(28, NULL, 6, 'Wood Polish', 'Wood polish', 1500.00, '', 'woodpolish.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(29, NULL, 5, 'Birthday Party', 'Birthday decoration', 5000.00, '', 'birthdayparty.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(30, NULL, 5, 'Baby Shower', 'Baby shower decoration', 6000.00, '', 'babyshower.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(31, NULL, 5, 'Interior Decoration', 'Interior decoration', 15000.00, '', 'interiordecoration.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(32, NULL, 5, 'Modular Kitchen Design', 'Kitchen design service', 50000.00, '', 'modularkitchen.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(33, NULL, 5, 'Wall Panel', 'Wall panel installation', 7000.00, '', 'wallpanel.png', 1, '2026-07-30 10:28:56', '2026-07-30 10:28:56'),
(34, NULL, 7, 'AC Installation', 'Professional AC Installation', 999.00, '', '', 1, '2026-08-06 06:09:39', '2026-08-06 06:09:39'),
(35, NULL, 7, 'AC Uninstallation', 'Safe AC Uninstallation', 699.00, '', '', 1, '2026-08-06 06:09:39', '2026-08-06 06:09:39'),
(36, NULL, 7, 'AC Repair', 'AC Repair Service', 499.00, '', '', 1, '2026-08-06 06:09:39', '2026-08-06 06:09:39'),
(37, NULL, 7, 'AC Gas Refill', 'AC Gas Charging', 1499.00, '', '', 1, '2026-08-06 06:09:39', '2026-08-06 06:09:39'),
(38, NULL, 7, 'AC General Service', 'Regular AC Maintenance', 599.00, '', '', 1, '2026-08-06 06:09:39', '2026-08-06 06:09:39'),
(39, NULL, 7, 'AC Deep Cleaning', 'Complete AC Deep Cleaning', 799.00, '', '', 1, '2026-08-06 06:09:39', '2026-08-06 06:09:39'),
(40, NULL, 8, 'Washing Machine Installation', 'Professional Washing Machine Installation', 799.00, '', '', 1, '2026-08-06 06:11:09', '2026-08-06 06:11:09'),
(41, NULL, 8, 'Washing Machine Repair', 'Repair for all Washing Machine brands', 499.00, '', '', 1, '2026-08-06 06:11:09', '2026-08-06 06:11:09'),
(42, NULL, 8, 'Washing Machine Deep Cleaning', 'Complete Washing Machine Cleaning', 699.00, '', '', 1, '2026-08-06 06:11:09', '2026-08-06 06:11:09'),
(43, NULL, 8, 'Drum Repair', 'Washing Machine Drum Repair', 899.00, '', '', 1, '2026-08-06 06:11:09', '2026-08-06 06:11:09'),
(44, NULL, 8, 'Water Leakage Repair', 'Fix Washing Machine Water Leakage', 599.00, '', '', 1, '2026-08-06 06:11:09', '2026-08-06 06:11:09'),
(45, NULL, 8, 'Annual Maintenance', 'Complete Washing Machine Maintenance', 1299.00, '', '', 1, '2026-08-06 06:11:09', '2026-08-06 06:11:09');

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
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bookings_customer` (`customer_id`),
  ADD KEY `fk_bookings_provider` (`provider_id`),
  ADD KEY `fk_bookings_service` (`service_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_customer` (`customer_id`),
  ADD KEY `fk_notifications_provider` (`provider_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_booking` (`booking_id`),
  ADD KEY `fk_payments_customer` (`customer_id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_providers_category` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_booking` (`booking_id`),
  ADD KEY `fk_reviews_customer` (`customer_id`),
  ADD KEY `fk_reviews_provider` (`provider_id`),
  ADD KEY `fk_reviews_service` (`service_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_services_provider` (`provider_id`),
  ADD KEY `fk_services_category` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notifications_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payments_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `providers`
--
ALTER TABLE `providers`
  ADD CONSTRAINT `fk_providers_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_services_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_services_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
