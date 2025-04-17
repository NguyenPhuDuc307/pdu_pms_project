<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Chi tiết người dùng";
$pageSubtitle = "Xem thông tin chi tiết tài khoản người dùng";
$pageIcon = "fas fa-user";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý người dùng', 'link' => '/pdu_pms_project/public/admin/manage_users'],
    ['title' => 'Chi tiết người dùng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();

// Lấy thông tin người dùng từ controller
$user = $data['user'] ?? null;

if (!$user) {
    echo '<div class="alert alert-danger">Không tìm thấy thông tin người dùng</div>';
    $pageContent = ob_get_clean();
    $pageRole = 'admin';
    include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
    exit;
}
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>
    
    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/manage_users" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
        <a href="/pdu_pms_project/public/admin/edit_user?id=<?= $user['id'] ?>" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Chỉnh sửa
        </a>
    </div>
    
    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-id-card me-2"></i>Thông tin cơ bản</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3" style="width: 100px; height: 100px; background-color: #4e73df; color: white; font-size: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <?= substr(htmlspecialchars($user['full_name'] ?? $user['username'] ?? 'U'), 0, 1); ?>
                        </div>
                        <h5 class="fw-bold"><?= htmlspecialchars($user['full_name'] ?? $user['username'] ?? '') ?></h5>
                        <p class="text-muted">
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'teacher' ? 'primary' : 'success') ?>">
                                <?= $user['role'] === 'admin' ? 'Quản trị viên' : ($user['role'] === 'teacher' ? 'Giáo viên' : 'Sinh viên') ?>
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tên đăng nhập</label>
                        <div class="fw-medium"><?= htmlspecialchars($user['username'] ?? '') ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small">Email</label>
                        <div class="fw-medium"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                    </div>
                    
                    <?php if ($user['role'] === 'student' && !empty($user['class_code'])): ?>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Mã lớp</label>
                        <div class="fw-medium"><?= htmlspecialchars($user['class_code']) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small">Trạng thái</label>
                        <div>
                            <span class="badge bg-<?= ($user['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                <?= ($user['status'] ?? 'active') === 'active' ? 'Hoạt động' : 'Vô hiệu hóa' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hoạt động gần đây -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-history me-2"></i>Hoạt động gần đây</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($data['activities']) && !empty($data['activities'])): ?>
                        <div class="timeline">
                            <?php foreach ($data['activities'] as $activity): ?>
                                <div class="timeline-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <div class="bg-light rounded-circle p-2">
                                                <i class="fas fa-calendar-check text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?= htmlspecialchars($activity['action'] ?? '') ?></div>
                                            <div class="text-muted small"><?= isset($activity['timestamp']) ? date('d/m/Y H:i', strtotime($activity['timestamp'])) : '' ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5>Không có hoạt động nào</h5>
                            <p class="text-muted">Người dùng này chưa có hoạt động nào được ghi lại</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Lịch đặt phòng -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-calendar-alt me-2"></i>Lịch đặt phòng gần đây</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($data['bookings']) && !empty($data['bookings'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Phòng</th>
                                        <th>Thời gian bắt đầu</th>
                                        <th>Thời gian kết thúc</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['bookings'] as $booking): ?>
                                        <tr>
                                            <td><?= $booking['id'] ?></td>
                                            <td><?= htmlspecialchars($booking['room_name'] ?? '') ?></td>
                                            <td><?= isset($booking['start_time']) ? date('d/m/Y H:i', strtotime($booking['start_time'])) : '' ?></td>
                                            <td><?= isset($booking['end_time']) ? date('d/m/Y H:i', strtotime($booking['end_time'])) : '' ?></td>
                                            <td>
                                                <span class="badge bg-<?= $booking['status'] === 'được duyệt' ? 'success' : ($booking['status'] === 'chờ duyệt' ? 'warning' : 'danger') ?>">
                                                    <?= htmlspecialchars($booking['status'] ?? '') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>Không có lịch đặt phòng</h5>
                            <p class="text-muted">Người dùng này chưa đặt phòng nào</p>
                        </div>
                    <?php endif; ?>
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
