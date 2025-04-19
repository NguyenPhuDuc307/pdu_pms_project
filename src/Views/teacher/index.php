<?php
// Đảm bảo chỉ cho teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Bảng điều khiển giáo viên";
$pageSubtitle = "Quản lý lịch đặt phòng và theo dõi trạng thái phòng học";
$pageIcon = "fas fa-chalkboard-teacher";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Bảng điều khiển giáo viên', 'link' => '']
];

// Bắt đầu output buffering để thu thập nội dung
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <!-- Các thẻ thống kê -->
    <div class="row">
        <!-- Thống kê 1: Đặt phòng hôm nay -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 rounded">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Đặt phòng hôm nay</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($data['today_bookings']) ? count($data['today_bookings']) : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê 2: Tổng số phòng -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 rounded">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng số phòng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($data['total_rooms']) ? $data['total_rooms'] : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê 3: Phòng đang trống -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 rounded">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Phòng đang trống</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($data['available_rooms']) ? $data['available_rooms'] : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    <div class="row">
        <!-- Biểu đồ 1: Thống kê đặt phòng theo trạng thái -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow rounded">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê đặt phòng theo trạng thái</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="bookingStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ 2: Thống kê đặt phòng theo thời gian -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow rounded">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê đặt phòng theo thời gian</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="bookingTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đặt phòng gần đây -->
    <div class="card shadow mb-4 rounded">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Đặt phòng gần đây</h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="/pdu_pms_project/public/teacher/calendar_bookings" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-calendar-alt me-1"></i> Xem dạng lịch
                </a>

                <a href="/pdu_pms_project/public/teacher/book_room" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Đặt phòng mới
                </a>

                <a href="/pdu_pms_project/public/maintenance" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-tools me-1"></i> Yêu cầu sửa chữa
                </a>
            </div>
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
                                        switch ($booking['status']) {
                                            case 'chờ duyệt':
                                                $statusClass = 'bg-warning';
                                                $statusText = 'Chờ duyệt';
                                                break;
                                            case 'được duyệt':
                                                $statusClass = 'bg-success';
                                                $statusText = 'Đã duyệt';
                                                break;
                                            case 'từ chối':
                                                $statusClass = 'bg-danger';
                                                $statusText = 'Từ chối';
                                                break;
                                            default:
                                                $statusClass = 'bg-secondary';
                                                $statusText = 'N/A';
                                        }
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có đặt phòng nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
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
            responsive: true,
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between"ip>'
        });

        // Thêm placeholder cho ô tìm kiếm
        $('.dataTables_filter input').attr('placeholder', 'Tìm kiếm...');

        // Thêm class Bootstrap vào các phần tử
        $('.dataTables_length select').addClass('form-select form-select-sm');
        $('.dataTables_filter input').addClass('form-control form-control-sm');

        // Không còn các nút lọc nhanh
    });
</script>

<script>
    // Khởi tạo biểu đồ thống kê khi trang đã tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Biểu đồ thống kê theo trạng thái
        const statusCtx = document.getElementById('bookingStatusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Chờ duyệt', 'Đã duyệt', 'Từ chối'],
                datasets: [{
                    data: [
                        <?= isset($data['status_stats']['waiting']) ? $data['status_stats']['waiting'] : 0 ?>,
                        <?= isset($data['status_stats']['approved']) ? $data['status_stats']['approved'] : 0 ?>,
                        <?= isset($data['status_stats']['rejected']) ? $data['status_stats']['rejected'] : 0 ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(40, 167, 69, 1)',
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
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Biểu đồ thống kê theo thời gian
        const timeCtx = document.getElementById('bookingTimeChart').getContext('2d');
        const timeChart = new Chart(timeCtx, {
            type: 'bar',
            data: {
                labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                datasets: [{
                    label: 'Số lượng đặt phòng',
                    data: [
                        <?= isset($data['day_stats'][1]) ? $data['day_stats'][1] : 0 ?>,
                        <?= isset($data['day_stats'][2]) ? $data['day_stats'][2] : 0 ?>,
                        <?= isset($data['day_stats'][3]) ? $data['day_stats'][3] : 0 ?>,
                        <?= isset($data['day_stats'][4]) ? $data['day_stats'][4] : 0 ?>,
                        <?= isset($data['day_stats'][5]) ? $data['day_stats'][5] : 0 ?>,
                        <?= isset($data['day_stats'][6]) ? $data['day_stats'][6] : 0 ?>,
                        <?= isset($data['day_stats'][0]) ? $data['day_stats'][0] : 0 ?>
                    ],
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'teacher';

// Include the main layout
include dirname(__DIR__) . '/layouts/main_layout.php';
?>