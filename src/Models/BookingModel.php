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
        // Thêm log để debug
        error_log("BookingModel::checkBookingConflict - Tham số đầu vào: roomId=$roomId, startTime=$startTime, endTime=$endTime, bookingId=$bookingId");

        // Kiểm tra xem có booking nào xung đột với khoảng thời gian này không
        $query = "SELECT id, start_time, end_time FROM bookings
              WHERE room_id = ? AND status IN ('được duyệt', 'đã duyệt', 'chờ duyệt')
              AND (
                  (start_time <= ? AND end_time > ?) OR  -- Booking hiện tại bắt đầu trong khoảng thời gian của booking khác
                  (start_time < ? AND end_time >= ?) OR  -- Booking hiện tại kết thúc trong khoảng thời gian của booking khác
                  (start_time >= ? AND end_time <= ?) OR -- Booking hiện tại nằm hoàn toàn trong booking khác
                  (start_time <= ? AND end_time >= ?)    -- Booking hiện tại bao trùm hoàn toàn booking khác
              )";

        $params = [$roomId, $startTime, $startTime, $endTime, $endTime, $startTime, $endTime, $startTime, $endTime];

        // Nếu đang cập nhật booking hiện có, loại trừ booking đó khỏi kiểm tra xung đột
        if ($bookingId) {
            $query .= " AND id != ?";
            $params[] = $bookingId;
        }

        // Thêm log để debug
        error_log("BookingModel::checkBookingConflict - Kiểm tra xung đột cho phòng $roomId từ $startTime đến $endTime");

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $conflictingBookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $result = count($conflictingBookings) > 0;

            if ($result) {
                foreach ($conflictingBookings as $booking) {
                    error_log("BookingModel::checkBookingConflict - Xung đột với booking ID {$booking['id']} ({$booking['start_time']} - {$booking['end_time']})");
                }
            }

            error_log("BookingModel::checkBookingConflict - Kết quả kiểm tra xung đột: " . ($result ? "Có xung đột" : "Không xung đột"));

            return $result;
        } catch (\Exception $e) {
            error_log("BookingModel::checkBookingConflict - Lỗi: " . $e->getMessage());
            // Trả về false để đảm bảo phòng vẫn được hiển thị là trống nếu có lỗi
            return false;
        }
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
        $stmt = $this->db->prepare("SELECT b.*, r.name AS room_name,
            u.full_name AS user_name
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.user_id = ? AND b.status = 'được duyệt' AND u.role = 'teacher'
            ORDER BY b.start_time ASC");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getBookingsByStudent($studentId)
    {
        $stmt = $this->db->prepare("SELECT b.*, r.name AS room_name,
            u.full_name AS user_name
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.user_id = ? AND u.role = 'student'
            ORDER BY b.start_time DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getBookingsByClassCode($classCode)
    {
        $stmt = $this->db->prepare("SELECT b.*, r.name AS room_name,
            u.full_name AS user_name,
            u.role AS user_role
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.class_code = ? AND b.status = 'được duyệt'
            ORDER BY b.start_time ASC");
        $stmt->execute([$classCode]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllBookings()
    {
        $stmt = $this->db->prepare(
            "SELECT b.*,
                r.name AS room_name,
                CASE WHEN u.full_name IS NOT NULL AND u.full_name != '' THEN u.full_name ELSE u.username END AS user_name,
                u.full_name AS user_fullname,
                u.role AS user_role
         FROM bookings b
         JOIN rooms r ON b.room_id = r.id
         LEFT JOIN users u ON b.user_id = u.id"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getBookingById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT b.*,
                r.name AS room_name,
                CASE WHEN u.full_name IS NOT NULL AND u.full_name != '' THEN u.full_name ELSE u.username END AS user_name,
                u.full_name AS user_fullname,
                u.role AS user_role
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function addBooking($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO bookings (room_id, user_id, class_code, start_time, end_time, purpose, status)
             VALUES (:room_id, :user_id, :class_code, :start_time, :end_time, :purpose, :status)"
        );
        return $stmt->execute([
            ':room_id' => $data['room_id'],
            ':user_id' => $data['user_id'],
            ':class_code' => $data['class_code'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':purpose' => $data['purpose'] ?? null,
            ':status' => $data['status']
        ]);
    }

    public function updateBooking($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE bookings
             SET room_id = ?, user_id = ?, class_code = ?, start_time = ?, end_time = ?, purpose = ?, status = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['room_id'],
            $data['user_id'],
            $data['class_code'],
            $data['start_time'],
            $data['end_time'],
            $data['purpose'] ?? null,
            $data['status'],
            $id
        ]);
    }

    public function deleteBooking($id)
    {
        try {
            // Lấy thông tin booking trước khi xóa
            $getBookingStmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
            $getBookingStmt->execute([$id]);
            $booking = $getBookingStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$booking) {
                return false;
            }

            // Bắt đầu transaction
            $this->db->beginTransaction();

            // Xóa booking
            $deleteStmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
            $deleteResult = $deleteStmt->execute([$id]);

            if (!$deleteResult) {
                $this->db->rollBack();
                return false;
            }

            // Tìm và cập nhật timetable tương ứng (nếu có)
            $findTimetableStmt = $this->db->prepare(
                "SELECT id FROM timetables
                 WHERE class_code = ? AND teacher_id = ? AND room_id = ? AND
                 ((start_time = ? AND end_time = ?) OR
                  (start_time BETWEEN ? AND ?))"
            );

            // Lấy teacher_id từ user_id nếu user là giáo viên
            $teacherId = null;
            if ($booking['user_id']) {
                $userStmt = $this->db->prepare("SELECT id FROM users WHERE id = ? AND role = 'teacher'");
                $userStmt->execute([$booking['user_id']]);
                $teacherId = $userStmt->fetchColumn();
            }

            $findTimetableStmt->execute([
                $booking['class_code'],
                $teacherId,
                $booking['room_id'],
                $booking['start_time'],
                $booking['end_time'],
                date('Y-m-d H:i:s', strtotime($booking['start_time']) - 3600), // 1 giờ trước
                date('Y-m-d H:i:s', strtotime($booking['end_time']) + 3600)   // 1 giờ sau
            ]);
            $timetable = $findTimetableStmt->fetch(\PDO::FETCH_ASSOC);

            if ($timetable) {
                // Cập nhật room_id = NULL trong timetable
                $updateTimetableStmt = $this->db->prepare("UPDATE timetables SET room_id = NULL WHERE id = ?");
                $updateResult = $updateTimetableStmt->execute([$timetable['id']]);

                if (!$updateResult) {
                    $this->db->rollBack();
                    error_log("Lỗi khi cập nhật timetable sau khi xóa booking: " . json_encode($timetable));
                    return false;
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Lỗi khi xóa booking: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the status of a booking
     *
     * @param int $id Booking ID
     * @param string $status New status (approved, rejected, pending, cancelled)
     * @return bool Success or failure
     */
    public function updateBookingStatus($id, $status)
    {
        try {
            // Map standard status values to legacy values if needed
            $statusMapping = [
                'approved' => 'được duyệt',
                'rejected' => 'từ chối',
                'pending' => 'chờ duyệt',
                'cancelled' => 'đã hủy'
            ];

            // Use the mapped value if it exists, otherwise use the original value
            $dbStatus = isset($statusMapping[$status]) ? $statusMapping[$status] : $status;

            // Nếu trạng thái là 'đã hủy', cần cập nhật cả timetable
            if ($dbStatus === 'đã hủy' || $status === 'cancelled') {
                // Lấy thông tin booking
                $getBookingStmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
                $getBookingStmt->execute([$id]);
                $booking = $getBookingStmt->fetch(\PDO::FETCH_ASSOC);

                if (!$booking) {
                    return false;
                }

                // Bắt đầu transaction
                $this->db->beginTransaction();

                // Cập nhật trạng thái booking
                $updateStmt = $this->db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
                $updateResult = $updateStmt->execute([$dbStatus, $id]);

                if (!$updateResult) {
                    $this->db->rollBack();
                    return false;
                }

                // Tìm và cập nhật timetable tương ứng (nếu có)
                $findTimetableStmt = $this->db->prepare(
                    "SELECT id FROM timetables
                     WHERE class_code = ? AND teacher_id = ? AND room_id = ? AND
                     ((start_time = ? AND end_time = ?) OR
                      (start_time BETWEEN ? AND ?))"
                );

                // Lấy teacher_id từ user_id nếu user là giáo viên
                $teacherId = null;
                if ($booking['user_id']) {
                    $userStmt = $this->db->prepare("SELECT id FROM users WHERE id = ? AND role = 'teacher'");
                    $userStmt->execute([$booking['user_id']]);
                    $teacherId = $userStmt->fetchColumn();
                }

                $findTimetableStmt->execute([
                    $booking['class_code'],
                    $teacherId,
                    $booking['room_id'],
                    $booking['start_time'],
                    $booking['end_time'],
                    date('Y-m-d H:i:s', strtotime($booking['start_time']) - 3600), // 1 giờ trước
                    date('Y-m-d H:i:s', strtotime($booking['end_time']) + 3600)   // 1 giờ sau
                ]);
                $timetable = $findTimetableStmt->fetch(\PDO::FETCH_ASSOC);

                if ($timetable) {
                    // Cập nhật room_id = NULL trong timetable
                    $updateTimetableStmt = $this->db->prepare("UPDATE timetables SET room_id = NULL WHERE id = ?");
                    $updateResult = $updateTimetableStmt->execute([$timetable['id']]);

                    if (!$updateResult) {
                        $this->db->rollBack();
                        error_log("Lỗi khi cập nhật timetable sau khi hủy booking: " . json_encode($timetable));
                        return false;
                    }
                }

                // Commit transaction
                $this->db->commit();
                return true;
            } else {
                // Cập nhật trạng thái bình thường
                $stmt = $this->db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
                return $stmt->execute([$dbStatus, $id]);
            }
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Lỗi khi cập nhật trạng thái booking: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy số lượng đặt phòng đang chờ duyệt
     * @return int Số lượng đặt phòng chờ duyệt
     */
    public function getPendingBookingsCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM bookings WHERE status = 'chờ duyệt'");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Lấy tổng số đặt phòng
     * @return int Tổng số đặt phòng
     */
    public function getTotalBookings()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM bookings");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}
