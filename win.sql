-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 05, 2025 at 07:51 AM
-- Server version: 10.6.7-MariaDB-log
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `win`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `name`, `student_id`, `purpose`, `date`, `time_in`, `time_out`) VALUES
(1, 'test', 'test', 'tse', '2025-04-28', '15:45:00', NULL),
(2, '', 'test', '', '2025-04-28', NULL, '15:45:00'),
(3, 'testug', 'sd123', 'asd', '2025-04-28', '15:47:00', NULL),
(4, 'dong', '1234', 'tambay', '2025-04-28', '00:32:00', NULL),
(5, '', 'sd123', '', '2025-04-28', NULL, '15:47:00');

-- --------------------------------------------------------

--
-- Table structure for table `kumbati_attendance`
--

CREATE TABLE `kumbati_attendance` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `kumbati_attendance_logs`
--

CREATE TABLE `kumbati_attendance_logs` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `timestamp`, `details`, `name`) VALUES
(1, 'asd', 'Student ID: asd timed out', '2025-04-28 07:18:58', 'Student ID: asd timed out on 2025-04-28 at 15:15', ''),
(2, '123', 'Student ID: 123 timed out', '2025-04-28 07:23:16', 'Student ID: 123 timed out on 2025-04-28 at 15:23', ''),
(3, '0000', 'Student ID: 0000 timed out', '2025-04-28 07:27:40', 'Student ID: 0000 timed out on 2025-04-28 at 00:27', ''),
(4, 'test', 'Student ID: test timed out', '2025-04-28 07:46:08', 'Student ID: test timed out on 2025-04-28 at 15:45', ''),
(5, 'sd123', 'Student ID: sd123 timed out', '2025-04-28 07:47:56', 'Student ID: sd123 timed out on 2025-04-28 at 15:47', ''),
(6, 'test', 'Student ID: test timed out', '2025-04-28 07:49:31', 'Student ID: test timed out on 2025-04-28 at 15:49', ''),
(7, '1232', 'Student ID: 1232 timed out', '2025-04-28 07:50:07', 'Student ID: 1232 timed out on 2025-04-28 at 15:49', '1232'),
(8, '1234', 'Student ID: 1234 timed out', '2025-04-28 07:50:08', 'Student ID: 1234 timed out on 2025-04-28 at 00:47', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kumbati_attendance`
--
ALTER TABLE `kumbati_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kumbati_attendance_logs`
--
ALTER TABLE `kumbati_attendance_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
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
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kumbati_attendance`
--
ALTER TABLE `kumbati_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `kumbati_attendance_logs`
--
ALTER TABLE `kumbati_attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
