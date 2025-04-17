<?php
// Đảm bảo chỉ cho admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

$pageTitle = "Hồ sơ cá nhân";
$user = $data['user'] ?? null;

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">Hồ sơ cá nhân</h1>
                <a href="/pdu_pms_project/public/admin" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin cá nhân</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="mx-auto" style="width: 150px; height: 150px; background-color: #4e73df; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span class="text-white font-weight-bold" style="font-size: 3rem;">
                                <?php echo isset($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'A'; ?>
                            </span>
                        </div>
                    </div>
                    <h5 class="font-weight-bold"><?php echo isset($user['fullname']) ? htmlspecialchars($user['fullname']) : 'Admin'; ?></h5>
                    <p class="text-muted"><?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'admin@example.com'; ?></p>
                    <p>
                        <span class="badge bg-primary">Quản trị viên</span>
                    </p>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key fa-sm"></i> Đổi mật khẩu
                        </button>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit fa-sm"></i> Chỉnh sửa
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                                    <td><?php echo isset($user['fullname']) ? htmlspecialchars($user['fullname']) : 'Chưa cập nhật'; ?></td>
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
                                    <td>Quản trị viên</td>
                                </tr>
                                <tr>
                                    <th>Ngày tham gia</th>
                                    <td><?php echo isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : date('d/m/Y'); ?></td>
                                </tr>
                                <tr>
                                    <th>Trạng thái</th>
                                    <td><span class="badge bg-success">Đang hoạt động</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hoạt động gần đây</h6>
                </div>
                <div class="card-body">
                    <div class="timeline-item">
                        <div class="timeline-item-marker">
                            <div class="timeline-item-marker-text">Hôm nay</div>
                            <div class="timeline-item-marker-indicator bg-primary"></div>
                        </div>
                        <div class="timeline-item-content">
                            <p class="mb-0">Đăng nhập vào hệ thống</p>
                            <p class="text-muted small"><?php echo date('H:i'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
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
                <form id="changePasswordForm" action="/pdu_pms_project/public/admin/change_password" method="post">
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="changePasswordForm" class="btn btn-primary">Lưu thay đổi</button>
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
                <form id="editProfileForm" action="/pdu_pms_project/public/admin/update_profile" method="post">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo isset($user['fullname']) ? htmlspecialchars($user['fullname']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="editProfileForm" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<?php
// Lấy nội dung đã được output buffering
$content = ob_get_clean();

// Bao gồm layout
include dirname(dirname(__DIR__)) . '/layouts/admin_layout.php';
?>
<?php
// Không cần đóng thêm thẻ PHP vì đã được đóng trong file admin_layout.php
?>