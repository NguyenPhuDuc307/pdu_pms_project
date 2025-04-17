# Hướng dẫn cập nhật cơ sở dữ liệu

Để hệ thống hoạt động đúng với các tính năng mới, bạn cần thêm cột `purpose` vào bảng `bookings` trong cơ sở dữ liệu.

## Cách 1: Sử dụng file SQL

1. Mở phpMyAdmin hoặc công cụ quản lý cơ sở dữ liệu MySQL khác
2. Chọn cơ sở dữ liệu `pdu_pms` (hoặc tên cơ sở dữ liệu bạn đang sử dụng)
3. Chọn tab "SQL" để thực thi câu lệnh SQL
4. Mở file `add_purpose_column.sql` và sao chép nội dung
5. Dán nội dung vào cửa sổ thực thi SQL và nhấn "Go" hoặc "Execute"

## Cách 2: Thực hiện thủ công

Nếu bạn không muốn sử dụng file SQL, bạn có thể thực hiện các bước sau:

1. Mở phpMyAdmin hoặc công cụ quản lý cơ sở dữ liệu MySQL khác
2. Chọn cơ sở dữ liệu `pdu_pms` (hoặc tên cơ sở dữ liệu bạn đang sử dụng)
3. Chọn bảng `bookings`
4. Chọn tab "Structure" hoặc "Cấu trúc"
5. Nhấn "Add column" hoặc "Thêm cột"
6. Nhập thông tin sau:
   - Tên cột: `purpose`
   - Kiểu dữ liệu: `TEXT`
   - Mặc định: `NULL`
   - Vị trí: `After end_time`
7. Nhấn "Save" hoặc "Lưu"

## Kiểm tra

Sau khi thêm cột, bạn có thể kiểm tra bằng cách:

1. Chọn bảng `bookings`
2. Chọn tab "Structure" hoặc "Cấu trúc"
3. Kiểm tra xem cột `purpose` đã được thêm vào chưa

## Lưu ý

- Nếu bạn đang sử dụng tên cơ sở dữ liệu khác với `pdu_pms`, hãy điều chỉnh tên cơ sở dữ liệu trong các bước trên.
- Đảm bảo bạn đã sao lưu cơ sở dữ liệu trước khi thực hiện các thay đổi.
