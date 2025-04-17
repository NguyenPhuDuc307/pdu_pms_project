<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Quản lý đặt phòng";
$pageSubtitle = "Quản lý và duyệt các yêu cầu đặt phòng từ giảng viên và sinh viên";
$pageIcon = "fas fa-calendar-check";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý đặt phòng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group">
            <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-primary active">
                <i class="fas fa-table me-1"></i> Dạng bảng
            </a>
            <a href="/pdu_pms_project/public/admin/calendar_bookings" class="btn btn-outline-primary">
                <i class="fas fa-calendar-alt me-1"></i> Dạng lịch
            </a>
        </div>
        <a href="/pdu_pms_project/public/admin/add_booking" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Thêm đặt phòng
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <form action="/pdu_pms_project/public/admin/manage_bookings" method="get" class="row g-3">
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $data['filters']['start_date'] ?? '' ?>" placeholder="Từ ngày">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $data['filters']['end_date'] ?? '' ?>" placeholder="Đến ngày">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">Tất cả người dùng</option>
                            <?php if (isset($data['users']) && is_array($data['users'])): ?>
                                <?php foreach ($data['users'] as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= (isset($data['filters']['user_id']) && $data['filters']['user_id'] == $user['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['fullname'] ?? $user['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="status" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" <?= isset($data['filters']['status']) && $data['filters']['status'] === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                            <option value="approved" <?= isset($data['filters']['status']) && $data['filters']['status'] === 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
                            <option value="rejected" <?= isset($data['filters']['status']) && $data['filters']['status'] === 'rejected' ? 'selected' : '' ?>>Từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Lọc
                        </button>
                        <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-light border">
                            <i class="fas fa-redo me-1"></i>
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <select class="form-select form-select-sm d-inline-block w-auto" id="length-change">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div>
                        <input type="search" class="form-control form-control-sm d-inline-block w-auto" placeholder="Nhập tìm kiếm..." id="table-search">
                    </div>
                </div>
                <table class="table table-striped table-hover" id="bookingsTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Người đặt</th>
                            <th>Phòng</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['bookings']) && is_array($data['bookings'])): ?>
                            <?php foreach ($data['bookings'] as $booking): ?>
                                <tr>
                                    <td><?= $booking['id'] ?? '' ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $initials = '';
                                            $fullname = '';

                                            // Ưu tiên hiển thị tên giáo viên, nếu không có thì hiển thị tên sinh viên
                                            if (!empty($booking['teacher_id'])) {
                                                $fullname = $booking['teacher_fullname'] ?? $booking['teacher_name'];
                                                $role = 'Giáo viên';
                                            } elseif (!empty($booking['student_id'])) {
                                                $fullname = $booking['student_fullname'] ?? $booking['student_name'];
                                                $role = 'Sinh viên';
                                            }

                                            $nameParts = explode(' ', $fullname);
                                            if (count($nameParts) > 0) {
                                                $lastName = end($nameParts);
                                                $initials = mb_substr($lastName, 0, 1, 'UTF-8');
                                            }

                                            $bgColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
                                            $colorIndex = isset($booking['id']) ? $booking['id'] % count($bgColors) : 0;
                                            $bgColor = $bgColors[$colorIndex];
                                            ?>
                                            <div class="avatar-sm me-2" style="background-color: <?= $bgColor ?>;"><?= $initials ?></div>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($fullname) ?></div>
                                                <small class="text-muted"><?= $role ?? 'Người dùng' ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($booking['room_name'] ?? '') ?></td>
                                    <td><?= isset($booking['start_time']) ? date('d/m/Y H:i', strtotime($booking['start_time'])) : '' ?></td>
                                    <td><?= isset($booking['end_time']) ? date('d/m/Y H:i', strtotime($booking['end_time'])) : '' ?></td>
                                    <td>
                                        <?php
                                        $status = strtolower($booking['status'] ?? '');
                                        switch ($status) {
                                            case 'pending':
                                            case 'chờ duyệt':
                                                echo '<span class="badge bg-warning">Chờ duyệt</span>';
                                                break;
                                            case 'approved':
                                            case 'được duyệt':
                                                echo '<span class="badge bg-success">Đã duyệt</span>';
                                                break;
                                            case 'rejected':
                                            case 'từ chối':
                                                echo '<span class="badge bg-danger">Từ chối</span>';
                                                break;
                                            case 'cancelled':
                                            case 'đã hủy':
                                                echo '<span class="badge bg-secondary">Đã hủy</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-secondary">' . htmlspecialchars($booking['status'] ?? 'Không xác định') . '</span>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/pdu_pms_project/public/admin/edit_booking/<?= $booking['id'] ?? '' ?>" class="btn btn-sm btn-light border" data-bs-toggle="tooltip" title="Chỉnh sửa đặt phòng">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (($booking['status'] ?? '') == 'pending'): ?>
                                                <a href="/pdu_pms_project/public/admin/approve_booking/<?= $booking['id'] ?? '' ?>" class="btn btn-sm btn-light border" data-bs-toggle="tooltip" title="Duyệt đặt phòng">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="/pdu_pms_project/public/admin/reject_booking/<?= $booking['id'] ?? '' ?>" class="btn btn-sm btn-light border" data-bs-toggle="tooltip" title="Từ chối đặt phòng">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-light border delete-booking"
                                                data-id="<?= $booking['id'] ?? '' ?>"
                                                data-room="<?= htmlspecialchars($booking['room_name'] ?? '') ?>"
                                                data-bs-toggle="tooltip" title="Xóa đặt phòng">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có đặt phòng nào được tìm thấy</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
    </style>

    <!-- DataTables đã được tải trong main_layout.php -->

    <!-- Script sẽ được thực thi sau khi jQuery được tải -->
    <?php ob_start(); ?>
    $(document).ready(function() {
    // Initialize DataTable
    var bookingsTable = $('#bookingsTable').DataTable({
    responsive: true,
    searching: true,
    lengthChange: true,
    pageLength: 10,
    language: {
    lengthMenu: "",
    search: "",
    zeroRecords: "Không tìm thấy dữ liệu phù hợp",
    info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
    infoEmpty: "Hiển thị 0 đến 0 của 0 mục",
    infoFiltered: "(lọc từ _MAX_ mục)",
    loadingRecords: "Đang tải...",
    processing: "Đang xử lý...",
    paginate: {
    first: "Đầu",
    last: "Cuối",
    next: "Tiếp",
    previous: "Trước"
    }
    },
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6">>rtip'
            });

            // Make length selector nicer
            $('.dataTables_length select').addClass('form-select-sm');

            // Remove the text "Hiển thị [entries]"
            $('.dataTables_length label').contents().filter(function() {
            return this.nodeType === 3; // Text node
            }).remove();

            // Connect custom search box with DataTables search
            $('#table-search').on('keyup', function() {
            bookingsTable.search(this.value).draw();
            });

            // Connect custom length menu with DataTables
            $('#length-change').on('change', function() {
            bookingsTable.page.len($(this).val()).draw();
            });



            // Handle deletion button outside modal
            $(document).on('click', '.delete-booking', function() {
            const bookingId = $(this).data('id');
            const roomName = $(this).data('room');

            if (confirm('Bạn có chắc chắn muốn xóa đặt phòng cho phòng ' + roomName + '?')) {
            // Sử dụng AJAX để xóa đặt phòng
            $.ajax({
            url: '/pdu_pms_project/public/admin/delete_booking/' + bookingId,
            type: 'GET',
            dataType: 'json',
            headers: {
            'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
            if (response.success) {
            alert(response.message);
            location.reload(); // Tải lại trang để cập nhật danh sách
            } else {
            alert('Lỗi: ' + response.message);
            }
            },
            error: function() {
            alert('Có lỗi xảy ra khi xóa đặt phòng. Vui lòng thử lại sau.');
            }
            });
            }
            });

            // Enable tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            });
            <?php $pageScripts = ob_get_clean(); ?>

</div>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>