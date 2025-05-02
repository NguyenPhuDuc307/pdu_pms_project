<?php
// Đảm bảo chỉ cho teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Chi tiết phòng: " . htmlspecialchars($room['name']);
$pageSubtitle = "Thông tin chi tiết và lịch sử dụng phòng";
$pageIcon = "fas fa-door-open";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/teacher'],
    ['title' => 'Tìm kiếm phòng', 'link' => '/pdu_pms_project/public/teacher/search_rooms'],
    ['title' => 'Chi tiết phòng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/teacher/search_rooms" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại tìm kiếm
        </a>
    </div>

    <div class="row">
        <!-- Main Room Info -->
        <div class="col-xl-8">
            <!-- Room Details Card -->
            <div class="card shadow mb-4 rounded">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle me-2"></i>Thông tin phòng</h6>
                    <?php
                    $statusClass = 'success';
                    $statusText = 'Khả dụng';
                    $statusIcon = 'check-circle';

                    if ($room['status'] === 'Đang bảo trì') {
                        $statusClass = 'warning';
                        $statusText = 'Đang bảo trì';
                        $statusIcon = 'tools';
                    } elseif ($room['status'] === 'Không khả dụng' || $room['status'] === 'đã đặt') {
                        $statusClass = 'danger';
                        $statusText = 'Không khả dụng';
                        $statusIcon = 'times-circle';
                    }
                    ?>
                    <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> px-3 py-2 rounded-pill">
                        <i class="fas fa-<?= $statusIcon ?> me-1"></i>
                        <?= $statusText ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Room Info -->
                        <div class="col-md-8">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-muted small fw-bold">Tên phòng</h6>
                                    <p class="fs-5 fw-semibold"><?= htmlspecialchars($room['name']) ?></p>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-muted small fw-bold">Vị trí</h6>
                                    <p>
                                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                        <?= htmlspecialchars($room['location']) ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-muted small fw-bold">Số máy</h6>
                                    <p>
                                        <i class="fas fa-users me-2 text-primary"></i>
                                        <?= intval($room['capacity']) ?> người
                                    </p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted small fw-bold">Mô tả</h6>
                                <p><?= !empty($room['description']) ? htmlspecialchars($room['description']) : '<em class="text-muted">Không có mô tả</em>' ?></p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-4">
                            <div class="d-grid gap-3">
                                <a href="/pdu_pms_project/public/teacher/book_room?room_id=<?= $room['id'] ?>" class="btn btn-primary py-3">
                                    <i class="fas fa-calendar-plus me-2"></i>Đặt phòng này
                                </a>
                                <a href="/pdu_pms_project/public/maintenance/create?room_id=<?= $room['id'] ?>" class="btn btn-outline-warning py-3">
                                    <i class="fas fa-tools me-2"></i>Yêu cầu sửa chữa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment Card -->
            <div class="card shadow mb-4 rounded">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-desktop me-2"></i>Thiết bị trong phòng</h6>
                    <span class="badge bg-primary rounded-pill"><?= count($room['equipment'] ?? []) ?> thiết bị</span>
                </div>
                <div class="card-body">
                    <?php if (isset($room['equipment']) && !empty($room['equipment'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tên thiết bị</th>
                                        <th>Mô tả</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end">Bảo trì gần nhất</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($room['equipment'] as $equipment): ?>
                                        <?php
                                        $equipStatusClass = 'success';
                                        $equipStatusIcon = 'check-circle';
                                        if ($equipment['status'] !== 'hoạt động') {
                                            $equipStatusClass = 'warning';
                                            $equipStatusIcon = 'exclamation-triangle';
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    $icon = 'desktop';
                                                    $equipName = strtolower($equipment['name']);
                                                    if (strpos($equipName, 'máy chiếu') !== false || strpos($equipName, 'projector') !== false) {
                                                        $icon = 'film';
                                                    } elseif (strpos($equipName, 'bàn') !== false || strpos($equipName, 'ghế') !== false || strpos($equipName, 'table') !== false) {
                                                        $icon = 'chair';
                                                    } elseif (strpos($equipName, 'điều hòa') !== false || strpos($equipName, 'air') !== false) {
                                                        $icon = 'fan';
                                                    } elseif (strpos($equipName, 'đèn') !== false || strpos($equipName, 'light') !== false) {
                                                        $icon = 'lightbulb';
                                                    }
                                                    ?>
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 34px; height: 34px;">
                                                        <i class="fas fa-<?= $icon ?> text-primary"></i>
                                                    </div>
                                                    <div class="fw-semibold"><?= htmlspecialchars($equipment['name']) ?></div>
                                                </div>
                                            </td>
                                            <td><?= !empty($equipment['description']) ? htmlspecialchars($equipment['description']) : '<em class="text-muted">Không có mô tả</em>' ?></td>
                                            <td>
                                                <span class="badge bg-<?= $equipStatusClass ?>-subtle text-<?= $equipStatusClass ?> px-3 py-2 rounded-pill">
                                                    <i class="fas fa-<?= $equipStatusIcon ?> me-1"></i>
                                                    <?= htmlspecialchars($equipment['status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?= $equipment['last_maintenance_date'] ? date('d/m/Y', strtotime($equipment['last_maintenance_date'])) : '<em class="text-muted">Chưa bảo trì</em>' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5>Không có thiết bị</h5>
                            <p class="text-muted">Phòng này không có thiết bị hoặc chưa cập nhật thông tin thiết bị</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Available Time Slots Card -->
            <div class="card shadow mb-4 rounded">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clock me-2"></i>Khung giờ trống</h6>
                    <span class="badge bg-primary rounded-pill"><?= count($availableSlots) ?> khung giờ</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($availableSlots)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($availableSlots, 0, 10) as $slot): ?>
                                <a href="/pdu_pms_project/public/teacher/book_room?room_id=<?= $room['id'] ?>&start_time=<?= urlencode($slot['start']) ?>&end_time=<?= urlencode($slot['end']) ?>"
                                    class="list-group-item list-group-item-action py-3 border-start-0 border-end-0">
                                    <div class="d-flex">
                                        <div class="me-3 text-center" style="min-width: 60px;">
                                            <?php
                                            $slotDate = new DateTime($slot['start']);
                                            $dayOfWeek = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                                            ?>
                                            <div class="bg-light rounded-3 py-1">
                                                <div class="small"><?= $dayOfWeek[$slotDate->format('w')] ?></div>
                                                <div class="fw-bold"><?= $slotDate->format('d/m') ?></div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 fw-semibold"><?= date('H:i', strtotime($slot['start'])) ?> - <?= date('H:i', strtotime($slot['end'])) ?></h6>
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Trống
                                                </span>
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php
                                                $start = new DateTime($slot['start']);
                                                $end = new DateTime($slot['end']);
                                                $duration = $start->diff($end);
                                                $hours = $duration->h;
                                                $minutes = $duration->i;
                                                if ($duration->days > 0) {
                                                    $hours += $duration->days * 24;
                                                }
                                                echo $hours > 0 ? $hours . ' giờ ' : '';
                                                echo $minutes > 0 ? $minutes . ' phút' : '';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($availableSlots) > 10): ?>
                            <div class="text-center pt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="showMoreSlots">
                                    <i class="fas fa-plus me-1"></i>Xem thêm khung giờ
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>Không có khung giờ trống</h5>
                            <p class="text-muted">Phòng này hiện không có khung giờ trống nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Booking History Card -->
            <div class="card shadow mb-4 rounded">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Lịch sử đặt phòng</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($bookingHistory)): ?>
                        <div class="timeline">
                            <?php foreach (array_slice($bookingHistory, 0, 5) as $booking): ?>
                                <div class="timeline-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex">
                                        <div class="me-3 text-center" style="min-width: 60px;">
                                            <?php
                                            $bookingDate = new DateTime($booking['start_time']);
                                            $dayOfWeek = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                                            ?>
                                            <div class="bg-light rounded-3 py-1">
                                                <div class="small"><?= $dayOfWeek[$bookingDate->format('w')] ?></div>
                                                <div class="fw-bold"><?= $bookingDate->format('d/m') ?></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center mb-1">
                                                <h6 class="mb-0 fw-semibold me-2"><?= date('H:i', strtotime($booking['start_time'])) ?> - <?= date('H:i', strtotime($booking['end_time'])) ?></h6>
                                                <?php
                                                $bookingStatusClass = 'secondary';
                                                $bookingStatusText = 'Chưa xác định';

                                                if ($booking['status'] === 'được duyệt') {
                                                    $bookingStatusClass = 'success';
                                                    $bookingStatusText = 'Đã duyệt';
                                                } elseif ($booking['status'] === 'chờ duyệt') {
                                                    $bookingStatusClass = 'warning';
                                                    $bookingStatusText = 'Chờ duyệt';
                                                } elseif ($booking['status'] === 'từ chối') {
                                                    $bookingStatusClass = 'danger';
                                                    $bookingStatusText = 'Từ chối';
                                                }
                                                ?>
                                                <span class="badge bg-<?= $bookingStatusClass ?> ms-auto"><?= $bookingStatusText ?></span>
                                            </div>
                                            <div class="text-muted small mb-1">
                                                <i class="fas fa-chalkboard me-1"></i>
                                                Mã lớp: <?= htmlspecialchars($booking['class_code']) ?>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-user me-1"></i>
                                                Người đặt: <?= htmlspecialchars($booking['teacher_name'] ?? 'Không xác định') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($bookingHistory) > 5): ?>
                            <div class="text-center pt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="showMoreHistory">
                                    <i class="fas fa-plus me-1"></i>Xem thêm lịch sử
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5>Chưa có lịch sử đặt phòng</h5>
                            <p class="text-muted">Phòng này chưa được đặt trước đây</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'teacher';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>