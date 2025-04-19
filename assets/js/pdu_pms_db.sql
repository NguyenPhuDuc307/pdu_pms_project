-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 19, 2025 at 08:53 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pdu_pms`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `class_code` varchar(50) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `purpose` text NOT NULL,
  `status` enum('chờ duyệt','được duyệt','từ chối') NOT NULL DEFAULT 'chờ duyệt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `class_code`, `start_time`, `end_time`, `purpose`, `status`, `created_at`, `user_id`) VALUES
(10, 11, 'CS102', '2025-04-17 17:33:00', '2025-04-17 19:33:00', 'Kiểm tra dự án cuối kỳ', 'chờ duyệt', '2025-04-17 07:34:05', 3),
(11, 12, 'CS101', '2025-04-17 15:00:00', '2025-04-17 17:00:00', 'Kiểm tra dự án cuối kỳ', 'được duyệt', '2025-04-17 08:11:23', 3),
(12, 11, 'CS101', '2025-04-17 15:14:00', '2025-04-17 17:14:00', 'Kiểm tra dự án cuối kỳ', 'từ chối', '2025-04-17 08:14:26', 3),
(13, 11, 'CS101', '2025-04-16 10:00:00', '2025-04-16 12:00:00', 'Giảng dạy theo lịch', 'được duyệt', '2025-04-17 12:36:40', 3),
(14, 12, 'CS101', '2025-04-18 10:30:00', '2025-04-18 12:00:00', 'Kiểm tra dự án cuối kỳ', 'chờ duyệt', '2025-04-18 03:38:54', 3),
(15, 11, 'CS102', '2025-04-18 13:30:00', '2025-04-18 16:30:00', 'Kiểm tra dự án cuối kỳ', 'được duyệt', '2025-04-18 03:39:42', 6),
(16, 11, 'CS101', '2025-04-19 13:00:00', '2025-04-19 16:00:00', 'Kiểm tra dự án cuối kỳ', 'chờ duyệt', '2025-04-19 05:46:04', 6);

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE `equipments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `maintenance_period` int(11) DEFAULT NULL COMMENT 'Số ngày giữa các lần bảo trì',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`id`, `name`, `description`, `maintenance_period`, `created_at`) VALUES
(11, 'Máy chiếu', 'Máy chiếu Samsung', 90, '2025-04-16 07:11:21'),
(12, 'Bộ máy tính DELL', 'Bộ máy tính DELL 16/512GB', 120, '2025-04-16 07:49:15');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `issue_description` text NOT NULL,
  `priority` enum('thấp','trung bình','cao','khẩn cấp') DEFAULT 'trung bình',
  `status` enum('đang chờ','đang xử lý','đã xử lý','từ chối') DEFAULT 'đang chờ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`id`, `room_id`, `equipment_id`, `user_id`, `issue_description`, `priority`, `status`, `created_at`, `resolved_at`, `admin_notes`) VALUES
(1, 11, 12, 6, 'Máy tính hỏng màn', 'trung bình', 'đang chờ', '2025-04-18 10:22:46', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('trống','đã đặt','bảo trì') DEFAULT 'trống'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `capacity`, `status`) VALUES
(11, 'E201', 30, 'trống'),
(12, 'E202', 45, 'trống');

-- --------------------------------------------------------

--
-- Table structure for table `room_equipments`
--

CREATE TABLE `room_equipments` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `last_maintenance` date DEFAULT NULL,
  `next_maintenance` date DEFAULT NULL,
  `status` enum('hoạt động','bảo trì','hỏng') DEFAULT 'hoạt động',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_equipments`
--

INSERT INTO `room_equipments` (`id`, `room_id`, `equipment_id`, `quantity`, `last_maintenance`, `next_maintenance`, `status`, `notes`) VALUES
(1, 11, 11, 2, '2025-04-16', '2025-07-15', 'hoạt động', ''),
(2, 11, 12, 30, '2025-04-16', '2025-08-14', 'hoạt động', 'Đủ số lượng máy tính');

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

CREATE TABLE `timetables` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `class_code` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `participants` int(11) NOT NULL DEFAULT 0,
  `room_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetables`
--

INSERT INTO `timetables` (`id`, `teacher_id`, `class_code`, `subject`, `start_time`, `end_time`, `participants`, `room_id`) VALUES
(11, 3, 'CS101', 'Lập trình cơ bản', '2025-04-16 10:00:00', '2025-04-16 12:00:00', 30, 11),
(12, 3, 'CS102', 'Cơ sở dữ liệu', '2025-04-17 08:00:00', '2025-04-17 10:00:00', 45, NULL),
(13, 3, 'CS103', 'Thiết kế đồ họa', '2025-04-17 10:00:00', '2025-04-17 12:00:00', 50, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `class_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `email`, `password`, `role`, `class_code`, `created_at`, `phone`) VALUES
(1, 'admin', 'Ngô Kim Hồng', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '', '2025-04-04 19:17:20', '0987654321'),
(3, 'teacher1', 'Phạm Thị Hồng', 'teacher1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, '2025-04-04 19:17:20', ''),
(6, 'student1', 'Nguyễn Thị Lan', 'student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'CNTT01', '2025-04-04 19:17:20', '0987654321'),
(7, 'student2', 'Bùi Ngọc Huy', 'student2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'CNTT01', '2025-04-04 19:17:20', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `bookings_ibfk_4` (`user_id`);

--
-- Indexes for table `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_equipments`
--
ALTER TABLE `room_equipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `equipments`
--
ALTER TABLE `equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `room_equipments`
--
ALTER TABLE `room_equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `maintenance_requests_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_equipments`
--
ALTER TABLE `room_equipments`
  ADD CONSTRAINT `room_equipments_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_equipments_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `timetables`
--
ALTER TABLE `timetables`
  ADD CONSTRAINT `timetables_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `timetables_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
