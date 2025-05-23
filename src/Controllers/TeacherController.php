<?php

namespace Controllers;

use Models\BookingModel;
use Models\RoomModel;
use Models\TimetableModel;
use DateTime;

require_once __DIR__ . '/../Helpers/AlertHelper.php';

use \AlertHelper;

class TeacherController
{
    private $bookingModel;
    private $roomModel;
    private $timetableModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->roomModel = new RoomModel();
        $this->timetableModel = new TimetableModel();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        // Lấy dữ liệu đặt phòng của giáo viên
        $teacher_id = $_SESSION['user_id'];
        $bookings = $this->bookingModel->getBookingsByTeacher($teacher_id);

        // Lấy dữ liệu thống kê theo trạng thái
        $status_stats = [
            'waiting' => 0,
            'approved' => 0,
            'rejected' => 0
        ];

        // Lấy dữ liệu thống kê theo ngày trong tuần
        $day_stats = [0, 0, 0, 0, 0, 0, 0]; // [CN, T2, T3, T4, T5, T6, T7]

        // Đếm số lượng đặt phòng hôm nay
        $today_bookings = [];
        $today = date('Y-m-d');

        foreach ($bookings as $booking) {
            // Thống kê theo trạng thái
            if ($booking['status'] === 'chờ duyệt') {
                $status_stats['waiting']++;
            } elseif ($booking['status'] === 'được duyệt') {
                $status_stats['approved']++;
            } elseif ($booking['status'] === 'từ chối') {
                $status_stats['rejected']++;
            }

            // Thống kê theo ngày trong tuần
            $booking_date = date('Y-m-d', strtotime($booking['start_time']));
            $day_of_week = date('w', strtotime($booking_date)); // 0 (Chủ nhật) đến 6 (Thứ 7)
            $day_stats[$day_of_week]++;

            // Đếm số đặt phòng hôm nay
            if ($booking_date === $today) {
                $today_bookings[] = $booking;
            }
        }

        // Lấy tổng số phòng
        $total_rooms = count($this->roomModel->getAllRooms());

        // Lấy số phòng đang trống vào thời điểm hiện tại
        $now = date('Y-m-d H:i:s');
        $available_rooms = 0;
        $rooms = $this->roomModel->getAllRooms();
        foreach ($rooms as $room) {
            if (!$this->bookingModel->checkBookingConflict($room['id'], $now, $now)) {
                $available_rooms++;
            }
        }

        return [
            'bookings' => $bookings,
            'status_stats' => $status_stats,
            'day_stats' => $day_stats,
            'today_bookings' => $today_bookings,
            'total_rooms' => $total_rooms,
            'available_rooms' => $available_rooms
        ];
    }

    public function bookRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id = $_POST['room_id'] ?? null;
            $class_code = $_POST['class_code'] ?? '';
            $start_time = $_POST['start_time'] ?? '';
            $end_time = $_POST['end_time'] ?? '';

            $teacher_id = $_SESSION['user_id'] ?? null;

            if (!$teacher_id) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Bạn cần đăng nhập để đặt phòng']);
                    exit;
                }

                return [
                    'error' => 'Bạn cần đăng nhập để đặt phòng',
                    'rooms' => $this->roomModel->getAllRooms(),
                    'available_rooms' => []
                ];
            }

            if ($room_id && $class_code && $start_time && $end_time) {
                // Chuyển đổi và kiểm tra định dạng thời gian
                $start_timestamp = strtotime($start_time);
                $end_timestamp = strtotime($end_time);
                $now_timestamp = time();

                if (!$start_timestamp || !$end_timestamp) {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'Định dạng thời gian không hợp lệ']);
                        exit;
                    }

                    return [
                        'error' => 'Định dạng thời gian không hợp lệ',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }

                // Kiểm tra thời gian bắt đầu phải trong tương lai
                if ($start_timestamp <= $now_timestamp) {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'Không thể đặt phòng trong quá khứ. Vui lòng chọn thời gian bắt đầu từ hiện tại trở đi.']);
                        exit;
                    }

                    return [
                        'error' => 'Không thể đặt phòng trong quá khứ. Vui lòng chọn thời gian bắt đầu từ hiện tại trở đi.',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }

                // Kiểm tra thời gian kết thúc phải sau thời gian bắt đầu
                if ($end_timestamp <= $start_timestamp) {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'Thời gian kết thúc phải muộn hơn thời gian bắt đầu']);
                        exit;
                    }

                    return [
                        'error' => 'Thời gian kết thúc phải muộn hơn thời gian bắt đầu',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }

                // Kiểm tra thời lượng đặt phòng tối thiểu là 30 phút
                $duration_minutes = ($end_timestamp - $start_timestamp) / 60;
                if ($duration_minutes < 30) {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'Thời lượng đặt phòng tối thiểu là 30 phút']);
                        exit;
                    }

                    return [
                        'error' => 'Thời lượng đặt phòng tối thiểu là 30 phút',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }

                $formatted_start = date('Y-m-d H:i:s', $start_timestamp);
                $formatted_end = date('Y-m-d H:i:s', $end_timestamp);

                // Kiểm tra xung đột lịch đặt phòng
                if ($this->bookingModel->checkBookingConflict($room_id, $formatted_start, $formatted_end)) {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'Phòng đã được đặt trong khoảng thời gian này']);
                        exit;
                    }

                    // Lấy danh sách phòng trống để hiển thị lại
                    $allRooms = $this->roomModel->getAllRooms();
                    $availableRooms = [];
                    $bookedRooms = [];

                    foreach ($allRooms as $room) {
                        if (!$this->bookingModel->checkBookingConflict($room['id'], $formatted_start, $formatted_end)) {
                            $availableRooms[] = $room;
                        } else {
                            $bookedRooms[] = $room;
                        }
                    }

                    return [
                        'error' => 'Phòng đã được đặt trong khoảng thời gian này',
                        'rooms' => $allRooms,
                        'available_rooms' => $availableRooms,
                        'booked_rooms' => $bookedRooms
                    ];
                }

                $bookingData = [
                    'room_id' => $room_id,
                    'teacher_id' => $teacher_id,
                    'student_id' => null,
                    'class_code' => $class_code,
                    'start_time' => $formatted_start,
                    'end_time' => $formatted_end,
                    'status' => 'pending'  // Teacher bookings also need approval
                ];

                if ($this->bookingModel->addBooking($bookingData)) {
                    // Lấy thông tin phòng vừa đặt
                    $room = $this->roomModel->getRoomById($room_id);
                    $roomName = $room ? $room['name'] : 'Phòng không xác định';

                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => 'Đặt phòng thành công',
                            'booking' => [
                                'room_id' => $room_id,
                                'room_name' => $roomName,
                                'class_code' => $class_code,
                                'start_time' => $formatted_start,
                                'end_time' => $formatted_end,
                                'status' => 'được duyệt'
                            ]
                        ]);
                        exit;
                    }

                    return [
                        'success' => 'Đặt phòng thành công',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                } else {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'Đặt phòng thất bại, vui lòng thử lại']);
                        exit;
                    }

                    return [
                        'error' => 'Đặt phòng thất bại, vui lòng thử lại',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'available_rooms' => []
                    ];
                }
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin']);
                    exit;
                }

                return [
                    'error' => 'Vui lòng điền đầy đủ thông tin',
                    'rooms' => $this->roomModel->getAllRooms(),
                    'available_rooms' => []
                ];
            }
        }

        // Nếu là GET request, hiển thị form đặt phòng
        $all_rooms = $this->roomModel->getAllRooms();

        // Kiểm tra nếu đã có start_time và end_time được truyền vào thì lọc phòng trống
        $available_rooms = [];

        if (isset($_POST['start_time']) && isset($_POST['end_time'])) {
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];

            // Định dạng lại thời gian
            $start_timestamp = strtotime($start_time);
            $end_timestamp = strtotime($end_time);

            if ($start_timestamp && $end_timestamp && $start_timestamp < $end_timestamp) {
                $formatted_start = date('Y-m-d H:i:s', $start_timestamp);
                $formatted_end = date('Y-m-d H:i:s', $end_timestamp);

                // Lọc ra phòng trống
                foreach ($all_rooms as $room) {
                    if (!$this->bookingModel->checkBookingConflict($room['id'], $formatted_start, $formatted_end)) {
                        $available_rooms[] = $room['id']; // Chỉ lưu ID phòng trống
                    }
                }
            }
        }

        return [
            'rooms' => $all_rooms,
            'available_rooms' => $available_rooms
        ];
    }

    // Hàm trợ giúp để lấy dữ liệu đặt phòng của giáo viên
    private function getTeacherBookingsData($teacher_id)
    {
        // Lấy tất cả các đặt phòng của giáo viên
        $bookings = $this->bookingModel->getBookingsByTeacher($teacher_id);

        // Thêm thông tin phòng vào dữ liệu đặt phòng
        $bookingsWithRoomInfo = [];
        foreach ($bookings as $booking) {
            $room = $this->roomModel->getRoomById($booking['room_id']);
            if ($room) {
                $booking['room_name'] = $room['name'];
                $bookingsWithRoomInfo[] = $booking;
            }
        }

        // Sắp xếp theo thời gian bắt đầu (gần nhất lên đầu)
        usort($bookingsWithRoomInfo, function ($a, $b) {
            return strtotime($b['start_time']) - strtotime($a['start_time']);
        });

        return $bookingsWithRoomInfo;
    }

    // API endpoint cho danh sách đặt phòng của giáo viên
    public function getTeacherBookings()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Không có quyền truy cập']);
            exit;
        }

        $teacher_id = $_SESSION['user_id'];
        $bookings = $this->getTeacherBookingsData($teacher_id);

        header('Content-Type: application/json');
        echo json_encode(['bookings' => $bookings]);
        exit;
    }

    // API endpoint để lấy tất cả các phòng
    public function getAllRooms()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Không có quyền truy cập']);
            exit;
        }

        $rooms = $this->roomModel->getAllRooms();

        header('Content-Type: application/json');
        echo json_encode(['rooms' => $rooms]);
        exit;
    }

    // Phương thức xem lịch dạy của giáo viên
    public function myTimetables($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            error_log("ERROR: Người dùng không có quyền truy cập hoặc chưa đăng nhập");
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        // Debug
        error_log("DEBUG: myTimetables() được gọi với user_id = {$_SESSION['user_id']}, role = {$_SESSION['role']}");

        $teacher_id = $_SESSION['user_id'];
        $filters = [];

        // Xử lý các tham số lọc
        if (isset($params['subject']) && !empty($params['subject'])) {
            $filters['subject'] = $params['subject'];
        }

        if (isset($params['class_code']) && !empty($params['class_code'])) {
            $filters['class_code'] = $params['class_code'];
        }

        if (isset($params['date_range']) && !empty($params['date_range'])) {
            $date_range = $params['date_range'];
            $now = new DateTime();

            switch ($date_range) {
                case 'today':
                    $filters['start_date'] = $now->format('Y-m-d 00:00:00');
                    $filters['end_date'] = $now->format('Y-m-d 23:59:59');
                    break;

                case 'tomorrow':
                    $tomorrow = (new DateTime())->modify('+1 day');
                    $filters['start_date'] = $tomorrow->format('Y-m-d 00:00:00');
                    $filters['end_date'] = $tomorrow->format('Y-m-d 23:59:59');
                    break;

                case 'this_week':
                    $start_of_week = (new DateTime())->modify('this week monday');
                    $end_of_week = (new DateTime())->modify('this week sunday');
                    $filters['start_date'] = $start_of_week->format('Y-m-d 00:00:00');
                    $filters['end_date'] = $end_of_week->format('Y-m-d 23:59:59');
                    break;

                case 'next_week':
                    $start_of_next_week = (new DateTime())->modify('next week monday');
                    $end_of_next_week = (new DateTime())->modify('next week sunday');
                    $filters['start_date'] = $start_of_next_week->format('Y-m-d 00:00:00');
                    $filters['end_date'] = $end_of_next_week->format('Y-m-d 23:59:59');
                    break;

                case 'this_month':
                    $start_of_month = (new DateTime())->modify('first day of this month');
                    $end_of_month = (new DateTime())->modify('last day of this month');
                    $filters['start_date'] = $start_of_month->format('Y-m-d 00:00:00');
                    $filters['end_date'] = $end_of_month->format('Y-m-d 23:59:59');
                    break;
            }
        }

        try {
            // Lấy danh sách lịch dạy của giáo viên
            $timetables = $this->timetableModel->getTimetablesByTeacher($teacher_id, $filters);

            // Debug
            error_log("DEBUG: Đã lấy được " . count($timetables) . " lịch dạy");

            return [
                'timetables' => $timetables
            ];
        } catch (\Exception $e) {
            error_log("ERROR: Lỗi khi lấy lịch dạy: " . $e->getMessage());
            return [
                'timetables' => [],
                'error' => 'Có lỗi xảy ra khi lấy dữ liệu lịch dạy. Vui lòng thử lại sau.'
            ];
        }
    }

    // Phương thức tìm kiếm phòng
    public function searchRooms($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $roomModel = new \Models\RoomModel();
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

        // Giáo viên chỉ tìm phòng trống để đặt
        $searchParams['status'] = 'trống';

        if (!empty($params['location'])) {
            $searchParams['location'] = trim($params['location']);
        }

        // Thực hiện tìm kiếm
        $rooms = $roomModel->searchRooms($searchParams);

        return [
            'rooms' => $rooms,
            'roomTypes' => $roomModel->getRoomTypes(),
            'searchParams' => $searchParams
        ];
    }

    // Phương thức xem chi tiết đặt phòng
    public function bookingDetail($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $params['id'] ?? 0;
        $teacher_id = $_SESSION['user_id'];

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/teacher/calendar_bookings');
            exit;
        }

        // Kiểm tra xem đặt phòng có thuộc về giáo viên này không
        $booking = $this->bookingModel->getBookingById($id);
        if (!$booking || $booking['user_id'] != $teacher_id) {
            AlertHelper::error("Bạn không có quyền xem chi tiết đặt phòng này");
            header('Location: /pdu_pms_project/public/teacher/calendar_bookings');
            exit;
        }

        // Lấy thông tin phòng
        $room = $this->roomModel->getDetailedRoom($booking['room_id']);

        return [
            'booking' => $booking,
            'room' => $room
        ];
    }

    // Phương thức xem danh sách đặt phòng của giáo viên
    public function myBookings($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $teacher_id = $_SESSION['user_id'];
        $filters = [];

        // Xử lý các tham số lọc
        if (isset($params['status']) && !empty($params['status'])) {
            $filters['status'] = $params['status'];
        }

        if (isset($params['date_from']) && !empty($params['date_from'])) {
            $filters['date_from'] = $params['date_from'] . ' 00:00:00';
        }

        if (isset($params['date_to']) && !empty($params['date_to'])) {
            $filters['date_to'] = $params['date_to'] . ' 23:59:59';
        }

        if (isset($params['room_type']) && !empty($params['room_type'])) {
            $filters['room_type_id'] = $params['room_type'];
        }

        // Lấy danh sách đặt phòng của giáo viên
        $bookings = $this->bookingModel->getBookingsByTeacher($teacher_id, $filters);

        // Lấy danh sách loại phòng
        $room_types = $this->roomModel->getRoomTypes();

        return [
            'bookings' => $bookings,
            'room_types' => $room_types
        ];
    }

    // Phương thức xem chi tiết lịch dạy
    public function timetableDetail($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $params['id'] ?? 0;
        $teacher_id = $_SESSION['user_id'];

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/teacher/my_timetables');
            exit;
        }

        // Kiểm tra xem lịch dạy có thuộc về giáo viên này không
        $timetable = $this->timetableModel->getTimetableById($id);
        if (!$timetable || $timetable['teacher_id'] != $teacher_id) {
            AlertHelper::error("Bạn không có quyền xem chi tiết lịch dạy này");
            header('Location: /pdu_pms_project/public/teacher/my_timetables');
            exit;
        }

        // Lấy thông tin phòng nếu có
        $room = null;
        if (!empty($timetable['room_id'])) {
            $room = $this->roomModel->getDetailedRoom($timetable['room_id']);
        }

        return [
            'timetable' => $timetable,
            'room' => $room
        ];
    }

    // Phương thức xem lịch đặt phòng dạng lịch
    public function calendarBookings()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
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
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Lấy các tham số lọc từ query string
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $room_id = $_GET['room_id'] ?? null;
        $status = $_GET['status'] ?? null;
        $teacher_id = $_SESSION['user_id']; // Chỉ lấy đặt phòng của giáo viên hiện tại

        // Lấy tất cả đặt phòng của giáo viên
        $bookings = $this->bookingModel->getBookingsByTeacher($teacher_id);

        // Lọc theo các tiêu chí - sửa để hiển thị các sự kiện giao với khoảng thời gian đang xem
        if ($start && $end) {
            $bookings = array_filter($bookings, function ($booking) use ($start, $end) {
                // Hiển thị các sự kiện có bất kỳ phần giao nào với khoảng thời gian đang xem
                return !(($booking['end_time'] < $start) || ($booking['start_time'] > $end));
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
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $params['id'] ?? 0;
        $teacher_id = $_SESSION['user_id'];

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/teacher/calendar_bookings');
            exit;
        }

        // Kiểm tra xem đặt phòng có thuộc về giáo viên này không
        $booking = $this->bookingModel->getBookingById($id);
        if (!$booking || $booking['user_id'] != $teacher_id) {
            AlertHelper::error("Bạn không có quyền hủy đặt phòng này");
            header('Location: /pdu_pms_project/public/teacher/calendar_bookings');
            exit;
        }

        // Cập nhật trạng thái đặt phòng thành "đã hủy"
        $success = $this->bookingModel->updateBookingStatus($id, 'cancelled');

        if ($success) {
            AlertHelper::success("Hủy đặt phòng thành công");
        } else {
            AlertHelper::error("Không thể hủy đặt phòng. Vui lòng thử lại sau");
        }

        header('Location: /pdu_pms_project/public/teacher/calendar_bookings');
        exit;
    }

    // Phương thức xem chi tiết phòng
    public function roomDetail($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $roomModel = new \Models\RoomModel();
        $id = $params['id'] ?? 0;

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/teacher/search_rooms');
            exit;
        }

        $room = $roomModel->getDetailedRoom($id);
        if (!$room) {
            AlertHelper::error(AlertHelper::ROOM_NOT_FOUND);
            header('Location: /pdu_pms_project/public/teacher/search_rooms');
            exit;
        }

        // Lấy thông tin về các khung giờ trống của phòng
        $availableSlots = $roomModel->suggestNextAvailableTime($id);

        return [
            'room' => $room,
            'availableSlots' => $availableSlots
        ];
    }

    // Phương thức đề xuất phòng trống theo thời gian và loại
    public function suggestAvailableRooms($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $roomModel = new \Models\RoomModel();
        $start_time = $params['start_time'] ?? '';
        $end_time = $params['end_time'] ?? '';
        $room_type_id = $params['room_type_id'] ?? null;
        $min_capacity = $params['min_capacity'] ?? 0;

        $rooms = [];
        $roomTypes = $roomModel->getRoomTypes();

        if ($start_time && $end_time) {
            // Định dạng thời gian
            $start_time = date('Y-m-d H:i:s', strtotime($start_time));
            $end_time = date('Y-m-d H:i:s', strtotime($end_time));

            // Lấy danh sách phòng trống theo thời gian và loại
            $rooms = $roomModel->getAvailableRoomsByTimeAndType($start_time, $end_time, $room_type_id, $min_capacity);
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

    // Phương thức API trả về danh sách phòng trống dành cho AJAX
    public function getAvailableRooms()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Không có quyền truy cập']);
            exit;
        }

        // Nhận dữ liệu từ POST request
        $start_time = $_POST['start_time'] ?? null;
        $end_time = $_POST['end_time'] ?? null;
        $class_code = $_POST['class_code'] ?? '';

        if (!$start_time || !$end_time) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Thiếu thông tin thời gian']);
            exit;
        }

        // Chuyển đổi và kiểm tra định dạng thời gian
        $start_timestamp = strtotime($start_time);
        $end_timestamp = strtotime($end_time);
        $now_timestamp = time();

        if (!$start_timestamp || !$end_timestamp) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Định dạng thời gian không hợp lệ']);
            exit;
        }

        // Không cần kiểm tra thời gian trong quá khứ khi chỉ kiểm tra phòng trống
        // Kiểm tra thời gian kết thúc phải sau thời gian bắt đầu
        if ($end_timestamp <= $start_timestamp) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Thời gian kết thúc phải muộn hơn thời gian bắt đầu']);
            exit;
        }

        // Kiểm tra thời lượng đặt phòng tối thiểu là 30 phút
        $duration_minutes = ($end_timestamp - $start_timestamp) / 60;
        if ($duration_minutes < 30) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Thời lượng đặt phòng tối thiểu là 30 phút']);
            exit;
        }

        $formatted_start = date('Y-m-d H:i:s', $start_timestamp);
        $formatted_end = date('Y-m-d H:i:s', $end_timestamp);

        // Lấy tất cả các phòng
        $all_rooms = $this->roomModel->getAllRooms();
        $available_rooms = [];
        $booked_rooms = [];

        // Lọc ra các phòng còn trống và đã đặt trong khoảng thời gian đã chọn
        foreach ($all_rooms as $room) {
            if (!$this->bookingModel->checkBookingConflict($room['id'], $formatted_start, $formatted_end)) {
                $available_rooms[] = $room;
            } else {
                $booked_rooms[] = $room;
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'rooms' => $all_rooms,
            'available_rooms' => $available_rooms,
            'booked_rooms' => $booked_rooms
        ]);
        exit;
    }
}
