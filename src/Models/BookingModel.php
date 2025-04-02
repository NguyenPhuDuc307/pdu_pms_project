<?php

namespace Models;

use Config\Database;

class BookingModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy số lượng đặt phòng trong ngày hôm nay
    public function getTodayBookingsCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM bookings WHERE DATE(start_time) = CURDATE()");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Lấy số lượng phòng đang được sử dụng
    public function getCurrentlyInUseRoomsCount()
    {
        $currentTime = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT room_id) as total FROM bookings WHERE ? BETWEEN start_time AND end_time");
        $stmt->execute([$currentTime]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Lấy các đặt phòng gần đây - sửa để xử lý LIMIT đúng cách
    public function getRecentBookings($limit = 10)
    {
        $limit = (int)$limit; // Ép kiểu để đảm bảo an toàn
        $stmt = $this->db->prepare("SELECT * FROM bookings ORDER BY created_at DESC LIMIT $limit");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy thời gian cao điểm (số lượng đặt phòng theo giờ)
    public function getPeakHours()
    {
        $stmt = $this->db->prepare(
            "SELECT HOUR(start_time) as hour, COUNT(*) as booking_count 
             FROM bookings 
             WHERE DATE(start_time) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY HOUR(start_time) 
             ORDER BY booking_count DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy tổng số đặt phòng trong tháng hiện tại
    public function getBookingsThisMonth()
    {
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total 
             FROM bookings 
             WHERE start_time BETWEEN ? AND ?"
        );
        $stmt->execute([$firstDayOfMonth, $lastDayOfMonth]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }

    // Tính tỷ lệ thành công của việc đặt phòng
    public function getBookingSuccessRate()
    {
        $stmt = $this->db->prepare(
            "SELECT 
             (SELECT COUNT(*) FROM bookings WHERE status = 'được duyệt') as approved,
             COUNT(*) as total
         FROM bookings"
        );
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result['total'] == 0) {
            return 0;
        }

        return round(($result['approved'] / $result['total']) * 100, 2);
    }

    public function checkBookingConflict($roomId, $startTime, $endTime, $bookingId = null)
    {
        // Kiểm tra xem có booking nào xung đột với khoảng thời gian này không
        $query = "SELECT COUNT(*) FROM bookings 
              WHERE room_id = ? AND status = 'được duyệt' 
              AND (
                  (start_time <= ? AND end_time > ?) OR  -- Booking hiện tại bắt đầu trong khoảng thời gian của booking khác
                  (start_time < ? AND end_time >= ?) OR  -- Booking hiện tại kết thúc trong khoảng thời gian của booking khác
                  (? <= start_time AND end_time <= ?)     -- Booking hiện tại bao trùm hoàn toàn booking khác
              )";

        $params = [$roomId, $startTime, $startTime, $endTime, $endTime, $startTime, $endTime];

        // Nếu đang cập nhật booking hiện có, loại trừ booking đó khỏi kiểm tra xung đột
        if ($bookingId) {
            $query .= " AND id != ?";
            $params[] = $bookingId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function getYesterdayBookingsCount()
    {
        $query = "SELECT COUNT(*) FROM bookings WHERE DATE(start_time) = DATE(DATE_SUB(CURDATE(), INTERVAL 1 DAY))";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getRoomsInUseYesterday()
    {
        $query = "SELECT COUNT(DISTINCT room_id) FROM bookings 
              WHERE DATE(start_time) = DATE(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) 
              AND status = 'được duyệt'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getBookingsByTeacher($teacherId)
    {
        $stmt = $this->db->prepare("SELECT b.*, r.name AS room_name FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.teacher_id = ? AND b.status = 'được duyệt' ORDER BY b.start_time ASC");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getBookingsByClassCode($classCode)
    {
        $stmt = $this->db->prepare("SELECT b.*, r.name AS room_name FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.class_code = ? AND b.status = 'được duyệt' ORDER BY b.start_time ASC");
        $stmt->execute([$classCode]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllBookings()
    {
        $stmt = $this->db->prepare(
            "SELECT b.*, 
                r.name AS room_name, 
                u1.username AS teacher_name, 
                u2.username AS student_name 
         FROM bookings b 
         JOIN rooms r ON b.room_id = r.id 
         LEFT JOIN users u1 ON b.teacher_id = u1.id 
         LEFT JOIN users u2 ON b.student_id = u2.id"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getBookingById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function addBooking($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO bookings (room_id, teacher_id, student_id, class_code, start_time, end_time, status) 
             VALUES (:room_id, :teacher_id, :student_id, :class_code, :start_time, :end_time, :status)"
        );
        return $stmt->execute([
            ':room_id' => $data['room_id'],
            ':teacher_id' => $data['teacher_id'] ?? null,
            ':student_id' => $data['student_id'] ?? null,
            ':class_code' => $data['class_code'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':status' => $data['status']
        ]);
    }

    public function updateBooking($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE bookings 
             SET room_id = ?, teacher_id = ?, student_id = ?, class_code = ?, start_time = ?, end_time = ?, status = ? 
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['room_id'],
            $data['teacher_id'] ?? null,
            $data['student_id'] ?? null,
            $data['class_code'],
            $data['start_time'],
            $data['end_time'],
            $data['status'],
            $id
        ]);
    }

    public function deleteBooking($id)
    {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
