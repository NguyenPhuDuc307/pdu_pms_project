<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Thêm lịch dạy mới";
$pageSubtitle = "Tạo lịch dạy mới cho giáo viên";
$pageIcon = "fas fa-calendar-plus";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý lịch dạy', 'link' => '/pdu_pms_project/public/admin/manage_timetable'],
    ['title' => 'Thêm lịch dạy', 'link' => '']
];

// Lấy danh sách giáo viên và phòng học
$teachers = $data['users'] ?? [];
$teachers = array_filter($teachers, function ($user) {
    return $user['role'] === 'teacher';
});

// Lấy danh sách phòng học
$rooms = $data['rooms'] ?? [];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-calendar-plus me-2"></i>Thêm lịch dạy mới</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($data['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($data['error']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/pdu_pms_project/public/admin/add_timetable" class="needs-validation" novalidate>
                        <!-- Giáo viên -->
                        <div class="mb-4">
                            <label for="teacher_id" class="form-label fw-bold"><i class="fas fa-user-tie me-2 text-primary"></i>Giáo viên phụ trách</label>
                            <select id="teacher_id" name="teacher_id" class="form-select" required>
                                <option value="">-- Chọn giáo viên --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['full_name'] . ' (' . $teacher['username'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn giáo viên phụ trách</div>
                        </div>

                        <!-- Thông tin lớp học -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="class_code" class="form-label fw-bold"><i class="fas fa-users me-2 text-primary"></i>Mã lớp</label>
                                <input type="text" id="class_code" name="class_code" class="form-control" placeholder="Ví dụ: CS101" required>
                                <div class="invalid-feedback">Vui lòng nhập mã lớp</div>
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label fw-bold"><i class="fas fa-book me-2 text-primary"></i>Môn học</label>
                                <input type="text" id="subject" name="subject" class="form-control" placeholder="Ví dụ: Lập trình cơ bản" required>
                                <div class="invalid-feedback">Vui lòng nhập tên môn học</div>
                            </div>
                        </div>

                        <!-- Thời gian -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-bold"><i class="fas fa-clock me-2 text-primary"></i>Thời gian bắt đầu</label>
                                <input type="datetime-local" id="start_time" name="start_time" class="form-control" required>
                                <div class="invalid-feedback">Vui lòng chọn thời gian bắt đầu</div>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label fw-bold"><i class="fas fa-clock me-2 text-primary"></i>Thời gian kết thúc</label>
                                <input type="datetime-local" id="end_time" name="end_time" class="form-control" required>
                                <div class="invalid-feedback">Vui lòng chọn thời gian kết thúc</div>
                                <div class="form-text text-muted">Thời gian kết thúc phải sau thời gian bắt đầu</div>
                            </div>
                        </div>

                        <!-- Phòng học (tùy chọn) -->
                        <div class="mb-4">
                            <label for="room_id" class="form-label fw-bold"><i class="fas fa-door-open me-2 text-primary"></i>Phòng học (tùy chọn)</label>
                            <select id="room_id" name="room_id" class="form-select">
                                <option value="">-- Chọn phòng học (nếu cần) --</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['name'] . ' (Sức chứa: ' . $room['capacity'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted">Bạn có thể chọn phòng ngay bây giờ hoặc để sau</div>
                        </div>

                        <!-- Số lượng học viên -->
                        <div class="mb-4">
                            <label for="participants" class="form-label fw-bold"><i class="fas fa-user-friends me-2 text-primary"></i>Số lượng học viên</label>
                            <input type="number" id="participants" name="participants" class="form-control" min="1" value="1">
                            <div class="form-text text-muted">Số lượng học viên dự kiến tham gia lớp học</div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold"><i class="fas fa-sticky-note me-2 text-primary"></i>Ghi chú</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Thông tin bổ sung về lớp học (nếu có)"></textarea>
                        </div>

                        <!-- Nút submit -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="add_timetable" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-save me-2"></i>Lưu lịch dạy
                            </button>
                            <a href="/pdu_pms_project/public/admin/manage_timetable" class="btn btn-secondary flex-grow-1">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Thiết lập giá trị mặc định cho thời gian bắt đầu và kết thúc
        const now = new Date();
        const startTime = new Date(now);
        startTime.setHours(startTime.getHours() + 1);
        startTime.setMinutes(0);
        startTime.setSeconds(0);

        const endTime = new Date(startTime);
        endTime.setHours(endTime.getHours() + 2);

        // Format datetime-local string: YYYY-MM-DDThh:mm
        document.getElementById('start_time').value = startTime.toISOString().slice(0, 16);
        document.getElementById('end_time').value = endTime.toISOString().slice(0, 16);

        // Kiểm tra sức chứa phòng khi chọn phòng
        const roomSelect = document.getElementById('room_id');
        const participantsInput = document.getElementById('participants');

        roomSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const roomCapacity = parseInt(selectedOption.text.match(/Sức chứa: (\d+)/)[1]);

                if (parseInt(participantsInput.value) > roomCapacity) {
                    alert(`Số lượng học viên (${participantsInput.value}) vượt quá sức chứa của phòng (${roomCapacity})`);
                }
            }
        });

        // Kiểm tra form validation
        const form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Kiểm tra thời gian kết thúc phải sau thời gian bắt đầu
            const startTimeValue = new Date(document.getElementById('start_time').value);
            const endTimeValue = new Date(document.getElementById('end_time').value);

            if (endTimeValue <= startTimeValue) {
                event.preventDefault();
                alert('Thời gian kết thúc phải sau thời gian bắt đầu');
                document.getElementById('end_time').classList.add('is-invalid');
            }

            // Kiểm tra sức chứa phòng khi submit
            const roomId = roomSelect.value;
            if (roomId) {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];
                const roomCapacity = parseInt(selectedOption.text.match(/Sức chứa: (\d+)/)[1]);

                if (parseInt(participantsInput.value) > roomCapacity) {
                    event.preventDefault();
                    alert(`Số lượng học viên (${participantsInput.value}) vượt quá sức chứa của phòng (${roomCapacity})`);
                }
            }

            form.classList.add('was-validated');
        });
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