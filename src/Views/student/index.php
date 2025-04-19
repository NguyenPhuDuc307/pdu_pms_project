<?php
// Đảm bảo chỉ cho student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Bảng điều khiển sinh viên";
$pageSubtitle = "Quản lý lịch đặt phòng và theo dõi hoạt động của bạn";
$pageIcon = "fas fa-user-graduate";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Bảng điều khiển sinh viên', 'link' => '']
];

// Bắt đầu output buffering để thu thập nội dung
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/student/calendar_bookings" class="btn btn-outline-primary shadow-sm me-2">
            <i class="fas fa-calendar-alt me-2"></i>Xem dạng lịch
        </a>
        <a href="/pdu_pms_project/public/student/book_room" class="btn btn-primary shadow-sm me-2">
            <i class="fas fa-calendar-plus me-2"></i>Đặt phòng mới
        </a>
        <a href="/pdu_pms_project/public/maintenance" class="btn btn-outline-warning shadow-sm">
            <i class="fas fa-tools me-2"></i>Yêu cầu sửa chữa
        </a>
    </div>

    <!-- Các thẻ thống kê -->
    <div class="row">
        <!-- Thống kê 1: Tổng đặt phòng -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 rounded">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng đặt phòng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($data['total_bookings']) ? $data['total_bookings'] : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê 2: Đặt phòng đã duyệt -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 rounded">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đặt phòng đã duyệt</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($data['approved_bookings']) ? $data['approved_bookings'] : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê 3: Đặt phòng chờ duyệt -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 rounded">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Đặt phòng chờ duyệt</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($data['pending_bookings']) ? $data['pending_bookings'] : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phần thống kê và thông tin -->
    <div class="row">
        <!-- Lịch trình sắp tới -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow rounded">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch trình sắp tới</h6>
                    <a href="/pdu_pms_project/public/student/my_bookings" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <?php if (isset($data['upcoming_bookings']) && count($data['upcoming_bookings']) > 0): ?>
                        <div class="list-group">
                            <?php foreach (array_slice($data['upcoming_bookings'], 0, 3) as $booking): ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($booking['room_name'] ?? 'Phòng không xác định') ?></h6>
                                        <small>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch (strtolower($booking['status'])) {
                                                case 'pending':
                                                case 'chờ duyệt':
                                                    $statusClass = 'text-warning';
                                                    $statusText = 'Chờ duyệt';
                                                    break;
                                                case 'approved':
                                                case 'được duyệt':
                                                case 'đã duyệt':
                                                    $statusClass = 'text-success';
                                                    $statusText = 'Đã duyệt';
                                                    break;
                                                case 'rejected':
                                                case 'từ chối':
                                                    $statusClass = 'text-danger';
                                                    $statusText = 'Từ chối';
                                                    break;
                                                case 'cancelled':
                                                case 'đã hủy':
                                                    $statusClass = 'text-secondary';
                                                    $statusText = 'Đã hủy';
                                                    break;
                                                default:
                                                    $statusClass = 'text-secondary';
                                                    $statusText = $booking['status'];
                                            }
                                            ?>
                                            <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                        </small>
                                    </div>
                                    <p class="mb-1">Lớp: <?= htmlspecialchars($booking['class_code']) ?></p>
                                    <small>
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($booking['start_time']))) ?>
                                        <i class="fas fa-clock ms-2 me-1"></i>
                                        <?= htmlspecialchars(date('H:i', strtotime($booking['start_time']))) ?> -
                                        <?= htmlspecialchars(date('H:i', strtotime($booking['end_time']))) ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($data['upcoming_bookings']) > 3): ?>
                            <div class="text-center mt-3">
                                <a href="/pdu_pms_project/public/student/my_bookings" class="text-primary">Xem thêm <i class="fas fa-arrow-right"></i></a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <p>Bạn không có lịch đặt phòng sắp tới</p>
                            <a href="/pdu_pms_project/public/student/book_room" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>Đặt phòng ngay
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Thống kê theo trạng thái -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow rounded">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê đặt phòng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="bookingStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch sử đặt phòng -->
    <div class="card shadow mb-4 rounded">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Lịch sử đặt phòng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bookingsTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Phòng</th>
                            <th>Mã lớp</th>
                            <th>Thời gian bắt đầu</th>
                            <th>Thời gian kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['bookings']) && is_array($data['bookings'])): ?>
                            <?php foreach ($data['bookings'] as $booking): ?>
                                <tr>
                                    <td><?= htmlspecialchars($booking['id']) ?></td>
                                    <td><?= htmlspecialchars($booking['room_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($booking['class_code']) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($booking['start_time']))) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($booking['end_time']))) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch (strtolower($booking['status'])) {
                                            case 'chờ duyệt':
                                            case 'pending':
                                                $statusClass = 'bg-warning';
                                                $statusText = 'Chờ duyệt';
                                                break;
                                            case 'được duyệt':
                                            case 'đã duyệt':
                                            case 'approved':
                                                $statusClass = 'bg-success';
                                                $statusText = 'Đã duyệt';
                                                break;
                                            case 'từ chối':
                                            case 'rejected':
                                                $statusClass = 'bg-danger';
                                                $statusText = 'Từ chối';
                                                break;
                                            case 'đã hủy':
                                            case 'cancelled':
                                                $statusClass = 'bg-secondary';
                                                $statusText = 'Đã hủy';
                                                break;
                                            default:
                                                $statusClass = 'bg-secondary';
                                                $statusText = $booking['status'];
                                        }
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td>
                                        <a href="/pdu_pms_project/public/student/booking_detail?id=<?= $booking['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có đặt phòng nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo DataTable
        if ($.fn.DataTable) {
            $('#bookingsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json',
                    search: "",
                    lengthMenu: "_MENU_"
                },
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
            });
        }

        // Biểu đồ trạng thái đặt phòng
        var ctx = document.getElementById('bookingStatusChart').getContext('2d');
        if (ctx) {
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
                    datasets: [{
                        data: [
                            <?= isset($data['approved_bookings']) ? $data['approved_bookings'] : 0 ?>,
                            <?= isset($data['pending_bookings']) ? $data['pending_bookings'] : 0 ?>,
                            <?= isset($data['rejected_bookings']) ? $data['rejected_bookings'] : 0 ?>
                        ],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.raw || 0;
                                    return label + ': ' + value + ' đặt phòng';
                                }
                            }
                        }
                    }
                }
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
include dirname(__DIR__) . '/layouts/main_layout.php';
?>