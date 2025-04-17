<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Quản lý thiết bị";
$pageSubtitle = "Quản lý danh sách thiết bị và theo dõi tình trạng bảo trì";
$pageIcon = "fas fa-tools";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý thiết bị', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
            <i class="fas fa-plus me-1"></i> Thêm thiết bị mới
        </a>
    </div>

    <!-- Thiết bị cần bảo trì -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">Thiết bị cần bảo trì</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tên thiết bị</th>
                            <th>Phòng</th>
                            <th>Lần bảo trì cuối</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['maintenance_needed']) && is_array($data['maintenance_needed'])): ?>
                            <?php foreach ($data['maintenance_needed'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['equipment_name']) ?></td>
                                    <td><?= htmlspecialchars($item['room_name']) ?></td>
                                    <td><?= htmlspecialchars($item['last_maintenance'] ?? 'Chưa bảo trì') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $item['status'] === 'cần bảo trì' ? 'warning' : 'danger' ?>">
                                            <?= htmlspecialchars($item['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/pdu_pms_project/public/admin/room_equipments?room_id=<?= $item['room_id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Không có thiết bị nào cần bảo trì</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Danh sách thiết bị -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách thiết bị</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên thiết bị</th>
                            <th>Mô tả</th>
                            <th>Chu kỳ bảo trì (ngày)</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['equipments']) && is_array($data['equipments'])): ?>
                            <?php foreach ($data['equipments'] as $equipment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($equipment['id']) ?></td>
                                    <td><?= htmlspecialchars($equipment['name']) ?></td>
                                    <td><?= htmlspecialchars($equipment['description'] ?? 'Không có mô tả') ?></td>
                                    <td><?= htmlspecialchars($equipment['maintenance_period'] ?? 90) ?></td>
                                    <td><?= htmlspecialchars($equipment['created_at'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary edit-equipment"
                                            data-id="<?= $equipment['id'] ?>"
                                            data-name="<?= htmlspecialchars($equipment['name']) ?>"
                                            data-description="<?= htmlspecialchars($equipment['description'] ?? '') ?>"
                                            data-maintenance-period="<?= htmlspecialchars($equipment['maintenance_period'] ?? 90) ?>"
                                            data-bs-toggle="modal" data-bs-target="#editEquipmentModal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger delete-equipment"
                                            data-id="<?= $equipment['id'] ?>"
                                            data-name="<?= htmlspecialchars($equipment['name']) ?>"
                                            data-bs-toggle="modal" data-bs-target="#deleteEquipmentModal">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Thêm Thiết Bị -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEquipmentModalLabel">Thêm thiết bị mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEquipmentForm" action="/pdu_pms_project/public/admin/add_equipment" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thiết bị</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="maintenance_period" class="form-label">Chu kỳ bảo trì (ngày)</label>
                        <input type="number" class="form-control" id="maintenance_period" name="maintenance_period" value="90" min="1">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="addEquipmentForm" class="btn btn-primary">Thêm thiết bị</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Thiết Bị -->
<div class="modal fade" id="editEquipmentModal" tabindex="-1" aria-labelledby="editEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEquipmentModalLabel">Sửa thiết bị</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEquipmentForm" action="/pdu_pms_project/public/admin/edit_equipment" method="post">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Tên thiết bị</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_maintenance_period" class="form-label">Chu kỳ bảo trì (ngày)</label>
                        <input type="number" class="form-control" id="edit_maintenance_period" name="maintenance_period" min="1">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="editEquipmentForm" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xóa Thiết Bị -->
<div class="modal fade" id="deleteEquipmentModal" tabindex="-1" aria-labelledby="deleteEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEquipmentModalLabel">Xác nhận xóa thiết bị</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa thiết bị <strong id="delete_equipment_name"></strong>?</p>
                <p class="text-danger">Lưu ý: Hành động này không thể hoàn tác.</p>
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
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');
                const maintenancePeriod = this.getAttribute('data-maintenance-period');

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_description').value = description;
                document.getElementById('edit_maintenance_period').value = maintenancePeriod;
            });
        });

        // Xử lý sự kiện khi nhấn nút xóa thiết bị
        document.querySelectorAll('.delete-equipment').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('delete_equipment_name').textContent = name;
                document.getElementById('confirmDeleteBtn').href = '/pdu_pms_project/public/admin/delete_equipment?id=' + id;
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
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>