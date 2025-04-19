<?php

namespace Controllers;

use Models\BookingModel;
use Models\RoomModel;
use Models\UserModel;
use Models\ScheduleModel;
use DateTime;

require_once __DIR__ . '/../Helpers/AlertHelper.php';

use \AlertHelper;

class StudentController
{
    private $bookingModel;
    private $roomModel;
    private $userModel;
    private $scheduleModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->roomModel = new RoomModel();
        $this->userModel = new UserModel();
        $this->scheduleModel = new ScheduleModel();
    }

    private function render($view, $data = [])
    {
        // Kiểm tra tệp view tồn tại
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            extract($data);
            include $viewFile;
        } else {
            die("View {$view} không tồn tại");
        }
    }

    public function index()
    {
        // Kiểm tra session
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        // Lấy dữ liệu student
        $studentId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($studentId);

        // Lấy dữ liệu đặt phòng của học sinh
        $bookings = $this->bookingModel->getBookingsByStudent($studentId, []);

        // Thống kê đặt phòng
        $totalBookings = count($bookings);
        $approvedBookings = 0;
        $pendingBookings = 0;
        $rejectedBookings = 0;
        $upcomingBookings = [];

        $now = new DateTime();

        foreach ($bookings as $booking) {
            // Đếm theo trạng thái
            switch (strtolower($booking['status'])) {
                case 'được duyệt':
                case 'đã duyệt':
                case 'approved':
                    $approvedBookings++;
                    break;
                case 'chờ duyệt':
                case 'pending':
                    $pendingBookings++;
                    break;
                case 'từ chối':
                case 'rejected':
                    $rejectedBookings++;
                    break;
            }

            // Tìm các đặt phòng sắp tới (trạng thái đã duyệt và thời gian bắt đầu > hiện tại)
            $startTime = new DateTime($booking['start_time']);
            $status = strtolower($booking['status']);
            if (($status == 'được duyệt' || $status == 'đã duyệt' || $status == 'approved' || $status == 'chờ duyệt' || $status == 'pending') && $startTime > $now) {
                $upcomingBookings[] = $booking;
            }
        }

        // Sắp xếp lịch đặt phòng sắp tới theo thời gian bắt đầu
        usort($upcomingBookings, function ($a, $b) {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        });

        // Lấy dữ liệu lịch học của lớp
        $classCode = $user['class_code'];
        $schedule = [];
        if ($classCode) {
            $schedule = $this->scheduleModel->getScheduleByClassCode($classCode);
        }

        // Trả về dữ liệu thay vì render view
        return [
            'user' => $user,
            'bookings' => $bookings,
            'total_bookings' => $totalBookings,
            'approved_bookings' => $approvedBookings,
            'pending_bookings' => $pendingBookings,
            'rejected_bookings' => $rejectedBookings,
            'upcoming_bookings' => $upcomingBookings,
            'schedule' => $schedule
        ];
    }

    // Phương thức tìm kiếm phòng cho sinh viên
    public function searchRooms($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $searchParams = [];

        // Xử lý các tham số tìm kiếm
        if (!empty($params['name'])) {
            $searchParams['name'] = trim($params['name']);
        }

        if (!empty($params['room_type_id'])) {
            $searchParams['room_type_id'] = $params['room_type_id'];
        }

        if (!empty($params['min_capacity'])) {
            $searchParams['min_capacity'] = (int)$params['min_capacity'];
        }

        // Sinh viên chỉ tìm kiếm phòng đang hoạt động
        $searchParams['status'] = 'trống';

        if (!empty($params['location'])) {
            $searchParams['location'] = trim($params['location']);
        }

        // Thực hiện tìm kiếm
        $rooms = $this->roomModel->searchRooms($searchParams);

        return [
            'rooms' => $rooms,
            'roomTypes' => $this->roomModel->getRoomTypes(),
            'searchParams' => $searchParams
        ];
    }

    // Phương thức xem chi tiết phòng
    public function roomDetail($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $params['id'] ?? 0;

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/student/search_rooms');
            exit;
        }

        $room = $this->roomModel->getDetailedRoom($id);
        if (!$room) {
            AlertHelper::error(AlertHelper::ROOM_NOT_FOUND);
            header('Location: /pdu_pms_project/public/student/search_rooms');
            exit;
        }

        $scheduledClasses = $this->roomModel->getUpcomingClassesForRoom($id);

        return [
            'room' => $room,
            'scheduledClasses' => $scheduledClasses
        ];
    }

    public function bookRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id = $_POST['room_id'] ?? null;
            $purpose = $_POST['purpose'] ?? '';
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

            $user = $this->userModel->getUserById($student_id);
            if (!$user) {
                return [
                    'error' => 'Không tìm thấy thông tin người dùng',
                    'rooms' => $this->roomModel->getAllRooms(),
                    'available_rooms' => []
                ];
            }

            if ($room_id && $purpose && $start_time && $end_time) {
                // Chuyển đổi và kiểm tra định dạng thời gian
                $start_timestamp = strtotime($start_time);
                $end_timestamp = strtotime($end_time);

                if (!$start_timestamp || !$end_timestamp) {
                    return [
                        'error' => 'Định dạng thời gian không hợp lệ',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }

                // Kiểm tra thời gian kết thúc phải sau thời gian bắt đầu
                if ($end_timestamp <= $start_timestamp) {
                    return [
                        'error' => 'Thời gian kết thúc phải sau thời gian bắt đầu',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }

                $formatted_start = date('Y-m-d H:i:s', $start_timestamp);
                $formatted_end = date('Y-m-d H:i:s', $end_timestamp);

                // Kiểm tra xung đột lịch đặt phòng
                if ($this->bookingModel->checkBookingConflict($room_id, $formatted_start, $formatted_end)) {
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
                    'class_code' => $user['class_code'],
                    'start_time' => $formatted_start,
                    'end_time' => $formatted_end,
                    'status' => 'pending' // Student bookings need approval
                ];

                if ($this->bookingModel->addBooking($bookingData)) {
                    AlertHelper::success(AlertHelper::BOOKING_ADDED);
                    return [
                        'success' => AlertHelper::BOOKING_ADDED,
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                } else {
                    AlertHelper::error(AlertHelper::ACTION_FAILED);
                    return [
                        'error' => AlertHelper::ACTION_FAILED,
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }
            } else {
                AlertHelper::error(AlertHelper::INVALID_INPUT);
                return [
                    'error' => AlertHelper::INVALID_INPUT,
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
            // Kiểm tra định dạng thời gian
            $start_timestamp = strtotime($start_time);
            $end_timestamp = strtotime($end_time);

            if ($start_timestamp && $end_timestamp && $start_timestamp < $end_timestamp) {
                $formatted_start = date('Y-m-d H:i:s', $start_timestamp);
                $formatted_end = date('Y-m-d H:i:s', $end_timestamp);

                // Lọc danh sách phòng trống
                $rooms = $this->roomModel->getAllRooms();
                foreach ($rooms as $room) {
                    if (!$this->bookingModel->checkBookingConflict($room['id'], $formatted_start, $formatted_end)) {
                        $available_rooms[] = $room['id']; // Chỉ lưu ID phòng trống
                    }
                }
            }
        }

        return [
            'rooms' => $this->roomModel->getAllRooms(),
            'available_rooms' => $available_rooms
        ];
    }

    // Phương thức xem lịch đặt phòng dạng lịch
    public function calendarBookings()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        return [
            'rooms' => $this->roomModel->getAllRooms()
        ];
    }

    // API lấy dữ liệu đặt phòng dưới dạng JSON cho FullCalendar
    public function getBookingsJson()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Lấy các tham số lọc từ query string
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $room_id = $_GET['room_id'] ?? null;
        $status = $_GET['status'] ?? null;
        $student_id = $_SESSION['user_id']; // Chỉ lấy đặt phòng của sinh viên hiện tại

        // Lấy tất cả đặt phòng của sinh viên
        $bookings = $this->bookingModel->getBookingsByStudent($student_id, []);

        // Lọc theo các tiêu chí
        if ($start && $end) {
            $bookings = array_filter($bookings, function ($booking) use ($start, $end) {
                return $booking['start_time'] >= $start && $booking['end_time'] <= $end;
            });
        }

        if ($room_id) {
            $bookings = array_filter($bookings, function ($booking) use ($room_id) {
                return $booking['room_id'] == $room_id;
            });
        }

        if ($status) {
            $bookings = array_filter($bookings, function ($booking) use ($status) {
                return $booking['status'] == $status;
            });
        }

        // Trả về dữ liệu dưới dạng JSON
        header('Content-Type: application/json');
        echo json_encode(array_values($bookings));
        exit;
    }

    // Phương thức hủy đặt phòng
    public function cancelBooking($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $params['id'] ?? 0;
        $student_id = $_SESSION['user_id'];

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/student/calendar_bookings');
            exit;
        }

        // Kiểm tra xem đặt phòng có thuộc về sinh viên này không
        $booking = $this->bookingModel->getBookingById($id);
        if (!$booking || $booking['user_id'] != $student_id) {
            AlertHelper::error("Bạn không có quyền hủy đặt phòng này");
            header('Location: /pdu_pms_project/public/student/calendar_bookings');
            exit;
        }

        // Cập nhật trạng thái đặt phòng thành "đã hủy"
        $success = $this->bookingModel->updateBookingStatus($id, 'cancelled');

        if ($success) {
            AlertHelper::success("Hủy đặt phòng thành công");
        } else {
            AlertHelper::error("Không thể hủy đặt phòng. Vui lòng thử lại sau");
        }

        header('Location: /pdu_pms_project/public/student/calendar_bookings');
        exit;
    }

    // Phương thức xem chi tiết đặt phòng
    public function bookingDetail($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $params['id'] ?? 0;
        $student_id = $_SESSION['user_id'];

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/student/my_bookings');
            exit;
        }

        // Kiểm tra xem đặt phòng có thuộc về sinh viên này không
        $booking = $this->bookingModel->getBookingById($id);
        if (!$booking || $booking['user_id'] != $student_id) {
            AlertHelper::error("Bạn không có quyền xem chi tiết đặt phòng này");
            header('Location: /pdu_pms_project/public/student/my_bookings');
            exit;
        }

        // Lấy thông tin phòng
        $room = $this->roomModel->getDetailedRoom($booking['room_id']);

        return [
            'booking' => $booking,
            'room' => $room
        ];
    }

    // Phương thức đề xuất phòng trống theo thời gian và loại
    public function suggestAvailableRooms($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $start_time = $params['start_time'] ?? '';
        $end_time = $params['end_time'] ?? '';
        $room_type_id = $params['room_type_id'] ?? null;
        $min_capacity = $params['min_capacity'] ?? 0;

        $rooms = [];
        $roomTypes = $this->roomModel->getRoomTypes();

        if ($start_time && $end_time) {
            // Định dạng thời gian
            $start_time = date('Y-m-d H:i:s', strtotime($start_time));
            $end_time = date('Y-m-d H:i:s', strtotime($end_time));

            // Lấy danh sách phòng trống theo thời gian và loại
            $rooms = $this->roomModel->getAvailableRoomsByTimeAndType($start_time, $end_time, $room_type_id, $min_capacity);
        }

        return [
            'rooms' => $rooms,
            'roomTypes' => $roomTypes,
            'searchParams' => [
                'start_time' => $start_time,
                'end_time' => $end_time,
                'room_type_id' => $room_type_id,
                'min_capacity' => $min_capacity
            ]
        ];
    }
}
