<?php $role = $_SESSION['role'] ?? null; ?>
<div x-data="{ sidebarOpen: true }" class="flex">
    <aside class="h-screen fixed transition-all duration-300 ease-in-out z-10 shadow-lg" :class="sidebarOpen ? 'w-64 p-5' : 'w-18 p-3'" style="background: linear-gradient(180deg, #4F46E5, #7C3AED);">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <i class="fas fa-home text-white text-xl"></i>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="text-white opacity-80 hover:opacity-100">
                <i x-show="sidebarOpen" class="fas fa-chevron-left"></i>
                <i x-show="!sidebarOpen" class="fas fa-chevron-right"></i>
            </button>
        </div>
        <ul class="space-y-4">
            <!-- Trang chính -->
            <li>
                <a href="/pdu_pms_project/public/<?php echo strtolower($role); ?>" class="flex items-center py-3 px-4 rounded-md bg-white/10 hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                    <i class="fas fa-tachometer-alt text-lg"></i>
                    <span x-show="sidebarOpen">Trang chính</span>
                </a>
            </li>

            <?php if ($role === 'admin'): ?>
                <!-- Quản lý người dùng -->
                <li>
                    <a href="/pdu_pms_project/public/admin/manage_users" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-users text-lg"></i>
                        <span x-show="sidebarOpen">Quản lý người dùng</span>
                    </a>
                </li>

                <!-- Quản lý phòng -->
                <li>
                    <a href="/pdu_pms_project/public/admin/manage_rooms" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-building text-lg"></i>
                        <span x-show="sidebarOpen">Quản lý phòng</span>
                    </a>
                </li>

                <!-- Quản lý lịch dạy -->
                <li>
                    <a href="/pdu_pms_project/public/admin/manage_timetable" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-calendar-alt text-lg"></i>
                        <span x-show="sidebarOpen">Quản lý lịch dạy</span>
                    </a>
                </li>

                <!-- Quản lý đặt phòng -->
                <li>
                    <a href="/pdu_pms_project/public/admin/manage_bookings" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-bookmark text-lg"></i>
                        <span x-show="sidebarOpen">Quản lý đặt phòng</span>
                    </a>
                </li>

                <!-- Xếp lịch tự động -->
                <li>
                    <a href="/pdu_pms_project/public/admin/auto_schedule" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-clock text-lg"></i>
                        <span x-show="sidebarOpen">Xếp lịch tự động</span>
                    </a>
                </li>
            <?php elseif ($role === 'teacher'): ?>
                <!-- Lịch giảng dạy -->
                <li>
                    <a href="/pdu_pms_project/public/teacher" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-calendar-alt text-lg"></i>
                        <span x-show="sidebarOpen">Lịch giảng dạy</span>
                    </a>
                </li>

                <!-- Đặt phòng -->
                <li>
                    <a href="/pdu_pms_project/public/teacher/book_room" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-plus-square text-lg"></i>
                        <span x-show="sidebarOpen">Đặt phòng</span>
                    </a>
                </li>
            <?php elseif ($role === 'student'): ?>
                <!-- Lịch thực hành -->
                <li>
                    <a href="/pdu_pms_project/public/student" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-calendar-alt text-lg"></i>
                        <span x-show="sidebarOpen">Lịch thực hành</span>
                    </a>
                </li>

                <!-- Đặt phòng -->
                <li>
                    <a href="/pdu_pms_project/public/student/book_room" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                        <i class="fas fa-plus-square text-lg"></i>
                        <span x-show="sidebarOpen">Đặt phòng</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Hướng dẫn sử dụng và Giới thiệu (Dành cho tất cả người dùng) -->
            <li class="pt-6 mt-6 border-t border-white/20">
                <a href="/pdu_pms_project/public/guide" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                    <i class="fas fa-book text-lg"></i>
                    <span x-show="sidebarOpen">Hướng dẫn sử dụng</span>
                </a>
            </li>
            <li>
                <a href="/pdu_pms_project/public/about" class="flex items-center py-3 px-4 rounded-md hover:bg-white/20 transition duration-300 text-white whitespace-nowrap" :class="sidebarOpen ? 'justify-start space-x-3' : 'justify-center'">
                    <i class="fas fa-info-circle text-lg"></i>
                    <span x-show="sidebarOpen">Giới thiệu</span>
                </a>
            </li>
        </ul>
    </aside>

    <div class="flex-1" :class="sidebarOpen ? 'ml-64' : 'ml-18'">
        <!-- Main content area -->
        <main class="container mx-auto px-6 py-8">
            <?php if (isset($content)): ?>
                <?php echo $content; ?>
            <?php endif; ?>
        </main>
    </div>
</div>