<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Tìm kiếm phòng";
$pageSubtitle = "Tìm kiếm và quản lý phòng học trong hệ thống";
$pageIcon = "fas fa-search";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Tìm kiếm phòng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/add_room" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> Thêm phòng mới
        </a>

    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="/pdu_pms_project/public/admin/search_rooms" method="post" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Tên phòng</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= isset($searchParams['name']) ? htmlspecialchars($searchParams['name']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="room_type_id">Loại phòng</label>
                            <select class="form-control" id="room_type_id" name="room_type_id">
                                <option value="">Tất cả loại phòng</option>
                                <?php foreach ($roomTypes as $roomType): ?>
                                    <option value="<?= $roomType['id'] ?>" <?= (isset($searchParams['room_type_id']) && $searchParams['room_type_id'] == $roomType['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($roomType['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="trống" <?= (isset($searchParams['status']) && $searchParams['status'] == 'trống') ? 'selected' : '' ?>>Trống</option>
                                <option value="đang sử dụng" <?= (isset($searchParams['status']) && $searchParams['status'] == 'đang sử dụng') ? 'selected' : '' ?>>Đang sử dụng</option>
                                <option value="bảo trì" <?= (isset($searchParams['status']) && $searchParams['status'] == 'bảo trì') ? 'selected' : '' ?>>Bảo trì</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="min_capacity">Sức chứa tối thiểu</label>
                            <input type="number" class="form-control" id="min_capacity" name="min_capacity" min="1"
                                value="<?= isset($searchParams['min_capacity']) ? intval($searchParams['min_capacity']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="location">Vị trí</label>
                            <input type="text" class="form-control" id="location" name="location"
                                value="<?= isset($searchParams['location']) ? htmlspecialchars($searchParams['location']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Tìm kiếm
                        </button>
                        <a href="/pdu_pms_project/public/admin/search_rooms" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($_GET['message']) ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên phòng</th>
                            <th>Loại phòng</th>
                            <th>Sức chứa</th>
                            <th>Vị trí</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rooms)): ?>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= $room['id'] ?></td>
                                    <td><?= htmlspecialchars($room['name']) ?></td>
                                    <td><?= htmlspecialchars($room['room_type_name']) ?></td>
                                    <td><?= intval($room['capacity']) ?> người</td>
                                    <td><?= htmlspecialchars($room['location']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $room['status'] == 'trống' ? 'success' : ($room['status'] == 'đang sử dụng' ? 'warning' : 'danger') ?>">
                                            <?= htmlspecialchars($room['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="/pdu_pms_project/public/admin/edit_room/<?= $room['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="/pdu_pms_project/public/admin/room_detail/<?= $room['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-info-circle"></i> Chi tiết
                                            </a>
                                            <a href="#" onclick="confirmDelete(<?= $room['id'] ?>, '<?= htmlspecialchars($room['name']) ?>')" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không tìm thấy phòng phù hợp với tiêu chí tìm kiếm</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, name) {
            if (confirm('Bạn có chắc chắn muốn xóa phòng "' + name + '" không?')) {
                window.location.href = '/pdu_pms_project/public/admin/delete_room/' + id;
            }
        }
    </script>
</div>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>