<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Thêm người dùng mới";
$pageSubtitle = "Tạo tài khoản mới cho người dùng trong hệ thống";
$pageIcon = "fas fa-user-plus";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý người dùng', 'link' => '/pdu_pms_project/public/admin/manage_users'],
    ['title' => 'Thêm người dùng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/manage_users" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-user-plus me-2"></i> Thông tin người dùng</h6>
        </div>
        <div class="card-body">
            <form method="POST" class="p-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Vai trò</label>
                    <select name="role" class="form-select">
                        <option value="admin">Admin</option>
                        <option value="teacher">Giáo viên</option>
                        <option value="student">Sinh viên</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mã lớp (nếu là sinh viên)</label>
                    <input type="text" name="class_code" class="form-control">
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="/pdu_pms_project/public/admin/manage_users" class="btn btn-secondary">Hủy bỏ</a>
                    <button type="submit" name="add_user" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu người dùng
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>