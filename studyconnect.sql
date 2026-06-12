-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2025 at 07:52 AM
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

INSERT INTO `coursechoice` (`student_id`, `COUNTRY_CODE`, `University_Name`, `Course_Name`, `Course_URL`) VALUES
(113, 'UK', 'Orford', 'AI Agentic', 'aaaaasdasd'),
(113, 'UK', 'Harvard University', 'Gen AI', 'fffffff');

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

INSERT INTO `menu_table` (`Menu_id`, `Menu_name`, `Menu_desc`, `Menu_url`) VALUES
(1, 'Students List', 'List of all Applicants', 'student_list.php'),
(2, 'Add New Student', 'Add new applicant', 'student_form.php'),
(3, 'Manage Applications', 'Student Applications Management', ''),
(4, 'News & Updates', 'News & Updates', NULL);

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

INSERT INTO `studentdetails` (`student_id`, `Country_code`, `name`, `address`, `email`, `phone`, `preferred_country`, `Other_country`, `marks_10th`, `cert_10th`, `marks_intermediate`, `cert_intermediate`, `marks_degree`, `cert_degree`, `marks_pg`, `cert_pg`, `marks_diploma`, `cert_diploma`, `marks_other`, `cert_other`, `created_at`, `Exp1From_date`, `Exp1To_date`, `Exp1_Cert`, `Exp2From_date`, `Exp2To_date`, `Exp2_Cert`, `Exp3From_date`, `Exp3To_date`, `Exp3_Cert`, `Branch_name`, `Passport_no`, `Passport_issue`, `Passport_Expiry`, `Passport_Upload`, `DateOfBirth`) VALUES
(113, 'UK', 'Samuel Edwin', 'Check Address', 'PhinnyNoel@gmail.com', '9885194115', 'United Kingdom', '', 90.00, 'uploads/68ef351fd306d_68dce988f05a4_Photo.jpg', 89.00, 'uploads/68ef351fd38d3_6879d9b8bed8d_gta-v-cover-wallpaper-screenshot.jpg', 90.00, 'uploads/68ef351fd4417_6879e5c66f411_SAVE_20230416_184938.jpg', 78.00, 'uploads/68ef351fd4cdb_6879d9b8ba699_Campus - II (1).jpeg', 96.00, 'uploads/68ef351fd51ee_6879d9b8c27a4_GuitarClassesDesign.jpg', 66.00, 'uploads/68ef351fd5a2b_6879d7049076f_Missionpic.png', '2025-10-15 05:46:07', '2025-10-01', '2025-10-08', 'uploads/68ef351fd60f4_68b31ef636224_Student List (4).pdf', '0000-00-00', '0000-00-00', '', '0000-00-00', '0000-00-00', '', 'KPHB Hyderabad (HO)', 'A345678', '2025-10-15', '2025-10-08', 'uploads/68ef351fd263c_68b1a3c8d8af1_Student List (5).pdf', '2025-10-15');

-- --------------------------------------------------------

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

INSERT INTO `studentlanguagetests` (`student_id`, `COUNTRY_CODE`, `IELTS_OA`, `IELTS_READ`, `IELTS_WRITE`, `IELTS_SPEAK`, `IELTS_LISTEN`, `IELTS_UPLOAD`, `PTE_OA`, `PTE_READ`, `PTE_WRITE`, `PTE_SPEAK`, `PTE_LISTEN`, `PTE_UPLOAD`, `TOEFL_OA`, `TOEFL_READ`, `TOEFL_WRITE`, `TOEFL_SPEAK`, `TOEFL_LISTEN`, `TOEFL_UPLOAD`, `LANGCERT_OA`, `LANGCERT_READ`, `LANGCERT_WRITE`, `LANGCERT_SPEAK`, `LANGCERT_LISTEN`, `LANGCERT_UPLOAD`, `DULINGO_OA`, `DULINGO_READ`, `DULINGO_WRITE`, `DULINGO_SPEAK`, `DULINGO_LISTEN`, `DULINGO_UPLOAD`, `ENGOTHER_OA`, `ENGOTHER_READ`, `ENGOTHER_WRITE`, `ENGOTHER_SPEAK`, `ENGOTHER_LISTEN`, `ENGOTHER_UPLOAD`, `ENGOTHER_NAME`, `GRE_OA`, `GRE_UPLOAD`, `SAT_OA`, `SAT_UPLOAD`, `GMAT_OA`, `GMAT_UPLOAD`, `APTOTHER_NAME`, `APTOTHER_OA`, `APTOTHER_UPLOAD`) VALUES
(113, 'UK', 77, 66, 76, 46, 77, 'uploads/68ef357cd3ce5_68b1a581e2995_Student List (5).pdf', 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, '', '', 55, 'uploads/68ef351fd8df6_68b1abc984706_Student List.pdf', 0, '', 0, '', '', 0, '');

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

INSERT INTO `student_messages` (`Mail_from`, `student_id`, `Mail_message`, `Message_date`, `University_Name`) VALUES
('phinnynoel@gmail.com', 113, 'Have to check on th e inbox part', '2025-10-15 11:17:10', 'Orford');

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
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `Group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


CREATE TABLE studentotherdetails
(
student_id int(11) NOT NULL,
 Country_code varchar(5) NOT NULL,
 immi_country VARCHAR(50),
 medical_cond VARCHAR(50),
 visa_refusal VARCHAR(50),
 convicted_offence VARCHAR(50),

 emergency_name VARCHAR(50),
 emergency_phone VARCHAR(50),
 emergency_email VARCHAR(50),
 emergency_relation VARCHAR(50),

lor1 VARCHAR(1000),
lor2 VARCHAR(1000),
lor3 VARCHAR(1000),

moi VARCHAR(1000),
resume VARCHAR(1000),
otherdoc VARCHAR(1000),
gender VARCHAR(50),
maritalstatus VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 
 
ALTER TABLE studentlanguagetests

MODIFY IELTS_OA INT(10) NULL,
MODIFY IELTS_READ INT(10) NULL,
MODIFY IELTS_WRITE INT(10) NULL,
MODIFY IELTS_SPEAK INT(10) NULL,
MODIFY IELTS_LISTEN INT(10) NULL,
MODIFY IELTS_UPLOAD VARCHAR(1000) NULL,

MODIFY PTE_OA INT(10) NULL,
MODIFY PTE_READ INT(10) NULL,
MODIFY PTE_WRITE INT(10) NULL,
MODIFY PTE_SPEAK INT(10) NULL,
MODIFY PTE_LISTEN INT(10) NULL,
MODIFY PTE_UPLOAD VARCHAR(1000) NULL,

MODIFY TOEFL_OA INT(10) NULL,
MODIFY TOEFL_READ INT(10) NULL,
MODIFY TOEFL_WRITE INT(10) NULL,
MODIFY TOEFL_SPEAK INT(10) NULL,
MODIFY TOEFL_LISTEN INT(10) NULL,
MODIFY TOEFL_UPLOAD VARCHAR(1000) NULL,

MODIFY LANGCERT_OA INT(10) NULL,
MODIFY LANGCERT_READ INT(10) NULL,
MODIFY LANGCERT_WRITE INT(10) NULL,
MODIFY LANGCERT_SPEAK INT(10) NULL,
MODIFY LANGCERT_LISTEN INT(10) NULL,
MODIFY LANGCERT_UPLOAD VARCHAR(1000) NULL,

MODIFY DULINGO_OA INT(10) NULL,
MODIFY DULINGO_READ INT(10) NULL,
MODIFY DULINGO_WRITE INT(10) NULL,
MODIFY DULINGO_SPEAK INT(10) NULL,
MODIFY DULINGO_LISTEN INT(10) NULL,
MODIFY DULINGO_UPLOAD VARCHAR(1000) NULL,

MODIFY ENGOTHER_OA INT(10) NULL,
MODIFY ENGOTHER_READ INT(10) NULL,
MODIFY ENGOTHER_WRITE INT(10) NULL,
MODIFY ENGOTHER_SPEAK INT(10) NULL,
MODIFY ENGOTHER_LISTEN INT(10) NULL,
MODIFY ENGOTHER_UPLOAD VARCHAR(1000) NULL,
MODIFY ENGOTHER_NAME VARCHAR(50) NULL,

MODIFY GRE_OA INT(10) NULL,
MODIFY GRE_UPLOAD VARCHAR(1000) NULL,

MODIFY SAT_OA INT(11) NULL,
MODIFY SAT_UPLOAD VARCHAR(1000) NULL,

MODIFY GMAT_OA INT(11) NULL,
MODIFY GMAT_UPLOAD VARCHAR(1000) NULL,

MODIFY APTOTHER_NAME VARCHAR(50) NULL,
MODIFY APTOTHER_OA INT(10) NULL,
MODIFY APTOTHER_UPLOAD VARCHAR(1000) NULL;




ALTER TABLE student_messages
ADD COLUMN your_docs VARCHAR(500) NULL,
ADD COLUMN ho_docs VARCHAR(500) NULL,
ADD COLUMN payment_link VARCHAR(1000) NULL;


ALTER TABLE coursechoice
ADD COLUMN Payment_Status TINYINT(1) DEFAULT 0;

ALTER TABLE studentotherdetails
ADD COLUMN payment_link_file VARCHAR(1000) NULL,
ADD COLUMN medical_cond_file VARCHAR(1000) NULL,
ADD COLUMN visa_refusal_file VARCHAR(1000) NULL,
ADD COLUMN immi_country_file VARCHAR(1000) NULL,
ADD COLUMN convicted_offence_file VARCHAR(1000) NULL;


ALTER TABLE studentotherdetails
ADD COLUMN lor1name varchar(100) DEFAULT NULL,
ADD COLUMN lor1email varchar(100) DEFAULT NULL,
ADD COLUMN lor1phone varchar(20) DEFAULT NULL,
ADD COLUMN lor2name varchar(100) DEFAULT NULL,
ADD COLUMN lor2email varchar(100) DEFAULT NULL,
ADD COLUMN lor2phone varchar(20) DEFAULT NULL,
ADD COLUMN lor3name varchar(100) DEFAULT NULL,
ADD COLUMN lor3email varchar(100) DEFAULT NULL,
ADD COLUMN lor3phone varchar(20) DEFAULT NULL,
ADD COLUMN explor1name varchar(100) DEFAULT NULL,
ADD COLUMN explor1email varchar(100) DEFAULT NULL,
ADD COLUMN explor1phone varchar(20) DEFAULT NULL,
ADD COLUMN explor2name varchar(100) DEFAULT NULL,
ADD COLUMN explor2email varchar(100) DEFAULT NULL,
ADD COLUMN explor2phone varchar(20) DEFAULT NULL,
ADD COLUMN explor3name varchar(100) DEFAULT NULL,
ADD COLUMN explor3email varchar(100) DEFAULT NULL,
ADD COLUMN explor3phone varchar(20) DEFAULT NULL,
ADD COLUMN explor1 VARCHAR(1000) DEFAULT NULL,
ADD COLUMN explor2 VARCHAR(1000) DEFAULT NULL,
ADD COLUMN explor3 VARCHAR(1000) DEFAULT NULL;