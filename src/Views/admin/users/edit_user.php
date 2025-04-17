<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Chỉnh sửa người dùng";
$pageSubtitle = "Cập nhật thông tin tài khoản người dùng trong hệ thống";
$pageIcon = "fas fa-user-edit";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý người dùng', 'link' => '/pdu_pms_project/public/admin/manage_users'],
    ['title' => 'Chỉnh sửa người dùng', 'link' => '']
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
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">Sửa thông tin người dùng</h4>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $data['user']['id']; ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Tên đăng nhập</label>
                            <input type="text" id="username" name="username" value="<?php echo $data['user']['username']; ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $data['user']['email']; ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">Vai trò</label>
                            <select id="role" name="role" class="form-select">
                                <option value="admin" <?php echo $data['user']['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="teacher" <?php echo $data['user']['role'] === 'teacher' ? 'selected' : ''; ?>>Giáo viên</option>
                                <option value="student" <?php echo $data['user']['role'] === 'student' ? 'selected' : ''; ?>>Sinh viên</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="class_code" class="form-label fw-semibold">Mã lớp (nếu là sinh viên)</label>
                            <input type="text" id="class_code" name="class_code" value="<?php echo $data['user']['class_code'] ?? ''; ?>" class="form-control">
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="edit_user" class="btn btn-primary flex-grow-1">Cập nhật</button>
                            <a href="/pdu_pms_project/public/admin/manage_users" class="btn btn-secondary flex-grow-1">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
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