-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 06, 2025 lúc 02:59 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `pdu_pms_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `class_code` varchar(50) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('chờ duyệt','được duyệt','từ chối') DEFAULT 'chờ duyệt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `teacher_id`, `student_id`, `class_code`, `start_time`, `end_time`, `status`, `created_at`) VALUES
(1, 2, 3, NULL, 'CNTT01', '2025-04-05 19:17:20', '2025-04-05 21:17:20', 'được duyệt', '2025-04-04 19:17:20'),
(2, 2, 3, NULL, 'CNTT01', '2025-04-06 19:17:20', '2025-04-06 21:17:20', 'chờ duyệt', '2025-04-04 19:17:20'),
(3, 3, 4, NULL, 'KTPM01', '2025-04-07 19:17:20', '2025-04-07 21:17:20', 'được duyệt', '2025-04-04 19:17:20'),
(4, 4, NULL, 7, 'KTPM02', '2025-04-08 19:17:20', '2025-04-08 21:17:20', 'từ chối', '2025-04-04 19:17:20'),
(5, 5, 5, NULL, 'CNTT01', '2025-04-09 19:17:20', '2025-04-09 21:17:20', 'được duyệt', '2025-04-04 19:17:20'),
(6, 6, NULL, 8, 'CNTT03', '2025-04-10 19:17:20', '2025-04-10 21:17:20', 'chờ duyệt', '2025-04-04 19:17:20'),
(7, 7, 4, NULL, 'KTPM01', '2025-04-11 19:17:20', '2025-04-11 21:17:20', 'được duyệt', '2025-04-04 19:17:20'),
(8, 8, NULL, 9, 'CNTT02', '2025-04-12 19:17:20', '2025-04-12 21:17:20', 'chờ duyệt', '2025-04-04 19:17:20'),
(9, 9, 3, NULL, 'KTPM02', '2025-04-13 19:17:20', '2025-04-13 21:17:20', 'được duyệt', '2025-04-04 19:17:20'),
(10, 10, NULL, 6, 'CNTT03', '2025-04-14 19:17:20', '2025-04-14 21:17:20', 'từ chối', '2025-04-04 19:17:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `equipments`
--

CREATE TABLE `equipments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `maintenance_period` int(11) DEFAULT NULL COMMENT 'Số ngày giữa các lần bảo trì',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `equipments`
--

INSERT INTO `equipments` (`id`, `name`, `description`, `maintenance_period`, `created_at`) VALUES
(1, 'Máy tính', 'Máy tính để bàn cho sinh viên', 90, '2025-04-04 19:17:20'),
(2, 'Máy chiếu', 'Máy chiếu và màn hình', 60, '2025-04-04 19:17:20'),
(3, 'Máy in', 'Máy in laser đen trắng', 45, '2025-04-04 19:17:20'),
(4, 'Micro', 'Microphone không dây', 30, '2025-04-04 19:17:20'),
(5, 'Bảng tương tác', 'Bảng tương tác thông minh', 120, '2025-04-04 19:17:20'),
(6, 'Máy quét', 'Máy quét tài liệu', 60, '2025-04-04 19:17:20'),
(7, 'Điều hòa', 'Điều hòa nhiệt độ', 180, '2025-04-04 19:17:20'),
(8, 'Quạt trần', 'Quạt trần 4 cánh', 365, '2025-04-04 19:17:20'),
(9, 'Tủ tài liệu', 'Tủ đựng tài liệu', 0, '2025-04-04 19:17:20'),
(10, 'Loa âm thanh', 'Hệ thống loa âm thanh', 90, '2025-04-04 19:17:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `maintenance_requests`
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
-- Đang đổ dữ liệu cho bảng `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`id`, `room_id`, `equipment_id`, `user_id`, `issue_description`, `priority`, `status`, `created_at`, `resolved_at`, `admin_notes`) VALUES
(1, 1, 1, 6, 'Máy tính số 5 không khởi động được', 'trung bình', 'đã xử lý', '2025-03-20 19:17:20', '2025-03-21 19:17:20', 'Đã thay thế ổ cứng'),
(2, 2, 2, 3, 'Máy chiếu hiển thị hình ảnh mờ', 'cao', 'đã xử lý', '2025-03-25 19:17:20', '2025-03-26 19:17:20', 'Vệ sinh ống kính'),
(3, 3, NULL, 4, 'Điều hòa không hoạt động', 'cao', 'đang xử lý', '2025-04-01 19:17:20', NULL, 'Đang chờ thợ sửa chữa'),
(4, 4, 1, 7, 'Máy tính số 12 chạy chậm', 'thấp', 'đang chờ', '2025-04-02 19:17:20', NULL, NULL),
(5, 5, 3, 5, 'Máy in kẹt giấy', 'trung bình', 'đã xử lý', '2025-03-28 19:17:20', '2025-03-29 19:17:20', 'Đã sửa chữa'),
(6, 6, NULL, 3, 'Đèn phòng bị hỏng', 'thấp', 'đã xử lý', '2025-03-15 19:17:20', '2025-03-16 19:17:20', 'Đã thay bóng đèn'),
(7, 7, 5, 4, 'Bảng tương tác không phản hồi cảm ứng', 'cao', 'đang xử lý', '2025-03-31 19:17:20', NULL, 'Chờ linh kiện thay thế'),
(8, 8, 1, 9, 'Bàn phím máy tính bị hỏng', 'thấp', 'đã xử lý', '2025-03-27 19:17:20', '2025-03-28 19:17:20', 'Đã thay bàn phím mới'),
(9, 9, 2, 3, 'Màn chiếu không hạ xuống được', 'trung bình', 'đang chờ', '2025-04-03 19:17:20', NULL, NULL),
(10, 10, 4, 4, 'Micro không có tiếng', 'khẩn cấp', 'đã xử lý', '2025-03-30 19:17:20', '2025-03-31 19:17:20', 'Thay pin và vệ sinh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('trống','đã đặt','bảo trì') DEFAULT 'trống',
  `room_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `capacity`, `status`, `room_type_id`) VALUES
(1, 'Phòng A101', 30, 'trống', 1),
(2, 'Phòng A102', 25, 'trống', 1),
(3, 'Phòng B201', 40, 'trống', 1),
(4, 'Phòng B202', 35, 'đã đặt', 2),
(5, 'Phòng C301', 50, 'bảo trì', 2),
(6, 'Phòng C302', 45, 'trống', 2),
(7, 'Phòng D401', 20, 'trống', 4),
(8, 'Phòng D402', 30, 'trống', 4),
(9, 'Phòng E501', 25, 'đã đặt', 4),
(10, 'Phòng E502', 35, 'bảo trì', 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_equipments`
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
-- Đang đổ dữ liệu cho bảng `room_equipments`
--

INSERT INTO `room_equipments` (`id`, `room_id`, `equipment_id`, `quantity`, `last_maintenance`, `next_maintenance`, `status`, `notes`) VALUES
(1, 1, 1, 30, '2025-03-05', '2025-06-03', 'hoạt động', 'Máy tính cho sinh viên'),
(2, 1, 2, 1, '2025-03-20', '2025-05-19', 'hoạt động', 'Máy chiếu mới'),
(3, 2, 1, 25, '2025-02-18', '2025-05-19', 'hoạt động', 'Cần cập nhật phần mềm'),
(4, 3, 2, 1, '2025-03-05', '2025-05-04', 'hoạt động', NULL),
(5, 4, 1, 35, '2025-03-25', '2025-06-23', 'hoạt động', NULL),
(6, 5, 3, 1, '2025-03-15', '2025-04-29', 'bảo trì', 'Đang sửa chữa'),
(7, 6, 4, 2, '2025-03-20', '2025-04-19', 'hoạt động', NULL),
(8, 7, 5, 1, '2025-02-03', '2025-06-03', 'hoạt động', 'Mới lắp đặt'),
(9, 8, 1, 30, '2025-02-23', '2025-05-24', 'hoạt động', NULL),
(10, 9, 2, 1, '2025-02-13', '2025-04-14', 'hoạt động', 'Cần thay bóng đèn');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Phòng lý thuyết', 'Phòng học thông thường', '2025-04-04 19:17:20'),
(2, 'Phòng máy tính', 'Phòng thực hành máy tính', '2025-04-04 19:17:20'),
(3, 'Phòng đồ họa', 'Phòng thực hành thiết kế đồ họa', '2025-04-04 19:17:20'),
(4, 'Phòng thí nghiệm', 'Phòng thực hành thí nghiệm', '2025-04-04 19:17:20'),
(5, 'Phòng hội thảo', 'Phòng tổ chức hội thảo, seminar', '2025-04-04 19:17:20'),
(6, 'Phòng máy tính', 'Gồm 45 máy tính', '2025-04-04 19:17:20'),
(7, 'Phòng gym', 'Phòng tập thể thao', '2025-04-04 19:17:20'),
(8, 'Phòng dự án', 'Phòng làm việc nhóm dự án', '2025-04-04 19:17:20'),
(9, 'Phòng trình bày', 'Phòng trình bày kết quả', '2025-04-04 19:17:20'),
(10, 'Phòng đa năng', 'Phòng phục vụ nhiều mục đích', '2025-04-04 19:17:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `timetables`
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
-- Đang đổ dữ liệu cho bảng `timetables`
--

INSERT INTO `timetables` (`id`, `teacher_id`, `class_code`, `subject`, `start_time`, `end_time`, `participants`, `room_id`) VALUES
(1, 3, 'CNTT01', 'Lập trình C++', '2025-04-05 19:17:20', '2025-04-05 21:17:20', 22, 1),
(2, 3, 'CNTT02', 'Cơ sở dữ liệu', '2025-04-06 19:17:20', '2025-04-06 21:17:20', 20, 2),
(3, 4, 'KTPM01', 'Phân tích thiết kế hệ thống', '2025-04-07 19:17:20', '2025-04-07 21:17:20', 31, 3),
(4, 4, 'KTPM02', 'Lập trình web', '2025-04-08 19:17:20', '2025-04-08 21:17:20', 24, NULL),
(5, 5, 'CNTT01', 'Mạng máy tính', '2025-04-09 19:17:20', '2025-04-09 21:17:20', 32, 10),
(6, 5, 'CNTT03', 'Trí tuệ nhân tạo', '2025-04-10 19:17:20', '2025-04-10 21:17:20', 43, 6),
(7, 3, 'KTPM01', 'Lập trình Java', '2025-04-11 19:17:20', '2025-04-11 21:17:20', 26, 1),
(8, 4, 'CNTT02', 'Hệ điều hành', '2025-04-12 19:17:20', '2025-04-12 21:17:20', 29, 1),
(9, 3, 'KTPM02', 'Kiểm thử phần mềm', '2025-04-13 19:17:20', '2025-04-13 21:17:20', 24, 1),
(10, 4, 'CNTT03', 'An ninh mạng', '2025-04-14 19:17:20', '2025-04-14 21:17:20', 40, 6);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
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
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `email`, `password`, `role`, `class_code`, `created_at`) VALUES
(1, 'admin', 'Nguyễn Văn Hưng', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '', '2025-04-04 19:17:20'),
(2, 'admin1', 'Trần Minh Quân', 'admin1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, '2025-04-04 19:17:20'),
(3, 'teacher1', 'Phạm Thị Hồng', 'teacher1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, '2025-04-04 19:17:20'),
(4, 'teacher2', 'Hoàng Văn Dũng', 'teacher2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, '2025-04-04 19:17:20'),
(5, 'teacher3', 'Vũ Thanh Tùng', 'teacher3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, '2025-04-04 19:17:20'),
(6, 'student1', 'Nguyễn Thị Lan', 'student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'CNTT01', '2025-04-04 19:17:20'),
(7, 'student2', 'Bùi Ngọc Huy', 'student2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'CNTT01', '2025-04-04 19:17:20'),
(8, 'student3', 'Ngô Minh Khôi', 'student3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'CNTT02', '2025-04-04 19:17:20'),
(9, 'student4', 'Dương Thị Hạnh', 'student4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'CNTT02', '2025-04-04 19:17:20'),
(10, 'teacher4', 'Trần Quốc Hưng', 'teacher4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, '2025-04-04 19:17:20');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Chỉ mục cho bảng `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Chỉ mục cho bảng `room_equipments`
--
ALTER TABLE `room_equipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Chỉ mục cho bảng `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `equipments`
--
ALTER TABLE `equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `room_equipments`
--
ALTER TABLE `room_equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `maintenance_requests_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `room_equipments`
--
ALTER TABLE `room_equipments`
  ADD CONSTRAINT `room_equipments_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_equipments_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `timetables`
--
ALTER TABLE `timetables`
  ADD CONSTRAINT `timetables_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `timetables_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
