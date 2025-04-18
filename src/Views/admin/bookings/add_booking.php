<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Thêm đặt phòng";
$pageSubtitle = "Tạo yêu cầu đặt phòng mới cho giảng viên hoặc sinh viên";
$pageIcon = "fas fa-calendar-plus";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý đặt phòng', 'link' => '/pdu_pms_project/public/admin/manage_bookings'],
    ['title' => 'Thêm đặt phòng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="text-end mb-3">
        <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success border-start border-success border-4 mb-4" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php elseif (isset($data['error'])): ?>
        <div class="alert alert-danger border-start border-danger border-4 mb-4" role="alert">
            <?php echo htmlspecialchars($data['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body p-4">
            <h4 class="card-title mb-4">Thêm đặt phòng</h4>
            <form method="POST" action="/pdu_pms_project/public/admin/add_booking">
                <input type="hidden" name="user_id" id="user_id" value="">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="user_type" class="form-label fw-semibold">Loại người dùng</label>
                        <select name="user_type" id="user_type" class="form-select" required onchange="toggleUserSelect()">
                            <option value="teacher" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'teacher') ? 'selected' : ''; ?>>Giảng viên</option>
                            <option value="student" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'student') ? 'selected' : ''; ?>>Sinh viên</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="teacher_select" style="display: <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'student') ? 'none' : 'block'; ?>;">
                        <label for="teacher_id" class="form-label fw-semibold">Chọn giảng viên</label>
                        <select name="teacher_id" id="teacher_id" class="form-select">
                            <option value="">-- Chọn giảng viên --</option>
                            <?php foreach ($data['users'] as $user): ?>
                                <?php if ($user['role'] === 'teacher'): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['teacher_id']) && $_POST['teacher_id'] == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6" id="student_select" style="display: <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'student') ? 'block' : 'none'; ?>;">
                        <label for="student_id" class="form-label fw-semibold">Chọn sinh viên</label>
                        <select name="student_id" id="student_id" class="form-select">
                            <option value="">-- Chọn sinh viên --</option>
                            <?php foreach ($data['users'] as $user): ?>
                                <?php if ($user['role'] === 'student'): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['student_id']) && $_POST['student_id'] == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="class_code" class="form-label fw-semibold">Mã lớp</label>
                        <input type="text" name="class_code" id="class_code" value="<?php echo htmlspecialchars($_POST['class_code'] ?? ''); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="start_time" class="form-label fw-semibold">Thời gian bắt đầu</label>
                        <input type="datetime-local" name="start_time" id="start_time" value="<?php echo htmlspecialchars($_POST['start_time'] ?? ''); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="end_time" class="form-label fw-semibold">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_time" id="end_time" value="<?php echo htmlspecialchars($_POST['end_time'] ?? ''); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="purpose" class="form-label fw-semibold">Mục đích sử dụng</label>
                        <input type="text" name="purpose" id="purpose" value="<?php echo htmlspecialchars($_POST['purpose'] ?? ''); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="chờ duyệt" <?php echo (isset($_POST['status']) && $_POST['status'] === 'chờ duyệt') ? 'selected' : ''; ?>>Chờ duyệt</option>
                            <option value="được duyệt" <?php echo (isset($_POST['status']) && $_POST['status'] === 'được duyệt') ? 'selected' : ''; ?>>Được duyệt</option>
                            <option value="bị hủy" <?php echo (isset($_POST['status']) && $_POST['status'] === 'bị hủy') ? 'selected' : ''; ?>>Bị hủy</option>
                        </select>
                    </div>
                </div>

                <!-- Phần chọn phòng trực quan -->
                <div class="mt-4 mb-4">
                    <h5 class="mb-3">Chọn phòng học</h5>

                    <!-- Giải thích màu sắc -->
                    <div class="d-flex gap-4 mb-3 small">
                        <div class="d-flex align-items-center">
                            <div class="bg-success-subtle border border-success rounded me-2" style="width: 16px; height: 16px;"></div>
                            <span>Phòng trống</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-danger-subtle border border-danger rounded me-2" style="width: 16px; height: 16px;"></div>
                            <span>Phòng đã đặt</span>
                        </div>
                    </div>

                    <!-- Grid hiển thị các phòng -->
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                        <?php
                        $start_time = $_POST['start_time'] ?? null;
                        $end_time = $_POST['end_time'] ?? null;

                        foreach ($data['rooms'] as $room):
                            $isRoomAvailable = true;
                            if ($start_time && $end_time) {
                                // Kiểm tra xem phòng có trong danh sách phòng trống không
                                $isRoomAvailable = in_array($room['id'], $data['available_rooms']);
                                // Thêm log để debug
                                error_log("Phòng {$room['id']} ({$room['name']}) " . ($isRoomAvailable ? "TRỐNG" : "ĐÃ ĐẶT"));
                            }

                            $roomClass = $isRoomAvailable ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger opacity-50';
                            $roomCursor = $isRoomAvailable ? 'cursor-pointer' : 'disabled';
                        ?>
                            <div class="col">
                                <label class="position-relative w-100 <?php echo $roomCursor; ?>">
                                    <input type="radio" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>"
                                        <?php echo (!$isRoomAvailable) ? 'disabled' : ''; ?>
                                        <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'checked' : ''; ?>
                                        class="d-none">
                                    <div class="room-card border rounded p-2 text-center h-100 <?php echo $roomClass; ?> <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'room-selected' : ''; ?>">
                                        <div class="fw-medium"><?php echo htmlspecialchars($room['name']); ?></div>
                                        <div class="small text-secondary">Sức chứa: <?php echo htmlspecialchars($room['capacity']); ?></div>
                                        <div class="mt-1 d-inline-block px-2 py-1 small rounded status-badge <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'bg-primary-subtle text-primary' : ($isRoomAvailable ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'); ?>">
                                            <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'Đã chọn' : ($isRoomAvailable ? 'Trống' : 'Đã đặt'); ?>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Nút Submit -->
                <div class="mt-4 d-flex justify-content-end">
                    <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-light me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">Thêm đặt phòng</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .room-selected {
            box-shadow: 0 0 0 3px var(--bs-primary) !important;
            transform: scale(1.05);
            z-index: 10;
            position: relative;
        }

        .room-card:not(.disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .room-card:not(.disabled):active {
            transform: scale(0.98);
        }

        .room-card {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .disabled {
            cursor: not-allowed;
        }
    </style>

    <script>
        function toggleUserSelect() {
            const userType = document.getElementById('user_type').value;
            const teacherSelect = document.getElementById('teacher_select');
            const studentSelect = document.getElementById('student_select');
            const userIdInput = document.getElementById('user_id');
            const classCodeInput = document.getElementById('class_code');

            if (userType === 'teacher') {
                teacherSelect.style.display = 'block';
                studentSelect.style.display = 'none';
                document.getElementById('teacher_id').setAttribute('required', 'required');
                document.getElementById('student_id').removeAttribute('required');
                // Bỏ thuộc tính required cho mã lớp khi người dùng là giảng viên
                classCodeInput.removeAttribute('required');
                // Cập nhật user_id khi thay đổi teacher_id
                userIdInput.value = document.getElementById('teacher_id').value;
            } else {
                teacherSelect.style.display = 'none';
                studentSelect.style.display = 'block';
                document.getElementById('student_id').setAttribute('required', 'required');
                document.getElementById('teacher_id').removeAttribute('required');
                // Thêm thuộc tính required cho mã lớp khi người dùng là sinh viên
                classCodeInput.setAttribute('required', 'required');
                // Cập nhật user_id khi thay đổi student_id
                userIdInput.value = document.getElementById('student_id').value;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Gọi toggleUserSelect để đảm bảo trạng thái ban đầu của form
            toggleUserSelect();

            // Tạo hàm khởi tạo sự kiện cho các phòng
            function initRoomEvents() {
                document.querySelectorAll('input[name="room_id"]').forEach(function(input) {
                    const label = input.closest('label');
                    const card = label.querySelector('.room-card');

                    if (input.checked) {
                        card.classList.add('room-selected');
                        const statusBadge = card.querySelector('.status-badge');
                        if (statusBadge) {
                            statusBadge.textContent = 'Đã chọn';
                            statusBadge.classList.remove('bg-success-subtle', 'text-success', 'bg-danger-subtle', 'text-danger');
                            statusBadge.classList.add('bg-primary-subtle', 'text-primary');
                        }
                    }

                    if (!input.disabled) {
                        label.addEventListener('click', function() {
                            document.querySelectorAll('.room-card').forEach(function(otherCard) {
                                otherCard.classList.remove('room-selected');
                                const otherStatusBadge = otherCard.querySelector('.status-badge');
                                if (otherStatusBadge && otherStatusBadge.textContent === 'Đã chọn') {
                                    const otherInput = otherCard.closest('label').querySelector('input');
                                    if (otherInput && otherInput.value != input.value) {
                                        otherStatusBadge.textContent = otherCard.classList.contains('bg-danger-subtle') ? 'Đã đặt' : 'Trống';
                                        otherStatusBadge.classList.remove('bg-primary-subtle', 'text-primary');
                                        otherStatusBadge.classList.add(otherCard.classList.contains('bg-danger-subtle') ? 'bg-danger-subtle' : 'bg-success-subtle', otherCard.classList.contains('bg-danger-subtle') ? 'text-danger' : 'text-success');
                                    }
                                }
                            });

                            card.classList.add('room-selected');
                            input.checked = true;

                            const statusBadge = card.querySelector('.status-badge');
                            if (statusBadge) {
                                statusBadge.textContent = 'Đã chọn';
                                statusBadge.classList.remove('bg-success-subtle', 'text-success', 'bg-danger-subtle', 'text-danger');
                                statusBadge.classList.add('bg-primary-subtle', 'text-primary');
                            }

                            // Sử dụng Bootstrap để tạo hiệu ứng
                            $(card).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                        });
                    }
                });
            }

            // Khởi tạo sự kiện cho các phòng khi trang tải lần đầu
            initRoomEvents();

            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const classCodeInput = document.getElementById('class_code');
            const userTypeInput = document.getElementById('user_type');
            const teacherIdInput = document.getElementById('teacher_id');
            const studentIdInput = document.getElementById('student_id');
            const statusInput = document.getElementById('status');

            const updateRoomAvailability = function() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;
                const classCode = classCodeInput.value;
                const userType = userTypeInput.value;
                const teacherId = teacherIdInput.value;
                const studentId = studentIdInput.value;
                const status = statusInput.value;
                // Sử dụng user_id từ teacher_id hoặc student_id
                const userId = userType === 'teacher' ? teacherId : studentId;

                // Chỉ gọi fetch nếu cả start_time và end_time đều có giá trị
                if (startTime && endTime) {
                    // Kiểm tra xem start_time có nhỏ hơn end_time không
                    const startDate = new Date(startTime);
                    const endDate = new Date(endTime);
                    if (startDate >= endDate) {
                        // Sử dụng Bootstrap alert thay vì alert thông thường
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        const alertContainer = document.createElement('div');
                        alertContainer.innerHTML = alertHtml;
                        document.querySelector('.card-body').prepend(alertContainer.firstChild);
                        return;
                    }

                    // Hiển thị spinner khi đang tải
                    const roomsContainer = document.querySelector('.row.row-cols-2.row-cols-sm-3.row-cols-md-4.row-cols-lg-6.g-3');
                    roomsContainer.innerHTML = '<div class="text-center my-3 w-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></div>';

                    // Log dữ liệu được gửi đi để debug
                    console.log('Gửi dữ liệu:', {
                        start_time: startTime,
                        end_time: endTime,
                        class_code: classCode
                    });

                    // Cập nhật user_id trong form
                    document.getElementById('user_id').value = userId;

                    fetch('/pdu_pms_project/public/admin/add_booking', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: `start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}&class_code=${encodeURIComponent(classCode)}`
                        })
                        .then(response => response.text())
                        .then(html => {
                            // Tạo một DOM parser để phân tích HTML trả về
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            // Lấy danh sách phòng từ HTML trả về
                            const newRoomsContainer = doc.querySelector('.row.row-cols-2.row-cols-sm-3.row-cols-md-4.row-cols-lg-6.g-3');

                            if (newRoomsContainer) {
                                // Cập nhật danh sách phòng
                                roomsContainer.innerHTML = newRoomsContainer.innerHTML;

                                // Khởi tạo lại các sự kiện cho các phòng
                                initRoomEvents();
                            } else {
                                // Nếu không tìm thấy danh sách phòng, hiển thị thông báo lỗi
                                roomsContainer.innerHTML = '<div class="col-12 text-center"><div class="alert alert-danger">Không thể cập nhật danh sách phòng. Vui lòng thử lại.</div></div>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            roomsContainer.innerHTML = '<div class="col-12 text-center"><div class="alert alert-danger">Đã có lỗi xảy ra khi cập nhật danh sách phòng.</div></div>';

                            const alertHtml = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Đã có lỗi xảy ra khi cập nhật danh sách phòng.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                            const alertContainer = document.createElement('div');
                            alertContainer.innerHTML = alertHtml;
                            document.querySelector('.card-body').prepend(alertContainer.firstChild);
                        });
                } else {
                    // Thông báo nếu người dùng chưa chọn đủ cả hai trường
                    let message = '';
                    if (startTime && !endTime) {
                        message = 'Vui lòng chọn thời gian kết thúc để cập nhật danh sách phòng.';
                    } else if (!startTime && endTime) {
                        message = 'Vui lòng chọn thời gian bắt đầu để cập nhật danh sách phòng.';
                    }

                    if (message) {
                        const alertHtml = `
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        const alertContainer = document.createElement('div');
                        alertContainer.innerHTML = alertHtml;
                        document.querySelector('.card-body').prepend(alertContainer.firstChild);
                    }
                }
            };

            startTimeInput.addEventListener('change', updateRoomAvailability);
            endTimeInput.addEventListener('change', updateRoomAvailability);
            teacherIdInput.addEventListener('change', function() {
                document.getElementById('user_id').value = this.value;
                updateRoomAvailability();
            });
            studentIdInput.addEventListener('change', function() {
                document.getElementById('user_id').value = this.value;
                updateRoomAvailability();
            });

            // Kiểm tra form trước khi submit
            document.querySelector('form').addEventListener('submit', function(event) {
                const userType = document.getElementById('user_type').value;
                const teacherId = document.getElementById('teacher_id').value;
                const studentId = document.getElementById('student_id').value;
                // Sử dụng user_id từ teacher_id hoặc student_id
                const userId = userType === 'teacher' ? teacherId : studentId;
                const classCode = document.getElementById('class_code').value;
                const startTime = document.getElementById('start_time').value;
                const endTime = document.getElementById('end_time').value;
                const purpose = document.getElementById('purpose').value;
                const roomSelected = document.querySelector('input[name="room_id"]:checked');

                let isValid = true;
                let errorMessage = '';

                // Xóa thông báo lỗi cũ
                const oldAlerts = document.querySelectorAll('.alert');
                oldAlerts.forEach(alert => alert.remove());

                // Kiểm tra các trường bắt buộc
                if (!startTime) {
                    isValid = false;
                    errorMessage = 'Vui lòng chọn thời gian bắt đầu';
                } else if (!endTime) {
                    isValid = false;
                    errorMessage = 'Vui lòng chọn thời gian kết thúc';
                } else if (!purpose) {
                    isValid = false;
                    errorMessage = 'Vui lòng nhập mục đích sử dụng';
                } else if (!roomSelected) {
                    isValid = false;
                    errorMessage = 'Vui lòng chọn phòng';
                } else if (userType === 'teacher' && !teacherId) {
                    isValid = false;
                    errorMessage = 'Vui lòng chọn giảng viên';
                } else if (userType === 'student' && !studentId) {
                    isValid = false;
                    errorMessage = 'Vui lòng chọn sinh viên';
                } else if (!classCode) {
                    isValid = false;
                    errorMessage = 'Vui lòng nhập mã lớp';
                }

                if (!isValid) {
                    event.preventDefault();
                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${errorMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    const alertContainer = document.createElement('div');
                    alertContainer.innerHTML = alertHtml;
                    document.querySelector('.card-body').prepend(alertContainer.firstChild);
                }
            });
        });
    </script>
</div>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>