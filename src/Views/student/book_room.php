<?php
// Đảm bảo chỉ cho student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Đặt phòng học";
$pageSubtitle = "Tạo yêu cầu đặt phòng học mới";
$pageIcon = "fas fa-calendar-plus";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/student'],
    ['title' => 'Tìm kiếm phòng học', 'link' => '/pdu_pms_project/public/student/search_rooms'],
    ['title' => 'Đặt phòng học', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Thông tin đặt phòng</h5>
                </div>
                <div class="card-body">
                    <form action="/pdu_pms_project/public/student/submit_booking" method="post" id="bookingForm">
                        <!-- Phòng đã chọn -->
                        <?php if (isset($data['room'])): ?>
                            <input type="hidden" name="room_id" value="<?php echo $data['room']['id']; ?>">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Phòng đã chọn</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                                <i class="fas fa-door-open fa-lg text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($data['room']['name']); ?></h6>
                                                <p class="text-muted mb-0">
                                                    <small>
                                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($data['room']['location']); ?> |
                                                        <i class="fas fa-users me-1"></i><?php echo htmlspecialchars($data['room']['capacity']); ?> người |
                                                        <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($data['room']['room_type_name']); ?>
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <label for="room_id" class="form-label fw-bold">Chọn phòng</label>
                                <select class="form-select" id="room_id" name="room_id" required>
                                    <option value="" selected disabled>-- Chọn phòng học --</option>
                                    <?php if (isset($_POST['start_time']) && isset($_POST['end_time'])): ?>
                                        <?php if (!empty($data['available_rooms'])): ?>
                                            <?php foreach ($data['rooms'] as $room): ?>
                                                <?php if (in_array($room['id'], $data['available_rooms'])): ?>
                                                    <option value="<?php echo $room['id']; ?>"
                                                        data-capacity="<?php echo $room['capacity']; ?>"
                                                        data-type="<?php echo $room['room_type_name'] ?? $room['type_name'] ?? 'Không có thông tin'; ?>"
                                                        data-location="<?php echo $room['location']; ?>">
                                                        <?php echo htmlspecialchars($room['name']); ?> (Trống)
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php foreach ($data['rooms'] as $room): ?>
                                            <option value="<?php echo $room['id']; ?>"
                                                data-capacity="<?php echo $room['capacity']; ?>"
                                                data-type="<?php echo $room['room_type_name'] ?? $room['type_name'] ?? 'Không có thông tin'; ?>"
                                                data-location="<?php echo $room['location']; ?>">
                                                <?php echo htmlspecialchars($room['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div id="roomDetails" class="mt-2 d-none">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="mb-0 small">
                                                <i class="fas fa-map-marker-alt me-1"></i><span id="roomLocation"></span> |
                                                <i class="fas fa-users me-1"></i><span id="roomCapacity"></span> người |
                                                <i class="fas fa-tag me-1"></i><span id="roomType"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Thông tin thời gian -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-bold">Thời gian bắt đầu</label>
                                <input type="datetime-local" class="form-control" id="start_time" name="start_time" required
                                    <?php if (isset($_GET['start'])): ?>
                                    value="<?php echo date('Y-m-d\TH:i', strtotime($_GET['start'])); ?>"
                                    <?php else: ?>
                                    min="<?php echo date('Y-m-d\TH:i'); ?>"
                                    <?php endif; ?>>
                                <div class="invalid-feedback">Vui lòng chọn thời gian bắt đầu</div>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label fw-bold">Thời gian kết thúc</label>
                                <input type="datetime-local" class="form-control" id="end_time" name="end_time" required
                                    <?php if (isset($_GET['end'])): ?>
                                    value="<?php echo date('Y-m-d\TH:i', strtotime($_GET['end'])); ?>"
                                    <?php endif; ?>>
                                <div class="invalid-feedback">Vui lòng chọn thời gian kết thúc sau thời gian bắt đầu</div>
                            </div>
                        </div>

                        <!-- Thông báo đang tìm phòng -->
                        <?php if (!isset($data['room'])): ?>
                            <div id="searchingRoomsSpinner" class="d-none mb-4">
                                <div class="alert alert-info d-flex align-items-center">
                                    <div class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span>Đang kiểm tra phòng trống trong khoảng thời gian đã chọn...</span>
                                </div>
                            </div>

                            <!-- Kết quả tìm phòng -->
                            <?php if (isset($_POST['start_time']) && isset($_POST['end_time'])): ?>
                                <?php if (empty($data['available_rooms'])): ?>
                                    <div class="alert alert-warning mb-4">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Không có phòng trống trong khoảng thời gian đã chọn. Vui lòng thử chọn thời gian khác.
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-success mb-4">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Đã tìm thấy <?php echo count($data['available_rooms']); ?> phòng trống trong khoảng thời gian đã chọn.
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Thông tin lớp học -->
                        <div class="mb-4">
                            <label for="class_code" class="form-label fw-bold">Mã lớp học</label>
                            <input type="text" class="form-control" id="class_code" name="class_code" placeholder="Ví dụ: CS101, MATH202..." required>
                            <div class="form-text">Nhập mã lớp học hoặc mã hoạt động</div>
                        </div>

                        <!-- Mục đích sử dụng -->
                        <div class="mb-4">
                            <label for="purpose" class="form-label fw-bold">Mục đích sử dụng</label>
                            <select class="form-select" id="purpose" name="purpose" required>
                                <option value="" selected disabled>-- Chọn mục đích sử dụng --</option>
                                <option value="học nhóm">Học nhóm</option>
                                <option value="thuyết trình">Thuyết trình</option>
                                <option value="ôn thi">Ôn thi</option>
                                <option value="seminar">Seminar</option>
                                <option value="hội họp">Hội họp</option>
                                <option value="khác">Khác</option>
                            </select>
                        </div>

                        <div id="otherPurposeContainer" class="mb-4 d-none">
                            <label for="other_purpose" class="form-label">Mục đích khác</label>
                            <input type="text" class="form-control" id="other_purpose" name="other_purpose" placeholder="Vui lòng nêu rõ mục đích sử dụng...">
                        </div>

                        <!-- Số người tham gia -->
                        <div class="mb-4">
                            <label for="participants" class="form-label fw-bold">Số lượng người tham gia (dự kiến)</label>
                            <input type="number" class="form-control" id="participants" name="participants" min="1" required>
                            <div class="form-text" id="capacity-warning"></div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">Ghi chú bổ sung</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Ghi chú thêm về nhu cầu đặc biệt hoặc thông tin khác..."></textarea>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">điều khoản sử dụng</a> và cam kết sử dụng phòng học đúng mục đích
                            </label>
                            <div class="invalid-feedback">
                                Bạn phải đồng ý với điều khoản sử dụng
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitBookingBtn">
                            <i class="fas fa-calendar-check me-2"></i>Gửi yêu cầu đặt phòng
                        </button>
                        <a href="/pdu_pms_project/public/student/search_rooms" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Thông tin bổ sung</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="mb-2"><i class="fas fa-info-circle me-2 text-primary"></i>Lưu ý quan trọng</h6>
                        <ul class="text-muted small mb-0">
                            <li>Yêu cầu đặt phòng của sinh viên sẽ cần được kiểm duyệt trước khi được chấp nhận.</li>
                            <li>Thời gian đặt phòng phải kết thúc trước 21:00.</li>
                            <li>Vui lòng đặt trước ít nhất 24 giờ để đảm bảo thời gian xét duyệt.</li>
                            <li>Chỉ được phép đặt phòng cho các hoạt động học tập.</li>
                        </ul>
                    </div>

                    <hr>

                    <div class="mb-0">
                        <h6 class="mb-2"><i class="fas fa-clock me-2 text-primary"></i>Thời gian phản hồi</h6>
                        <p class="text-muted small mb-0">Thông thường các yêu cầu đặt phòng sẽ được xử lý trong vòng 24 giờ làm việc (không tính thứ 7, chủ nhật và ngày lễ).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều khoản sử dụng phòng học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Quy định chung</h6>
                <p>Người đặt phòng phải là sinh viên hoặc giảng viên của trường, có trách nhiệm tuân thủ các quy định của nhà trường về sử dụng cơ sở vật chất.</p>

                <h6>2. Thời gian sử dụng</h6>
                <p>Thời gian sử dụng phòng học phải nằm trong khung giờ từ 7:00 đến 21:00 hàng ngày. Sinh viên chỉ được đặt phòng tối đa 3 giờ mỗi lần.</p>

                <h6>3. Trách nhiệm người sử dụng</h6>
                <ul>
                    <li>Giữ gìn vệ sinh phòng học, không viết, vẽ lên bàn ghế, tường, bảng.</li>
                    <li>Sử dụng thiết bị đúng cách, tiết kiệm điện nước.</li>
                    <li>Không được tự ý di chuyển bàn ghế, thiết bị ra khỏi phòng.</li>
                    <li>Không được hút thuốc, ăn uống trong phòng học (trừ nước uống).</li>
                    <li>Giữ trật tự, không làm ồn ảnh hưởng đến các lớp học khác.</li>
                </ul>

                <h6>4. Quy định hủy đặt phòng</h6>
                <p>Việc hủy đặt phòng phải được thực hiện trước thời gian sử dụng ít nhất 2 giờ. Nếu không sử dụng phòng đã đặt mà không hủy sẽ bị ghi nhận và có thể bị hạn chế quyền đặt phòng trong tương lai.</p>

                <h6>5. Xử lý vi phạm</h6>
                <p>Những trường hợp vi phạm quy định sử dụng phòng học sẽ bị xử lý theo quy định của nhà trường, từ nhắc nhở, cảnh cáo đến hạn chế quyền đặt phòng học.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đã hiểu</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý hiển thị thông tin chi tiết phòng khi chọn
        const roomSelect = document.getElementById('room_id');
        const roomDetails = document.getElementById('roomDetails');
        const roomLocation = document.getElementById('roomLocation');
        const roomCapacity = document.getElementById('roomCapacity');
        const roomType = document.getElementById('roomType');

        if (roomSelect) {
            roomSelect.addEventListener('change', function() {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];

                if (selectedOption.value) {
                    roomLocation.textContent = selectedOption.dataset.location;
                    roomCapacity.textContent = selectedOption.dataset.capacity;
                    roomType.textContent = selectedOption.dataset.type;
                    roomDetails.classList.remove('d-none');
                } else {
                    roomDetails.classList.add('d-none');
                }
            });
        }

        // Xử lý mục đích sử dụng khác
        const purposeSelect = document.getElementById('purpose');
        const otherPurposeContainer = document.getElementById('otherPurposeContainer');
        const otherPurposeInput = document.getElementById('other_purpose');

        if (purposeSelect && otherPurposeContainer) {
            purposeSelect.addEventListener('change', function() {
                if (purposeSelect.value === 'khác') {
                    otherPurposeContainer.classList.remove('d-none');
                    otherPurposeInput.setAttribute('required', 'required');
                } else {
                    otherPurposeContainer.classList.add('d-none');
                    otherPurposeInput.removeAttribute('required');
                }
            });
        }

        // Kiểm tra số người và cảnh báo vượt quá sức chứa
        const participantsInput = document.getElementById('participants');
        const capacityWarning = document.getElementById('capacity-warning');

        if (participantsInput && roomSelect) {
            participantsInput.addEventListener('input', function() {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];

                if (selectedOption.value) {
                    const roomCapacity = parseInt(selectedOption.dataset.capacity);
                    const participants = parseInt(participantsInput.value);

                    if (participants > roomCapacity) {
                        capacityWarning.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Số người vượt quá sức chứa của phòng (${roomCapacity} người)</span>`;
                    } else {
                        capacityWarning.innerHTML = '';
                    }
                }
            });
        }

        // Kiểm tra thời gian đặt phòng
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const searchingRoomsSpinner = document.getElementById('searchingRoomsSpinner');

        if (startTimeInput && endTimeInput && searchingRoomsSpinner) {
            // Hiển thị trạng thái tìm kiếm khi thay đổi thời gian
            [startTimeInput, endTimeInput].forEach(input => {
                input.addEventListener('change', function() {
                    if (startTimeInput.value && endTimeInput.value) {
                        const startTime = new Date(startTimeInput.value);
                        const endTime = new Date(endTimeInput.value);

                        if (endTime <= startTime) {
                            endTimeInput.setCustomValidity('Thời gian kết thúc phải sau thời gian bắt đầu');
                        } else {
                            endTimeInput.setCustomValidity('');
                            // Chỉ hiển thị spinner khi cả hai trường thời gian hợp lệ
                            if (!document.getElementById('room_id').value) {
                                searchingRoomsSpinner.classList.remove('d-none');
                                document.getElementById('bookingForm').submit();
                            }
                        }
                    }
                });
            });
        }

        // Form validation
        const form = document.getElementById('bookingForm');

        if (form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        }
    });
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'student';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>