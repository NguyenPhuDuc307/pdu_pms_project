<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Xếp lịch tự động";
$pageSubtitle = "Tự động xếp phòng học cho các lớp dựa trên các tiêu chí phù hợp";
$pageIcon = "fas fa-magic";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý lịch dạy', 'link' => '/pdu_pms_project/public/admin/manage_timetable'],
    ['title' => 'Xếp lịch tự động', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/manage_timetable" class="btn btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Quay lại
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Content Row - Stats -->
    <div class="row mb-4">
        <!-- Total Classes Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số lớp học</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($data['timetables'] ?? []) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unscheduled Classes Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chưa xếp phòng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($data['timetables'] ?? [], function ($timetable) {
                                    return empty($timetable['room_id']);
                                })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scheduled Classes Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đã xếp phòng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($data['timetables'] ?? [], function ($timetable) {
                                    return !empty($timetable['room_id']);
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

        <!-- Available Rooms Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Phòng khả dụng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['available_rooms'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-list me-2"></i>Danh sách thời khóa biểu cần xếp phòng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="autoScheduleTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Giảng viên</th>
                            <th>Mã lớp</th>
                            <th>Môn học</th>
                            <th>Thời gian bắt đầu</th>
                            <th>Thời gian kết thúc</th>
                            <th>Phòng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['timetables']) && is_array($data['timetables']) && count($data['timetables']) > 0): ?>
                            <?php foreach ($data['timetables'] as $timetable): ?>
                                <tr class="<?= empty($timetable['room_id']) ? 'table-warning' : '' ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle me-2">
                                                <?php
                                                $teacherName = $timetable['teacher_name'] ?? 'N/A';
                                                $initials = '';
                                                $parts = explode(' ', $teacherName);
                                                if (count($parts) >= 2) {
                                                    $initials = substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1);
                                                } else {
                                                    $initials = substr($teacherName, 0, 2);
                                                }
                                                echo strtoupper($initials);
                                                ?>
                                            </div>
                                            <?php echo htmlspecialchars($timetable['teacher_name'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($timetable['class_code'] ?? ''); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($timetable['subject'] ?? ''); ?></strong></td>
                                    <td><i class="far fa-calendar-alt me-1 text-muted"></i> <?php echo isset($timetable['start_time']) ? date('d/m/Y H:i', strtotime($timetable['start_time'])) : ''; ?></td>
                                    <td><i class="far fa-clock me-1 text-muted"></i> <?php echo isset($timetable['end_time']) ? date('d/m/Y H:i', strtotime($timetable['end_time'])) : ''; ?></td>
                                    <td>
                                        <?php if (isset($timetable['room_id']) && $timetable['room_id']): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-door-open me-1"></i>
                                                <?php
                                                $room = (new \Models\RoomModel())->getRoomById($timetable['room_id']);
                                                echo htmlspecialchars($room['name'] ?? 'Không xác định');
                                                ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                Chưa xếp phòng
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="/pdu_pms_project/public/admin/auto_schedule" class="d-inline">
                                            <input type="hidden" name="timetable_id" value="<?php echo $timetable['id'] ?? ''; ?>">
                                            <button type="submit" class="btn btn-sm btn-primary" <?= !empty($timetable['room_id']) ? 'disabled' : '' ?>>
                                                <i class="fas fa-magic me-1"></i> Xếp phòng
                                            </button>
                                        </form>

                                        <?php if (!empty($timetable['room_id'])): ?>
                                            <a href="/pdu_pms_project/public/admin/edit_timetable/<?php echo $timetable['id'] ?? ''; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit me-1"></i> Chỉnh sửa
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger cancel-room-btn" data-timetable-id="<?php echo $timetable['id'] ?? ''; ?>">
                                                <i class="fas fa-times-circle me-1"></i> Hủy xếp phòng
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có dữ liệu thời khóa biểu nào cần xếp phòng</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Card styles */
    .card.border-left-primary {
        border-left: .25rem solid #4e73df !important;
    }

    .card.border-left-success {
        border-left: .25rem solid #1cc88a !important;
    }

    .card.border-left-warning {
        border-left: .25rem solid #f6c23e !important;
    }

    .card.border-left-info {
        border-left: .25rem solid #36b9cc !important;
    }

    /* Avatar */
    .avatar-sm {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }
</style>

<!-- Script sẽ được thực thi sau khi jQuery được tải -->
<?php ob_start(); ?>
// Initialize DataTable
$(document).ready(function() {
$('#autoScheduleTable').DataTable({
language: {
// Sử dụng cấu hình trực tiếp thay vì tải từ URL để tránh lỗi CORS
emptyTable: "Không có dữ liệu trong bảng",
info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
infoEmpty: "Hiển thị 0 đến 0 của 0 mục",
infoFiltered: "(lọc từ _MAX_ mục)",
lengthMenu: "Hiển thị _MENU_ mục",
loadingRecords: "Đang tải...",
processing: "Đang xử lý...",
search: "Tìm kiếm:",
zeroRecords: "Không tìm thấy kết quả phù hợp",
paginate: {
first: "Đầu",
last: "Cuối",
next: "Tiếp",
previous: "Trước"
}
},
order: [
[0, 'desc']
]
});

// Bulk auto schedule button
$('#autoScheduleAllBtn').click(function() {
if (confirm('Bạn có chắc chắn muốn xếp tự động cho tất cả lớp học chưa có phòng? Quá trình này có thể mất vài phút.')) {
window.location.href = '/pdu_pms_project/public/admin/auto_schedule_all';
}
});

// Cancel room schedule button
$('.cancel-room-btn').click(function() {
const timetableId = $(this).data('timetable-id');
if (confirm('Bạn có chắc chắn muốn hủy xếp phòng cho lịch dạy này?')) {
// Gửi AJAX request đến endpoint hủy xếp phòng
$.ajax({
url: '/pdu_pms_project/public/admin/cancel_room_schedule',
type: 'POST',
data: {
timetable_id: timetableId
},
dataType: 'json',
success: function(response) {
if (response.success) {
alert('Hủy xếp phòng thành công!');
location.reload(); // Tải lại trang để cập nhật trạng thái
} else {
alert('Lỗi: ' + response.message);
}
},
error: function() {
alert('Có lỗi xảy ra khi hủy xếp phòng. Vui lòng thử lại sau.');
}
});
}
});
});
<?php $pageScripts = ob_get_clean(); ?>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>