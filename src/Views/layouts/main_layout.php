<?php

/**
 * Main layout file for all pages
 * This file replaces the separate layout files for admin, teacher, and student
 *
 * Usage:
 * 1. Set $pageTitle, $pageRole, and $pageContent variables before including this file
 * 2. Include this file in your view
 *
 * Example:
 * $pageTitle = "Dashboard";
 * $pageRole = "admin"; // admin, teacher, student, or public
 * ob_start();
 * // Your page content here
 * $pageContent = ob_get_clean();
 * include __DIR__ . '/../layouts/main_layout.php';
 */

// Ensure user has appropriate role if not public page
if ($pageRole !== 'public' && (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $pageRole)) {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Get current page for active menu highlighting
$request_uri = $_SERVER['REQUEST_URI'];
$current_page = '';

// Extract the current page from the URI
if (strpos($request_uri, "/{$pageRole}/") !== false) {
    $path = parse_url($request_uri, PHP_URL_PATH);
    $path_parts = explode('/', $path);

    // Find the role index in the path
    $role_index = array_search($pageRole, $path_parts);

    // Get the next segment after role if it exists
    if ($role_index !== false && isset($path_parts[$role_index + 1]) && !empty($path_parts[$role_index + 1])) {
        $current_page = $path_parts[$role_index + 1];
    }
}

// Default to 'index' if on the main role page
if (empty($current_page) || $current_page === $pageRole) {
    $current_page = 'index';
}

// Set title if not already set
$title = $pageTitle ?? 'PDU - PMS';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <!-- Page specific styles -->
    <?php if (isset($pageStyles)): ?>
        <style>
            <?php echo $pageStyles; ?>
        </style>
    <?php endif; ?>
</head>

<body class="<?php echo $pageRole; ?>-layout d-flex flex-column min-vh-100" style="background-color: #f5f7fa; padding-top: 70px;">
    <style>
        :root {
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            --font-secondary: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            font-family: var(--font-primary);
            font-weight: 400;
            letter-spacing: -0.01em;
            line-height: 1.6;
            color: #333;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand,
        .nav-link,
        .btn,
        .dropdown-item {
            font-family: var(--font-secondary);
            font-weight: 400;
            letter-spacing: -0.02em;
        }

        .card-title,
        .alert,
        .badge {
            font-family: var(--font-secondary);
        }
    </style>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm py-2">
        <div class="container-fluid">
            <a class="navbar-brand fw-semibold" href="<?php
                                                        if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
                                                            echo '/pdu_pms_project/public/' . $_SESSION['role'];
                                                        } else {
                                                            echo '/pdu_pms_project/public/';
                                                        }
                                                        ?>">
                <i class="fas fa-school me-2"></i>PDU - PMS
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link rounded px-3 mx-1" href="/pdu_pms_project/public/page/contact">
                            <i class="fas fa-envelope me-1"></i>Liên hệ
                        </a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <!-- Admin Menu - Quản lý -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle rounded px-3 mx-1" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-tachometer-alt me-1"></i>Quản lý
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'index' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin">
                                        <i class="fas fa-tachometer-alt me-1"></i>Bảng điều khiển
                                    </a>
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'manage_users' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin/manage_users">
                                        <i class="fas fa-users me-1"></i>Người dùng
                                    </a>
                                </ul>
                            </li>

                            <!-- Admin Menu - Lịch & Phòng -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle rounded px-3 mx-1" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-calendar-alt me-1"></i>Lịch & Phòng
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'manage_bookings' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin/manage_bookings">
                                        <i class="fas fa-calendar-check me-1"></i>Đặt phòng
                                    </a>
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'manage_timetable' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin/manage_timetable">
                                        <i class="fas fa-calendar-alt me-1"></i>Lịch dạy
                                    </a>
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'auto_schedule' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin/auto_schedule">
                                        <i class="fas fa-magic me-1"></i>Xếp lịch tự động
                                    </a>
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'search_rooms' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin/search_rooms">
                                        <i class="fas fa-search me-1"></i>Tìm phòng
                                    </a>
                                </ul>
                            </li>

                            <!-- Admin Menu - Cơ sở vật chất -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle rounded px-3 mx-1" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-building me-1"></i>Cơ sở vật chất
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'manage_rooms' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin/manage_rooms">
                                        <i class="fas fa-door-open me-1"></i>Quản lý phòng
                                    </a>
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'equipments' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin/equipments">
                                        <i class="fas fa-tools me-1"></i>Thiết bị
                                    </a>
                                    <a class="dropdown-item <?= $pageRole === 'admin' && $current_page === 'maintenance_requests' ? 'active' : '' ?>" href="/pdu_pms_project/public/admin/maintenance_requests">
                                        <i class="fas fa-wrench me-1"></i>Yêu cầu bảo trì
                                    </a>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] === 'teacher'): ?>
                            <!-- Teacher Menu - Quản lý -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle rounded px-3 mx-1" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-tachometer-alt me-1"></i>Quản lý
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    <a class="dropdown-item <?= $pageRole === 'teacher' && $current_page === 'index' ? 'active' : '' ?>" href="/pdu_pms_project/public/teacher">
                                        <i class="fas fa-tachometer-alt me-1"></i>Bảng điều khiển
                                    </a>
                                </ul>
                            </li>

                            <!-- Teacher Menu - Lịch & Phòng -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle rounded px-3 mx-1" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-calendar-alt me-1"></i>Lịch & Phòng
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    <a class="dropdown-item <?= $pageRole === 'teacher' && $current_page === 'my_timetables' ? 'active' : '' ?>" href="/pdu_pms_project/public/teacher/my_timetables">
                                        <i class="fas fa-calendar-alt me-1"></i>Lịch dạy của tôi
                                    </a>
                                    <a class="dropdown-item <?= $pageRole === 'teacher' && $current_page === 'book_room' ? 'active' : '' ?>" href="/pdu_pms_project/public/teacher/book_room">
                                        <i class="fas fa-bookmark me-1"></i>Đặt phòng
                                    </a>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] === 'student'): ?>
                            <!-- Student Menu - Quản lý -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle rounded px-3 mx-1" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-tachometer-alt me-1"></i>Quản lý
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    <a class="dropdown-item <?= $pageRole === 'student' && $current_page === 'index' ? 'active' : '' ?>" href="/pdu_pms_project/public/student">
                                        <i class="fas fa-tachometer-alt me-1"></i>Bảng điều khiển
                                    </a>
                                </ul>
                            </li>

                            <!-- Student Menu - Lịch & Phòng -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle rounded px-3 mx-1" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-calendar-alt me-1"></i>Lịch & Phòng
                                </a>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    <a class="dropdown-item <?= $pageRole === 'student' && ($current_page === 'book_room' || $current_page === 'search_rooms' || $current_page === 'room_detail') ? 'active' : '' ?>" href="/pdu_pms_project/public/student/book_room">
                                        <i class="fas fa-bookmark me-1"></i>Đặt phòng
                                    </a>
                                    <a class="dropdown-item <?= $pageRole === 'student' && $current_page === 'my_bookings' ? 'active' : '' ?>" href="/pdu_pms_project/public/student/my_bookings">
                                        <i class="fas fa-calendar-alt me-1"></i>Lịch đặt phòng
                                    </a>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Profile Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center rounded px-3" href="#" role="button" data-bs-toggle="dropdown">
                                <span class="d-none d-md-inline fw-semibold"><?php echo $_SESSION['full_name']; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <a class="dropdown-item py-2" href="/pdu_pms_project/public/profile">
                                    <i class="fas fa-user me-2"></i>Hồ sơ
                                </a>
                                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'teacher' || $_SESSION['role'] === 'student'): ?>
                                    <a class="dropdown-item py-2" href="/pdu_pms_project/public/<?php echo $_SESSION['role']; ?>">
                                        <i class="fas fa-tachometer-alt me-2"></i>Bảng điều khiển
                                    </a>
                                <?php endif; ?>
                                <hr class="dropdown-divider">
                                <a class="dropdown-item py-2 text-danger" href="/pdu_pms_project/public/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login/Register buttons -->
                        <li class="nav-item mx-1">
                            <a class="nav-link rounded px-3" href="/pdu_pms_project/public/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link btn btn-outline-light rounded px-3" href="/pdu_pms_project/public/register">
                                <i class="fas fa-user-plus me-1"></i>Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid flex-grow-1">
        <?php
        // Hiển thị thông báo alert
        include_once dirname(__DIR__) . '/components/session_alerts.php';

        // Display page content
        echo $pageContent ?? '';
        ?>
    </div>

    <!-- Footer -->
    <?php if ($pageRole === 'public'): ?>
        <footer class="py-3 bg-primary bg-gradient text-white mt-auto">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-school fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-semibold">PDU PMS</h6>
                                <p class="mb-0 small">Hệ thống Quản lý Phòng Đào tạo</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1 small"><i class="fas fa-envelope me-2"></i>contact@pdupms.edu.vn</p>
                        <p class="mb-1 small"><i class="fas fa-phone me-2"></i>(024) 7300 1955</p>
                        <p class="mt-2 mb-0 small">© <?= date('Y') ?> PDU PMS. Bản quyền thuộc về Phòng Đào tạo</p>
                    </div>
                </div>
            </div>
        </footer>
    <?php endif; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/vi.js"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            const popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(alert => {
                    if (alert && bootstrap.Alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);

            // Initialize DataTables if any tables with .datatable class exist
            if (typeof $.fn.DataTable !== 'undefined' && $('.datatable').length > 0) {
                $('.datatable').DataTable({
                    language: {
                        // Sử dụng cấu hình trực tiếp thay vì tải từ URL để tránh lỗi CORS
                        emptyTable: "Không có dữ liệu trong bảng",
                        info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                        infoEmpty: "Hiển thị 0 đến 0 của 0 mục",
                        infoFiltered: "(lọc từ _MAX_ mục)",
                        lengthMenu: "Hiển thị _MENU_ mục",
                        loadingRecords: "Đang tải...",
                        processing: "Đang xử lý...",
                        search: "Tìm kiếm:",
                        zeroRecords: "Không tìm thấy kết quả phù hợp",
                        paginate: {
                            first: "Đầu",
                            last: "Cuối",
                            next: "Tiếp",
                            previous: "Trước"
                        }
                    },
                    responsive: true
                });
            }
        });
    </script>

    <!-- Page specific scripts -->
    <?php if (isset($pageScripts)): ?>
        <script>
            <?php echo $pageScripts; ?>
        </script>
    <?php endif; ?>
</body>

</html>