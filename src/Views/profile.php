<?php
// Đảm bảo người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

$pageTitle = "Hồ sơ cá nhân";
$pageRole = $_SESSION['role']; // admin, teacher, hoặc student
$user = $data['user'] ?? null;

// Thiết lập thông tin cho page_header
$pageSubtitle = "Quản lý thông tin cá nhân của bạn";
$pageIcon = "fas fa-user-circle";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Hồ sơ cá nhân', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/components/page_header.php'; ?>

    <!-- Thông báo -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-sm-flex align-items-center justify-content-between">
                <a href="/pdu_pms_project/public/<?php echo $_SESSION['role']; ?>" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Quay lại Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cá nhân -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin cá nhân</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php
                        // Màu avatar dựa trên vai trò
                        $avatarColor = '#4e73df'; // Mặc định màu xanh
                        if ($_SESSION['role'] === 'admin') {
                            $avatarColor = '#e74a3b'; // Đỏ cho admin
                        } elseif ($_SESSION['role'] === 'teacher') {
                            $avatarColor = '#4e73df'; // Xanh cho giáo viên
                        } elseif ($_SESSION['role'] === 'student') {
                            $avatarColor = '#1cc88a'; // Xanh lá cho sinh viên
                        }
                        ?>
                        <div class="mx-auto" style="width: 150px; height: 150px; background-color: <?php echo $avatarColor; ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span class="text-white font-weight-bold" style="font-size: 3rem;">
                                <?php echo isset($user['full_name']) ? strtoupper(substr($user['full_name'], 0, 1)) : 'U'; ?>
                            </span>
                        </div>
                    </div>
                    <h5 class="font-weight-bold"><?php echo isset($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Người dùng'; ?></h5>
                    <p class="text-muted"><?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'email@example.com'; ?></p>
                    <p>
                        <?php
                        $roleBadgeClass = 'bg-secondary';
                        $roleText = 'Người dùng';

                        if ($_SESSION['role'] === 'admin') {
                            $roleBadgeClass = 'bg-danger';
                            $roleText = 'Quản trị viên';
                        } elseif ($_SESSION['role'] === 'teacher') {
                            $roleBadgeClass = 'bg-primary';
                            $roleText = 'Giảng viên';
                        } elseif ($_SESSION['role'] === 'student') {
                            $roleBadgeClass = 'bg-success';
                            $roleText = 'Sinh viên';
                        }
                        ?>
                        <span class="badge <?php echo $roleBadgeClass; ?>"><?php echo $roleText; ?></span>
                    </p>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key fa-sm me-1"></i> Đổi mật khẩu
                        </button>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit fa-sm me-1"></i> Chỉnh sửa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiết hồ sơ -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Chi tiết hồ sơ</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">Họ và tên</th>
                                    <td><?php echo isset($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Chưa cập nhật'; ?></td>
                                </tr>
                                <tr>
                                    <th>Tên đăng nhập</th>
                                    <td><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'Chưa cập nhật'; ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'Chưa cập nhật'; ?></td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td><?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : 'Chưa cập nhật'; ?></td>
                                </tr>
                                <tr>
                                    <th>Vai trò</th>
                                    <td><?php echo $roleText; ?></td>
                                </tr>
                                <?php if ($_SESSION['role'] === 'student' && isset($user['class_code'])): ?>
                                    <tr>
                                        <th>Mã lớp</th>
                                        <td><?php echo htmlspecialchars($user['class_code']); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th>Ngày tham gia</th>
                                    <td><?php echo isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'Không có thông tin'; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card phụ có thể thêm sau nếu cần -->
            <!-- <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin bổ sung</h6>
                </div>
                <div class="card-body">
                    <p>Thông tin bổ sung sẽ được hiển thị ở đây.</p>
                </div>
            </div> -->
        </div>
    </div>
</div>

<!-- Modal đổi mật khẩu -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm" action="/pdu_pms_project/public/profile/change_password" method="post">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                    </div>
                    <input type="hidden" name="change_password" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="changePasswordForm" class="btn btn-primary">Đổi mật khẩu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal chỉnh sửa hồ sơ -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Chỉnh sửa hồ sơ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm" action="/pdu_pms_project/public/profile/update" method="post">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo isset($user['full_name']) ? htmlspecialchars($user['full_name']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>">
                    </div>
                    <input type="hidden" name="update_profile" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="editProfileForm" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Các style tùy chỉnh có thể thêm ở đây */
</style>

<?php
// Lấy nội dung đã được output buffering
$pageContent = ob_get_clean();

// Bao gồm layout
include __DIR__ . '/layouts/main_layout.php';
?>