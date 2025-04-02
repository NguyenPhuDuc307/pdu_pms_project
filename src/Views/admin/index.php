<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="p-6 bg-gray-50">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center bg-white p-4 rounded-lg shadow-sm">
        <nav class="text-sm">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="/pdu_pms_project/public/admin" class="text-indigo-600 font-medium">Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-md mb-8 p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Chào mừng, <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Người dùng'; ?>!</h1>
                <p class="opacity-90">Hệ thống quản lý phòng học PDU - PMS</p>
                <a href="/pdu_pms_project/public/guide" class="inline-block mt-4 bg-white text-indigo-600 px-4 py-2 rounded-md hover:bg-opacity-90 transition duration-300 font-medium shadow-sm">Xem hướng dẫn</a>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-university text-8xl text-white opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Quick Actions - MOVED UP as it's an important feature for admin convenience -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="/pdu_pms_project/public/admin/add_booking" class="bg-indigo-50 hover:bg-indigo-100 transition duration-300 p-4 rounded-lg flex flex-col items-center justify-center text-center">
                <i class="fas fa-plus-circle text-indigo-600 text-2xl mb-2"></i>
                <span class="text-gray-700 font-medium">Đặt phòng mới</span>
            </a>
            <a href="/pdu_pms_project/public/admin/manage_users" class="bg-green-50 hover:bg-green-100 transition duration-300 p-4 rounded-lg flex flex-col items-center justify-center text-center">
                <i class="fas fa-users text-green-600 text-2xl mb-2"></i>
                <span class="text-gray-700 font-medium">Quản lý người dùng</span>
            </a>
            <a href="/pdu_pms_project/public/admin/manage_timetable" class="bg-purple-50 hover:bg-purple-100 transition duration-300 p-4 rounded-lg flex flex-col items-center justify-center text-center">
                <i class="fas fa-calendar-alt text-purple-600 text-2xl mb-2"></i>
                <span class="text-gray-700 font-medium">Xem lịch tuần</span>
            </a>
            <a href="/pdu_pms_project/public/admin/auto_schedule" class="bg-orange-50 hover:bg-orange-100 transition duration-300 p-4 rounded-lg flex flex-col items-center justify-center text-center">
                <i class="fas fa-magic text-orange-600 text-2xl mb-2"></i>
                <span class="text-gray-700 font-medium">Xếp lịch tự động</span>
            </a>
        </div>
    </div>

    <!-- Calendar Overview - MOVED UP as it shows urgent daily information -->
    <div class="bg-white rounded-lg shadow-sm p-5 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700">Lịch phòng hôm nay</h3>
            <div>
                <span class="text-gray-500 mr-2"><?php echo date('d/m/Y'); ?></span>
                <a href="/pdu_pms_project/public/admin/manage_timetable" class="text-indigo-600 hover:text-indigo-800 text-sm">Xem lịch đầy đủ</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phòng</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">7:00 - 9:30</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">9:45 - 12:15</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">13:00 - 15:30</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">15:45 - 18:15</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($data['today_schedule'])): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Không có lịch nào cho hôm nay</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['today_schedule'] as $room): ?>
                            <tr>
                                <td class="py-2 px-3 whitespace-nowrap text-sm font-medium text-gray-700"><?php echo htmlspecialchars($room['room_number']); ?></td>
                                <td class="py-2 px-3 whitespace-nowrap text-sm text-gray-500">
                                    <?php if (isset($room['slots']['7:00 - 9:30'])): ?>
                                        <div class="bg-green-100 text-green-800 py-1 px-2 rounded text-xs">
                                            <?php echo htmlspecialchars($room['slots']['7:00 - 9:30']['subject']); ?> -
                                            <?php echo htmlspecialchars($room['slots']['7:00 - 9:30']['teacher']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap text-sm text-gray-500">
                                    <?php if (isset($room['slots']['9:45 - 12:15'])): ?>
                                        <div class="bg-blue-100 text-blue-800 py-1 px-2 rounded text-xs">
                                            <?php echo htmlspecialchars($room['slots']['9:45 - 12:15']['subject']); ?> -
                                            <?php echo htmlspecialchars($room['slots']['9:45 - 12:15']['teacher']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap text-sm text-gray-500">
                                    <?php if (isset($room['slots']['13:00 - 15:30'])): ?>
                                        <div class="bg-yellow-100 text-yellow-800 py-1 px-2 rounded text-xs">
                                            <?php echo htmlspecialchars($room['slots']['13:00 - 15:30']['subject']); ?> -
                                            <?php echo htmlspecialchars($room['slots']['13:00 - 15:30']['teacher']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap text-sm text-gray-500">
                                    <?php if (isset($room['slots']['15:45 - 18:15'])): ?>
                                        <div class="bg-purple-100 text-purple-800 py-1 px-2 rounded text-xs">
                                            <?php echo htmlspecialchars($room['slots']['15:45 - 18:15']['subject']); ?> -
                                            <?php echo htmlspecialchars($room['slots']['15:45 - 18:15']['teacher']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stats Cards - KEY METRICS stay high in the layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Phòng đang sử dụng</p>
                    <h3 class="text-2xl font-bold text-gray-700"><?php echo $data['stats']['rooms_in_use']; ?></h3>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-door-open text-orange-500 text-xl"></i>
                </div>
            </div>
            <p class="<?php
                        echo $data['stats']['rooms_in_use_change_percent'] >= 0
                            ? 'text-green-500'
                            : 'text-orange-500';
                        ?> text-sm mt-4 flex items-center">
                <i class="fas fa-arrow-<?php
                                        echo $data['stats']['rooms_in_use_change_percent'] >= 0
                                            ? 'up'
                                            : 'down';
                                        ?> mr-1"></i>
                <span>
                    Đã <?php
                        echo $data['stats']['rooms_in_use_change_percent'] >= 0 ? 'tăng' : 'giảm';
                        echo ' ' . abs($data['stats']['rooms_in_use_change_percent']) . '% từ hôm qua';
                        ?>
                </span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đặt phòng hôm nay</p>
                    <h3 class="text-2xl font-bold text-gray-700"><?php echo $data['stats']['today_bookings']; ?></h3>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-bookmark text-green-500 text-xl"></i>
                </div>
            </div>
            <p class="text-green-500 text-sm mt-4 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>Đã tăng <?php echo $data['stats']['today_bookings_increase_percent'] ?? 12; ?>% từ hôm qua</span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng số phòng</p>
                    <h3 class="text-2xl font-bold text-gray-700"><?php echo $data['stats']['total_rooms']; ?></h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-building text-blue-500 text-xl"></i>
                </div>
            </div>
            <p class="text-green-500 text-sm mt-4 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>Đã tăng 8% từ tháng trước</span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng số người dùng</p>
                    <h3 class="text-2xl font-bold text-gray-700"><?php echo $data['stats']['total_users']; ?></h3>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-users text-purple-500 text-xl"></i>
                </div>
            </div>
            <p class="text-green-500 text-sm mt-4 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>Đã tăng <?php echo $data['stats']['total_users_increase_percent'] ?? 5; ?>% từ tháng trước</span>
            </p>
        </div>
    </div>

    <!-- Most Used Rooms - IMPORTANT ANALYTICS moved up -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Phòng được sử dụng nhiều nhất</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số phòng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Loại phòng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sức chứa
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số lần đặt
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tỷ lệ sử dụng
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($data['most_used_rooms'])): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Không có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['most_used_rooms'] as $room): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($room['room_number']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($room['type']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($room['capacity']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($room['booking_count']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $percentage = min(100, ($room['booking_count'] / max(1, $data['total_bookings_this_month'])) * 100);
                                    $colorClass = $percentage > 75 ? 'bg-green-500' : ($percentage > 50 ? 'bg-blue-500' : 'bg-indigo-500');
                                    ?>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="<?php echo $colorClass; ?> h-2.5 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php echo number_format($percentage, 1); ?>%
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Statistics Summary - HIGHER LEVEL STATS moved up -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Thống kê đặt phòng</h3>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-gray-500">Tổng số đặt phòng tháng này</p>
                    <h4 class="text-2xl font-bold text-gray-700 mt-1"><?php echo $data['total_bookings_this_month']; ?></h4>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Tỷ lệ đặt phòng thành công</p>
                    <h4 class="text-2xl font-bold text-gray-700 mt-1"><?php echo $data['booking_success_rate']; ?>%</h4>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Thống kê người dùng</h3>
            <div class="bg-gray-100 p-4 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600">Quản trị viên</p>
                    <p class="font-semibold"><?php echo $data['users_by_role']['admin']; ?></p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-indigo-500 h-2 rounded-full" style="width: <?php echo ($data['users_by_role']['admin'] / $data['stats']['total_users']) * 100; ?>%"></div>
                </div>

                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600">Giảng viên</p>
                    <p class="font-semibold"><?php echo $data['users_by_role']['teacher']; ?></p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo ($data['users_by_role']['teacher'] / $data['stats']['total_users']) * 100; ?>%"></div>
                </div>

                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600">Sinh viên</p>
                    <p class="font-semibold"><?php echo $data['users_by_role']['student']; ?></p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo ($data['users_by_role']['student'] / $data['stats']['total_users']) * 100; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Role Distribution -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Phân bố người dùng theo vai trò</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-indigo-50 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-indigo-700 font-medium">Quản trị viên</p>
                        <h4 class="text-3xl font-bold text-indigo-800 mt-1"><?php echo $data['users_by_role']['admin']; ?></h4>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <i class="fas fa-user-shield text-indigo-700 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-blue-700 font-medium">Giảng viên</p>
                        <h4 class="text-3xl font-bold text-blue-800 mt-1"><?php echo $data['users_by_role']['teacher']; ?></h4>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-chalkboard-teacher text-blue-700 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-green-50 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-green-700 font-medium">Sinh viên</p>
                        <h4 class="text-3xl font-bold text-green-800 mt-1"><?php echo $data['users_by_role']['student']; ?></h4>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-user-graduate text-green-700 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm p-5 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700">Hoạt động gần đây</h3>
            <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm">Xem tất cả</a>
        </div>
        <div class="space-y-4">
            <?php if (empty($data['recent_activities'])): ?>
                <p class="text-gray-500 text-center py-4">Không có hoạt động nào gần đây</p>
            <?php else: ?>
                <?php foreach ($data['recent_activities'] as $activity): ?>
                    <div class="flex items-start">
                        <?php if ($activity['type'] === 'user_registration'): ?>
                            <div class="bg-purple-100 p-2 rounded-full mr-3">
                                <i class="fas fa-user-plus text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-700"><?php echo htmlspecialchars($activity['message']); ?></p>
                                <p class="text-gray-400 text-sm"><?php echo date('d/m/Y H:i', strtotime($activity['timestamp'])); ?></p>
                            </div>
                        <?php elseif ($activity['type'] === 'booking'): ?>
                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                <i class="fas fa-plus text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-700"><?php echo htmlspecialchars($activity['message']); ?></p>
                                <p class="text-gray-400 text-sm"><?php echo date('d/m/Y H:i', strtotime($activity['timestamp'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="#" class="block mt-4 text-center text-indigo-600 hover:text-indigo-800 text-sm">Xem thêm hoạt động</a>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>