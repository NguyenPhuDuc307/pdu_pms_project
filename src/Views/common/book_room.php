<?php
// Đảm bảo người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Lấy thông tin từ query parameter nếu có
$preselected_room_id = $_GET['room_id'] ?? null;
$preselected_start_time = $_GET['start_time'] ?? '';
$preselected_end_time = $_GET['end_time'] ?? '';

// Thiết lập thông tin cho page_header dựa trên vai trò
$role = $_SESSION['role'];
$pageTitle = "Đặt phòng";
$pageSubtitle = "Chọn thời gian và xem các phòng còn trống để đặt";
$pageIcon = "fas fa-calendar-plus";

// Thiết lập breadcrumbs dựa trên vai trò
switch ($role) {
    case 'admin':
        $breadcrumbs = [
            ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
            ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
            ['title' => 'Quản lý đặt phòng', 'link' => '/pdu_pms_project/public/admin/manage_bookings'],
            ['title' => 'Đặt phòng', 'link' => '']
        ];
        break;
    case 'teacher':
        $breadcrumbs = [
            ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/teacher'],
            ['title' => 'Tìm kiếm phòng', 'link' => '/pdu_pms_project/public/teacher/search_rooms'],
            ['title' => 'Đặt phòng', 'link' => '']
        ];
        break;
    case 'student':
        $breadcrumbs = [
            ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/student'],
            ['title' => 'Tìm kiếm phòng', 'link' => '/pdu_pms_project/public/student/search_rooms'],
            ['title' => 'Đặt phòng', 'link' => '']
        ];
        break;
}

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-body p-4">
            <h4 class="card-title mb-4">Thông tin đặt phòng</h4>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-danger border-start border-danger border-4 mb-4" role="alert">
                    <?php echo htmlspecialchars($data['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo getFormAction(); ?>" id="bookingForm">
                <div class="row g-3 mb-4">
                    <?php if ($role === 'admin'): ?>
                        <!-- Admin có thể chọn người dùng -->
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
                    <?php endif; ?>

                    <!-- Phòng đã chọn (nếu có) -->
                    <?php if (isset($data['room'])): ?>
                        <input type="hidden" name="room_id" value="<?php echo $data['room']['id']; ?>">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Phòng đã chọn</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                            <i class="fas fa-door-open fa-lg text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($data['room']['name']); ?></h6>
                                            <p class="text-muted mb-0">
                                                <small>
                                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($data['room']['location']); ?> |
                                                    <i class="fas fa-users me-1"></i><?php echo htmlspecialchars($data['room']['capacity']); ?> người
                                                    <?php if (isset($data['room']['room_type_name'])): ?>
                                                        | <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($data['room']['room_type_name']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-6">
                        <label for="class_code" class="form-label fw-semibold">Mã lớp</label>
                        <input type="text" name="class_code" id="class_code" value="<?php echo htmlspecialchars($_POST['class_code'] ?? ''); ?>" class="form-control" required>
                        <div class="form-text">
                            Nhập mã lớp học
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="start_time" class="form-label fw-semibold">Thời gian bắt đầu</label>
                        <input type="datetime-local" name="start_time" id="start_time" value="<?php echo htmlspecialchars($_POST['start_time'] ?? $preselected_start_time); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="end_time" class="form-label fw-semibold">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_time" id="end_time" value="<?php echo htmlspecialchars($_POST['end_time'] ?? $preselected_end_time); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="purpose" class="form-label fw-semibold">Mục đích sử dụng</label>
                        <input type="text" name="purpose" id="purpose" value="<?php echo htmlspecialchars($_POST['purpose'] ?? ''); ?>" class="form-control" required>
                    </div>

                    <?php if ($role === 'admin'): ?>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Trạng thái</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="chờ duyệt" <?php echo (isset($_POST['status']) && $_POST['status'] === 'chờ duyệt') ? 'selected' : ''; ?>>Chờ duyệt</option>
                                <option value="được duyệt" <?php echo (isset($_POST['status']) && $_POST['status'] === 'được duyệt') ? 'selected' : ''; ?>>Được duyệt</option>
                                <option value="bị hủy" <?php echo (isset($_POST['status']) && $_POST['status'] === 'bị hủy') ? 'selected' : ''; ?>>Bị hủy</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Phần chọn phòng trực quan (nếu chưa chọn phòng) -->
                <?php if (!isset($data['room']) && !$preselected_room_id): ?>
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
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3" id="roomsContainer">
                            <?php
                            $start_time = $_POST['start_time'] ?? null;
                            $end_time = $_POST['end_time'] ?? null;

                            if (isset($data['rooms']) && is_array($data['rooms'])):
                                foreach ($data['rooms'] as $room):
                                    $isRoomAvailable = true;
                                    if ($start_time && $end_time) {
                                        $isRoomAvailable = in_array($room['id'], $data['available_rooms'] ?? []);
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
                                                <div class="small text-secondary">Số máy: <?php echo htmlspecialchars($room['capacity']); ?></div>
                                                <div class="mt-1 d-inline-block px-2 py-1 small rounded status-badge <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'bg-primary-subtle text-primary' : ($isRoomAvailable ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'); ?>">
                                                    <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'Đã chọn' : ($isRoomAvailable ? 'Trống' : 'Đã đặt'); ?>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <h5>Chọn thời gian để xem phòng trống</h5>
                                        <p>Vui lòng chọn thời gian bắt đầu và kết thúc để hệ thống hiển thị các phòng còn trống</p>
                                        <button type="button" id="checkAvailability" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>Kiểm tra phòng trống
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Nút Submit -->
                <div class="mt-4 d-flex justify-content-end">
                    <a href="<?php echo getCancelUrl(); ?>" class="btn btn-light me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check me-2"></i>Đặt phòng
                    </button>
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

            if (userType === 'teacher') {
                teacherSelect.style.display = 'block';
                studentSelect.style.display = 'none';
                document.getElementById('teacher_id').setAttribute('required', 'required');
                document.getElementById('student_id').removeAttribute('required');
            } else {
                teacherSelect.style.display = 'none';
                studentSelect.style.display = 'block';
                document.getElementById('student_id').setAttribute('required', 'required');
                document.getElementById('teacher_id').removeAttribute('required');
            }
            // Mã lớp luôn là bắt buộc cho tất cả các vai trò
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Gọi toggleUserSelect để đảm bảo trạng thái ban đầu của form (nếu là admin)
            if (document.getElementById('user_type')) {
                toggleUserSelect();
            }

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
                        });
                    }
                });
            }

            // Hàm kiểm tra phòng trống
            function checkAvailableRooms() {
                const startTimeInput = document.getElementById('start_time');
                const endTimeInput = document.getElementById('end_time');

                if (!startTimeInput.value || !endTimeInput.value) {
                    return; // Không làm gì nếu chưa có thời gian
                }

                const startTime = new Date(startTimeInput.value);
                const endTime = new Date(endTimeInput.value);

                if (startTime >= endTime) {
                    alert('Thời gian kết thúc phải sau thời gian bắt đầu');
                    return;
                }

                // Hiển thị loading
                const roomsContainer = document.getElementById('roomsContainer');
                if (!roomsContainer) return;

                roomsContainer.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div><p class="mt-3">Đang kiểm tra phòng trống...</p></div>';

                // Gửi request để lấy danh sách phòng trống
                const formData = new FormData();
                formData.append('start_time', startTimeInput.value);
                formData.append('end_time', endTimeInput.value);

                // Thêm các trường khác nếu cần
                const userTypeInput = document.getElementById('user_type');
                if (userTypeInput) {
                    formData.append('user_type', userTypeInput.value);

                    if (userTypeInput.value === 'teacher') {
                        const teacherId = document.getElementById('teacher_id').value;
                        if (teacherId) formData.append('teacher_id', teacherId);
                    } else {
                        const studentId = document.getElementById('student_id').value;
                        if (studentId) formData.append('student_id', studentId);
                    }
                }

                const classCode = document.getElementById('class_code').value;
                if (classCode) formData.append('class_code', classCode);

                const purpose = document.getElementById('purpose').value;
                if (purpose) formData.append('purpose', purpose);

                // Gửi request
                fetch(window.location.href, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Tạo một DOM parser để phân tích HTML trả về
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Lấy danh sách phòng từ HTML trả về
                        const newRoomsContainer = doc.querySelector('#roomsContainer');

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
                    });
            }

            // Khởi tạo sự kiện cho các phòng khi trang tải lần đầu
            initRoomEvents();

            // Thêm sự kiện lắng nghe thay đổi cho các trường thời gian
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');

            // Biến để theo dõi thời gian chờ giữa các lần gọi API
            let timeoutId = null;

            // Hàm xử lý sự kiện thay đổi thời gian
            function handleTimeChange() {
                // Hủy bỏ timeout trước đó nếu có
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }

                // Đặt timeout mới để tránh gọi API quá nhiều lần
                timeoutId = setTimeout(() => {
                    checkAvailableRooms();
                }, 500); // Chờ 500ms sau khi người dùng dừng nhập
            }

            // Thêm sự kiện lắng nghe cho các trường thời gian
            if (startTimeInput) {
                startTimeInput.addEventListener('change', handleTimeChange);
            }

            if (endTimeInput) {
                endTimeInput.addEventListener('change', handleTimeChange);
            }

            // Xử lý nút kiểm tra phòng trống
            const checkAvailabilityBtn = document.getElementById('checkAvailability');
            if (checkAvailabilityBtn) {
                checkAvailabilityBtn.addEventListener('click', function() {
                    const startTimeInput = document.getElementById('start_time');
                    const endTimeInput = document.getElementById('end_time');

                    if (!startTimeInput.value || !endTimeInput.value) {
                        alert('Vui lòng chọn thời gian bắt đầu và kết thúc');
                        return;
                    }

                    checkAvailableRooms();
                });
            }

            // Kiểm tra form trước khi submit
            document.querySelector('form').addEventListener('submit', function(event) {
                const startTime = document.getElementById('start_time').value;
                const endTime = document.getElementById('end_time').value;
                const purpose = document.getElementById('purpose').value;
                const roomSelected = document.querySelector('input[name="room_id"]:checked');

                let isValid = true;
                let errorMessage = '';

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
                }

                // Kiểm tra thêm cho admin
                if (document.getElementById('user_type')) {
                    const userType = document.getElementById('user_type').value;
                    if (userType === 'teacher' && !document.getElementById('teacher_id').value) {
                        isValid = false;
                        errorMessage = 'Vui lòng chọn giảng viên';
                    } else if (userType === 'student' && !document.getElementById('student_id').value) {
                        isValid = false;
                        errorMessage = 'Vui lòng chọn sinh viên';
                    }
                }

                // Kiểm tra mã lớp (luôn bắt buộc)
                if (!document.getElementById('class_code').value) {
                    isValid = false;
                    errorMessage = 'Vui lòng nhập mã lớp';
                }

                if (!isValid) {
                    event.preventDefault();
                    alert(errorMessage);
                }
            });
        });
    </script>
</div>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = $_SESSION['role'];

// Include the main layout
include __DIR__ . '/../layouts/main_layout.php';

/**
 * Hàm trả về URL hành động của form dựa trên vai trò người dùng
 */
function getFormAction()
{
    $role = $_SESSION['role'];
    switch ($role) {
        case 'admin':
            return '/pdu_pms_project/public/admin/add_booking';
        case 'teacher':
            return '/pdu_pms_project/public/teacher/book_room';
        case 'student':
            return '/pdu_pms_project/public/student/book_room';
        default:
            return '/pdu_pms_project/public/book_room';
    }
}

/**
 * Hàm trả về URL hủy bỏ dựa trên vai trò người dùng
 */
function getCancelUrl()
{
    $role = $_SESSION['role'];
    switch ($role) {
        case 'admin':
            return '/pdu_pms_project/public/admin/manage_bookings';
        case 'teacher':
            return '/pdu_pms_project/public/teacher/search_rooms';
        case 'student':
            return '/pdu_pms_project/public/student/search_rooms';
        default:
            return '/pdu_pms_project/public/';
    }
}
?>