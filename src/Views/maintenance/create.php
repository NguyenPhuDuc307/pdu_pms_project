<?php
// Đảm bảo người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Tạo yêu cầu bảo trì";
$pageSubtitle = "Báo cáo vấn đề về phòng học hoặc thiết bị";
$pageIcon = "fas fa-tools";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Yêu cầu bảo trì', 'link' => '/pdu_pms_project/public/maintenance'],
    ['title' => 'Tạo yêu cầu mới', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card shadow mb-4 rounded">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tạo yêu cầu bảo trì mới</h6>
                </div>
                <div class="card-body">
                    <form action="/pdu_pms_project/public/maintenance/create" method="post">
                        <!-- Chọn phòng -->
                        <div class="mb-3">
                            <label for="room_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                            <select class="form-select" id="room_id" name="room_id" required>
                                <option value="">-- Chọn phòng --</option>
                                <?php if (isset($data['rooms']) && is_array($data['rooms'])): ?>
                                    <?php foreach ($data['rooms'] as $room): ?>
                                        <option value="<?= $room['id'] ?>" <?= (isset($_GET['room_id']) && $_GET['room_id'] == $room['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($room['name']) ?> <?= isset($room['location']) ? '(' . htmlspecialchars($room['location']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Chọn thiết bị -->
                        <div class="mb-3">
                            <label for="equipment_id" class="form-label">Thiết bị</label>
                            <select class="form-select" id="equipment_id" name="equipment_id">
                                <option value="">-- Chọn thiết bị --</option>
                                <?php if (isset($data['equipments']) && is_array($data['equipments'])): ?>
                                    <?php foreach ($data['equipments'] as $equipment): ?>
                                        <option value="<?= $equipment['id'] ?>">
                                            <?= htmlspecialchars($equipment['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Nếu vấn đề không liên quan đến thiết bị cụ thể, hãy để trống.</div>
                        </div>

                        <!-- Mô tả vấn đề -->
                        <div class="mb-3">
                            <label for="issue_description" class="form-label">Mô tả vấn đề <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="issue_description" name="issue_description" rows="4" required></textarea>
                            <div class="form-text">Mô tả chi tiết vấn đề bạn gặp phải.</div>
                        </div>

                        <!-- Mức độ ưu tiên -->
                        <div class="mb-3">
                            <label for="priority" class="form-label">Mức độ ưu tiên <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="thấp">Thấp - Không ảnh hưởng đến hoạt động</option>
                                <option value="trung bình" selected>Trung bình - Ảnh hưởng một phần</option>
                                <option value="cao">Cao - Ảnh hưởng nghiêm trọng</option>
                                <option value="khẩn cấp">Khẩn cấp - Cần xử lý ngay</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/pdu_pms_project/public/maintenance" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Gửi yêu cầu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý khi chọn phòng để lấy danh sách thiết bị
        const roomSelect = document.getElementById('room_id');
        const equipmentSelect = document.getElementById('equipment_id');

        roomSelect.addEventListener('change', function() {
            const roomId = this.value;
            if (!roomId) {
                // Nếu không chọn phòng, xóa danh sách thiết bị
                equipmentSelect.innerHTML = '<option value="">-- Chọn thiết bị --</option>';
                return;
            }

            // Gọi API để lấy danh sách thiết bị của phòng
            fetch(`/pdu_pms_project/public/api/get_room_equipments?room_id=${roomId}`)
                .then(response => response.json())
                .then(data => {
                    // Xóa danh sách thiết bị cũ
                    equipmentSelect.innerHTML = '<option value="">-- Chọn thiết bị --</option>';

                    // Thêm các thiết bị mới
                    if (data.equipments && data.equipments.length > 0) {
                        data.equipments.forEach(equipment => {
                            const option = document.createElement('option');
                            option.value = equipment.equipment_id;
                            option.textContent = equipment.equipment_name;
                            equipmentSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching equipment data:', error);
                });
        });

        // Nếu đã có room_id được chọn (từ URL), kích hoạt sự kiện change để lấy thiết bị
        if (roomSelect.value) {
            const event = new Event('change');
            roomSelect.dispatchEvent(event);
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