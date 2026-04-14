-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 07:23 PM
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
-- Database: `avestra-travel-agency`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phoneNumber` varchar(11) NOT NULL,
  `role` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `date` varchar(250) NOT NULL,
  `status` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `phoneNumber`, `role`, `password`, `profile_image`, `date`, `status`) VALUES
(1, 'Mursalin', 'mursalinleon12@gmail.com', '01971652295', 'Admin', '$2y$10$UMm6rJu0H6ONA3TTGOUd/uYWm31CkHT1U5o19d3VCnrTzSlI42T/K', NULL, '2026-01-15 19:39:09', 'Active'),
(2, 'sabbir Hossain', 'sabbirhossain@gmail.com', '01425252525', 'admin', '$2y$10$v2MopkdRLlPsq5FvfxiJJO/3kQWXtWyTvbKVTEftdrwrQC9cGDRpK', 'profile_87fe2ad86be5a90f692a2d60d54b39cd.png', '2026-01-15 20:17:07', 'Active'),
(3, 'seum', 'seum@gmail.com', '01555555555', 'Admin', '$2y$10$O6G6BUmqknZh7S3gElvVPe3SasFLRZRDdUkx/lXQMR.nuRzI1nCpa', NULL, '2026-01-15 20:26:02', 'Active'),
(4, 'shovon', 'shovon@gmail.com', '01536363636', 'admin', '$2y$10$oTwFYsBDnvvgMO1waQEt6.wRyxlwR6MpEVkbQhNZ1c7sdn.9TSxTS', NULL, '2026-01-15 20:39:28', 'Active'),
(5, 'Riad', 'riad@gmail.com', '01374747474', 'admin', '$2y$10$XaNRXyn07K61eIBQSpZExODBtFQUy/AXEa28oFX56QNCQ6j5VWB26', 'profile_5a1239595d438d77713955f34dca4657.png', '2026-01-19 21:47:01', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `travel_date` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_status` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_email`, `service_type`, `service_name`, `travel_date`, `quantity`, `total_price`, `booking_status`, `payment_status`, `payment_method`, `created_at`) VALUES
(1, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-01-19', 1, 0.00, 'confirmed', 'paid', 'card', '2026-01-20 03:47:45'),
(2, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-01-19', 1, 0.00, 'pending', 'unpaid', NULL, '2026-01-20 03:48:57'),
(3, 'abir@gmail.com', 'hotel', 'Hotel Inna', '2026-01-19', 1, 0.00, 'confirmed', 'paid', 'nagad', '2026-01-20 04:10:30'),
(4, 'abir@gmail.com', 'hotel', 'dg', '2026-01-19', 1, 0.00, 'pending', 'unpaid', NULL, '2026-01-20 04:12:07'),
(5, 'abir@gmail.com', 'hotel', 'Hotel Inna', '2026-01-19', 1, 9647.00, 'pending', 'unpaid', NULL, '2026-01-20 04:13:11'),
(6, 'abir@gmail.com', 'hotel', 'dg', '2026-01-19', 1, 2170.00, 'confirmed', 'paid', 'bkash', '2026-01-20 04:13:15'),
(7, 'abir@gmail.com', 'tour', 'Sundarban Gateway', '2026-01-19', 1, 14500.00, 'confirmed', 'paid', 'card', '2026-01-20 04:21:48'),
(8, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-01-20', 1, 3793.00, 'confirmed', 'paid', 'bkash', '2026-01-20 11:54:32'),
(9, 'abir@gmail.com', 'hotel', 'Sonargoan', '2026-01-20', 1, 6688.00, 'pending', 'unpaid', NULL, '2026-01-20 11:54:56'),
(10, 'abir@gmail.com', 'hotel', 'dg', '2026-01-20', 1, 4274.00, 'pending', 'unpaid', NULL, '2026-01-20 11:55:02'),
(11, 'abir@gmail.com', 'hotel', 'Pila', '2026-01-20', 1, 6626.00, 'pending', 'unpaid', NULL, '2026-01-20 11:55:08'),
(12, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-02-19', 1, 1330.00, 'confirmed', 'paid', 'bkash', '2026-02-19 13:31:42'),
(13, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-02-19', 1, 2155.00, 'pending', 'unpaid', NULL, '2026-02-19 13:42:26'),
(14, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-02-19', 1, 3419.00, 'pending', 'unpaid', NULL, '2026-02-19 13:46:30'),
(15, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-02-19', 1, 3091.00, 'pending', 'unpaid', NULL, '2026-02-19 13:47:27'),
(16, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-02-19', 1, 565.00, 'pending', 'unpaid', NULL, '2026-02-19 13:48:39'),
(17, 'abir@gmail.com', 'tour', 'Sundarban Gateway', '2026-02-20', 1, 14500.00, 'pending', 'unpaid', NULL, '2026-02-21 00:50:51'),
(18, 'abir@gmail.com', 'ticket', 'Dhaka - Sajek', '2026-02-25', 1, 1200.00, 'pending', 'unpaid', NULL, '2026-02-25 10:58:44'),
(19, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-02-26', 1, 1800.00, 'confirmed', 'paid', 'nagad', '2026-02-26 13:46:21'),
(20, 'abir@gmail.com', 'hotel', 'Sonargoan', '2026-02-26', 1, 1132.00, 'pending', 'unpaid', NULL, '2026-02-26 13:50:56'),
(21, 'abir@gmail.com', 'hotel', 'Sonargoan', '2026-02-26', 1, 2399.00, 'pending', 'unpaid', NULL, '2026-02-26 14:22:27'),
(22, 'abir@gmail.com', 'hotel', 'Hilaaaaa', '2026-02-26', 1, 2500.00, 'pending', 'unpaid', NULL, '2026-02-26 14:40:09'),
(23, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-02-26', 1, 1700.00, 'pending', 'unpaid', NULL, '2026-02-26 16:24:54'),
(24, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-02-26', 1, 1200.00, 'pending', 'unpaid', NULL, '2026-02-26 16:30:17'),
(25, 'abir@gmail.com', 'hotel', 'Radison Hotel', '2026-02-26', 1, 3500.00, 'pending', 'unpaid', NULL, '2026-02-26 16:30:26'),
(26, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-02-26', 1, 1700.00, 'pending', 'unpaid', NULL, '2026-02-26 16:40:04'),
(27, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-02-26', 1, 900.00, 'pending', 'unpaid', NULL, '2026-02-26 17:00:31'),
(28, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-02-26', 1, 1200.00, 'pending', 'unpaid', NULL, '2026-02-26 21:17:36'),
(29, 'mursalinleon12@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'confirmed', 'paid', 'bkash', '2026-03-02 00:21:30'),
(30, 'mursalinleon12@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'confirmed', 'paid', 'nagad', '2026-03-02 00:25:34'),
(31, 'mursalinleon12@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'confirmed', 'paid', 'card', '2026-03-02 00:25:40'),
(32, 'mursalinleon12@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'confirmed', 'paid', 'bkash', '2026-03-02 01:15:26'),
(33, 'mursalinleon12@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-01', 1, 900.00, 'confirmed', 'paid', 'bkash', '2026-03-02 01:17:56'),
(34, 'mursalinleon12@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-01', 1, 900.00, 'confirmed', 'paid', 'bkash', '2026-03-02 01:20:31'),
(35, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-01', 1, 900.00, 'pending', 'pending', 'bkash', '2026-03-02 01:36:10'),
(36, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-01', 1, 900.00, 'pending', 'unpaid', NULL, '2026-03-02 02:18:59'),
(37, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'pending', 'unpaid', NULL, '2026-03-02 02:36:43'),
(38, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'pending', 'unpaid', NULL, '2026-03-02 02:37:43'),
(39, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'pending', 'unpaid', NULL, '2026-03-02 02:37:57'),
(40, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-01', 1, 900.00, 'pending', 'unpaid', NULL, '2026-03-02 02:38:59'),
(41, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'pending', 'pending', 'bkash', '2026-03-02 02:47:56'),
(42, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'pending', 'pending', 'bkash', '2026-03-02 02:49:47'),
(43, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'pending', 'pending', 'card', '2026-03-02 02:51:14'),
(44, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-03-01', 1, 1800.00, 'pending', 'pending', 'nagad', '2026-03-02 02:52:50'),
(45, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-03-01', 1, 1800.00, 'pending', 'pending', 'nagad', '2026-03-02 02:55:30'),
(46, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'pending', 'pending', 'nagad', '2026-03-02 02:57:56'),
(47, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-01', 1, 1200.00, 'confirmed', 'paid', 'card', '2026-03-02 03:00:16'),
(48, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 1, 900.00, 'confirmed', 'paid', 'nagad', '2026-03-02 19:43:36'),
(49, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-03-02', 1, 900.00, 'pending', 'unpaid', NULL, '2026-03-02 19:59:51'),
(50, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 1, 900.00, 'pending', 'unpaid', NULL, '2026-03-02 20:00:02'),
(51, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-03-02', 1, 900.00, 'pending', 'paid', 'nagad', '2026-03-02 20:55:53'),
(52, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 1, 900.00, 'pending', 'paid', 'nagad', '2026-03-02 20:55:53'),
(53, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-03-02', 21, 35700.00, 'rejected', 'paid', 'card', '2026-03-02 21:00:43'),
(54, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-02', 6, 7200.00, 'confirmed', 'paid', 'bkash', '2026-03-02 21:09:01'),
(55, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-02', 5, 6000.00, 'confirmed', 'unpaid', NULL, '2026-03-02 21:19:51'),
(56, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-02', 5, 6000.00, 'confirmed', 'paid', 'card', '2026-03-02 21:20:11'),
(57, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 5, 4500.00, 'confirmed', 'paid', 'card', '2026-03-02 21:20:11'),
(58, 'mursalinleon12@gmail.com', 'hotel', 'Hotel Inn', '2026-03-02', 5, 6000.00, 'confirmed', 'paid', 'card', '2026-03-02 21:31:01'),
(59, 'mursalinleon12@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 4, 3600.00, 'confirmed', 'paid', 'card', '2026-03-02 21:31:01'),
(60, 'mursalinleon12@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-03-02', 4, 6800.00, 'confirmed', 'paid', 'card', '2026-03-02 21:31:01'),
(61, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 10, 9000.00, 'confirmed', 'paid', 'bkash', '2026-03-02 21:33:40'),
(62, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 1, 900.00, 'confirmed', 'paid', 'bkash', '2026-03-02 21:35:43'),
(63, 'abir@gmail.com', 'hotel', 'Hotel Inn', '2026-03-02', 1, 1200.00, 'confirmed', 'paid', 'bkash', '2026-03-02 21:35:43'),
(64, 'abir@gmail.com', 'ticket', 'Dhaka - Bandarban', '2026-03-02', 1, 1800.00, 'confirmed', 'paid', 'bkash', '2026-03-02 21:35:43'),
(65, 'mursalinleon12@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 20, 18000.00, 'confirmed', 'unpaid', NULL, '2026-03-02 21:43:59'),
(66, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 10, 9000.00, 'confirmed', 'paid', 'nagad', '2026-03-02 21:47:47'),
(67, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 15, 13500.00, 'pending', 'unpaid', NULL, '2026-03-02 21:48:57'),
(68, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 15, 13500.00, 'confirmed', 'pending', 'card', '2026-03-02 21:49:09'),
(69, 'abir@gmail.com', 'ticket', 'Dhaka - Cox\'s Bazar', '2026-03-02', 1, 900.00, 'pending', 'pending', 'bkash', '2026-03-02 23:08:54'),
(70, 'abir@gmail.com', 'tour', 'Tanguar haour Gateway', '2026-03-02', 1, 900.00, 'pending', 'pending', 'nagad', '2026-03-03 00:05:44');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `status`, `date`) VALUES
(1, 'Mursalin', 'mursalinleon@gmail.com', 'unblock my id', 'Unread', '2026-01-15 20:12:44'),
(2, 'Mursalin', 'mursalinleon@gmail.com', 'unblock my id', 'Unread', '2026-01-15 20:12:52'),
(3, 'Mursalin', 'mursalinleon@gmail.com', 'unblock', 'Unread', '2026-01-15 20:22:39'),
(4, 'Mursalin', 'mursalinleon@gmail.com', 'unblocked', 'Unread', '2026-01-15 20:24:09'),
(5, 'shovon', 'shovon@gmail.com', 'my acccount is block. Please turn into unblock.', 'Unread', '2026-01-19 10:48:35'),
(6, 'shotto', 'shotto@gmail.com', 'unblock me', 'Unread', '2026-01-20 05:49:09'),
(7, 'abir', 'abir@gmail.com', 'hi', 'Unread', '2026-03-01 18:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `username` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phoneNumber` varchar(50) NOT NULL,
  `role` varchar(20) NOT NULL,
  `password` varchar(250) NOT NULL,
  `date` varchar(250) NOT NULL,
  `status` varchar(25) NOT NULL,
  `image` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`username`, `email`, `phoneNumber`, `role`, `password`, `date`, `status`, `image`) VALUES
('abir', 'abir@gmail.com', '01736423599', 'customer', '$2y$10$p.J3sKXFJWNNLvSc/59P2O3zRUJaLDFPsGPwCBzkAsVb/RodBPpBG', '2026-01-15 19:51:46', 'Active', 'profiles/1772127826_profile_87fe2ad86be5a90f692a2d60d54b39cd.png'),
('shotto', 'shotto@gmail.com', '01526262626', 'customer', '$2y$10$6Gd8X3J0Dc1V5iLhYXTzY.HOYfxFrTtbaRCQgwMvW2ZnEiqLmI8O6', '2026-01-15 20:40:32', 'Blocked', ''),
('sarthok', 'sarthok@gmail.com', '01682828282', 'customer', '$2y$10$Ey9LJot031HbBIHfsiog3.6aFDxsCXY2nIIXrt0V2ImUEBirxdS82', '2026-01-19 21:45:49', 'Active', ''),
('jsjsj', 'e@gmail.com', '', 'Admin', '$2y$10$KL0JZ0KflHASDNlxJVPXdOgdv.3qeSJ244MonCrjcM4A2lgl9qOWW', '2026-01-20 05:10:54', 'Blocked', '');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `rating` varchar(10) NOT NULL,
  `rooms` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Inactive',
  `includes_text` varchar(255) DEFAULT NULL,
  `price_per_night` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `location`, `rating`, `rooms`, `status`, `includes_text`, `price_per_night`, `image`, `created_at`) VALUES
('H101', 'Radison Hotel', 'Dhaka', '5', '55', 'Active', 'Breakfast, lunch', 3500.00, 'hotel_69a01a26c32a63.00257479.jpg', NULL),
('H102', 'Hotel Inn', 'Dhaka', '5', '55', 'Active', 'Breakfast, lunch', 3500.00, 'hotel_69a01af18b0387.93515473.jpg', NULL),
('H103', 'Hotel Inn', 'Dhaka', '2', '350', 'Active', 'Breakfast, lunch', 1200.00, 'hotel_69a01b3f1385a9.17207559.jpg', NULL),
('H104', 'Hotel Inn', 'Dhaka', '5', '35', 'Active', 'Breakfast, lunch', 1200.00, 'hotel_69a01b803bbc58.99324054.jpg', NULL),
('H105', 'Hotel Inn', 'Dhaka', '4', '150', 'Active', 'Breakfast, lunch', 1200.00, 'hotel_69a01babbc44d5.89365859.jpg', NULL),
('H106', 'Hotel Inn', 'Dhaka', '4', '155', 'Active', 'Breakfast, lunch', 1200.00, 'hotel_69a01bbe4a7435.29610284.jpg', NULL),
('H107', 'Hotel Inn', 'Dhaka', '4', '155', 'Active', 'Breakfast, lunch', 1200.00, 'hotel_69a01bd02f02c3.00218521.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(11) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_status` varchar(250) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `user_email`, `amount`, `payment_method`, `transaction_id`, `payment_status`, `payment_date`) VALUES
(1, '101', 'user1@gmail.com', 2500.00, 'bKash', 'TXN1001', 'success', '2026-01-19 18:12:32'),
(2, '102', 'user2@gmail.com', 1800.00, 'Nagad', 'TXN1002', 'success', '2026-01-19 18:12:32'),
(3, '103', 'user3@gmail.com', 3200.00, 'Card', 'TXN1003', 'pending', '2026-01-19 18:12:32'),
(4, '104', 'user4@gmail.com', 1500.00, 'Cash', 'TXN1004', 'success', '2026-01-19 18:12:32'),
(5, '105', 'user5@gmail.com', 4000.00, 'bKash', 'TXN1005', 'failed', '2026-01-19 18:12:32'),
(6, '106', 'user6@gmail.com', 2200.00, 'Nagad', 'TXN1006', 'success', '2026-01-19 18:12:32'),
(7, '107', 'user7@gmail.com', 2750.00, 'Card', 'TXN1007', 'pending', '2026-01-19 18:12:32'),
(8, '108', 'user8@gmail.com', 1950.00, 'Cash', 'TXN1008', 'success', '2026-01-19 18:12:32'),
(9, '109', 'user9@gmail.com', 3600.00, 'bKash', 'TXN1009', 'success', '2026-01-19 18:12:32'),
(10, '110', 'user10@gmail.com', 2900.00, 'Nagad', 'TXN1010', 'pending', '2026-01-19 18:12:32'),
(11, '1', 'abir@gmail.com', 0.00, 'card', 'TXN696EA6A92D03E', 'paid', '2026-01-20 03:48:25'),
(12, '3', 'abir@gmail.com', 0.00, 'nagad', 'TXN696EABDE32B7D', 'paid', '2026-01-20 04:10:38'),
(13, '6', 'abir@gmail.com', 2170.00, 'bkash', 'TXN696EAC810AF8C', 'paid', '2026-01-20 04:13:21'),
(14, '7', 'abir@gmail.com', 14500.00, 'card', 'TXN696EAE81D1C97', 'paid', '2026-01-20 04:21:53'),
(15, '8', 'abir@gmail.com', 3793.00, 'bkash', 'TXN696F189BD628E', 'paid', '2026-01-20 11:54:35'),
(16, '12', 'abir@gmail.com', 1330.00, 'bkash', 'TXN6996BC62BD188', 'paid', '2026-02-19 13:31:46'),
(17, '19', 'abir@gmail.com', 1800.00, 'nagad', 'TXN699FFA54BA349', 'paid', '2026-02-26 13:46:28'),
(18, '29', 'mursalinleon12@gmail.com', 1200.00, 'bkash', 'TXN69A483ADAEC07', 'paid', '2026-03-02 00:21:33'),
(19, '29', 'mursalinleon12@gmail.com', 1200.00, 'bkash', 'TXN69A484991D2DC', 'paid', '2026-03-02 00:25:29'),
(20, '30', 'mursalinleon12@gmail.com', 1200.00, 'nagad', 'TXN69A484A04FD02', 'paid', '2026-03-02 00:25:36'),
(21, '31', 'mursalinleon12@gmail.com', 1200.00, 'card', 'TXN69A484A679395', 'paid', '2026-03-02 00:25:42'),
(22, '32', 'mursalinleon12@gmail.com', 1200.00, 'bkash', 'TXN69A49051140C8', 'paid', '2026-03-02 01:15:29'),
(23, '33', 'mursalinleon12@gmail.com', 900.00, 'bkash', 'TXN69A490E9AC227', 'paid', '2026-03-02 01:18:01'),
(24, '34', 'mursalinleon12@gmail.com', 900.00, 'bkash', 'TXN69A491827FDFF', 'success', '2026-03-02 01:20:34'),
(25, '35', 'abir@gmail.com', 900.00, 'bkash', 'TXN69A4952EF2E7C', 'pending', '2026-03-02 01:36:14'),
(26, '41', 'abir@gmail.com', 1200.00, 'bkash', 'TXN69A4A5FF73D7A', 'pending', '2026-03-02 02:47:59'),
(27, '42', 'abir@gmail.com', 1200.00, 'bkash', 'TXN69A4A66DCB682', 'pending', '2026-03-02 02:49:49'),
(28, '43', 'abir@gmail.com', 1200.00, 'card', 'TXN69A4A6C538FA5', 'pending', '2026-03-02 02:51:17'),
(29, '44', 'abir@gmail.com', 1800.00, 'nagad', 'TXN69A4A725787EE', 'pending', '2026-03-02 02:52:53'),
(30, '45', 'abir@gmail.com', 1800.00, 'nagad', 'TXN69A4A7C703CC3', 'pending', '2026-03-02 02:55:35'),
(31, '46', 'abir@gmail.com', 1200.00, 'nagad', 'TXN69A4A87E5A2E2', 'pending', '2026-03-02 02:58:38'),
(32, '47', 'abir@gmail.com', 1200.00, 'card', 'TXN69A4A8E2E5C70', 'success', '2026-03-02 03:00:18'),
(33, '48', 'abir@gmail.com', 900.00, 'nagad', 'TXN69A5940E18103', 'success', '2026-03-02 19:43:42'),
(34, '48', 'abir@gmail.com', 900.00, 'nagad', 'TXN69A594241A982', 'rejected', '2026-03-02 19:44:04'),
(35, '66', 'abir@gmail.com', 9000.00, 'nagad', 'CART69A5B126DDF8E', 'success', '2026-03-02 21:47:50'),
(36, '68', 'abir@gmail.com', 13500.00, 'card', 'CART69A5B1792177E', 'pending', '2026-03-02 21:49:13'),
(37, '69', 'abir@gmail.com', 900.00, 'bkash', 'TXN69A5C42AAD10B', 'pending', '2026-03-02 23:08:58'),
(38, '70', 'abir@gmail.com', 900.00, 'nagad', 'TXN69A5D17B8E131', 'pending', '2026-03-03 00:05:47');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `ticket_code` varchar(30) NOT NULL,
  `ticket_type` varchar(20) NOT NULL DEFAULT 'Bus',
  `route` varchar(100) NOT NULL,
  `bus_class` enum('AC','Non-AC') NOT NULL,
  `seat_count` int(11) NOT NULL,
  `status` varchar(30) NOT NULL,
  `includes_text` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `provider` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_code`, `ticket_type`, `route`, `bus_class`, `seat_count`, `status`, `includes_text`, `price`, `provider`, `image`) VALUES
(19, 'TB100', 'Bus', 'Dhaka - Sajek', 'AC', 45, 'active', NULL, 1200.00, NULL, 'ticket_1771995368_3841.png'),
(20, 'TB101', 'Bus', 'Dhaka - Sajek', 'Non-AC', 45, 'active', NULL, 900.00, NULL, 'ticket_1771995703_5257.png'),
(21, 'TB102', 'Bus', 'Dhaka - Cox\'s Bazar', 'AC', 45, 'active', NULL, 900.00, NULL, 'ticket_1771995796_1486.png'),
(22, 'TB103', 'Bus', 'Dhaka - Cox\'s Bazar', 'AC', 45, 'active', NULL, 1800.00, NULL, 'ticket_1771996636_5697.png'),
(23, 'TB104', 'Bus', 'Dhaka - Bandarban', 'AC', 45, 'active', NULL, 1800.00, NULL, 'ticket_1771997763_7916.png'),
(24, 'TB105', 'Bus', 'Dhaka - Bandarban', 'AC', 45, 'active', NULL, 1800.00, NULL, 'ticket_1772042215_3503.png'),
(25, 'TB106', 'Bus', 'Dhaka - Bandarban', 'AC', 45, 'active', NULL, 1700.00, NULL, 'ticket_1772043078_3937.png');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` varchar(255) NOT NULL,
  `name` varchar(250) NOT NULL,
  `destination` varchar(250) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `price` varchar(250) NOT NULL,
  `status` varchar(20) NOT NULL,
  `includes_text` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `name`, `destination`, `duration`, `price`, `status`, `includes_text`, `image`) VALUES
('', 'Sundarban Gateway', 'Sundarban', '7 Days', '14500', 'Active', NULL, NULL),
('', 'Cox\'s Bazar Gateway', 'Dhaka - Cox\'s Bazar', '3 Days', '6500', 'Active', NULL, NULL),
('', 'Stiakunda Gateway', 'Stiakunda', '1 Day', '1000', 'Active', 'Breakfast, lunch', 'tour_1772104771_4018.png'),
('', 'Stiakunda Gateway', 'Stiakunda', '1 Day', '900', 'Active', 'Breakfast, lunch', 'tour_1772104849_9012.png'),
('TRV-101', 'Tanguar haour Gateway', 'Tanguar haour', '1 Day', '900', 'Active', 'Breakfast, lunch', 'tour_1772110865_9669.png'),
('T102', 'Tanguar haour Gateway', 'Tanguar haour', '1 Day', '900', 'Active', 'Breakfast, lunch', 'tour_1772111394_4615.png'),
('T103', 'Tanguar haour Gateway', 'Tanguar haour', '1 Day', '900', 'Active', 'Breakfast, lunch', 'tour_1772111423_7530.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
