<?php
// Sử dụng đường dẫn tuyệt đối để tránh lỗi
$title = 'Hướng dẫn sử dụng';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/sidebar.php';
?>

<div class="p-6 bg-gray-50">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center bg-white p-4 rounded-lg shadow-sm">
        <nav class="text-sm">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="/pdu_pms_project/public" class="text-gray-500 hover:text-indigo-600">Trang chủ</a>
                    <svg class="w-3 h-3 mx-2 fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path>
                    </svg>
                </li>
                <li>
                    <span class="text-indigo-600 font-medium">Hướng dẫn sử dụng</span>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-md mb-8 p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Hướng dẫn sử dụng PDU-PMS</h1>
                <p class="opacity-90">Tài liệu hướng dẫn chi tiết cho hệ thống quản lý phòng học</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-book-reader text-8xl text-white opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Guide Content -->
    <div class="bg-white rounded-lg shadow-sm mb-8">
        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <a href="#general" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Tổng quan
                </a>
                <a href="#admin" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Quản trị viên
                </a>
                <a href="#teacher" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Giảng viên
                </a>
                <a href="#student" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Sinh viên
                </a>
                <a href="#faq" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Câu hỏi thường gặp
                </a>
            </nav>
        </div>

        <!-- General Guide -->
        <div id="general" class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Giới thiệu về PDU-PMS</h2>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Về hệ thống</h3>
                <p class="text-gray-600 mb-4">
                    PDU-PMS (Phan Dinh Phung University - Phòng Management System) là hệ thống quản lý phòng học hiện đại được phát triển nhằm tối ưu hóa việc sử dụng tài nguyên phòng học và thiết bị của trường đại học. Hệ thống cho phép quản lý toàn diện về phòng học, lịch dạy, và quản lý người dùng.
                </p>
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <h4 class="font-medium text-indigo-700 mb-2">Tính năng chính:</h4>
                    <ul class="list-disc pl-5 text-gray-600 space-y-1">
                        <li>Quản lý thông tin phòng học và trang thiết bị</li>
                        <li>Đặt phòng trực tuyến và xem lịch sử đặt phòng</li>
                        <li>Theo dõi lịch sử sử dụng phòng học</li>
                        <li>Xếp lịch tự động cho các lớp học</li>
                        <li>Quản lý người dùng (Admin, Giảng viên, Sinh viên)</li>
                        <li>Thông báo và nhắc nhở về lịch học</li>
                    </ul>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Hướng dẫn đăng nhập</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 mb-4">
                            Để bắt đầu sử dụng PDU-PMS, bạn cần đăng nhập vào hệ thống bằng tài khoản đã được cấp.
                        </p>
                        <ol class="list-decimal pl-5 text-gray-600 space-y-2">
                            <li>Truy cập vào trang đăng nhập</li>
                            <li>Nhập tên đăng nhập (mã số cá nhân) và mật khẩu</li>
                            <li>Nhấn nút "Đăng nhập"</li>
                            <li>Đối với lần đăng nhập đầu tiên, bạn sẽ được yêu cầu đổi mật khẩu</li>
                        </ol>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-2">Thông tin đăng nhập mẫu:</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Quản trị viên:</p>
                                <p class="font-mono bg-gray-200 p-2 rounded">
                                    Tài khoản: admin<br>
                                    Mật khẩu: password
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Giảng viên:</p>
                                <p class="font-mono bg-gray-200 p-2 rounded">
                                    Tài khoản: teacher<br>
                                    Mật khẩu: password
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Sinh viên:</p>
                                <p class="font-mono bg-gray-200 p-2 rounded">
                                    Tài khoản: student<br>
                                    Mật khẩu: password
                                </p>
                            </div>
                        </div>
                        <p class="text-red-500 text-sm mt-3">*Lưu ý: Thông tin này chỉ dùng cho mục đích demo. Vui lòng đổi mật khẩu sau khi đăng nhập.</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Giao diện hệ thống</h3>
                <p class="text-gray-600 mb-4">
                    Giao diện PDU-PMS được thiết kế trực quan, dễ sử dụng với các thành phần chính sau:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-white border rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-bars text-indigo-600 mr-2"></i>
                            <h4 class="font-medium text-gray-700">Thanh điều hướng</h4>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Nằm bên trái màn hình, cho phép truy cập nhanh đến các chức năng chính của hệ thống.
                        </p>
                    </div>
                    <div class="bg-white border rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-tachometer-alt text-indigo-600 mr-2"></i>
                            <h4 class="font-medium text-gray-700">Dashboard</h4>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Hiển thị tổng quan về thông tin quan trọng, thống kê và hoạt động gần đây.
                        </p>
                    </div>
                    <div class="bg-white border rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-user-circle text-indigo-600 mr-2"></i>
                            <h4 class="font-medium text-gray-700">Quản lý tài khoản</h4>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Truy cập từ góc trên bên phải, cho phép quản lý thông tin cá nhân và đăng xuất.
                        </p>
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-1"></i>
                        <div>
                            <h4 class="font-medium text-yellow-700 mb-1">Mẹo sử dụng:</h4>
                            <p class="text-gray-600 text-sm">
                                Bạn có thể thu gọn thanh điều hướng bằng cách nhấn vào biểu tượng mũi tên ở phía trên để có không gian làm việc rộng hơn. Các tính năng vẫn có thể được truy cập thông qua các biểu tượng.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <span></span>
                <a href="#admin" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                    <span>Hướng dẫn cho Quản trị viên</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Admin Guide Preview -->
    <div id="admin" class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Hướng dẫn cho Quản trị viên</h2>
        <p class="text-gray-600 mb-6">
            Phần này cung cấp hướng dẫn chi tiết về các chức năng dành cho quản trị viên, bao gồm quản lý người dùng, quản lý phòng, quản lý lịch dạy và xếp lịch tự động.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="border rounded-lg p-5">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-users mr-2 text-indigo-600"></i>
                    Quản lý người dùng
                </h3>
                <ul class="text-gray-600 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Thêm, sửa, xóa tài khoản người dùng</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Phân quyền và quản lý vai trò</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Đặt lại mật khẩu khi cần thiết</span>
                    </li>
                </ul>
                <a href="#" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 text-sm">
                    Xem hướng dẫn chi tiết
                </a>
            </div>

            <div class="border rounded-lg p-5">
                <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-building mr-2 text-indigo-600"></i>
                    Quản lý phòng
                </h3>
                <ul class="text-gray-600 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Thêm mới và cập nhật thông tin phòng</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Quản lý trang thiết bị</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Giám sát tình trạng sử dụng</span>
                    </li>
                </ul>
                <a href="#" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 text-sm">
                    Xem hướng dẫn chi tiết
                </a>
            </div>
        </div>

        <a href="#" class="block text-center py-3 px-4 bg-indigo-100 text-indigo-700 font-medium rounded-lg hover:bg-indigo-200 transition duration-300">
            Xem đầy đủ hướng dẫn cho Quản trị viên
        </a>
    </div>

    <!-- Footer Note -->
    <div class="text-center text-gray-500 text-sm">
        <p>© 2025 PDU - PMS | Phát triển bởi Đại học Phương Đông</p>
        <p class="mt-1">Phiên bản hướng dẫn: 1.0 | Cập nhật lần cuối: 22/03/2025</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab navigation
        const tabs = document.querySelectorAll('nav[aria-label="Tabs"] a');
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.className = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm';
                });

                // Add active class to clicked tab
                this.className = 'border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm';

                // Show corresponding content
                const targetId = this.getAttribute('href').substring(1);

                // You would implement showing/hiding content based on the tab here
                // For this example, we'll just scroll to the section
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>