<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Quản lý người dùng";
$pageSubtitle = "Quản lý tài khoản và quyền truy cập trong hệ thống";
$pageIcon = "fas fa-users";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý người dùng', 'link' => '']
];

// Helper functions for user display
function getRoleBadgeColor($role)
{
    switch ($role) {
        case 'admin':
            return 'danger';
        case 'teacher':
            return 'primary';
        case 'student':
            return 'success';
        default:
            return 'secondary';
    }
}

function getAvatarColor($role)
{
    switch ($role) {
        case 'admin':
            return 'admin';
        case 'teacher':
            return 'teacher';
        case 'student':
            return 'student';
        default:
            return 'secondary';
    }
}

function translateRole($role)
{
    switch ($role) {
        case 'admin':
            return 'Quản trị viên';
        case 'teacher':
            return 'Giảng viên';
        case 'student':
            return 'Sinh viên';
        default:
            return 'Không xác định';
    }
}

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../../components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/add_user" class="btn btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Thêm người dùng
        </a>
    </div>

    <!-- Content Row - User Stats -->
    <div class="row mb-4">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng người dùng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($data['users'] ?? []) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Quản trị viên</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($data['users'] ?? [], function ($user) {
                                    return $user['role'] === 'admin';
                                })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Giảng viên</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($data['users'] ?? [], function ($user) {
                                    return $user['role'] === 'teacher';
                                })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Sinh viên</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($data['users'] ?? [], function ($user) {
                                    return $user['role'] === 'student';
                                })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Bộ lọc người dùng</h6>
        </div>
        <div class="card-body py-3">
            <form id="userFilterForm" class="row g-3">
                <div class="col-md-4">
                    <label for="searchKeyword" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="searchKeyword" placeholder="Nhập tên, username...">
                </div>
                <div class="col-md-3">
                    <label for="roleFilter" class="form-label">Vai trò</label>
                    <select class="form-select" id="roleFilter">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin">Quản trị viên</option>
                        <option value="teacher">Giảng viên</option>
                        <option value="student">Sinh viên</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sortBy" class="form-label">Sắp xếp theo</label>
                    <select class="form-select" id="sortBy">
                        <option value="id">ID</option>
                        <option value="username">Tên đăng nhập</option>
                        <option value="full_name">Tên người dùng</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="resetFilter" class="btn btn-secondary w-100">
                        <i class="fas fa-redo me-1"></i> Đặt lại
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-list me-2"></i>Danh sách người dùng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="usersTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Tên người dùng</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['users']) && is_array($data['users']) && count($data['users']) > 0): ?>
                            <?php foreach ($data['users'] as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id'] ?? ''); ?></td>
                                    <td><?= htmlspecialchars($user['username'] ?? ''); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2 bg-<?= getAvatarColor($user['role'] ?? 'student') ?>">
                                                <?= substr(htmlspecialchars($user['full_name'] ?? 'U'), 0, 1); ?>
                                            </div>
                                            <?= htmlspecialchars($user['full_name'] ?? ''); ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($user['email'] ?? ''); ?></td>
                                    <td>
                                        <span class="badge bg-<?= getRoleBadgeColor($user['role'] ?? '') ?>">
                                            <?= translateRole($user['role'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= ($user['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                            <?= ($user['status'] ?? 'active') === 'active' ? 'Hoạt động' : 'Vô hiệu hóa' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/pdu_pms_project/public/admin/edit_user?id=<?= $user['id'] ?? '' ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/pdu_pms_project/public/admin/view_user?id=<?= $user['id'] ?? '' ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/pdu_pms_project/public/admin/delete_user?id=<?= $user['id'] ?? '' ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa người dùng <?= htmlspecialchars($user['full_name'] ?? $user['username'] ?? '') ?>?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu người dùng</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
    }

    .bg-admin {
        background-color: #dc3545;
    }

    .bg-teacher {
        background-color: #0d6efd;
    }

    .bg-student {
        background-color: #198754;
    }

    /* Add colored left border to card */
    .card.border-left-primary {
        border-left: .25rem solid #4e73df !important;
    }

    .card.border-left-success {
        border-left: .25rem solid #1cc88a !important;
    }

    .card.border-left-info {
        border-left: .25rem solid #36b9cc !important;
    }

    .card.border-left-danger {
        border-left: .25rem solid #e74a3b !important;
    }
</style>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        const table = $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
            },
            order: [
                [0, 'desc']
            ]
        });

        // Filter functionality
        $('#searchKeyword').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#roleFilter').on('change', function() {
            const role = $(this).val();
            table.column(4).search(role).draw();
        });

        $('#sortBy').on('change', function() {
            const columnIndex = {
                'id': 0,
                'username': 1,
                'full_name': 2
            } [$(this).val()];

            table.order([columnIndex, 'asc']).draw();
        });

        $('#resetFilter').on('click', function() {
            $('#searchKeyword').val('');
            $('#roleFilter').val('');
            $('#sortBy').val('id');
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

</div> <!-- Close admin-content div from admin_sidebar.php -->

<?php // Footer is already included by admin_layout.php
?>