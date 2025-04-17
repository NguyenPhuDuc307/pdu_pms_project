<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Chỉnh sửa phòng: " . htmlspecialchars($data['room']['name']);
$pageSubtitle = "Cập nhật thông tin phòng thực hành";
$pageIcon = "fas fa-edit";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý phòng', 'link' => '/pdu_pms_project/public/admin/manage_rooms'],
    ['title' => 'Chỉnh sửa phòng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/manage_rooms" class="btn btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Quay lại
        </a>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
                    <h6 class="m-0 fw-bold text-white">Thông tin phòng #<?php echo $data['room']['id']; ?></h6>
                </div>
                <div class="card-body">
                    <?php if (isset($data['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $data['error']; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $data['room']['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-door-open me-1"></i> Tên phòng</label>
                            <input type="text" name="name" value="<?php echo $data['room']['name']; ?>"
                                class="form-control rounded-3" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-users me-1"></i> Sức chứa</label>
                            <input type="number" name="capacity" value="<?php echo $data['room']['capacity']; ?>"
                                class="form-control rounded-3" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-info-circle me-1"></i> Trạng thái</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="trống" <?php echo $data['room']['status'] === 'trống' ? 'selected' : ''; ?>>
                                    <i class="fas fa-check-circle"></i> Trống
                                </option>
                                <option value="đã đặt" <?php echo $data['room']['status'] === 'đã đặt' ? 'selected' : ''; ?>>
                                    <i class="fas fa-calendar-check"></i> Đã đặt
                                </option>
                                <option value="bảo trì" <?php echo $data['room']['status'] === 'bảo trì' ? 'selected' : ''; ?>>
                                    <i class="fas fa-tools"></i> Bảo trì
                                </option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <button type="submit" name="edit_room" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-1"></i> Lưu thay đổi
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <a href="/pdu_pms_project/public/admin/manage_rooms" class="btn btn-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Room Details Preview -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info">
                    <h6 class="m-0 fw-bold text-white">Xem trước thông tin</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-door-open fa-5x text-primary mb-3"></i>
                        <h4 id="preview-name"><?php echo $data['room']['name']; ?></h4>
                    </div>

                    <div class="mb-4">
                        <div class="row align-items-center py-2 border-bottom">
                            <div class="col-5 text-muted">
                                <i class="fas fa-hashtag me-1"></i> ID Phòng:
                            </div>
                            <div class="col-7 fw-bold">
                                <?php echo $data['room']['id']; ?>
                            </div>
                        </div>

                        <div class="row align-items-center py-2 border-bottom">
                            <div class="col-5 text-muted">
                                <i class="fas fa-layer-group me-1"></i> Loại phòng:
                            </div>
                            <div class="col-7 fw-bold" id="preview-roomtype">
                                Phòng thực hành
                            </div>
                        </div>

                        <div class="row align-items-center py-2 border-bottom">
                            <div class="col-5 text-muted">
                                <i class="fas fa-users me-1"></i> Sức chứa:
                            </div>
                            <div class="col-7 fw-bold" id="preview-capacity">
                                <?php echo $data['room']['capacity']; ?> người
                            </div>
                        </div>

                        <div class="row align-items-center py-2 border-bottom">
                            <div class="col-5 text-muted">
                                <i class="fas fa-info-circle me-1"></i> Trạng thái:
                            </div>
                            <div class="col-7">
                                <span id="preview-status" class="badge bg-<?php
                                                                            echo $data['room']['status'] === 'trống' ? 'success' : ($data['room']['status'] === 'đã đặt' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst($data['room']['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i> Thông tin sẽ cập nhật sau khi lưu thay đổi.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.querySelector('input[name="name"]');
        const capacityInput = document.querySelector('input[name="capacity"]');
        const statusSelect = document.querySelector('select[name="status"]');
        const previewName = document.getElementById('preview-name');
        const previewCapacity = document.getElementById('preview-capacity');
        const previewStatus = document.getElementById('preview-status');

        // Update preview when inputs change
        nameInput.addEventListener('input', function() {
            previewName.textContent = this.value;
        });

        capacityInput.addEventListener('input', function() {
            previewCapacity.textContent = this.value + ' người';
        });

        statusSelect.addEventListener('change', function() {
            previewStatus.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);

            // Update badge color for Bootstrap 5
            previewStatus.className = 'badge bg-' +
                (this.value === 'trống' ? 'success' :
                    (this.value === 'đã đặt' ? 'warning' : 'danger'));
        });
    });
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>