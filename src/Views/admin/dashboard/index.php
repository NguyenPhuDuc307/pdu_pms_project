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

    <!-- Yêu cầu bảo trì Cards -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark">Yêu cầu bảo trì</h5>
                <a href="/pdu_pms_project/public/admin/maintenance_requests" class="btn btn-sm btn-primary">
                    <i class="fas fa-tools me-1"></i> Quản lý yêu cầu
                </a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-primary text-white p-3 rounded">
                                <i class="fas fa-tools fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Tổng yêu cầu</div>
                            <div class="h3 mb-0 fw-bold"><?= $data['maintenance_stats']['total'] ?? 0 ?></div>
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
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Đang chờ xử lý</div>
                            <div class="h3 mb-0 fw-bold"><?= $data['maintenance_stats']['pending'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow-sm h-100 py-2">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-danger text-white p-3 rounded">
                                <i class="fas fa-fire-alt fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Khẩn cấp</div>
                            <div class="h3 mb-0 fw-bold"><?= $data['maintenance_stats']['urgent'] ?? 0 ?></div>
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
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Đã xử lý</div>
                            <div class="h3 mb-0 fw-bold"><?= $data['maintenance_stats']['completed'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quản lý hệ thống -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark">Quản lý hệ thống</h5>
            </div>
        </div>

        <!-- Quản lý phòng -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-door-open me-2"></i>Quản lý phòng</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Quản lý thông tin phòng học, trạng thái và thiết bị trong phòng.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="/pdu_pms_project/public/admin/manage_rooms" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i> Danh sách phòng
                        </a>
                        <a href="/pdu_pms_project/public/admin/add_room" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Thêm phòng mới
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quản lý người dùng -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-users me-2"></i>Quản lý người dùng</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Quản lý tài khoản giáo viên, sinh viên và quản trị viên.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="/pdu_pms_project/public/admin/manage_users" class="btn btn-info text-white">
                            <i class="fas fa-list me-1"></i> Danh sách người dùng
                        </a>
                        <a href="/pdu_pms_project/public/admin/add_user" class="btn btn-success">
                            <i class="fas fa-user-plus me-1"></i> Thêm người dùng
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quản lý đặt phòng -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-calendar-check me-2"></i>Quản lý đặt phòng</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Quản lý lịch đặt phòng, lịch dạy và lịch học.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-success">
                            <i class="fas fa-list me-1"></i> Danh sách đặt phòng
                        </a>
                        <a href="/pdu_pms_project/public/admin/calendar_bookings" class="btn btn-primary">
                            <i class="fas fa-calendar-alt me-1"></i> Lịch đặt phòng
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quản lý bảo trì -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-tools me-2"></i>Quản lý bảo trì</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Quản lý yêu cầu bảo trì, sửa chữa thiết bị và phòng học.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="/pdu_pms_project/public/admin/maintenance_requests" class="btn btn-warning text-white">
                            <i class="fas fa-list me-1"></i> Danh sách yêu cầu
                        </a>
                        <a href="/pdu_pms_project/public/maintenance/create" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Tạo yêu cầu mới
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quản lý thiết bị -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-cogs me-2"></i>Quản lý thiết bị</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Quản lý danh sách thiết bị, phân bổ thiết bị cho các phòng.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="/pdu_pms_project/public/admin/equipments" class="btn btn-secondary">
                            <i class="fas fa-list me-1"></i> Danh sách thiết bị
                        </a>
                        <a href="/pdu_pms_project/public/admin/add_equipment" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Thêm thiết bị mới
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch dạy -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-calendar me-2"></i>Quản lý lịch dạy</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Quản lý lịch dạy của giáo viên, lịch học của các lớp.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="/pdu_pms_project/public/admin/manage_timetable" class="btn btn-danger">
                            <i class="fas fa-list me-1"></i> Danh sách lịch dạy
                        </a>
                        <a href="/pdu_pms_project/public/admin/add_timetable" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Thêm lịch dạy mới
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin hệ thống -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Thông tin hệ thống</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Chào mừng đến với hệ thống Quản lý Phòng Đào tạo. Sử dụng các nút bấm trên để truy cập nhanh các chức năng quản lý.
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