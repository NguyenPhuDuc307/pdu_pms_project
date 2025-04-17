<?php
namespace Models;

use PDO;
use Config\Database;

class ScheduleModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Lấy lịch học của lớp theo mã lớp
     * 
     * @param string $classCode Mã lớp
     * @return array Danh sách lịch học
     */
    public function getScheduleByClassCode($classCode) {
        $sql = "SELECT 
                    b.id,
                    b.start_time,
                    b.end_time,
                    b.class_code,
                    r.id as room_id,
                    r.name as room_name,
                    r.location,
                    u.full_name as teacher_name
                FROM 
                    bookings b
                JOIN 
                    rooms r ON b.room_id = r.id
                LEFT JOIN 
                    users u ON b.teacher_id = u.id
                WHERE 
                    b.class_code = :class_code
                    AND b.status = 'được duyệt'
                ORDER BY 
                    b.start_time ASC";
                    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':class_code', $classCode);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy lịch biểu cho phòng
     * 
     * @param int $roomId ID phòng
     * @param string $startDate Ngày bắt đầu (Định dạng Y-m-d)
     * @param string $endDate Ngày kết thúc (Định dạng Y-m-d) 
     * @return array Danh sách lịch sử dụng phòng
     */
    public function getRoomSchedule($roomId, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    b.id,
                    b.start_time,
                    b.end_time,
                    b.class_code,
                    b.status,
                    b.notes,
                    u.full_name as teacher_name,
                    s.full_name as student_name
                FROM 
                    bookings b
                LEFT JOIN 
                    users u ON b.teacher_id = u.id
                LEFT JOIN 
                    users s ON b.student_id = s.id
                WHERE 
                    b.room_id = :room_id";
                    
        // Thêm điều kiện ngày nếu có
        if ($startDate) {
            $sql .= " AND DATE(b.start_time) >= :start_date";
        }
        
        if ($endDate) {
            $sql .= " AND DATE(b.end_time) <= :end_date";
        }
        
        $sql .= " ORDER BY b.start_time ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':room_id', $roomId);
            
            if ($startDate) {
                $stmt->bindParam(':start_date', $startDate);
            }
            
            if ($endDate) {
                $stmt->bindParam(':end_date', $endDate);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
} 