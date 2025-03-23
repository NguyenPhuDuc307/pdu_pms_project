<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../Helpers/BreadcrumbHelper.php'; ?>

<div class="p-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <?php echo BreadcrumbHelper::render(); ?>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_GET['message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo htmlspecialchars($_GET['message']); ?></p>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?php echo htmlspecialchars($_GET['error']); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
        <div class="flex mb-4 items-center justify-between">
            <h2 class="text-2xl font-bold">Quản lý lịch dạy</h2>
            <a href="/pdu_pms_project/public/admin/add_timetable"
                class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 transition duration-300 flex items-center">
                <i class="fas fa-plus mr-1"></i>
                Thêm lịch dạy
            </a>
        </div>
        <table class="w-full text-left min-w-max">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="p-4 font-semibold">ID</th>
                    <th class="p-4 font-semibold">Giảng viên</th>
                    <th class="p-4 font-semibold">Mã lớp</th>
                    <th class="p-4 font-semibold">Môn học</th>
                    <th class="p-4 font-semibold">Thời gian bắt đầu</th>
                    <th class="p-4 font-semibold">Thời gian kết thúc</th>
                    <th class="p-4 font-semibold">Số người tham gia</th>
                    <th class="p-4 font-semibold">Phòng</th>
                    <th class="p-4 font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['timetables'] as $timetable): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4"><?php echo htmlspecialchars($timetable['id']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($timetable['teacher_name'] ?? 'Chưa phân công'); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($timetable['class_code']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($timetable['subject']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($timetable['start_time']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($timetable['end_time']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($timetable['participants'] ?? 0); ?></td>
                        <td class="p-4">
                            <?php
                            if (isset($timetable['room_id'])) {
                                $room = (new \Models\RoomModel())->getRoomById($timetable['room_id']);
                                echo htmlspecialchars($room['name'] ?? 'Không xác định');
                            } else {
                                echo 'Chưa xếp phòng';
                            }
                            ?>
                        </td>
                        <td class="p-4 flex space-x-2">
                            <?php if (!empty($timetable['room_id'])): ?>
                                <button onclick="cancelRoomSchedule(<?php echo $timetable['id']; ?>)" class="bg-yellow-500 text-white px-4 py-2 rounded-full hover:bg-yellow-600 transition duration-300 flex items-center">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Hủy đặt phòng
                                </button>
                            <?php endif; ?>
                            <button onclick="autoScheduleRoom(<?php echo $timetable['id']; ?>)" class="bg-purple-500 text-white px-4 py-2 rounded-full hover:bg-purple-600 transition duration-300 flex items-center">
                                <i class="fas fa-clock mr-1"></i>
                                Xếp phòng tự động
                            </button>
                            <a href="/pdu_pms_project/public/admin/edit_timetable?id=<?php echo $timetable['id']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition duration-300 flex items-center">
                                <i class="fas fa-edit mr-1"></i>
                                Sửa
                            </a>
                            <a href="/pdu_pms_project/public/admin/delete_timetable?id=<?php echo $timetable['id']; ?>" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition duration-300 flex items-center" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                <i class="fas fa-trash-alt mr-1"></i>
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function autoScheduleRoom(timetable_id) {
        fetch('/pdu_pms_project/public/admin/auto_schedule_room', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    timetable_id: timetable_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Xếp phòng thành công! Phòng được chọn: ' + data.room.name);
                    location.reload(); // Tải lại trang để cập nhật bảng
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra. Vui lòng thử lại.');
            });
    }

    function cancelRoomSchedule(timetable_id) {
        if (!confirm('Bạn có chắc muốn hủy đặt phòng cho lịch dạy này?')) {
            return;
        }

        const formData = new FormData();
        formData.append('timetable_id', timetable_id);

        fetch('/pdu_pms_project/public/admin/cancel_room_schedule', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Tải lại trang để cập nhật
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra. Vui lòng thử lại.');
            });
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>