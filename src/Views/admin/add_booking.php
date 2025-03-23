<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../Helpers/BreadcrumbHelper.php'; ?>

<div class="p-6 bg-gray-50">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center bg-white p-4 rounded-lg shadow-sm">
        <?php echo BreadcrumbHelper::render(); ?>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_GET['message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo htmlspecialchars($_GET['message']); ?></p>
        </div>
    <?php elseif (isset($data['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?php echo htmlspecialchars($data['error']); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Thêm đặt phòng</h2>
        <form method="POST" action="/pdu_pms_project/public/admin/add_booking" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">Loại người dùng</label>
                    <select name="user_type" id="user_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required onchange="toggleUserSelect()">
                        <option value="teacher" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'teacher') ? 'selected' : ''; ?>>Giảng viên</option>
                        <option value="student" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'student') ? 'selected' : ''; ?>>Sinh viên</option>
                    </select>
                </div>

                <div id="teacher_select" style="display: <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'student') ? 'none' : 'block'; ?>;">
                    <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">Chọn giảng viên</label>
                    <select name="teacher_id" id="teacher_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Chọn giảng viên --</option>
                        <?php foreach ($data['users'] as $user): ?>
                            <?php if ($user['role'] === 'teacher'): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['teacher_id']) && $_POST['teacher_id'] == $user['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="student_select" style="display: <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'student') ? 'block' : 'none'; ?>;">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Chọn sinh viên</label>
                    <select name="student_id" id="student_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Chọn sinh viên --</option>
                        <?php foreach ($data['users'] as $user): ?>
                            <?php if ($user['role'] === 'student'): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['student_id']) && $_POST['student_id'] == $user['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="class_code" class="block text-sm font-medium text-gray-700 mb-1">Mã lớp</label>
                    <input type="text" name="class_code" id="class_code" value="<?php echo htmlspecialchars($_POST['class_code'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Thời gian bắt đầu</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="<?php echo htmlspecialchars($_POST['start_time'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Thời gian kết thúc</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="<?php echo htmlspecialchars($_POST['end_time'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="chờ duyệt" <?php echo (isset($_POST['status']) && $_POST['status'] === 'chờ duyệt') ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="được duyệt" <?php echo (isset($_POST['status']) && $_POST['status'] === 'được duyệt') ? 'selected' : ''; ?>>Được duyệt</option>
                        <option value="bị hủy" <?php echo (isset($_POST['status']) && $_POST['status'] === 'bị hủy') ? 'selected' : ''; ?>>Bị hủy</option>
                    </select>
                </div>
            </div>

            <!-- Phần chọn phòng trực quan -->
            <div class="mt-8 mb-4">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Chọn phòng học</h3>

                <!-- Giải thích màu sắc -->
                <div class="flex items-center space-x-6 mb-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                        <span>Phòng trống</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-100 border border-red-300 rounded mr-2"></div>
                        <span>Phòng đã đặt</span>
                    </div>
                </div>

                <!-- Grid hiển thị các phòng -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <?php
                    $start_time = $_POST['start_time'] ?? null;
                    $end_time = $_POST['end_time'] ?? null;

                    foreach ($data['rooms'] as $room):
                        $isRoomAvailable = true;
                        if ($start_time && $end_time) {
                            $isRoomAvailable = in_array($room['id'], $data['available_rooms']);
                        }

                        $roomClass = $isRoomAvailable ? 'bg-green-100 border-green-300 hover:bg-green-200 cursor-pointer' : 'bg-red-100 border-red-300 opacity-50 cursor-not-allowed';
                    ?>
                        <label class="relative">
                            <input type="radio" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>"
                                <?php echo (!$isRoomAvailable) ? 'disabled' : ''; ?>
                                <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'checked' : ''; ?>
                                class="hidden">
                            <div class="room-card border rounded-md p-3 text-center <?php echo $roomClass; ?> <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'room-selected' : ''; ?>">
                                <div class="font-medium"><?php echo htmlspecialchars($room['name']); ?></div>
                                <div class="text-sm text-gray-600">Sức chứa: <?php echo htmlspecialchars($room['capacity']); ?></div>
                                <div class="mt-1 inline-block px-2 py-1 text-xs rounded status-badge <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'bg-indigo-200 text-indigo-800' : ($isRoomAvailable ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'); ?>">
                                    <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'Đã chọn' : ($isRoomAvailable ? 'Trống' : 'Đã đặt'); ?>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Nút Submit -->
            <div class="mt-8 flex justify-end">
                <a href="/pdu_pms_project/public/admin/manage_bookings" class="mr-3 bg-gray-200 py-2 px-4 rounded-md text-gray-700 hover:bg-gray-300 transition duration-300">Hủy bỏ</a>
                <button type="submit" class="bg-indigo-600 py-2 px-4 rounded-md text-white hover:bg-indigo-700 transition duration-300">Thêm đặt phòng</button>
            </div>
        </form>
    </div>
</div>

<style>
    .room-selected {
        box-shadow: 0 0 0 3px #4F46E5 !important;
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
                    statusBadge.classList.remove('bg-green-200', 'text-green-800', 'bg-red-200', 'text-red-800');
                    statusBadge.classList.add('bg-indigo-200', 'text-indigo-800');
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
                                otherStatusBadge.textContent = otherCard.classList.contains('bg-red-100') ? 'Đã đặt' : 'Trống';
                                otherStatusBadge.classList.remove('bg-indigo-200', 'text-indigo-800');
                                otherStatusBadge.classList.add(otherCard.classList.contains('bg-red-100') ? 'bg-red-200' : 'bg-green-200', otherCard.classList.contains('bg-red-100') ? 'text-red-800' : 'text-green-800');
                            }
                        }
                    });

                    card.classList.add('room-selected');
                    input.checked = true;

                    const statusBadge = card.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = 'Đã chọn';
                        statusBadge.classList.remove('bg-green-200', 'text-green-800', 'bg-red-200', 'text-red-800');
                        statusBadge.classList.add('bg-indigo-200', 'text-indigo-800');
                    }

                    card.animate([{
                        transform: 'scale(1.05)'
                    }, {
                        transform: 'scale(1)'
                    }, {
                        transform: 'scale(1.05)'
                    }], {
                        duration: 300,
                        easing: 'ease-in-out'
                    });
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

            // Chỉ gọi fetch nếu cả start_time và end_time đều có giá trị
            if (startTime && endTime) {
                // Kiểm tra xem start_time có nhỏ hơn end_time không
                const startDate = new Date(startTime);
                const endDate = new Date(endTime);
                if (startDate >= endDate) {
                    alert('Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc.');
                    return;
                }

                fetch('/pdu_pms_project/public/admin/add_booking', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}&class_code=${encodeURIComponent(classCode)}&user_type=${encodeURIComponent(userType)}&teacher_id=${encodeURIComponent(teacherId)}&student_id=${encodeURIComponent(studentId)}&status=${encodeURIComponent(status)}`
                    })
                    .then(response => response.text())
                    .then(() => {
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Đã có lỗi xảy ra khi cập nhật danh sách phòng.');
                    });
            } else {
                // Thông báo nếu người dùng chưa chọn đủ cả hai trường
                if (startTime && !endTime) {
                    alert('Vui lòng chọn thời gian kết thúc để cập nhật danh sách phòng.');
                } else if (!startTime && endTime) {
                    alert('Vui lòng chọn thời gian bắt đầu để cập nhật danh sách phòng.');
                }
            }
        };

        startTimeInput.addEventListener('change', updateRoomAvailability);
        endTimeInput.addEventListener('change', updateRoomAvailability);
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>