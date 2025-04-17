<?php
// Đảm bảo chỉ cho student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Tìm kiếm phòng học";
$pageSubtitle = "Tìm kiếm và đặt phòng học theo nhu cầu của bạn";
$pageIcon = "fas fa-search";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/student'],
    ['title' => 'Tìm kiếm phòng học', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="/pdu_pms_project/public/student/search_rooms" method="post" id="searchForm">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Tên phòng</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên phòng" value="<?php echo isset($data['searchParams']['name']) ? htmlspecialchars($data['searchParams']['name']) : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="room_type_id" class="form-label">Loại phòng</label>
                                <select class="form-select" id="room_type_id" name="room_type_id">
                                    <option value="">Tất cả loại phòng</option>
                                    <?php foreach ($data['roomTypes'] as $roomType): ?>
                                        <option value="<?php echo $roomType['id']; ?>" <?php echo (isset($data['searchParams']['room_type_id']) && $data['searchParams']['room_type_id'] == $roomType['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($roomType['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="min_capacity" class="form-label">Sức chứa tối thiểu</label>
                                <input type="number" class="form-control" id="min_capacity" name="min_capacity" min="1" placeholder="Nhập sức chứa" value="<?php echo isset($data['searchParams']['min_capacity']) ? htmlspecialchars($data['searchParams']['min_capacity']) : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="location" class="form-label">Vị trí</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="Nhập vị trí" value="<?php echo isset($data['searchParams']['location']) ? htmlspecialchars($data['searchParams']['location']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="start_time" class="form-label">Thời gian bắt đầu</label>
                                <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="<?php echo isset($data['searchParams']['start_time']) ? htmlspecialchars($data['searchParams']['start_time']) : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="end_time" class="form-label">Thời gian kết thúc</label>
                                <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="<?php echo isset($data['searchParams']['end_time']) ? htmlspecialchars($data['searchParams']['end_time']) : ''; ?>">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="only_available" name="only_available" value="1" <?php echo (isset($data['searchParams']['only_available']) && $data['searchParams']['only_available'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="only_available">
                                        Chỉ hiển thị phòng trống
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Kết quả tìm kiếm</h6>
                    <span class="badge bg-primary"><?php echo count($data['rooms']); ?> phòng học</span>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($data['rooms'])): ?>
                        <div class="alert alert-info">
                            Không tìm thấy phòng học nào phù hợp với tiêu chí tìm kiếm.
                        </div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php foreach ($data['rooms'] as $room): ?>
                                <div class="col">
                                    <div class="card h-100 room-card">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0"><?php echo htmlspecialchars($room['name']); ?></h5>
                                            <span class="badge <?php echo $room['status'] === 'trống' ? 'bg-success' : ($room['status'] === 'đã đặt' ? 'bg-danger' : 'bg-warning'); ?>">
                                                <?php echo htmlspecialchars($room['status']); ?>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span><i class="fas fa-tags me-2"></i> Loại phòng:</span>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($room['room_type_name']); ?></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span><i class="fas fa-users me-2"></i> Sức chứa:</span>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($room['capacity']); ?> người</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span><i class="fas fa-map-marker-alt me-2"></i> Vị trí:</span>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($room['location']); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <div class="d-grid gap-2">
                                                <a href="/pdu_pms_project/public/student/room_detail?id=<?php echo $room['id']; ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-info-circle"></i> Xem chi tiết
                                                </a>
                                                <?php if ($room['status'] === 'trống'): ?>
                                                    <a href="/pdu_pms_project/public/student/book_room?room_id=<?php echo $room['id']; ?>" class="btn btn-primary">
                                                        <i class="fas fa-calendar-plus"></i> Đặt phòng
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set min date-time for datetime-local inputs to today
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const offset = today.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(Date.now() - offset)).toISOString().slice(0, 16);

        document.getElementById('start_time').min = localISOTime;
        document.getElementById('end_time').min = localISOTime;

        // Set end_time min value based on start_time
        document.getElementById('start_time').addEventListener('change', function() {
            const startTime = this.value;
            document.getElementById('end_time').min = startTime;

            // If end time is before start time, reset it
            if (document.getElementById('end_time').value < startTime) {
                document.getElementById('end_time').value = startTime;
            }
        });
    });
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'student';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>