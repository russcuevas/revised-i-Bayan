

CREATE TABLE `tbl_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_activity_resident` (`resident_id`),
  KEY `fk_activity_barangay` (`barangay_id`),
  CONSTRAINT `fk_activity_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_activity_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_activity_logs` VALUES('7','25','Logged in to the system','37','2025-10-07 23:51:56');
INSERT INTO `tbl_activity_logs` VALUES('8','25','Logged out to the system','37','2025-10-08 00:29:45');
INSERT INTO `tbl_activity_logs` VALUES('9','25','Logged in to the system','37','2025-10-08 20:18:19');
INSERT INTO `tbl_activity_logs` VALUES('10','25','Logged in to the system','37','2025-10-08 20:22:39');
INSERT INTO `tbl_activity_logs` VALUES('11','25','Logged out to the system','37','2025-10-08 20:23:39');



CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `barangay_id` (`barangay_id`),
  CONSTRAINT `tbl_admin_ibfk_1` FOREIGN KEY (`barangay_id`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_admin` VALUES('12','37','zyrellcalingatan','f7c3bc1d808e04732adf679965ccc34ca7ae3441','zyrell@gmail.com','Zyrell Admin','Male','095748302','administrator','offline','2025-10-07 23:53:35','2025-10-07 23:53:35');



CREATE TABLE `tbl_announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_title` varchar(255) NOT NULL,
  `announcement_content` text NOT NULL,
  `announcement_venue` varchar(255) DEFAULT NULL,
  `announcement_image` varchar(255) DEFAULT NULL,
  `barangay` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_announcement_barangay` (`barangay`),
  CONSTRAINT `fk_announcement_barangay` FOREIGN KEY (`barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_barangay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barangay_name` varchar(255) NOT NULL,
  `municipality` varchar(100) NOT NULL DEFAULT 'mataasnakahoy',
  `zip` char(4) NOT NULL DEFAULT '4223',
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_barangay` VALUES('28','i','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:11:56','2025-06-28 01:00:07');
INSERT INTO `tbl_barangay` VALUES('29','ii','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:12:02','2025-06-28 01:00:02');
INSERT INTO `tbl_barangay` VALUES('30','ii-a','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:12:16','2025-06-28 00:59:56');
INSERT INTO `tbl_barangay` VALUES('31','iii','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:12:25','2025-06-28 00:59:51');
INSERT INTO `tbl_barangay` VALUES('32','iv','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:12:36','2025-06-28 00:59:43');
INSERT INTO `tbl_barangay` VALUES('35','bayorbor','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:12:49','2025-06-28 01:00:12');
INSERT INTO `tbl_barangay` VALUES('36','bubuyan','mataasnakahoy','4223','Bubuyan Mission Sample','Bubuyan Vision Sample','2025-06-26 22:13:16','2025-07-17 00:19:34');
INSERT INTO `tbl_barangay` VALUES('37','calingatan','mataasnakahoy','4223','Calingatan Mission Sample','Calingatan Vision Sample','2025-06-26 22:13:32','2025-07-17 00:19:21');
INSERT INTO `tbl_barangay` VALUES('38','kinalaglagan','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:13:51','2025-06-28 00:58:27');
INSERT INTO `tbl_barangay` VALUES('39','loob','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:14:05','2025-06-28 00:46:32');
INSERT INTO `tbl_barangay` VALUES('40','lumanglipa','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:14:13','2025-06-28 00:46:25');
INSERT INTO `tbl_barangay` VALUES('41','upa','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:14:26','2025-06-28 00:45:35');
INSERT INTO `tbl_barangay` VALUES('42','manggahan','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:14:32','2025-06-28 00:46:17');
INSERT INTO `tbl_barangay` VALUES('43','nangkaan','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:14:44','2025-06-28 00:46:02');
INSERT INTO `tbl_barangay` VALUES('44','san-sebastian','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:14:50','2025-06-28 14:21:32');
INSERT INTO `tbl_barangay` VALUES('45','santol','mataasnakahoy','4223',NULL,NULL,'2025-06-26 22:15:14','2025-06-28 00:45:43');



CREATE TABLE `tbl_barangay_officials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_picture` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `barangay` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `barangay` (`barangay`),
  CONSTRAINT `tbl_barangay_officials_ibfk_1` FOREIGN KEY (`barangay`) REFERENCES `tbl_barangay` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_business_trade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_business_trade` VALUES('7','BT0001','Water Refilling Stations','1000.00','2025-06-26 22:23:30','2025-06-26 22:23:30');
INSERT INTO `tbl_business_trade` VALUES('8','BT0002','Water Retailing Stations','500.00','2025-06-26 22:23:40','2025-06-26 22:23:40');
INSERT INTO `tbl_business_trade` VALUES('9','BT0003','Upholstery Shops','1000.00','2025-06-26 22:23:51','2025-06-26 22:23:51');
INSERT INTO `tbl_business_trade` VALUES('10','BT0004','Trucking Services','1000.00','2025-06-26 22:24:06','2025-06-26 22:24:06');
INSERT INTO `tbl_business_trade` VALUES('11','BT0005','Talipapa Sarisari Store','1000.00','2025-06-26 22:24:18','2025-06-26 22:24:18');
INSERT INTO `tbl_business_trade` VALUES('12','BT0006','Talipapa Meatshop','800.00','2025-06-26 22:24:29','2025-06-26 22:24:29');
INSERT INTO `tbl_business_trade` VALUES('13','BT0007','Talipapa Fish & Vegetable Store','800.00','2025-06-26 22:24:42','2025-06-26 22:24:42');
INSERT INTO `tbl_business_trade` VALUES('14','BT0008','Talent Centers','2000.00','2025-06-26 22:24:52','2025-06-26 22:24:52');
INSERT INTO `tbl_business_trade` VALUES('15','BT0009','Tailoring Shops','1000.00','2025-06-26 22:24:58','2025-06-26 22:24:58');
INSERT INTO `tbl_business_trade` VALUES('16','BT0010','Swimming Pools/Resorts','2000.00','2025-06-26 22:25:09','2025-06-26 22:25:09');
INSERT INTO `tbl_business_trade` VALUES('17','BT0011','STL','2000.00','2025-06-26 22:25:13','2025-06-26 22:25:13');
INSERT INTO `tbl_business_trade` VALUES('18','BT0012','Shoe Repair Shops','800.00','2025-06-26 22:25:21','2025-06-26 22:25:21');
INSERT INTO `tbl_business_trade` VALUES('19','BT0013','Sari-Sari Store','500.00','2025-06-26 22:25:29','2025-06-26 22:25:29');
INSERT INTO `tbl_business_trade` VALUES('20','BT0014','Sari-Sari Store with Liquor and Cigarettes','1000.00','2025-06-26 22:25:42','2025-06-26 22:25:42');
INSERT INTO `tbl_business_trade` VALUES('21','BT0015','Retailer','1000.00','2025-06-26 22:28:18','2025-06-26 22:28:18');
INSERT INTO `tbl_business_trade` VALUES('22','BT0016','Restaurants & Eateries Establishments','1000.00','2025-06-26 22:28:36','2025-06-26 22:28:36');
INSERT INTO `tbl_business_trade` VALUES('23','BT0017','Repair Shops for Mechanical & Electrical Devices','1000.00','2025-06-26 22:28:47','2025-06-26 22:28:47');
INSERT INTO `tbl_business_trade` VALUES('24','BT0018','Refrigeration & Air-conditioning Shops','1000.00','2025-06-26 22:29:02','2025-06-26 22:29:02');
INSERT INTO `tbl_business_trade` VALUES('25','BT0019','Reccaping Shops','1000.00','2025-06-26 22:29:11','2025-06-26 22:29:11');
INSERT INTO `tbl_business_trade` VALUES('26','BT0020','Realtor','1000.00','2025-06-26 22:43:40','2025-06-26 22:43:40');
INSERT INTO `tbl_business_trade` VALUES('27','BT0021','Radiator Repair Shops','1000.00','2025-06-26 22:43:54','2025-06-26 22:43:54');
INSERT INTO `tbl_business_trade` VALUES('28','BT0022','PUJ/PUT Operator','200.00','2025-06-26 22:45:50','2025-06-26 22:45:50');
INSERT INTO `tbl_business_trade` VALUES('29','BT0023','Printing & Bookbinding Shops','500.00','2025-06-26 22:46:02','2025-06-26 22:46:02');
INSERT INTO `tbl_business_trade` VALUES('30','BT0024','Photographic Studios','1000.00','2025-06-26 22:46:12','2025-06-26 22:46:12');
INSERT INTO `tbl_business_trade` VALUES('31','BT0025','Pawnshops','2000.00','2025-06-26 22:46:25','2025-06-26 22:46:25');
INSERT INTO `tbl_business_trade` VALUES('32','BT0026','Parking Lots Establishments','1000.00','2025-06-26 22:46:42','2025-06-26 22:46:42');
INSERT INTO `tbl_business_trade` VALUES('33','BT0027','Packaging','2000.00','2025-06-26 22:46:48','2025-06-26 22:46:48');
INSERT INTO `tbl_business_trade` VALUES('34','BT0028','Optical Clinic','1000.00','2025-06-26 22:46:53','2025-06-26 22:46:53');
INSERT INTO `tbl_business_trade` VALUES('35','BT0029','Motor Repainting Shops','1000.00','2025-06-26 22:47:01','2025-06-26 22:47:01');
INSERT INTO `tbl_business_trade` VALUES('36','BT0030','Money Shops/Money Changer','1500.00','2025-06-26 22:47:12','2025-06-26 22:47:12');
INSERT INTO `tbl_business_trade` VALUES('37','BT0031','Memorial Services','2000.00','2025-06-26 22:47:24','2025-06-26 22:47:24');
INSERT INTO `tbl_business_trade` VALUES('38','BT0032','Medical Services','800.00','2025-06-26 22:47:34','2025-06-26 22:47:34');
INSERT INTO `tbl_business_trade` VALUES('39','BT0033','Medical Foundation','500.00','2025-06-26 22:47:43','2025-06-26 22:47:43');
INSERT INTO `tbl_business_trade` VALUES('40','BT0034','Medical Distributor','500.00','2025-06-26 22:47:55','2025-06-26 22:47:55');
INSERT INTO `tbl_business_trade` VALUES('41','BT0035','Master Plumbing Shops','1000.00','2025-06-26 22:48:00','2025-06-26 22:48:00');
INSERT INTO `tbl_business_trade` VALUES('42','BT0036','Machine Shop/Manufacturer','1000.00','2025-06-26 22:48:11','2025-06-26 22:48:11');
INSERT INTO `tbl_business_trade` VALUES('43','BT0037','LPG Refilling Station','1000.00','2025-06-26 22:48:17','2025-06-26 22:48:17');
INSERT INTO `tbl_business_trade` VALUES('44','BT0038','Litographic Shops','1000.00','2025-06-26 22:48:25','2025-06-26 22:48:25');
INSERT INTO `tbl_business_trade` VALUES('45','BT0039','Lending Investor','1000.00','2025-06-26 22:48:30','2025-06-26 22:48:30');
INSERT INTO `tbl_business_trade` VALUES('46','BT0040','Lamination Establishment','1000.00','2025-06-26 22:48:36','2025-06-26 22:48:36');
INSERT INTO `tbl_business_trade` VALUES('47','BT0041','Key Duplicating Shops','500.00','2025-06-26 22:48:51','2025-06-26 22:48:51');
INSERT INTO `tbl_business_trade` VALUES('48','BT0042','Junk Shops','1000.00','2025-06-26 22:48:54','2025-06-26 22:48:54');
INSERT INTO `tbl_business_trade` VALUES('49','BT0043','Iron Works','1000.00','2025-06-26 22:48:59','2025-06-26 22:48:59');
INSERT INTO `tbl_business_trade` VALUES('50','BT0044','Instruments & Apparatus','1000.00','2025-06-26 22:49:06','2025-06-26 22:49:06');
INSERT INTO `tbl_business_trade` VALUES('51','BT0045','Installing Telecommunications Network','2000.00','2025-06-26 22:49:24','2025-06-26 22:49:24');
INSERT INTO `tbl_business_trade` VALUES('52','BT0046','House & Sign Painting Shops','1000.00','2025-06-26 22:49:35','2025-06-26 22:49:35');
INSERT INTO `tbl_business_trade` VALUES('53','BT0047','Hotel, Motels, & Lodging Houses','5000.00','2025-06-26 22:49:51','2025-06-26 22:49:51');
INSERT INTO `tbl_business_trade` VALUES('54','BT0048','Hospital','5000.00','2025-06-26 22:49:58','2025-06-26 22:49:58');
INSERT INTO `tbl_business_trade` VALUES('55','BT0049','Hardware','4000.00','2025-06-26 22:50:02','2025-06-26 22:50:02');
INSERT INTO `tbl_business_trade` VALUES('56','BT0050','General Merchandise','500.00','2025-06-26 22:50:08','2025-06-26 22:50:08');
INSERT INTO `tbl_business_trade` VALUES('57','BT0051','Gasoline Station','2500.00','2025-06-26 22:50:12','2025-06-26 22:50:12');
INSERT INTO `tbl_business_trade` VALUES('58','BT0052','Garments','1000.00','2025-06-26 22:50:16','2025-06-26 22:50:16');
INSERT INTO `tbl_business_trade` VALUES('59','BT0053','Furniture Shops','1000.00','2025-06-26 22:50:22','2025-06-26 22:50:22');
INSERT INTO `tbl_business_trade` VALUES('60','BT0054','Funeral Parlors','2000.00','2025-06-26 22:50:29','2025-06-26 22:50:29');
INSERT INTO `tbl_business_trade` VALUES('61','BT0055','Foundation','1000.00','2025-06-26 22:50:32','2025-06-26 22:50:32');
INSERT INTO `tbl_business_trade` VALUES('62','BT0056','Food Processing Shops','1000.00','2025-06-26 22:50:37','2025-06-26 22:50:37');
INSERT INTO `tbl_business_trade` VALUES('63','BT0057','Flower Shops','500.00','2025-06-26 22:50:42','2025-06-26 22:50:42');
INSERT INTO `tbl_business_trade` VALUES('64','BT0058','Fast Food','1000.00','2025-06-26 22:50:46','2025-06-26 22:50:46');
INSERT INTO `tbl_business_trade` VALUES('65','BT0059','Development/Research Center','2000.00','2025-06-26 22:50:52','2025-06-26 22:50:52');
INSERT INTO `tbl_business_trade` VALUES('66','BT0060','Dress Shops','500.00','2025-06-26 22:50:57','2025-06-26 22:50:57');
INSERT INTO `tbl_business_trade` VALUES('67','BT0061','Dental Clinics','1000.00','2025-06-26 22:51:06','2025-06-26 22:51:06');
INSERT INTO `tbl_business_trade` VALUES('68','BT0062','Cooperatives','500.00','2025-06-26 22:51:11','2025-06-26 22:51:11');
INSERT INTO `tbl_business_trade` VALUES('69','BT0063','Convenient Stores','5000.00','2025-06-26 22:51:17','2025-06-26 22:51:17');
INSERT INTO `tbl_business_trade` VALUES('70','BT0064','Computer Shop','1000.00','2025-06-26 22:51:22','2025-06-26 22:51:22');
INSERT INTO `tbl_business_trade` VALUES('71','BT0065','Catering','1000.00','2025-06-26 22:51:26','2025-06-26 22:51:26');
INSERT INTO `tbl_business_trade` VALUES('72','BT0066','Brokerage','500.00','2025-06-26 22:51:50','2025-06-26 22:51:50');
INSERT INTO `tbl_business_trade` VALUES('73','BT0067','Brake & Clutch Bonding','500.00','2025-06-26 22:52:00','2025-06-26 22:52:00');
INSERT INTO `tbl_business_trade` VALUES('74','BT0068','Beauty Parlors','1000.00','2025-06-26 22:52:06','2025-06-26 22:52:06');
INSERT INTO `tbl_business_trade` VALUES('75','BT0069','Barber Shop','1000.00','2025-06-26 22:52:11','2025-06-26 22:52:11');
INSERT INTO `tbl_business_trade` VALUES('76','BT0070','Bakery or Bakeshops','1000.00','2025-06-26 22:52:17','2025-06-26 22:52:17');
INSERT INTO `tbl_business_trade` VALUES('77','BT0071','Auto Parts Supply','1000.00','2025-06-26 22:52:30','2025-06-26 22:52:30');
INSERT INTO `tbl_business_trade` VALUES('78','BT0072','Agricultural Products','500.00','2025-06-26 22:52:45','2025-06-26 22:52:45');



CREATE TABLE `tbl_cedula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_number` (`document_number`),
  KEY `for_barangay` (`for_barangay`),
  KEY `fk_resident` (`resident_id`),
  CONSTRAINT `fk_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `tbl_cedula_ibfk_1` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_cedula_claimed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `resident_id` (`resident_id`),
  KEY `for_barangay` (`for_barangay`),
  CONSTRAINT `tbl_cedula_claimed_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`),
  CONSTRAINT `tbl_cedula_claimed_ibfk_2` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `status` varchar(50) DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `fk_certificates_resident` (`resident_id`),
  CONSTRAINT `fk_certificates_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_certificates_claimed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_number` (`document_number`),
  KEY `resident_id` (`resident_id`),
  KEY `for_barangay` (`for_barangay`),
  CONSTRAINT `tbl_certificates_claimed_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_certificates_claimed_ibfk_2` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` varchar(100) DEFAULT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sender_type` enum('resident','admin') NOT NULL,
  `chat_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `resident_id` (`resident_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `tbl_chats_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_chats_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_closure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_closure_resident` (`resident_id`),
  KEY `fk_closure_barangay` (`for_barangay`),
  KEY `fk_closure_trade` (`business_trade`),
  CONSTRAINT `fk_closure_barangay` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_closure_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_closure_trade` FOREIGN KEY (`business_trade`) REFERENCES `tbl_business_trade` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_closure_claimed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_closure_claimed_resident` (`resident_id`),
  KEY `fk_closure_claimed_barangay` (`for_barangay`),
  KEY `fk_closure_claimed_trade` (`business_trade`),
  CONSTRAINT `fk_closure_claimed_barangay` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_closure_claimed_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_closure_claimed_trade` FOREIGN KEY (`business_trade`) REFERENCES `tbl_business_trade` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `barangay` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `resident_id` (`resident_id`),
  KEY `barangay` (`barangay`),
  CONSTRAINT `tbl_feedback_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_feedback_ibfk_2` FOREIGN KEY (`barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_operate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_operate_resident` (`resident_id`),
  KEY `fk_operate_barangay` (`for_barangay`),
  KEY `fk_operate_trade` (`business_trade`),
  CONSTRAINT `fk_operate_barangay` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_operate_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_operate_trade` FOREIGN KEY (`business_trade`) REFERENCES `tbl_business_trade` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_operate_claimed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_claimed_resident` (`resident_id`),
  KEY `fk_claimed_business_trade` (`business_trade`),
  KEY `fk_claimed_barangay` (`for_barangay`),
  CONSTRAINT `fk_claimed_barangay` FOREIGN KEY (`for_barangay`) REFERENCES `tbl_barangay` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_claimed_business_trade` FOREIGN KEY (`business_trade`) REFERENCES `tbl_business_trade` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_claimed_resident` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `tbl_residents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `street` varchar(255) DEFAULT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_barangay` (`barangay_address`),
  CONSTRAINT `fk_barangay` FOREIGN KEY (`barangay_address`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_residents` VALUES('25','Russel Vincent','Cardino','Cuevas','','Male','single','2001-12-26','Lipa City','2','University of Batangas','','37','Cuevas Residence','4','russelcvs','f7c3bc1d808e04732adf679965ccc34ca7ae3441','russelcuevas0@gmail.com','1759852121_Capture.PNG','09495748302','1','offline',NULL,'2025-10-07 17:48:41','2025-10-08 20:23:39');



CREATE TABLE `tbl_residents_family_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` int(11) DEFAULT NULL,
  `barangay_address` int(11) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `resident_id` (`resident_id`),
  KEY `fk_family_barangay` (`barangay_address`),
  CONSTRAINT `fk_family_barangay` FOREIGN KEY (`barangay_address`) REFERENCES `tbl_barangay` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_residents_family_members_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_residents_family_members` VALUES('40','25','37','Cuevas Residence','Russel Vincent','Cardino','Cuevas','','4','Account Owner','Male','2001-12-26','Lipa City','23','single','2','1','0','0','09495748302',NULL,'University of Batangas','','2025-10-07 23:48:41');
INSERT INTO `tbl_residents_family_members` VALUES('41','25','37','Cuevas Residence','Roi Czar','Cardino','Cuevas','','4','sibling','Male','1996-01-18','Lipa City','29','single','1','1','1','29','09495748301','123456789','','Job Order - RITM','2025-10-07 23:59:32');



CREATE TABLE `tbl_superadmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_superadmin` VALUES('1','Zyrell Superadmin','Hidalgo','22','09495748301','zyrellsuperadmin','zyrellhidalgo@gmail.com','f7c3bc1d808e04732adf679965ccc34ca7ae3441');



CREATE TABLE `tbl_system_logs_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `logged_in` datetime DEFAULT NULL,
  `logged_out` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `tbl_system_logs_admin_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_system_logs_admin` VALUES('9','12','2025-10-07 23:53:54','2025-10-08 00:32:19');
INSERT INTO `tbl_system_logs_admin` VALUES('10','12','2025-10-08 20:26:44','2025-10-08 20:27:18');



CREATE TABLE `tbl_system_logs_residents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` int(11) NOT NULL,
  `logged_in` datetime NOT NULL,
  `logged_out` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resident_id` (`resident_id`),
  CONSTRAINT `tbl_system_logs_residents_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tbl_residents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_system_logs_residents` VALUES('7','25','2025-10-07 23:51:56','2025-10-08 00:29:45');
INSERT INTO `tbl_system_logs_residents` VALUES('8','25','2025-10-08 20:18:19',NULL);
INSERT INTO `tbl_system_logs_residents` VALUES('9','25','2025-10-08 20:22:39','2025-10-08 20:23:39');



CREATE TABLE `tbl_system_logs_superadmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `superadmin_id` int(11) NOT NULL,
  `logged_in` datetime DEFAULT NULL,
  `logged_out` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `superadmin_id` (`superadmin_id`),
  CONSTRAINT `tbl_system_logs_superadmin_ibfk_1` FOREIGN KEY (`superadmin_id`) REFERENCES `tbl_superadmin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_system_logs_superadmin` VALUES('6','1','2025-07-17 23:21:19','2025-07-17 23:28:28');
INSERT INTO `tbl_system_logs_superadmin` VALUES('7','1','2025-07-17 23:28:35',NULL);
INSERT INTO `tbl_system_logs_superadmin` VALUES('8','1','2025-10-07 23:52:51','2025-10-07 23:53:42');
INSERT INTO `tbl_system_logs_superadmin` VALUES('9','1','2025-10-08 00:32:27',NULL);
INSERT INTO `tbl_system_logs_superadmin` VALUES('10','1','2025-10-08 20:27:26',NULL);

