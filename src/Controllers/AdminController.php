<?php

namespace Controllers;

use Models\UserModel;
use Models\RoomModel;
use Models\TimetableModel;
use Models\BookingModel;

class AdminController
{
    private $userModel;
    private $roomModel;
    private $timetableModel;
    private $bookingModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roomModel = new RoomModel();
        $this->timetableModel = new TimetableModel();
        $this->bookingModel = new BookingModel();
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

        $data = [
            'title' => 'Admin Dashboard',

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
                        'user_name' => $user['username'],
                        'room_name' => $room['room_number'],
                        'timestamp' => $booking['created_at'] ?? date('Y-m-d H:i:s'),
                        'message' => "{$user['username']} đã đặt phòng {$room['room_number']}"
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
            $this->userModel->addUser($data['username'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $data['role'], $data['class_code'] ?? null);
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
            $this->userModel->updateUser($data['id'], $data['username'], $data['email'], $data['role'], $data['class_code'] ?? null);
            header('Location: /pdu_pms_project/public/admin/manage_users');
            exit;
        }
        $user = $this->userModel->getUserById($data['id']);
        return ['user' => $user];
    }

    public function deleteUser($data)
    {
        $id = $data['id'] ?? null;
        if ($id) {
            $success = $this->userModel->deleteUser($id);
            if ($success) {
                header('Location: /pdu_pms_project/public/admin/manage_users?message=User deleted successfully');
                exit;
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_users?error=Failed to delete user');
                exit;
            }
        }
        header('Location: /pdu_pms_project/public/admin/manage_users?error=Invalid user ID');
        exit;
    }

    // Quản lý phòng
    public function manageRooms()
    {
        return ['rooms' => $this->roomModel->getAllRooms()];
    }

    public function addRoom($data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $data['name'] ?? '';
            $capacity = $data['capacity'] ?? 0;

            if ($name && $capacity) {
                $success = $this->roomModel->addRoom($name, $capacity);
                if ($success) {
                    header('Location: /pdu_pms_project/public/admin/manage_rooms?message=Room added successfully');
                    exit;
                } else {
                    return ['error' => 'Failed to add room'];
                }
            } else {
                return ['error' => 'Please fill in all fields'];
            }
        }
        return [];
    }

    public function editRoom($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) {
            header('Location: /pdu_pms_project/public/admin/manage_rooms?error=Invalid room ID');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $data['name'] ?? '';
            $capacity = $data['capacity'] ?? 0;
            $status = $data['status'] ?? 'trống';

            if ($name && $capacity) {
                $success = $this->roomModel->updateRoom($id, $name, $capacity, $status);
                if ($success) {
                    header('Location: /pdu_pms_project/public/admin/manage_rooms?message=Room updated successfully');
                    exit;
                } else {
                    return ['error' => 'Failed to update room', 'room' => $this->roomModel->getRoomById($id)];
                }
            } else {
                return ['error' => 'Please fill in all required fields', 'room' => $this->roomModel->getRoomById($id)];
            }
        }

        return ['room' => $this->roomModel->getRoomById($id)];
    }

    public function deleteRoom($data)
    {
        $id = $data['id'] ?? null;
        if ($id) {
            $success = $this->roomModel->deleteRoom($id);
            if ($success) {
                header('Location: /pdu_pms_project/public/admin/manage_rooms?message=Room deleted successfully');
                exit;
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_rooms?error=Failed to delete room');
                exit;
            }
        }
        header('Location: /pdu_pms_project/public/admin/manage_rooms?error=Invalid room ID');
        exit;
    }

    // Quản lý lịch dạy
    public function manageTimetable()
    {
        return ['timetables' => $this->timetableModel->getAllTimetables()];
    }

    public function addTimetable($data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teacher_id = $data['teacher_id'] ?? null;
            $class_code = $data['class_code'] ?? '';
            $subject = $data['subject'] ?? '';
            $start_time = $data['start_time'] ?? '';
            $end_time = $data['end_time'] ?? '';
            $participants = $data['participants'] ?? 0;

            if ($teacher_id && $class_code && $subject && $start_time && $end_time) {
                $success = $this->timetableModel->addTimetable($teacher_id, $class_code, $subject, $start_time, $end_time, $participants);
                if ($success) {
                    header('Location: /pdu_pms_project/public/admin/manage_timetable?message=Timetable added successfully');
                    exit;
                } else {
                    return ['error' => 'Failed to add timetable'];
                }
            } else {
                return ['error' => 'Please fill in all required fields'];
            }
        }
        return ['users' => $this->userModel->getAllUsers()];
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

            // Kiểm tra sức chứa của phòng
            if ($room_id) {
                $room = $this->roomModel->getRoomById($room_id);
                if ($room && $participants > $room['capacity']) {
                    $errors[] = "Phòng {$room['name']} chỉ có sức chứa {$room['capacity']} người, không đủ cho {$participants} sinh viên.";
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
                    header('Location: /pdu_pms_project/public/admin/manage_timetable?message=Lịch dạy đã được cập nhật thành công');
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
                header('Location: /pdu_pms_project/public/admin/manage_timetable?message=Timetable deleted successfully');
                exit;
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_timetable?error=Failed to delete timetable');
                exit;
            }
        }
        header('Location: /pdu_pms_project/public/admin/manage_timetable?error=Invalid timetable ID');
        exit;
    }

    // Quản lý đặt phòng
    public function manageBookings()
    {
        return ['bookings' => $this->bookingModel->getAllBookings()];
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
            $status = $data['status'] ?? 'chờ duyệt';

            if ($room_id && $class_code && $start_time && $end_time) {
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
                    'teacher_id' => $teacher_id,
                    'student_id' => $student_id,
                    'class_code' => $class_code,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'status' => $status
                ];
                $success = $this->bookingModel->addBooking($bookingData);
                if ($success) {
                    header('Location: /pdu_pms_project/public/admin/manage_bookings?message=Đặt phòng thành công');
                    exit;
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
            $status = $data['status'] ?? 'chờ duyệt';

            if ($room_id && $class_code && $start_time && $end_time) {
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
                    'teacher_id' => $teacher_id,
                    'student_id' => $student_id,
                    'class_code' => $class_code,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'status' => $status
                ];
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
            if ($success) {
                header('Location: /pdu_pms_project/public/admin/manage_bookings?message=Booking deleted successfully');
                exit;
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_bookings?error=Failed to delete booking');
                exit;
            }
        }
        header('Location: /pdu_pms_project/public/admin/manage_bookings?error=Invalid booking ID');
        exit;
    }

    public function autoSchedule()
    {
        $roomController = new \Controllers\RoomController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $timetable_id = $_POST['timetable_id'] ?? null;

            if (!$timetable_id) {
                header('Location: /pdu_pms_project/public/admin/manage_timetable?error=Invalid timetable ID');
                exit;
            }

            $result = $roomController->autoScheduleRoom($timetable_id);

            if ($result['success']) {
                header('Location: /pdu_pms_project/public/admin/manage_timetable?message=Room scheduled successfully: ' . $result['room']['name']);
                exit;
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_timetable?error=' . urlencode($result['message']));
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
                header('Location: /pdu_pms_project/public/admin/manage_timetable?error=Invalid timetable ID');
                exit;
            }

            $result = $roomController->cancelRoomSchedule($timetable_id);

            if ($result['success']) {
                header('Location: /pdu_pms_project/public/admin/manage_timetable?message=' . urlencode($result['message']));
                exit;
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_timetable?error=' . urlencode($result['message']));
                exit;
            }
        }

        // Nếu không phải POST, trả về trang manage_timetable
        return ['timetables' => $this->timetableModel->getAllTimetables()];
    }
}
