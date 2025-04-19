<?php
// Đảm bảo chỉ cho teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Debug - Kiểm tra session
error_log("DEBUG: User ID = " . $_SESSION['user_id'] . ", Role = " . $_SESSION['role']);

// Thiết lập thông tin cho page_header
$pageTitle = "Lịch dạy của tôi";
$pageSubtitle = "Xem tất cả các lịch dạy được phân công";
$pageIcon = "fas fa-calendar-alt";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/teacher'],
    ['title' => 'Lịch dạy của tôi', 'link' => '']
];

// Thiết lập các biến cần thiết cho layout
$pageRole = 'teacher';
$current_page = 'my_timetables';

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <!-- Thông báo lỗi nếu có -->
    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($data['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Thông báo thành công nếu có -->
    <?php if (isset($data['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($data['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Nút tác vụ nhanh -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="/pdu_pms_project/public/teacher/calendar_bookings" class="btn btn-primary">
                <i class="fas fa-calendar-alt me-1"></i> Xem dạng lịch
            </a>
        </div>
        <button class="btn btn-outline-secondary" id="filterToggle">
            <i class="fas fa-filter me-1"></i> Bộ lọc
        </button>
    </div>

    <!-- Bộ lọc -->
    <div class="card shadow-sm mb-4" id="filterContainer" style="display: none;">
        <div class="card-body">
            <form method="GET" action="/pdu_pms_project/public/teacher/my_timetables" class="row g-3">
                <div class="col-md-4">
                    <label for="subject" class="form-label">Môn học</label>
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Nhập tên môn học" value="<?php echo htmlspecialchars($_GET['subject'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="class_code" class="form-label">Mã lớp</label>
                    <input type="text" class="form-control" id="class_code" name="class_code" placeholder="Nhập mã lớp" value="<?php echo htmlspecialchars($_GET['class_code'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_range" class="form-label">Khoảng thời gian</label>
                    <select class="form-select" id="date_range" name="date_range">
                        <option value="all" <?php echo (!isset($_GET['date_range']) || $_GET['date_range'] === 'all') ? 'selected' : ''; ?>>Tất cả</option>
                        <option value="today" <?php echo (isset($_GET['date_range']) && $_GET['date_range'] === 'today') ? 'selected' : ''; ?>>Hôm nay</option>
                        <option value="tomorrow" <?php echo (isset($_GET['date_range']) && $_GET['date_range'] === 'tomorrow') ? 'selected' : ''; ?>>Ngày mai</option>
                        <option value="this_week" <?php echo (isset($_GET['date_range']) && $_GET['date_range'] === 'this_week') ? 'selected' : ''; ?>>Tuần này</option>
                        <option value="next_week" <?php echo (isset($_GET['date_range']) && $_GET['date_range'] === 'next_week') ? 'selected' : ''; ?>>Tuần sau</option>
                        <option value="this_month" <?php echo (isset($_GET['date_range']) && $_GET['date_range'] === 'this_month') ? 'selected' : ''; ?>>Tháng này</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách lịch dạy -->
    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-alt me-2"></i>Lịch dạy của tôi</h5>
        </div>
        <div class="card-body">
            <?php if (empty($data['timetables'])): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Không tìm thấy lịch dạy nào.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Mã lớp</th>
                                <th>Môn học</th>
                                <th>Phòng</th>
                                <th>Thời gian bắt đầu</th>
                                <th>Thời gian kết thúc</th>
                                <th>Số lượng SV</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['timetables'] as $timetable): ?>
                                <?php
                                // Xác định trạng thái
                                $now = new DateTime();
                                $start = new DateTime($timetable['start_time']);
                                $end = new DateTime($timetable['end_time']);

                                if ($now > $end) {
                                    $status = 'Đã kết thúc';
                                    $statusClass = 'bg-secondary';
                                } elseif ($now >= $start && $now <= $end) {
                                    $status = 'Đang diễn ra';
                                    $statusClass = 'bg-success';
                                } else {
                                    $status = 'Sắp diễn ra';
                                    $statusClass = 'bg-primary';
                                }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($timetable['class_code']); ?></td>
                                    <td><?php echo htmlspecialchars($timetable['subject']); ?></td>
                                    <td>
                                        <?php if (!empty($timetable['room_name'])): ?>
                                            <span class="badge bg-info text-dark"><?php echo htmlspecialchars($timetable['room_name']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Chưa phân phòng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($timetable['start_time'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($timetable['end_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($timetable['participants']); ?></td>
                                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                                    <td>
                                        <a href="/pdu_pms_project/public/teacher/timetable_detail?id=<?php echo $timetable['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý hiển thị/ẩn bộ lọc nâng cao
        const filterToggle = document.getElementById('filterToggle');
        const filterContainer = document.getElementById('filterContainer');

        filterToggle.addEventListener('click', function() {
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
                filterToggle.innerHTML = '<i class="fas fa-times me-1"></i> Đóng bộ lọc';
            } else {
                filterContainer.style.display = 'none';
                filterToggle.innerHTML = '<i class="fas fa-filter me-1"></i> Bộ lọc';
            }
        });

        // Không cần khởi tạo DataTable ở đây vì đã được khởi tạo trong main_layout.php
    });
</script>

<?php
// Get the buffered content
$pageContent = ob_get_clean();

// Include the main layout
include __DIR__ . '/../layouts/main_layout.php';
?>