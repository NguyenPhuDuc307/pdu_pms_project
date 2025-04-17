<?php
// Script để cập nhật cấu trúc bảng bookings

// Kết nối đến cơ sở dữ liệu
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/src/Config/Database.php';
$db = new Config\Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Không thể kết nối đến cơ sở dữ liệu");
}

try {
    // Bắt đầu transaction
    $conn->beginTransaction();

    echo "Bắt đầu cập nhật cấu trúc bảng bookings...\n";

    // 1. Thêm cột user_id mới
    echo "Thêm cột user_id...\n";
    $conn->exec("ALTER TABLE bookings ADD COLUMN user_id INT NULL");

    // 2. Cập nhật user_id từ teacher_id hoặc student_id
    echo "Cập nhật user_id từ teacher_id và student_id...\n";
    $conn->exec("UPDATE bookings SET user_id = teacher_id WHERE teacher_id IS NOT NULL");
    $conn->exec("UPDATE bookings SET user_id = student_id WHERE student_id IS NOT NULL AND user_id IS NULL");

    // 3. Thêm khóa ngoại cho user_id
    echo "Thêm khóa ngoại cho user_id...\n";
    $conn->exec("ALTER TABLE bookings ADD CONSTRAINT bookings_ibfk_4 FOREIGN KEY (user_id) REFERENCES users(id)");

    // 4. Xóa các ràng buộc khóa ngoại cũ
    echo "Xóa các ràng buộc khóa ngoại cũ...\n";
    $conn->exec("ALTER TABLE bookings DROP FOREIGN KEY bookings_ibfk_2"); // teacher_id
    $conn->exec("ALTER TABLE bookings DROP FOREIGN KEY bookings_ibfk_3"); // student_id

    // 5. Xóa các cột cũ
    echo "Xóa các cột cũ...\n";
    $conn->exec("ALTER TABLE bookings DROP COLUMN teacher_id");
    $conn->exec("ALTER TABLE bookings DROP COLUMN student_id");

    // Commit transaction
    $conn->commit();
    echo "Cập nhật cấu trúc bảng bookings thành công!\n";
} catch (PDOException $e) {
    // Rollback transaction nếu có lỗi
    $conn->rollBack();
    echo "Lỗi: " . $e->getMessage() . "\n";
}
