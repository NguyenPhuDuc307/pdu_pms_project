<?php
// Đảm bảo chỉ cho teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Lấy room_id từ query parameter nếu có
$preselected_room_id = $_GET['room_id'] ?? null;
$preselected_start_time = $_GET['start_time'] ?? '';
$preselected_end_time = $_GET['end_time'] ?? '';

// Thiết lập thông tin cho page_header
$pageTitle = "Đặt phòng";
$pageSubtitle = "Chọn thời gian và xem các phòng còn trống để đặt";
$pageIcon = "fas fa-calendar-plus";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/teacher'],
    ['title' => 'Tìm kiếm phòng', 'link' => '/pdu_pms_project/public/teacher/search_rooms'],
    ['title' => 'Đặt phòng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<style>
    /* Custom styling for room booking page */
    .booking-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .time-selector {
        background-color: rgba(78, 115, 223, 0.05);
        border-radius: 0.5rem;
        padding: 0.75rem;
        border-left: 4px solid #4e73df;
    }

    .room-card {
        transition: all 0.3s ease;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
    }

    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .room-card.selected {
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
    }

    .room-card .card-header {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .room-card .card-footer {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    .equipment-badge {
        transition: all 0.2s ease;
    }

    .equipment-badge:hover {
        transform: scale(1.05);
    }

    .room-capacity {
        color: #5a5c69;
        font-weight: 600;
    }

    .room-location {
        color: #e74a3b;
        font-weight: 600;
    }

    .btn-check-availability {
        background: linear-gradient(45deg, #4e73df 0%, #36b9cc 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-check-availability:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .btn-book-room {
        background: linear-gradient(45deg, #1cc88a 0%, #36b9cc 100%);
        border: none;
        font-weight: 600;
    }

    .btn-book-room:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    .room-feature {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .room-feature i {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(78, 115, 223, 0.1);
        color: #4e73df;
        margin-right: 0.75rem;
    }
</style>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <div class="d-none d-md-flex align-items-center justify-content-end mb-3">
        <div class="d-flex align-items-center me-4">
            <div class="rounded-circle bg-success d-inline-block me-2" style="width: 10px; height: 10px;"></div>
            <span class="small">Phòng trống</span>
        </div>
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-danger d-inline-block me-2" style="width: 10px; height: 10px;"></div>
            <span class="small">Phòng đã đặt</span>
        </div>
    </div>

    <!-- Booking Form Card -->
    <div class="card shadow mb-4 rounded border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin đặt phòng</h6>
        </div>
        <div class="card-body">
            <?php if (isset($data['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
                    <div><?= $data['error'] ?></div>
                </div>
            <?php endif; ?>

            <?php if (isset($data['success'])): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2 fa-lg"></i>
                    <div><?= $data['success'] ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="bookingForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="class_code" class="form-label">Mã lớp học</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-graduation-cap text-primary"></i></span>
                            <input type="text" class="form-control border-start-0" id="class_code" name="class_code" placeholder="Nhập mã lớp học..." required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="booking_date" class="form-label">Ngày đặt</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-alt text-primary"></i></span>
                            <input type="date" class="form-control border-start-0" id="booking_date" name="booking_date" min="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="time-selector">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_hour" class="form-label">Thời gian bắt đầu</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="fas fa-clock text-primary"></i></span>
                                                <select class="form-select" id="start_hour" name="start_hour" required>
                                                    <?php for ($i = 7; $i <= 21; $i++): ?>
                                                        <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select" id="start_minute" name="start_minute" required>
                                                <option value="00">00</option>
                                                <option value="30">30</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="end_hour" class="form-label">Thời gian kết thúc</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="fas fa-clock text-primary"></i></span>
                                                <select class="form-select" id="end_hour" name="end_hour" required>
                                                    <?php for ($i = 7; $i <= 21; $i++): ?>
                                                        <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select" id="end_minute" name="end_minute" required>
                                                <option value="00">00</option>
                                                <option value="30">30</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between">
                            <button type="button" id="checkAvailability" class="btn btn-check-availability py-2 px-4">
                                <i class="fas fa-search me-2"></i>Kiểm tra phòng trống
                            </button>
                            <input type="hidden" id="selected_room_id" name="room_id" value="<?= $preselected_room_id ?>">
                            <button type="submit" id="bookRoom" class="btn btn-book-room py-2 px-4 text-white" <?= empty($preselected_room_id) ? 'disabled' : '' ?>>
                                <i class="fas fa-calendar-check me-2"></i>Đặt phòng
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Available Rooms Card -->
    <div class="card shadow mb-4 rounded border-0" id="availableRoomsCard" style="display: <?= empty($data['available_rooms']) ? 'none' : 'block' ?>;">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Phòng trống trong khung giờ đã chọn</h6>
            <?php if (!empty($data['available_rooms'])): ?>
                <span class="badge bg-primary rounded-pill px-3 py-2"><?= count($data['available_rooms']) ?> phòng</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div id="availableRoomsContainer">
                <?php if (!empty($data['available_rooms'])): ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($data['available_rooms'] as $index => $room): ?>
                            <div class="col animate-fade-in" style="animation-delay: <?= $index * 0.1 ?>s">
                                <div class="card h-100 room-card">
                                    <div class="card-header bg-light py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($room['name']) ?></h6>
                                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Trống</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="room-feature">
                                                <i class="fas fa-chalkboard"></i>
                                                <div>
                                                    <div class="small text-muted">Loại phòng</div>
                                                    <div class="fw-medium"><?= htmlspecialchars($room['room_type_name']) ?></div>
                                                </div>
                                            </div>
                                            <div class="room-feature">
                                                <i class="fas fa-users"></i>
                                                <div>
                                                    <div class="small text-muted">Sức chứa</div>
                                                    <div class="room-capacity"><?= intval($room['capacity']) ?> người</div>
                                                </div>
                                            </div>
                                            <div class="room-feature">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <div>
                                                    <div class="small text-muted">Vị trí</div>
                                                    <div class="room-location"><?= htmlspecialchars($room['location']) ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (!empty($room['equipment'])): ?>
                                            <div class="mt-3">
                                                <small class="text-muted d-block mb-2">Thiết bị:</small>
                                                <div>
                                                    <?php foreach (array_slice(explode(',', $room['equipment']), 0, 3) as $equipment): ?>
                                                        <span class="badge bg-info-subtle text-info me-1 mb-1 equipment-badge px-2 py-1"><?= trim($equipment) ?></span>
                                                    <?php endforeach; ?>
                                                    <?php if (count(explode(',', $room['equipment'])) > 3): ?>
                                                        <span class="badge bg-secondary">...</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer bg-white border-top py-3">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-secondary flex-grow-1 view-room-detail" data-room-id="<?= $room['id'] ?>">
                                                <i class="fas fa-info-circle me-1"></i>Chi tiết
                                            </button>
                                            <button type="button" class="btn btn-primary flex-grow-1 select-room" data-room-id="<?= $room['id'] ?>">
                                                <i class="fas fa-check me-1"></i>Chọn
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5" id="noRoomsMessage">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3 opacity-50"></i>
                        <h4>Không có phòng trống</h4>
                        <p class="text-muted col-md-6 mx-auto">Không có phòng nào trống trong khung giờ bạn chọn. Vui lòng thử chọn thời gian khác.</p>
                        <button type="button" class="btn btn-outline-primary mt-2 px-4" id="changeTimeBtn">
                            <i class="fas fa-clock me-2"></i>Thay đổi thời gian
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Form Handling -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default values
        const today = new Date();
        document.getElementById('booking_date').value = today.toISOString().split('T')[0];

        // Set default start and end time (next closest hour or half-hour)
        const currentHour = today.getHours();
        const currentMinute = today.getMinutes();

        // Default to next 30-minute slot
        let defaultStartHour = currentHour;
        let defaultStartMinute = "30";

        // If past 30 minutes, go to next hour
        if (currentMinute >= 30) {
            defaultStartHour = currentHour + 1;
            defaultStartMinute = "00";
        }

        // Default end time is 1.5 hours after start time
        let defaultEndHour = defaultStartHour + 1;
        let defaultEndMinute = defaultStartMinute === "00" ? "30" : "00";

        // Handle day overflow
        if (defaultStartHour >= 21) {
            defaultStartHour = 21;
            defaultStartMinute = "00";
            defaultEndHour = 21;
            defaultEndMinute = "30";
        }

        if (defaultEndHour > 21) {
            defaultEndHour = 21;
            defaultEndMinute = "30";
        }

        // Set the default values in the form
        document.getElementById('start_hour').value = String(defaultStartHour).padStart(2, '0');
        document.getElementById('start_minute').value = defaultStartMinute;
        document.getElementById('end_hour').value = String(defaultEndHour).padStart(2, '0');
        document.getElementById('end_minute').value = defaultEndMinute;

        // Pre-fill form if values are passed in URL
        <?php if (!empty($preselected_start_time) && !empty($preselected_end_time)): ?>
            try {
                const startDate = new Date('<?= $preselected_start_time ?>');
                const endDate = new Date('<?= $preselected_end_time ?>');

                document.getElementById('booking_date').value = startDate.toISOString().split('T')[0];
                document.getElementById('start_hour').value = String(startDate.getHours()).padStart(2, '0');
                document.getElementById('start_minute').value = String(startDate.getMinutes()).padStart(2, '0');
                document.getElementById('end_hour').value = String(endDate.getHours()).padStart(2, '0');
                document.getElementById('end_minute').value = String(endDate.getMinutes()).padStart(2, '0');
            } catch (e) {
                console.error('Error parsing dates from URL parameters:', e);
            }
        <?php endif; ?>

        // Check room availability button
        document.getElementById('checkAvailability').addEventListener('click', function() {
            // Validate time inputs
            const startHour = parseInt(document.getElementById('start_hour').value);
            const startMinute = document.getElementById('start_minute').value;
            const endHour = parseInt(document.getElementById('end_hour').value);
            const endMinute = document.getElementById('end_minute').value;
            const bookingDate = document.getElementById('booking_date').value;

            // Create Date objects for comparison
            const startTime = new Date(`${bookingDate}T${String(startHour).padStart(2, '0')}:${startMinute}:00`);
            const endTime = new Date(`${bookingDate}T${String(endHour).padStart(2, '0')}:${endMinute}:00`);

            // Validate end time is after start time
            if (endTime <= startTime) {
                alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('start_time', `${bookingDate} ${String(startHour).padStart(2, '0')}:${startMinute}:00`);
            formData.append('end_time', `${bookingDate} ${String(endHour).padStart(2, '0')}:${endMinute}:00`);

            // Submit using fetch API - Use the specific getAvailableRooms endpoint
            fetch('/pdu_pms_project/public/teacher/get-available-rooms', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Received data:', data); // Debug output
                    const availableRoomsCard = document.getElementById('availableRoomsCard');
                    const availableRoomsContainer = document.getElementById('availableRoomsContainer');

                    availableRoomsCard.style.display = 'block';

                    if (data.error) {
                        // Display error message
                        availableRoomsContainer.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>${data.error}
                    </div>
                    <div class="text-center py-5">
                        <button type="button" class="btn btn-outline-primary mt-2" id="changeTimeBtn">
                            <i class="fas fa-clock me-2"></i>Thay đổi thời gian
                        </button>
                    </div>
                `;

                        document.getElementById('changeTimeBtn').addEventListener('click', function() {
                            document.getElementById('booking_date').focus();
                        });
                        return;
                    }

                    if (data.available_rooms && data.available_rooms.length > 0) {
                        // Generate card layout with available rooms
                        let tableHTML = `
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                `;

                        data.available_rooms.forEach((room, index) => {
                            let equipmentHTML = '';
                            if (room.equipment) {
                                const equipmentList = room.equipment.split(',');
                                const displayEquipment = equipmentList.slice(0, 3).map(item =>
                                    `<span class="badge bg-info-subtle text-info me-1 mb-1 equipment-badge px-2 py-1">${item.trim()}</span>`
                                ).join('');

                                if (equipmentList.length > 3) {
                                    equipmentHTML = displayEquipment + ' <span class="badge bg-secondary">...</span>';
                                } else {
                                    equipmentHTML = displayEquipment;
                                }
                            }

                            tableHTML += `
                        <div class="col animate-fade-in" style="animation-delay: ${index * 0.1}s">
                            <div class="card h-100 room-card" data-room-id="${room.id}">
                                <div class="card-header bg-light py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">${room.name}</h6>
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Trống</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="room-feature">
                                            <i class="fas fa-chalkboard"></i>
                                            <div>
                                                <div class="small text-muted">Loại phòng</div>
                                                <div class="fw-medium">${room.room_type_name}</div>
                                            </div>
                                        </div>
                                        <div class="room-feature">
                                            <i class="fas fa-users"></i>
                                            <div>
                                                <div class="small text-muted">Sức chứa</div>
                                                <div class="room-capacity">${room.capacity} người</div>
                                            </div>
                                        </div>
                                        <div class="room-feature">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <div>
                                                <div class="small text-muted">Vị trí</div>
                                                <div class="room-location">${room.location}</div>
                                            </div>
                                        </div>
                                    </div>

                                    ${room.equipment ? `
                                        <div class="mt-3">
                                            <small class="text-muted d-block mb-2">Thiết bị:</small>
                                            <div>
                                                ${equipmentHTML}
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="card-footer bg-white border-top py-3">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary flex-grow-1 view-room-detail" data-room-id="${room.id}">
                                            <i class="fas fa-info-circle me-1"></i>Chi tiết
                                        </button>
                                        <button type="button" class="btn btn-primary flex-grow-1 select-room" data-room-id="${room.id}">
                                            <i class="fas fa-check me-1"></i>Chọn
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                        });

                        tableHTML += `</div>`;

                        availableRoomsContainer.innerHTML = tableHTML;

                        // Add event listeners for the newly created buttons
                        document.querySelectorAll('.select-room').forEach(button => {
                            button.addEventListener('click', function() {
                                const roomId = this.getAttribute('data-room-id');
                                document.getElementById('selected_room_id').value = roomId;
                                document.getElementById('bookRoom').disabled = false;

                                // Highlight selected room
                                document.querySelectorAll('.room-card').forEach(card => {
                                    card.classList.remove('selected');
                                });
                                this.closest('.room-card').classList.add('selected');
                            });
                        });

                        document.querySelectorAll('.view-room-detail').forEach(button => {
                            button.addEventListener('click', function() {
                                const roomId = this.getAttribute('data-room-id');
                                window.location.href = `/pdu_pms_project/public/teacher/room_detail?id=${roomId}`;
                            });
                        });

                    } else {
                        // No available rooms
                        availableRoomsContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5>Không có phòng trống</h5>
                        <p class="text-muted">Không có phòng nào trống trong khung giờ bạn chọn</p>
                        <button type="button" class="btn btn-outline-primary mt-2" id="changeTimeBtn">
                            <i class="fas fa-clock me-2"></i>Thay đổi thời gian
                        </button>
                    </div>
                `;

                        document.getElementById('changeTimeBtn').addEventListener('click', function() {
                            document.getElementById('booking_date').focus();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error checking room availability:', error);
                    alert('Có lỗi xảy ra khi kiểm tra phòng trống. Vui lòng thử lại sau.');
                });
        });

        // Form submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate inputs
            const classCode = document.getElementById('class_code').value.trim();
            const startHour = document.getElementById('start_hour').value;
            const startMinute = document.getElementById('start_minute').value;
            const endHour = document.getElementById('end_hour').value;
            const endMinute = document.getElementById('end_minute').value;
            const bookingDate = document.getElementById('booking_date').value;
            const roomId = document.getElementById('selected_room_id').value;

            if (!classCode) {
                alert('Vui lòng nhập mã lớp học!');
                return;
            }

            if (!roomId) {
                alert('Vui lòng chọn phòng!');
                return;
            }

            // Create Date objects for comparison
            const startTime = new Date(`${bookingDate}T${startHour}:${startMinute}:00`);
            const endTime = new Date(`${bookingDate}T${endHour}:${endMinute}:00`);

            // Validate end time is after start time
            if (endTime <= startTime) {
                alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
                return;
            }

            // Prepare form data for booking
            const formData = new FormData();
            formData.append('class_code', classCode);
            formData.append('room_id', roomId);
            formData.append('start_time', `${bookingDate} ${startHour}:${startMinute}:00`);
            formData.append('end_time', `${bookingDate} ${endHour}:${endMinute}:00`);

            // Submit booking
            fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.success);
                        // Redirect to bookings page
                        window.location.href = '/pdu_pms_project/public/teacher';
                    } else if (data.error) {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error booking room:', error);
                    alert('Có lỗi xảy ra khi đặt phòng. Vui lòng thử lại sau.');
                });
        });

        // Change time button in no rooms message
        document.getElementById('changeTimeBtn')?.addEventListener('click', function() {
            document.getElementById('booking_date').focus();
        });
    });
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'teacher';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>