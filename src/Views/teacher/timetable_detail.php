<?php
// Đảm bảo chỉ cho teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Chi tiết lịch dạy";
$pageSubtitle = "Xem thông tin chi tiết về lịch dạy";
$pageIcon = "fas fa-calendar-alt";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/teacher'],
    ['title' => 'Lịch dạy của tôi', 'link' => '/pdu_pms_project/public/teacher/my_timetables'],
    ['title' => 'Chi tiết lịch dạy', 'link' => '']
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

    <?php if (isset($data['timetable'])): ?>
        <div class="row">
            <div class="col-lg-8">
                <!-- Thông tin lịch dạy -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin lịch dạy #<?php echo $data['timetable']['id']; ?></h6>
                        <div>
                            <a href="/pdu_pms_project/public/teacher/my_timetables" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Thông tin cơ bản</h5>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Mã lịch dạy:</p>
                                    <p class="fw-bold">#<?php echo $data['timetable']['id']; ?></p>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Môn học:</p>
                                    <p><?php echo htmlspecialchars($data['timetable']['subject']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Mã lớp:</p>
                                    <p><?php echo htmlspecialchars($data['timetable']['class_code']); ?></p>
                                </div>
                                <?php if (!empty($data['timetable']['description'])): ?>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Mô tả:</p>
                                    <p><?php echo htmlspecialchars($data['timetable']['description']); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Thời gian</h5>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Thời gian bắt đầu:</p>
                                    <p class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt me-2 text-primary"></i>
                                        <?php echo date('d/m/Y', strtotime($data['timetable']['start_time'])); ?>
                                        <i class="far fa-clock ms-3 me-2 text-primary"></i>
                                        <?php echo date('H:i', strtotime($data['timetable']['start_time'])); ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Thời gian kết thúc:</p>
                                    <p class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt me-2 text-primary"></i>
                                        <?php echo date('d/m/Y', strtotime($data['timetable']['end_time'])); ?>
                                        <i class="far fa-clock ms-3 me-2 text-primary"></i>
                                        <?php echo date('H:i', strtotime($data['timetable']['end_time'])); ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Tổng thời gian:</p>
                                    <?php
                                    $start = new DateTime($data['timetable']['start_time']);
                                    $end = new DateTime($data['timetable']['end_time']);
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
                        
                        <?php if (!empty($data['timetable']['notes'])): ?>
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Ghi chú</h5>
                            <div class="bg-light p-3 rounded">
                                <?php echo nl2br(htmlspecialchars($data['timetable']['notes'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['timetable']['booking_id'])): ?>
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Thông tin đặt phòng</h5>
                            <p>Lịch dạy này đã được đặt phòng. <a href="/pdu_pms_project/public/teacher/booking_detail/<?php echo $data['timetable']['booking_id']; ?>" class="text-primary">Xem chi tiết đặt phòng</a></p>
                        </div>
                        <?php else: ?>
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Đặt phòng</h5>
                            <p>Lịch dạy này chưa được đặt phòng. Bạn có thể đặt phòng cho lịch dạy này.</p>
                            <a href="/pdu_pms_project/public/teacher/book_room?timetable_id=<?php echo $data['timetable']['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Đặt phòng
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Thông tin phòng nếu có -->
                <?php if (isset($data['room']) && $data['room']): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin phòng</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-door-open fa-3x text-primary"></i>
                            </div>
                            <h5 class="font-weight-bold"><?php echo htmlspecialchars($data['room']['name']); ?></h5>
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
                        
                        <div class="mt-3">
                            <a href="/pdu_pms_project/public/teacher/room_detail/<?php echo $data['room']['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-info-circle me-1"></i> Xem chi tiết phòng
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông tin phòng</h6>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-exclamation-circle fa-3x text-warning"></i>
                        </div>
                        <h5>Chưa có phòng được đặt</h5>
                        <p class="text-muted">Lịch dạy này chưa được đặt phòng</p>
                        <a href="/pdu_pms_project/public/teacher/book_room?timetable_id=<?php echo $data['timetable']['id']; ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-plus-circle me-1"></i> Đặt phòng ngay
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Thông tin hỗ trợ -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Hỗ trợ</h6>
                    </div>
                    <div class="card-body">
                        <p>Nếu bạn cần hỗ trợ về lịch dạy hoặc đặt phòng, vui lòng liên hệ:</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0">
                                <i class="fas fa-phone-alt me-2 text-primary"></i> Hotline: 028.1234.5678
                            </li>
                            <li class="list-group-item px-0">
                                <i class="fas fa-envelope me-2 text-primary"></i> Email: support@pducdtt.edu.vn
                            </li>
                            <li class="list-group-item px-0">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i> Văn phòng: Phòng A1-101
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h4>Không tìm thấy thông tin lịch dạy</h4>
                <p class="text-muted">Lịch dạy không tồn tại hoặc đã bị xóa</p>
                <a href="/pdu_pms_project/public/teacher/my_timetables" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'teacher';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>
