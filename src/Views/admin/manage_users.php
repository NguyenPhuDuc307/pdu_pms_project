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
            <h2 class="text-2xl font-bold">Quản lý người dùng</h2>
            <a href="/pdu_pms_project/public/admin/add_user"
                class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 transition duration-300 flex items-center">
                <i class="fas fa-plus mr-1"></i>
                Thêm người dùng
            </a>
        </div>
        <table class="w-full text-left min-w-max">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="p-4 font-semibold">ID</th>
                    <th class="p-4 font-semibold">Tên đăng nhập</th>
                    <th class="p-4 font-semibold">Tên người dùng</th>
                    <th class="p-4 font-semibold">Vai trò</th>
                    <th class="p-4 font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['users'] as $user): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4"><?php echo htmlspecialchars($user['id']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="p-4 flex space-x-2">
                            <a href="/pdu_pms_project/public/admin/edit_user?id=<?php echo $user['id']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition duration-300 flex items-center">
                                <i class="fas fa-edit mr-1"></i>
                                Sửa
                            </a>
                            <a href="/pdu_pms_project/public/admin/delete_user?id=<?php echo $user['id']; ?>" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition duration-300 flex items-center" onclick="return confirm('Bạn có chắc muốn xóa?')">
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>