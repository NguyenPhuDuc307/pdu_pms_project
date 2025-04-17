<?php
// Đảm bảo chỉ cho student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Lịch đặt phòng của tôi";
$pageSubtitle = "Quản lý tất cả các lịch đặt phòng của bạn";
$pageIcon = "fas fa-calendar-alt";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/student'],
    ['title' => 'Lịch đặt phòng của tôi', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="d-flex justify-content-end mb-3">
        <a href="/pdu_pms_project/public/student/book_room" class="btn btn-primary me-2">
            <i class="fas fa-plus-circle me-1"></i> Đặt phòng mới
        </a>
        <button class="btn btn-outline-primary" id="filterToggle">
            <i class="fas fa-filter me-1"></i> Bộ lọc
        </button>
    </div>

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

    <!-- Bộ lọc -->
    <div class="row mb-4" id="filterContainer" style="display: none;">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="/pdu_pms_project/public/student/my_bookings" method="get" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="chờ duyệt" <?php echo isset($_GET['status']) && $_GET['status'] === 'chờ duyệt' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                <option value="đã duyệt" <?php echo isset($_GET['status']) && $_GET['status'] === 'đã duyệt' ? 'selected' : ''; ?>>Đã duyệt</option>
                                <option value="từ chối" <?php echo isset($_GET['status']) && $_GET['status'] === 'từ chối' ? 'selected' : ''; ?>>Từ chối</option>
                                <option value="đã hủy" <?php echo isset($_GET['status']) && $_GET['status'] === 'đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                                <option value="hoàn thành" <?php echo isset($_GET['status']) && $_GET['status'] === 'hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? $_GET['date_from'] : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo isset($_GET['date_to']) ? $_GET['date_to'] : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="room_type" class="form-label">Loại phòng</label>
                            <select class="form-select" id="room_type" name="room_type">
                                <option value="">Tất cả loại phòng</option>
                                <?php if (isset($data['room_types'])): ?>
                                    <?php foreach ($data['room_types'] as $type): ?>
                                        <option value="<?php echo $type['id']; ?>" <?php echo isset($_GET['room_type']) && $_GET['room_type'] == $type['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">Đặt lại</button>
                            <button type="submit" class="btn btn-primary">Áp dụng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab điều hướng -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link <?php echo !isset($_GET['tab']) || $_GET['tab'] === 'upcoming' ? 'active' : ''; ?>" href="/pdu_pms_project/public/student/my_bookings?tab=upcoming">
                        Sắp tới
                        <?php if (isset($data['upcoming_count']) && $data['upcoming_count'] > 0): ?>
                            <span class="badge bg-primary rounded-pill ms-1"><?php echo $data['upcoming_count']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['tab']) && $_GET['tab'] === 'pending' ? 'active' : ''; ?>" href="/pdu_pms_project/public/student/my_bookings?tab=pending">
                        Chờ duyệt
                        <?php if (isset($data['pending_count']) && $data['pending_count'] > 0): ?>
                            <span class="badge bg-warning rounded-pill ms-1"><?php echo $data['pending_count']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['tab']) && $_GET['tab'] === 'past' ? 'active' : ''; ?>" href="/pdu_pms_project/public/student/my_bookings?tab=past">
                        Đã qua
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['tab']) && $_GET['tab'] === 'cancelled' ? 'active' : ''; ?>" href="/pdu_pms_project/public/student/my_bookings?tab=cancelled">
                        Đã hủy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['tab']) && $_GET['tab'] === 'all' ? 'active' : ''; ?>" href="/pdu_pms_project/public/student/my_bookings?tab=all">
                        Tất cả
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Danh sách lịch đặt phòng -->
    <div class="row">
        <div class="col-12">
            <?php if (empty($data['bookings'])): ?>
                <div class="card shadow mb-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h5>Không có lịch đặt phòng nào</h5>
                        <p class="text-muted">Bạn chưa có lịch đặt phòng nào trong mục này</p>
                        <a href="/pdu_pms_project/public/student/book_room" class="btn btn-primary mt-2">
                            <i class="fas fa-plus-circle me-1"></i> Đặt phòng ngay
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="bookingsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="min-width: 100px;">Mã đặt</th>
                                        <th style="min-width: 120px;">Phòng</th>
                                        <th style="min-width: 120px;">Thời gian</th>
                                        <th style="min-width: 120px;">Mã lớp</th>
                                        <th style="min-width: 120px;">Trạng thái</th>
                                        <th style="min-width: 120px;">Ngày đặt</th>
                                        <th class="text-center" style="min-width: 120px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['bookings'] as $booking): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold">#<?php echo $booking['id']; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                                        <i class="fas fa-door-open text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($booking['room_name']); ?></div>
                                                        <div class="small text-muted"><?php echo htmlspecialchars($booking['room_type_name']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <?php
                                                    $startDate = date('d/m/Y', strtotime($booking['start_time']));
                                                    $endDate = date('d/m/Y', strtotime($booking['end_time']));

                                                    if ($startDate === $endDate) {
                                                        echo $startDate;
                                                    } else {
                                                        echo $startDate . ' - ' . $endDate;
                                                    }
                                                    ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?php echo date('H:i', strtotime($booking['start_time'])); ?> -
                                                    <?php echo date('H:i', strtotime($booking['end_time'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($booking['class_code']); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = 'secondary';
                                                $statusIcon = 'clock';

                                                switch (strtolower($booking['status'])) {
                                                    case 'pending':
                                                        $statusClass = 'warning';
                                                        $statusIcon = 'clock';
                                                        break;
                                                    case 'approved':
                                                        $statusClass = 'success';
                                                        $statusIcon = 'check-circle';
                                                        break;
                                                    case 'rejected':
                                                        $statusClass = 'danger';
                                                        $statusIcon = 'times-circle';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'secondary';
                                                        $statusIcon = 'ban';
                                                        break;
                                                }

                                                // Display status text in Vietnamese
                                                $statusText = '';
                                                switch (strtolower($booking['status'])) {
                                                    case 'pending':
                                                        $statusText = 'Chờ duyệt';
                                                        break;
                                                    case 'approved':
                                                        $statusText = 'Đã duyệt';
                                                        break;
                                                    case 'rejected':
                                                        $statusText = 'Từ chối';
                                                        break;
                                                    case 'cancelled':
                                                        $statusText = 'Đã hủy';
                                                        break;
                                                    default:
                                                        $statusText = $booking['status'];
                                                }
                                                ?>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <i class="fas fa-<?php echo $statusIcon; ?> me-1"></i>
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <?php echo date('d/m/Y', strtotime($booking['created_at'])); ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?php echo date('H:i', strtotime($booking['created_at'])); ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <a href="/pdu_pms_project/public/student/booking_detail?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <?php if (strtolower($booking['status']) === 'chờ duyệt'): ?>
                                                    <a href="/pdu_pms_project/public/student/cancel_booking?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-danger cancel-booking" title="Hủy đặt phòng">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php if (isset($data['pagination'])): ?>
                    <div class="d-flex justify-content-end mt-4">
                        <nav aria-label="Điều hướng trang">
                            <ul class="pagination">
                                <?php if ($data['pagination']['current_page'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $data['pagination']['current_page'] - 1; ?>" aria-label="Trước">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $data['pagination']['total_pages']; $i++): ?>
                                    <li class="page-item <?php echo $i === $data['pagination']['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $data['pagination']['current_page'] + 1; ?>" aria-label="Tiếp">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý hiển thị bộ lọc
        const filterToggle = document.getElementById('filterToggle');
        const filterContainer = document.getElementById('filterContainer');

        if (filterToggle && filterContainer) {
            filterToggle.addEventListener('click', function() {
                if (filterContainer.style.display === 'none') {
                    filterContainer.style.display = 'block';
                    filterToggle.innerHTML = '<i class="fas fa-times me-1"></i> Đóng bộ lọc';
                } else {
                    filterContainer.style.display = 'none';
                    filterToggle.innerHTML = '<i class="fas fa-filter me-1"></i> Bộ lọc';
                }
            });
        }

        // Khởi tạo DataTable nếu có
        if ($.fn.DataTable && document.getElementById('bookingsTable')) {
            $('#bookingsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
                },
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                responsive: true
            });
        }

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
$pageRole = 'student';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>