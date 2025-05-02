<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Thêm phòng mới";
$pageSubtitle = "Thêm phòng học mới vào hệ thống quản lý";
$pageIcon = "fas fa-plus-circle";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý phòng', 'link' => '/pdu_pms_project/public/admin/manage_rooms'],
    ['title' => 'Thêm phòng mới', 'link' => '']
];

// Thông báo kết quả nếu có
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

// Xóa thông báo sau khi hiển thị
if (isset($_SESSION['success_message'])) unset($_SESSION['success_message']);
if (isset($_SESSION['error_message'])) unset($_SESSION['error_message']);

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/manage_rooms" class="btn btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Quay lại
        </a>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>



    <div class="row">
        <!-- Room Form -->
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-door-open me-2"></i>Thông tin phòng</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="addRoomForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold">Tên phòng <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-door-closed"></i></span>
                                    <input type="text" class="form-control preview-trigger" id="name" name="name" placeholder="Nhập tên phòng" required>
                                </div>
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="capacity" class="form-label fw-bold">Số máy <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <input type="number" class="form-control preview-trigger" id="capacity" name="capacity" placeholder="Số máy trong phòng" min="1" value="30" required>
                                    <span class="input-group-text">máy</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">Trạng thái</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                    <select class="form-select preview-trigger" id="status" name="status">
                                        <option value="trống" selected>Trống</option>
                                        <option value="đang sử dụng">Đang sử dụng</option>
                                        <option value="bảo trì">Bảo trì</option>
                                    </select>
                                </div>
                            </div>
                        </div>



                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Mô tả</label>
                            <textarea class="form-control preview-trigger" id="description" name="description" rows="3" placeholder="Mô tả thêm về phòng học"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-light me-md-2" id="resetButton">
                                <i class="fas fa-redo me-1"></i> Đặt lại
                            </button>
                            <button type="submit" name="add_room" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Thêm phòng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Room preview styles */
    .preview-room-icon {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 80px;
        height: 80px;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .preview-room-details {
        background-color: #f8f9fc;
        border-radius: 0.5rem;
    }

    /* Form styles */
    .input-group-text {
        background-color: #f8f9fc;
        border-right: none;
    }

    .form-control,
    .form-select {
        border-left: none;
    }

    .input-group:focus-within .input-group-text {
        border-color: #bac8f3;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #bac8f3;
        box-shadow: none;
    }

    /* Card hover effect */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }

    /* Gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%) !important;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #36b9cc 0%, #258391 100%) !important;
    }

    /* Alert animation */
    .alert {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Badges */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Enhanced Info Card Styles */
    .info-card {
        overflow: hidden;
        border: none;
        border-radius: 0.75rem;
    }

    .info-card .card-header {
        border-bottom: none;
        padding-bottom: 0.75rem;
        background: linear-gradient(135deg, #36b9cc 0%, #1a8a98 100%) !important;
    }

    .info-card .card-body {
        padding: 1.5rem;
        background-color: #f8f9fc;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        background-color: white;
        border-radius: 0.5rem;
        padding: 0.75rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
        border-left: 3px solid #4e73df;
    }

    .info-item:hover {
        transform: translateX(5px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    .info-badge {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 2rem;
        height: 2rem;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        margin-right: 0.75rem;
        font-weight: bold;
        box-shadow: 0 0.25rem 0.5rem rgba(78, 115, 223, 0.2);
    }

    .info-content {
        flex: 1;
        padding-top: 0.25rem;
    }

    .note-box {
        display: flex;
        background-color: #fff7e1;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
        border-left: 3px solid #f6c23e;
    }

    .note-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 2.5rem;
        height: 2.5rem;
        background-color: #f6c23e;
        color: white;
        border-radius: 50%;
        margin-right: 1rem;
        box-shadow: 0 0.25rem 0.5rem rgba(246, 194, 62, 0.2);
    }

    .note-content {
        flex: 1;
    }

    .note-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .note-list li {
        padding: 0.5rem 0;
        border-bottom: 1px dashed rgba(246, 194, 62, 0.3);
        display: flex;
        align-items: center;
    }

    .note-list li:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        border: none;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .info-card h5 {
        display: inline-block;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid currentColor;
        font-weight: 600;
    }

    .text-info {
        color: #1a8a98 !important;
    }

    .text-warning {
        color: #e6b009 !important;
    }

    .note-list li i {
        transition: transform 0.2s ease;
    }

    .note-list li:hover i {
        transform: translateX(3px);
    }
</style>

<script>
    $(document).ready(function() {
        // Live Room Preview
        $('.preview-trigger').on('input change', function() {
            updateRoomPreview();
        });

        // Update preview initially
        updateRoomPreview();

        // Reset button also updates preview
        $('#resetButton').click(function() {
            setTimeout(updateRoomPreview, 100);
        });

        // Form validation
        $('#addRoomForm').submit(function(event) {
            var isValid = true;

            // Validate required fields
            if ($('#name').val().trim() === '') {
                showValidationError($('#name'), 'Vui lòng nhập tên phòng');
                isValid = false;
            }

            if ($('#capacity').val() <= 0) {
                showValidationError($('#capacity'), 'Số máy phải lớn hơn 0');
                isValid = false;
            }

            if (isValid) {
                // Show loading spinner on button
                $(this).find('button[type="submit"]').html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang xử lý...');
                $(this).find('button[type="submit"]').prop('disabled', true);
            }

            return isValid;
        });

        // Functions
        function updateRoomPreview() {
            // Update room name
            var roomName = $('#name').val() || '---';
            $('#previewRoomName').text(roomName);

            // Set fixed room type
            $('#previewRoomType').text('Phòng thực hành');

            // Update icon color based on status
            var status = $('#status').val();
            updateStatusColor(status);

            // Update capacity
            var capacity = $('#capacity').val() || '---';
            $('#previewCapacity').text(capacity + ' máy');

            // Update status badge
            var statusText = $('#status option:selected').text();
            var statusClass = getStatusClass(status);
            $('#previewStatus').html('<span class="badge bg-' + statusClass + '">' + statusText + '</span>');

            // Update description
            var description = $('#description').val() || '---';
            $('#previewDescription').text(description);
        }

        function getStatusClass(status) {
            switch (status) {
                case 'trống':
                    return 'success';
                case 'đang sử dụng':
                    return 'warning';
                case 'bảo trì':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        function updateStatusColor(status) {
            var iconElement = $('.preview-room-icon');

            // Remove existing classes
            iconElement.removeClass('bg-success bg-warning bg-danger bg-secondary');

            // Add appropriate class
            switch (status) {
                case 'trống':
                    iconElement.addClass('bg-success');
                    break;
                case 'đang sử dụng':
                    iconElement.addClass('bg-warning');
                    break;
                case 'bảo trì':
                    iconElement.addClass('bg-danger');
                    break;
                default:
                    iconElement.addClass('bg-secondary');
            }
        }

        function showValidationError(element, message) {
            // Create error tooltip
            element.addClass('is-invalid');

            // Show error message
            if (!element.next('.invalid-feedback').length) {
                element.after('<div class="invalid-feedback">' + message + '</div>');
            }

            // Focus on element
            element.focus();

            // Remove error class on input
            element.on('input change', function() {
                $(this).removeClass('is-invalid');
            });
        }
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