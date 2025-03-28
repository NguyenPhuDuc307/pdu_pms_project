# Yêu Cầu Hệ Thống PDU PMS (Hệ Thống Quản Lý Phòng Đào Tạo)

## Tổng Quan

PDU PMS là hệ thống quản lý phòng học và tài nguyên toàn diện được thiết kế cho các tổ chức giáo dục. Hệ thống cho phép quản trị viên, giáo viên và sinh viên quản lý và đặt phòng dựa trên thời khóa biểu và tình trạng sẵn có.

## Yêu Cầu Hệ Thống

### Yêu Cầu Kỹ Thuật

- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Máy chủ web (Apache/Nginx)
- PDO PHP Extension
- Trình duyệt web hiện đại có hỗ trợ JavaScript

### Cấu Hình Cơ Sở Dữ Liệu

- Host: localhost
- Tên Database: pdu_pms
- Tên người dùng: root
- Cấu hình mật khẩu mặc định (có thể được sửa đổi trong Config/Database.php)

## Vai Trò Người Dùng và Quyền Truy Cập

### Quản Trị Viên

- Quản lý người dùng (thêm, sửa, xóa)
- Quản lý phòng học (thêm, sửa, xóa)
- Quản lý thời khóa biểu (thêm, sửa, xóa)
- Quản lý đặt phòng (thêm, sửa, xóa)
- Tự động sắp xếp phòng học dựa trên thời khóa biểu
- Xem bảng điều khiển với thống kê hệ thống

### Giáo Viên

- Xem phòng học có sẵn
- Đặt phòng cho lớp/buổi học
- Quản lý đặt phòng cá nhân
- Xem thời khóa biểu

### Sinh Viên

- Xem phòng học có sẵn
- Đặt phòng cho buổi học nhóm
- Xem thời khóa biểu

## Use Cases (Trường Hợp Sử Dụng)

### Use Cases Xác Thực

1. **Đăng Nhập**

   - **Tác nhân:** Quản trị viên, Giáo viên, Sinh viên
   - **Mô tả:** Người dùng đăng nhập vào hệ thống bằng tên đăng nhập và mật khẩu
   - **Luồng chính:**
     1. Người dùng truy cập trang đăng nhập
     2. Điền tên đăng nhập và mật khẩu
     3. Hệ thống xác thực thông tin
     4. Chuyển hướng đến trang chính tương ứng với vai trò

2. **Đăng Ký**

   - **Tác nhân:** Người dùng mới
   - **Mô tả:** Người dùng tạo tài khoản mới trong hệ thống
   - **Luồng chính:**
     1. Người dùng truy cập trang đăng ký
     2. Điền thông tin cá nhân, tên đăng nhập, mật khẩu, vai trò
     3. Hệ thống xác thực và lưu thông tin
     4. Chuyển hướng đến trang đăng nhập

3. **Đăng Xuất**
   - **Tác nhân:** Người dùng đã đăng nhập
   - **Mô tả:** Người dùng đăng xuất khỏi hệ thống
   - **Luồng chính:**
     1. Người dùng nhấn nút đăng xuất
     2. Hệ thống xóa phiên đăng nhập
     3. Chuyển hướng đến trang đăng nhập

### Use Cases Quản Trị Viên

1. **Quản Lý Người Dùng**

   - **Tác nhân:** Quản trị viên
   - **Mô tả:** Quản lý thông tin người dùng trong hệ thống
   - **Luồng chính:**
     1. Quản trị viên truy cập trang quản lý người dùng
     2. Xem danh sách người dùng hiện có
     3. Thêm người dùng mới
     4. Chỉnh sửa thông tin người dùng
     5. Xóa người dùng khỏi hệ thống

2. **Quản Lý Phòng Học**

   - **Tác nhân:** Quản trị viên
   - **Mô tả:** Quản lý thông tin phòng học
   - **Luồng chính:**
     1. Quản trị viên truy cập trang quản lý phòng học
     2. Xem danh sách phòng học hiện có
     3. Thêm phòng học mới
     4. Chỉnh sửa thông tin phòng học
     5. Xóa phòng học khỏi hệ thống

3. **Quản Lý Thời Khóa Biểu**

   - **Tác nhân:** Quản trị viên
   - **Mô tả:** Tạo và quản lý thời khóa biểu
   - **Luồng chính:**
     1. Quản trị viên truy cập trang quản lý thời khóa biểu
     2. Xem danh sách thời khóa biểu hiện có
     3. Tạo thời khóa biểu mới
     4. Chỉnh sửa thông tin thời khóa biểu
     5. Xóa thời khóa biểu khỏi hệ thống

4. **Tự Động Sắp Xếp Phòng Học**

   - **Tác nhân:** Quản trị viên
   - **Mô tả:** Hệ thống tự động phân bổ phòng học dựa trên thời khóa biểu
   - **Luồng chính:**
     1. Quản trị viên truy cập chức năng tự động sắp xếp
     2. Chọn thời khóa biểu cần sắp xếp
     3. Hệ thống tự động phân tích và gợi ý phòng học phù hợp
     4. Quản trị viên xác nhận hoặc điều chỉnh gợi ý
     5. Hệ thống cập nhật và lưu thông tin

5. **Xem Thống Kê Hệ Thống**
   - **Tác nhân:** Quản trị viên
   - **Mô tả:** Xem các thống kê về việc sử dụng phòng học
   - **Luồng chính:**
     1. Quản trị viên truy cập trang thống kê
     2. Xem thông tin về tần suất sử dụng phòng học
     3. Xem thống kê về đặt phòng theo thời gian
     4. Xem báo cáo tổng hợp về hoạt động của hệ thống

### Use Cases Giáo Viên

1. **Xem Phòng Học Trống**

   - **Tác nhân:** Giáo viên
   - **Mô tả:** Giáo viên xem danh sách phòng học có sẵn
   - **Luồng chính:**
     1. Giáo viên truy cập trang phòng học
     2. Chọn ngày và khung giờ cần tìm
     3. Hệ thống hiển thị danh sách phòng học trống

2. **Đặt Phòng Học**

   - **Tác nhân:** Giáo viên
   - **Mô tả:** Giáo viên đặt phòng học cho lớp/buổi dạy
   - **Luồng chính:**
     1. Giáo viên truy cập trang đặt phòng
     2. Chọn phòng học trống
     3. Nhập thông tin về thời gian và mục đích sử dụng
     4. Hệ thống xác nhận và lưu thông tin đặt phòng

3. **Quản Lý Đặt Phòng Cá Nhân**

   - **Tác nhân:** Giáo viên
   - **Mô tả:** Giáo viên quản lý các yêu cầu đặt phòng của mình
   - **Luồng chính:**
     1. Giáo viên truy cập trang quản lý đặt phòng
     2. Xem danh sách các yêu cầu đặt phòng
     3. Chỉnh sửa hoặc hủy yêu cầu đặt phòng

4. **Xem Thời Khóa Biểu Cá Nhân**
   - **Tác nhân:** Giáo viên
   - **Mô tả:** Giáo viên xem thời khóa biểu cá nhân
   - **Luồng chính:**
     1. Giáo viên truy cập trang thời khóa biểu
     2. Xem thông tin về các lớp học và phòng học được phân công

### Use Cases Sinh Viên

1. **Xem Phòng Học Trống**

   - **Tác nhân:** Sinh viên
   - **Mô tả:** Sinh viên xem danh sách phòng học có sẵn
   - **Luồng chính:**
     1. Sinh viên truy cập trang phòng học
     2. Chọn ngày và khung giờ cần tìm
     3. Hệ thống hiển thị danh sách phòng học trống

2. **Đặt Phòng Học Nhóm**

   - **Tác nhân:** Sinh viên
   - **Mô tả:** Sinh viên đặt phòng học cho nhóm học tập
   - **Luồng chính:**
     1. Sinh viên truy cập trang đặt phòng
     2. Chọn phòng học trống
     3. Nhập thông tin về thời gian, số người tham gia và mục đích sử dụng
     4. Hệ thống xác nhận và gửi yêu cầu đặt phòng

3. **Quản Lý Đặt Phòng Cá Nhân**

   - **Tác nhân:** Sinh viên
   - **Mô tả:** Sinh viên quản lý các yêu cầu đặt phòng của mình
   - **Luồng chính:**
     1. Sinh viên truy cập trang quản lý đặt phòng
     2. Xem danh sách các yêu cầu đặt phòng
     3. Chỉnh sửa hoặc hủy yêu cầu đặt phòng

4. **Xem Thời Khóa Biểu Lớp**
   - **Tác nhân:** Sinh viên
   - **Mô tả:** Sinh viên xem thời khóa biểu của lớp
   - **Luồng chính:**
     1. Sinh viên truy cập trang thời khóa biểu
     2. Xem thông tin về các môn học, giáo viên và phòng học

## Tính Năng Chính

### Hệ Thống Xác Thực

- Đăng nhập người dùng với tên đăng nhập và mật khẩu
- Đăng ký người dùng với lựa chọn vai trò
- Quản lý phiên làm việc
- Mã hóa mật khẩu sử dụng password_hash của PHP

### Quản Lý Phòng Học

- Thêm phòng học mới với thông tin sức chứa
- Cập nhật thông tin phòng (tên, sức chứa, trạng thái)
- Xóa phòng học
- Xem thống kê phòng học (phòng được sử dụng nhiều nhất)
- Theo dõi trạng thái phòng (trống/đã sử dụng)

### Quản Lý Thời Khóa Biểu

- Tạo thời khóa biểu với các khung giờ
- Phân công phòng cho thời khóa biểu
- Xem thời khóa biểu theo phòng, giáo viên hoặc lớp
- Tối ưu hóa lịch trình

### Hệ Thống Đặt Phòng

- Đặt phòng cho khoảng thời gian cụ thể
- Kiểm tra tình trạng phòng
- Hủy đặt phòng
- Tính năng tự động sắp xếp để phân bổ phòng tối ưu

## Mô Hình Dữ Liệu

### Người Dùng

- id (khóa chính)
- username (tên đăng nhập)
- email
- password (mật khẩu đã được mã hóa)
- full_name (họ tên đầy đủ)
- role (vai trò: admin/teacher/student)
- class_code (mã lớp, dành cho sinh viên)

### Phòng Học

- id (khóa chính)
- name (số phòng)
- capacity (sức chứa)
- status (trạng thái: trống/đã sử dụng)

### Thời Khóa Biểu

- id (khóa chính)
- day_of_week (ngày trong tuần)
- start_time (thời gian bắt đầu)
- end_time (thời gian kết thúc)
- subject (môn học)
- teacher_id (id giáo viên)
- class_id (id lớp học)

### Đặt Phòng

- id (khóa chính)
- room_id (id phòng)
- user_id (id người dùng)
- start_time (thời gian bắt đầu)
- end_time (thời gian kết thúc)
- purpose (mục đích)
- status (trạng thái: đang chờ/chấp nhận/từ chối)
- timetable_id (tham chiếu tùy chọn đến thời khóa biểu)

## Yêu Cầu Giao Diện Người Dùng

- Thiết kế đáp ứng tương thích với máy tính để bàn và thiết bị di động
- Điều hướng trực quan
- Bảng điều khiển để truy cập nhanh các tính năng quan trọng
- Chỉ báo trực quan rõ ràng về tình trạng phòng
- Giao diện đặt phòng thân thiện với người dùng
- Hỗ trợ tiếng Việt

## Yêu Cầu Báo Cáo

- Thống kê sử dụng phòng
- Thời gian đặt phòng phổ biến
- Báo cáo hoạt động người dùng
- Phòng có sẵn tại các khung giờ cụ thể

## Yêu Cầu Bảo Mật

- Mã hóa mật khẩu
- Quản lý phiên làm việc
- Xác thực đầu vào
- Bảo vệ CSRF
- Kiểm soát truy cập dựa trên vai trò

## Cấu Trúc Dự Án

- `/public` - Điểm vào và các file có thể truy cập công khai
- `/src/Config` - Các file cấu hình cho cơ sở dữ liệu và cài đặt hệ thống
- `/src/Controllers` - Các bộ điều khiển ứng dụng để xử lý yêu cầu
- `/src/Models` - Các mô hình dữ liệu cho tương tác cơ sở dữ liệu
- `/src/Views` - Các mẫu giao diện được tổ chức theo vai trò người dùng
- `/src/Helpers` - Các hàm tiện ích và hỗ trợ

## Chi Tiết Triển Khai

- Kiến trúc MVC (Model-View-Controller)
- PDO cho tương tác cơ sở dữ liệu
- Định tuyến URL sạch thông qua .htaccess
- Tách biệt logic nghiệp vụ khỏi giao diện
