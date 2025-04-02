<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../Helpers/BreadcrumbHelper.php'; ?>
<div class="p-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <?php echo BreadcrumbHelper::render(); ?>
    </div>
    <form method="POST" class="bg-white p-8 rounded-xl shadow-lg max-w-md">
        <input type="hidden" name="id" value="<?php echo $data['room']['id']; ?>">
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Tên phòng</label>
            <input type="text" name="name" value="<?php echo $data['room']['name']; ?>" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Sức chứa</label>
            <input type="number" name="capacity" value="<?php echo $data['room']['capacity']; ?>" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Trạng thái</label>
            <select name="status" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="trống" <?php echo $data['room']['status'] === 'trống' ? 'selected' : ''; ?>>Trống</option>
                <option value="đã đặt" <?php echo $data['room']['status'] === 'đã đặt' ? 'selected' : ''; ?>>Đã đặt</option>
                <option value="bảo trì" <?php echo $data['room']['status'] === 'bảo trì' ? 'selected' : ''; ?>>Bảo trì</option>
            </select>
        </div>
        <div class="flex space-x-4">
            <button type="submit" name="edit_room" class="flex-1 bg-indigo-500 text-white p-3 rounded-lg hover:bg-indigo-600 transition duration-300">Cập nhật</button>
            <a href="/pdu_pms_project/public/admin/manage_rooms" class="flex-1 bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600 transition duration-300 text-center">Quay lại</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>