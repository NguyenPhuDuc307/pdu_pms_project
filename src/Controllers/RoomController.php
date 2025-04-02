<?php

namespace Controllers;

use Models\RoomModel;
use Models\TimetableModel;
use Models\BookingModel;

class RoomController
{
    private $roomModel;
    private $timetableModel;
    private $bookingModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->timetableModel = new TimetableModel();
        $this->bookingModel = new BookingModel();
    }

    // Hàm xếp phòng tự động
    public function autoScheduleRoom($timetable_id)
    {
        // Lấy thông tin lịch dạy
        $timetable = $this->timetableModel->getTimetableById($timetable_id);
        if (!$timetable) {
            return ['success' => false, 'message' => 'Không tìm thấy lịch dạy.'];
        }

        $start_time = $timetable['start_time'];
        $end_time = $timetable['end_time'];
        $participants = $timetable['participants'] ?? 0;

        // Lấy danh sách phòng trống và đủ sức chứa
        $rooms = $this->roomModel->getAvailableRoomsByTimeRange($start_time, $end_time, $participants);

        // Tìm phòng phù hợp
        $available_rooms = [];
        foreach ($rooms as $room) {
            // Kiểm tra xung đột lịch đặt phòng
            $conflict = $this->bookingModel->checkBookingConflict($room['id'], $start_time, $end_time);
            if ($conflict) {
                continue;
            }

            // Kiểm tra xung đột với các lịch dạy khác
            $existing_timetables = $this->timetableModel->getTimetablesByTimeRange($start_time, $end_time);
            $has_conflict = false;
            foreach ($existing_timetables as $existing_timetable) {
                if ($existing_timetable['id'] == $timetable_id || !$existing_timetable['room_id']) {
                    continue;
                }
                if ($existing_timetable['room_id'] == $room['id']) {
                    $has_conflict = true;
                    break;
                }
            }
            if ($has_conflict) {
                continue;
            }

            // Phòng phù hợp, thêm vào danh sách
            $available_rooms[] = $room;
        }

        // Nếu không có phòng phù hợp
        if (empty($available_rooms)) {
            return ['success' => false, 'message' => 'Không tìm thấy phòng phù hợp.'];
        }

        // Chọn phòng đầu tiên trong danh sách (có thể cải tiến để chọn phòng tối ưu hơn)
        $selected_room = $available_rooms[0];

        // Cập nhật lịch dạy với phòng được chọn
        $updated = $this->timetableModel->updateTimetable(
            $timetable_id,                 // id
            $timetable['teacher_id'],      // teacher_id
            $selected_room['id'],          // room_id (phòng được chọn từ available_rooms)
            $timetable['subject'],         // subject
            $timetable['start_time'],      // start_time
            $timetable['end_time'],        // end_time
            $timetable['participants'] ?? 0, // participants
            $timetable['class_code']       // class_code
        );

        if ($updated) {
            return ['success' => true, 'room' => $selected_room];
        } else {
            return ['success' => false, 'message' => 'Cập nhật lịch dạy thất bại.'];
        }
    }

    public function cancelRoomSchedule($timetable_id)
    {
        // Lấy thông tin lịch dạy
        $timetable = $this->timetableModel->getTimetableById($timetable_id);
        if (!$timetable) {
            return ['success' => false, 'message' => 'Không tìm thấy lịch dạy.'];
        }

        // Kiểm tra xem lịch dạy có phòng để hủy không
        if (!$timetable['room_id']) {
            return ['success' => false, 'message' => 'Lịch dạy này chưa được xếp phòng.'];
        }

        // Cập nhật lịch dạy với room_id = NULL
        $updated = $this->timetableModel->updateTimetable(
            $timetable_id,                 // id
            $timetable['teacher_id'],      // teacher_id
            null,                          // room_id (hủy phòng)
            $timetable['subject'],         // subject
            $timetable['start_time'],      // start_time
            $timetable['end_time'],        // end_time
            $timetable['participants'] ?? 0, // participants
            $timetable['class_code']       // class_code
        );

        if ($updated) {
            return ['success' => true, 'message' => 'Hủy phòng thành công'];
        } else {
            return ['success' => false, 'message' => 'Hủy phòng thất bại.'];
        }
    }
}
