<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../Helpers/BreadcrumbHelper.php'; ?>
<div class="p-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <?php echo BreadcrumbHelper::render(); ?>
    </div>
    <form method="POST" class="bg-white p-8 rounded-xl shadow-lg max-w-md">
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Tên đăng nhập</label>
            <input type="text" name="username" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Email</label>
            <input type="email" name="email" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Mật khẩu</label>
            <input type="password" name="password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Vai trò</label>
            <select name="role" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="admin">Admin</option>
                <option value="teacher">Giáo viên</option>
                <option value="student">Sinh viên</option>
            </select>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Mã lớp (nếu là sinh viên)</label>
            <input type="text" name="class_code" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex space-x-4">
            <button type="submit" name="add_user" class="flex-1 bg-indigo-500 text-white p-3 rounded-lg hover:bg-indigo-600 transition duration-300">Thêm</button>
            <a href="/pdu_pms_project/public/admin/manage_users" class="flex-1 bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600 transition duration-300 text-center">Quay lại</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>