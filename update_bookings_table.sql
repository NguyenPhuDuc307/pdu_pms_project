-- Thêm cột user_id mới
ALTER TABLE bookings ADD COLUMN user_id INT NULL;

-- Cập nhật user_id từ teacher_id hoặc student_id
UPDATE bookings SET user_id = teacher_id WHERE teacher_id IS NOT NULL;
UPDATE bookings SET user_id = student_id WHERE student_id IS NOT NULL AND user_id IS NULL;

-- Thêm khóa ngoại cho user_id
ALTER TABLE bookings ADD CONSTRAINT bookings_ibfk_4 FOREIGN KEY (user_id) REFERENCES users(id);

-- Xóa các ràng buộc khóa ngoại cũ
ALTER TABLE bookings DROP FOREIGN KEY bookings_ibfk_2; -- teacher_id
ALTER TABLE bookings DROP FOREIGN KEY bookings_ibfk_3; -- student_id

-- Xóa các cột cũ
ALTER TABLE bookings DROP COLUMN teacher_id;
ALTER TABLE bookings DROP COLUMN student_id;
