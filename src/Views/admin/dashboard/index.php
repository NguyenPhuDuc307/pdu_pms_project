<?php
// Đảm bảo chỉ cho admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Bảng điều khiển";
$pageSubtitle = "Tổng quan về hệ thống Quản lý Phòng Đào tạo";
$pageIcon = "fas fa-tachometer-alt";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Bảng điều khiển', 'link' => '']
];

// Bắt đầu output buffering để thu thập nội dung
ob_start();
?>

<style>
    /* Cải thiện dropdown no-arrow */
    .dropdown.no-arrow .dropdown-toggle::after {
        display: none;
    }

    .dropdown.no-arrow .dropdown-toggle {
        background: transparent;
        border: none;
        color: #6c757d;
        padding: 0.25rem 0.5rem;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .dropdown.no-arrow .dropdown-toggle:hover {
        color: #000;
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Nâng cấp avatar người dùng */
    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Card và các phần tử khác */
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }

    .card .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card .card-body {
        padding: 1.25rem;
        background-color: #ffffff;
    }

    /* Thêm style cho border-left trên card */
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }

    .border-left-success {
        border-left: 4px solid #1cc88a;
    }

    .border-left-info {
        border-left: 4px solid #36b9cc;
    }

    .border-left-warning {
        border-left: 4px solid #f6c23e;
    }

    /* Cải thiện giao diện bảng */
    .table th {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #5a5c69;
        border-top: none;
    }

    /* Cải thiện biểu đồ */
    .chart-pie {
        position: relative;
        height: 15rem;
        width: 100%;
    }
</style>

<!-- Content inside container-fluid -->
<!-- Page Header -->
<?php include __DIR__ . '/../../components/page_header.php'; ?>

<div class="py-2">
    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/reports" class="btn btn-primary">
            <i class="fas fa-download me-1"></i> Xuất báo cáo
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-primary text-white p-3 rounded">
                                <i class="fas fa-door-open fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Tổng số phòng</div>
                            <div class="h3 mb-0 fw-bold"><?= $data['stats']['total_rooms'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-success text-white p-3 rounded">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Lượt đặt phòng hôm nay</div>
                            <div class="h3 mb-0 fw-bold"><?= $data['stats']['today_bookings'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-info text-white p-3 rounded">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Tổng số người dùng</div>
                            <div class="h3 mb-0 fw-bold"><?= $data['stats']['total_users'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-warning text-white p-3 rounded">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Yêu cầu chờ duyệt</div>
                            <div class="h3 mb-0 fw-bold"><?= $data['pending_bookings'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Recent Activities -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Hoạt động gần đây</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#">Tất cả hoạt động</a></li>
                            <li><a class="dropdown-item" href="#">Chỉ đặt phòng</a></li>
                            <li><a class="dropdown-item" href="#">Chỉ người dùng</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Xuất dữ liệu</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>THỜI GIAN</th>
                                    <th>NGƯỜI DÙNG</th>
                                    <th>HOẠT ĐỘNG</th>
                                    <th>TRẠNG THÁI</th>
                                    <th>THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['recent_activities']) && !empty($data['recent_activities'])): ?>
                                    <?php foreach ($data['recent_activities'] as $activity): ?>
                                        <?php
                                        // Define status class and label based on activity type and status
                                        $statusClass = 'bg-secondary';
                                        $statusLabel = 'Không xác định';

                                        if (isset($activity['type'])) {
                                            if ($activity['type'] === 'booking') {
                                                $bookingStatus = $activity['status'] ?? '';

                                                switch (strtolower($bookingStatus)) {
                                                    case 'pending':
                                                        $statusClass = 'bg-warning';
                                                        $statusLabel = 'Chờ duyệt';
                                                        break;
                                                    case 'approved':
                                                        $statusClass = 'bg-success';
                                                        $statusLabel = 'Đã duyệt';
                                                        break;
                                                    case 'rejected':
                                                        $statusClass = 'bg-danger';
                                                        $statusLabel = 'Từ chối';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'bg-secondary';
                                                        $statusLabel = 'Đã hủy';
                                                        break;
                                                }
                                            } elseif ($activity['type'] === 'user_registration') {
                                                $statusClass = 'bg-info';
                                                $statusLabel = 'Đăng ký mới';
                                            } elseif ($activity['type'] === 'room_issue') {
                                                $statusClass = 'bg-danger';
                                                $statusLabel = 'Sự cố';
                                            }
                                        }

                                        // Generate initials for avatar
                                        $initials = '';
                                        $fullname = $activity['user_name'] ?? '';
                                        $nameParts = explode(' ', $fullname);
                                        if (count($nameParts) > 0) {
                                            $lastName = end($nameParts);
                                            $initials = mb_substr($lastName, 0, 1, 'UTF-8');
                                        }

                                        // Random background color for avatar
                                        $bgColors = ['#4CAF50', '#2196F3', '#9C27B0', '#F44336', '#FF9800'];
                                        $colorIndex = isset($activity['user_id']) ? $activity['user_id'] % count($bgColors) : 0;
                                        $bgColor = $bgColors[$colorIndex];
                                        ?>
                                        <tr>
                                            <td><?= isset($activity['timestamp']) ? date('d/m/Y H:i', strtotime($activity['timestamp'])) : '-' ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2" style="background-color: <?= $bgColor ?>;"><?= $initials ?></div>
                                                    <?= htmlspecialchars($activity['user_name'] ?? 'Không xác định') ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($activity['message'] ?? 'Không có thông tin') ?></td>
                                            <td><span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                                            <td>
                                                <?php if ($activity['type'] === 'booking' && isset($activity['booking_id'])): ?>
                                                    <a href="/pdu_pms_project/public/admin/booking_detail/<?= $activity['booking_id'] ?>" class="btn btn-sm btn-link">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php elseif ($activity['type'] === 'user_registration' && isset($activity['user_id'])): ?>
                                                    <a href="/pdu_pms_project/public/admin/edit_user/<?= $activity['user_id'] ?>" class="btn btn-sm btn-link">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-link" disabled><i class="fas fa-eye"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Không có hoạt động nào gần đây</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-primary btn-sm">
                            Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Usage Chart -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Sử dụng phòng</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="roomUsageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="roomUsageDropdown">
                            <li><a class="dropdown-item" href="#">Tuần này</a></li>
                            <li><a class="dropdown-item" href="#">Tháng này</a></li>
                            <li><a class="dropdown-item" href="#">Năm nay</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Xuất dữ liệu</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-pie mb-4">
                        <canvas id="roomUsageChart" height="250"></canvas>
                    </div>
                    <div class="mt-4">
                        <?php if (!empty($data['most_used_rooms'])): ?>
                            <?php foreach ($data['most_used_rooms'] as $index => $room): ?>
                                <?php
                                $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                $colorIndex = $index % count($colors);
                                $color = $colors[$colorIndex];
                                ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div><i class="fas fa-circle text-<?= $color ?> me-2"></i> <?= htmlspecialchars($room['room_type_name']) ?></div>
                                        <div class="fw-bold"><?= $room['usage_percent'] ?>%</div>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-<?= $color ?>" role="progressbar" style="width: <?= $room['usage_percent'] ?>%" aria-valuenow="<?= $room['usage_percent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Không có dữ liệu sử dụng phòng.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Removed unnecessary closing div -->

    <!-- Add Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Room Usage Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('roomUsageChart').getContext('2d');
            const roomUsageChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($data['most_used_rooms'] ?? [], 'room_type_name')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_column($data['most_used_rooms'] ?? [], 'usage_percent')) ?>,
                        backgroundColor: [
                            'rgba(78, 115, 223, 0.9)',
                            'rgba(40, 167, 69, 0.9)',
                            'rgba(23, 162, 184, 0.9)',
                            'rgba(255, 193, 7, 0.9)',
                            'rgba(220, 53, 69, 0.9)'
                        ],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            displayColors: false
                        }
                    },
                    cutout: '75%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });
    </script>

    <?php
    // Lấy nội dung đã buffer
    $pageContent = ob_get_clean();

    // Set page role
    $pageRole = 'admin';

    // Include the main layout
    include dirname(dirname(__DIR__)) . '/layouts/main_layout.php';
    ?>