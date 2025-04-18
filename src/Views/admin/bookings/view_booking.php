<?php
// Trang xem chi tiết đặt phòng

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Lấy thông tin đặt phòng từ controller
$booking = $data['booking'] ?? null;

if (!$booking) {
    header('Location: /pdu_pms_project/public/admin/manage_bookings?error=Booking not found');
    exit;
}

// Tiêu đề trang
$pageTitle = 'Chi tiết đặt phòng #' . $booking['id'];

// Load header
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Chi tiết đặt phòng #<?php echo htmlspecialchars($booking['id']); ?></h6>
                    <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Thông tin đặt phòng</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>ID</th>
                                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Phòng</th>
                                            <td><?php echo htmlspecialchars($booking['room_name'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Thời gian bắt đầu</th>
                                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($booking['start_time']))); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Thời gian kết thúc</th>
                                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($booking['end_time']))); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Mục đích sử dụng</th>
                                            <td><?php echo htmlspecialchars($booking['purpose'] ?? 'Không có'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Trạng thái</th>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $statusText = '';

                                                switch ($booking['status']) {
                                                    case 'pending':
                                                        $statusClass = 'bg-warning-subtle text-warning';
                                                        $statusText = 'Chờ duyệt';
                                                        break;
                                                    case 'approved':
                                                        $statusClass = 'bg-success-subtle text-success';
                                                        $statusText = 'Đã duyệt';
                                                        break;
                                                    case 'rejected':
                                                        $statusClass = 'bg-danger-subtle text-danger';
                                                        $statusText = 'Từ chối';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'bg-secondary-subtle text-secondary';
                                                        $statusText = 'Đã hủy';
                                                        break;
                                                    case 'completed':
                                                        $statusClass = 'bg-primary-subtle text-primary';
                                                        $statusText = 'Hoàn thành';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-secondary-subtle text-secondary';
                                                        $statusText = 'Không xác định';
                                                }
                                                ?>
                                                <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Thông tin người dùng</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Loại người dùng</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($booking['user_role'] === 'teacher' ? 'Giáo viên' : 'Sinh viên');
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ID người dùng</th>
                                            <td>
                                                <?php echo htmlspecialchars($booking['user_id'] ?? 'N/A'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tên người dùng</th>
                                            <td>
                                                <?php echo htmlspecialchars($booking['user_name'] ?? 'N/A'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Mã lớp</th>
                                            <td><?php echo htmlspecialchars($booking['class_code'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Ngày tạo</th>
                                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($booking['created_at']))); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-end">
                            <a href="/pdu_pms_project/public/admin/edit_booking?id=<?php echo htmlspecialchars($booking['id']); ?>" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-2"></i>Chỉnh sửa
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBookingModal">
                                <i class="fas fa-trash-alt me-2"></i>Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xóa -->
<div class="modal fade" id="deleteBookingModal" tabindex="-1" aria-labelledby="deleteBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBookingModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa đặt phòng này không? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="/pdu_pms_project/public/admin/delete_booking?id=<?php echo htmlspecialchars($booking['id']); ?>" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>

<?php
// Load footer
require_once __DIR__ . '/../../layouts/admin_footer.php';
?>