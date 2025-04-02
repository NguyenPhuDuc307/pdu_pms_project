# PDU-PMS (Phương Đông University - Phòng Management System)

Hệ thống quản lý phòng học thông minh của Đại học Phương Đông

## Giới thiệu

PDU-PMS là hệ thống quản lý phòng học hiện đại được phát triển nhằm tối ưu hóa việc sử dụng tài nguyên phòng học và thiết bị của trường đại học. Hệ thống cho phép quản lý toàn diện về phòng học, lịch dạy, và quản lý người dùng.

## Tính năng chính

- **Quản lý thông tin phòng học và trang thiết bị**
- **Đặt phòng trực tuyến và xem lịch sử đặt phòng**
- **Theo dõi lịch sử sử dụng phòng học**
- **Xếp lịch tự động cho các lớp học**
- **Quản lý người dùng (Admin, Giảng viên, Sinh viên)**
- **Thông báo và nhắc nhở về lịch học**

## Yêu cầu hệ thống

- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Web server (Apache/Nginx)

## Cài đặt

1. Clone repository:
   ```
   git clone https://github.com/yourusername/pdu_pms_project.git
   ```

2. Cấu hình database trong file `src/Config/Database.php`

3. Import database từ file SQL (nếu có)

4. Cấu hình web server để trỏ đến thư mục `public/`

## Cấu trúc thư mục

```
pdu_pms_project/
├── public/           # Document root
├── src/
│   ├── Config/       # Cấu hình hệ thống
│   ├── Controllers/  # Các controller
│   ├── Helpers/      # Các hàm trợ giúp
│   ├── Models/       # Các model
│   └── Views/        # Các view
```

## Nhóm phát triển

- Nguyễn Văn A (Project Lead)
- Trần Thị B (Frontend Developer)
- Lê Văn C (Backend Developer)
- Phạm Thị D (Database Manager)

## Giấy phép

© 2025 PDU - PMS | Phát triển bởi Đại học Phương Đông
