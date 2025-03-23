<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="p-6 bg-gray-50">
    <div class="mb-6 flex items-center bg-white p-4 rounded-lg shadow-sm">
        <nav class="text-sm">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="/pdu_pms_project/public/admin" class="text-gray-500 hover:text-indigo-600">Dashboard</a>
                    <svg class="w-3 h-3 mx-2 fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path>
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="/pdu_pms_project/public/admin/manage_timetable" class="text-gray-500 hover:text-indigo-600">Quản lý lịch dạy</a>
                    <svg class="w-3 h-3 mx-2 fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path>
                    </svg>
                </li>
                <li>
                    <span class="text-indigo-600 font-medium">Chỉnh sửa lịch dạy</span>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Chỉnh sửa lịch dạy</h2>

        <?php if (isset($data['errors']) && !empty($data['errors'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <ul class="list-disc pl-4">
                    <?php foreach ($data['errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/pdu_pms_project/public/admin/edit_timetable?id=<?php echo htmlspecialchars($data['timetable']['id']); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">Giảng viên</label>
                    <select name="teacher_id" id="teacher_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <?php foreach ($data['teachers'] as $teacher): ?>
                            <?php if ($teacher['role'] === 'teacher' || $teacher['role'] === 'admin'): ?>
                                <option value="<?php echo htmlspecialchars($teacher['id']); ?>" <?php echo ($teacher['id'] == $data['timetable']['teacher_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($teacher['username']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Môn học</label>
                    <input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars($data['timetable']['subject']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="class_code" class="block text-sm font-medium text-gray-700 mb-1">Mã lớp</label>
                    <input type="text" name="class_code" id="class_code" value="<?php echo htmlspecialchars($data['timetable']['class_code']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="participants" class="block text-sm font-medium text-gray-700 mb-1">Số lượng sinh viên</label>
                    <input type="number" name="participants" id="participants" value="<?php echo htmlspecialchars($data['timetable']['participants']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Thời gian bắt đầu</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($data['timetable']['start_time'])); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Thời gian kết thúc</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($data['timetable']['end_time'])); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
                        <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded mr-2"></div>
                        <span>Phòng hiện tại</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-100 border border-red-300 rounded mr-2"></div>
                        <span>Phòng đã đặt</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-gray-100 border border-gray-300 rounded mr-2"></div>
                        <span>Không đủ sức chứa</span>
                    </div>
                </div>

                <!-- Grid hiển thị các phòng -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <?php
                    $participants = (int)($data['timetable']['participants'] ?? 0);
                    $currentRoomId = $data['timetable']['room_id'] ?? null;
                    $start_time = $data['timetable']['start_time'] ?? null;
                    $end_time = $data['timetable']['end_time'] ?? null;
                    $timetableId = $data['timetable']['id'] ?? null;

                    foreach ($data['rooms'] as $room):
                        // Kiểm tra xem phòng này có đủ sức chứa không
                        $hasEnoughCapacity = $room['capacity'] >= $participants;

                        // Kiểm tra xem phòng này có phải phòng hiện tại không
                        $isCurrentRoom = ($room['id'] == $currentRoomId);

                        // Kiểm tra xem phòng này có trống trong khung giờ này không
                        $isRoomAvailable = true;
                        if (!$isCurrentRoom && $start_time && $end_time) {
                            // Giả sử bạn có một hàm kiểm tra phòng trống trong controller và truyền kết quả vào view
                            // Ở đây tôi tạm giả định rằng những phòng có ID chẵn là trống
                            // Trong thực tế, bạn sẽ cần kiểm tra từ cơ sở dữ liệu
                            $isRoomAvailable = isset($data['available_rooms']) ?
                                in_array($room['id'], $data['available_rooms']) : ($room['id'] % 2 == 0);
                        }

                        // Xác định class cho phòng
                        if ($isCurrentRoom) {
                            $roomClass = 'bg-yellow-100 border-yellow-300';
                        } elseif (!$hasEnoughCapacity) {
                            $roomClass = 'bg-gray-100 border-gray-300 opacity-50 cursor-not-allowed';
                        } elseif (!$isRoomAvailable) {
                            $roomClass = 'bg-red-100 border-red-300 opacity-50 cursor-not-allowed';
                        } else {
                            $roomClass = 'bg-green-100 border-green-300 hover:bg-green-200 cursor-pointer';
                        }
                    ?>
                        <label class="relative">
                            <input type="radio" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>"
                                <?php echo $isCurrentRoom ? 'checked' : ''; ?>
                                <?php echo (!$hasEnoughCapacity || (!$isRoomAvailable && !$isCurrentRoom)) ? 'disabled' : ''; ?>
                                class="hidden">
                            <div class="room-card border rounded-md p-3 text-center <?php echo $roomClass; ?> <?php echo $isCurrentRoom && !isset($_POST['room_id']) ? 'room-selected' : ''; ?>">
                                <div class="font-medium"><?php echo htmlspecialchars($room['name']); ?></div>
                                <div class="text-sm text-gray-600">Sức chứa: <?php echo htmlspecialchars($room['capacity']); ?></div>
                                <?php
                                $badgeClass = "mt-1 inline-block px-2 py-1 text-xs rounded status-badge";
                                $badgeText = "";
                                $badgeColorClass = "";

                                if ($isCurrentRoom && !isset($_POST['room_id'])) {
                                    $badgeText = "Hiện tại";
                                    $badgeColorClass = "bg-yellow-200 text-yellow-800";
                                } elseif ($isCurrentRoom && isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) {
                                    $badgeText = "Đã chọn";
                                    $badgeColorClass = "bg-indigo-200 text-indigo-800";
                                } elseif (!$hasEnoughCapacity) {
                                    $badgeText = "Không đủ";
                                    $badgeColorClass = "bg-gray-200 text-gray-800";
                                } elseif (!$isRoomAvailable) {
                                    $badgeText = "Đã đặt";
                                    $badgeColorClass = "bg-red-200 text-red-800";
                                } else {
                                    $badgeText = "Trống";
                                    $badgeColorClass = "bg-green-200 text-green-800";
                                }
                                ?>
                                <div class="<?php echo $badgeClass . ' ' . $badgeColorClass; ?>"><?php echo $badgeText; ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Nút Submit -->
            <div class="mt-8 flex justify-end">
                <a href="/pdu_pms_project/public/admin/manage_timetable" class="mr-3 bg-gray-200 py-2 px-4 rounded-md text-gray-700 hover:bg-gray-300 transition duration-300">Hủy bỏ</a>
                <button type="submit" name="update_timetable" class="bg-indigo-600 py-2 px-4 rounded-md text-white hover:bg-indigo-700 transition duration-300">Cập nhật</button>
            </div>
        </form>
    </div>
</div>
<style>
    /* Thêm style cho phòng được chọn */
    .room-selected {
        box-shadow: 0 0 0 3px #4F46E5 !important;
        transform: scale(1.05);
        z-index: 10;
        position: relative;
    }

    /* Hiệu ứng khi hover */
    .room-card:not(.disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Hiệu ứng khi nhấp chuột */
    .room-card:not(.disabled):active {
        transform: scale(0.98);
    }

    /* Transition cho mọi hiệu ứng */
    .room-card {
        transition: all 0.2s ease-in-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Thêm sự kiện cho các thẻ div chứa radio button
        document.querySelectorAll('input[name="room_id"]').forEach(function(input) {
            const label = input.closest('label');
            const card = label.querySelector('.room-card');

            // Thiết lập trạng thái ban đầu
            if (input.checked) {
                card.classList.add('room-selected');
                const statusBadge = card.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.textContent = 'Đã chọn';
                    statusBadge.classList.remove('bg-yellow-200', 'text-yellow-800', 'bg-green-200', 'text-green-800');
                    statusBadge.classList.add('bg-indigo-200', 'text-indigo-800');
                }
            }

            if (!input.disabled) {
                label.addEventListener('click', function() {
                    // Bỏ chọn tất cả các phòng
                    document.querySelectorAll('.room-card').forEach(function(otherCard) {
                        otherCard.classList.remove('room-selected');
                        const otherStatusBadge = otherCard.querySelector('.status-badge');
                        if (otherStatusBadge && otherStatusBadge.textContent === 'Đã chọn') {
                            const otherInput = otherCard.closest('label').querySelector('input');
                            if (otherInput && otherInput.value != input.value) {
                                // Khôi phục trạng thái ban đầu
                                if (otherCard.classList.contains('bg-yellow-100')) {
                                    otherStatusBadge.textContent = 'Hiện tại';
                                    otherStatusBadge.classList.remove('bg-indigo-200', 'text-indigo-800');
                                    otherStatusBadge.classList.add('bg-yellow-200', 'text-yellow-800');
                                } else {
                                    otherStatusBadge.textContent = 'Trống';
                                    otherStatusBadge.classList.remove('bg-indigo-200', 'text-indigo-800');
                                    otherStatusBadge.classList.add('bg-green-200', 'text-green-800');
                                }
                            }
                        }
                    });

                    // Đánh dấu phòng này là đã chọn
                    card.classList.add('room-selected');
                    input.checked = true;

                    // Cập nhật badge
                    const statusBadge = card.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = 'Đã chọn';
                        statusBadge.classList.remove('bg-yellow-200', 'text-yellow-800', 'bg-green-200', 'text-green-800');
                        statusBadge.classList.add('bg-indigo-200', 'text-indigo-800');
                    }

                    // Thêm hiệu ứng cảm giác tốt
                    card.animate([{
                            transform: 'scale(1.05)'
                        },
                        {
                            transform: 'scale(1)'
                        },
                        {
                            transform: 'scale(1.05)'
                        }
                    ], {
                        duration: 300,
                        easing: 'ease-in-out'
                    });
                });
            }
        });

        // Thêm sự kiện cho input số lượng sinh viên để kiểm tra lại sức chứa
        const participantsInput = document.getElementById('participants');
        if (participantsInput) {
            participantsInput.addEventListener('change', function() {
                const participants = parseInt(this.value, 10) || 0;
                // Trong thực tế, bạn có thể muốn gửi một AJAX request để lấy lại danh sách phòng phù hợp
                // Ở đây tôi chỉ cập nhật UI cho phù hợp với logic đơn giản
                document.querySelectorAll('label').forEach(function(label) {
                    const capacityText = label.querySelector('.text-gray-600');
                    if (capacityText) {
                        const capacity = parseInt(capacityText.textContent.replace('Sức chứa: ', ''), 10) || 0;
                        const input = label.querySelector('input[name="room_id"]');
                        const div = label.querySelector('div.border');
                        const statusDiv = label.querySelector('.mt-1');

                        if (capacity < participants) {
                            // Phòng không đủ sức chứa
                            input.disabled = true;
                            div.classList.remove('bg-green-100', 'border-green-300', 'hover:bg-green-200', 'cursor-pointer');
                            div.classList.add('bg-gray-100', 'border-gray-300', 'opacity-50', 'cursor-not-allowed');

                            if (statusDiv) {
                                statusDiv.textContent = 'Không đủ';
                                statusDiv.classList.remove('bg-green-200', 'text-green-800');
                                statusDiv.classList.add('bg-gray-200', 'text-gray-800');
                            }
                        } else if (!input.disabled && !div.classList.contains('bg-yellow-100') && !div.classList.contains('bg-red-100')) {
                            // Phòng đủ sức chứa và không phải phòng hiện tại hoặc đã đặt
                            div.classList.remove('bg-gray-100', 'border-gray-300', 'opacity-50', 'cursor-not-allowed');
                            div.classList.add('bg-green-100', 'border-green-300', 'hover:bg-green-200', 'cursor-pointer');

                            if (statusDiv && statusDiv.textContent === 'Không đủ') {
                                statusDiv.textContent = 'Trống';
                                statusDiv.classList.remove('bg-gray-200', 'text-gray-800');
                                statusDiv.classList.add('bg-green-200', 'text-green-800');
                            }
                        }
                    }
                });
            });
        }
    });
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>