-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2025 at 05:33 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ibayandatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_activity_logs`
--

CREATE TABLE `tbl_activity_logs` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement`
--

CREATE TABLE `tbl_announcement` (
  `id` int(11) NOT NULL,
  `announcement_title` varchar(255) NOT NULL,
  `announcement_content` text NOT NULL,
  `announcement_venue` varchar(255) DEFAULT NULL,
  `announcement_image` varchar(255) DEFAULT NULL,
  `barangay` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barangay`
--

CREATE TABLE `tbl_barangay` (
  `id` int(11) NOT NULL,
  `barangay_name` varchar(255) NOT NULL,
  `municipality` varchar(100) NOT NULL DEFAULT 'mataasnakahoy',
  `zip` char(4) NOT NULL DEFAULT '4223',
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_barangay`
--

INSERT INTO `tbl_barangay` (`id`, `barangay_name`, `municipality`, `zip`, `mission`, `vision`, `created_at`, `updated_at`) VALUES
(28, 'i', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:11:56', '2025-06-28 01:00:07'),
(29, 'ii', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:12:02', '2025-06-28 01:00:02'),
(30, 'ii-a', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:12:16', '2025-06-28 00:59:56'),
(31, 'iii', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:12:25', '2025-06-28 00:59:51'),
(32, 'iv', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:12:36', '2025-06-28 00:59:43'),
(35, 'bayorbor', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:12:49', '2025-06-28 01:00:12'),
(36, 'bubuyan', 'mataasnakahoy', '4223', 'Bubuyan Mission Sample', 'Bubuyan Vision Sample', '2025-06-26 22:13:16', '2025-07-17 00:19:34'),
(37, 'calingatan', 'mataasnakahoy', '4223', 'Calingatan Mission Sample', 'Calingatan Vision Sample', '2025-06-26 22:13:32', '2025-07-17 00:19:21'),
(38, 'kinalaglagan', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:13:51', '2025-06-28 00:58:27'),
(39, 'loob', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:14:05', '2025-06-28 00:46:32'),
(40, 'lumanglipa', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:14:13', '2025-06-28 00:46:25'),
(41, 'upa', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:14:26', '2025-06-28 00:45:35'),
(42, 'manggahan', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:14:32', '2025-06-28 00:46:17'),
(43, 'nangkaan', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:14:44', '2025-06-28 00:46:02'),
(44, 'san-sebastian', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:14:50', '2025-06-28 14:21:32'),
(45, 'santol', 'mataasnakahoy', '4223', NULL, NULL, '2025-06-26 22:15:14', '2025-06-28 00:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barangay_officials`
--

CREATE TABLE `tbl_barangay_officials` (
  `id` int(11) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `barangay` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_business_trade`
--

CREATE TABLE `tbl_business_trade` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_business_trade`
--

INSERT INTO `tbl_business_trade` (`id`, `code`, `name`, `price`, `created_at`, `updated_at`) VALUES
(7, 'BT0001', 'Water Refilling Stations', '1000.00', '2025-06-26 14:23:30', '2025-06-26 14:23:30'),
(8, 'BT0002', 'Water Retailing Stations', '500.00', '2025-06-26 14:23:40', '2025-06-26 14:23:40'),
(9, 'BT0003', 'Upholstery Shops', '1000.00', '2025-06-26 14:23:51', '2025-06-26 14:23:51'),
(10, 'BT0004', 'Trucking Services', '1000.00', '2025-06-26 14:24:06', '2025-06-26 14:24:06'),
(11, 'BT0005', 'Talipapa Sarisari Store', '1000.00', '2025-06-26 14:24:18', '2025-06-26 14:24:18'),
(12, 'BT0006', 'Talipapa Meatshop', '800.00', '2025-06-26 14:24:29', '2025-06-26 14:24:29'),
(13, 'BT0007', 'Talipapa Fish & Vegetable Store', '800.00', '2025-06-26 14:24:42', '2025-06-26 14:24:42'),
(14, 'BT0008', 'Talent Centers', '2000.00', '2025-06-26 14:24:52', '2025-06-26 14:24:52'),
(15, 'BT0009', 'Tailoring Shops', '1000.00', '2025-06-26 14:24:58', '2025-06-26 14:24:58'),
(16, 'BT0010', 'Swimming Pools/Resorts', '2000.00', '2025-06-26 14:25:09', '2025-06-26 14:25:09'),
(17, 'BT0011', 'STL', '2000.00', '2025-06-26 14:25:13', '2025-06-26 14:25:13'),
(18, 'BT0012', 'Shoe Repair Shops', '800.00', '2025-06-26 14:25:21', '2025-06-26 14:25:21'),
(19, 'BT0013', 'Sari-Sari Store', '500.00', '2025-06-26 14:25:29', '2025-06-26 14:25:29'),
(20, 'BT0014', 'Sari-Sari Store with Liquor and Cigarettes', '1000.00', '2025-06-26 14:25:42', '2025-06-26 14:25:42'),
(21, 'BT0015', 'Retailer', '1000.00', '2025-06-26 14:28:18', '2025-06-26 14:28:18'),
(22, 'BT0016', 'Restaurants & Eateries Establishments', '1000.00', '2025-06-26 14:28:36', '2025-06-26 14:28:36'),
(23, 'BT0017', 'Repair Shops for Mechanical & Electrical Devices', '1000.00', '2025-06-26 14:28:47', '2025-06-26 14:28:47'),
(24, 'BT0018', 'Refrigeration & Air-conditioning Shops', '1000.00', '2025-06-26 14:29:02', '2025-06-26 14:29:02'),
(25, 'BT0019', 'Reccaping Shops', '1000.00', '2025-06-26 14:29:11', '2025-06-26 14:29:11'),
(26, 'BT0020', 'Realtor', '1000.00', '2025-06-26 14:43:40', '2025-06-26 14:43:40'),
(27, 'BT0021', 'Radiator Repair Shops', '1000.00', '2025-06-26 14:43:54', '2025-06-26 14:43:54'),
(28, 'BT0022', 'PUJ/PUT Operator', '200.00', '2025-06-26 14:45:50', '2025-06-26 14:45:50'),
(29, 'BT0023', 'Printing & Bookbinding Shops', '500.00', '2025-06-26 14:46:02', '2025-06-26 14:46:02'),
(30, 'BT0024', 'Photographic Studios', '1000.00', '2025-06-26 14:46:12', '2025-06-26 14:46:12'),
(31, 'BT0025', 'Pawnshops', '2000.00', '2025-06-26 14:46:25', '2025-06-26 14:46:25'),
(32, 'BT0026', 'Parking Lots Establishments', '1000.00', '2025-06-26 14:46:42', '2025-06-26 14:46:42'),
(33, 'BT0027', 'Packaging', '2000.00', '2025-06-26 14:46:48', '2025-06-26 14:46:48'),
(34, 'BT0028', 'Optical Clinic', '1000.00', '2025-06-26 14:46:53', '2025-06-26 14:46:53'),
(35, 'BT0029', 'Motor Repainting Shops', '1000.00', '2025-06-26 14:47:01', '2025-06-26 14:47:01'),
(36, 'BT0030', 'Money Shops/Money Changer', '1500.00', '2025-06-26 14:47:12', '2025-06-26 14:47:12'),
(37, 'BT0031', 'Memorial Services', '2000.00', '2025-06-26 14:47:24', '2025-06-26 14:47:24'),
(38, 'BT0032', 'Medical Services', '800.00', '2025-06-26 14:47:34', '2025-06-26 14:47:34'),
(39, 'BT0033', 'Medical Foundation', '500.00', '2025-06-26 14:47:43', '2025-06-26 14:47:43'),
(40, 'BT0034', 'Medical Distributor', '500.00', '2025-06-26 14:47:55', '2025-06-26 14:47:55'),
(41, 'BT0035', 'Master Plumbing Shops', '1000.00', '2025-06-26 14:48:00', '2025-06-26 14:48:00'),
(42, 'BT0036', 'Machine Shop/Manufacturer', '1000.00', '2025-06-26 14:48:11', '2025-06-26 14:48:11'),
(43, 'BT0037', 'LPG Refilling Station', '1000.00', '2025-06-26 14:48:17', '2025-06-26 14:48:17'),
(44, 'BT0038', 'Litographic Shops', '1000.00', '2025-06-26 14:48:25', '2025-06-26 14:48:25'),
(45, 'BT0039', 'Lending Investor', '1000.00', '2025-06-26 14:48:30', '2025-06-26 14:48:30'),
(46, 'BT0040', 'Lamination Establishment', '1000.00', '2025-06-26 14:48:36', '2025-06-26 14:48:36'),
(47, 'BT0041', 'Key Duplicating Shops', '500.00', '2025-06-26 14:48:51', '2025-06-26 14:48:51'),
(48, 'BT0042', 'Junk Shops', '1000.00', '2025-06-26 14:48:54', '2025-06-26 14:48:54'),
(49, 'BT0043', 'Iron Works', '1000.00', '2025-06-26 14:48:59', '2025-06-26 14:48:59'),
(50, 'BT0044', 'Instruments & Apparatus', '1000.00', '2025-06-26 14:49:06', '2025-06-26 14:49:06'),
(51, 'BT0045', 'Installing Telecommunications Network', '2000.00', '2025-06-26 14:49:24', '2025-06-26 14:49:24'),
(52, 'BT0046', 'House & Sign Painting Shops', '1000.00', '2025-06-26 14:49:35', '2025-06-26 14:49:35'),
(53, 'BT0047', 'Hotel, Motels, & Lodging Houses', '5000.00', '2025-06-26 14:49:51', '2025-06-26 14:49:51'),
(54, 'BT0048', 'Hospital', '5000.00', '2025-06-26 14:49:58', '2025-06-26 14:49:58'),
(55, 'BT0049', 'Hardware', '4000.00', '2025-06-26 14:50:02', '2025-06-26 14:50:02'),
(56, 'BT0050', 'General Merchandise', '500.00', '2025-06-26 14:50:08', '2025-06-26 14:50:08'),
(57, 'BT0051', 'Gasoline Station', '2500.00', '2025-06-26 14:50:12', '2025-06-26 14:50:12'),
(58, 'BT0052', 'Garments', '1000.00', '2025-06-26 14:50:16', '2025-06-26 14:50:16'),
(59, 'BT0053', 'Furniture Shops', '1000.00', '2025-06-26 14:50:22', '2025-06-26 14:50:22'),
(60, 'BT0054', 'Funeral Parlors', '2000.00', '2025-06-26 14:50:29', '2025-06-26 14:50:29'),
(61, 'BT0055', 'Foundation', '1000.00', '2025-06-26 14:50:32', '2025-06-26 14:50:32'),
(62, 'BT0056', 'Food Processing Shops', '1000.00', '2025-06-26 14:50:37', '2025-06-26 14:50:37'),
(63, 'BT0057', 'Flower Shops', '500.00', '2025-06-26 14:50:42', '2025-06-26 14:50:42'),
(64, 'BT0058', 'Fast Food', '1000.00', '2025-06-26 14:50:46', '2025-06-26 14:50:46'),
(65, 'BT0059', 'Development/Research Center', '2000.00', '2025-06-26 14:50:52', '2025-06-26 14:50:52'),
(66, 'BT0060', 'Dress Shops', '500.00', '2025-06-26 14:50:57', '2025-06-26 14:50:57'),
(67, 'BT0061', 'Dental Clinics', '1000.00', '2025-06-26 14:51:06', '2025-06-26 14:51:06'),
(68, 'BT0062', 'Cooperatives', '500.00', '2025-06-26 14:51:11', '2025-06-26 14:51:11'),
(69, 'BT0063', 'Convenient Stores', '5000.00', '2025-06-26 14:51:17', '2025-06-26 14:51:17'),
(70, 'BT0064', 'Computer Shop', '1000.00', '2025-06-26 14:51:22', '2025-06-26 14:51:22'),
(71, 'BT0065', 'Catering', '1000.00', '2025-06-26 14:51:26', '2025-06-26 14:51:26'),
(72, 'BT0066', 'Brokerage', '500.00', '2025-06-26 14:51:50', '2025-06-26 14:51:50'),
(73, 'BT0067', 'Brake & Clutch Bonding', '500.00', '2025-06-26 14:52:00', '2025-06-26 14:52:00'),
(74, 'BT0068', 'Beauty Parlors', '1000.00', '2025-06-26 14:52:06', '2025-06-26 14:52:06'),
(75, 'BT0069', 'Barber Shop', '1000.00', '2025-06-26 14:52:11', '2025-06-26 14:52:11'),
(76, 'BT0070', 'Bakery or Bakeshops', '1000.00', '2025-06-26 14:52:17', '2025-06-26 14:52:17'),
(77, 'BT0071', 'Auto Parts Supply', '1000.00', '2025-06-26 14:52:30', '2025-06-26 14:52:30'),
(78, 'BT0072', 'Agricultural Products', '500.00', '2025-06-26 14:52:45', '2025-06-26 14:52:45');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cedula`
--

CREATE TABLE `tbl_cedula` (
  `id` int(11) NOT NULL,
  `certificate_type` varchar(100) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `document_number` varchar(50) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `civil_status` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `tin` varchar(50) NOT NULL,
  `purok` varchar(50) NOT NULL,
  `profession` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `valid_id` varchar(255) NOT NULL,
  `birth_certificate` varchar(255) NOT NULL,
  `is_resident` tinyint(1) NOT NULL,
  `purpose` text NOT NULL,
  `for_barangay` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `picked_up_by` varchar(255) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cedula_claimed`
--

CREATE TABLE `tbl_cedula_claimed` (
  `id` int(11) NOT NULL,
  `certificate_type` varchar(100) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `document_number` varchar(50) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `civil_status` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `tin` varchar(50) NOT NULL,
  `profession` varchar(100) NOT NULL,
  `purok` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `valid_id` varchar(255) NOT NULL,
  `birth_certificate` varchar(255) NOT NULL,
  `is_resident` tinyint(1) NOT NULL,
  `purpose` text NOT NULL,
  `for_barangay` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Claimed',
  `picked_up_by` varchar(255) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_certificates`
--

CREATE TABLE `tbl_certificates` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `certificate_type` varchar(100) DEFAULT NULL,
  `purpose` varchar(100) DEFAULT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `is_resident` varchar(10) DEFAULT NULL,
  `picked_up_by` varchar(150) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `for_barangay` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_certificates_claimed`
--

CREATE TABLE `tbl_certificates_claimed` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `document_number` varchar(100) NOT NULL,
  `picked_up_by` varchar(255) DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `certificate_type` varchar(100) NOT NULL,
  `purpose` text DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `total_amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `for_barangay` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_chats`
--

CREATE TABLE `tbl_chats` (
  `id` int(11) NOT NULL,
  `room_id` varchar(100) DEFAULT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sender_type` enum('resident','admin') NOT NULL,
  `chat_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_closure`
--

CREATE TABLE `tbl_closure` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `picked_up_by` varchar(100) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `certificate_type` varchar(100) DEFAULT 'Clearance to operate',
  `purpose` text NOT NULL,
  `business_name` varchar(150) NOT NULL,
  `business_trade` int(11) NOT NULL,
  `business_address` varchar(255) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_purok` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `for_barangay` int(11) NOT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `is_resident` varchar(10) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_closure_claimed`
--

CREATE TABLE `tbl_closure_claimed` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `picked_up_by` varchar(100) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `certificate_type` varchar(100) DEFAULT 'Clearance to operate',
  `purpose` text NOT NULL,
  `business_name` varchar(150) NOT NULL,
  `business_trade` int(11) NOT NULL,
  `business_address` varchar(255) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_purok` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `for_barangay` int(11) NOT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `is_resident` varchar(10) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `barangay` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_operate`
--

CREATE TABLE `tbl_operate` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `picked_up_by` varchar(100) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `certificate_type` varchar(100) DEFAULT 'Clearance to operate',
  `purpose` text NOT NULL,
  `business_name` varchar(150) NOT NULL,
  `business_trade` int(11) NOT NULL,
  `business_address` varchar(255) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_purok` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `for_barangay` int(11) NOT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `is_resident` varchar(10) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_operate_claimed`
--

CREATE TABLE `tbl_operate_claimed` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `document_number` varchar(255) DEFAULT NULL,
  `picked_up_by` varchar(255) DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `certificate_type` varchar(100) DEFAULT 'Clearance to operate',
  `purpose` text DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `business_trade` int(11) DEFAULT NULL,
  `business_address` text DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `owner_purok` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `for_barangay` int(11) DEFAULT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `is_resident` varchar(10) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_residents`
--

CREATE TABLE `tbl_residents` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `gender` varchar(100) NOT NULL,
  `civil_status` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `birthplace` varchar(255) NOT NULL,
  `is_working` varchar(255) NOT NULL,
  `school` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `barangay_address` int(11) NOT NULL,
  `purok` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `valid_id` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `is_online` enum('offline','online') DEFAULT 'offline',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_residents_family_members`
--

CREATE TABLE `tbl_residents_family_members` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `barangay_address` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `relationship` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `date_of_birth` date NOT NULL,
  `birthplace` varchar(150) NOT NULL,
  `age` int(11) NOT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `is_working` varchar(100) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `is_barangay_voted` tinyint(1) DEFAULT 0,
  `years_in_barangay` int(11) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `philhealth_number` varchar(50) DEFAULT NULL,
  `school` varchar(150) DEFAULT NULL,
  `occupation` varchar(150) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_superadmin`
--

CREATE TABLE `tbl_superadmin` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_superadmin`
--

INSERT INTO `tbl_superadmin` (`id`, `first_name`, `last_name`, `age`, `phone_number`, `username`, `email`, `password`) VALUES
(1, 'Zyrell Superadmin', 'Hidalgo', 22, '09495748301', 'zyrellsuperadmin', 'zyrellhidalgo@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_system_logs_admin`
--

CREATE TABLE `tbl_system_logs_admin` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `logged_in` datetime DEFAULT NULL,
  `logged_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_system_logs_residents`
--

CREATE TABLE `tbl_system_logs_residents` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `logged_in` datetime NOT NULL,
  `logged_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_system_logs_superadmin`
--

CREATE TABLE `tbl_system_logs_superadmin` (
  `id` int(11) NOT NULL,
  `superadmin_id` int(11) NOT NULL,
  `logged_in` datetime DEFAULT NULL,
  `logged_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_system_logs_superadmin`
--

INSERT INTO `tbl_system_logs_superadmin` (`id`, `superadmin_id`, `logged_in`, `logged_out`) VALUES
(6, 1, '2025-07-17 23:21:19', '2025-07-17 23:28:28'),
(7, 1, '2025-07-17 23:28:35', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_activity_resident` (`resident_id`),
  ADD KEY `fk_activity_barangay` (`barangay_id`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `barangay_id` (`barangay_id`);

--
-- Indexes for table `tbl_announcement`
--
ALTER TABLE `tbl_announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_announcement_barangay` (`barangay`);

--
-- Indexes for table `tbl_barangay`
--
ALTER TABLE `tbl_barangay`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_barangay_officials`
--
ALTER TABLE `tbl_barangay_officials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barangay` (`barangay`);

--
-- Indexes for table `tbl_business_trade`
--
ALTER TABLE `tbl_business_trade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `tbl_cedula`
--
ALTER TABLE `tbl_cedula`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_number` (`document_number`),
  ADD KEY `for_barangay` (`for_barangay`),
  ADD KEY `fk_resident` (`resident_id`);

--
-- Indexes for table `tbl_cedula_claimed`
--
ALTER TABLE `tbl_cedula_claimed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `for_barangay` (`for_barangay`);

--
-- Indexes for table `tbl_certificates`
--
ALTER TABLE `tbl_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_certificates_resident` (`resident_id`);

--
-- Indexes for table `tbl_certificates_claimed`
--
ALTER TABLE `tbl_certificates_claimed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_number` (`document_number`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `for_barangay` (`for_barangay`);

--
-- Indexes for table `tbl_chats`
--
ALTER TABLE `tbl_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `tbl_closure`
--
ALTER TABLE `tbl_closure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_closure_resident` (`resident_id`),
  ADD KEY `fk_closure_barangay` (`for_barangay`),
  ADD KEY `fk_closure_trade` (`business_trade`);

--
-- Indexes for table `tbl_closure_claimed`
--
ALTER TABLE `tbl_closure_claimed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_closure_claimed_resident` (`resident_id`),
  ADD KEY `fk_closure_claimed_barangay` (`for_barangay`),
  ADD KEY `fk_closure_claimed_trade` (`business_trade`);

--
-- Indexes for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `barangay` (`barangay`);

--
-- Indexes for table `tbl_operate`
--
ALTER TABLE `tbl_operate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_operate_resident` (`resident_id`),
  ADD KEY `fk_operate_barangay` (`for_barangay`),
  ADD KEY `fk_operate_trade` (`business_trade`);

--
-- Indexes for table `tbl_operate_claimed`
--
ALTER TABLE `tbl_operate_claimed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_claimed_resident` (`resident_id`),
  ADD KEY `fk_claimed_business_trade` (`business_trade`),
  ADD KEY `fk_claimed_barangay` (`for_barangay`);

--
-- Indexes for table `tbl_residents`
--
ALTER TABLE `tbl_residents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_barangay` (`barangay_address`);

--
-- Indexes for table `tbl_residents_family_members`
--
ALTER TABLE `tbl_residents_family_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `fk_family_barangay` (`barangay_address`);

--
-- Indexes for table `tbl_superadmin`
--
ALTER TABLE `tbl_superadmin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tbl_system_logs_admin`
--
ALTER TABLE `tbl_system_logs_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `tbl_system_logs_residents`
--
ALTER TABLE `tbl_system_logs_residents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `tbl_system_logs_superadmin`
--
ALTER TABLE `tbl_system_logs_superadmin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `superadmin_id` (`superadmin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_announcement`
--
ALTER TABLE `tbl_announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_barangay`
--
ALTER TABLE `tbl_barangay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tbl_barangay_officials`
--
ALTER TABLE `tbl_barangay_officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_business_trade`
--
ALTER TABLE `tbl_business_trade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `tbl_cedula`
--
ALTER TABLE `tbl_cedula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_cedula_claimed`
--
ALTER TABLE `tbl_cedula_claimed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_certificates`
--
ALTER TABLE `tbl_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_certificates_claimed`
--
ALTER TABLE `tbl_certificates_claimed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_chats`
--
ALTER TABLE `tbl_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tbl_closure`
--
ALTER TABLE `tbl_closure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_closure_claimed`
--
ALTER TABLE `tbl_closure_claimed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_operate`
--
ALTER TABLE `tbl_operate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_operate_claimed`
--
ALTER TABLE `tbl_operate_claimed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_residents`
--
ALTER TABLE `tbl_residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tbl_residents_family_members`
--
ALTER TABLE `tbl_residents_family_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `tbl_superadmin`
--
ALTER TABLE `tbl_superadmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_system_logs_admin`
--
ALTER TABLE `tbl_system_logs_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_system_logs_residents`
--
ALTER TABLE `tbl_system_logs_residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_system_logs_superadmin`
--
ALTER TABLE `tbl_system_logs_superadmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  ADD CONSTRAINT `fk_activity_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_activity_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD CONSTRAINT `tbl_admin_ibfk_1` FOREIGN KEY (`barangay_id`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_announcement`
--
ALTER TABLE `tbl_announcement`
  ADD CONSTRAINT `fk_announcement_barangay` FOREIGN KEY (`barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_barangay_officials`
--
ALTER TABLE `tbl_barangay_officials`
  ADD CONSTRAINT `tbl_barangay_officials_ibfk_1` FOREIGN KEY (`barangay`) REFERENCES `tbl_barangay` (`id`);

--
-- Constraints for table `tbl_cedula`
--
ALTER TABLE `tbl_cedula`
  ADD CONSTRAINT `fk_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_cedula_ibfk_1` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`);

--
-- Constraints for table `tbl_cedula_claimed`
--
ALTER TABLE `tbl_cedula_claimed`
  ADD CONSTRAINT `tbl_cedula_claimed_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`),
  ADD CONSTRAINT `tbl_cedula_claimed_ibfk_2` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`);

--
-- Constraints for table `tbl_certificates`
--
ALTER TABLE `tbl_certificates`
  ADD CONSTRAINT `fk_certificates_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_certificates_claimed`
--
ALTER TABLE `tbl_certificates_claimed`
  ADD CONSTRAINT `tbl_certificates_claimed_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_certificates_claimed_ibfk_2` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_chats`
--
ALTER TABLE `tbl_chats`
  ADD CONSTRAINT `tbl_chats_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_chats_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_closure`
--
ALTER TABLE `tbl_closure`
  ADD CONSTRAINT `fk_closure_barangay` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_closure_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_closure_trade` FOREIGN KEY (`business_trade`) REFERENCES `tbl_business_trade` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_closure_claimed`
--
ALTER TABLE `tbl_closure_claimed`
  ADD CONSTRAINT `fk_closure_claimed_barangay` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_closure_claimed_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_closure_claimed_trade` FOREIGN KEY (`business_trade`) REFERENCES `tbl_business_trade` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD CONSTRAINT `tbl_feedback_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_feedback_ibfk_2` FOREIGN KEY (`barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_operate`
--
ALTER TABLE `tbl_operate`
  ADD CONSTRAINT `fk_operate_barangay` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_operate_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_operate_trade` FOREIGN KEY (`business_trade`) REFERENCES `tbl_business_trade` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_operate_claimed`
--
ALTER TABLE `tbl_operate_claimed`
  ADD CONSTRAINT `fk_claimed_barangay` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_claimed_business_trade` FOREIGN KEY (`business_trade`) REFERENCES `tbl_business_trade` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_claimed_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_residents`
--
ALTER TABLE `tbl_residents`
  ADD CONSTRAINT `fk_barangay` FOREIGN KEY (`barangay_address`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_residents_family_members`
--
ALTER TABLE `tbl_residents_family_members`
  ADD CONSTRAINT `fk_family_barangay` FOREIGN KEY (`barangay_address`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_residents_family_members_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`);

--
-- Constraints for table `tbl_system_logs_admin`
--
ALTER TABLE `tbl_system_logs_admin`
  ADD CONSTRAINT `tbl_system_logs_admin_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`id`);

--
-- Constraints for table `tbl_system_logs_residents`
--
ALTER TABLE `tbl_system_logs_residents`
  ADD CONSTRAINT `tbl_system_logs_residents_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`);

--
-- Constraints for table `tbl_system_logs_superadmin`
--
ALTER TABLE `tbl_system_logs_superadmin`
  ADD CONSTRAINT `tbl_system_logs_superadmin_ibfk_1` FOREIGN KEY (`superadmin_id`) REFERENCES `tbl_superadmin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
