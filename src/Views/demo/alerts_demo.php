<?php

/**
 * Trang demo hiển thị tất cả các loại alert
 */

// Đảm bảo AlertHelper đã được include
require_once dirname(dirname(__DIR__)) . '/Helpers/AlertHelper.php';

// Tiêu đề trang
$pageTitle = 'Demo Alert Components';

// Thêm các alert demo
AlertHelper::add('Đây là thông báo primary', AlertHelper::PRIMARY);
AlertHelper::add('Đây là thông báo secondary', AlertHelper::SECONDARY);
AlertHelper::success('Đây là thông báo thành công');
AlertHelper::error('Đây là thông báo lỗi');
AlertHelper::warning('Đây là thông báo cảnh báo');
AlertHelper::info('Đây là thông báo thông tin');
AlertHelper::add('Đây là thông báo light', AlertHelper::LIGHT);
AlertHelper::add('Đây là thông báo dark', AlertHelper::DARK);
AlertHelper::add('Đây là thông báo không thể đóng', AlertHelper::INFO, false);

// Sử dụng main layout
// Bắt đầu output buffering để capture nội dung
ob_start();

?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Demo Alert Components</h1>
    </div>

    <!-- Hiển thị các alert -->
    <?php include dirname(dirname(__DIR__)) . '/Views/components/alerts.php'; ?>

    <!-- Thông tin hướng dẫn sử dụng -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hướng dẫn sử dụng Alert</h6>
        </div>
        <div class="card-body">
            <h5>1. Thêm thông báo</h5>
            <pre><code>
// Thêm thông báo cơ bản
AlertHelper::add('Nội dung thông báo', AlertHelper::PRIMARY);

// Các helper method
AlertHelper::success('Thao tác thành công');
AlertHelper::error('Đã xảy ra lỗi');
AlertHelper::warning('Cảnh báo');
AlertHelper::info('Thông tin');

// Thông báo không thể đóng
AlertHelper::add('Thông báo không thể đóng', AlertHelper::INFO, false);
            </code></pre>

            <h5>2. Hiển thị thông báo</h5>
            <pre><code>
// Trong file view
include 'src/Views/components/alerts.php';
            </code></pre>

            <h5>3. Các loại thông báo</h5>
            <ul>
                <li><code>AlertHelper::PRIMARY</code> - Thông báo primary</li>
                <li><code>AlertHelper::SECONDARY</code> - Thông báo secondary</li>
                <li><code>AlertHelper::SUCCESS</code> - Thông báo thành công</li>
                <li><code>AlertHelper::DANGER</code> - Thông báo lỗi</li>
                <li><code>AlertHelper::WARNING</code> - Thông báo cảnh báo</li>
                <li><code>AlertHelper::INFO</code> - Thông báo thông tin</li>
                <li><code>AlertHelper::LIGHT</code> - Thông báo light</li>
                <li><code>AlertHelper::DARK</code> - Thông báo dark</li>
            </ul>
        </div>
    </div>

    <!-- Ví dụ về Bootstrap Alert -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ví dụ về Bootstrap Alert</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-primary" role="alert">
                A simple primary alert—check it out!
            </div>
            <div class="alert alert-secondary" role="alert">
                A simple secondary alert—check it out!
            </div>
            <div class="alert alert-success" role="alert">
                A simple success alert—check it out!
            </div>
            <div class="alert alert-danger" role="alert">
                A simple danger alert—check it out!
            </div>
            <div class="alert alert-warning" role="alert">
                A simple warning alert—check it out!
            </div>
            <div class="alert alert-info" role="alert">
                A simple info alert—check it out!
            </div>
            <div class="alert alert-light" role="alert">
                A simple light alert—check it out!
            </div>
            <div class="alert alert-dark" role="alert">
                A simple dark alert—check it out!
            </div>
        </div>
    </div>
</div>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role và title
$pageRole = 'admin';
$pageTitle = 'Demo Alert Components - PDU PMS';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
