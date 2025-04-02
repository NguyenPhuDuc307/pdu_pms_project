<?php
// Sử dụng đường dẫn tuyệt đối để tránh lỗi
$title = 'Hướng dẫn sử dụng';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/sidebar.php';
?>

<div class="p-6 bg-gray-50">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center p-4">
        <nav class="text-sm">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="/pdu_pms_project/public" class="text-indigo-500 hover:text-indigo-700 font-bold">Trang chủ</a>
                    <svg class="w-3 h-3 mx-2 fill-current text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path>
                    </svg>
                </li>
                <li>
                    <span class="text-purple-600 font-bold">Hướng dẫn sử dụng</span>
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
        <div class="border-b border-gray-200 p-4">
            <ul class="nav nav-pills flex flex-wrap">
                <li class="nav-item">
                    <a class="nav-link active bg-indigo-600 text-white hover:bg-indigo-700 py-3 px-6 rounded-t-lg mr-2" data-target="general" href="#general">Tổng quan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-gray-600 hover:bg-indigo-100 py-3 px-6 rounded-t-lg mr-2" data-target="admin" href="#admin">Quản trị viên</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-gray-600 hover:bg-indigo-100 py-3 px-6 rounded-t-lg mr-2" data-target="teacher" href="#teacher">Giảng viên</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-gray-600 hover:bg-indigo-100 py-3 px-6 rounded-t-lg mr-2" data-target="student" href="#student">Sinh viên</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-gray-600 hover:bg-indigo-100 py-3 px-6 rounded-t-lg" data-target="faq" href="#faq">Câu hỏi thường gặp</a>
                </li>
            </ul>
        </div>
        
        <!-- Tab Contents -->
        <div class="tab-content">
            <!-- General Guide -->
            <div id="general" class="tab-pane active">
                <div class="p-6">
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

            <!-- Admin Guide -->
            <div id="admin" class="tab-pane" style="display: none;">
                <div class="p-6">
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
                        </div>
                    </div>

                    <!-- Detailed Admin Guide -->
                    <div class="space-y-8 mt-8">
                        <!-- User Management Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-users-cog text-indigo-600 mr-2"></i>
                                Quản lý người dùng chi tiết
                            </h3>
                            
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Thêm người dùng mới</h4>
                                <ol class="list-decimal ml-5 text-gray-600 space-y-2">
                                    <li>Truy cập vào menu <span class="font-medium">Quản lý người dùng</span> từ thanh điều hướng.</li>
                                    <li>Nhấp vào nút <span class="font-medium text-indigo-600">+ Thêm người dùng</span> ở góc trên bên phải.</li>
                                    <li>Điền đầy đủ thông tin vào biểu mẫu, bao gồm tên, email, số điện thoại, vai trò, v.v.</li>
                                    <li>Chọn loại tài khoản phù hợp (Admin, Giảng viên, Sinh viên).</li>
                                    <li>Nhấp vào <span class="font-medium">Lưu</span> để tạo tài khoản mới.</li>
                                </ol>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Quản lý phân quyền</h4>
                                <p class="text-gray-600 mb-3">
                                    Hệ thống cho phép quản trị viên tùy chỉnh quyền truy cập cho từng vai trò và người dùng cụ thể:
                                </p>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4">
                                    <h5 class="font-medium text-gray-700 mb-2">Các vai trò mặc định:</h5>
                                    <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                        <li><span class="font-medium">Quản trị viên (Admin):</span> Toàn quyền truy cập và quản lý hệ thống</li>
                                        <li><span class="font-medium">Giảng viên:</span> Quyền đặt phòng, quản lý lịch dạy của bản thân</li>
                                        <li><span class="font-medium">Sinh viên:</span> Quyền xem lịch học, đặt phòng tự học (nếu được phép)</li>
                                    </ul>
                                </div>
                                <p class="text-gray-600">
                                    Để tùy chỉnh quyền cho người dùng cụ thể:
                                </p>
                                <ol class="list-decimal ml-5 text-gray-600 space-y-2 mt-2">
                                    <li>Tìm người dùng trong danh sách và nhấp vào <span class="font-medium">Chỉnh sửa</span>.</li>
                                    <li>Chuyển đến tab <span class="font-medium">Quyền hạn</span>.</li>
                                    <li>Tùy chỉnh các quyền cụ thể cho người dùng đó.</li>
                                    <li>Nhấp vào <span class="font-medium">Lưu thay đổi</span>.</li>
                                </ol>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                                    <div>
                                        <h4 class="font-medium text-blue-700 mb-1">Lưu ý quan trọng:</h4>
                                        <p class="text-gray-600 text-sm">
                                            Khi tạo tài khoản mới, hệ thống sẽ tự động gửi email thông báo với mật khẩu tạm thời cho người dùng. Người dùng sẽ được yêu cầu đổi mật khẩu khi đăng nhập lần đầu tiên. Nếu người dùng không nhận được email, quản trị viên có thể tạo lại mật khẩu tạm thời.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Room Management Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-door-open text-indigo-600 mr-2"></i>
                                Quản lý phòng học
                            </h3>
                            
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Thêm và cấu hình phòng học</h4>
                                <p class="text-gray-600 mb-3">
                                    Để thêm phòng học mới vào hệ thống:
                                </p>
                                <ol class="list-decimal ml-5 text-gray-600 space-y-2">
                                    <li>Truy cập vào menu <span class="font-medium">Quản lý phòng</span> từ thanh điều hướng.</li>
                                    <li>Nhấp vào nút <span class="font-medium text-indigo-600">+ Thêm phòng mới</span>.</li>
                                    <li>Điền thông tin cơ bản: mã phòng, tên phòng, vị trí, sức chứa, v.v.</li>
                                    <li>Cấu hình loại phòng (phòng học, phòng lab, hội trường, v.v.)</li>
                                    <li>Thêm thiết bị có trong phòng từ danh sách hoặc tạo mới.</li>
                                    <li>Cấu hình thời gian sử dụng cho phép (nếu cần).</li>
                                    <li>Nhấp vào <span class="font-medium">Lưu</span> để hoàn tất.</li>
                                </ol>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-medium text-gray-700 mb-2">Quản lý thiết bị</h4>
                                    <p class="text-gray-600 mb-3">
                                        Để quản lý thiết bị trong phòng học:
                                    </p>
                                    <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                        <li>Chọn phòng từ danh sách và nhấp vào <span class="font-medium">Thiết bị</span>.</li>
                                        <li>Thêm thiết bị mới hoặc cập nhật thiết bị hiện có.</li>
                                        <li>Đánh dấu tình trạng thiết bị (hoạt động, bảo trì, hỏng).</li>
                                        <li>Cập nhật lịch bảo trì định kỳ nếu cần.</li>
                                    </ul>
                                </div>
                                
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-medium text-gray-700 mb-2">Giám sát sử dụng</h4>
                                    <p class="text-gray-600 mb-3">
                                        Hệ thống cung cấp các công cụ giám sát sử dụng phòng:
                                    </p>
                                    <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                        <li>Xem báo cáo sử dụng theo thời gian thực.</li>
                                        <li>Theo dõi tỷ lệ sử dụng phòng theo khung giờ, ngày, tuần.</li>
                                        <li>Kiểm tra lịch sử đặt phòng và sử dụng.</li>
                                        <li>Xuất báo cáo định kỳ theo nhiều định dạng.</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                                <div class="flex items-start">
                                    <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-2"></i>
                                    <div>
                                        <h4 class="font-medium text-yellow-700 mb-1">Mẹo quản lý hiệu quả:</h4>
                                        <p class="text-gray-600 text-sm">
                                            Sử dụng tính năng gắn thẻ để phân loại phòng học theo mục đích sử dụng (lý thuyết, thực hành, hội thảo) để dễ dàng tìm kiếm và lọc khi cần. Bạn cũng có thể cấu hình các quy tắc đặt phòng riêng cho từng loại phòng để tối ưu hóa việc sử dụng tài nguyên.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Schedule Management Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>
                                Quản lý lịch và xếp lịch tự động
                            </h3>
                            
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Xếp lịch tự động</h4>
                                <p class="text-gray-600 mb-3">
                                    Hệ thống PDU-PMS cung cấp tính năng xếp lịch tự động giúp tối ưu hóa việc sử dụng phòng học:
                                </p>
                                <ol class="list-decimal ml-5 text-gray-600 space-y-2">
                                    <li>Truy cập vào menu <span class="font-medium">Xếp lịch</span> từ thanh điều hướng.</li>
                                    <li>Chọn học kỳ và năm học cần xếp lịch.</li>
                                    <li>Nhập danh sách các lớp học cần xếp lịch (hoặc nhập từ file Excel).</li>
                                    <li>Cấu hình các ràng buộc (ví dụ: phòng đặc biệt cho một số môn học, thời gian không khả dụng của giảng viên).</li>
                                    <li>Nhấp vào <span class="font-medium">Tạo lịch tự động</span>.</li>
                                    <li>Hệ thống sẽ tính toán và đề xuất lịch tối ưu.</li>
                                    <li>Xem trước, điều chỉnh nếu cần và phê duyệt lịch cuối cùng.</li>
                                    <li>Xuất bản lịch để thông báo cho giảng viên và sinh viên.</li>
                                </ol>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-700 mb-2">Quản lý xung đột lịch</h4>
                                <p class="text-gray-600 mb-3">
                                    Khi xảy ra xung đột lịch, hệ thống sẽ thông báo và cung cấp các tùy chọn giải quyết:
                                </p>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <ul class="ml-5 text-gray-600 space-y-2 list-disc">
                                        <li><span class="font-medium">Phát hiện tự động:</span> Hệ thống sẽ đánh dấu các xung đột tiềm ẩn trong quá trình tạo lịch.</li>
                                        <li><span class="font-medium">Đề xuất thay thế:</span> Đề xuất phòng thay thế hoặc khung giờ thay thế để giải quyết xung đột.</li>
                                        <li><span class="font-medium">Ưu tiên tùy chỉnh:</span> Thiết lập quy tắc ưu tiên cho các lớp hoặc giảng viên cụ thể.</li>
                                        <li><span class="font-medium">Giải quyết thủ công:</span> Cho phép quản trị viên can thiệp và điều chỉnh thủ công khi cần.</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                                <div class="flex items-start">
                                    <i class="fas fa-star text-indigo-500 mt-1 mr-2"></i>
                                    <div>
                                        <h4 class="font-medium text-indigo-700 mb-1">Tính năng nâng cao:</h4>
                                        <p class="text-gray-600 text-sm mb-2">
                                            Thuật toán xếp lịch tiên tiến của PDU-PMS còn có thể:
                                        </p>
                                        <ul class="list-disc ml-4 text-gray-600 text-sm space-y-1">
                                            <li>Tối ưu hóa khoảng cách di chuyển giữa các phòng học cho giảng viên có nhiều tiết liên tiếp</li>
                                            <li>Cân nhắc thiết bị đặc biệt cần thiết cho từng môn học</li>
                                            <li>Phân bổ phòng dựa trên sĩ số lớp và sức chứa phòng</li>
                                            <li>Xem xét thời gian nghỉ giữa các tiết học</li>
                                            <li>Áp dụng các ràng buộc đặc biệt cho các sự kiện hoặc hoạt động ngoại khóa</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                        <a href="#general" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Tổng quan</span>
                        </a>
                        <a href="#teacher" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                            <span>Hướng dẫn cho Giảng viên</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Teacher Guide -->
            <div id="teacher" class="tab-pane" style="display: none;">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Hướng dẫn cho Giảng viên</h2>
                    <p class="text-gray-600 mb-6">
                        Hướng dẫn này cung cấp thông tin chi tiết về cách giảng viên có thể sử dụng hệ thống PDU-PMS để quản lý phòng học, lịch dạy và các tài nguyên giảng dạy khác.
                    </p>

                    <!-- Schedule Viewing Section -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calendar-day text-indigo-600 mr-2"></i>
                            Xem lịch giảng dạy
                        </h3>
                        
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Truy cập lịch giảng dạy</h4>
                            <p class="text-gray-600 mb-3">
                                Để xem lịch giảng dạy của bạn:
                            </p>
                            <ol class="list-decimal ml-5 text-gray-600 space-y-2">
                                <li>Đăng nhập vào hệ thống với tài khoản giảng viên.</li>
                                <li>Trên trang Dashboard, bạn sẽ thấy lịch giảng dạy của tuần hiện tại.</li>
                                <li>Sử dụng các điều khiển để chuyển đổi giữa chế độ xem ngày, tuần hoặc tháng.</li>
                                <li>Nhấp vào mũi tên điều hướng để xem lịch của các tuần khác.</li>
                            </ol>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-700 mb-2">Cá nhân hóa chế độ xem lịch</h4>
                                <p class="text-gray-600 mb-3">
                                    Bạn có thể tùy chỉnh chế độ xem lịch theo nhu cầu:
                                </p>
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li>Lọc theo loại lớp học hoặc môn học.</li>
                                    <li>Đánh dấu màu cho các loại lớp học khác nhau.</li>
                                    <li>Hiển thị/ẩn thông tin chi tiết (sĩ số, phòng học, v.v.).</li>
                                    <li>Xuất lịch ra file Excel hoặc PDF để sử dụng ngoại tuyến.</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-700 mb-2">Tích hợp với ứng dụng lịch cá nhân</h4>
                                <p class="text-gray-600 mb-3">
                                    Đồng bộ hóa lịch giảng dạy với các ứng dụng lịch cá nhân:
                                </p>
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li>Tạo URL đồng bộ iCal để thêm vào Google Calendar, Outlook, v.v.</li>
                                    <li>Nhận thông báo tự động về thay đổi lịch.</li>
                                    <li>Thiết lập nhắc nhở trước khi bắt đầu lớp học.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room Booking Section -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-door-open text-indigo-600 mr-2"></i>
                            Đặt phòng học
                        </h3>
                        
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Quy trình đặt phòng</h4>
                            <p class="text-gray-600 mb-3">
                                Giảng viên có thể đặt phòng học để giảng dạy hoặc tổ chức các hoạt động khác:
                            </p>
                            <ol class="list-decimal ml-5 text-gray-600 space-y-2">
                                <li>Truy cập vào chức năng <span class="font-medium">Đặt phòng</span> từ menu chính.</li>
                                <li>Chọn ngày và khung giờ mong muốn.</li>
                                <li>Hệ thống sẽ hiển thị các phòng khả dụng trong thời gian đã chọn.</li>
                                <li>Lọc phòng theo các tiêu chí như sức chứa, thiết bị, v.v.</li>
                                <li>Chọn phòng phù hợp và nhấp vào <span class="font-medium">Đặt phòng</span>.</li>
                                <li>Nhập thông tin mục đích sử dụng, số lượng người tham dự, và yêu cầu thiết bị đặc biệt (nếu có).</li>
                                <li>Xác nhận đặt phòng.</li>
                            </ol>
                        </div>
                        
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Quản lý đặt phòng</h4>
                            <p class="text-gray-600 mb-3">
                                Sau khi đặt phòng, bạn có thể:
                            </p>
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li>Xem tất cả các đặt phòng hiện tại và lịch sử đặt phòng.</li>
                                    <li>Chỉnh sửa thông tin đặt phòng (nếu chưa bắt đầu sử dụng).</li>
                                    <li>Hủy đặt phòng nếu không còn nhu cầu sử dụng.</li>
                                    <li>Lặp lại đặt phòng cho các sự kiện định kỳ.</li>
                                    <li>Nhận thông báo xác nhận và nhắc nhở qua email hoặc SMS.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                            <div class="flex items-start">
                                <i class="fas fa-leaf text-green-500 mt-1 mr-2"></i>
                                <div>
                                    <h4 class="font-medium text-green-700 mb-1">Thực hành tốt:</h4>
                                    <p class="text-gray-600 text-sm mb-2">
                                        Để đảm bảo hiệu quả sử dụng tài nguyên phòng học:
                                    </p>
                                    <ul class="list-disc ml-4 text-gray-600 text-sm space-y-1">
                                        <li>Đặt phòng với sức chứa phù hợp với số lượng người tham dự.</li>
                                        <li>Hủy đặt phòng càng sớm càng tốt nếu không sử dụng.</li>
                                        <li>Chọn phòng có trang thiết bị cần thiết thay vì yêu cầu thiết bị bổ sung.</li>
                                        <li>Tuân thủ thời gian bắt đầu và kết thúc để không ảnh hưởng đến người sử dụng tiếp theo.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Teaching Resources Section -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-book text-indigo-600 mr-2"></i>
                            Quản lý tài nguyên giảng dạy
                        </h3>
                        
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Tài liệu giảng dạy</h4>
                            <p class="text-gray-600 mb-3">
                                PDU-PMS cho phép giảng viên quản lý và chia sẻ tài liệu giảng dạy:
                            </p>
                            <ul class="ml-5 text-gray-600 space-y-2 list-disc">
                                <li>Tải lên tài liệu giảng dạy và liên kết với môn học.</li>
                                <li>Tạo thư viện tài liệu cá nhân để sử dụng lại trong các học kỳ khác.</li>
                                <li>Chia sẻ tài liệu với sinh viên hoặc đồng nghiệp.</li>
                                <li>Quản lý phiên bản của tài liệu giảng dạy.</li>
                            </ul>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-700 mb-2">Yêu cầu thiết bị đặc biệt</h4>
                                <p class="text-gray-600 mb-3">
                                    Nếu bạn cần thiết bị đặc biệt cho buổi giảng:
                                </p>
                                <ol class="list-decimal ml-5 text-gray-600 space-y-1">
                                    <li>Truy cập vào chức năng <span class="font-medium">Yêu cầu thiết bị</span>.</li>
                                    <li>Chọn buổi dạy cần thiết bị.</li>
                                    <li>Liệt kê các thiết bị cần thiết và lý do.</li>
                                    <li>Gửi yêu cầu để quản trị viên xem xét.</li>
                                    <li>Theo dõi trạng thái yêu cầu và nhận thông báo khi được phê duyệt.</li>
                                </ol>
                            </div>
                            
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-700 mb-2">Báo cáo sự cố</h4>
                                <p class="text-gray-600 mb-3">
                                    Nếu gặp vấn đề với phòng học hoặc thiết bị:
                                </p>
                                <ol class="list-decimal ml-5 text-gray-600 space-y-1">
                                    <li>Sử dụng chức năng <span class="font-medium">Báo cáo sự cố</span> trong ứng dụng.</li>
                                    <li>Chọn phòng học và thiết bị gặp vấn đề.</li>
                                    <li>Mô tả chi tiết sự cố và cung cấp hình ảnh nếu có thể.</li>
                                    <li>Gửi báo cáo để đội kỹ thuật xử lý.</li>
                                    <li>Theo dõi trạng thái xử lý sự cố.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                        <a href="#admin" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Hướng dẫn cho Quản trị viên</span>
                        </a>
                        <a href="#student" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                            <span>Hướng dẫn cho Sinh viên</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Student Guide -->
            <div id="student" class="tab-pane" style="display: none;">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Hướng dẫn cho Sinh viên</h2>
                    <p class="text-gray-600 mb-6">
                        Phần này hướng dẫn sinh viên cách sử dụng hệ thống PDU-PMS để xem lịch học, đặt phòng tự học và các tính năng khác dành cho sinh viên.
                    </p>

                    <!-- Schedule Viewing Section -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-clock text-indigo-600 mr-2"></i>
                            Xem lịch học
                        </h3>
                        
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Truy cập lịch học</h4>
                            <p class="text-gray-600 mb-3">
                                Để xem lịch học của bạn:
                            </p>
                            <ol class="list-decimal ml-5 text-gray-600 space-y-2">
                                <li>Đăng nhập vào hệ thống với tài khoản sinh viên.</li>
                                <li>Trên trang Dashboard, bạn sẽ thấy lịch học của tuần hiện tại.</li>
                                <li>Sử dụng các điều khiển để chuyển đổi giữa chế độ xem ngày, tuần hoặc tháng.</li>
                                <li>Lọc lịch học theo môn học hoặc giảng viên nếu cần.</li>
                            </ol>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-700 mb-2">Thông tin chi tiết về lớp học</h4>
                                <p class="text-gray-600 mb-3">
                                    Nhấp vào một lớp học trong lịch để xem thông tin chi tiết:
                                </p>
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li>Tên môn học và mã môn học</li>
                                    <li>Giảng viên phụ trách</li>
                                    <li>Phòng học và thời gian</li>
                                    <li>Thông tin về tài liệu học tập</li>
                                    <li>Thông báo đặc biệt từ giảng viên</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-700 mb-2">Đồng bộ hóa với ứng dụng cá nhân</h4>
                                <p class="text-gray-600 mb-3">
                                    Đồng bộ lịch học với ứng dụng lịch cá nhân:
                                </p>
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li>Nhấp vào nút <span class="font-medium">Xuất lịch</span> trên màn hình lịch.</li>
                                    <li>Chọn định dạng xuất (iCal, Google Calendar, v.v.).</li>
                                    <li>Thiết lập thông báo trước khi bắt đầu lớp học.</li>
                                    <li>Lịch sẽ tự động cập nhật khi có thay đổi.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                                <div>
                                    <h4 class="font-medium text-blue-700 mb-1">Lưu ý:</h4>
                                    <p class="text-gray-600 text-sm">
                                        Lịch học có thể thay đổi vào phút chót do nhiều yếu tố. Sinh viên nên kiểm tra lịch học hàng ngày và bật thông báo để nhận cập nhật về các thay đổi lịch học. Các thay đổi sẽ được đánh dấu màu đỏ trong lịch.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Self-Study Room Booking -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-book-reader text-indigo-600 mr-2"></i>
                            Đặt phòng tự học
                        </h3>
                        
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Quy trình đặt phòng tự học</h4>
                            <p class="text-gray-600 mb-3">
                                Sinh viên có thể đặt phòng tự học hoặc làm việc nhóm:
                            </p>
                            <ol class="list-decimal ml-5 text-gray-600 space-y-2">
                                <li>Truy cập vào chức năng <span class="font-medium">Đặt phòng tự học</span> từ menu chính.</li>
                                <li>Chọn ngày và khung giờ mong muốn.</li>
                                <li>Xem danh sách các phòng khả dụng trong thời gian đã chọn.</li>
                                <li>Lọc theo nhu cầu (phòng yên tĩnh, phòng làm việc nhóm, v.v.).</li>
                                <li>Chọn phòng và nhấp vào <span class="font-medium">Đặt phòng</span>.</li>
                                <li>Điền thông tin mục đích sử dụng và số người tham gia.</li>
                                <li>Xác nhận đặt phòng.</li>
                            </ol>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-700 mb-2">Các loại phòng tự học</h4>
                                <p class="text-gray-600 mb-3">
                                    Hệ thống cung cấp nhiều loại phòng khác nhau:
                                </p>
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li><span class="font-medium">Phòng yên tĩnh:</span> Dành cho học tập cá nhân, không gian yên tĩnh</li>
                                    <li><span class="font-medium">Phòng thảo luận:</span> Dành cho nhóm nhỏ làm việc cùng nhau</li>
                                    <li><span class="font-medium">Phòng dự án:</span> Trang bị bảng lớn và thiết bị hỗ trợ làm việc nhóm</li>
                                    <li><span class="font-medium">Phòng máy tính:</span> Có máy tính và phần mềm chuyên dụng</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-700 mb-2">Quản lý đặt phòng</h4>
                                <p class="text-gray-600 mb-3">
                                    Sau khi đặt phòng, bạn có thể:
                                </p>
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li>Xem tất cả các đặt phòng hiện tại và lịch sử.</li>
                                    <li>Chỉnh sửa thông tin đặt phòng nếu cần.</li>
                                    <li>Hủy đặt phòng nếu không sử dụng.</li>
                                    <li>Gia hạn thời gian sử dụng (nếu phòng vẫn trống sau đó).</li>
                                    <li>Đánh giá phòng học sau khi sử dụng.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-2"></i>
                                <div>
                                    <h4 class="font-medium text-yellow-700 mb-1">Quy định đặt phòng:</h4>
                                    <ul class="list-disc ml-4 text-gray-600 text-sm space-y-1">
                                        <li>Mỗi sinh viên được đặt tối đa 2 giờ mỗi ngày cho phòng tự học.</li>
                                        <li>Đặt phòng nhóm yêu cầu có ít nhất 3 sinh viên tham gia.</li>
                                        <li>Không sử dụng phòng sẽ bị ghi nhận và có thể bị hạn chế quyền đặt phòng.</li>
                                        <li>Không được mang thức ăn vào phòng học (chỉ được mang đồ uống có nắp).</li>
                                        <li>Báo cáo ngay nếu phát hiện bất kỳ vấn đề nào với phòng hoặc thiết bị.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Learning Resources -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-laptop-code text-indigo-600 mr-2"></i>
                            Tài nguyên học tập
                        </h3>
                        
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Truy cập tài liệu học tập</h4>
                            <p class="text-gray-600 mb-3">
                                PDU-PMS cho phép sinh viên truy cập các tài liệu học tập:
                            </p>
                            <ol class="list-decimal ml-5 text-gray-600 space-y-2">
                                <li>Trong lịch học, nhấp vào một lớp học cụ thể.</li>
                                <li>Chuyển đến tab <span class="font-medium">Tài liệu</span>.</li>
                                <li>Xem và tải xuống các tài liệu được giảng viên chia sẻ.</li>
                                <li>Tài liệu được phân loại theo loại (bài giảng, bài tập, tài liệu tham khảo).</li>
                            </ol>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Các tính năng học tập khác</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li>Diễn đàn thảo luận cho các môn học</li>
                                    <li>Lịch nộp bài tập và nhắc nhở</li>
                                    <li>Đánh dấu và ghi chú trên tài liệu</li>
                                    <li>Chia sẻ ghi chú với bạn học</li>
                                </ul>
                                <ul class="ml-5 text-gray-600 space-y-1 list-disc">
                                    <li>Tra cứu tài liệu tham khảo</li>
                                    <li>Đánh giá và phản hồi về tài liệu học tập</li>
                                    <li>Tạo thư viện tài liệu cá nhân</li>
                                    <li>Theo dõi tiến độ học tập</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                            <div class="flex items-start">
                                <i class="fas fa-lightbulb text-green-500 mt-1 mr-2"></i>
                                <div>
                                    <h4 class="font-medium text-green-700 mb-1">Mẹo học tập hiệu quả:</h4>
                                    <p class="text-gray-600 text-sm mb-2">
                                        Tận dụng tối đa hệ thống PDU-PMS để nâng cao trải nghiệm học tập của bạn:
                                    </p>
                                    <ul class="list-disc ml-4 text-gray-600 text-sm space-y-1">
                                        <li>Thiết lập thông báo nhắc nhở lịch học và thời hạn nộp bài.</li>
                                        <li>Đặt phòng tự học trước các kỳ thi ít nhất một tuần để đảm bảo có chỗ.</li>
                                        <li>Tạo nhóm học tập và chia sẻ lịch với nhóm để dễ dàng sắp xếp thời gian học chung.</li>
                                        <li>Sử dụng tính năng ghi chú trực tuyến để đồng bộ ghi chú giữa các thiết bị.</li>
                                        <li>Cung cấp phản hồi về tài liệu học tập để giúp giảng viên cải thiện chất lượng.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                        <a href="#teacher" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Hướng dẫn cho Giảng viên</span>
                        </a>
                        <a href="#faq" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                            <span>Câu hỏi thường gặp</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div id="faq" class="tab-pane" style="display: none;">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Câu hỏi thường gặp</h2>
                    <p class="text-gray-600 mb-6">
                        Dưới đây là những câu hỏi thường gặp về hệ thống PDU-PMS. Nếu bạn không tìm thấy câu trả lời cho câu hỏi của mình, vui lòng liên hệ với bộ phận hỗ trợ.
                    </p>

                    <!-- Accordion FAQ List -->
                    <div class="space-y-4">
                        <!-- General Questions -->
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <div class="p-4 bg-indigo-50 border-l-4 border-indigo-500">
                                <h3 class="text-lg font-semibold text-gray-800">Câu hỏi chung</h3>
                            </div>
                            <div class="p-5 border border-gray-200 border-t-0 rounded-b-lg">
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-indigo-500 mr-2 mt-1"></i>
                                            Làm thế nào để đặt lại mật khẩu khi quên?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Để đặt lại mật khẩu, nhấp vào liên kết "Quên mật khẩu" trên trang đăng nhập. Nhập email đã đăng ký của bạn, và hệ thống sẽ gửi hướng dẫn đặt lại mật khẩu qua email. Nếu bạn không nhận được email, hãy kiểm tra thư mục spam hoặc liên hệ với quản trị viên hệ thống.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-indigo-500 mr-2 mt-1"></i>
                                            Tôi có thể truy cập hệ thống từ thiết bị di động không?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Có, PDU-PMS được thiết kế để hoạt động trên tất cả các thiết bị, bao gồm điện thoại thông minh và máy tính bảng. Bạn có thể truy cập hệ thống thông qua trình duyệt web trên thiết bị di động hoặc tải xuống ứng dụng PDU-PMS từ App Store hoặc Google Play Store.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-indigo-500 mr-2 mt-1"></i>
                                            Làm thế nào để cập nhật thông tin cá nhân của tôi?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Để cập nhật thông tin cá nhân, đăng nhập vào hệ thống và nhấp vào tên người dùng ở góc trên bên phải. Chọn "Thông tin cá nhân" từ menu thả xuống. Tại đây, bạn có thể chỉnh sửa thông tin liên hệ, đổi mật khẩu, và cập nhật các tùy chọn thông báo. Sau khi thực hiện các thay đổi, hãy nhớ nhấp vào "Lưu thay đổi".
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Room Booking Questions -->
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <div class="p-4 bg-purple-50 border-l-4 border-purple-500">
                                <h3 class="text-lg font-semibold text-gray-800">Đặt phòng</h3>
                            </div>
                            <div class="p-5 border border-gray-200 border-t-0 rounded-b-lg">
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-purple-500 mr-2 mt-1"></i>
                                            Tôi có thể đặt phòng trước bao lâu?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Giảng viên có thể đặt phòng trước tối đa 3 tháng. Sinh viên có thể đặt phòng tự học trước tối đa 2 tuần. Việc đặt phòng cho các sự kiện đặc biệt (hội thảo, hội nghị) có thể được thực hiện trước tối đa 6 tháng, nhưng cần có sự phê duyệt của quản trị viên.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-purple-500 mr-2 mt-1"></i>
                                            Làm thế nào để hủy đặt phòng?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Để hủy đặt phòng, truy cập vào mục "Quản lý đặt phòng" từ menu chính. Tìm đặt phòng bạn muốn hủy và nhấp vào nút "Hủy". Bạn sẽ được yêu cầu xác nhận và cung cấp lý do hủy. Lưu ý rằng việc hủy phòng trước thời điểm sử dụng ít hơn 24 giờ có thể ảnh hưởng đến quyền đặt phòng trong tương lai.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-purple-500 mr-2 mt-1"></i>
                                            Tôi có thể xem phòng trước khi đặt không?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Có, hệ thống cung cấp hình ảnh và thông tin chi tiết về mỗi phòng. Khi xem danh sách phòng khả dụng, bạn có thể nhấp vào "Xem chi tiết" để xem hình ảnh, sơ đồ phòng, danh sách thiết bị, và đánh giá từ người dùng trước đây. Nếu bạn muốn xem phòng trực tiếp, hãy liên hệ với bộ phận quản lý phòng để sắp xếp thời gian phù hợp.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Technical Issues -->
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <div class="p-4 bg-blue-50 border-l-4 border-blue-500">
                                <h3 class="text-lg font-semibold text-gray-800">Vấn đề kỹ thuật</h3>
                            </div>
                            <div class="p-5 border border-gray-200 border-t-0 rounded-b-lg">
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-blue-500 mr-2 mt-1"></i>
                                            Làm thế nào để báo cáo sự cố kỹ thuật?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Để báo cáo sự cố kỹ thuật, nhấp vào biểu tượng "Hỗ trợ" ở góc dưới bên phải của màn hình. Chọn "Báo cáo sự cố" và điền vào mẫu với mô tả chi tiết về vấn đề. Cung cấp ảnh chụp màn hình nếu có thể. Bạn cũng có thể liên hệ trực tiếp với đội hỗ trợ kỹ thuật qua email support@pdu-pms.edu.vn hoặc gọi điện thoại đến số 0123-456-789 trong giờ làm việc.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-blue-500 mr-2 mt-1"></i>
                                            Tôi không thể đăng nhập vào hệ thống, phải làm gì?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Nếu bạn không thể đăng nhập, hãy kiểm tra các vấn đề sau:
                                            </p>
                                            <ul class="list-disc ml-5 text-gray-600 space-y-1 mt-2">
                                                <li>Đảm bảo bạn đang sử dụng tên người dùng và mật khẩu chính xác</li>
                                                <li>Kiểm tra xem Caps Lock có bật không</li>
                                                <li>Xóa cache và cookie trình duyệt, sau đó thử lại</li>
                                                <li>Thử trình duyệt khác hoặc thiết bị khác</li>
                                                <li>Nếu vẫn không được, sử dụng tính năng "Quên mật khẩu" hoặc liên hệ với hỗ trợ kỹ thuật</li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-gray-800 mb-2 flex items-start">
                                            <i class="fas fa-question-circle text-blue-500 mr-2 mt-1"></i>
                                            Hệ thống có bảo trì theo lịch không?
                                        </h4>
                                        <div class="pl-8">
                                            <p class="text-gray-600">
                                                Có, hệ thống PDU-PMS có lịch bảo trì định kỳ vào ngày Chủ nhật cuối cùng của mỗi tháng, từ 23:00 đến 02:00 sáng hôm sau. Trong thời gian này, hệ thống có thể không khả dụng hoặc hoạt động chậm. Thông báo bảo trì sẽ được gửi qua email và hiển thị trên hệ thống trước ít nhất 3 ngày. Các bảo trì khẩn cấp sẽ được thông báo càng sớm càng tốt.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Help -->
                        <div class="bg-gray-100 p-5 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Cần thêm trợ giúp?</h3>
                            <p class="text-gray-600 mb-4">
                                Nếu bạn không tìm thấy câu trả lời cho câu hỏi của mình, hãy liên hệ với chúng tôi qua các kênh sau:
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-envelope text-indigo-600 mr-2"></i>
                                        <h4 class="font-medium text-gray-700">Email Hỗ trợ</h4>
                                    </div>
                                    <p class="text-gray-600 text-sm">
                                        support@pdu-pms.edu.vn<br>
                                        Thời gian phản hồi: 24 giờ
                                    </p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-phone-alt text-indigo-600 mr-2"></i>
                                        <h4 class="font-medium text-gray-700">Hỗ trợ Điện thoại</h4>
                                    </div>
                                    <p class="text-gray-600 text-sm">
                                        0123-456-789<br>
                                        Thứ Hai - Thứ Sáu: 8:00 - 17:00
                                    </p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-comment-dots text-indigo-600 mr-2"></i>
                                        <h4 class="font-medium text-gray-700">Trò chuyện Trực tuyến</h4>
                                    </div>
                                    <p class="text-gray-600 text-sm">
                                        Có sẵn trong hệ thống<br>
                                        Thứ Hai - Thứ Bảy: 8:00 - 20:00
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                        <a href="#student" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Hướng dẫn cho Sinh viên</span>
                        </a>
                        <a href="#general" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                            <span>Quay lại Tổng quan</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Note -->
    <div class="text-center text-gray-500 text-sm">
        <p>© 2025 PDU - PMS | Phát triển bởi Đại học Phương Đông</p>
        <p class="mt-1">Phiên bản hướng dẫn: 1.0 | Cập nhật lần cuối: 22/03/2025</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM loaded!");
        
        // Lấy tất cả các tab
        const tabLinks = document.querySelectorAll('.nav-pills .nav-link');
        console.log("Tab links found:", tabLinks.length);
        
        // Lấy tất cả các tab-pane
        const tabPanes = document.querySelectorAll('.tab-pane');
        console.log("Tab panes found:", tabPanes.length);
        
        // Hàm ẩn tất cả các tab-pane
        function hideAllTabPanes() {
            tabPanes.forEach(pane => {
                pane.style.display = 'none';
                pane.classList.remove('active');
            });
        }
        
        // Hàm hiển thị tab-pane theo ID
        function showTabPane(id) {
            const targetPane = document.getElementById(id);
            if (targetPane) {
                targetPane.style.display = 'block';
                targetPane.classList.add('active');
                console.log(`Showing tab pane: ${id}`);
            } else {
                console.error(`Tab pane ${id} not found`);
            }
        }
        
        // Ẩn tất cả tab-pane và hiển thị tab 'general' khi trang tải
        hideAllTabPanes();
        showTabPane('general');
        
        // Xử lý sự kiện click tab
        tabLinks.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Reset active state từ tất cả tab links
                tabLinks.forEach(link => {
                    link.classList.remove('active', 'bg-indigo-600', 'text-white');
                    link.classList.add('text-gray-600', 'hover:bg-indigo-100');
                });
                
                // Set active state cho tab được chọn
                this.classList.add('active', 'bg-indigo-600', 'text-white');
                this.classList.remove('text-gray-600', 'hover:bg-indigo-100');
                
                // Lấy target tab ID từ data-target hoặc href
                const targetId = this.getAttribute('data-target') || this.getAttribute('href').substring(1);
                console.log(`Activating tab: ${targetId}`);
                
                // Ẩn tất cả tab-pane và hiển thị tab được chọn
                hideAllTabPanes();
                showTabPane(targetId);
            });
        });
        
        // Xử lý các liên kết trong trang (Next, Previous buttons)
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            if (!link.closest('.nav-pills')) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    
                    // Tìm và kích hoạt tab tương ứng
                    let foundTab = false;
                    tabLinks.forEach(tab => {
                        const tabTarget = tab.getAttribute('data-target') || tab.getAttribute('href').substring(1);
                        if (tabTarget === targetId) {
                            tab.click();
                            foundTab = true;
                        }
                    });
                    
                    if (!foundTab) {
                        console.warn(`No tab found for ${targetId}`);
                    }
                });
            }
        });
        
        // Xử lý URL hash khi tải trang
        const currentHash = window.location.hash;
        if (currentHash) {
            const hashId = currentHash.substring(1);
            console.log(`Initial hash: ${hashId}`);
            
            // Tìm và kích hoạt tab tương ứng với hash
            let foundTab = false;
            tabLinks.forEach(tab => {
                const tabTarget = tab.getAttribute('data-target') || tab.getAttribute('href').substring(1);
                if (tabTarget === hashId) {
                    // Delay nhỏ để đảm bảo trang đã tải hoàn toàn
                    setTimeout(() => tab.click(), 100);
                    foundTab = true;
                }
            });
            
            if (!foundTab) {
                console.warn(`No tab found for initial hash: ${hashId}`);
            }
        }
    });
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>