<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Báo cáo thống kê";
$pageSubtitle = "Thống kê và báo cáo về việc sử dụng phòng, đặt phòng và hoạt động người dùng";
$pageIcon = "fas fa-chart-bar";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Báo cáo thống kê', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../../components/page_header.php'; ?>

    <div class="text-end mb-3">
        <button id="printReport" class="btn btn-primary shadow-sm">
            <i class="fas fa-print fa-sm text-white-50 me-1"></i> In báo cáo
        </button>
        <button id="exportReport" class="btn btn-success shadow-sm ms-2">
            <i class="fas fa-file-export fa-sm text-white-50 me-1"></i> Xuất báo cáo
        </button>
    </div>

    <!-- Content Row - Report Stats -->
    <div class="row mb-4">
        <!-- Total Bookings Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng lượt đặt phòng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['total_bookings'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Bookings Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đặt phòng còn hiệu lực</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['active_bookings'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng người dùng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['total_users'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Requests Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Yêu cầu bảo trì</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['maintenance_requests'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Bộ lọc báo cáo</h6>
        </div>
        <div class="card-body">
            <form method="post" id="reportFilterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="report_type" class="form-label">Loại báo cáo</label>
                    <select name="report_type" id="report_type" class="form-select">
                        <option value="all" <?= ($data['report_type'] ?? '') == 'all' ? 'selected' : '' ?>>Tất cả báo cáo</option>
                        <option value="bookings" <?= ($data['report_type'] ?? '') == 'bookings' ? 'selected' : '' ?>>Đặt phòng</option>
                        <option value="room_usage" <?= ($data['report_type'] ?? '') == 'room_usage' ? 'selected' : '' ?>>Sử dụng phòng</option>
                        <option value="user_activity" <?= ($data['report_type'] ?? '') == 'user_activity' ? 'selected' : '' ?>>Hoạt động người dùng</option>
                        <option value="maintenance" <?= ($data['report_type'] ?? '') == 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="time_range" class="form-label">Khoảng thời gian</label>
                    <select name="time_range" id="time_range" class="form-select">
                        <option value="day" <?= ($data['time_range'] ?? '') == 'day' ? 'selected' : '' ?>>Hôm nay</option>
                        <option value="week" <?= ($data['time_range'] ?? '') == 'week' ? 'selected' : '' ?>>Tuần này</option>
                        <option value="month" <?= ($data['time_range'] ?? '') == 'month' ? 'selected' : '' ?>>Tháng này</option>
                        <option value="quarter" <?= ($data['time_range'] ?? '') == 'quarter' ? 'selected' : '' ?>>Quý này</option>
                        <option value="year" <?= ($data['time_range'] ?? '') == 'year' ? 'selected' : '' ?>>Năm nay</option>
                        <option value="custom" <?= ($data['time_range'] ?? '') == 'custom' ? 'selected' : '' ?>>Tùy chỉnh</option>
                    </select>
                </div>
                <div class="col-md-3" id="date_range_container" style="<?= ($data['time_range'] ?? '') != 'custom' ? 'display: none;' : '' ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Từ ngày</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $data['start_date'] ?? date('Y-m-d', strtotime('-30 days')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Đến ngày</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $data['end_date'] ?? date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Lọc báo cáo
                    </button>
                    <button type="button" id="resetFilter" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Đặt lại
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Booking Trends Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow chart-card">
                <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center chart-header">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-line me-2"></i>Xu hướng đặt phòng</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-1"></i> Tải xuống biểu đồ</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sync me-1"></i> Làm mới dữ liệu</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body chart-container">
                    <div class="chart-area">
                        <canvas id="bookingTrendsChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Usage Pie Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow chart-card">
                <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center chart-header">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-pie me-2"></i>Tỷ lệ sử dụng phòng</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-1"></i> Tải xuống biểu đồ</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sync me-1"></i> Làm mới dữ liệu</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body chart-container">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="roomUsageChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Tables -->
    <div class="row">
        <!-- Booking Report -->
        <div class="col-12 mb-4" id="bookingsReportSection" style="<?= ($data['report_type'] ?? '') != 'all' && ($data['report_type'] ?? '') != 'bookings' ? 'display: none;' : '' ?>">
            <div class="card shadow chart-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between chart-header" style="background-color: #4e73df; color: white;">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-calendar-check me-2"></i>Báo cáo đặt phòng</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-1"></i> Tải xuống</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-print me-1"></i> In báo cáo</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body chart-container">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="bookingsTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Người đặt</th>
                                    <th>Phòng</th>
                                    <th>Ngày</th>
                                    <th>Giờ bắt đầu</th>
                                    <th>Giờ kết thúc</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['bookings']) && is_array($data['bookings'])): ?>
                                    <?php foreach ($data['bookings'] as $booking): ?>
                                        <tr>
                                            <td><?= $booking['id'] ?? '' ?></td>
                                            <td><?= htmlspecialchars($booking['user_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($booking['room_name'] ?? 'N/A') ?></td>
                                            <td><?= date('d/m/Y', strtotime($booking['date'] ?? 'now')) ?></td>
                                            <td><?= date('H:i', strtotime($booking['start_time'] ?? 'now')) ?></td>
                                            <td><?= date('H:i', strtotime($booking['end_time'] ?? 'now')) ?></td>
                                            <td>
                                                <span class="badge bg-<?=
                                                                        ($booking['status'] ?? '') == 'đã xác nhận' ? 'success' : (($booking['status'] ?? '') == 'chờ xác nhận' ? 'warning' : (($booking['status'] ?? '') == 'đã hủy' ? 'danger' : 'secondary'))
                                                                        ?>">
                                                    <?= htmlspecialchars($booking['status'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Usage Report -->
        <div class="col-12 mb-4" id="roomUsageReportSection" style="<?= ($data['report_type'] ?? '') != 'all' && ($data['report_type'] ?? '') != 'room_usage' ? 'display: none;' : '' ?>">
            <div class="card shadow chart-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between chart-header" style="background-color: #1cc88a; color: white;">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-door-open me-2"></i>Báo cáo sử dụng phòng</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-1"></i> Tải xuống</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-print me-1"></i> In báo cáo</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body chart-container">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="roomUsageTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Phòng</th>
                                    <th>Loại phòng</th>
                                    <th>Số lượt đặt</th>
                                    <th>Tổng thời gian sử dụng</th>
                                    <th>Tỷ lệ sử dụng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['room_usage']) && is_array($data['room_usage'])): ?>
                                    <?php foreach ($data['room_usage'] as $usage): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($usage['room_name'] ?? 'N/A') ?></strong></td>
                                            <td><?= htmlspecialchars($usage['room_type'] ?? 'N/A') ?></td>
                                            <td><?= $usage['booking_count'] ?? 0 ?></td>
                                            <td><?= $usage['total_hours'] ?? 0 ?> giờ</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $usage['usage_percentage'] ?? 0 ?>%;"
                                                        aria-valuenow="<?= $usage['usage_percentage'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100">
                                                        <?= $usage['usage_percentage'] ?? 0 ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Activity Report -->
        <div class="col-12 mb-4" id="userActivityReportSection" style="<?= ($data['report_type'] ?? '') != 'all' && ($data['report_type'] ?? '') != 'user_activity' ? 'display: none;' : '' ?>">
            <div class="card shadow chart-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between chart-header" style="background-color: #36b9cc; color: white;">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-user-clock me-2"></i>Báo cáo hoạt động người dùng</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-1"></i> Tải xuống</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-print me-1"></i> In báo cáo</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body chart-container">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="userActivityTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Người dùng</th>
                                    <th>Vai trò</th>
                                    <th>Đặt phòng</th>
                                    <th>Đăng nhập gần nhất</th>
                                    <th>Hoạt động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['user_activity']) && is_array($data['user_activity'])): ?>
                                    <?php foreach ($data['user_activity'] as $activity): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($activity['name'] ?? 'N/A') ?></strong></td>
                                            <td>
                                                <span class="badge bg-<?= ($activity['role'] ?? '') == 'admin' ? 'danger' : 'primary' ?>">
                                                    <?= htmlspecialchars($activity['role'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td><?= $activity['booking_count'] ?? 0 ?></td>
                                            <td><?= $activity['last_login'] ? date('d/m/Y H:i', strtotime($activity['last_login'])) : 'N/A' ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $activity['activity_level'] ?? 0 ?>%;"
                                                            aria-valuenow="<?= $activity['activity_level'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="ms-2"><?= $activity['activity_level'] ?? 0 ?>%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Report -->
        <div class="col-12 mb-4" id="maintenanceReportSection" style="<?= ($data['report_type'] ?? '') != 'all' && ($data['report_type'] ?? '') != 'maintenance' ? 'display: none;' : '' ?>">
            <div class="card shadow chart-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between chart-header" style="background-color: #f6c23e; color: white;">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-tools me-2"></i>Báo cáo bảo trì</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-1"></i> Tải xuống</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-print me-1"></i> In báo cáo</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body chart-container">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="maintenanceTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Phòng</th>
                                    <th>Vấn đề</th>
                                    <th>Người báo cáo</th>
                                    <th>Ngày báo cáo</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày hoàn thành</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['maintenance']) && is_array($data['maintenance'])): ?>
                                    <?php foreach ($data['maintenance'] as $item): ?>
                                        <tr>
                                            <td><?= $item['id'] ?? '' ?></td>
                                            <td><strong><?= htmlspecialchars($item['room_name'] ?? 'N/A') ?></strong></td>
                                            <td><?= htmlspecialchars($item['issue'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($item['reported_by'] ?? 'N/A') ?></td>
                                            <td><?= date('d/m/Y', strtotime($item['reported_date'] ?? 'now')) ?></td>
                                            <td>
                                                <span class="badge bg-<?=
                                                                        ($item['status'] ?? '') == 'đã hoàn thành' ? 'success' : (($item['status'] ?? '') == 'đang xử lý' ? 'warning' : (($item['status'] ?? '') == 'chưa xử lý' ? 'danger' : 'secondary'))
                                                                        ?>">
                                                    <?= htmlspecialchars($item['status'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td><?= $item['completed_date'] ? date('d/m/Y', strtotime($item['completed_date'])) : 'N/A' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Card styles */
    .card.border-left-primary {
        border-left: .25rem solid #4e73df !important;
    }

    .card.border-left-success {
        border-left: .25rem solid #1cc88a !important;
    }

    .card.border-left-info {
        border-left: .25rem solid #36b9cc !important;
    }

    .card.border-left-warning {
        border-left: .25rem solid #f6c23e !important;
    }

    /* Chart containers */
    .chart-area {
        position: relative;
        height: 300px;
        margin: 0 auto;
    }

    .chart-pie {
        position: relative;
        height: 300px;
        margin: 0 auto;
    }

    /* Enhanced chart styles */
    .chart-card {
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }

    .chart-header {
        border-bottom: none !important;
    }

    .chart-container {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Progress bar enhancement */
    .progress {
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
        background-color: rgba(54, 185, 204, 0.15);
    }

    .progress-bar {
        border-radius: 5px;
        position: relative;
        overflow: hidden;
    }

    .progress-bar::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg,
                rgba(255, 255, 255, 0.2) 25%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0.2) 75%);
        animation: shine 2s infinite linear;
    }

    @keyframes shine {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    /* Report table enhancements */
    .table-hover tbody tr:hover {
        background-color: rgba(54, 185, 204, 0.05) !important;
        transition: background-color 0.2s ease;
    }

    .table th {
        border-top: none;
        border-bottom: 2px solid #e3e6f0;
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    /* Filter card enhancement */
    .bg-gradient-light {
        background: linear-gradient(135deg, #f8f9fc 0%, #eaecf4 100%) !important;
    }

    /* DataTable enhancement */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: #858796;
        margin-bottom: 0.5rem;
        margin-top: 0.5rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.375rem;
        border: 1px solid #d1d3e2;
        margin-left: 0.5rem;
        padding: 0.25rem 0.5rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(to bottom, #4e73df 0%, #2754e6 100%);
        border: 1px solid #4e73df;
        color: #fff !important;
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#bookingsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
            },
            order: [
                [0, 'desc']
            ],
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Tất cả"]
            ]
        });

        $('#roomUsageTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
            },
            order: [
                [3, 'desc']
            ],
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Tất cả"]
            ]
        });

        $('#userActivityTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
            },
            order: [
                [2, 'desc']
            ],
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Tất cả"]
            ]
        });

        $('#maintenanceTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
            },
            order: [
                [0, 'desc']
            ],
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Tất cả"]
            ]
        });

        // Toggle custom date range based on selection
        $('#time_range').change(function() {
            if ($(this).val() === 'custom') {
                $('#date_range_container').show();
            } else {
                $('#date_range_container').hide();
            }
        });

        // Reset filter
        $('#resetFilter').click(function() {
            $('#report_type').val('all');
            $('#time_range').val('month');
            $('#date_range_container').hide();
            $('#start_date').val('<?= date('Y-m-d', strtotime('-30 days')) ?>');
            $('#end_date').val('<?= date('Y-m-d') ?>');
        });

        // Initialize booking trends chart with enhanced styling
        var bookingCtx = document.getElementById('bookingTrendsChart');
        if (bookingCtx) {
            // Create gradient fill
            var ctx = bookingCtx.getContext('2d');
            var gradientFill = ctx.createLinearGradient(0, 0, 0, 350);
            gradientFill.addColorStop(0, "rgba(78, 115, 223, 0.3)");
            gradientFill.addColorStop(1, "rgba(78, 115, 223, 0.0)");

            var myLineChart = new Chart(bookingCtx, {
                type: 'line',
                data: {
                    labels: ["T1", "T2", "T3", "T4", "T5", "T6", "T7", "T8", "T9", "T10", "T11", "T12"],
                    datasets: [{
                        label: "Đặt phòng",
                        lineTension: 0.3,
                        backgroundColor: gradientFill,
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 4,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "#fff",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: <?= isset($data['booking_trends']) ? json_encode($data['booking_trends']) : '[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]' ?>,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7,
                                fontColor: "#77838f",
                                fontSize: 11
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                beginAtZero: true,
                                fontColor: "#77838f",
                                fontSize: 11,
                                callback: function(value) {
                                    return value;
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244, 0.7)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + ': ' + tooltipItem.yLabel + ' lượt';
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: false
                    }
                }
            });
        }

        // Initialize room usage pie chart with enhanced styling
        var roomUsageCtx = document.getElementById('roomUsageChart');
        if (roomUsageCtx) {
            var myPieChart = new Chart(roomUsageCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= isset($data['room_usage_labels']) ? json_encode($data['room_usage_labels']) : '["Phòng học", "Phòng thực hành", "Phòng hội thảo"]' ?>,
                    datasets: [{
                        data: <?= isset($data['room_usage_data']) ? json_encode($data['room_usage_data']) : '[45, 30, 25]' ?>,
                        backgroundColor: [
                            'rgba(78, 115, 223, 0.9)',
                            'rgba(28, 200, 138, 0.9)',
                            'rgba(54, 185, 204, 0.9)'
                        ],
                        hoverBackgroundColor: [
                            'rgba(46, 89, 217, 1)',
                            'rgba(23, 166, 115, 1)',
                            'rgba(44, 159, 175, 1)'
                        ],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var currentValue = dataset.data[tooltipItem.index];
                                var total = dataset.data.reduce(function(previousValue, currentValue) {
                                    return previousValue + currentValue;
                                });
                                var percentage = Math.round((currentValue / total) * 100);
                                return data.labels[tooltipItem.index] + ': ' + percentage + '%';
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            fontColor: '#858796',
                            boxWidth: 12,
                            padding: 15
                        }
                    },
                    cutoutPercentage: 70,
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    },
                    elements: {
                        arc: {
                            borderWidth: 2
                        }
                    }
                }
            });
        }

        // Toggle sections based on report type selection
        $('#report_type').change(function() {
            const reportType = $(this).val();
            if (reportType === 'all') {
                $('#bookingsReportSection, #roomUsageReportSection, #userActivityReportSection, #maintenanceReportSection').show();
            } else {
                $('#bookingsReportSection, #roomUsageReportSection, #userActivityReportSection, #maintenanceReportSection').hide();
                $(`#${reportType}ReportSection`).show();
            }
        });

        // Print report
        $('#printReport').click(function() {
            window.print();
        });

        // Export report
        $('#exportReport').click(function() {
            alert('Chức năng xuất báo cáo đang được phát triển');
        });
    });
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>