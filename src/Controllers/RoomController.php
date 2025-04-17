<?php

namespace Controllers;

use Models\RoomModel;
use Models\TimetableModel;
use Models\BookingModel;

require_once __DIR__ . '/../Helpers/AlertHelper.php';

use \AlertHelper;

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
            return ['success' => false, 'message' => AlertHelper::TIMETABLE_NOT_FOUND];
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
            $room_available = $this->timetableModel->checkRoomAvailability($room['id'], $start_time, $end_time, $timetable_id);
            if (!$room_available) {
                continue;
            }

            // Phòng phù hợp, thêm vào danh sách
            $available_rooms[] = $room;
        }

        // Nếu không có phòng phù hợp
        if (empty($available_rooms)) {
            return ['success' => false, 'message' => AlertHelper::ROOM_NOT_FOUND];
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
            return ['success' => true, 'message' => AlertHelper::TIMETABLE_UPDATED, 'room' => $selected_room];
        } else {
            return ['success' => false, 'message' => AlertHelper::ACTION_FAILED];
        }
    }

    public function cancelRoomSchedule($timetable_id)
    {
        // Lấy thông tin lịch dạy
        $timetable = $this->timetableModel->getTimetableById($timetable_id);
        if (!$timetable) {
            return ['success' => false, 'message' => AlertHelper::TIMETABLE_NOT_FOUND];
        }

        // Kiểm tra xem lịch dạy có phòng để hủy không
        if (!$timetable['room_id']) {
            return ['success' => false, 'message' => 'Lịch dạy này chưa được xếp phòng.'];
        }

        // Sử dụng phương thức cancelRoomSchedule của TimetableModel
        $updated = $this->timetableModel->cancelRoomSchedule($timetable_id);

        if ($updated) {
            return ['success' => true, 'message' => AlertHelper::ACTION_COMPLETED];
        } else {
            return ['success' => false, 'message' => AlertHelper::ACTION_FAILED];
        }
    }
}
