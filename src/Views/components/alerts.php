<?php
/**
 * View hiển thị các thông báo alert
 * 
 * Sử dụng:
 * 1. Thêm thông báo: AlertHelper::add('Nội dung thông báo', 'success');
 * 2. Hiển thị: include 'src/Views/components/alerts.php';
 */

// Đảm bảo AlertHelper đã được include
if (!class_exists('AlertHelper')) {
    require_once dirname(dirname(__DIR__)) . '/Helpers/AlertHelper.php';
}

// Lấy tất cả thông báo
$alerts = AlertHelper::getAll();

// Hiển thị các thông báo
if (!empty($alerts)): 
?>
<div class="alert-container my-3">
    <?php foreach ($alerts as $alert): ?>
        <div class="alert alert-<?= htmlspecialchars($alert['type']) ?> <?= $alert['dismissible'] ? 'alert-dismissible fade show' : '' ?>" role="alert">
            <?= htmlspecialchars($alert['message']) ?>
            
            <?php if ($alert['dismissible']): ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
