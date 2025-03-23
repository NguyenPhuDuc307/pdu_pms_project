<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="flex-1 flex items-center justify-center p-6">
    <form method="POST" class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Đăng ký</h1>
        <?php if (isset($data['success'])): ?>
            <p class="text-green-500 bg-green-100 p-3 rounded-lg mb-4"><?php echo $data['success']; ?></p>
        <?php elseif (isset($data['error'])): ?>
            <p class="text-red-500 bg-red-100 p-3 rounded-lg mb-4"><?php echo $data['error']; ?></p>
        <?php endif; ?>
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
                <option value="student">Sinh viên</option>
                <option value="teacher">Giáo viên</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Mã lớp (nếu là sinh viên)</label>
            <input type="text" name="class_code" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit" name="register" class="w-full bg-indigo-500 text-white p-3 rounded-lg hover:bg-indigo-600 transition duration-300">Đăng ký</button>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
