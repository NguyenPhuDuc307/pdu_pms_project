<?php

/**
 * AlertHelper - Lớp hỗ trợ hiển thị thông báo alert
 */
class AlertHelper
{
    // Các thông báo mặc định cho các hành động
    // Thông báo cho người dùng
    const USER_ADDED = 'Thêm người dùng thành công';
    const USER_UPDATED = 'Cập nhật người dùng thành công';
    const USER_DELETED = 'Xóa người dùng thành công';
    const USER_NOT_FOUND = 'Không tìm thấy người dùng';
    const USER_EXISTS = 'Người dùng đã tồn tại';

    // Thông báo cho phòng học
    const ROOM_ADDED = 'Thêm phòng học thành công';
    const ROOM_UPDATED = 'Cập nhật phòng học thành công';
    const ROOM_DELETED = 'Xóa phòng học thành công';
    const ROOM_NOT_FOUND = 'Không tìm thấy phòng học';
    const ROOM_EXISTS = 'Phòng học đã tồn tại';

    // Thông báo cho đặt phòng
    const BOOKING_ADDED = 'Đặt phòng thành công';
    const BOOKING_UPDATED = 'Cập nhật đặt phòng thành công';
    const BOOKING_DELETED = 'Hủy đặt phòng thành công';
    const BOOKING_APPROVED = 'Phê duyệt đặt phòng thành công';
    const BOOKING_REJECTED = 'Từ chối đặt phòng thành công';
    const BOOKING_NOT_FOUND = 'Không tìm thấy đặt phòng';
    const BOOKING_CONFLICT = 'Xung đột lịch đặt phòng';

    // Thông báo cho thiết bị
    const EQUIPMENT_ADDED = 'Thêm thiết bị thành công';
    const EQUIPMENT_UPDATED = 'Cập nhật thiết bị thành công';
    const EQUIPMENT_DELETED = 'Xóa thiết bị thành công';
    const EQUIPMENT_NOT_FOUND = 'Không tìm thấy thiết bị';

    // Thông báo cho bảo trì
    const MAINTENANCE_ADDED = 'Thêm yêu cầu bảo trì thành công';
    const MAINTENANCE_UPDATED = 'Cập nhật yêu cầu bảo trì thành công';
    const MAINTENANCE_DELETED = 'Xóa yêu cầu bảo trì thành công';
    const MAINTENANCE_NOT_FOUND = 'Không tìm thấy yêu cầu bảo trì';

    // Thông báo cho lịch dạy
    const TIMETABLE_ADDED = 'Thêm lịch dạy thành công';
    const TIMETABLE_UPDATED = 'Cập nhật lịch dạy thành công';
    const TIMETABLE_DELETED = 'Xóa lịch dạy thành công';
    const TIMETABLE_NOT_FOUND = 'Không tìm thấy lịch dạy';

    // Thông báo cho hệ thống
    const SYSTEM_ERROR = 'Lỗi hệ thống, vui lòng thử lại sau';
    const PERMISSION_DENIED = 'Bạn không có quyền thực hiện hành động này';
    const LOGIN_REQUIRED = 'Vui lòng đăng nhập để tiếp tục';
    const INVALID_INPUT = 'Dữ liệu nhập không hợp lệ';
    const ACTION_COMPLETED = 'Thao tác hoàn tất';
    const ACTION_FAILED = 'Thao tác thất bại';

    // Thông báo cho đăng nhập/đăng ký
    const LOGIN_SUCCESS = 'Đăng nhập thành công';
    const LOGIN_FAILED = 'Sai tên đăng nhập hoặc mật khẩu';
    const REGISTER_SUCCESS = 'Đăng ký thành công';
    const REGISTER_FAILED = 'Đăng ký thất bại';
    const LOGOUT_SUCCESS = 'Đã đăng xuất';
    const PASSWORD_CHANGED = 'Đổi mật khẩu thành công';
    const PASSWORD_MISMATCH = 'Mật khẩu không khớp';
    const PROFILE_UPDATED = 'Cập nhật hồ sơ thành công';
    // Các loại alert
    const PRIMARY = 'primary';
    const SECONDARY = 'secondary';
    const SUCCESS = 'success';
    const DANGER = 'danger';
    const WARNING = 'warning';
    const INFO = 'info';
    const LIGHT = 'light';
    const DARK = 'dark';

    // Mảng lưu trữ các thông báo
    private static $alerts = [];

    /**
     * Thêm một thông báo vào danh sách
     *
     * @param string $message Nội dung thông báo
     * @param string $type Loại thông báo (primary, secondary, success, danger, warning, info, light, dark)
     * @param bool $dismissible Có thể đóng được không
     * @return void
     */
    public static function add($message, $type = self::INFO, $dismissible = true)
    {
        self::$alerts[] = [
            'message' => $message,
            'type' => $type,
            'dismissible' => $dismissible
        ];

        // Lưu vào session để có thể hiển thị sau khi chuyển trang
        if (!isset($_SESSION['alerts'])) {
            $_SESSION['alerts'] = [];
        }
        $_SESSION['alerts'][] = [
            'message' => $message,
            'type' => $type,
            'dismissible' => $dismissible
        ];
    }

    /**
     * Lấy tất cả thông báo và xóa khỏi session
     *
     * @return array Mảng các thông báo
     */
    public static function getAll()
    {
        $alerts = self::$alerts;

        // Thêm các thông báo từ session
        if (isset($_SESSION['alerts']) && is_array($_SESSION['alerts'])) {
            $alerts = array_merge($alerts, $_SESSION['alerts']);
            // Xóa thông báo khỏi session sau khi đã lấy
            unset($_SESSION['alerts']);
        }

        return $alerts;
    }

    /**
     * Kiểm tra xem có thông báo nào không
     *
     * @return bool
     */
    public static function hasAlerts()
    {
        return !empty(self::$alerts) || (isset($_SESSION['alerts']) && !empty($_SESSION['alerts']));
    }

    /**
     * Thêm thông báo thành công
     *
     * @param string $message Nội dung thông báo
     * @param bool $dismissible Có thể đóng được không
     * @return void
     */
    public static function success($message, $dismissible = true)
    {
        self::add($message, self::SUCCESS, $dismissible);
    }

    /**
     * Thêm thông báo lỗi
     *
     * @param string $message Nội dung thông báo
     * @param bool $dismissible Có thể đóng được không
     * @return void
     */
    public static function error($message, $dismissible = true)
    {
        self::add($message, self::DANGER, $dismissible);
    }

    /**
     * Thêm thông báo cảnh báo
     *
     * @param string $message Nội dung thông báo
     * @param bool $dismissible Có thể đóng được không
     * @return void
     */
    public static function warning($message, $dismissible = true)
    {
        self::add($message, self::WARNING, $dismissible);
    }

    /**
     * Thêm thông báo thông tin
     *
     * @param string $message Nội dung thông báo
     * @param bool $dismissible Có thể đóng được không
     * @return void
     */
    public static function info($message, $dismissible = true)
    {
        self::add($message, self::INFO, $dismissible);
    }
}
