<?php
// Đảm bảo người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Yêu cầu bảo trì";
$pageSubtitle = "Quản lý các yêu cầu bảo trì thiết bị và phòng học";
$pageIcon = "fas fa-wrench";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Yêu cầu bảo trì', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/maintenance/create" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i>Tạo yêu cầu mới
        </a>
    </div>

    <!-- Danh sách yêu cầu bảo trì -->
    <div class="card shadow mb-4 rounded">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách yêu cầu bảo trì của bạn</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered maintenance-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Phòng</th>
                            <th>Thiết bị</th>
                            <th>Mô tả vấn đề</th>
                            <th>Mức độ ưu tiên</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Ghi chú admin</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['requests']) && is_array($data['requests']) && count($data['requests']) > 0): ?>
                            <?php foreach ($data['requests'] as $request): ?>
                                <tr>
                                    <td><?= htmlspecialchars($request['id']) ?></td>
                                    <td><?= htmlspecialchars($request['room_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($request['equipment_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($request['issue_description']) ?></td>
                                    <td>
                                        <?php
                                        $priorityClass = '';
                                        switch ($request['priority']) {
                                            case 'khẩn cấp':
                                                $priorityClass = 'bg-danger';
                                                break;
                                            case 'cao':
                                                $priorityClass = 'bg-warning';
                                                break;
                                            case 'trung bình':
                                                $priorityClass = 'bg-info';
                                                break;
                                            case 'thấp':
                                                $priorityClass = 'bg-secondary';
                                                break;
                                            default:
                                                $priorityClass = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?= $priorityClass ?>"><?= htmlspecialchars(ucfirst($request['priority'])) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        switch ($request['status']) {
                                            case 'đang chờ':
                                                $statusClass = 'bg-warning';
                                                break;
                                            case 'đang xử lý':
                                                $statusClass = 'bg-info';
                                                break;
                                            case 'đã xử lý':
                                                $statusClass = 'bg-success';
                                                break;
                                            case 'từ chối':
                                                $statusClass = 'bg-danger';
                                                break;
                                            default:
                                                $statusClass = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($request['status'])) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($request['created_at']))) ?></td>
                                    <td><?= !empty($request['admin_notes']) ? htmlspecialchars($request['admin_notes']) : '<span class="text-muted">Chưa có ghi chú</span>' ?></td>
                                    <td>
                                        <?php if ($request['status'] === 'đang chờ' || $request['status'] === 'từ chối'): ?>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $request['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <!-- Modal Xóa -->
                                            <div class="modal fade" id="deleteModal<?= $request['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $request['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel<?= $request['id'] ?>">Xác nhận xóa yêu cầu</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Bạn có chắc chắn muốn xóa yêu cầu bảo trì này?</p>
                                                            <p class="text-danger">Lưu ý: Hành động này không thể hoàn tác.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <a href="/pdu_pms_project/public/maintenance/delete?id=<?= $request['id'] ?>" class="btn btn-danger">Xóa</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Bạn chưa có yêu cầu bảo trì nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo DataTable
        if ($.fn.DataTable) {
            // Hủy bỏ DataTable nếu đã được khởi tạo trước đó
            if ($.fn.dataTable.isDataTable('.maintenance-table')) {
                $('.maintenance-table').DataTable().destroy();
            }

            // Khởi tạo DataTable mới
            $('.maintenance-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
                },
                order: [
                    [0, 'desc']
                ]
            });
        }
    });
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = $_SESSION['role'];

// Include the main layout
include dirname(__DIR__) . '/layouts/main_layout.php';
?>