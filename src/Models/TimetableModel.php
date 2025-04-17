<?php

namespace Models;

use Config\Database;

class TimetableModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy lịch phòng học hôm nay
    // Lấy lịch phòng học hôm nay
    public function getTodaySchedule()
    {
        try {
            // Đầu tiên kiểm tra xem bảng rooms có cột nào tên là gì
            $stmt = $this->db->prepare("DESCRIBE rooms");
            $stmt->execute();
            $roomColumns = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);

            // Xác định tên cột phòng (có thể là name, room_name, hoặc khác)
            $roomNameColumn = in_array('room_number', $roomColumns) ? 'room_number' : (in_array('name', $roomColumns) ? 'name' : 'id');

            // Sử dụng tên cột đúng trong truy vấn
            $query = "SELECT t.id, t.teacher_id, t.room_id, t.subject, t.start_time, t.end_time,
                 r.$roomNameColumn as room_name, u.full_name
                 FROM timetables t
                 JOIN rooms r ON t.room_id = r.id
                 JOIN users u ON t.teacher_id = u.id
                 WHERE DATE(t.start_time) = CURDATE()
                 ORDER BY t.start_time ASC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $schedule = [];
            foreach ($results as $row) {
                // Tính toán các time slot
                $startHour = date('H:i', strtotime($row['start_time']));

                if ($startHour >= '07:00' && $startHour < '09:30') {
                    $timeSlot = '7:00 - 9:30';
                } elseif ($startHour >= '09:45' && $startHour < '12:15') {
                    $timeSlot = '9:45 - 12:15';
                } elseif ($startHour >= '13:00' && $startHour < '15:30') {
                    $timeSlot = '13:00 - 15:30';
                } elseif ($startHour >= '15:45' && $startHour < '18:15') {
                    $timeSlot = '15:45 - 18:15';
                } else {
                    $timeSlot = 'other';
                }

                // Sắp xếp lịch theo phòng và khung giờ
                if (!isset($schedule[$row['room_name']])) {
                    $schedule[$row['room_name']] = [
                        'room_id' => $row['room_id'],
                        'room_number' => $row['room_name'], // Sử dụng tên cột đã xác định
                        'slots' => []
                    ];
                }

                $schedule[$row['room_name']]['slots'][$timeSlot] = [
                    'id' => $row['id'],
                    'subject' => $row['subject'],
                    'teacher' => "Teacher: " . $row['full_name'],
                    'start_time' => date('H:i', strtotime($row['start_time'])),
                    'end_time' => date('H:i', strtotime($row['end_time'])),
                ];
            }

            return $schedule;
        } catch (\PDOException $e) {
            // Nếu có lỗi, trả về mảng rỗng để tránh crash ứng dụng
            error_log("Lỗi lấy lịch phòng: " . $e->getMessage());
            return [];
        }
    }

    // Các phương thức khác
    public function getAllTimetables()
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, r.name as room_name,
            CASE WHEN u.full_name IS NOT NULL AND u.full_name != '' THEN u.full_name ELSE u.username END as teacher_name
         FROM timetables t
         LEFT JOIN rooms r ON t.room_id = r.id
         LEFT JOIN users u ON t.teacher_id = u.id
         ORDER BY t.start_time ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getTimetableById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, r.name as room_name,
            CASE WHEN u.full_name IS NOT NULL AND u.full_name != '' THEN u.full_name ELSE u.username END as teacher_name
         FROM timetables t
         LEFT JOIN rooms r ON t.room_id = r.id
         LEFT JOIN users u ON t.teacher_id = u.id
         WHERE t.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Thêm phương thức để lấy các lịch dạy trong khoảng thời gian
    public function getTimetablesByTimeRange($start_time, $end_time)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM timetables
             WHERE (start_time <= ? AND end_time >= ?)
             OR (start_time <= ? AND end_time >= ?)
             OR (start_time >= ? AND end_time <= ?)"
        );
        $stmt->execute([$end_time, $start_time, $end_time, $end_time, $start_time, $end_time]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addTimetable($teacher_id, $class_code, $subject, $start_time, $end_time, $participants = 0, $room_id = null)
    {
        try {
            // Bắt đầu transaction
            $this->db->beginTransaction();

            // Thêm lịch dạy mới
            $stmt = $this->db->prepare(
                "INSERT INTO timetables (teacher_id, class_code, subject, start_time, end_time, participants, room_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $result = $stmt->execute([$teacher_id, $class_code, $subject, $start_time, $end_time, $participants, $room_id]);

            if (!$result) {
                $this->db->rollBack();
                return false;
            }

            // Nếu có room_id, tạo booking tương ứng
            if ($room_id !== null) {
                $insertBookingStmt = $this->db->prepare(
                    "INSERT INTO bookings (room_id, teacher_id, student_id, class_code, start_time, end_time, status)
                     VALUES (?, ?, NULL, ?, ?, ?, 'được duyệt')"
                );
                $insertResult = $insertBookingStmt->execute([$room_id, $teacher_id, $class_code, $start_time, $end_time]);

                if (!$insertResult) {
                    $this->db->rollBack();
                    return false;
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            $this->db->rollBack();
            error_log("Lỗi khi thêm lịch dạy và booking: " . $e->getMessage());
            return false;
        }
    }

    // Phương thức updateTimetable:
    public function updateTimetable($id, $teacher_id, $room_id, $subject, $start_time, $end_time, $participants = 0, $class_code = null)
    {
        try {
            // Lấy thông tin lịch dạy hiện tại trước khi cập nhật
            $getTimetableStmt = $this->db->prepare("SELECT * FROM timetables WHERE id = ?");
            $getTimetableStmt->execute([$id]);
            $oldTimetable = $getTimetableStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$oldTimetable) {
                return false;
            }

            // Bắt đầu transaction
            $this->db->beginTransaction();

            // Cập nhật bảng timetables
            $stmt = $this->db->prepare(
                "UPDATE timetables
             SET teacher_id = ?, room_id = ?, subject = ?, start_time = ?, end_time = ?, participants = ?, class_code = ?
             WHERE id = ?"
            );
            $result = $stmt->execute([$teacher_id, $room_id, $subject, $start_time, $end_time, $participants, $class_code, $id]);

            if (!$result) {
                $this->db->rollBack();
                return false;
            }

            // Tìm kiếm booking tương ứng với lịch dạy cũ
            $findOldBookingStmt = $this->db->prepare(
                "SELECT id FROM bookings
                 WHERE class_code = ? AND teacher_id = ? AND
                 ((start_time = ? AND end_time = ?) OR
                  (room_id = ? AND start_time BETWEEN ? AND ?))"
            );
            $findOldBookingStmt->execute([
                $oldTimetable['class_code'],
                $oldTimetable['teacher_id'],
                $oldTimetable['start_time'],
                $oldTimetable['end_time'],
                $oldTimetable['room_id'],
                date('Y-m-d H:i:s', strtotime($oldTimetable['start_time']) - 3600), // 1 giờ trước
                date('Y-m-d H:i:s', strtotime($oldTimetable['end_time']) + 3600)   // 1 giờ sau
            ]);
            $oldBooking = $findOldBookingStmt->fetch(\PDO::FETCH_ASSOC);

            // Xử lý đồng bộ với bảng bookings
            if ($room_id !== null) {
                if ($oldBooking) {
                    // Cập nhật booking hiện có với tất cả thông tin mới
                    $updateBookingStmt = $this->db->prepare(
                        "UPDATE bookings
                         SET room_id = ?, teacher_id = ?, class_code = ?,
                             start_time = ?, end_time = ?, status = 'được duyệt'
                         WHERE id = ?"
                    );
                    $updateResult = $updateBookingStmt->execute([
                        $room_id,
                        $teacher_id,
                        $class_code,
                        $start_time,
                        $end_time,
                        $oldBooking['id']
                    ]);

                    if (!$updateResult) {
                        $this->db->rollBack();
                        return false;
                    }
                } else {
                    // Tạo booking mới
                    $insertBookingStmt = $this->db->prepare(
                        "INSERT INTO bookings (room_id, teacher_id, student_id, class_code, start_time, end_time, status)
                         VALUES (?, ?, NULL, ?, ?, ?, 'được duyệt')"
                    );
                    $insertResult = $insertBookingStmt->execute([$room_id, $teacher_id, $class_code, $start_time, $end_time]);

                    if (!$insertResult) {
                        $this->db->rollBack();
                        return false;
                    }
                }
            } else if ($oldBooking) {
                // Nếu room_id là null và có booking cũ, cập nhật trạng thái thành 'đã hủy'
                $updateStatusStmt = $this->db->prepare(
                    "UPDATE bookings SET status = 'đã hủy' WHERE id = ?"
                );
                $updateStatusResult = $updateStatusStmt->execute([$oldBooking['id']]);

                if (!$updateStatusResult) {
                    $this->db->rollBack();
                    return false;
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            $this->db->rollBack();
            error_log("Lỗi khi cập nhật timetable và booking: " . $e->getMessage());
            return false;
        }
    }

    public function deleteTimetable($id)
    {
        try {
            // Lấy thông tin lịch dạy trước khi xóa
            $getTimetableStmt = $this->db->prepare("SELECT * FROM timetables WHERE id = ?");
            $getTimetableStmt->execute([$id]);
            $timetable = $getTimetableStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$timetable) {
                return false;
            }

            // Bắt đầu transaction
            $this->db->beginTransaction();

            // Xóa lịch dạy
            $deleteStmt = $this->db->prepare("DELETE FROM timetables WHERE id = ?");
            $result = $deleteStmt->execute([$id]);

            if (!$result) {
                $this->db->rollBack();
                return false;
            }

            // Tìm và hủy booking tương ứng với các tiêu chí mở rộng
            $findBookingStmt = $this->db->prepare(
                "SELECT id FROM bookings
                 WHERE class_code = ? AND teacher_id = ? AND
                 ((start_time = ? AND end_time = ?) OR
                  (room_id = ? AND start_time BETWEEN ? AND ?))"
            );
            $findBookingStmt->execute([
                $timetable['class_code'],
                $timetable['teacher_id'],
                $timetable['start_time'],
                $timetable['end_time'],
                $timetable['room_id'],
                date('Y-m-d H:i:s', strtotime($timetable['start_time']) - 3600), // 1 giờ trước
                date('Y-m-d H:i:s', strtotime($timetable['end_time']) + 3600)   // 1 giờ sau
            ]);
            $booking = $findBookingStmt->fetch(\PDO::FETCH_ASSOC);

            if ($booking) {
                // Xóa booking tương ứng
                $deleteBookingStmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
                $deleteResult = $deleteBookingStmt->execute([$booking['id']]);

                if (!$deleteResult) {
                    $this->db->rollBack();
                    return false;
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            $this->db->rollBack();
            error_log("Lỗi khi xóa lịch dạy và booking: " . $e->getMessage());
            return false;
        }
    }

    // Lấy lịch dạy của giáo viên
    public function getTimetablesByTeacher($teacher_id, $filters = [])
    {
        try {
            // Kiểm tra xem bảng timetables có tồn tại không
            $checkTable = $this->db->query("SHOW TABLES LIKE 'timetables'");
            if ($checkTable->rowCount() == 0) {
                error_log("ERROR: Bảng timetables không tồn tại trong cơ sở dữ liệu");
                return [];
            }

            // Debug
            error_log("DEBUG: Đang lấy lịch dạy cho giáo viên ID = " . $teacher_id);

            $sql = "SELECT t.*, r.name as room_name,
                    CASE WHEN u.full_name IS NOT NULL AND u.full_name != '' THEN u.full_name ELSE u.username END as teacher_name
                    FROM timetables t
                    LEFT JOIN rooms r ON t.room_id = r.id
                    LEFT JOIN users u ON t.teacher_id = u.id
                    WHERE t.teacher_id = ?";

            $params = [$teacher_id];

            // Thêm các điều kiện lọc
            if (isset($filters['subject']) && !empty($filters['subject'])) {
                $sql .= " AND t.subject LIKE ?";
                $params[] = '%' . $filters['subject'] . '%';
            }

            if (isset($filters['class_code']) && !empty($filters['class_code'])) {
                $sql .= " AND t.class_code LIKE ?";
                $params[] = '%' . $filters['class_code'] . '%';
            }

            if (isset($filters['start_date']) && !empty($filters['start_date'])) {
                $sql .= " AND t.start_time >= ?";
                $params[] = $filters['start_date'];
            }

            if (isset($filters['end_date']) && !empty($filters['end_date'])) {
                $sql .= " AND t.start_time <= ?";
                $params[] = $filters['end_date'];
            }

            // Sắp xếp theo thời gian bắt đầu
            $sql .= " ORDER BY t.start_time ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Debug
            error_log("DEBUG: Tìm thấy " . count($results) . " lịch dạy");

            return $results;
        } catch (\PDOException $e) {
            error_log("ERROR: Lỗi khi lấy lịch dạy: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra xem phòng có trống trong khoảng thời gian không
     * @param int $room_id ID của phòng cần kiểm tra
     * @param string $start_time Thời gian bắt đầu
     * @param string $end_time Thời gian kết thúc
     * @param int|null $exclude_id ID của lịch cần loại trừ (khi sửa lịch)
     * @return bool true nếu phòng trống, false nếu đã có người đặt
     */
    public function checkRoomAvailability($room_id, $start_time, $end_time, $exclude_id = null)
    {
        $query = "SELECT COUNT(*) as count FROM timetables
             WHERE room_id = ?
             AND ((start_time < ? AND end_time > ?)
                  OR (start_time < ? AND end_time > ?)
                  OR (start_time >= ? AND end_time <= ?)
                  OR (start_time <= ? AND end_time >= ?))";
        $params = [$room_id, $end_time, $start_time, $end_time, $start_time, $start_time, $end_time, $start_time, $end_time];

        if ($exclude_id) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Trả về true nếu không có lịch trùng (count = 0)
        return $result['count'] == 0;
    }

    public function cancelRoomSchedule($timetable_id)
    {
        try {
            // Lấy thông tin lịch dạy trước khi hủy
            $getTimetableStmt = $this->db->prepare("SELECT * FROM timetables WHERE id = ?");
            $getTimetableStmt->execute([$timetable_id]);
            $timetable = $getTimetableStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$timetable) {
                return false;
            }

            // Bắt đầu transaction
            $this->db->beginTransaction();

            // Tìm booking tương ứng với các tiêu chí mở rộng
            $findBookingStmt = $this->db->prepare(
                "SELECT id FROM bookings
                 WHERE class_code = ? AND teacher_id = ? AND
                 ((start_time = ? AND end_time = ?) OR
                  (room_id = ? AND start_time BETWEEN ? AND ?))"
            );
            $findBookingStmt->execute([
                $timetable['class_code'],
                $timetable['teacher_id'],
                $timetable['start_time'],
                $timetable['end_time'],
                $timetable['room_id'],
                date('Y-m-d H:i:s', strtotime($timetable['start_time']) - 3600), // 1 giờ trước
                date('Y-m-d H:i:s', strtotime($timetable['end_time']) + 3600)   // 1 giờ sau
            ]);
            $booking = $findBookingStmt->fetch(\PDO::FETCH_ASSOC);

            // Xóa booking trước nếu tìm thấy
            if ($booking) {
                // Xóa booking thay vì cập nhật trạng thái
                $deleteBookingStmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
                $deleteResult = $deleteBookingStmt->execute([$booking['id']]);

                if (!$deleteResult) {
                    $this->db->rollBack();
                    error_log("Lỗi khi xóa booking: " . json_encode($booking));
                    return false;
                }
            }

            // Sau đó mới cập nhật bảng timetables
            $query = "UPDATE timetables SET room_id = NULL WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([$timetable_id]);

            if (!$result) {
                $this->db->rollBack();
                return false;
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            $this->db->rollBack();
            error_log("Lỗi khi hủy phòng trong lịch dạy và booking: " . $e->getMessage());
            return false;
        }
    }
}
