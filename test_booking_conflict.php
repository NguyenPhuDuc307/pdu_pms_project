<?php
include 'src/Config/Database.php';
include 'src/Models/BookingModel.php';

try {
    $bookingModel = new Models\BookingModel();
    
    // Kiểm tra xung đột với user_id
    $roomId = 12;
    $startTime = '2025-04-18 10:00:00';
    $endTime = '2025-04-18 12:00:00';
    $userId = 3; // Giả sử đây là ID của một giáo viên
    
    echo "Kiểm tra xung đột với user_id = $userId:<br>";
    $conflict1 = $bookingModel->checkBookingConflict($roomId, $startTime, $endTime);
    echo "Kết quả: " . ($conflict1 ? "Có xung đột" : "Không xung đột") . "<br><br>";
    
    // Kiểm tra xung đột không có user_id
    echo "Kiểm tra xung đột không có user_id:<br>";
    $conflict2 = $bookingModel->checkBookingConflict($roomId, $startTime, $endTime);
    echo "Kết quả: " . ($conflict2 ? "Có xung đột" : "Không xung đột") . "<br>";
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
