<?php

/**
 * Hiển thị các thông báo từ session
 *
 * File này chuyển đổi các thông báo từ session sang AlertHelper
 * để hiển thị theo định dạng mới
 */

// Đảm bảo AlertHelper đã được include
if (!class_exists('AlertHelper')) {
    require_once dirname(dirname(__DIR__)) . '/Helpers/AlertHelper.php';
}

// Chuyển đổi thông báo lỗi từ session
if (isset($_SESSION['error'])) {
    AlertHelper::error($_SESSION['error']);
    unset($_SESSION['error']);
}

// Chuyển đổi thông báo thành công từ session
if (isset($_SESSION['success'])) {
    AlertHelper::success($_SESSION['success']);
    unset($_SESSION['success']);
}

// Chuyển đổi thông báo thông tin từ session
if (isset($_SESSION['info'])) {
    AlertHelper::info($_SESSION['info']);
    unset($_SESSION['info']);
}

// Chuyển đổi thông báo cảnh báo từ session
if (isset($_SESSION['warning'])) {
    AlertHelper::warning($_SESSION['warning']);
    unset($_SESSION['warning']);
}

// Chuyển đổi thông báo từ query string - chỉ chuyển đổi nếu chưa có thông báo trong session
// Và chỉ xử lý một loại thông báo duy nhất từ URL parameters
if (empty($_SESSION['alerts'])) {
    if (isset($_GET['error'])) {
        AlertHelper::error($_GET['error']);
    } elseif (isset($_GET['success'])) {
        AlertHelper::success($_GET['success']);
    } elseif (isset($_GET['message'])) {
        AlertHelper::info($_GET['message']);
    } elseif (isset($_GET['warning'])) {
        AlertHelper::warning($_GET['warning']);
    }

    // Xóa các tham số thông báo khỏi URL sau khi đã xử lý
    if (isset($_GET['error']) || isset($_GET['success']) || isset($_GET['message']) || isset($_GET['warning'])) {
        $url = strtok($_SERVER['REQUEST_URI'], '?');
        $params = $_GET;
        unset($params['error'], $params['success'], $params['message'], $params['warning']);

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        // Sử dụng JavaScript để cập nhật URL mà không cần tải lại trang
        echo '<script>window.history.replaceState({}, document.title, "' . $url . '");</script>';
    }
}

// Hiển thị các thông báo
include dirname(__DIR__) . '/components/alerts.php';
