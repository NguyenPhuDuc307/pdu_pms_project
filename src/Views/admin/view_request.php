<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Chi tiết yêu cầu bảo trì";
$pageSubtitle = "Xem và cập nhật thông tin yêu cầu bảo trì";
$pageIcon = "fas fa-tools";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Yêu cầu bảo trì', 'link' => '/pdu_pms_project/public/admin/maintenance_requests'],
    ['title' => 'Chi tiết yêu cầu', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/maintenance_requests" class="btn btn-primary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <?php if (isset($data['request']) && $data['request']): ?>
        <div class="row">
            <div class="col-lg-8">
                <!-- Thông tin yêu cầu -->
                <div class="card shadow mb-4 rounded">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin yêu cầu bảo trì #<?= $data['request']['id'] ?></h6>
                        <span class="badge bg-<?= getStatusBadgeColor($data['request']['status']) ?>">
                            <?= htmlspecialchars(ucfirst($data['request']['status'])) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Thông tin cơ bản</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 150px;">Phòng:</th>
                                        <td><?= htmlspecialchars($data['request']['room_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Thiết bị:</th>
                                        <td><?= $data['request']['equipment_name'] ? htmlspecialchars($data['request']['equipment_name']) : '<span class="text-muted">Không có thiết bị cụ thể</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Người yêu cầu:</th>
                                        <td>
                                            <?= htmlspecialchars($data['request']['user_name']) ?>
                                            <span class="badge bg-info"><?= htmlspecialchars(ucfirst($data['request']['user_role'])) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Mức độ ưu tiên:</th>
                                        <td>
                                            <span class="badge bg-<?= getPriorityBadgeColor($data['request']['priority']) ?>">
                                                <?= htmlspecialchars(ucfirst($data['request']['priority'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Thời gian</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 150px;">Ngày tạo:</th>
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($data['request']['created_at']))) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ngày xử lý:</th>
                                        <td>
                                            <?= $data['request']['resolved_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['request']['resolved_at']))) : '<span class="text-muted">Chưa xử lý</span>' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Trạng thái:</th>
                                        <td>
                                            <span class="badge bg-<?= getStatusBadgeColor($data['request']['status']) ?>">
                                                <?= htmlspecialchars(ucfirst($data['request']['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="font-weight-bold">Mô tả vấn đề</h6>
                            <div class="p-3 bg-light rounded">
                                <?= nl2br(htmlspecialchars($data['request']['issue_description'])) ?>
                            </div>
                        </div>

                        <?php if (!empty($data['request']['admin_notes'])): ?>
                            <div class="mb-4">
                                <h6 class="font-weight-bold">Ghi chú của admin</h6>
                                <div class="p-3 bg-light rounded">
                                    <?= nl2br(htmlspecialchars($data['request']['admin_notes'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Form cập nhật trạng thái -->
                        <form action="/pdu_pms_project/public/admin/update_request_status" method="post">
                            <input type="hidden" name="id" value="<?= $data['request']['id'] ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Cập nhật trạng thái</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="đang chờ" <?= $data['request']['status'] === 'đang chờ' ? 'selected' : '' ?>>Đang chờ</option>
                                        <option value="đang xử lý" <?= $data['request']['status'] === 'đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
                                        <option value="đã xử lý" <?= $data['request']['status'] === 'đã xử lý' ? 'selected' : '' ?>>Đã xử lý</option>
                                        <option value="từ chối" <?= $data['request']['status'] === 'từ chối' ? 'selected' : '' ?>>Từ chối</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="admin_notes" class="form-label">Ghi chú của admin</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"><?= htmlspecialchars($data['request']['admin_notes'] ?? '') ?></textarea>
                                <div class="form-text">Ghi chú này sẽ hiển thị cho người dùng.</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/pdu_pms_project/public/admin/maintenance_requests" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                                </a>
                                <div>
                                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteRequestModal">
                                        <i class="fas fa-trash me-1"></i> Xóa yêu cầu
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Cập nhật
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Thông tin phòng -->
                <div class="card shadow mb-4 rounded">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin phòng</h6>
                    </div>
                    <div class="card-body">
                        <h5><?= htmlspecialchars($data['request']['room_name']) ?></h5>
                        <p class="text-muted">ID: <?= htmlspecialchars($data['request']['room_id']) ?></p>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="/pdu_pms_project/public/admin/room_detail/<?= $data['request']['room_id'] ?>" class="btn btn-outline-primary">
                                <i class="fas fa-info-circle me-1"></i> Xem chi tiết phòng
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Lịch sử yêu cầu của phòng -->
                <div class="card shadow mb-4 rounded">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Lịch sử yêu cầu của phòng</h6>
                    </div>
                    <div class="card-body">
                        <!-- Hiển thị lịch sử yêu cầu của phòng này -->
                        <p class="text-center text-muted">Chức năng đang phát triển</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>Không tìm thấy thông tin yêu cầu bảo trì
        </div>
    <?php endif; ?>
</div>

<!-- Modal Xóa Yêu Cầu -->
<div class="modal fade" id="deleteRequestModal" tabindex="-1" aria-labelledby="deleteRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRequestModalLabel">Xác nhận xóa yêu cầu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa yêu cầu bảo trì này?</p>
                <p class="text-danger">Lưu ý: Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="/pdu_pms_project/public/admin/delete_request?id=<?= $data['request']['id'] ?>" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>

<?php
// Hàm hỗ trợ
function getStatusBadgeColor($status)
{
    switch ($status) {
        case 'đang chờ':
            return 'warning';
        case 'đang xử lý':
            return 'info';
        case 'đã xử lý':
            return 'success';
        case 'từ chối':
            return 'danger';
        default:
            return 'secondary';
    }
}

function getPriorityBadgeColor($priority)
{
    switch ($priority) {
        case 'khẩn cấp':
            return 'danger';
        case 'cao':
            return 'warning';
        case 'trung bình':
            return 'info';
        case 'thấp':
            return 'secondary';
        default:
            return 'secondary';
    }
}
?>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>
