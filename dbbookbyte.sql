-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2025 at 10:55 AM
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
-- Database: `dbbookbyte`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbllogininformation`
--

CREATE TABLE `tbllogininformation` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `accounttype` varchar(100) NOT NULL,
  `datecreated` date NOT NULL,
  `remarks` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbllogininformation`
--

INSERT INTO `tbllogininformation` (`id`, `username`, `email`, `contact`, `password`, `accounttype`, `datecreated`, `remarks`) VALUES
(1, 'admin', 'admin@gmail.com', '09776754698', '0192023a7bbd73250516f069df18b500', 'admin', '2025-08-22', 'active'),
(2, 'student', 'student@gmail.com', '09099157026', 'ad6a280417a0f533d8b670c61667e1a0', 'student', '2025-10-04', 'active'),
(3, 'librarian', 'librarian@gmail.com', '09707128431', '16e70200e2731e74d6c05bc0316cf293', 'librarian', '2025-08-22', 'active'),
(44, 'Juan', 'juan@gmail.com', '09085496018', 'e87c1ccee4ec801a54753c12343f37db', 'librarian', '2025-10-31', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_books`
--

CREATE TABLE `tbl_books` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `remarks` enum('available','unavailable') NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_borrowed`
--

CREATE TABLE `tbl_borrowed` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `date_borrowed` datetime NOT NULL,
  `date_returned` datetime DEFAULT NULL,
  `status` enum('borrowed','returned') DEFAULT 'borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_borrowed`
--

INSERT INTO `tbl_borrowed` (`id`, `student_id`, `book_id`, `date_borrowed`, `date_returned`, `status`) VALUES
(12, 2, 21, '2025-10-31 10:01:52', NULL, 'borrowed');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_personal_information`
--

CREATE TABLE `tbl_personal_information` (
  `personal_id` int(11) NOT NULL,
  `login_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `student_id_number` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year_level` enum('1','2','3','4') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_personal_information`
--

INSERT INTO `tbl_personal_information` (`personal_id`, `login_id`, `first_name`, `middle_name`, `last_name`, `gender`, `birthdate`, `address`, `phone_number`, `student_id_number`, `course`, `year_level`) VALUES
(8, 44, 'Juan Miguel', 'Calma', 'Montero', 'Male', '2003-03-05', 'Tenejero Balanga City Bataan', NULL, 'N/A', 'N/A', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reservations`
--

CREATE TABLE `tbl_reservations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `date_reserved` datetime NOT NULL,
  `status` enum('reserved','cancelled') DEFAULT 'reserved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbllogininformation`
--
ALTER TABLE `tbllogininformation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_books`
--
ALTER TABLE `tbl_books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_borrowed`
--
ALTER TABLE `tbl_borrowed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_personal_information`
--
ALTER TABLE `tbl_personal_information`
  ADD PRIMARY KEY (`personal_id`),
  ADD KEY `fk_login_personal` (`login_id`);

--
-- Indexes for table `tbl_reservations`
--
ALTER TABLE `tbl_reservations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbllogininformation`
--
ALTER TABLE `tbllogininformation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tbl_books`
--
ALTER TABLE `tbl_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_borrowed`
--
ALTER TABLE `tbl_borrowed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_personal_information`
--
ALTER TABLE `tbl_personal_information`
  MODIFY `personal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_reservations`
--
ALTER TABLE `tbl_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_personal_information`
--
ALTER TABLE `tbl_personal_information`
  ADD CONSTRAINT `fk_login_personal` FOREIGN KEY (`login_id`) REFERENCES `tbllogininformation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
