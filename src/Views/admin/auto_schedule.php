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
        <h2 class="text-2xl font-bold mb-4">Xếp lịch tự động</h2>
        <table class="w-full text-left min-w-max">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="p-4 font-semibold">ID</th>
                    <th class="p-4 font-semibold">Giảng viên</th>
                    <th class="p-4 font-semibold">Mã lớp</th>
                    <th class="p-4 font-semibold">Môn học</th>
                    <th class="p-4 font-semibold">Thời gian bắt đầu</th>
                    <th class="p-4 font-semibold">Thời gian kết thúc</th>
                    <th class="p-4 font-semibold">Phòng</th>
                    <th class="p-4 font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['timetables'] as $timetable): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4"><?php echo $timetable['id']; ?></td>
                        <td class="p-4"><?php echo $timetable['teacher_name']; ?></td>
                        <td class="p-4"><?php echo $timetable['class_code']; ?></td>
                        <td class="p-4"><?php echo $timetable['subject']; ?></td>
                        <td class="p-4"><?php echo $timetable['start_time']; ?></td>
                        <td class="p-4"><?php echo $timetable['end_time']; ?></td>
                        <td class="p-4">
                            <?php
                            if (isset($timetable['room_id'])) {
                                $room = (new \Models\RoomModel())->getRoomById($timetable['room_id']);
                                echo $room['name'] ?? 'Không xác định';
                            } else {
                                echo 'Chưa xếp phòng';
                            }
                            ?>
                        </td>
                        <td class="p-4">
                            <form method="POST" action="/pdu_pms_project/public/admin/auto_schedule">
                                <input type="hidden" name="timetable_id" value="<?php echo $timetable['id']; ?>">
                                <button type="submit" class="bg-purple-500 text-white px-4 py-2 rounded-full hover:bg-purple-600 transition duration-300 flex items-center">
                                    <i class="fas fa-clock mr-1"></i>
                                    Xếp phòng tự động
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>