-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2026 at 12:05 PM
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
-- Database: `studyconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_users`
--

CREATE TABLE `app_users` (
  `User_id` varchar(50) NOT NULL,
  `User_name` varchar(500) NOT NULL,
  `Group_name` varchar(500) NOT NULL,
  `User_pwd` varchar(500) NOT NULL,
  `Branch_name` varchar(50) NOT NULL,
  `Branch_Access` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_users`
--

INSERT INTO `app_users` (`User_id`, `User_name`, `Group_name`, `User_pwd`, `Branch_name`, `Branch_Access`) VALUES
('phinnynoel@gmail.com', 'Phinehas Noel J', 'Dev Group', 'changelater', 'KPHB Hyderabad (HO)', 'B'),
('phinny@gmail.com', 'Phinny', 'BranchUser', 'xxx', 'KPHB Hyderabad (HO)', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `Branch_name` varchar(50) NOT NULL,
  `Branch_location` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`Branch_name`, `Branch_location`) VALUES
('KPHB Hyderabad (HO)', 'Hyderabad'),
('Vizag', 'Visakhapatnam'),
('SR Nagar - Hyd', 'Hyderabad'),
('Nellore Branch', 'Nellore'),
('Eluru Branch', 'Eluru');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `Country_code` varchar(3) NOT NULL,
  `Country_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`Country_code`, `Country_name`) VALUES
('US', 'United States'),
('UK', 'United Kingdom'),
('CA', 'Canada'),
('AU', 'Australia'),
('EU', 'Europe'),
('NZ', 'Newzealand'),
('OT', 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `coursechoice`
--

CREATE TABLE `coursechoice` (
  `student_id` int(11) NOT NULL,
  `COUNTRY_CODE` varchar(5) DEFAULT NULL,
  `University_Name` varchar(500) DEFAULT NULL,
  `Course_Name` varchar(500) DEFAULT NULL,
  `Course_URL` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coursechoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu_table`
--

CREATE TABLE `menu_table` (
  `Menu_id` int(11) NOT NULL,
  `Menu_name` varchar(50) DEFAULT NULL,
  `Menu_desc` varchar(50) DEFAULT NULL,
  `Menu_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_table`
--


-- --------------------------------------------------------

--
-- Table structure for table `studentdetails`
--

CREATE TABLE `studentdetails` (
  `student_id` int(11) NOT NULL,
  `Country_code` varchar(5) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `preferred_country` varchar(50) DEFAULT NULL,
  `Other_country` varchar(50) DEFAULT NULL,
  `marks_10th` decimal(5,2) DEFAULT NULL,
  `cert_10th` varchar(1000) DEFAULT NULL,
  `marks_intermediate` decimal(5,2) DEFAULT NULL,
  `cert_intermediate` varchar(1000) DEFAULT NULL,
  `marks_degree` decimal(5,2) DEFAULT NULL,
  `cert_degree` varchar(1000) DEFAULT NULL,
  `marks_pg` decimal(5,2) DEFAULT NULL,
  `cert_pg` varchar(1000) DEFAULT NULL,
  `marks_diploma` decimal(5,2) DEFAULT NULL,
  `cert_diploma` varchar(1000) DEFAULT NULL,
  `marks_other` decimal(5,2) DEFAULT NULL,
  `cert_other` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Exp1From_date` date DEFAULT NULL,
  `Exp1To_date` date DEFAULT NULL,
  `Exp1_Cert` varchar(1000) DEFAULT NULL,
  `Exp2From_date` date DEFAULT NULL,
  `Exp2To_date` date DEFAULT NULL,
  `Exp2_Cert` varchar(1000) DEFAULT NULL,
  `Exp3From_date` date DEFAULT NULL,
  `Exp3To_date` date DEFAULT NULL,
  `Exp3_Cert` varchar(1000) DEFAULT NULL,
  `Branch_name` varchar(50) DEFAULT NULL,
  `Passport_no` varchar(50) NOT NULL,
  `Passport_issue` date DEFAULT NULL,
  `Passport_Expiry` date DEFAULT NULL,
  `Passport_Upload` varchar(1000) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentdetails`
--

--
-- Table structure for table `studentlanguagetests`
--

CREATE TABLE `studentlanguagetests` (
  `student_id` int(11) NOT NULL,
  `COUNTRY_CODE` varchar(5) NOT NULL,
  `IELTS_OA` int(10) NOT NULL,
  `IELTS_READ` int(10) NOT NULL,
  `IELTS_WRITE` int(10) NOT NULL,
  `IELTS_SPEAK` int(10) NOT NULL,
  `IELTS_LISTEN` int(10) NOT NULL,
  `IELTS_UPLOAD` varchar(1000) NOT NULL,
  `PTE_OA` int(10) NOT NULL,
  `PTE_READ` int(10) NOT NULL,
  `PTE_WRITE` int(10) NOT NULL,
  `PTE_SPEAK` int(10) NOT NULL,
  `PTE_LISTEN` int(10) NOT NULL,
  `PTE_UPLOAD` varchar(1000) NOT NULL,
  `TOEFL_OA` int(10) NOT NULL,
  `TOEFL_READ` int(10) NOT NULL,
  `TOEFL_WRITE` int(10) NOT NULL,
  `TOEFL_SPEAK` int(10) NOT NULL,
  `TOEFL_LISTEN` int(10) NOT NULL,
  `TOEFL_UPLOAD` varchar(1000) NOT NULL,
  `LANGCERT_OA` int(10) NOT NULL,
  `LANGCERT_READ` int(10) NOT NULL,
  `LANGCERT_WRITE` int(10) NOT NULL,
  `LANGCERT_SPEAK` int(10) NOT NULL,
  `LANGCERT_LISTEN` int(10) NOT NULL,
  `LANGCERT_UPLOAD` varchar(1000) NOT NULL,
  `DULINGO_OA` int(10) NOT NULL,
  `DULINGO_READ` int(10) NOT NULL,
  `DULINGO_WRITE` int(10) NOT NULL,
  `DULINGO_SPEAK` int(10) NOT NULL,
  `DULINGO_LISTEN` int(10) NOT NULL,
  `DULINGO_UPLOAD` varchar(1000) NOT NULL,
  `ENGOTHER_OA` int(10) NOT NULL,
  `ENGOTHER_READ` int(10) NOT NULL,
  `ENGOTHER_WRITE` int(10) NOT NULL,
  `ENGOTHER_SPEAK` int(10) NOT NULL,
  `ENGOTHER_LISTEN` int(10) NOT NULL,
  `ENGOTHER_UPLOAD` varchar(1000) NOT NULL,
  `ENGOTHER_NAME` varchar(50) NOT NULL,
  `GRE_OA` int(10) NOT NULL,
  `GRE_UPLOAD` varchar(1000) NOT NULL,
  `SAT_OA` int(11) NOT NULL,
  `SAT_UPLOAD` varchar(1000) NOT NULL,
  `GMAT_OA` int(11) NOT NULL,
  `GMAT_UPLOAD` varchar(1000) NOT NULL,
  `APTOTHER_NAME` varchar(50) NOT NULL,
  `APTOTHER_OA` int(10) NOT NULL,
  `APTOTHER_UPLOAD` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentlanguagetests`
--
-- --------------------------------------------------------

--
-- Table structure for table `student_messages`
--

CREATE TABLE `student_messages` (
  `Mail_from` varchar(100) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `Mail_message` text DEFAULT NULL,
  `Message_date` datetime DEFAULT current_timestamp(),
  `University_Name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `Group_id` int(11) NOT NULL,
  `Group_name` varchar(500) NOT NULL,
  `Menu_id` int(11) NOT NULL,
  `Access_add` char(1) NOT NULL,
  `Access_modify` char(1) NOT NULL,
  `Access_delete` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`Group_id`, `Group_name`, `Menu_id`, `Access_add`, `Access_modify`, `Access_delete`) VALUES
(1, 'Dev Group', 1, 'Y', 'Y', 'Y'),
(2, 'Dev Group', 2, 'Y', 'Y', 'Y'),
(4, 'BranchUser', 1, 'Y', 'Y', 'Y'),
(5, 'BranchUser', 2, 'Y', 'Y', 'Y'),
(6, 'BranchUser', 4, 'Y', 'Y', 'Y');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_table`
--
ALTER TABLE `menu_table`
  ADD PRIMARY KEY (`Menu_id`);

--
-- Indexes for table `studentdetails`
--
ALTER TABLE `studentdetails`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`Group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_table`
--
ALTER TABLE `menu_table`
  MODIFY `Menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `studentdetails`
--
ALTER TABLE `studentdetails`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `Group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE education_news (

    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(500),

    summary TEXT,

    image_url VARCHAR(1000),

    article_url VARCHAR(1000),

    source_name VARCHAR(255),

    category VARCHAR(100),

    published_at DATETIME,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_article (article_url(255))

);

CREATE TABLE magi_chat_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'active'
);

CREATE TABLE magi_chat_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    sender ENUM('user','ai','admin') NOT NULL,
    message TEXT,
    file_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE magi_uploaded_files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT,
    uploaded_by VARCHAR(20),
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);