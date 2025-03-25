-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 23, 2025 at 03:16 PM
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
  `teacher_id` int(11) DEFAULT NULL,
  `class_code` varchar(50) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('chờ duyệt','được duyệt','từ chối') DEFAULT 'chờ duyệt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `teacher_id`, `class_code`, `start_time`, `end_time`, `status`, `created_at`, `student_id`) VALUES
(1, 2, 6, 'CNTT01', '2025-03-22 08:00:00', '2025-03-30 10:00:00', 'được duyệt', '2025-03-22 09:53:03', NULL),
(2, 2, 3, 'CNTT01', '2025-04-01 10:30:00', '2025-04-01 12:00:00', 'chờ duyệt', '2025-03-22 09:53:03', NULL),
(3, 3, 4, 'KTPM01', '2025-04-02 08:00:00', '2025-04-02 10:00:00', 'được duyệt', '2025-03-22 09:53:03', NULL),
(4, 4, NULL, 'KTPM02', '2025-04-02 10:30:00', '2025-04-02 12:00:00', 'từ chối', '2025-03-22 09:53:03', NULL),
(5, 5, 5, 'CNTT01', '2025-04-03 08:00:00', '2025-04-03 10:00:00', 'được duyệt', '2025-03-22 09:53:03', NULL),
(6, 6, NULL, 'CNTT03', '2025-04-03 10:30:00', '2025-04-03 12:00:00', 'chờ duyệt', '2025-03-22 09:53:03', NULL),
(7, 7, 12, 'KTPM01', '2025-04-04 08:00:00', '2025-04-04 10:00:00', 'được duyệt', '2025-03-22 09:53:03', NULL),
(8, 8, NULL, 'CNTT02', '2025-04-04 10:30:00', '2025-04-04 12:00:00', 'chờ duyệt', '2025-03-22 09:53:03', NULL),
(9, 9, 3, 'KTPM02', '2025-04-05 08:00:00', '2025-04-05 10:00:00', 'được duyệt', '2025-03-22 09:53:03', NULL),
(10, 10, NULL, 'CNTT03', '2025-04-05 10:30:00', '2025-04-05 12:00:00', 'từ chối', '2025-03-22 09:53:03', NULL),
(11, 2, 4, 'CNTT02', '2025-04-06 08:00:00', '2025-04-06 10:00:00', 'được duyệt', '2025-03-22 09:53:03', NULL),
(12, 2, NULL, 'KTPM01', '2025-04-06 13:00:00', '2025-04-06 15:00:00', 'chờ duyệt', '2025-03-22 09:53:03', NULL),
(13, 3, 5, 'CNTT03', '2025-04-07 08:00:00', '2025-04-07 10:00:00', 'được duyệt', '2025-03-22 09:53:03', NULL),
(14, 4, NULL, 'KTPM02', '2025-04-07 13:00:00', '2025-04-07 15:00:00', 'từ chối', '2025-03-22 09:53:03', NULL),
(15, 5, 12, 'CNTT01', '2025-04-08 08:00:00', '2025-04-08 10:00:00', 'được duyệt', '2025-03-22 09:53:03', NULL),
(16, 9, 4, 'CNTT02', '2025-03-22 22:43:00', '2025-03-23 22:43:00', 'chờ duyệt', '2025-03-22 15:43:35', NULL);

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
(1, 'Phòng A101', 30, 'trống'),
(2, 'Phòng A102', 25, 'trống'),
(3, 'Phòng B201', 40, 'trống'),
(4, 'Phòng B202', 35, 'đã đặt'),
(5, 'Phòng C301', 50, 'bảo trì'),
(6, 'Phòng C302', 45, 'trống'),
(7, 'Phòng D401', 20, 'trống'),
(8, 'Phòng D402', 30, 'trống'),
(9, 'Phòng E501', 25, 'đã đặt'),
(10, 'Phòng E502', 35, 'trống'),
(11, 'Phòng E503', 35, 'trống');

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
(1, 3, 'CNTT01', 'Lập trình C++', '2025-03-23 08:00:00', '2025-03-23 10:00:00', 22, 1),
(2, 3, 'CNTT02', 'Cơ sở dữ liệu', '2025-03-22 08:00:00', '2025-03-22 10:00:00', 20, 2),
(3, 4, 'KTPM01', 'Phân tích thiết kế hệ thống', '2025-03-23 13:00:00', '2025-03-23 17:00:00', 31, 3),
(4, 4, 'KTPM02', 'Lập trình web', '2025-04-02 13:00:00', '2025-04-02 15:00:00', 24, NULL),
(5, 5, 'CNTT01', 'Mạng máy tính', '2025-04-03 08:00:00', '2025-04-03 10:00:00', 32, 10),
(6, 5, 'CNTT03', 'Trí tuệ nhân tạo', '2025-04-03 13:00:00', '2025-04-03 15:00:00', 43, 6),
(7, 12, 'KTPM01', 'Lập trình Java', '2025-04-04 08:00:00', '2025-04-04 10:00:00', 26, NULL),
(8, 12, 'CNTT02', 'Hệ điều hành', '2025-04-04 13:00:00', '2025-04-04 15:00:00', 29, NULL),
(9, 3, 'KTPM02', 'Kiểm thử phần mềm', '2025-04-05 08:00:00', '2025-04-05 10:00:00', 24, NULL),
(10, 4, 'CNTT03', 'An ninh mạng', '2025-04-05 13:00:00', '2025-04-05 15:00:00', 40, 6);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `email`, `password`, `role`, `class_code`, `created_at`) VALUES
(1, 'admin', 'Nguyễn Văn Hưng', 'admin@example.com', '$2y$10$YOUR_HASHED_PASSWORD', 'admin', '', '2025-03-22 09:50:37'),
(2, 'admin1', 'Trần Minh Quân', 'admin1@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'admin', NULL, '2025-03-22 09:50:37'),
(3, 'admin2', 'Lê Quốc Bảo', 'admin2@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'admin', NULL, '2025-03-22 09:50:37'),
(4, 'teacher1', 'Phạm Thị Hồng', 'teacher1@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'teacher', NULL, '2025-03-22 09:50:37'),
(5, 'teacher2', 'Hoàng Văn Dũng', 'teacher2@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'teacher', NULL, '2025-03-22 09:50:37'),
(6, 'teacher3', 'Vũ Thanh Tùng', 'teacher3@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'teacher', NULL, '2025-03-22 09:50:37'),
(7, 'student1', 'Nguyễn Thị Lan', 'student1@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'CNTT01', '2025-03-22 09:50:37'),
(8, 'student2', 'Bùi Ngọc Huy', 'student2@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'CNTT01', '2025-03-22 09:50:37'),
(9, 'student3', 'Ngô Minh Khôi', 'student3@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'CNTT02', '2025-03-22 09:50:37'),
(10, 'student4', 'Dương Thị Hạnh', 'student4@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'CNTT02', '2025-03-22 09:50:37'),
(11, 'student5', 'Tô Văn Sơn', 'student5@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'KTPM01', '2025-03-22 09:50:37'),
(12, 'student6', 'Lương Thị Ngọc', 'student6@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'KTPM01', '2025-03-22 09:50:37'),
(13, 'teacher4', 'Đỗ Ngọc Anh', 'teacher4@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'teacher', NULL, '2025-03-22 09:50:37'),
(14, 'student7', 'Đặng Quang Huy', 'student7@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'KTPM02', '2025-03-22 09:50:37'),
(15, 'student8', 'Cao Thị Mai', 'student8@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'KTPM02', '2025-03-22 09:50:37'),
(16, 'student9', 'Tống Văn Thành', 'student9@example.com', '$2y$10$hNKiWhGzl.6ki2d9Hp2PheY5wm6idfKfrVHqtdTAafajyQ.BM2t8m', 'student', 'CNTT03', '2025-03-22 09:50:37'),
(17, 'student10', 'Hồ Thị Phượng', 'student10@gmail.com', '$2y$10$ohzB6RMN9srmWhYPatme.e7z5rl3UmpkY1bTjjp/mxslgU4PSyM/m', 'student', 'CNTT02', '2025-03-22 09:50:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

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
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `timetables`
--
ALTER TABLE `timetables`
  ADD CONSTRAINT `timetables_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
