<?php

namespace Controllers;

use Models\UserModel;
use Models\RoomModel;
use Models\TimetableModel;
use Models\BookingModel;

require_once __DIR__ . '/../Helpers/AlertHelper.php';

use \AlertHelper;

class AdminController
{
    private $userModel;
    private $roomModel;
    private $timetableModel;
    private $bookingModel;
    private $maintenanceRequestModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roomModel = new RoomModel();
        $this->timetableModel = new TimetableModel();
        $this->bookingModel = new BookingModel();
        $this->maintenanceRequestModel = new \Models\MaintenanceRequestModel();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        // Thu thập dữ liệu cho dashboard
        $today_bookings = $this->bookingModel->getTodayBookingsCount();
        $yesterday_bookings = $this->bookingModel->getYesterdayBookingsCount();
        $today_bookings_increase_percent = $yesterday_bookings > 0
            ? round((($today_bookings - $yesterday_bookings) / $yesterday_bookings) * 100, 1)
            : 0;

        $total_users = $this->userModel->getTotalUsers();
        $last_month_users = $this->userModel->getUsersCountLastMonth();
        $total_users_increase_percent = $last_month_users > 0
            ? round((($total_users - $last_month_users) / $last_month_users) * 100, 1)
            : 0;

        $rooms_in_use = $this->bookingModel->getCurrentlyInUseRoomsCount();
        $yesterday_rooms_in_use = $this->bookingModel->getRoomsInUseYesterday();
        $rooms_in_use_change_percent = $yesterday_rooms_in_use > 0
            ? round((($rooms_in_use - $yesterday_rooms_in_use) / $yesterday_rooms_in_use) * 100, 1)
            : 0;

        // Số lượng đặt phòng đang chờ duyệt
        $pending_bookings = $this->bookingModel->getPendingBookingsCount();

        // Số lượng yêu cầu bảo trì
        $pending_maintenance = $this->maintenanceRequestModel->getPendingRequestsCount();
        $total_maintenance = $this->maintenanceRequestModel->getTotalRequestsCount();
        $urgent_maintenance = $this->maintenanceRequestModel->getRequestsCountByStatus('khẩn cấp');
        $completed_maintenance = $this->maintenanceRequestModel->getRequestsCountByStatus('đã xử lý');

        $data = [
            'title' => 'Admin Dashboard',

            // Số yêu cầu đặt phòng chờ duyệt
            'pending_bookings' => $pending_bookings,

            // Thống kê yêu cầu bảo trì
            'maintenance_stats' => [
                'pending' => $pending_maintenance,
                'total' => $total_maintenance,
                'urgent' => $urgent_maintenance,
                'completed' => $completed_maintenance
            ],

            // Thống kê tổng quan với phần trăm thay đổi
            'stats' => [
                'total_rooms' => $this->roomModel->getTotalRooms(),
                'total_users' => $total_users,
                'today_bookings' => $today_bookings,
                'rooms_in_use' => $rooms_in_use,

                // Các phần trăm thay đổi
                'today_bookings_increase_percent' => $today_bookings_increase_percent,
                'total_users_increase_percent' => $total_users_increase_percent,
                'rooms_in_use_change_percent' => $rooms_in_use_change_percent
            ],

            // Thống kê người dùng theo vai trò
            'users_by_role' => [
                'admin' => $this->userModel->getUserCountByRole('admin'),
                'teacher' => $this->userModel->getUserCountByRole('teacher'),
                'student' => $this->userModel->getUserCountByRole('student')
            ],

            // Hoạt động gần đây (đăng ký, đặt phòng, v.v.)
            'recent_activities' => $this->getRecentActivities(),

            // Lịch đặt phòng hôm nay
            'today_schedule' => $this->timetableModel->getTodaySchedule(),

            // Phòng học được sử dụng nhiều nhất
            'most_used_rooms' => $this->roomModel->getMostUsedRooms(5),

            // Dữ liệu thống kê cơ bản khác
            'total_bookings_this_month' => $this->bookingModel->getBookingsThisMonth(),
            'booking_success_rate' => $this->bookingModel->getBookingSuccessRate()
        ];

        return $data;
    }

    /**
     * Lấy các hoạt động gần đây từ nhiều nguồn khác nhau
     * @param int $limit Số lượng hoạt động muốn lấy
     * @return array Mảng các hoạt động gần đây
     */
    private function getRecentActivities($limit = 10)
    {
        $activities = [];

        // Lấy các đăng ký người dùng gần đây
        $recentUsers = $this->userModel->getRecentUsers($limit);
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user_registration',
                'user_id' => $user['id'],
                'user_name' => $user['username'],
                'role' => $user['role'],
                'timestamp' => $user['created_at'] ?? date('Y-m-d H:i:s'),
                'message' => "Người dùng {$user['full_name']} ({$user['role']}) đã được thêm vào hệ thống"
            ];
        }

        // Lấy các đặt phòng gần đây
        $recentBookings = $this->bookingModel->getRecentBookings($limit);
        foreach ($recentBookings as $booking) {
            // Kiểm tra các khóa tồn tại trước khi sử dụng
            $userId = $booking['user_id'] ?? null;
            $roomId = $booking['room_id'] ?? null;

            if ($userId && $roomId) {
                $user = $this->userModel->getUserById($userId);
                $room = $this->roomModel->getRoomById($roomId);

                if ($user && $room) {
                    $activities[] = [
                        'type' => 'booking',
                        'booking_id' => $booking['id'],
                        'user_id' => $userId,
                        'user_name' => $user['username'],
                        'room_name' => $room['name'],
                        'status' => $booking['status'],
                        'timestamp' => $booking['created_at'] ?? date('Y-m-d H:i:s'),
                        'message' => "{$user['username']} đã đặt phòng {$room['name']}"
                    ];
                }
            }
        }

        // Sắp xếp hoạt động theo thời gian (mới nhất trước)
        usort($activities, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        // Giới hạn số lượng kết quả
        return array_slice($activities, 0, $limit);
    }

    public function manageUsers()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        $users = $this->userModel->getAllUsers();
        return ['users' => $users];
    }

    public function addUser($data)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        if (isset($data['add_user'])) {
            $full_name = $data['full_name'] ?? $data['username'];
            $result = $this->userModel->addUser(
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role'],
                $data['class_code'] ?? null,
                $full_name
            );

            if ($result) {
                AlertHelper::success(AlertHelper::USER_ADDED);
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
            }

            header('Location: /pdu_pms_project/public/admin/manage_users');
            exit;
        }
        return [];
    }

    public function editUser($data)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        if (isset($data['edit_user'])) {
            $full_name = $data['full_name'] ?? $data['username'];
            $result = $this->userModel->updateUser($data['id'], $data['username'], $data['email'], $data['role'], $data['class_code'] ?? null, $full_name);

            if ($result) {
                AlertHelper::success(AlertHelper::USER_UPDATED);
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
            }

            header('Location: /pdu_pms_project/public/admin/manage_users');
            exit;
        }
        $user = $this->userModel->getUserById($data['id']);
        return ['user' => $user];
    }

    public function viewUser($data)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $data['id'] ?? null;
        if (!$id) {
            AlertHelper::error(AlertHelper::USER_NOT_FOUND);
            header('Location: /pdu_pms_project/public/admin/manage_users');
            exit;
        }

        // Lấy thông tin người dùng
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            AlertHelper::error(AlertHelper::USER_NOT_FOUND);
            header('Location: /pdu_pms_project/public/admin/manage_users');
            exit;
        }

        // Lấy lịch đặt phòng của người dùng
        $bookings = [];
        if ($user['role'] === 'teacher') {
            $bookings = $this->bookingModel->getBookingsByTeacher($id);
        } elseif ($user['role'] === 'student') {
            $bookings = $this->bookingModel->getBookingsByStudent($id);
        }

        // Lấy hoạt động gần đây của người dùng
        $activities = $this->getUserActivities($id);

        return [
            'user' => $user,
            'bookings' => $bookings,
            'activities' => $activities
        ];
    }

    /**
     * Lấy hoạt động gần đây của người dùng
     * @param int $userId ID của người dùng
     * @param int $limit Số lượng hoạt động muốn lấy
     * @return array Mảng các hoạt động gần đây
     */
    private function getUserActivities($userId, $limit = 10)
    {
        // Trong thực tế, sẽ truy vấn hoạt động từ cơ sở dữ liệu
        // Đây là dữ liệu mẫu
        return [
            ['action' => 'Đăng nhập vào hệ thống', 'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['action' => 'Đặt phòng học', 'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['action' => 'Cập nhật thông tin cá nhân', 'timestamp' => date('Y-m-d H:i:s', strtotime('-3 days'))],
            ['action' => 'Xem lịch học', 'timestamp' => date('Y-m-d H:i:s', strtotime('-5 days'))]
        ];
    }

    public function deleteUser($data)
    {
        $id = $data['id'] ?? null;
        if ($id) {
            $success = $this->userModel->deleteUser($id);
            if ($success) {
                AlertHelper::success(AlertHelper::USER_DELETED);
                header('Location: /pdu_pms_project/public/admin/manage_users');
                exit;
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
                header('Location: /pdu_pms_project/public/admin/manage_users');
                exit;
            }
        }
        AlertHelper::error(AlertHelper::USER_NOT_FOUND);
        header('Location: /pdu_pms_project/public/admin/manage_users');
        exit;
    }

    // Quản lý phòng
    public function manageRooms()
    {
        $rooms = $this->roomModel->getAllRooms();

        // Kiểm tra nếu không có phòng nào, thêm thông báo lỗi
        if (empty($rooms)) {
            AlertHelper::error(AlertHelper::ROOM_NOT_FOUND);
        }

        // Đặt tên loại phòng mặc định
        foreach ($rooms as &$room) {
            $room['room_type_name'] = 'Phòng thực hành';
        }

        return [
            'rooms' => $rooms
        ];
    }

    public function addRoom($data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $data['name'] ?? '';
            $capacity = $data['capacity'] ?? 0;

            if ($name && $capacity) {
                $success = $this->roomModel->addRoom($name, $capacity);
                if ($success) {
                    AlertHelper::success(AlertHelper::ROOM_ADDED);
                    header('Location: /pdu_pms_project/public/admin/manage_rooms');
                    exit;
                } else {
                    return ['error' => AlertHelper::ACTION_FAILED];
                }
            } else {
                return ['error' => AlertHelper::INVALID_INPUT];
            }
        }
        return [];
    }

    public function editRoom($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/admin/manage_rooms');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $data['name'] ?? '';
            $capacity = $data['capacity'] ?? 0;
            $status = $data['status'] ?? 'trống';

            if ($name && $capacity) {
                $success = $this->roomModel->updateRoom($id, $name, $capacity, $status);
                if ($success) {
                    AlertHelper::success(AlertHelper::ROOM_UPDATED);
                    header('Location: /pdu_pms_project/public/admin/manage_rooms');
                    exit;
                } else {
                    return ['error' => AlertHelper::ACTION_FAILED, 'room' => $this->roomModel->getRoomById($id)];
                }
            } else {
                return ['error' => AlertHelper::INVALID_INPUT, 'room' => $this->roomModel->getRoomById($id)];
            }
        }

        return [
            'room' => $this->roomModel->getRoomById($id)
        ];
    }

    public function deleteRoom($data)
    {
        $id = $data['id'] ?? null;
        if ($id) {
            $success = $this->roomModel->deleteRoom($id);
            if ($success) {
                AlertHelper::success(AlertHelper::ROOM_DELETED);
                header('Location: /pdu_pms_project/public/admin/manage_rooms');
                exit;
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
                header('Location: /pdu_pms_project/public/admin/manage_rooms');
                exit;
            }
        }
        AlertHelper::error(AlertHelper::INVALID_INPUT);
        header('Location: /pdu_pms_project/public/admin/manage_rooms');
        exit;
    }

    /**
     * Xóa loại phòng
     * @param array $data Dữ liệu từ request
     */
    public function deleteRoomType($data)
    {
        $id = $data['id'] ?? null;
        if ($id) {
            // Kiểm tra xem có phòng nào đang sử dụng loại phòng này không
            $roomsUsingType = $this->roomModel->getRoomsByType($id);
            if (!empty($roomsUsingType)) {
                AlertHelper::error("Không thể xóa loại phòng này vì có " . count($roomsUsingType) . " phòng đang sử dụng.");
                header('Location: /pdu_pms_project/public/admin/manage_room_types');
                exit;
            }

            $success = $this->roomModel->deleteRoomType($id);
            if ($success) {
                AlertHelper::success("Xóa loại phòng thành công");
                header('Location: /pdu_pms_project/public/admin/manage_room_types');
                exit;
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
                header('Location: /pdu_pms_project/public/admin/manage_room_types');
                exit;
            }
        }
        AlertHelper::error(AlertHelper::INVALID_INPUT);
        header('Location: /pdu_pms_project/public/admin/manage_room_types');
        exit;
    }

    // Quản lý lịch dạy
    public function manageTimetable()
    {
        return ['timetables' => $this->timetableModel->getAllTimetables()];
    }

    public function addTimetable($data)
    {
        // Đảm bảo AlertHelper được include
        if (!class_exists('AlertHelper')) {
            require_once dirname(__DIR__) . '/Helpers/AlertHelper.php';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teacher_id = $data['teacher_id'] ?? null;
            $class_code = $data['class_code'] ?? '';
            $subject = $data['subject'] ?? '';
            $start_time = $data['start_time'] ?? '';
            $end_time = $data['end_time'] ?? '';
            $participants = $data['participants'] ?? 0;
            $room_id = $data['room_id'] ?? null;
            $notes = $data['notes'] ?? '';

            if ($teacher_id && $class_code && $subject && $start_time && $end_time) {
                // Kiểm tra xung đột nếu có room_id
                if ($room_id) {
                    $conflict = $this->timetableModel->checkRoomAvailability(
                        $room_id,
                        $start_time,
                        $end_time
                    );

                    if (!$conflict) {
                        return [
                            'error' => 'Phòng đã được đặt trong khoảng thời gian này',
                            'users' => $this->userModel->getAllUsers(),
                            'rooms' => $this->roomModel->getAllRooms()
                        ];
                    }
                }

                $success = $this->timetableModel->addTimetable($teacher_id, $class_code, $subject, $start_time, $end_time, $participants, $room_id);
                if ($success) {
                    AlertHelper::success('Thêm lịch dạy thành công');
                    header('Location: /pdu_pms_project/public/admin/manage_timetable');
                    exit;
                } else {
                    return ['error' => 'Không thể thêm lịch dạy, vui lòng thử lại'];
                }
            } else {
                return ['error' => 'Vui lòng điền đầy đủ thông tin bắt buộc'];
            }
        }
        return [
            'users' => $this->userModel->getAllUsers(),
            'rooms' => $this->roomModel->getAllRooms()
        ];
    }

    public function editTimetable($data)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $data['id'] ?? null;
        if (!$id) {
            header('Location: /pdu_pms_project/public/admin/manage_timetable?error=Invalid timetable ID');
            exit;
        }

        // Lấy thông tin lịch hiện tại
        $timetable = $this->timetableModel->getTimetableById($id);
        if (!$timetable) {
            header('Location: /pdu_pms_project/public/admin/manage_timetable?error=Timetable not found');
            exit;
        }

        // Lấy danh sách phòng và giáo viên để hiển thị trong form
        $rooms = $this->roomModel->getAllRooms();
        $teachers = $this->userModel->getAllUsers();

        // Tạo danh sách phòng trống trong khung giờ này
        $availableRooms = [];
        foreach ($rooms as $room) {
            if ($this->timetableModel->checkRoomAvailability(
                $room['id'],
                $timetable['start_time'],
                $timetable['end_time'],
                $id
            )) {
                $availableRooms[] = $room['id'];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['update_timetable'])) {
            $teacher_id = $data['teacher_id'] ?? $timetable['teacher_id'];
            $room_id = $data['room_id'] ?? $timetable['room_id'];
            $subject = $data['subject'] ?? $timetable['subject'];
            $class_code = $data['class_code'] ?? $timetable['class_code'];
            $start_time = $data['start_time'] ?? $timetable['start_time'];
            $end_time = $data['end_time'] ?? $timetable['end_time'];
            $participants = (int)($data['participants'] ?? $timetable['participants']);

            $errors = [];

            // Kiểm tra xem phòng đã được đặt trong khung giờ này chưa
            if ($room_id != $timetable['room_id'] || $start_time != $timetable['start_time'] || $end_time != $timetable['end_time']) {
                // Nếu đổi phòng hoặc thời gian, kiểm tra xem phòng có trống không
                $isRoomAvailable = $this->timetableModel->checkRoomAvailability(
                    $room_id,
                    $start_time,
                    $end_time,
                    $id // Loại trừ lịch hiện tại
                );

                if (!$isRoomAvailable) {
                    $errors[] = "Phòng đã được đặt trong khoảng thời gian này. Vui lòng chọn phòng khác hoặc thay đổi thời gian.";
                }
            }

            // Kiểm tra số máy của phòng
            if ($room_id) {
                $room = $this->roomModel->getRoomById($room_id);
                if ($room && $participants > $room['capacity']) {
                    $errors[] = "Phòng {$room['name']} chỉ có số máy {$room['capacity']} người, không đủ cho {$participants} sinh viên.";
                }
            }

            // Tái tạo danh sách phòng trống dựa trên thời gian mới (nếu thay đổi)
            if ($start_time != $timetable['start_time'] || $end_time != $timetable['end_time']) {
                $availableRooms = [];
                foreach ($rooms as $room) {
                    if ($this->timetableModel->checkRoomAvailability($room['id'], $start_time, $end_time, $id)) {
                        $availableRooms[] = $room['id'];
                    }
                }
            }

            if (empty($errors)) {
                $success = $this->timetableModel->updateTimetable(
                    $id,
                    $teacher_id,
                    $room_id,
                    $subject,
                    $start_time,
                    $end_time,
                    $participants,
                    $class_code
                );

                if ($success) {
                    AlertHelper::success("Lịch dạy đã được cập nhật thành công");
                    header('Location: /pdu_pms_project/public/admin/manage_timetable');
                    exit;
                } else {
                    $errors[] = "Không thể cập nhật lịch. Vui lòng thử lại.";
                }
            }

            // Nếu có lỗi, hiển thị lại form với thông báo lỗi
            return [
                'timetable' => $timetable,
                'rooms' => $rooms,
                'teachers' => $teachers,
                'available_rooms' => $availableRooms,
                'errors' => $errors
            ];
        }

        return [
            'timetable' => $timetable,
            'rooms' => $rooms,
            'teachers' => $teachers,
            'available_rooms' => $availableRooms
        ];
    }

    public function deleteTimetable($data)
    {
        $id = $data['id'] ?? null;
        if ($id) {
            $success = $this->timetableModel->deleteTimetable($id);
            if ($success) {
                AlertHelper::success("Xóa lịch dạy thành công");
                header('Location: /pdu_pms_project/public/admin/manage_timetable');
                exit;
            } else {
                AlertHelper::error("Không thể xóa lịch dạy");
                header('Location: /pdu_pms_project/public/admin/manage_timetable');
                exit;
            }
        }
        AlertHelper::error("ID lịch dạy không hợp lệ");
        header('Location: /pdu_pms_project/public/admin/manage_timetable');
        exit;
    }

    // Quản lý đặt phòng
    public function manageBookings()
    {
        $filters = [];

        // Lấy các tham số lọc nếu có
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
        }

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }

        if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
            $filters['start_date'] = $_GET['start_date'];
        }

        if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
            $filters['end_date'] = $_GET['end_date'];
        }

        // Trả về dữ liệu cho view, bao gồm danh sách phòng và người dùng
        return [
            'bookings' => $this->bookingModel->getAllBookings(),
            'rooms' => $this->roomModel->getAllRooms(),
            'users' => $this->userModel->getAllUsers(),
            'filters' => $filters
        ];
    }

    // Hiển thị lịch đặt phòng dạng calendar
    public function calendarBookings()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        return [
            'rooms' => $this->roomModel->getAllRooms(),
            'users' => $this->userModel->getAllUsers()
        ];
    }

    // Phương thức duyệt đặt phòng
    public function approveBooking($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $params['id'] ?? 0;

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/admin/calendar_bookings');
            exit;
        }

        // Cập nhật trạng thái đặt phòng thành "được duyệt"
        $success = $this->bookingModel->updateBookingStatus($id, 'approved');

        if ($success) {
            AlertHelper::success("Duyệt đặt phòng thành công");
        } else {
            AlertHelper::error("Không thể duyệt đặt phòng. Vui lòng thử lại sau");
        }

        header('Location: /pdu_pms_project/public/admin/calendar_bookings');
        exit;
    }

    // Phương thức từ chối đặt phòng
    public function rejectBooking($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $params['id'] ?? 0;

        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/admin/calendar_bookings');
            exit;
        }

        // Cập nhật trạng thái đặt phòng thành "từ chối"
        $success = $this->bookingModel->updateBookingStatus($id, 'rejected');

        if ($success) {
            AlertHelper::success("Từ chối đặt phòng thành công");
        } else {
            AlertHelper::error("Không thể từ chối đặt phòng. Vui lòng thử lại sau");
        }

        header('Location: /pdu_pms_project/public/admin/calendar_bookings');
        exit;
    }

    // API lấy dữ liệu đặt phòng dưới dạng JSON cho FullCalendar
    public function getBookingsJson()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Lấy các tham số lọc từ query string
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $room_id = $_GET['room_id'] ?? null;
        $user_id = $_GET['user_id'] ?? null;
        $status = $_GET['status'] ?? null;

        // Lấy tất cả đặt phòng
        $bookings = $this->bookingModel->getAllBookings();

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

        if ($user_id) {
            $bookings = array_filter($bookings, function ($booking) use ($user_id) {
                return $booking['user_id'] == $user_id;
            });
        }

        if ($status) {
            $bookings = array_filter($bookings, function ($booking) use ($status) {
                return $booking['status'] == $status;
            });
        }

        // Thêm thông tin người dùng và phòng cho mỗi booking
        $enhancedBookings = [];
        foreach ($bookings as $booking) {
            // Lấy thông tin người dùng
            if (isset($booking['user_id']) && $booking['user_id']) {
                $user = $this->userModel->getUserById($booking['user_id']);
                if ($user) {
                    $booking['user_name'] = $user['full_name'] ? $user['full_name'] : $user['username'];
                    $booking['user_role'] = $user['role'];
                }
            }

            // Lấy thông tin phòng nếu chưa có
            if (isset($booking['room_id']) && $booking['room_id'] && !isset($booking['room_name'])) {
                $room = $this->roomModel->getRoomById($booking['room_id']);
                if ($room) {
                    $booking['room_name'] = $room['name'];
                }
            }

            $enhancedBookings[] = $booking;
        }

        // Trả về dữ liệu dưới dạng JSON
        header('Content-Type: application/json');
        echo json_encode($enhancedBookings);
        exit;
    }

    public function addBooking($data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id = $data['room_id'] ?? null;
            $user_type = $data['user_type'] ?? 'teacher';
            $teacher_id = $user_type === 'teacher' ? ($data['teacher_id'] ?? null) : null;
            $student_id = $user_type === 'student' ? ($data['student_id'] ?? null) : null;
            $class_code = $data['class_code'] ?? '';
            $start_time = $data['start_time'] ?? '';
            $end_time = $data['end_time'] ?? '';
            $purpose = $data['purpose'] ?? '';
            $status = $data['status'] ?? 'pending';

            // Kiểm tra xem có phải là AJAX request không
            $isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

            // Nếu chỉ có start_time và end_time và là AJAX request, thì chỉ cập nhật danh sách phòng trống
            if ($isAjaxRequest && $start_time && $end_time && !$room_id) {
                // Lấy danh sách phòng khả dụng (trống) nếu có start_time và end_time
                $start_time = date('Y-m-d H:i:s', strtotime($start_time));
                $end_time = date('Y-m-d H:i:s', strtotime($end_time));
                $available_rooms = [];
                $rooms = $this->roomModel->getAllRooms();
                foreach ($rooms as $room) {
                    if (!$this->bookingModel->checkBookingConflict($room['id'], $start_time, $end_time)) {
                        $available_rooms[] = $room['id'];
                    }
                }

                return [
                    'rooms' => $this->roomModel->getAllRooms(),
                    'users' => $this->userModel->getAllUsers(),
                    'available_rooms' => $available_rooms
                ];
            }

            if ($room_id && $class_code && $start_time && $end_time && $purpose) {
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

                // Kiểm tra xung đột lịch đặt phòng
                $start_time = date('Y-m-d H:i:s', strtotime($start_time));
                $end_time = date('Y-m-d H:i:s', strtotime($end_time));

                $conflict = $this->bookingModel->checkBookingConflict($room_id, $start_time, $end_time);
                if ($conflict) {
                    return [
                        'error' => 'Phòng đã được đặt trong khoảng thời gian này',
                        'rooms' => $this->roomModel->getAllRooms(),
                        'users' => $this->userModel->getAllUsers(),
                        'available_rooms' => []
                    ];
                }

                $bookingData = [
                    'room_id' => $room_id,
                    'class_code' => $class_code,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'purpose' => $purpose,
                    'status' => $status
                ];

                // Thêm user_id tùy theo loại người dùng
                if ($user_type === 'teacher') {
                    $bookingData['user_id'] = $teacher_id;
                } else {
                    $bookingData['user_id'] = $student_id;
                }
                $success = $this->bookingModel->addBooking($bookingData);

                if ($success) {
                    if ($isAjaxRequest) {
                        // Nếu là AJAX request, trả về JSON
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => 'Đặt phòng thành công']);
                        exit;
                    } else {
                        // Nếu không phải AJAX request, chuyển hướng
                        header('Location: /pdu_pms_project/public/admin/manage_bookings?message=Đặt phòng thành công');
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
            } else {
                return [
                    'error' => 'Vui lòng điền đầy đủ thông tin',
                    'rooms' => $this->roomModel->getAllRooms(),
                    'users' => $this->userModel->getAllUsers(),
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
            'users' => $this->userModel->getAllUsers(),
            'available_rooms' => $available_rooms
        ];
    }

    public function editBooking($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) {
            header('Location: /pdu_pms_project/public/admin/manage_bookings?error=Invalid booking ID');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id = $data['room_id'] ?? null;
            $user_type = $data['user_type'] ?? 'teacher';
            $teacher_id = $user_type === 'teacher' ? ($data['teacher_id'] ?? null) : null;
            $student_id = $user_type === 'student' ? ($data['student_id'] ?? null) : null;
            $class_code = $data['class_code'] ?? '';
            $start_time = $data['start_time'] ?? '';
            $end_time = $data['end_time'] ?? '';
            $purpose = $data['purpose'] ?? '';
            $status = $data['status'] ?? 'pending';

            if ($room_id && $class_code && $start_time && $end_time && $purpose) {
                if ($user_type === 'teacher' && !$teacher_id) {
                    return [
                        'error' => 'Vui lòng chọn giảng viên',
                        'booking' => $this->bookingModel->getBookingById($id),
                        'rooms' => $this->roomModel->getAllRooms(),
                        'users' => $this->userModel->getAllUsers(),
                        'available_rooms' => []
                    ];
                }
                if ($user_type === 'student' && !$student_id) {
                    return [
                        'error' => 'Vui lòng chọn sinh viên',
                        'booking' => $this->bookingModel->getBookingById($id),
                        'rooms' => $this->roomModel->getAllRooms(),
                        'users' => $this->userModel->getAllUsers(),
                        'available_rooms' => []
                    ];
                }

                $conflict = $this->bookingModel->checkBookingConflict($room_id, $start_time, $end_time, $id);
                if ($conflict) {
                    return [
                        'error' => 'Phòng đã được đặt trong khoảng thời gian này',
                        'booking' => $this->bookingModel->getBookingById($id),
                        'rooms' => $this->roomModel->getAllRooms(),
                        'users' => $this->userModel->getAllUsers(),
                        'available_rooms' => []
                    ];
                }

                $bookingData = [
                    'room_id' => $room_id,
                    'class_code' => $class_code,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'purpose' => $purpose,
                    'status' => $status
                ];

                // Thêm user_id tùy theo loại người dùng
                if ($user_type === 'teacher') {
                    $bookingData['user_id'] = $teacher_id;
                } else {
                    $bookingData['user_id'] = $student_id;
                }
                $success = $this->bookingModel->updateBooking($id, $bookingData);
                if ($success) {
                    header('Location: /pdu_pms_project/public/admin/manage_bookings?message=Đặt phòng đã được cập nhật thành công');
                    exit;
                } else {
                    return [
                        'error' => 'Không thể cập nhật đặt phòng, vui lòng thử lại',
                        'booking' => $this->bookingModel->getBookingById($id),
                        'rooms' => $this->roomModel->getAllRooms(),
                        'users' => $this->userModel->getAllUsers(),
                        'available_rooms' => []
                    ];
                }
            } else {
                return [
                    'error' => 'Vui lòng điền đầy đủ thông tin',
                    'booking' => $this->bookingModel->getBookingById($id),
                    'rooms' => $this->roomModel->getAllRooms(),
                    'users' => $this->userModel->getAllUsers(),
                    'available_rooms' => []
                ];
            }
        }

        // Lấy danh sách phòng khả dụng (trống) nếu có start_time và end_time
        $booking = $this->bookingModel->getBookingById($id);
        $start_time = $_POST['start_time'] ?? $booking['start_time'];
        $end_time = $_POST['end_time'] ?? $booking['end_time'];
        $available_rooms = [];
        if ($start_time && $end_time) {
            $start_time = date('Y-m-d H:i:s', strtotime($start_time));
            $end_time = date('Y-m-d H:i:s', strtotime($end_time));
            $rooms = $this->roomModel->getAllRooms();
            foreach ($rooms as $room) {
                if (!$this->bookingModel->checkBookingConflict($room['id'], $start_time, $end_time, $id)) {
                    $available_rooms[] = $room['id'];
                }
            }
        }

        return [
            'booking' => $booking,
            'rooms' => $this->roomModel->getAllRooms(),
            'users' => $this->userModel->getAllUsers(),
            'available_rooms' => $available_rooms
        ];
    }

    public function deleteBooking($data)
    {
        $id = $data['id'] ?? null;
        if ($id) {
            $success = $this->bookingModel->deleteBooking($id);

            // Kiểm tra nếu là AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                if ($success) {
                    echo json_encode(['success' => true, 'message' => "Xóa đặt phòng thành công"]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Không thể xóa đặt phòng"]);
                }
                exit;
            } else {
                // Sử dụng AlertHelper để hiển thị thông báo thành công
                require_once dirname(__DIR__) . '/Helpers/AlertHelper.php';
                if ($success) {
                    AlertHelper::success("Xóa đặt phòng thành công");
                } else {
                    AlertHelper::error("Không thể xóa đặt phòng");
                }
                header('Location: /pdu_pms_project/public/admin/manage_bookings');
                exit;
            }
        }

        // Kiểm tra nếu là AJAX request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => "ID đặt phòng không hợp lệ"]);
            exit;
        } else {
            require_once dirname(__DIR__) . '/Helpers/AlertHelper.php';
            AlertHelper::error("ID đặt phòng không hợp lệ");
            header('Location: /pdu_pms_project/public/admin/manage_bookings');
            exit;
        }
    }

    public function autoSchedule()
    {
        $roomController = new \Controllers\RoomController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $timetable_id = $_POST['timetable_id'] ?? null;

            if (!$timetable_id) {
                AlertHelper::error("ID lịch dạy không hợp lệ");
                header('Location: /pdu_pms_project/public/admin/manage_timetable');
                exit;
            }

            $result = $roomController->autoScheduleRoom($timetable_id);

            if ($result['success']) {
                AlertHelper::success("Xếp phòng thành công: " . $result['room']['name']);
                header('Location: /pdu_pms_project/public/admin/manage_timetable');
                exit;
            } else {
                AlertHelper::error($result['message']);
                header('Location: /pdu_pms_project/public/admin/manage_timetable');
                exit;
            }
        }

        // Nếu không phải POST, trả về trang auto_schedule
        return ['timetables' => $this->timetableModel->getAllTimetables()];
    }

    public function cancelRoomSchedule()
    {
        $roomController = new \Controllers\RoomController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $timetable_id = $_POST['timetable_id'] ?? null;

            if (!$timetable_id) {
                // Kiểm tra nếu là AJAX request
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => "ID lịch dạy không hợp lệ"]);
                    exit;
                } else {
                    AlertHelper::error("ID lịch dạy không hợp lệ");
                    header('Location: /pdu_pms_project/public/admin/manage_timetable');
                    exit;
                }
            }

            $result = $roomController->cancelRoomSchedule($timetable_id);

            // Kiểm tra nếu là AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($result);
                exit;
            } else {
                if ($result['success']) {
                    AlertHelper::success($result['message']);
                } else {
                    AlertHelper::error($result['message']);
                }
                header('Location: /pdu_pms_project/public/admin/manage_timetable');
                exit;
            }
        }

        // Nếu không phải POST, trả về trang manage_timetable
        return ['timetables' => $this->timetableModel->getAllTimetables()];
    }





    // Tìm kiếm phòng với các tiêu chí nâng cao
    public function searchRooms($params = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

        if (!empty($params['max_capacity'])) {
            $searchParams['max_capacity'] = (int)$params['max_capacity'];
        }

        if (!empty($params['status'])) {
            $searchParams['status'] = $params['status'];
        }

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

    public function profile()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        return ['user' => $user];
    }

    public function settings()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        // Tải cài đặt hệ thống từ cơ sở dữ liệu (nếu cần)
        return [
            'settings' => [
                'system_name' => 'PDU PMS',
                'email_notifications' => true,
                'maintenance_reminders' => true,
                'booking_approval_required' => false,
                'max_booking_days_in_advance' => 30
            ]
        ];
    }

    public function notifications()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        // Trong thực tế, sẽ truy vấn thông báo từ cơ sở dữ liệu
        return [
            'notifications' => [
                ['id' => 1, 'title' => 'Yêu cầu bảo trì mới', 'message' => 'Có một yêu cầu bảo trì mới cho phòng A101', 'date' => date('Y-m-d H:i:s', strtotime('-1 hour')), 'read' => false],
                ['id' => 2, 'title' => 'Đặt phòng mới', 'message' => 'Giảng viên Nguyễn Văn A đã đặt phòng B203', 'date' => date('Y-m-d H:i:s', strtotime('-3 hour')), 'read' => true],
                ['id' => 3, 'title' => 'Cảnh báo hệ thống', 'message' => 'Sắp đến thời gian bảo trì định kỳ cho máy chiếu PJ-201', 'date' => date('Y-m-d H:i:s', strtotime('-1 day')), 'read' => false]
            ]
        ];
    }

    public function activityLog()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        // Trong thực tế, sẽ truy vấn nhật ký hoạt động từ cơ sở dữ liệu
        return [
            'activities' => [
                ['id' => 1, 'user' => 'Admin', 'action' => 'Đăng nhập vào hệ thống', 'date' => date('Y-m-d H:i:s', strtotime('-10 minutes'))],
                ['id' => 2, 'user' => 'Admin', 'action' => 'Thêm người dùng mới', 'date' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
                ['id' => 3, 'user' => 'Admin', 'action' => 'Cập nhật thông tin phòng A101', 'date' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
                ['id' => 4, 'user' => 'Admin', 'action' => 'Xem thống kê hệ thống', 'date' => date('Y-m-d H:i:s', strtotime('-1 day'))]
            ]
        ];
    }















    public function systemLogs()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        // Trong thực tế, sẽ truy vấn log từ cơ sở dữ liệu hoặc file hệ thống
        return [
            'logs' => [
                ['id' => 1, 'level' => 'INFO', 'message' => 'Hệ thống khởi động', 'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['id' => 2, 'level' => 'WARNING', 'message' => 'Nhiều lần đăng nhập thất bại từ IP 192.168.1.10', 'timestamp' => date('Y-m-d H:i:s', strtotime('-12 hours'))],
                ['id' => 3, 'level' => 'ERROR', 'message' => 'Không thể kết nối đến cơ sở dữ liệu', 'timestamp' => date('Y-m-d H:i:s', strtotime('-6 hours'))],
                ['id' => 4, 'level' => 'INFO', 'message' => 'Sao lưu dữ liệu tự động thành công', 'timestamp' => date('Y-m-d H:i:s', strtotime('-4 hours'))]
            ],
            'filters' => [
                'levels' => ['INFO', 'WARNING', 'ERROR', 'CRITICAL'],
                'dateRange' => [
                    'start' => date('Y-m-d', strtotime('-7 days')),
                    'end' => date('Y-m-d')
                ]
            ]
        ];
    }

    /**
     * Create sample bookings for testing
     */
    public function createSampleBookings()
    {
        // Get all room ids
        $rooms = $this->roomModel->getAllRooms();
        $roomIds = array_column($rooms, 'id');

        // Get teacher and student ids
        $teachers = $this->userModel->getUsersByRole('teacher');
        $students = $this->userModel->getUsersByRole('student');

        $teacherIds = array_column($teachers, 'id');
        $studentIds = array_column($students, 'id');

        // Class codes
        $classCodes = ['CNTT01', 'CNTT02', 'CNTT03', 'KTPM01', 'KTPM02'];

        // Create 10 bookings
        $bookingsCreated = 0;

        for ($i = 0; $i < 10; $i++) {
            $roomId = $roomIds[array_rand($roomIds)];

            // Alternate between teacher and student bookings
            $teacherId = ($i % 2 == 0) ? $teacherIds[array_rand($teacherIds)] : null;
            $studentId = ($i % 2 == 1) ? $studentIds[array_rand($studentIds)] : null;

            $classCode = $classCodes[array_rand($classCodes)];

            $startDate = date('Y-m-d H:i:s', strtotime('+' . $i . ' day'));
            $endDate = date('Y-m-d H:i:s', strtotime('+' . $i . ' day +2 hours'));

            $bookingData = [
                'room_id' => $roomId,
                'teacher_id' => $teacherId,
                'student_id' => $studentId,
                'class_code' => $classCode,
                'start_time' => $startDate,
                'end_time' => $endDate,
                'status' => 'chờ duyệt'
            ];

            if ($this->bookingModel->addBooking($bookingData)) {
                $bookingsCreated++;
            }
        }

        header('Location: /pdu_pms_project/public/admin/manage_bookings?message=Created ' . $bookingsCreated . ' sample bookings');
        exit;
    }

    public function getUsersByRole($data = [])
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $role = isset($_GET['role']) ? $_GET['role'] : null;

        if (!$role) {
            echo json_encode(['error' => 'Role parameter is required']);
            return;
        }

        $users = $this->userModel->getUsersByRole($role);
        echo json_encode(['users' => $users]);
        exit;
    }

    public function assignRoom($data)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pdu_pms_project/public/admin/manage_bookings');
            exit;
        }

        $room_id = $data['room_id'] ?? null;
        $user_type = $data['user_type'] ?? null;
        $user_id = $data['user_id'] ?? null;
        $class_code = $data['class_code'] ?? '';
        $start_time = $data['start_time'] ?? '';
        $end_time = $data['end_time'] ?? '';
        $purpose = $data['purpose'] ?? '';
        $status = $data['status'] ?? 'pending';

        if (!$room_id || !$user_type || !$user_id || !$start_time || !$end_time) {
            header('Location: /pdu_pms_project/public/admin/manage_bookings?error=missing_fields');
            exit;
        }

        // Kiểm tra xung đột lịch
        $conflict = $this->bookingModel->checkBookingConflict($room_id, $start_time, $end_time);
        if ($conflict) {
            header('Location: /pdu_pms_project/public/admin/manage_bookings?error=conflict');
            exit;
        }

        $bookingData = [
            'room_id' => $room_id,
            'user_id' => $user_id,
            'class_code' => $class_code,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'purpose' => $purpose,
            'status' => $status
        ];

        $success = $this->bookingModel->addBooking($bookingData);

        if ($success) {
            header('Location: /pdu_pms_project/public/admin/manage_bookings?message=success');
        } else {
            header('Location: /pdu_pms_project/public/admin/manage_bookings?error=failed');
        }
        exit;
    }

    /**
     * Tạo đặt phòng mẫu với người dùng thực tế từ CSDL
     */
    public function createSampleBookingsWithUsers()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        // Lấy danh sách người dùng
        $teachers = $this->userModel->getUsersByRole('teacher');
        $students = $this->userModel->getUsersByRole('student');

        // Lấy danh sách phòng
        $rooms = $this->roomModel->getAllRooms();

        if (empty($teachers) || empty($students) || empty($rooms)) {
            header('Location: /pdu_pms_project/public/admin/manage_bookings?error=no_data');
            exit;
        }

        // Tạo mảng status với trọng số để tạo dữ liệu ngẫu nhiên có ý nghĩa
        $statuses = [
            'approved' => 60, // 60% khả năng được chọn
            'pending' => 30,  // 30% khả năng được chọn
            'rejected' => 10  // 10% khả năng được chọn
        ];

        // Mã lớp học mẫu
        $classCodes = ['CS101', 'MATH202', 'ENG305', 'PHYS101', 'CHEM203', 'BIO220', 'HIST105', 'ECO201'];

        // Tạo 10 đặt phòng mẫu
        $successCount = 0;

        for ($i = 0; $i < 10; $i++) {
            // Lấy ngẫu nhiên ngày bắt đầu trong 15 ngày tới
            $startDay = date('Y-m-d', strtotime('+' . rand(1, 15) . ' days'));
            $startHour = rand(7, 16); // Giờ bắt đầu từ 7h đến 16h
            $duration = rand(1, 3); // Thời lượng từ 1-3 giờ

            $startTime = date('Y-m-d H:i:s', strtotime("$startDay $startHour:00:00"));
            $endTime = date('Y-m-d H:i:s', strtotime("$startDay " . ($startHour + $duration) . ":00:00"));

            // Chọn ngẫu nhiên giữa giáo viên và sinh viên
            $userType = (rand(1, 100) > 70) ? 'student' : 'teacher'; // 70% là giáo viên, 30% là sinh viên
            $users = ($userType === 'teacher') ? $teachers : $students;

            if (empty($users)) {
                continue; // Bỏ qua nếu không có người dùng
            }

            // Chọn ngẫu nhiên người dùng và phòng
            $randomUser = $users[array_rand($users)];
            $randomRoom = $rooms[array_rand($rooms)];

            // Chọn trạng thái dựa trên trọng số
            $status = $this->getRandomWeightedElement($statuses);

            // Chọn ngẫu nhiên mã lớp học
            $classCode = $classCodes[array_rand($classCodes)];

            // Mục đích sử dụng mẫu
            $purposes = [
                'Giảng dạy lý thuyết',
                'Thực hành phòng máy',
                'Hội thảo chuyên đề',
                'Buổi thảo luận nhóm',
                'Bảo vệ đồ án/luận văn',
                'Kiểm tra giữa kỳ',
                'Thi cuối kỳ'
            ];
            $purpose = $purposes[array_rand($purposes)];

            // Kiểm tra xung đột
            $conflict = $this->bookingModel->checkBookingConflict($randomRoom['id'], $startTime, $endTime);
            if ($conflict) {
                // Nếu xung đột, thử lại ngày/giờ khác
                $i--; // Giảm biến đếm để đảm bảo tạo đủ số lượng yêu cầu
                continue;
            }

            // Tạo dữ liệu đặt phòng
            $bookingData = [
                'room_id' => $randomRoom['id'],
                'user_id' => $randomUser['id'],
                'class_code' => $classCode,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'purpose' => $purpose,
                'status' => $status
            ];

            $success = $this->bookingModel->addBooking($bookingData);
            if ($success) {
                $successCount++;
            }
        }

        header('Location: /pdu_pms_project/public/admin/manage_bookings?message=Created ' . $successCount . ' sample bookings');
        exit;
    }

    /**
     * Hàm hỗ trợ lấy phần tử ngẫu nhiên theo trọng số
     */
    private function getRandomWeightedElement(array $weightedValues)
    {
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }

        return array_key_first($weightedValues); // Fallback
    }
}
