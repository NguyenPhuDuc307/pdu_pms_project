<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../src/Models/UserModel.php';
require_once __DIR__ . '/../src/Models/RoomModel.php';
require_once __DIR__ . '/../src/Models/TimetableModel.php';
require_once __DIR__ . '/../src/Models/BookingModel.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';
require_once __DIR__ . '/../src/Controllers/TeacherController.php';
require_once __DIR__ . '/../src/Controllers/StudentController.php';
require_once __DIR__ . '/../src/Controllers/RoomController.php';
require_once __DIR__ . '/../src/Config/Database.php';

use Controllers\AuthController;
use Controllers\AdminController;
use Controllers\TeacherController;
use Controllers\StudentController;
use Controllers\RoomController;

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

switch ($uri) {
    case '':
    case 'login':
        $data = $authController->login($_POST);
        require_once __DIR__ . '/../src/Views/auth/login.php';
        break;
    case 'register':
        $data = $authController->register($_POST);
        require_once __DIR__ . '/../src/Views/auth/register.php';
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'guide':
        // Trang hướng dẫn
        require_once __DIR__ . '/../src/Views/guide.php';
        break;
    case 'admin':
        $data = $adminController->index();
        require_once __DIR__ . '/../src/Views/admin/index.php';
        break;
    case 'admin/manage_users':
        $data = $adminController->manageUsers();
        require_once __DIR__ . '/../src/Views/admin/manage_users.php';
        break;
    case 'admin/add_user':
        $data = $adminController->addUser($_POST);
        require_once __DIR__ . '/../src/Views/admin/add_user.php';
        break;
    case 'admin/edit_user':
        $data = $adminController->editUser(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/admin/edit_user.php';
        break;
    case 'admin/delete_user':
        $adminController->deleteUser($_GET); // Không gán $data vì dùng redirect
        break;
    case 'admin/manage_rooms':
        $data = $adminController->manageRooms();
        require_once __DIR__ . '/../src/Views/admin/manage_rooms.php';
        break;
    case 'admin/add_room':
        $data = $adminController->addRoom($_POST);
        require_once __DIR__ . '/../src/Views/admin/add_room.php';
        break;
    case 'admin/edit_room':
        $data = $adminController->editRoom(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/admin/edit_room.php';
        break;
    case 'admin/delete_room':
        $adminController->deleteRoom($_GET); // Không gán $data vì dùng redirect
        break;
    case 'admin/manage_timetable':
        $data = $adminController->manageTimetable();
        require_once __DIR__ . '/../src/Views/admin/manage_timetable.php';
        break;
    case 'admin/add_timetable':
        $data = $adminController->addTimetable($_POST);
        require_once __DIR__ . '/../src/Views/admin/add_timetable.php';
        break;
    case 'admin/edit_timetable':
        $data = $adminController->editTimetable(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/admin/edit_timetable.php';
        break;
    case 'admin/delete_timetable':
        $adminController->deleteTimetable($_GET); // Không gán $data vì dùng redirect
        break;
    case 'admin/manage_bookings':
        $data = $adminController->manageBookings();
        require_once __DIR__ . '/../src/Views/admin/manage_bookings.php';
        break;
    case 'admin/add_booking':
        $data = $adminController->addBooking($_POST);
        require_once __DIR__ . '/../src/Views/admin/add_booking.php';
        break;
    case 'admin/edit_booking':
        $data = $adminController->editBooking(array_merge($_GET, $_POST));
        require_once __DIR__ . '/../src/Views/admin/edit_booking.php';
        break;
    case 'admin/delete_booking':
        $adminController->deleteBooking($_GET); // Sửa: Bỏ gán $data vì dùng redirect
        break;
    case 'admin/auto_schedule':
        $data = $adminController->autoSchedule();
        require_once __DIR__ . '/../src/Views/admin/auto_schedule.php';
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
        $data = $teacherController->bookRoom();
        require_once __DIR__ . '/../src/Views/teacher/book_room.php';
        break;
    case 'student':
        $data = $studentController->index();
        require_once __DIR__ . '/../src/Views/student/index.php';
        break;
    case 'student/book_room':
        $data = $studentController->bookRoom();
        require_once __DIR__ . '/../src/Views/student/book_room.php';
        break;
    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        break;
}
