<?php

/**
 * Page Header Component
 *
 * Hiển thị tiêu đề trang, mô tả và breadcrumb
 *
 * Sử dụng:
 * include __DIR__ . '/../components/page_header.php';
 *
 * Các biến cần thiết:
 * $pageTitle - Tiêu đề trang
 * $pageSubtitle - Mô tả trang (tùy chọn)
 * $breadcrumbs - Mảng breadcrumb (tùy chọn)
 * Ví dụ: $breadcrumbs = [
 *   ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
 *   ['title' => 'Liên hệ', 'link' => '']
 * ];
 */

// Đảm bảo các biến tồn tại
$pageTitle = $pageTitle ?? 'Tiêu đề trang';
$pageSubtitle = $pageSubtitle ?? '';
$breadcrumbs = $breadcrumbs ?? [];

// Thêm trang chủ vào breadcrumb nếu chưa có
if (empty($breadcrumbs)) {
    $breadcrumbs[] = ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'];
    if (!empty($pageTitle)) {
        $breadcrumbs[] = ['title' => $pageTitle, 'link' => ''];
    }
}
?>

<!-- Breadcrumb -->
<?php if (!empty($breadcrumbs)): ?>
    <nav aria-label="breadcrumb" class="bg-light rounded py-2 px-3 mb-3">
        <ol class="breadcrumb mb-0">
            <?php foreach ($breadcrumbs as $index => $item): ?>
                <?php if ($index === count($breadcrumbs) - 1): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($item['title']) ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item">
                        <a href="<?= $item['link'] ?>" class="text-decoration-none"><?= htmlspecialchars($item['title']) ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif; ?>

<!-- Page Header -->
<div class="bg-primary text-white rounded-3 shadow-sm mb-3">
    <div class="container-fluid py-3 px-4">
        <div class="row align-items-center">
            <div class="col-md-10">
                <h1 class="h3 fw-bold mb-1"><?= htmlspecialchars($pageTitle) ?></h1>
                <?php if (!empty($pageSubtitle)): ?>
                    <p class="small mb-0 opacity-90"><?= htmlspecialchars($pageSubtitle) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-2 text-end d-none d-md-block">
                <?php if (isset($pageIcon)): ?>
                    <i class="<?= $pageIcon ?> fa-3x opacity-75"></i>
                <?php else: ?>
                    <i class="fas fa-file-alt fa-3x opacity-75"></i>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>