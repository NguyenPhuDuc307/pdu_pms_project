<?php
namespace Controllers;

use Models\BookingModel;
use Models\RoomModel;
use Models\UserModel;

class StudentController {
    private $bookingModel;
    private $roomModel;
    private $userModel;

    public function __construct() {
        $this->bookingModel = new BookingModel();
        $this->roomModel = new RoomModel();
        $this->userModel = new UserModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        $schedule = $this->bookingModel->getBookingsByClassCode($user['class_code']);
        return ['schedule' => $schedule];
    }

    public function bookRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id = $_POST['room_id'] ?? null;
            $class_code = $_POST['class_code'] ?? '';
            $start_time = $_POST['start_time'] ?? '';
            $end_time = $_POST['end_time'] ?? '';

            $student_id = $_SESSION['user_id'] ?? null;

            if (!$student_id) {
                return [
                    'error' => 'Bạn cần đăng nhập để đặt phòng',
                    'rooms' => $this->roomModel->getAllRooms(),
                    'available_rooms' => []
                ];
            }

            if ($room_id && $class_code && $start_time && $end_time) {
                $start_time = date('Y-m-d H:i:s', strtotime($start_time));
                $end_time = date('Y-m-d H:i:s', strtotime($end_time));
                $conflict = $this->bookingModel->checkBookingConflict($room_id, $start_time, $end_time);
                if ($conflict) {
                    return [
                        'error' => 'Phòng đã được đặt trong khoảng thời gian này',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }

                $bookingData = [
                    'room_id' => $room_id,
                    'teacher_id' => null,
                    'student_id' => $student_id,
                    'class_code' => $class_code,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'status' => 'chờ duyệt'
                ];

                $success = $this->bookingModel->addBooking($bookingData);
                if ($success) {
                    header('Location: /pdu_pms_project/public/student/book_room?message=Đặt phòng thành công, đang chờ duyệt');
                    exit;
                } else {
                    return [
                        'error' => 'Không thể đặt phòng, vui lòng thử lại',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }
            } else {
                return [
                    'error' => 'Vui lòng điền đầy đủ thông tin',
                    'rooms' => $this->roomModel->getAllRooms(),
                    'available_rooms' => []
                ];
            }
        }

        // Lấy danh sách phòng khả dụng (trống) nếu có start_time và end_time
        $start_time = $_POST['start_time'] ?? null;
        $end_time = $_POST['end_time'] ?? null;
        $available_rooms = [];
        if ($start_time && $end_time) {
            $start_time = date('Y-m-d H:i:s', strtotime($start_time));
            $end_time = date('Y-m-d H:i:s', strtotime($end_time));
            $rooms = $this->roomModel->getAllRooms();
            foreach ($rooms as $room) {
                if (!$this->bookingModel->checkBookingConflict($room['id'], $start_time, $end_time)) {
                    $available_rooms[] = $room['id'];
                }
            }
        }

        return [
            'rooms' => $this->roomModel->getAllRooms(),
            'available_rooms' => $available_rooms
        ];
    }
}
