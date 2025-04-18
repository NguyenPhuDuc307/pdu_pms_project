<?php

namespace Controllers;

use Models\BookingModel;
use Models\RoomModel;
use Models\UserModel;

class BookingController
{
    private $bookingModel;
    private $roomModel;
    private $userModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->roomModel = new RoomModel();
        $this->userModel = new UserModel();
    }

    /**
     * Xử lý đặt phòng chung cho tất cả các vai trò
     */
    public function bookRoom($data = [])
    {
        // Kiểm tra người dùng đã đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $role = $_SESSION['role'];
        $userId = $_SESSION['user_id'];
        $isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Xử lý khi form được submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id = $data['room_id'] ?? null;
            $class_code = $data['class_code'] ?? '';
            $start_time = $data['start_time'] ?? '';
            $end_time = $data['end_time'] ?? '';
            $purpose = $data['purpose'] ?? '';
            $status = $data['status'] ?? 'chờ duyệt'; // Mặc định là chờ duyệt

            // Xử lý khác nhau cho từng vai trò
            switch ($role) {
                case 'admin':
                    $user_type = $data['user_type'] ?? 'teacher';
                    $teacher_id = $user_type === 'teacher' ? ($data['teacher_id'] ?? null) : null;
                    $student_id = $user_type === 'student' ? ($data['student_id'] ?? null) : null;

                    // Admin có thể đặt cho giáo viên hoặc sinh viên
                    if ($user_type === 'teacher' && !$teacher_id) {
                        return [
                            'error' => 'Vui lòng chọn giảng viên',
                            'rooms' => $this->roomModel->getAllRooms(),
                            'users' => $this->userModel->getAllUsers(),
                            'available_rooms' => []
                        ];
                    }
                    if ($user_type === 'student' && !$student_id) {
                        return [
                            'error' => 'Vui lòng chọn sinh viên',
                            'rooms' => $this->roomModel->getAllRooms(),
                            'users' => $this->userModel->getAllUsers(),
                            'available_rooms' => []
                        ];
                    }

                    // Sử dụng user_id từ teacher_id hoặc student_id
                    $user_id = $user_type === 'teacher' ? $teacher_id : $student_id;
                    break;

                case 'teacher':
                case 'student':
                    // Giáo viên và sinh viên đặt cho chính mình
                    $user_id = $userId;
                    break;

                default:
                    return [
                        'error' => 'Vai trò không hợp lệ',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'users' => $this->userModel->getAllUsers(),
                        'available_rooms' => []
                    ];
            }

            // Kiểm tra các trường bắt buộc
            if ($room_id && $start_time && $end_time && $purpose) {
                // Thêm log để debug
                error_log("BookingController::bookRoom - Dữ liệu POST: " . json_encode($_POST));
                error_log("BookingController::bookRoom - user_id = $user_id");

                // Kiểm tra xung đột lịch
                $conflict = $this->bookingModel->checkBookingConflict($room_id, $start_time, $end_time);
                if ($conflict) {
                    return [
                        'error' => 'Phòng đã được đặt trong khoảng thời gian này',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'users' => $this->userModel->getAllUsers(),
                        'available_rooms' => []
                    ];
                }

                // Tạo dữ liệu đặt phòng
                $bookingData = [
                    'room_id' => $room_id,
                    'user_id' => $user_id,
                    'class_code' => $class_code,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'purpose' => $purpose,
                    'status' => $status
                ];

                // Thêm đặt phòng vào cơ sở dữ liệu
                $success = $this->bookingModel->addBooking($bookingData);

                if ($success) {
                    if ($isAjaxRequest) {
                        // Nếu là AJAX request, trả về JSON
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => 'Đặt phòng thành công']);
                        exit;
                    } else {
                        // Nếu không phải AJAX request, chuyển hướng
                        $redirectUrl = $this->getRedirectUrl($role);
                        header("Location: $redirectUrl?message=Đặt phòng thành công");
                        exit;
                    }
                } else {
                    return [
                        'error' => 'Không thể đặt phòng, vui lòng thử lại',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'users' => $this->userModel->getAllUsers(),
                        'available_rooms' => []
                    ];
                }
            } else if ($isAjaxRequest && $start_time && $end_time) {
                // Nếu là AJAX request để kiểm tra phòng trống
                $available_rooms = [];

                // Thêm log để debug
                error_log("BookingController::bookRoom (AJAX) - Kiểm tra phòng trống từ $start_time đến $end_time");
                error_log("BookingController::bookRoom (AJAX) - Dữ liệu POST: " . json_encode($_POST));

                // Lấy tất cả phòng
                $rooms = $this->roomModel->getAllRooms();

                // Thêm log để debug
                error_log("BookingController::bookRoom (AJAX) - Kiểm tra tất cả phòng từ $start_time đến $end_time");

                // Kiểm tra từng phòng
                foreach ($rooms as $room) {
                    // Kiểm tra xung đột
                    $isAvailable = !$this->bookingModel->checkBookingConflict($room['id'], $start_time, $end_time);

                    // Nếu phòng trống, thêm vào danh sách phòng khả dụng
                    if ($isAvailable) {
                        $available_rooms[] = $room['id'];
                    }

                    // Ghi log kết quả
                    error_log("BookingController::bookRoom (AJAX) - Phòng {$room['id']} ({$room['name']}) " . ($isAvailable ? "TRỐNG" : "ĐÃ ĐẶT"));
                }

                return [
                    'rooms' => $rooms,
                    'available_rooms' => $available_rooms
                ];
            } else {
                return [
                    'error' => 'Vui lòng điền đầy đủ thông tin',
                    'rooms' => $this->roomModel->getAllRooms(),
                    'users' => $this->userModel->getAllUsers(),
                    'available_rooms' => []
                ];
            }
        }

        // Lấy thông tin phòng nếu có room_id
        $room = null;
        if (isset($data['room_id']) && !empty($data['room_id'])) {
            $room = $this->roomModel->getRoomById($data['room_id']);
        }

        // Lấy danh sách phòng khả dụng (trống) nếu có start_time và end_time
        $start_time = $_POST['start_time'] ?? null;
        $end_time = $_POST['end_time'] ?? null;
        $available_rooms = [];

        if ($start_time && $end_time) {
            $start_time = date('Y-m-d H:i:s', strtotime($start_time));
            $end_time = date('Y-m-d H:i:s', strtotime($end_time));

            // Thêm log để debug
            error_log("BookingController::bookRoom - Kiểm tra phòng trống từ $start_time đến $end_time");

            $rooms = $this->roomModel->getAllRooms();
            foreach ($rooms as $room) {
                $isAvailable = !$this->bookingModel->checkBookingConflict($room['id'], $start_time, $end_time);
                if ($isAvailable) {
                    $available_rooms[] = $room['id'];
                }
                error_log("BookingController::bookRoom - Phòng {$room['id']} ({$room['name']}) " . ($isAvailable ? "TRỐNG" : "ĐÃ ĐẶT"));
            }
        }

        // Trả về dữ liệu cho view
        return [
            'room' => $room,
            'rooms' => $this->roomModel->getAllRooms(),
            'users' => $this->userModel->getAllUsers(),
            'available_rooms' => $available_rooms
        ];
    }

    /**
     * Lấy URL chuyển hướng sau khi đặt phòng thành công
     */
    private function getRedirectUrl($role)
    {
        switch ($role) {
            case 'admin':
                return '/pdu_pms_project/public/admin/manage_bookings';
            case 'teacher':
                return '/pdu_pms_project/public/teacher';
            case 'student':
                return '/pdu_pms_project/public/student';
            default:
                return '/pdu_pms_project/public/';
        }
    }

    /**
     * Lấy danh sách phòng trống trong khoảng thời gian
     */
    public function getAvailableRooms()
    {
        // Kiểm tra người dùng đã đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $start_time = $_POST['start_time'] ?? null;
        $end_time = $_POST['end_time'] ?? null;

        if (!$start_time || !$end_time) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Missing required parameters']);
            exit;
        }

        // Chuyển đổi định dạng thời gian
        $start_time = date('Y-m-d H:i:s', strtotime($start_time));
        $end_time = date('Y-m-d H:i:s', strtotime($end_time));

        // Thêm log để debug
        error_log("BookingController::getAvailableRooms - Kiểm tra phòng trống từ $start_time đến $end_time");

        // Lấy tất cả phòng
        $rooms = $this->roomModel->getAllRooms();
        $available_rooms = [];

        // Kiểm tra từng phòng
        foreach ($rooms as $room) {
            $isAvailable = !$this->bookingModel->checkBookingConflict($room['id'], $start_time, $end_time);
            if ($isAvailable) {
                $available_rooms[] = $room;
            }
            error_log("BookingController::getAvailableRooms - Phòng {$room['id']} ({$room['name']}) " . ($isAvailable ? "TRỐNG" : "ĐÃ ĐẶT"));
        }

        // Trả về kết quả dưới dạng JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'available_rooms' => $available_rooms
        ]);
        exit;
    }
}
