<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Quản lý phòng học";
$pageSubtitle = "Quản lý tất cả phòng học và phòng thực hành trong hệ thống";
$pageIcon = "fas fa-door-open";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý phòng học', 'link' => '']
];

// Helper functions for room display
function getRoomStatusBadgeColor($status)
{
    switch ($status) {
        case 'trống':
            return 'success';
        case 'đang sử dụng':
            return 'warning';
        case 'bảo trì':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../../components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/add_room" class="btn btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Thêm phòng
        </a>
    </div>

    <!-- Content Row - Room Stats -->
    <div class="row mb-4">
        <!-- Total Rooms Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số phòng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($data['rooms'] ?? []) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Rooms Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Phòng trống</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($data['rooms'] ?? [], function ($room) {
                                    return ($room['status'] ?? '') === 'trống';
                                })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Use Rooms Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Đang sử dụng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($data['rooms'] ?? [], function ($room) {
                                    return ($room['status'] ?? '') === 'đang sử dụng';
                                })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Rooms Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Bảo trì</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($data['rooms'] ?? [], function ($room) {
                                    return ($room['status'] ?? '') === 'bảo trì';
                                })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Bộ lọc phòng học</h6>
        </div>
        <div class="card-body py-3">
            <form id="roomFilterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="searchKeyword" class="form-label">Tìm kiếm theo tên phòng</label>
                    <input type="text" class="form-control" id="searchKeyword" placeholder="Nhập tên phòng">
                </div>

                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Trạng thái</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Tất cả trạng thái</option>
                        <option value="trống">Trống</option>
                        <option value="đang sử dụng">Đang sử dụng</option>
                        <option value="bảo trì">Bảo trì</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="resetFilter" class="btn btn-secondary w-100">
                        <i class="fas fa-redo me-1"></i> Đặt lại
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rooms Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-list me-2"></i>Danh sách phòng học</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="roomsTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên phòng</th>
                            <th>Loại phòng</th>
                            <th>Sức chứa</th>

                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['rooms']) && is_array($data['rooms']) && count($data['rooms']) > 0): ?>
                            <?php foreach ($data['rooms'] as $room): ?>
                                <tr>
                                    <td><?= $room['id'] ?? '' ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="room-icon me-2 bg-<?= getRoomStatusBadgeColor($room['status'] ?? '') ?>">
                                                <i class="fas fa-door-open"></i>
                                            </div>
                                            <strong><?= htmlspecialchars($room['name'] ?? '') ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            Phòng thực hành
                                        </span>
                                    </td>
                                    <td><i class="fas fa-users me-1 text-muted"></i> <?= ($room['capacity'] ?? 0) ?> người</td>

                                    <td>
                                        <span class="badge bg-<?= getRoomStatusBadgeColor($room['status'] ?? '') ?>">
                                            <?= htmlspecialchars($room['status'] ?? 'Không xác định') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/pdu_pms_project/public/admin/edit_room/<?= $room['id'] ?? '' ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/pdu_pms_project/public/admin/room_detail/<?= $room['id'] ?? '' ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            <a href="/pdu_pms_project/public/admin/room_detail/<?= $room['id'] ?? '' ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-tools"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="confirmDelete(<?= $room['id'] ?? 0 ?>, '<?= htmlspecialchars($room['name'] ?? '') ?>')" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu phòng học</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .room-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }

    .bg-success {
        background-color: #1cc88a;
    }

    .bg-warning {
        background-color: #f6c23e;
    }

    .bg-danger {
        background-color: #e74a3b;
    }

    /* Add colored left border to card */
    .card.border-left-primary {
        border-left: .25rem solid #4e73df !important;
    }

    .card.border-left-success {
        border-left: .25rem solid #1cc88a !important;
    }

    .card.border-left-warning {
        border-left: .25rem solid #f6c23e !important;
    }

    .card.border-left-danger {
        border-left: .25rem solid #e74a3b !important;
    }
</style>

<!-- Script xác nhận xóa và khởi tạo DataTable -->
<script>
    function confirmDelete(id, name) {
        if (confirm('Bạn có chắc chắn muốn xóa phòng "' + name + '"?')) {
            window.location.href = '/pdu_pms_project/public/admin/delete_room/' + id;
        }
    }

    // Initialize DataTable
    $(document).ready(function() {
        const table = $('#roomsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
            },
            order: [
                [0, 'desc']
            ]
        });

        // Filter functionality
        $('#searchKeyword').on('keyup', function() {
            table.column(1).search(this.value).draw();
        });



        $('#statusFilter').on('change', function() {
            const status = $(this).val();
            table.column(5).search(status).draw();
        });

        $('#resetFilter').on('click', function() {
            $('#searchKeyword').val('');
            $('#statusFilter').val('');
            table.search('').columns().search('').order([0, 'desc']).draw();
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