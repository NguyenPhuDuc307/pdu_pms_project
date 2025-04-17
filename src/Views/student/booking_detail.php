<?php
$pageTitle = "Chi Tiết Đặt Phòng";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/pdu_pms_project/public/student/dashboard">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/pdu_pms_project/public/student/my_bookings">Lịch đặt phòng của tôi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết đặt phòng</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($data['booking'])): ?>
    <div class="row">
        <div class="col-lg-8">
            <!-- Thông tin đặt phòng -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Chi tiết đặt phòng #<?php echo $data['booking']['id']; ?></h5>
                        </div>
                        <?php
                        $statusClass = 'secondary';
                        $statusIcon = 'clock';
                        
                        switch ($data['booking']['status']) {
                            case 'chờ duyệt':
                                $statusClass = 'warning';
                                $statusIcon = 'hourglass-half';
                                break;
                            case 'đã duyệt':
                                $statusClass = 'success';
                                $statusIcon = 'check-circle';
                                break;
                            case 'từ chối':
                                $statusClass = 'danger';
                                $statusIcon = 'times-circle';
                                break;
                            case 'đã hủy':
                                $statusClass = 'secondary';
                                $statusIcon = 'ban';
                                break;
                            case 'hoàn thành':
                                $statusClass = 'info';
                                $statusIcon = 'check-double';
                                break;
                        }
                        ?>
                        <span class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> px-3 py-2 rounded-pill">
                            <i class="fas fa-<?php echo $statusIcon; ?> me-1"></i>
                            <?php echo htmlspecialchars($data['booking']['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <!-- Thông tin thời gian -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted small fw-bold">Thời gian đặt phòng</h6>
                                <p class="mb-1">
                                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                                    <?php echo date('d/m/Y', strtotime($data['booking']['start_time'])); ?>
                                </p>
                                <p class="mb-0">
                                    <i class="far fa-clock me-2 text-primary"></i>
                                    <?php echo date('H:i', strtotime($data['booking']['start_time'])); ?> - 
                                    <?php echo date('H:i', strtotime($data['booking']['end_time'])); ?>
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted small fw-bold">Thông tin lớp học</h6>
                                <p class="mb-0">
                                    <i class="fas fa-users me-2 text-primary"></i>
                                    Mã lớp: <strong><?php echo htmlspecialchars($data['booking']['class_code']); ?></strong>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-user-friends me-2 text-primary"></i>
                                    Số người tham gia: <strong><?php echo htmlspecialchars($data['booking']['participants']); ?> người</strong>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-clipboard-list me-2 text-primary"></i>
                                    Mục đích: <strong><?php echo htmlspecialchars($data['booking']['purpose']); ?></strong>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Thông tin phòng -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted small fw-bold">Thông tin phòng học</h6>
                                <p class="fw-bold fs-5 mb-1">
                                    <?php echo htmlspecialchars($data['booking']['room_name']); ?>
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                    <?php echo htmlspecialchars($data['booking']['location']); ?>
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-user-friends me-2 text-primary"></i>
                                    Sức chứa: <?php echo htmlspecialchars($data['booking']['capacity']); ?> người
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    Loại phòng: <?php echo htmlspecialchars($data['booking']['room_type_name']); ?>
                                </p>
                            </div>
                            
                            <div>
                                <a href="/pdu_pms_project/public/student/room_detail/<?php echo $data['booking']['room_id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-info-circle me-1"></i> Xem chi tiết phòng
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($data['booking']['notes'])): ?>
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted small fw-bold">Ghi chú</h6>
                        <div class="bg-light p-3 rounded">
                            <?php echo nl2br(htmlspecialchars($data['booking']['notes'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($data['booking']['admin_notes'])): ?>
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted small fw-bold">Ghi chú của quản trị viên</h6>
                        <div class="alert alert-warning">
                            <?php echo nl2br(htmlspecialchars($data['booking']['admin_notes'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($data['booking']['cancel_reason'])): ?>
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted small fw-bold">Lý do hủy</h6>
                        <div class="alert alert-danger">
                            <?php echo nl2br(htmlspecialchars($data['booking']['cancel_reason'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($data['booking']['rejection_reason'])): ?>
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted small fw-bold">Lý do từ chối</h6>
                        <div class="alert alert-danger">
                            <?php echo nl2br(htmlspecialchars($data['booking']['rejection_reason'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between flex-wrap">
                        <div>
                            <a href="/pdu_pms_project/public/student/my_bookings" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                        <div>
                            <?php if ($data['booking']['status'] === 'chờ duyệt'): ?>
                            <a href="/pdu_pms_project/public/student/edit_booking/<?php echo $data['booking']['id']; ?>" class="btn btn-outline-primary me-2">
                                <i class="fas fa-edit me-1"></i> Chỉnh sửa
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($data['booking']['status'] === 'chờ duyệt' || $data['booking']['status'] === 'đã duyệt'): ?>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fas fa-times me-1"></i> Hủy đặt phòng
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($data['booking']['status'] === 'đã duyệt'): ?>
                            <a href="#" class="btn btn-outline-primary ms-2" onclick="window.print();">
                                <i class="fas fa-print me-1"></i> In phiếu
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lịch sử hoạt động -->
            <?php if (isset($data['booking_activities']) && !empty($data['booking_activities'])): ?>
            <div class="card shadow">
                <div class="card-header bg-white p-3">
                    <h5 class="mb-0">Lịch sử hoạt động</h5>
                </div>
                <div class="card-body p-0">
                    <div class="timeline-container p-3">
                        <?php foreach ($data['booking_activities'] as $activity): ?>
                            <div class="timeline-item">
                                <div class="timeline-item-content">
                                    <div class="d-flex">
                                        <div class="timeline-icon">
                                            <?php
                                            $activityIcon = 'info-circle';
                                            switch ($activity['action']) {
                                                case 'tạo mới':
                                                    $activityIcon = 'plus-circle';
                                                    break;
                                                case 'cập nhật':
                                                    $activityIcon = 'edit';
                                                    break;
                                                case 'hủy':
                                                    $activityIcon = 'times-circle';
                                                    break;
                                                case 'duyệt':
                                                    $activityIcon = 'check-circle';
                                                    break;
                                                case 'từ chối':
                                                    $activityIcon = 'ban';
                                                    break;
                                            }
                                            ?>
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                <i class="fas fa-<?php echo $activityIcon; ?> text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="timeline-content ms-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <h6 class="fw-bold mb-0">
                                                    <?php 
                                                    switch ($activity['action']) {
                                                        case 'tạo mới':
                                                            echo 'Tạo yêu cầu đặt phòng';
                                                            break;
                                                        case 'cập nhật':
                                                            echo 'Cập nhật thông tin đặt phòng';
                                                            break;
                                                        case 'hủy':
                                                            echo 'Hủy đặt phòng';
                                                            break;
                                                        case 'duyệt':
                                                            echo 'Phê duyệt đặt phòng';
                                                            break;
                                                        case 'từ chối':
                                                            echo 'Từ chối đặt phòng';
                                                            break;
                                                        default:
                                                            echo htmlspecialchars($activity['action']);
                                                    }
                                                    ?>
                                                </h6>
                                                <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($activity['timestamp'])); ?></small>
                                            </div>
                                            
                                            <?php if (!empty($activity['user_name'])): ?>
                                            <p class="mb-2">
                                                <i class="far fa-user me-1"></i>
                                                <?php 
                                                echo $activity['user_role'] === 'admin' ? 'Quản trị viên: ' : 'Người dùng: ';
                                                echo htmlspecialchars($activity['user_name']); 
                                                ?>
                                            </p>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($activity['description'])): ?>
                                            <p class="mb-0 text-muted small"><?php echo htmlspecialchars($activity['description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-4">
            <!-- Thông tin người đặt -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white p-3">
                    <h5 class="mb-0">Thông tin người đặt</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-user fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($data['booking']['student_name']); ?></h6>
                            <p class="text-muted mb-0 small"><?php echo htmlspecialchars($data['booking']['student_email']); ?></p>
                        </div>
                    </div>
                    
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 120px;">Mã sinh viên:</th>
                                <td><?php echo htmlspecialchars($data['booking']['student_id']); ?></td>
                            </tr>
                            <tr>
                                <th>Ngày đặt:</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($data['booking']['created_at'])); ?></td>
                            </tr>
                            <?php if ($data['booking']['status'] === 'đã duyệt' && !empty($data['booking']['approved_at'])): ?>
                            <tr>
                                <th>Ngày duyệt:</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($data['booking']['approved_at'])); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (($data['booking']['status'] === 'từ chối' || $data['booking']['status'] === 'đã hủy') && !empty($data['booking']['updated_at'])): ?>
                            <tr>
                                <th>Ngày <?php echo $data['booking']['status'] === 'từ chối' ? 'từ chối' : 'hủy'; ?>:</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($data['booking']['updated_at'])); ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Quy định và lưu ý -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white p-3">
                    <h5 class="mb-0">Quy định và lưu ý</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 border-0 d-flex">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 24px; height: 24px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <div>Phải có mặt đúng giờ theo lịch đã đặt</div>
                        </div>
                        <div class="list-group-item px-0 border-0 d-flex">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 24px; height: 24px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <div>Không làm ồn và gây ảnh hưởng đến các phòng khác</div>
                        </div>
                        <div class="list-group-item px-0 border-0 d-flex">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 24px; height: 24px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <div>Giữ gìn vệ sinh phòng học và thao tác thiết bị đúng cách</div>
                        </div>
                        <div class="list-group-item px-0 border-0 d-flex">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 24px; height: 24px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <div>Tắt đèn, điều hòa, thiết bị khi rời khỏi phòng</div>
                        </div>
                        <div class="list-group-item px-0 border-0 d-flex">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 24px; height: 24px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <div>Báo cáo ngay khi phát hiện sự cố hoặc hư hỏng</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hỗ trợ -->
            <div class="card shadow">
                <div class="card-header bg-white p-3">
                    <h5 class="mb-0">Hỗ trợ</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ:</p>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                    <i class="fas fa-phone-alt text-white small"></i>
                                </div>
                                <div class="fw-bold">Hotline hỗ trợ</div>
                            </div>
                            <p class="mb-0 ps-4">028.1234.5678</p>
                        </div>
                        <div class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                    <i class="fas fa-envelope text-white small"></i>
                                </div>
                                <div class="fw-bold">Email hỗ trợ</div>
                            </div>
                            <p class="mb-0 ps-4">support@pducdtt.edu.vn</p>
                        </div>
                        <div class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                    <i class="fas fa-map-marker-alt text-white small"></i>
                                </div>
                                <div class="fw-bold">Văn phòng quản lý</div>
                            </div>
                            <p class="mb-0 ps-4">Phòng A1-101, Tòa nhà A1</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h4>Không tìm thấy thông tin đặt phòng</h4>
            <p class="text-muted">Yêu cầu đặt phòng không tồn tại hoặc đã bị xóa</p>
            <a href="/pdu_pms_project/public/student/my_bookings" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal hủy đặt phòng -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Xác nhận hủy đặt phòng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn hủy lịch đặt phòng này?</p>
                <p class="mb-0 text-danger">Lưu ý: Việc hủy đặt phòng nhiều lần có thể ảnh hưởng đến khả năng đặt phòng trong tương lai.</p>
            </div>
            <div class="modal-footer">
                <form action="/pdu_pms_project/public/student/cancel_booking" method="post">
                    <input type="hidden" name="booking_id" value="<?php echo $data['booking']['id']; ?>">
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Lý do hủy</label>
                        <textarea class="form-control" name="cancel_reason" id="cancel_reason" rows="3" required></textarea>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CSS cho timeline -->
<style>
.timeline-container {
    position: relative;
    padding: 1rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 12px;
    top: 30px;
    height: calc(100% - 15px);
    width: 2px;
    background-color: #e9ecef;
}

.timeline-icon {
    position: relative;
    z-index: 1;
}

.timeline-icon .rounded-circle {
    width: 24px;
    height: 24px;
}
@media print {
    .breadcrumb, .btn, nav, footer, .card-header button {
        display: none !important;
    }
    
    .timeline-container, .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    body {
        background-color: white !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 