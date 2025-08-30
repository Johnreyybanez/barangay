-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 03:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_barangay`
--

-- --------------------------------------------------------

--
-- Table structure for table `assistance_requests`
--

CREATE TABLE `assistance_requests` (
  `request_id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `service_type_id` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `document_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assistance_requests`
--

INSERT INTO `assistance_requests` (`request_id`, `resident_id`, `service_type_id`, `request_date`, `status`, `document_path`) VALUES
(32, 19, 1, '2025-04-09', 'Pending', 'uploads/1745341150_491008595_1966467593878671_638077197717459863_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_clearances`
--

CREATE TABLE `barangay_clearances` (
  `clearance_id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `clearance_type_id` int(11) DEFAULT NULL,
  `issued_by` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_clearances`
--

INSERT INTO `barangay_clearances` (`clearance_id`, `resident_id`, `clearance_type_id`, `issued_by`, `issue_date`, `document_path`) VALUES
(30, 19, 1, 6, '2025-04-17', '../uploads/clearance_67eeb694dd6e54.18158457.png'),
(33, 19, 1, 1, '2025-04-17', '../uploads/clearance_67eeb694dd6e54.18158457.png');

-- --------------------------------------------------------

--
-- Table structure for table `blotter_records`
--

CREATE TABLE `blotter_records` (
  `blotter_id` int(11) NOT NULL,
  `complainant_id` int(11) NOT NULL,
  `respondent_id` int(11) NOT NULL,
  `incident_date` datetime NOT NULL,
  `incident_desc` text NOT NULL,
  `resolution` text DEFAULT NULL,
  `status` enum('Pending','solved') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotter_records`
--

INSERT INTO `blotter_records` (`blotter_id`, `complainant_id`, `respondent_id`, `incident_date`, `incident_desc`, `resolution`, `status`) VALUES
(3, 19, 19, '2025-04-19 19:08:00', 'dadsadsad', 'incedent', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `business_registrations`
--

CREATE TABLE `business_registrations` (
  `business_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` varchar(100) NOT NULL,
  `registration_date` date NOT NULL,
  `validity_period` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_registrations`
--

INSERT INTO `business_registrations` (`business_id`, `owner_id`, `business_name`, `business_type`, `registration_date`, `validity_period`) VALUES
(5, 19, 'xasdas', 'certificate', '2025-04-05', '2025-05-09');

-- --------------------------------------------------------

--
-- Table structure for table `business_types`
--

CREATE TABLE `business_types` (
  `business_type_id` int(11) NOT NULL,
  `business_type_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_types`
--

INSERT INTO `business_types` (`business_type_id`, `business_type_name`) VALUES
(3, 'ako ni'),
(12, 'wa lng');

-- --------------------------------------------------------

--
-- Table structure for table `clearance_types`
--

CREATE TABLE `clearance_types` (
  `clearance_type_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clearance_types`
--

INSERT INTO `clearance_types` (`clearance_type_id`, `type_name`, `description`) VALUES
(1, 'barangay clearance', 'asa'),
(3, 'barangay Indigency', 'financial');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL,
  `document_type_id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`document_id`, `document_type_id`, `resident_id`, `file_path`) VALUES
(7, 11, NULL, 'uploads/487063234_1919857575486998_4946129939620872811_n.jpg'),
(8, 11, 32, 'uploads/488967313_616740394689850_2238287631417291424_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `document_type_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`document_type_id`, `type_name`, `description`) VALUES
(11, 'clearance', 'for barangay'),
(13, 'dadad', 'ASDAD');

-- --------------------------------------------------------

--
-- Table structure for table `financial_reports`
--

CREATE TABLE `financial_reports` (
  `report_id` int(11) NOT NULL,
  `report_type` enum('Income','Expense','Budget') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `transaction_date` date NOT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_reports`
--

INSERT INTO `financial_reports` (`report_id`, `report_type`, `amount`, `description`, `transaction_date`, `document_path`, `balance`) VALUES
(28, 'Income', 200.00, 'barangay', '2025-04-18', '../uploads/1745328920_487545152_1369658477370121_7325455702483347816_n.jpg', 0.00),
(30, 'Income', 200.00, 'ewrwerwe', '2025-04-18', '../uploads/1745329596_logo.jpg', 0.00),
(31, 'Income', 200.00, 'ht', '2025-04-18', '../uploads/1745330133_487545152_1369658477370121_7325455702483347816_n.jpg', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `senior_pwd_services`
--

CREATE TABLE `senior_pwd_services` (
  `service_id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `service_type_id` int(11) DEFAULT NULL,
  `service_date` date NOT NULL,
  `document_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `senior_pwd_services`
--

INSERT INTO `senior_pwd_services` (`service_id`, `resident_id`, `service_type_id`, `service_date`, `document_path`) VALUES
(23, 19, 1, '2025-04-11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_types`
--

CREATE TABLE `service_types` (
  `service_type_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `service_type_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_types`
--

INSERT INTO `service_types` (`service_type_id`, `type_name`, `description`, `service_type_name`) VALUES
(1, 'clearance', 'self purposes', 'service'),
(3, 'allowance', 'financial', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `barangay` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `image`, `barangay`, `city`, `contact_no`, `created_at`, `updated_at`) VALUES
(5, 'logo.jpg', 'liloan ', 'Cebu city', '09094012677', '2025-04-10 15:09:27', '2025-04-10 15:09:27'),
(6, '491008595_1966467593878671_638077197717459863_n.jpg', 'liloan ', 'Cebu city', '09094012677', '2025-04-22 10:43:58', '2025-04-22 10:43:58');

-- --------------------------------------------------------

--
-- Table structure for table `sitio_purok`
--

CREATE TABLE `sitio_purok` (
  `id` int(11) NOT NULL,
  `sitio` varchar(255) NOT NULL,
  `purok` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sitio_purok`
--

INSERT INTO `sitio_purok` (`id`, `sitio`, `purok`) VALUES
(3, 'bantayan', 'tamis'),
(4, 'bato', 'lapok');

-- --------------------------------------------------------

--
-- Table structure for table `tblactivity`
--

CREATE TABLE `tblactivity` (
  `id` int(11) NOT NULL,
  `dateofactivity` date NOT NULL,
  `activity` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblactivity`
--

INSERT INTO `tblactivity` (`id`, `dateofactivity`, `activity`, `description`) VALUES
(10, '2017-01-03', 'activity', 'Description'),
(11, '2017-01-28', 'teets', 'sdfsdfsdfsdf'),
(12, '0000-00-00', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblactivityphoto`
--

CREATE TABLE `tblactivityphoto` (
  `id` int(11) NOT NULL,
  `activityid` int(11) NOT NULL,
  `filename` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblactivityphoto`
--

INSERT INTO `tblactivityphoto` (`id`, `activityid`, `filename`) VALUES
(18, 7, '1485255503893ChibiMaker.jpg'),
(19, 7, '1485255504014dental.jpg'),
(20, 7, '1485255504108images.jpg'),
(21, 8, '1485255608251dfxfxfxdfxfxfxdf.png'),
(22, 8, '1485255608315easy-nail-art-designs-for-beginners-youtube.jpg'),
(23, 8, '1485255608404Easy-Winter-Nail-Art-Tutorials-2013-2014-For-Beginners-Learners-10.jpg'),
(24, 8, '1485255608513motherboard.png'),
(25, 9, '148525575293111041019_1012143402147589_9043399646875097729_n.jpg'),
(26, 9, '1485255753089bg.PNG'),
(38, 11, '1485530620716user2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tblblotter`
--

CREATE TABLE `tblblotter` (
  `id` int(11) NOT NULL,
  `yearRecorded` varchar(4) NOT NULL,
  `dateRecorded` date NOT NULL,
  `complainant` text NOT NULL,
  `cage` int(11) NOT NULL,
  `caddress` text NOT NULL,
  `ccontact` int(11) NOT NULL,
  `personToComplain` text NOT NULL,
  `page` int(11) NOT NULL,
  `paddress` text NOT NULL,
  `pcontact` int(11) NOT NULL,
  `complaint` text NOT NULL,
  `actionTaken` varchar(50) NOT NULL,
  `sStatus` varchar(50) NOT NULL,
  `locationOfIncidence` text NOT NULL,
  `recordedby` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblblotter`
--

INSERT INTO `tblblotter` (`id`, `yearRecorded`, `dateRecorded`, `complainant`, `cage`, `caddress`, `ccontact`, `personToComplain`, `page`, `paddress`, `pcontact`, `complaint`, `actionTaken`, `sStatus`, `locationOfIncidence`, `recordedby`) VALUES
(3, '2016', '2016-10-15', 'sda, as das', 2132, 'asda', 213, '19', 3213, 'dasda', 2123, '213asd', '1st Option', 'Solved', 'asdsa', 'admin'),
(4, '2025', '2025-04-04', '', 0, '', 0, '', 0, '', 0, '', '1st Option', 'Solved', '', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tblclearance`
--

CREATE TABLE `tblclearance` (
  `id` int(11) NOT NULL,
  `clearanceNo` int(11) NOT NULL,
  `residentid` int(11) NOT NULL,
  `findings` text NOT NULL,
  `purpose` text NOT NULL,
  `orNo` int(11) NOT NULL,
  `samount` int(11) NOT NULL,
  `dateRecorded` date NOT NULL,
  `recordedBy` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclearance`
--

INSERT INTO `tblclearance` (`id`, `clearanceNo`, `residentid`, `findings`, `purpose`, `orNo`, `samount`, `dateRecorded`, `recordedBy`, `status`) VALUES
(8, 0, 11, '', 'asd', 0, 0, '2017-01-20', 'User1', 'New'),
(9, 1234, 15, 'asdada', 'local employment', 12, 3434, '2017-01-22', 'admin', 'Approved'),
(10, 123, 11, 'qwe', 'qwe', 213, 2123, '2017-01-24', 'admin', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `tblhousehold`
--

CREATE TABLE `tblhousehold` (
  `id` int(11) NOT NULL,
  `householdno` int(11) NOT NULL,
  `zone` varchar(11) NOT NULL,
  `totalhouseholdmembers` int(2) NOT NULL,
  `headoffamily` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblhousehold`
--

INSERT INTO `tblhousehold` (`id`, `householdno`, `zone`, `totalhouseholdmembers`, `headoffamily`) VALUES
(3, 2, '2', 0, '12');

-- --------------------------------------------------------

--
-- Table structure for table `tbllogs`
--

CREATE TABLE `tbllogs` (
  `id` int(11) NOT NULL,
  `user` varchar(50) NOT NULL,
  `logdate` datetime NOT NULL,
  `action` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbllogs`
--

INSERT INTO `tbllogs` (`id`, `user`, `logdate`, `action`) VALUES
(2, 'asd', '2017-01-04 00:00:00', 'Added Resident namedjayjay, asd asd'),
(3, 'asd', '2017-01-04 19:13:40', 'Update Resident named Sample1, User1 Brgy1'),
(4, 'sad', '2017-01-05 13:22:10', 'Update Official named eliezer a. vacalares, jr.'),
(7, 'sad', '2017-01-05 13:54:40', 'Update Household Number 1'),
(8, 'sad', '2017-01-05 14:00:08', 'Update Blotter Request sda, as das'),
(9, 'sad', '2017-01-05 14:15:39', 'Update Clearance with clearance number of 123131'),
(10, 'sad', '2017-01-05 14:25:03', 'Update Permit with business name of asda'),
(11, 'sad', '2017-01-05 14:25:25', 'Update Resident named Sample1, User1 Brgy1'),
(12, 'Administrator', '2017-01-24 16:32:40', 'Added Permit with business name of hahaha'),
(13, 'Administrator', '2017-01-24 16:35:41', 'Added Clearance with clearance number of 123'),
(14, 'Administrator', '2017-01-24 18:43:35', 'Added Activity sad'),
(15, 'Administrator', '2017-01-24 18:45:49', 'Added Activity qwe'),
(16, 'Administrator', '2017-01-24 18:46:20', 'Added Activity ss'),
(17, 'Administrator', '2017-01-24 18:47:39', 'Added Activity e'),
(18, 'Administrator', '2017-01-24 18:55:20', 'Added Activity activity'),
(19, 'Administrator', '2017-01-24 18:58:23', 'Added Activity Activity'),
(20, 'Administrator', '2017-01-24 19:00:07', 'Added Activity activity'),
(21, 'Administrator', '2017-01-24 19:02:32', 'Added Activity Activity'),
(22, 'Administrator', '2017-01-24 19:04:54', 'Added Activity activity'),
(23, 'Administrator', '2017-01-24 19:08:40', 'Update Activity activity'),
(24, 'Administrator', '2017-01-27 23:23:40', 'Added Activity teets'),
(25, 'Administrator', '2017-01-27 23:24:14', 'Update Resident named Sample1, User1 Brgy1'),
(26, 'Administrator', '2017-01-27 23:25:10', 'Update Resident named sda, as das'),
(27, 'Administrator', '2017-01-30 10:45:13', 'Added Resident named 2, 2 2'),
(28, 'Administrator', '2017-01-30 10:45:54', 'Added Resident named 2, 2 2'),
(29, 'Administrator', '2017-02-06 08:58:23', 'Update Resident named sda, as das'),
(30, 'Administrator', '2017-02-06 09:00:14', 'Update Resident named sda, as das'),
(31, 'Administrator', '2017-02-06 09:03:57', 'Added Household Number 2'),
(32, 'Administrator', '2017-02-06 09:04:25', 'Added Household Number 2'),
(33, 'Administrator', '2025-03-26 16:11:25', 'Update Staff with name of hello'),
(34, 'Administrator', '2025-03-26 16:11:39', 'Added Staff with name of '),
(35, 'Administrator', '2025-03-27 11:24:07', 'Update Staff with name of hello'),
(36, 'Administrator', '2025-03-27 11:37:25', 'Update Staff with name of hello'),
(37, 'Administrator', '2025-03-27 11:38:01', 'Added Staff with name of jan'),
(38, 'Admin', '2025-03-27 14:50:01', 'Update Blotter Request by sda, as das'),
(39, 'Admin', '2025-03-27 15:14:22', 'Added Permit with business name of barangay'),
(40, 'Admin', '2025-04-02 13:42:07', 'Added Permit with business name of ewqe'),
(41, 'Admin', '2025-04-02 13:42:07', 'Added Permit with business name of ewqe'),
(42, 'Official', '2025-04-02 14:12:44', 'Added Activity '),
(43, 'Admin', '2025-04-03 08:14:59', 'Update Blotter Request by sda, as das'),
(44, 'Admin', '2025-04-03 09:12:53', 'Added Barangay Clearance for Resident ID 19'),
(45, 'Admin', '2025-04-03 09:14:45', 'Added Barangay Clearance for Resident ID 19'),
(46, 'Admin', '2025-04-03 09:16:28', 'Added Barangay Clearance for Resident ID 19'),
(47, 'Admin', '2025-04-03 09:17:38', 'Added Barangay Clearance for Resident ID 19'),
(48, 'Admin', '2025-04-03 09:21:08', 'Added Barangay Clearance for Resident ID 19'),
(49, 'Admin', '2025-04-03 09:21:55', 'Added Barangay Clearance for Resident ID 19'),
(50, 'Admin', '2025-04-03 09:23:58', 'Added Barangay Clearance for Resident ID 19'),
(51, 'Admin', '2025-04-03 11:38:21', 'Added Blotter Request by '),
(52, 'Admin', '2025-04-05 09:44:26', 'Update Official named eliezer a. vacalares, jr.'),
(53, 'Admin', '2025-04-05 09:44:52', 'Update Official named eliezer a. vacalares, jr.'),
(54, 'Admin', '2025-04-05 09:48:20', 'Added Official named DAD'),
(55, 'Admin', '2025-04-05 10:11:46', 'Added Official named ybanez johnrey n'),
(56, 'Admin', '2025-04-05 10:12:26', 'Added Official named ybanez johnrey n'),
(57, 'Admin', '2025-04-21 08:30:07', 'Added Official named dtydht'),
(58, 'Admin', '2025-04-21 08:31:20', 'Added Official named DAD'),
(59, 'Admin', '2025-04-21 08:37:47', 'Added Official named DAD'),
(60, 'Admin', '2025-04-21 08:38:23', 'Updated Official named jupiter'),
(61, 'Admin', '2025-04-21 08:42:44', 'Added Official named czcz'),
(62, 'Admin', '2025-04-21 08:45:27', 'Added Official named DAD'),
(63, 'Admin', '2025-04-21 08:49:10', 'Added Official named DAD'),
(64, 'Admin', '2025-04-21 09:00:15', 'Added Official named DAD'),
(65, 'Admin', '2025-04-21 09:01:47', 'Added Official named DAD'),
(66, 'Admin', '2025-04-21 09:05:08', 'Updated Official named jupiter'),
(67, 'Admin', '2025-04-22 13:45:40', 'Updated Official named ramil d. pakino');

-- --------------------------------------------------------

--
-- Table structure for table `tblofficial`
--

CREATE TABLE `tblofficial` (
  `id` int(11) NOT NULL,
  `sPosition` varchar(50) NOT NULL,
  `completeName` text NOT NULL,
  `pcontact` varchar(20) NOT NULL,
  `paddress` text NOT NULL,
  `termStart` date NOT NULL,
  `termEnd` date NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblofficial`
--

INSERT INTO `tblofficial` (`id`, `sPosition`, `completeName`, `pcontact`, `paddress`, `termStart`, `termEnd`, `status`) VALUES
(1, 'Captain', 'ybanez johnrey n.', '09094012677', 'putian bantayan cebu', '2025-04-01', '2025-04-30', 'Ongoing Term'),
(5, 'Kagawad(Ordinance)', 'ramil d. pakino', '4234', 'sfdsa', '2016-10-31', '2016-11-17', 'End Term'),
(6, 'Kagawad(Public Safety)', 'chito t. epal', '234234', '45sdfdf', '2016-10-31', '2016-11-24', 'End Term'),
(7, 'Kagawad(Tourism)', 'debie v. pereyra', '67567', 'sdfgf543', '2016-11-13', '2016-12-01', 'End Term'),
(8, 'Kagawad(Budget & Finance)', 'milard t. muring', '35454', 'dfgfgxcg', '2016-11-06', '2016-11-30', 'End Term'),
(9, 'Kagawad(Agriculture)', 'jaime d. abella', '3453545', 'sfsfds', '2016-11-06', '2016-11-22', 'End Term'),
(12, 'Kagawad(Education)', 'jupiter', '09892347234', 'putian', '2025-05-11', '2025-05-31', 'Ongoing Term');

-- --------------------------------------------------------

--
-- Table structure for table `tblpermit`
--

CREATE TABLE `tblpermit` (
  `id` int(11) NOT NULL,
  `residentid` int(11) NOT NULL,
  `businessName` text NOT NULL,
  `businessAddress` text NOT NULL,
  `typeOfBusiness` varchar(50) NOT NULL,
  `orNo` int(11) NOT NULL,
  `samount` int(11) NOT NULL,
  `dateRecorded` date NOT NULL,
  `recordedBy` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpermit`
--

INSERT INTO `tblpermit` (`id`, `residentid`, `businessName`, `businessAddress`, `typeOfBusiness`, `orNo`, `samount`, `dateRecorded`, `recordedBy`, `status`) VALUES
(2, 11, 'test', 'test', 'Option 2', 213, 2132131, '2016-10-15', '', 'Disapproved'),
(5, 11, 'Business ', 'Address', 'Option 1', 0, 0, '2016-12-04', 'a', 'New'),
(7, 11, 'sad', 'asd', 'Option 2', 0, 0, '2017-01-20', 'a', 'New'),
(9, 19, 'barangay', 'putian', 'Option 2', 3, 5, '2025-03-27', 'admin', 'New'),
(10, 21, 'ewqe', 'sadasfs', 'Option 1', 3, 0, '2025-04-03', 'admin', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `tblresident`
--

CREATE TABLE `tblresident` (
  `id` int(11) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `mname` varchar(20) NOT NULL,
  `bdate` varchar(20) NOT NULL,
  `bplace` text NOT NULL,
  `age` int(11) NOT NULL,
  `civilstatus` varchar(20) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `religion` varchar(50) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `image` text NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `sitio` varchar(255) DEFAULT NULL,
  `purok` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `pwd` varchar(10) NOT NULL DEFAULT 'No',
  `senior_citizen` varchar(10) NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblresident`
--

INSERT INTO `tblresident` (`id`, `lname`, `fname`, `mname`, `bdate`, `bplace`, `age`, `civilstatus`, `occupation`, `religion`, `gender`, `image`, `contact_no`, `sitio`, `purok`, `address`, `pwd`, `senior_citizen`) VALUES
(19, 'desuyo', 'Jupiter', 'n', '2019-02-13', 'bantayan', 6, 'single', 'student', 'dasd', 'Male', '1745249032081_491008595_1966467593878671_638077197717459863_n.jpg', '76574573463643', 'bantayan', 'tamis', 'sdasdsa', 'Yes', 'No'),
(24, 'john', 'rey', 'v', '2025-04-04', 'putian', 0, 'single', 'sadsadsasadasd', 'sdasdas', 'Male', '1745247466378_Your paragraph text.png', '09892347234', 'bato', 'lapok', 'sdasdada', 'Yes', 'Yes'),
(32, 'villacarlos', 'shara', 'v', '2025-04-03', 'putian', 0, 'single', 'student', 'catholic', 'Female', '1745263277182_488967313_616740394689850_2238287631417291424_n.jpg', '09892347234', 'bato', 'lapok', 'asas', 'No', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `tblstaff`
--

CREATE TABLE `tblstaff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblstaff`
--

INSERT INTO `tblstaff` (`id`, `name`, `username`, `password`) VALUES
(1, 'hello', 'admin', 'admin'),
(3, 'jan', 'john', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Admin','Clerk','Official') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`id`, `fullname`, `username`, `password_hash`, `role`, `created_at`) VALUES
(3, 'Admin User', 'admin', '$2y$10$UaJ0F77dMPNKTc67oJO4BeFCWnDoq7mISqUhTRmw3AVI.4ww0bAWG', 'Admin', '2025-03-27 05:57:24'),
(4, 'Juan Dela Cruz', 'clerk', '$2y$10$HASHED_PASSWORD_HERE', 'Clerk', '2025-03-27 07:57:21'),
(5, 'Maria Santos', 'official', '$2y$10$HASHED_PASSWORD_HERE', 'Official', '2025-03-27 07:57:21'),
(6, 'Juan Dela Cruz', 'clerk_user', '$2y$10$A0CZrEjRTQqbBblzha0PueKz7BH36v9htO.EM6S./vMRPChFfzvlu', 'Clerk', '2025-03-27 08:01:41'),
(7, 'Maria Santos', 'official_user', '$2y$10$JmPbn7zuuv.WTlo0Jd6Zhey5eepgdGzYCpYh5n93kez6u3DOl8eha', 'Official', '2025-03-27 08:01:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assistance_requests`
--
ALTER TABLE `assistance_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `service_type_id` (`service_type_id`);

--
-- Indexes for table `barangay_clearances`
--
ALTER TABLE `barangay_clearances`
  ADD PRIMARY KEY (`clearance_id`),
  ADD KEY `clearance_type_id` (`clearance_type_id`),
  ADD KEY `issued_by` (`issued_by`),
  ADD KEY `barangay_clearances_ibfk_2` (`resident_id`);

--
-- Indexes for table `blotter_records`
--
ALTER TABLE `blotter_records`
  ADD PRIMARY KEY (`blotter_id`),
  ADD KEY `respondent_id` (`respondent_id`),
  ADD KEY `blotter_records_ibfk_1` (`complainant_id`);

--
-- Indexes for table `business_registrations`
--
ALTER TABLE `business_registrations`
  ADD PRIMARY KEY (`business_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `business_types`
--
ALTER TABLE `business_types`
  ADD PRIMARY KEY (`business_type_id`);

--
-- Indexes for table `clearance_types`
--
ALTER TABLE `clearance_types`
  ADD PRIMARY KEY (`clearance_type_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `fk_document_type_id` (`document_type_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`document_type_id`);

--
-- Indexes for table `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `senior_pwd_services`
--
ALTER TABLE `senior_pwd_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `service_type_id` (`service_type_id`);

--
-- Indexes for table `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`service_type_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sitio_purok`
--
ALTER TABLE `sitio_purok`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblactivity`
--
ALTER TABLE `tblactivity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblactivityphoto`
--
ALTER TABLE `tblactivityphoto`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblblotter`
--
ALTER TABLE `tblblotter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblclearance`
--
ALTER TABLE `tblclearance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblhousehold`
--
ALTER TABLE `tblhousehold`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbllogs`
--
ALTER TABLE `tbllogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblofficial`
--
ALTER TABLE `tblofficial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblpermit`
--
ALTER TABLE `tblpermit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblresident`
--
ALTER TABLE `tblresident`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblstaff`
--
ALTER TABLE `tblstaff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assistance_requests`
--
ALTER TABLE `assistance_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `barangay_clearances`
--
ALTER TABLE `barangay_clearances`
  MODIFY `clearance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `blotter_records`
--
ALTER TABLE `blotter_records`
  MODIFY `blotter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `business_registrations`
--
ALTER TABLE `business_registrations`
  MODIFY `business_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `business_types`
--
ALTER TABLE `business_types`
  MODIFY `business_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `clearance_types`
--
ALTER TABLE `clearance_types`
  MODIFY `clearance_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `document_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `financial_reports`
--
ALTER TABLE `financial_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `senior_pwd_services`
--
ALTER TABLE `senior_pwd_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `service_types`
--
ALTER TABLE `service_types`
  MODIFY `service_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sitio_purok`
--
ALTER TABLE `sitio_purok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblactivity`
--
ALTER TABLE `tblactivity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblactivityphoto`
--
ALTER TABLE `tblactivityphoto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tblblotter`
--
ALTER TABLE `tblblotter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblclearance`
--
ALTER TABLE `tblclearance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblhousehold`
--
ALTER TABLE `tblhousehold`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbllogs`
--
ALTER TABLE `tbllogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `tblofficial`
--
ALTER TABLE `tblofficial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tblpermit`
--
ALTER TABLE `tblpermit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tblresident`
--
ALTER TABLE `tblresident`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tblstaff`
--
ALTER TABLE `tblstaff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assistance_requests`
--
ALTER TABLE `assistance_requests`
  ADD CONSTRAINT `assistance_requests_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tblresident` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assistance_requests_ibfk_2` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`service_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `barangay_clearances`
--
ALTER TABLE `barangay_clearances`
  ADD CONSTRAINT `barangay_clearances_ibfk_1` FOREIGN KEY (`clearance_type_id`) REFERENCES `clearance_types` (`clearance_type_id`),
  ADD CONSTRAINT `barangay_clearances_ibfk_2` FOREIGN KEY (`resident_id`) REFERENCES `tblresident` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `barangay_clearances_ibfk_3` FOREIGN KEY (`issued_by`) REFERENCES `tblofficial` (`id`);

--
-- Constraints for table `blotter_records`
--
ALTER TABLE `blotter_records`
  ADD CONSTRAINT `blotter_records_ibfk_1` FOREIGN KEY (`complainant_id`) REFERENCES `tblresident` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blotter_records_ibfk_2` FOREIGN KEY (`respondent_id`) REFERENCES `tblresident` (`id`);

--
-- Constraints for table `business_registrations`
--
ALTER TABLE `business_registrations`
  ADD CONSTRAINT `business_registrations_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `tblresident` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`document_type_id`),
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`resident_id`) REFERENCES `tblresident` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_document_type_id` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`document_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `senior_pwd_services`
--
ALTER TABLE `senior_pwd_services`
  ADD CONSTRAINT `senior_pwd_services_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `tblresident` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `senior_pwd_services_ibfk_2` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`service_type_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
