<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Sửa đặt phòng";
$pageSubtitle = "Cập nhật thông tin đặt phòng";
$pageIcon = "fas fa-edit";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý đặt phòng', 'link' => '/pdu_pms_project/public/admin/manage_bookings'],
    ['title' => 'Sửa đặt phòng', 'link' => '']
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
            <h4 class="card-title mb-4">Sửa đặt phòng</h4>
            <form method="POST" action="/pdu_pms_project/public/admin/edit_booking?id=<?php echo htmlspecialchars($data['booking']['id']); ?>">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="user_type" class="form-label fw-semibold">Loại người dùng</label>
                        <select name="user_type" id="user_type" class="form-select" required onchange="toggleUserSelect()">
                            <option value="teacher" <?php echo (isset($_POST['user_type']) ? ($_POST['user_type'] === 'teacher') : ($data['booking']['user_role'] === 'teacher' ? true : false)) ? 'selected' : ''; ?>>Giảng viên</option>
                            <option value="student" <?php echo (isset($_POST['user_type']) ? ($_POST['user_type'] === 'student') : ($data['booking']['user_role'] === 'student' ? true : false)) ? 'selected' : ''; ?>>Sinh viên</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="teacher_select" style="display: <?php echo (isset($_POST['user_type']) ? ($_POST['user_type'] === 'student' ? 'none' : 'block') : ($data['booking']['user_role'] === 'teacher' ? 'block' : 'none')); ?>;">
                        <label for="teacher_id" class="form-label fw-semibold">Chọn giảng viên</label>
                        <select name="teacher_id" id="teacher_id" class="form-select" <?php echo (isset($_POST['user_type']) ? ($_POST['user_type'] === 'teacher' ? 'required' : '') : ($data['booking']['user_role'] === 'teacher' ? 'required' : '')); ?>>
                            <option value="">-- Chọn giảng viên --</option>
                            <?php foreach ($data['users'] as $user): ?>
                                <?php if ($user['role'] === 'teacher'): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['teacher_id']) ? ($_POST['teacher_id'] == $user['id'] ? 'selected' : '') : ($user['id'] == $data['booking']['user_id'] && $data['booking']['user_role'] === 'teacher' ? 'selected' : '')); ?>>
                                        <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6" id="student_select" style="display: <?php echo (isset($_POST['user_type']) ? ($_POST['user_type'] === 'student' ? 'block' : 'none') : ($data['booking']['user_role'] === 'student' ? 'block' : 'none')); ?>;">
                        <label for="student_id" class="form-label fw-semibold">Chọn sinh viên</label>
                        <select name="student_id" id="student_id" class="form-select" <?php echo (isset($_POST['user_type']) ? ($_POST['user_type'] === 'student' ? 'required' : '') : ($data['booking']['user_role'] === 'student' ? 'required' : '')); ?>>
                            <option value="">-- Chọn sinh viên --</option>
                            <?php foreach ($data['users'] as $user): ?>
                                <?php if ($user['role'] === 'student'): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['student_id']) ? ($_POST['student_id'] == $user['id'] ? 'selected' : '') : ($user['id'] == $data['booking']['user_id'] && $data['booking']['user_role'] === 'student' ? 'selected' : '')); ?>>
                                        <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="class_code" class="form-label fw-semibold">Mã lớp</label>
                        <input type="text" name="class_code" id="class_code" value="<?php echo htmlspecialchars($_POST['class_code'] ?? $data['booking']['class_code']); ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label for="start_time" class="form-label fw-semibold">Thời gian bắt đầu</label>
                        <input type="datetime-local" name="start_time" id="start_time" value="<?php echo htmlspecialchars($_POST['start_time'] ?? date('Y-m-d\TH:i', strtotime($data['booking']['start_time']))); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="end_time" class="form-label fw-semibold">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_time" id="end_time" value="<?php echo htmlspecialchars($_POST['end_time'] ?? date('Y-m-d\TH:i', strtotime($data['booking']['end_time']))); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="purpose" class="form-label fw-semibold">Mục đích sử dụng</label>
                        <input type="text" name="purpose" id="purpose" value="<?php echo htmlspecialchars($_POST['purpose'] ?? $data['booking']['purpose'] ?? ''); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="chờ duyệt" <?php echo (isset($_POST['status']) ? ($_POST['status'] === 'chờ duyệt') : ($data['booking']['status'] === 'chờ duyệt')) ? 'selected' : ''; ?>>Chờ duyệt</option>
                            <option value="được duyệt" <?php echo (isset($_POST['status']) ? ($_POST['status'] === 'được duyệt') : ($data['booking']['status'] === 'được duyệt')) ? 'selected' : ''; ?>>Được duyệt</option>
                            <option value="bị hủy" <?php echo (isset($_POST['status']) ? ($_POST['status'] === 'bị hủy') : ($data['booking']['status'] === 'bị hủy')) ? 'selected' : ''; ?>>Bị hủy</option>
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
                            <div class="bg-warning-subtle border border-warning rounded me-2" style="width: 16px; height: 16px;"></div>
                            <span>Phòng hiện tại</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-danger-subtle border border-danger rounded me-2" style="width: 16px; height: 16px;"></div>
                            <span>Phòng đã đặt</span>
                        </div>
                    </div>

                    <!-- Grid hiển thị các phòng -->
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                        <?php
                        $currentRoomId = $data['booking']['room_id'] ?? null;
                        $start_time = $_POST['start_time'] ?? $data['booking']['start_time'];
                        $end_time = $_POST['end_time'] ?? $data['booking']['end_time'];

                        foreach ($data['rooms'] as $room):
                            $isCurrentRoom = $room['id'] == $currentRoomId;
                            $isRoomAvailable = true;
                            if (!$isCurrentRoom && $start_time && $end_time) {
                                $isRoomAvailable = in_array($room['id'], $data['available_rooms']);
                            }

                            $roomClass = $isCurrentRoom ? 'bg-warning-subtle border-warning' : ($isRoomAvailable ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger opacity-50');
                            $roomCursor = $isCurrentRoom || $isRoomAvailable ? 'cursor-pointer' : 'disabled';
                        ?>
                            <div class="col">
                                <label class="position-relative w-100 <?php echo $roomCursor; ?>">
                                    <input type="radio" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>"
                                        <?php echo isset($_POST['room_id']) ? ($_POST['room_id'] == $room['id'] ? 'checked' : '') : ($isCurrentRoom ? 'checked' : ''); ?>
                                        <?php echo (!$isRoomAvailable && !$isCurrentRoom) ? 'disabled' : ''; ?>
                                        class="d-none">
                                    <div class="room-card border rounded p-2 text-center h-100 <?php echo $roomClass; ?> <?php echo isset($_POST['room_id']) ? ($_POST['room_id'] == $room['id'] ? 'room-selected' : '') : ($isCurrentRoom ? 'room-selected' : ''); ?>">
                                        <div class="fw-medium"><?php echo htmlspecialchars($room['name']); ?></div>
                                        <div class="small text-secondary">Sức chứa: <?php echo htmlspecialchars($room['capacity']); ?></div>
                                        <?php
                                        $badgeClass = "mt-1 d-inline-block px-2 py-1 small rounded status-badge";
                                        $badgeText = "";
                                        $badgeColorClass = "";

                                        if (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) {
                                            $badgeText = "Đã chọn";
                                            $badgeColorClass = "bg-primary-subtle text-primary";
                                        } elseif ($isCurrentRoom) {
                                            $badgeText = "Hiện tại";
                                            $badgeColorClass = "bg-warning-subtle text-warning";
                                        } elseif (!$isRoomAvailable) {
                                            $badgeText = "Đã đặt";
                                            $badgeColorClass = "bg-danger-subtle text-danger";
                                        } else {
                                            $badgeText = "Trống";
                                            $badgeColorClass = "bg-success-subtle text-success";
                                        }
                                        ?>
                                        <div class="<?php echo $badgeClass . ' ' . $badgeColorClass; ?>"><?php echo $badgeText; ?></div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Nút Submit -->
                <div class="mt-4 d-flex justify-content-end">
                    <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-light me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">Cập nhật đặt phòng</button>
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

            const classCodeInput = document.getElementById('class_code');

            if (userType === 'teacher') {
                teacherSelect.style.display = 'block';
                studentSelect.style.display = 'none';
                document.getElementById('teacher_id').setAttribute('required', 'required');
                document.getElementById('student_id').removeAttribute('required');
                // Bỏ thuộc tính required cho mã lớp khi người dùng là giảng viên
                classCodeInput.removeAttribute('required');
            } else {
                teacherSelect.style.display = 'none';
                studentSelect.style.display = 'block';
                document.getElementById('student_id').setAttribute('required', 'required');
                document.getElementById('teacher_id').removeAttribute('required');
                // Thêm thuộc tính required cho mã lớp khi người dùng là sinh viên
                classCodeInput.setAttribute('required', 'required');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Gọi toggleUserSelect để đảm bảo trạng thái ban đầu của form
            toggleUserSelect();

            document.querySelectorAll('input[name="room_id"]').forEach(function(input) {
                const label = input.closest('label');
                const card = label.querySelector('.room-card');

                if (input.checked) {
                    card.classList.add('room-selected');
                    const statusBadge = card.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = 'Đã chọn';
                        statusBadge.classList.remove('bg-warning-subtle', 'text-warning', 'bg-success-subtle', 'text-success', 'bg-danger-subtle', 'text-danger');
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
                                    if (otherCard.classList.contains('bg-warning-subtle')) {
                                        otherStatusBadge.textContent = 'Hiện tại';
                                        otherStatusBadge.classList.remove('bg-primary-subtle', 'text-primary');
                                        otherStatusBadge.classList.add('bg-warning-subtle', 'text-warning');
                                    } else {
                                        otherStatusBadge.textContent = otherCard.classList.contains('bg-danger-subtle') ? 'Đã đặt' : 'Trống';
                                        otherStatusBadge.classList.remove('bg-primary-subtle', 'text-primary');
                                        otherStatusBadge.classList.add(otherCard.classList.contains('bg-danger-subtle') ? 'bg-danger-subtle' : 'bg-success-subtle', otherCard.classList.contains('bg-danger-subtle') ? 'text-danger' : 'text-success');
                                    }
                                }
                            }
                        });

                        card.classList.add('room-selected');
                        input.checked = true;

                        const statusBadge = card.querySelector('.status-badge');
                        if (statusBadge) {
                            statusBadge.textContent = 'Đã chọn';
                            statusBadge.classList.remove('bg-warning-subtle', 'text-warning', 'bg-success-subtle', 'text-success', 'bg-danger-subtle', 'text-danger');
                            statusBadge.classList.add('bg-primary-subtle', 'text-primary');
                        }

                        // Sử dụng Bootstrap để tạo hiệu ứng
                        $(card).fadeOut(100).fadeIn(100);
                    });
                }
            });

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
                const roomId = document.querySelector('input[name="room_id"]:checked')?.value || '';

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
                    const loadingSpinner = document.createElement('div');
                    loadingSpinner.className = 'text-center my-3';
                    loadingSpinner.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div>';
                    document.querySelector('.card-body').appendChild(loadingSpinner);

                    fetch('/pdu_pms_project/public/admin/edit_booking?id=<?php echo htmlspecialchars($data['booking']['id']); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}&class_code=${encodeURIComponent(classCode)}&user_type=${encodeURIComponent(userType)}&teacher_id=${encodeURIComponent(teacherId)}&student_id=${encodeURIComponent(studentId)}&status=${encodeURIComponent(status)}&room_id=${encodeURIComponent(roomId)}&purpose=${encodeURIComponent(document.getElementById('purpose').value)}`
                        })
                        .then(response => response.text())
                        .then(() => {
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            loadingSpinner.remove();
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