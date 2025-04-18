<?php
include 'src/Config/Database.php';

try {
    $db = new Config\Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare('DESCRIBE bookings');
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Cấu trúc bảng bookings:\n";
    print_r($columns);
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
