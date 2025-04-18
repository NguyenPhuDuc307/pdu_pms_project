<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../src/Models/UserModel.php';
require_once __DIR__ . '/../src/Models/RoomModel.php';
require_once __DIR__ . '/../src/Models/TimetableModel.php';
require_once __DIR__ . '/../src/Models/BookingModel.php';
require_once __DIR__ . '/../src/Models/EquipmentModel.php';
require_once __DIR__ . '/../src/Models/MaintenanceRequestModel.php';
require_once __DIR__ . '/../src/Models/ScheduleModel.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';
require_once __DIR__ . '/../src/Controllers/TeacherController.php';
require_once __DIR__ . '/../src/Controllers/StudentController.php';
require_once __DIR__ . '/../src/Controllers/RoomController.php';
require_once __DIR__ . '/../src/Controllers/EquipmentController.php';
require_once __DIR__ . '/../src/Controllers/MaintenanceController.php';
require_once __DIR__ . '/../src/Controllers/ProfileController.php';
require_once __DIR__ . '/../src/Controllers/BookingController.php';
require_once __DIR__ . '/../src/Config/Database.php';

use Controllers\AuthController;
use Controllers\AdminController;
use Controllers\TeacherController;
use Controllers\StudentController;
use Controllers\RoomController;
use Controllers\EquipmentController;
use Controllers\MaintenanceController;
use Controllers\ProfileController;
use Controllers\BookingController;

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base_path = 'pdu_pms_project/public';
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
    $uri = trim($uri, '/');
}

$authController = new AuthController();
$adminController = new AdminController();
$teacherController = new TeacherController();
$studentController = new StudentController();
$roomController = new RoomController();
$equipmentController = new EquipmentController();
$maintenanceController = new MaintenanceController();
$profileController = new ProfileController();
$bookingController = new BookingController();

switch ($uri) {
    case '':
        require_once __DIR__ . '/../src/Views/page/home.php';
        break;
    case 'login':
        $data = $authController->login($_POST);
        require_once __DIR__ . '/../src/Views/auth/login.php';
        break;
    case 'login/authenticate':
        // Xử lý form đăng nhập
        $_POST['login'] = true; // Thêm flag để AuthController biết đây là request đăng nhập
        $data = $authController->login($_POST);
        if (isset($data['error'])) {
            $_SESSION['error'] = $data['error'];
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        break;
    case 'register':
        $data = $authController->register($_POST);
        require_once __DIR__ . '/../src/Views/auth/register.php';
        break;
    case 'register/process':
        // Xử lý form đăng ký
        $_POST['register'] = true; // Thêm flag để AuthController biết đây là request đăng ký
        $data = $authController->register($_POST);
        if (isset($data['success'])) {
            $_SESSION['success'] = $data['success'];
            header('Location: /pdu_pms_project/public/login');
        } else if (isset($data['error'])) {
            $_SESSION['error'] = $data['error'];
            header('Location: /pdu_pms_project/public/register');
        }
        exit;
    case 'logout':
        $authController->logout();
        break;

    case 'contact':
        // Trang liên hệ
        require_once __DIR__ . '/../src/Views/page/contact.php';
        break;

    case 'page/contact':
        // Trang liên hệ
        require_once __DIR__ . '/../src/Views/page/contact.php';
        break;
    case 'admin':
        $data = $adminController->index();
        require_once __DIR__ . '/../src/Views/admin/dashboard/index.php';
        break;
    case 'admin/dashboard':
        // Chuyển hướng đến trang admin chính để tránh trùng lặp
        header('Location: /pdu_pms_project/public/admin');
        exit;
    case 'admin/profile':
        $data = $adminController->profile();
        require_once __DIR__ . '/../src/Views/admin/dashboard/profile.php';
        break;
    case 'admin/settings':
        $data = $adminController->settings();
        require_once __DIR__ . '/../src/Views/admin/settings.php';
        break;
    case 'admin/notifications':
        $data = $adminController->notifications();
        require_once __DIR__ . '/../src/Views/admin/notifications.php';
        break;
    case 'admin/activity':
        $data = $adminController->activityLog();
        require_once __DIR__ . '/../src/Views/admin/activity.php';
        break;
    case 'admin/manage_users':
        $data = $adminController->manageUsers();
        require_once __DIR__ . '/../src/Views/admin/users/manage_users.php';
        break;
    case 'admin/add_user':
        $data = $adminController->addUser($_POST);
        require_once __DIR__ . '/../src/Views/admin/users/add_user.php';
        break;
    case 'admin/edit_user':
        $data = $adminController->editUser(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/admin/users/edit_user.php';
        break;
    case 'admin/view_user':
        $data = $adminController->viewUser(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/admin/users/view_user.php';
        break;
    case 'admin/delete_user':
        $adminController->deleteUser($_GET); // Không gán $data vì dùng redirect
        break;
    case 'admin/manage_rooms':
        $data = $adminController->manageRooms();
        require_once __DIR__ . '/../src/Views/admin/rooms/manage_rooms.php';
        break;
    case 'admin/add_room':
        $data = $adminController->addRoom($_POST);
        require_once __DIR__ . '/../src/Views/admin/rooms/add_room.php';
        break;
    case 'admin/edit_room':
        if (isset($_POST['id']) || isset($_GET['id'])) {
            // Nếu có ID trong POST hoặc GET, xử lý chỉnh sửa
            $data = $adminController->editRoom(array_merge($_GET, $_POST));
            if (isset($data['error'])) {
                // Nếu có lỗi, hiển thị lại form
                require_once __DIR__ . '/../src/Views/admin/rooms/edit_room.php';
            }
            // Nếu thành công, controller sẽ chuyển hướng
        } else {
            // Nếu không có ID trong POST hoặc GET, chuyển hướng đến trang quản lý phòng
            header('Location: /pdu_pms_project/public/admin/manage_rooms');
            exit;
        }
        break;
    case 'admin/delete_room':
        $adminController->deleteRoom($_GET); // Không gán $data vì dùng redirect
        break;
    case 'admin/manage_timetable':
        $data = $adminController->manageTimetable();
        require_once __DIR__ . '/../src/Views/admin/timetables/manage_timetable.php';
        break;
    case 'admin/manage_timetables':
        // Chuyển hướng đến route chính để tránh trùng lặp
        header('Location: /pdu_pms_project/public/admin/manage_timetable');
        exit;
    case 'admin/add_timetable':
        $data = $adminController->addTimetable($_POST);
        require_once __DIR__ . '/../src/Views/admin/timetables/add_timetable.php';
        break;
    case 'admin/edit_timetable':
        $data = $adminController->editTimetable(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/admin/timetables/edit_timetable.php';
        break;
    case 'admin/delete_timetable':
        $adminController->deleteTimetable($_GET); // Không gán $data vì dùng redirect
        break;
    case 'admin/manage_bookings':
        // Sử dụng đối tượng AdminController đã được khởi tạo
        $data = $adminController->manageBookings();
        require_once __DIR__ . '/../src/Views/admin/bookings/manage_bookings.php';
        break;
    case 'admin/calendar_bookings':
        // Hiển thị lịch đặt phòng dạng calendar
        $data = $adminController->calendarBookings();
        require_once __DIR__ . '/../src/Views/admin/bookings/calendar_view.php';
        break;
    case 'admin/get_bookings_json':
        // API lấy dữ liệu đặt phòng dưới dạng JSON
        $adminController->getBookingsJson();
        break;
    case 'admin/add_booking':
        $data = $bookingController->bookRoom($_POST);
        require_once __DIR__ . '/../src/Views/common/book_room.php';
        break;
    case 'admin/edit_booking':
        // Kiểm tra xem có ID trong query string không
        if (isset($_GET['id'])) {
            $_GET['id'] = intval($_GET['id']);
            $data = $adminController->editBooking(array_merge($_GET, $_POST));
            require_once __DIR__ . '/../src/Views/admin/bookings/edit_booking.php';
        } else {
            // Nếu không có ID, chuyển hướng về trang quản lý đặt phòng
            header('Location: /pdu_pms_project/public/admin/manage_bookings');
            exit;
        }
        break;
    case 'admin/delete_booking':
        $adminController->deleteBooking($_GET); // Sửa: Bỏ gán $data vì dùng redirect
        break;
    case 'admin/auto_schedule':
        $data = $adminController->autoSchedule();
        require_once __DIR__ . '/../src/Views/admin/bookings/auto_schedule.php';
        break;
    case 'admin/auto_schedule_room':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $timetable_id = $data['timetable_id'] ?? null;

            if (!$timetable_id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Thiếu timetable_id.']);
                break;
            }

            $result = $roomController->autoScheduleRoom($timetable_id);

            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ.']);
        }
        break;
    case 'admin/cancel_room_schedule':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $roomController->cancelRoomSchedule($_POST['timetable_id']);

            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ.']);
        }
        break;
    case 'teacher':
        $data = $teacherController->index();
        require_once __DIR__ . '/../src/Views/teacher/index.php';
        break;
    case 'teacher/book_room':
        $data = $bookingController->bookRoom($_POST);
        require_once __DIR__ . '/../src/Views/common/book_room.php';
        break;
    case 'teacher/my_bookings':
        // Thêm route cho xem lịch đặt phòng của giáo viên
        $data = $teacherController->index(); // Sử dụng index vì đã có dữ liệu bookings trong đó
        require_once __DIR__ . '/../src/Views/teacher/index.php'; // Sử dụng trang index vì đã có hiển thị bookings
        break;
    case 'teacher/my_timetables':
        // Route cho xem lịch dạy của giáo viên
        error_log("DEBUG: Đang xử lý route teacher/my_timetables");
        error_log("DEBUG: Session info: " . json_encode($_SESSION));

        try {
            $data = $teacherController->myTimetables($_GET);
            error_log("DEBUG: Đã lấy dữ liệu từ controller: " . json_encode($data));
            require_once __DIR__ . '/../src/Views/teacher/my_timetables.php';
        } catch (\Exception $e) {
            error_log("ERROR: Lỗi khi xử lý route teacher/my_timetables: " . $e->getMessage());
            echo "<h1>Có lỗi xảy ra</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
        }
        break;
    case 'student':
        $data = $studentController->index();
        require_once __DIR__ . '/../src/Views/student/index.php';
        break;
    case 'student/book_room':
        $data = $bookingController->bookRoom($_POST);
        require_once __DIR__ . '/../src/Views/common/book_room.php';
        break;

    case 'student/my_bookings':
        // Thêm route cho xem lịch đặt phòng của sinh viên
        $data = $studentController->index(); // Sử dụng index vì đã có dữ liệu bookings trong đó
        require_once __DIR__ . '/../src/Views/student/my_bookings.php';
        break;

    case 'admin/search_rooms':
        $data = $adminController->searchRooms($_GET);
        require_once __DIR__ . '/../src/Views/admin/rooms/search_rooms.php';
        break;
    case 'admin/equipments':
        $data = $equipmentController->index();
        require_once __DIR__ . '/../src/Views/admin/equipments.php';
        break;
    case 'admin/add_equipment':
        $data = $equipmentController->addEquipment($_POST);
        require_once __DIR__ . '/../src/Views/admin/add_equipment.php';
        break;
    case 'admin/edit_equipment':
        $data = $equipmentController->editEquipment(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/admin/edit_equipment.php';
        break;
    case 'admin/delete_equipment':
        $equipmentController->deleteEquipment($_GET);
        break;
    case 'admin/room_equipments':
        // Chuyển hướng đến trang chi tiết phòng kết hợp
        if (isset($_GET['room_id'])) {
            header('Location: /pdu_pms_project/public/admin/room_detail/' . $_GET['room_id']);
            exit;
        } else {
            header('Location: /pdu_pms_project/public/admin/manage_rooms');
            exit;
        }
    case 'admin/add_room_equipment':
        $equipmentController->addEquipmentToRoom($_POST);
        break;
    case 'admin/update_room_equipment':
        $equipmentController->updateRoomEquipment($_POST);
        break;
    case 'admin/remove_room_equipment':
        $equipmentController->removeEquipmentFromRoom($_GET);
        break;
    case 'admin/update_maintenance':
        $equipmentController->updateMaintenance($_POST);
        break;
    case 'admin/maintenance_requests':
        $data = $maintenanceController->adminIndex();
        require_once __DIR__ . '/../src/Views/admin/maintenance_requests.php';
        break;
    case 'admin/view_request':
        $data = $maintenanceController->viewRequest($_GET);
        require_once __DIR__ . '/../src/Views/admin/view_request.php';
        break;
    case 'admin/update_request_status':
        $maintenanceController->updateRequestStatus($_POST);
        break;
    case 'admin/delete_request':
        $maintenanceController->deleteRequest($_GET);
        break;
    case 'api/get_room_equipments':
        $maintenanceController->getRoomEquipments($_GET);
        break;
    case 'maintenance':
        $data = $maintenanceController->userIndex();
        require_once __DIR__ . '/../src/Views/maintenance/index.php';
        break;
    case 'maintenance/create':
        $data = $maintenanceController->createRequest(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/maintenance/create.php';
        break;
    case 'teacher/search_rooms':
        $data = $teacherController->searchRooms($_GET);
        require_once __DIR__ . '/../src/Views/teacher/search_rooms.php';
        break;
    case 'teacher/room_detail':
        $data = $teacherController->roomDetail($_GET);
        require_once __DIR__ . '/../src/Views/teacher/room_detail.php';
        break;
    case 'teacher/suggest_rooms':
        $data = $teacherController->suggestAvailableRooms($_GET);
        require_once __DIR__ . '/../src/Views/teacher/suggest_rooms.php';
        break;
    case 'teacher/get-available-rooms':
        $bookingController->getAvailableRooms();
        break;
    case 'teacher/get-teacher-bookings':
        $teacherController->getTeacherBookings();
        break;
    case 'teacher/get-all-rooms':
        $teacherController->getAllRooms();
        break;
    case 'student/search_rooms':
        $data = $studentController->searchRooms($_GET);
        require_once __DIR__ . '/../src/Views/student/search_rooms.php';
        break;
    case 'student/room_detail':
        $data = $studentController->roomDetail($_GET);
        require_once __DIR__ . '/../src/Views/student/room_detail.php';
        break;
    case 'student/suggest_rooms':
        $data = $studentController->suggestAvailableRooms($_GET);
        require_once __DIR__ . '/../src/Views/student/suggest_rooms.php';
        break;

    case 'admin/system_logs':
        $data = $adminController->systemLogs();
        require_once __DIR__ . '/../src/Views/admin/system_logs.php';
        break;
    case 'admin/create_sample_bookings':
        // Sử dụng đối tượng AdminController đã được khởi tạo
        $adminController->createSampleBookingsWithUsers();
        break;
    case 'admin/get_users_by_role':
        // Sử dụng đối tượng AdminController đã được khởi tạo
        $adminController->getUsersByRole();
        break;
    case 'admin/assign_room':
        // Sử dụng đối tượng AdminController đã được khởi tạo
        $adminController->assignRoom($_POST);
        break;
    case 'admin/view_booking':
        // This case should only handle /admin/view_booking without an ID parameter
        header('Location: /pdu_pms_project/public/admin/manage_bookings');
        exit;
    case 'profile':
        // Trang profile chung cho tất cả các role
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        $data = $profileController->index();
        require_once __DIR__ . '/../src/Views/profile.php';
        break;

    case 'profile/update':
        // Cập nhật thông tin profile
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        $profileController->updateProfile($_POST);
        break;

    case 'profile/change_password':
        // Đổi mật khẩu
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        $profileController->changePassword($_POST);
        break;

    case 'book_room':
        // Trang đặt phòng chung cho tất cả các vai trò
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }
        $data = $bookingController->bookRoom($_POST);
        require_once __DIR__ . '/../src/Views/common/book_room.php';
        break;
    case 'demo/alerts':
        // Trang demo hiển thị các loại alert
        require_once __DIR__ . '/../src/Helpers/AlertHelper.php';
        require_once __DIR__ . '/../src/Views/demo/alerts_demo.php';
        break;
    default:
        // Handle paths with IDs
        if (preg_match('/^admin\/edit_user\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $data = $adminController->editUser(array_merge($_GET, $_POST));
            require_once __DIR__ . '/../src/Views/admin/users/edit_user.php';
            break;
        } elseif (preg_match('/^admin\/delete_booking\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            // Sử dụng đối tượng AdminController đã được khởi tạo
            $adminController->deleteBooking($_GET);
            break;
        } elseif (preg_match('/^admin\/view_booking\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $data = [];
            $bookingModel = new \Models\BookingModel();
            $booking = $bookingModel->getBookingById($_GET['id']);
            if ($booking) {
                $data['booking'] = $booking;
                require_once __DIR__ . '/../src/Views/admin/bookings/view_booking.php';
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_bookings?error=Booking not found');
                exit;
            }
            break;
        } elseif (preg_match('/^admin\/edit_room\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $data = $adminController->editRoom(array_merge($_GET, $_POST));
            require_once __DIR__ . '/../src/Views/admin/rooms/edit_room.php';
            break;
        } elseif (preg_match('/^admin\/delete_room\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $adminController->deleteRoom($_GET);
            break;
        } elseif (preg_match('/^admin\/room_detail\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $roomModel = new \Models\RoomModel();
            $room = $roomModel->getRoomById($_GET['id']);

            // Get room type if the room has a room_type_id
            $roomType = null;
            if (!empty($room['room_type_id'])) {
                $roomTypes = $roomModel->getRoomTypes();
                foreach ($roomTypes as $type) {
                    if ($type['id'] == $room['room_type_id']) {
                        $roomType = $type;
                        break;
                    }
                }
            }

            // Get upcoming bookings for this room
            $upcomingBookings = $roomModel->getUpcomingClassesForRoom($_GET['id']);

            // Get booking statistics
            $bookingStats = [
                'total' => count($upcomingBookings ?? []),
                'usage_rate' => '65%' // Example placeholder value
            ];

            // Sample usage data for the chart
            $usageData = [4, 6, 2, 5, 3, 0, 1]; // Example data

            // Get equipment data for the room
            $equipmentModel = new \Models\EquipmentModel();
            $equipments = $equipmentModel->getAllEquipments();
            $room = $roomModel->getDetailedRoom($_GET['id']); // Get detailed room with equipment

            $data = [
                'room' => $room,
                'roomType' => $roomType,
                'upcomingBookings' => $upcomingBookings,
                'bookingStats' => $bookingStats,
                'usageData' => $usageData,
                'equipments' => $equipments
            ];

            // Use the combined view
            require_once __DIR__ . '/../src/Views/admin/rooms/room_detail_combined.php';
            break;
        } elseif (preg_match('/^admin\/edit_room_type\/(\d+)$/', $uri, $matches)) {
            // Directly redirect to the edit form and let the form handle the room type data
            $_GET['id'] = $matches[1];
            $_POST['id'] = $matches[1]; // Also add to POST for consistency

            // Just redirect to the edit_room_type page
            header('Location: /pdu_pms_project/public/admin/edit_room_type?id=' . $_GET['id']);
            exit;
        } elseif (preg_match('/^admin\/delete_room_type\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $adminController->deleteRoomType($_GET);
            break;
        } elseif (preg_match('/^admin\/approve_booking\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $bookingModel = new \Models\BookingModel();
            $success = $bookingModel->updateBookingStatus($_GET['id'], 'approved');
            if ($success) {
                header('Location: /pdu_pms_project/public/admin/manage_bookings?message=Booking approved successfully');
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_bookings?error=Failed to approve booking');
            }
            exit;
        } elseif (preg_match('/^admin\/reject_booking\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $bookingModel = new \Models\BookingModel();
            $success = $bookingModel->updateBookingStatus($_GET['id'], 'rejected');
            if ($success) {
                header('Location: /pdu_pms_project/public/admin/manage_bookings?message=Booking rejected successfully');
            } else {
                header('Location: /pdu_pms_project/public/admin/manage_bookings?error=Failed to reject booking');
            }
            exit;
        } elseif (preg_match('/^admin\/edit_timetable\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $data = $adminController->editTimetable(array_merge($_GET, $_POST));
            require_once __DIR__ . '/../src/Views/admin/timetables/edit_timetable.php';
            break;
        } elseif (preg_match('/^admin\/edit_booking\/(\d+)$/', $uri, $matches)) {
            // Chuyển hướng đến route admin/edit_booking với ID trong query string
            header('Location: /pdu_pms_project/public/admin/edit_booking?id=' . $matches[1]);
            exit;
        }

        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        break;
}
