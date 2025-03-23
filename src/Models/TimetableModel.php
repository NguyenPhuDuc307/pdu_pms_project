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
            "SELECT t.*, r.name as room_name, u.username as teacher_name 
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
            "SELECT t.*, r.name as room_name, u.username as teacher_name 
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

    public function addTimetable($teacher_id, $class_code, $subject, $start_time, $end_time, $participants = 0)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO timetables (teacher_id, class_code, subject, start_time, end_time, participants) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$teacher_id, $class_code, $subject, $start_time, $end_time, $participants]);
    }

    // Phương thức updateTimetable:
    public function updateTimetable($id, $teacher_id, $room_id, $subject, $start_time, $end_time, $participants = 0, $class_code = null)
    {
        $stmt = $this->db->prepare(
            "UPDATE timetables 
         SET teacher_id = ?, room_id = ?, subject = ?, start_time = ?, end_time = ?, participants = ?, class_code = ? 
         WHERE id = ?"
        );
        return $stmt->execute([$teacher_id, $room_id, $subject, $start_time, $end_time, $participants, $class_code, $id]);
    }

    public function deleteTimetable($id)
    {
        $stmt = $this->db->prepare("DELETE FROM timetables WHERE id = ?");
        return $stmt->execute([$id]);
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
             AND ((start_time <= ? AND end_time > ?) 
                  OR (start_time < ? AND end_time >= ?) 
                  OR (start_time >= ? AND end_time <= ?))";
        $params = [$room_id, $end_time, $start_time, $end_time, $start_time, $start_time, $end_time];

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
        $query = "UPDATE timetables SET room_id = NULL WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$timetable_id]);
    }
}
