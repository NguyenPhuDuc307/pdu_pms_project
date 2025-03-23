<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../Helpers/BreadcrumbHelper.php'; ?>
<div class="p-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <?php echo BreadcrumbHelper::render(); ?>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
        <table class="w-full text-left min-w-max">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="p-4 font-semibold">ID</th>
                    <th class="p-4 font-semibold">Phòng</th>
                    <th class="p-4 font-semibold">Mã lớp</th>
                    <th class="p-4 font-semibold">Bắt đầu</th>
                    <th class="p-4 font-semibold">Kết thúc</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['schedule'] as $booking): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4"><?php echo $booking['id']; ?></td>
                        <td class="p-4"><?php echo $booking['room_name']; ?></td>
                        <td class="p-4"><?php echo $booking['class_code']; ?></td>
                        <td class="p-4"><?php echo $booking['start_time']; ?></td>
                        <td class="p-4"><?php echo $booking['end_time']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>