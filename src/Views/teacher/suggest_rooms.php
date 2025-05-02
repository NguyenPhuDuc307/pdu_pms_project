<?php
// Đảm bảo chỉ cho teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Đề xuất phòng trống";
$pageSubtitle = "Tìm kiếm phòng trống theo thời gian và đặt phòng nhanh chóng";
$pageIcon = "fas fa-calendar-check";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/teacher'],
    ['title' => 'Tìm kiếm phòng', 'link' => '/pdu_pms_project/public/teacher/search_rooms'],
    ['title' => 'Đề xuất phòng trống', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="card shadow mb-4 rounded">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tìm phòng trống theo thời gian</h6>
            <a href="/pdu_pms_project/public/teacher/search_rooms" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại tìm kiếm
            </a>
        </div>
        <div class="card-body">
            <form action="/pdu_pms_project/public/teacher/suggest_rooms" method="post" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_time" class="form-label">Thời gian bắt đầu</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" required
                                value="<?= isset($searchParams['start_time']) ? date('Y-m-d\TH:i', strtotime($searchParams['start_time'])) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_time" class="form-label">Thời gian kết thúc</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time" required
                                value="<?= isset($searchParams['end_time']) ? date('Y-m-d\TH:i', strtotime($searchParams['end_time'])) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="min_capacity" class="form-label">Số máy tối thiểu</label>
                            <input type="number" class="form-control" id="min_capacity" name="min_capacity" min="1"
                                value="<?= isset($searchParams['min_capacity']) ? intval($searchParams['min_capacity']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Tìm phòng trống
                        </button>
                        <a href="/pdu_pms_project/public/teacher/suggest_rooms" class="btn btn-secondary">
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

            <?php if (isset($searchParams['start_time']) && isset($searchParams['end_time'])): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Kết quả tìm kiếm cho thời gian từ
                    <strong><?= date('H:i d/m/Y', strtotime($searchParams['start_time'])) ?></strong>
                    đến
                    <strong><?= date('H:i d/m/Y', strtotime($searchParams['end_time'])) ?></strong>
                    <?php if (isset($searchParams['min_capacity']) && !empty($searchParams['min_capacity'])): ?>
                        , số máy tối thiểu: <strong><?= intval($searchParams['min_capacity']) ?> người</strong>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row row-cols-1 row-cols-md-3 g-4 mt-2">
                <?php if (!empty($rooms)): ?>
                    <?php foreach ($rooms as $room): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0"><?= htmlspecialchars($room['name']) ?></h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>Vị trí:</strong> <?= htmlspecialchars($room['location']) ?><br>
                                        <strong>Số máy:</strong> <?= intval($room['capacity']) ?> người<br>
                                        <strong>Trạng thái:</strong>
                                        <span class="badge bg-success">Trống</span>
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <a href="/pdu_pms_project/public/teacher/room_detail?id=<?= $room['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-info-circle me-1"></i> Chi tiết
                                        </a>
                                        <a href="/pdu_pms_project/public/teacher/book_room?room_id=<?= $room['id'] ?>&start_time=<?= urlencode($searchParams['start_time']) ?>&end_time=<?= urlencode($searchParams['end_time']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-calendar-plus me-1"></i> Đặt phòng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i> Không tìm thấy phòng trống nào phù hợp với tiêu chí tìm kiếm
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Kiểm tra ràng buộc thời gian
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        function validateTimeInputs() {
            const startTime = new Date(startTimeInput.value);
            const endTime = new Date(endTimeInput.value);

            if (endTime <= startTime) {
                endTimeInput.setCustomValidity('Thời gian kết thúc phải sau thời gian bắt đầu');
            } else {
                endTimeInput.setCustomValidity('');
            }
        }

        startTimeInput.addEventListener('change', validateTimeInputs);
        endTimeInput.addEventListener('change', validateTimeInputs);
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