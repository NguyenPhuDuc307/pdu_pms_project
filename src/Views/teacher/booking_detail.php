<?php
// Đảm bảo chỉ cho teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Chi tiết đặt phòng";
$pageSubtitle = "Xem thông tin chi tiết về lịch đặt phòng";
$pageIcon = "fas fa-info-circle";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/teacher'],
    ['title' => 'Lịch đặt phòng', 'link' => '/pdu_pms_project/public/teacher/calendar_bookings'],
    ['title' => 'Chi tiết đặt phòng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Thông tin đặt phòng -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin đặt phòng #<?php echo $data['booking']['id']; ?></h6>
                    <div>
                        <a href="/pdu_pms_project/public/teacher/calendar_bookings" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                        <?php if (strtolower($data['booking']['status']) === 'chờ duyệt' || strtolower($data['booking']['status']) === 'pending'): ?>
                            <a href="/pdu_pms_project/public/teacher/cancel_booking/<?php echo $data['booking']['id']; ?>" class="btn btn-danger btn-sm ms-2 cancel-booking">
                                <i class="fas fa-times me-1"></i> Hủy đặt phòng
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Thông tin cơ bản</h5>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Mã đặt phòng:</p>
                                <p class="fw-bold">#<?php echo $data['booking']['id']; ?></p>
                            </div>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Trạng thái:</p>
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                $statusIcon = '';

                                switch (strtolower($data['booking']['status'])) {
                                    case 'chờ duyệt':
                                    case 'pending':
                                        $statusClass = 'warning';
                                        $statusText = 'Chờ duyệt';
                                        $statusIcon = 'clock';
                                        break;
                                    case 'được duyệt':
                                    case 'đã duyệt':
                                    case 'approved':
                                        $statusClass = 'success';
                                        $statusText = 'Đã duyệt';
                                        $statusIcon = 'check-circle';
                                        break;
                                    case 'từ chối':
                                    case 'rejected':
                                        $statusClass = 'danger';
                                        $statusText = 'Từ chối';
                                        $statusIcon = 'times-circle';
                                        break;
                                    case 'đã hủy':
                                    case 'cancelled':
                                        $statusClass = 'secondary';
                                        $statusText = 'Đã hủy';
                                        $statusIcon = 'ban';
                                        break;
                                    default:
                                        $statusClass = 'secondary';
                                        $statusText = $data['booking']['status'];
                                        $statusIcon = 'question-circle';
                                }
                                ?>
                                <p><span class="badge bg-<?php echo $statusClass; ?> fs-6">
                                        <i class="fas fa-<?php echo $statusIcon; ?> me-1"></i> <?php echo $statusText; ?>
                                    </span></p>
                            </div>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Người đặt:</p>
                                <p>
                                    <?php
                                    $role = isset($data['booking']['user_role']) ? $data['booking']['user_role'] : 'teacher';
                                    $roleName = ($role === 'teacher') ? 'Giảng viên' : 'Sinh viên';
                                    echo $roleName . ': ' . (isset($data['booking']['user_name']) ? htmlspecialchars($data['booking']['user_name']) : '');
                                    ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Ngày đặt:</p>
                                <p><?php echo date('d/m/Y H:i', strtotime($data['booking']['created_at'])); ?></p>
                            </div>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Mã lớp:</p>
                                <p><?php echo htmlspecialchars($data['booking']['class_code']); ?></p>
                            </div>
                            <?php if (!empty($data['booking']['purpose'])): ?>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Mục đích sử dụng:</p>
                                    <p><?php echo htmlspecialchars($data['booking']['purpose']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Thời gian</h5>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Thời gian bắt đầu:</p>
                                <p class="d-flex align-items-center">
                                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                                    <?php echo date('d/m/Y', strtotime($data['booking']['start_time'])); ?>
                                    <i class="far fa-clock ms-3 me-2 text-primary"></i>
                                    <?php echo date('H:i', strtotime($data['booking']['start_time'])); ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Thời gian kết thúc:</p>
                                <p class="d-flex align-items-center">
                                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                                    <?php echo date('d/m/Y', strtotime($data['booking']['end_time'])); ?>
                                    <i class="far fa-clock ms-3 me-2 text-primary"></i>
                                    <?php echo date('H:i', strtotime($data['booking']['end_time'])); ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Tổng thời gian:</p>
                                <?php
                                $start = new DateTime($data['booking']['start_time']);
                                $end = new DateTime($data['booking']['end_time']);
                                $interval = $start->diff($end);

                                $hours = $interval->h + ($interval->days * 24);
                                $minutes = $interval->i;

                                $durationText = '';
                                if ($hours > 0) {
                                    $durationText .= $hours . ' giờ ';
                                }
                                if ($minutes > 0 || $hours == 0) {
                                    $durationText .= $minutes . ' phút';
                                }
                                ?>
                                <p><span class="badge bg-info"><?php echo $durationText; ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Thông tin phòng -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin phòng</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-door-open fa-3x text-primary"></i>
                        </div>
                        <h5 class="font-weight-bold"><?php echo htmlspecialchars($data['booking']['room_name']); ?></h5>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1 text-muted">Sức chứa:</p>
                        <p><i class="fas fa-users me-2 text-primary"></i> <?php echo $data['room']['capacity'] ?? 'N/A'; ?> người</p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1 text-muted">Trạng thái phòng:</p>
                        <p>
                            <?php if (isset($data['room']['status'])): ?>
                                <?php
                                $roomStatusClass = '';
                                $roomStatusText = '';

                                switch (strtolower($data['room']['status'])) {
                                    case 'trống':
                                        $roomStatusClass = 'success';
                                        $roomStatusText = 'Trống';
                                        break;
                                    case 'đang sử dụng':
                                        $roomStatusClass = 'warning';
                                        $roomStatusText = 'Đang sử dụng';
                                        break;
                                    case 'bảo trì':
                                        $roomStatusClass = 'danger';
                                        $roomStatusText = 'Đang bảo trì';
                                        break;
                                    default:
                                        $roomStatusClass = 'secondary';
                                        $roomStatusText = $data['room']['status'];
                                }
                                ?>
                                <span class="badge bg-<?php echo $roomStatusClass; ?>"><?php echo $roomStatusText; ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Không xác định</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if (isset($data['room']['equipment']) && !empty($data['room']['equipment'])): ?>
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Thiết bị:</p>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($data['room']['equipment'] as $equipment): ?>
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span><?php echo htmlspecialchars($equipment['name']); ?></span>
                                        <span class="badge bg-primary rounded-pill"><?php echo $equipment['quantity']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý xác nhận hủy đặt phòng
        const cancelButtons = document.querySelectorAll('.cancel-booking');
        if (cancelButtons) {
            cancelButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Bạn có chắc chắn muốn hủy đặt phòng này không?')) {
                        e.preventDefault();
                    }
                });
            });
        }
    });
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'teacher';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>