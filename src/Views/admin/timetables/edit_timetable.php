<?php
// Thiết lập thông tin cho page_header
$pageTitle = "Chỉnh sửa lịch dạy";
$pageSubtitle = "Cập nhật thông tin lịch dạy và phòng học";
$pageIcon = "fas fa-calendar-alt";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý lịch dạy', 'link' => '/pdu_pms_project/public/admin/manage_timetable'],
    ['title' => 'Chỉnh sửa lịch dạy', 'link' => '']
];

// Thêm các style cần thiết
$pageStyles = <<<EOT
.room-card {
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}

.room-card:hover:not(.disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.room-card.selected {
    box-shadow: 0 0 0 3px #0d6efd !important;
    transform: scale(1.02);
    z-index: 10;
    position: relative;
}

.room-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
EOT;

// Thêm các script cần thiết
$pageScripts = <<<EOT
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý chọn phòng
    document.querySelectorAll('input[name="room_id"]').forEach(function(input) {
        const label = input.closest('label');
        const card = label.querySelector('.card');

        // Thiết lập trạng thái ban đầu
        if (input.checked) {
            card.classList.add('selected');
        }

        if (!input.disabled) {
            label.addEventListener('click', function() {
                // Bỏ chọn tất cả các phòng
                document.querySelectorAll('.room-card').forEach(function(otherCard) {
                    otherCard.classList.remove('selected');
                });

                // Đánh dấu phòng này là đã chọn
                card.classList.add('selected');
                input.checked = true;

                // Hiệu ứng khi chọn
                card.animate([
                    { transform: 'scale(1.05)' },
                    { transform: 'scale(1)' },
                    { transform: 'scale(1.05)' }
                ], {
                    duration: 300,
                    easing: 'ease-in-out'
                });
            });
        }
    });

    // Kiểm tra số lượng sinh viên
    const participantsInput = document.getElementById('participants');
    if (participantsInput) {
        participantsInput.addEventListener('change', function() {
            const participants = parseInt(this.value, 10) || 0;
            // Cập nhật UI dựa trên số lượng sinh viên
            document.querySelectorAll('.room-card').forEach(function(card) {
                const capacityText = card.querySelector('.card-text');
                if (capacityText) {
                    const capacity = parseInt(capacityText.textContent.replace('Số máy: ', ''), 10) || 0;
                    const label = card.closest('label');
                    const input = label.querySelector('input[name="room_id"]');

                    if (capacity < participants) {
                        // Phòng không đủ số máy
                        input.disabled = true;
                        card.classList.add('disabled');
                        card.classList.remove('bg-success', 'bg-opacity-10', 'border-success');
                        card.classList.add('bg-secondary', 'bg-opacity-10', 'border-secondary');

                        const badge = card.querySelector('.badge');
                        if (badge) {
                            badge.textContent = 'Không đủ';
                            badge.classList.remove('bg-success');
                            badge.classList.add('bg-secondary');
                        }
                    } else if (!input.disabled && !card.classList.contains('bg-warning') && !card.classList.contains('bg-danger')) {
                        // Phòng đủ số máy
                        input.disabled = false;
                        card.classList.remove('disabled');
                        card.classList.remove('bg-secondary', 'bg-opacity-10', 'border-secondary');
                        card.classList.add('bg-success', 'bg-opacity-10', 'border-success');

                        const badge = card.querySelector('.badge');
                        if (badge && badge.textContent === 'Không đủ') {
                            badge.textContent = 'Trống';
                            badge.classList.remove('bg-secondary');
                            badge.classList.add('bg-success');
                        }
                    }
                }
            });
        });
    }

    // Form validation
    const form = document.querySelector('form.needs-validation');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Kiểm tra thời gian kết thúc phải sau thời gian bắt đầu
            const startTime = new Date(document.getElementById('start_time').value);
            const endTime = new Date(document.getElementById('end_time').value);

            if (endTime <= startTime) {
                event.preventDefault();
                alert('Thời gian kết thúc phải sau thời gian bắt đầu');
                document.getElementById('end_time').classList.add('is-invalid');
            }

            form.classList.add('was-validated');
        });
    }
});
EOT;

// Set page role
$pageRole = 'admin';

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <!-- Thông báo lỗi nếu có -->
    <?php if (isset($data['errors']) && !empty($data['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($data['errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit me-2"></i>Chỉnh sửa lịch dạy</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/pdu_pms_project/public/admin/edit_timetable?id=<?php echo htmlspecialchars($data['timetable']['id']); ?>" class="needs-validation" novalidate>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="teacher_id" class="form-label fw-semibold">Giảng viên</label>
                            <select name="teacher_id" id="teacher_id" class="form-select" required>
                                <?php foreach ($data['teachers'] as $teacher): ?>
                                    <?php if ($teacher['role'] === 'teacher' || $teacher['role'] === 'admin'): ?>
                                        <option value="<?php echo htmlspecialchars($teacher['id']); ?>" <?php echo ($teacher['id'] == $data['timetable']['teacher_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($teacher['username']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn giảng viên</div>
                        </div>

                        <div class="mb-3">
                            <label for="class_code" class="form-label fw-semibold">Mã lớp</label>
                            <input type="text" name="class_code" id="class_code" value="<?php echo htmlspecialchars($data['timetable']['class_code']); ?>" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng nhập mã lớp</div>
                        </div>

                        <div class="mb-3">
                            <label for="participants" class="form-label fw-semibold">Số lượng sinh viên</label>
                            <input type="number" name="participants" id="participants" value="<?php echo htmlspecialchars($data['timetable']['participants']); ?>" class="form-control" required min="1">
                            <div class="invalid-feedback">Vui lòng nhập số lượng sinh viên hợp lệ</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">Môn học</label>
                            <input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars($data['timetable']['subject']); ?>" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng nhập tên môn học</div>
                        </div>

                        <div class="mb-3">
                            <label for="start_time" class="form-label fw-semibold">Thời gian bắt đầu</label>
                            <input type="datetime-local" name="start_time" id="start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($data['timetable']['start_time'])); ?>" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng chọn thời gian bắt đầu</div>
                        </div>

                        <div class="mb-3">
                            <label for="end_time" class="form-label fw-semibold">Thời gian kết thúc</label>
                            <input type="datetime-local" name="end_time" id="end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($data['timetable']['end_time'])); ?>" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng chọn thời gian kết thúc</div>
                        </div>
                    </div>
                </div>

                <!-- Phần chọn phòng trực quan -->
                <div class="mt-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-door-open me-2"></i>Chọn phòng học</h5>

                    <!-- Giải thích màu sắc -->
                    <div class="d-flex flex-wrap gap-3 mb-3 small">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 border border-success rounded me-2" style="width:16px;height:16px;"></div>
                            <span>Phòng trống</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 border border-warning rounded me-2" style="width:16px;height:16px;"></div>
                            <span>Phòng hiện tại</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 border border-danger rounded me-2" style="width:16px;height:16px;"></div>
                            <span>Phòng đã đặt</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 border border-secondary rounded me-2" style="width:16px;height:16px;"></div>
                            <span>Không đủ số máy</span>
                        </div>
                    </div>

                    <!-- Grid hiển thị các phòng -->
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                        <?php
                        $participants = (int)($data['timetable']['participants'] ?? 0);
                        $currentRoomId = $data['timetable']['room_id'] ?? null;
                        $start_time = $data['timetable']['start_time'] ?? null;
                        $end_time = $data['timetable']['end_time'] ?? null;
                        $timetableId = $data['timetable']['id'] ?? null;

                        foreach ($data['rooms'] as $room):
                            // Kiểm tra xem phòng này có đủ số máy không
                            $hasEnoughCapacity = $room['capacity'] >= $participants;

                            // Kiểm tra xem phòng này có phải phòng hiện tại không
                            $isCurrentRoom = ($room['id'] == $currentRoomId);

                            // Kiểm tra xem phòng này có trống trong khung giờ này không
                            $isRoomAvailable = true;
                            if (!$isCurrentRoom && $start_time && $end_time) {
                                // Giả sử bạn có một hàm kiểm tra phòng trống trong controller và truyền kết quả vào view
                                // Ở đây tôi tạm giả định rằng những phòng có ID chẵn là trống
                                // Trong thực tế, bạn sẽ cần kiểm tra từ cơ sở dữ liệu
                                $isRoomAvailable = isset($data['available_rooms']) ?
                                    in_array($room['id'], $data['available_rooms']) : ($room['id'] % 2 == 0);
                            }

                            // Xác định class cho phòng
                            $cardClass = 'card h-100 room-card';
                            $badgeText = '';
                            $badgeClass = 'badge';

                            if ($isCurrentRoom) {
                                $cardClass .= ' bg-warning bg-opacity-10 border-warning';
                                $badgeText = 'Hiện tại';
                                $badgeClass .= ' bg-warning text-dark';
                            } elseif (!$hasEnoughCapacity) {
                                $cardClass .= ' bg-secondary bg-opacity-10 border-secondary disabled';
                                $badgeText = 'Không đủ';
                                $badgeClass .= ' bg-secondary';
                            } elseif (!$isRoomAvailable) {
                                $cardClass .= ' bg-danger bg-opacity-10 border-danger disabled';
                                $badgeText = 'Đã đặt';
                                $badgeClass .= ' bg-danger';
                            } else {
                                $cardClass .= ' bg-success bg-opacity-10 border-success';
                                $badgeText = 'Trống';
                                $badgeClass .= ' bg-success';
                            }

                            // Thêm class selected nếu là phòng được chọn
                            if ($isCurrentRoom && !isset($_POST['room_id'])) {
                                $cardClass .= ' selected';
                            }
                        ?>
                            <div class="col">
                                <label class="d-block h-100 m-0">
                                    <input type="radio" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>"
                                        <?php echo $isCurrentRoom ? 'checked' : ''; ?>
                                        <?php echo (!$hasEnoughCapacity || (!$isRoomAvailable && !$isCurrentRoom)) ? 'disabled' : ''; ?>
                                        class="d-none">
                                    <div class="<?php echo $cardClass; ?>">
                                        <div class="card-body p-2 text-center">
                                            <h6 class="card-title mb-1"><?php echo htmlspecialchars($room['name']); ?></h6>
                                            <p class="card-text small text-muted mb-2">Số máy: <?php echo htmlspecialchars($room['capacity']); ?></p>
                                            <span class="<?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Nút Submit -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="/pdu_pms_project/public/admin/manage_timetable" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Hủy bỏ
                    </a>
                    <button type="submit" name="update_timetable" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Get the buffered content
$pageContent = ob_get_clean();

// Include the main layout
include dirname(dirname(__DIR__)) . '/layouts/main_layout.php';
?>