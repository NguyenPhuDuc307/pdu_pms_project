<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Chi tiết phòng: " . htmlspecialchars($data['room']['name']);
$pageSubtitle = "Thông tin chi tiết và quản lý thiết bị phòng";
$pageIcon = "fas fa-door-open";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý phòng', 'link' => '/pdu_pms_project/public/admin/manage_rooms'],
    ['title' => 'Chi tiết phòng', 'link' => '']
];

// Lấy thông tin phòng và thiết bị
$room = $data['room'];
$roomType = $data['roomType'] ?? null;
$upcomingBookings = $data['upcomingBookings'] ?? [];
$bookingStats = $data['bookingStats'] ?? [];
$usageData = $data['usageData'] ?? [];
$equipments = $data['equipments'] ?? [];
$roomEquipments = $room['equipment'] ?? [];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/edit_room/<?= $room['id'] ?>" class="btn btn-warning shadow-sm me-2">
            <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Chỉnh sửa
        </a>
        <a href="/pdu_pms_project/public/admin/manage_rooms" class="btn btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Quay lại
        </a>
    </div>

    <!-- Hiển thị thông báo -->
    <?php include dirname(dirname(dirname(__DIR__))) . '/Views/components/session_alerts.php'; ?>

    <div class="row">
        <!-- Room Information -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
                    <h6 class="m-0 fw-bold text-white">Thông tin phòng <?php echo $room['name']; ?></h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-door-open fa-5x text-primary mb-3"></i>
                        <h4 class="fw-bold"><?php echo htmlspecialchars($room['name']); ?></h4>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th style="width: 150px;"><i class="fas fa-hashtag me-2 text-primary"></i>Mã phòng:</th>
                                    <td><?= htmlspecialchars($room['id']) ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-users me-2 text-primary"></i>Số máy:</th>
                                    <td><?= htmlspecialchars($room['capacity']) ?> máy</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-check-circle me-2 text-primary"></i>Trạng thái:</th>
                                    <td>
                                        <span class="badge bg-<?= $room['status'] === 'trống' ? 'success' : ($room['status'] === 'đã đặt' ? 'warning' : 'danger') ?>">
                                            <?= htmlspecialchars($room['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if (!empty($room['notes'])): ?>
                                    <tr>
                                        <th><i class="fas fa-sticky-note me-2 text-primary"></i>Ghi chú:</th>
                                        <td><?= nl2br(htmlspecialchars($room['notes'])) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRoomModal">
                            <i class="fas fa-trash me-1"></i> Xóa phòng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách thiết bị trong phòng -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách thiết bị trong phòng</h6>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                <i class="fas fa-plus-circle me-1"></i> Thêm thiết bị
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($roomEquipments)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Phòng này chưa có thiết bị nào. Hãy thêm thiết bị bằng cách nhấn nút "Thêm thiết bị".
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên thiết bị</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                                <th>Bảo trì gần nhất</th>
                                <th>Bảo trì tiếp theo</th>
                                <th>Ghi chú</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roomEquipments as $equipment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($equipment['id']) ?></td>
                                    <td><?= htmlspecialchars($equipment['name']) ?></td>
                                    <td><?= htmlspecialchars($equipment['quantity']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $equipment['status'] === 'hoạt động' ? 'success' : ($equipment['status'] === 'cần bảo trì' ? 'warning' : 'danger') ?>">
                                            <?= htmlspecialchars($equipment['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($equipment['last_maintenance'] ?? 'Chưa bảo trì') ?></td>
                                    <td><?= htmlspecialchars($equipment['next_maintenance'] ?? 'Chưa xác định') ?></td>
                                    <td><?= htmlspecialchars($equipment['notes'] ?? '') ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-warning edit-equipment"
                                                data-id="<?= $equipment['id'] ?>"
                                                data-quantity="<?= $equipment['quantity'] ?>"
                                                data-status="<?= $equipment['status'] ?>"
                                                data-notes="<?= htmlspecialchars($equipment['notes'] ?? '') ?>"
                                                data-bs-toggle="modal" data-bs-target="#editEquipmentModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-equipment"
                                                data-id="<?= $equipment['id'] ?>"
                                                data-name="<?= htmlspecialchars($equipment['name']) ?>"
                                                data-bs-toggle="modal" data-bs-target="#deleteEquipmentModal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upcoming Bookings -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lịch đặt phòng sắp tới</h6>
        </div>
        <div class="card-body">
            <?php if (empty($upcomingBookings)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Không có lịch đặt phòng sắp tới.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Người đặt</th>
                                <th>Ngày</th>
                                <th>Thời gian</th>
                                <th>Mục đích</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingBookings as $booking): ?>
                                <tr>
                                    <td><?= htmlspecialchars($booking['user_name'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($booking['date'])) ?></td>
                                    <td><?= date('H:i', strtotime($booking['start_time'])) ?> - <?= date('H:i', strtotime($booking['end_time'])) ?></td>
                                    <td><?= htmlspecialchars($booking['purpose']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $booking['status'] === 'approved' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                            <?= $booking['status'] === 'approved' ? 'Đã duyệt' : ($booking['status'] === 'pending' ? 'Chờ duyệt' : 'Từ chối') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Xóa Phòng -->
<div class="modal fade" id="deleteRoomModal" tabindex="-1" aria-labelledby="deleteRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteRoomModalLabel">Xác nhận xóa phòng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa phòng <strong><?= htmlspecialchars($room['name']) ?></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> Lưu ý: Hành động này không thể hoàn tác và sẽ xóa tất cả dữ liệu liên quan đến phòng này!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Hủy
                </button>
                <a href="/pdu_pms_project/public/admin/delete_room/<?php echo $room['id']; ?>" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-1"></i> Xóa
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Thiết Bị -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEquipmentModalLabel">Thêm thiết bị vào phòng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEquipmentForm" action="/pdu_pms_project/public/admin/add_room_equipment" method="post">
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">

                    <div class="mb-3">
                        <label for="equipment_id" class="form-label">Thiết bị</label>
                        <select class="form-select" id="equipment_id" name="equipment_id" required>
                            <option value="">-- Chọn thiết bị --</option>
                            <?php foreach ($equipments as $equipment): ?>
                                <option value="<?= $equipment['id'] ?>"><?= htmlspecialchars($equipment['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Số lượng</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm thiết bị</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Thiết Bị -->
<div class="modal fade" id="editEquipmentModal" tabindex="-1" aria-labelledby="editEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEquipmentModalLabel">Cập nhật thiết bị</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEquipmentForm" action="/pdu_pms_project/public/admin/update_room_equipment" method="post">
                    <input type="hidden" id="edit_id" name="id">
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">

                    <div class="mb-3">
                        <label for="edit_quantity" class="form-label">Số lượng</label>
                        <input type="number" class="form-control" id="edit_quantity" name="quantity" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="hoạt động">Hoạt động</option>
                            <option value="cần bảo trì">Cần bảo trì</option>
                            <option value="hỏng">Hỏng</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xóa Thiết Bị -->
<div class="modal fade" id="deleteEquipmentModal" tabindex="-1" aria-labelledby="deleteEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteEquipmentModalLabel">Xác nhận xóa thiết bị</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa thiết bị <strong id="delete_equipment_name"></strong> khỏi phòng này?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> Lưu ý: Hành động này không thể hoàn tác!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý sự kiện khi nhấn nút sửa thiết bị
        document.querySelectorAll('.edit-equipment').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const quantity = this.getAttribute('data-quantity');
                const status = this.getAttribute('data-status');
                const notes = this.getAttribute('data-notes');

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_quantity').value = quantity;
                document.getElementById('edit_status').value = status;
                document.getElementById('edit_notes').value = notes;
            });
        });

        // Xử lý sự kiện khi nhấn nút xóa thiết bị
        document.querySelectorAll('.delete-equipment').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const roomId = <?= $room['id'] ?>;

                document.getElementById('delete_equipment_name').textContent = name;
                document.getElementById('confirmDeleteBtn').href = '/pdu_pms_project/public/admin/remove_room_equipment?id=' + id + '&room_id=' + roomId;
            });
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