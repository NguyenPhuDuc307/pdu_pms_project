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
        <!-- Placeholder for future content -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Thông tin hệ thống</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Chào mừng đến với hệ thống Quản lý Phòng Đào tạo. Sử dụng menu bên trái để truy cập các chức năng của hệ thống.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Removed unnecessary closing div -->



    <?php
    // Lấy nội dung đã buffer
    $pageContent = ob_get_clean();

    // Set page role
    $pageRole = 'admin';

    // Include the main layout
    include dirname(dirname(__DIR__)) . '/layouts/main_layout.php';
    ?>