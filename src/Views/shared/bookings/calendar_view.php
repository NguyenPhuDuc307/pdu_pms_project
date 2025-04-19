<?php
// Đảm bảo người dùng đã đăng nhập với vai trò phù hợp
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'teacher', 'student'])) {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Lịch đặt phòng";
$pageSubtitle = "Xem lịch đặt phòng theo dạng lịch";
$pageIcon = "fas fa-calendar-alt";

// Thiết lập breadcrumbs dựa trên vai trò
$role = $_SESSION['role'];
switch ($role) {
    case 'admin':
        $breadcrumbs = [
            ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
            ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
            ['title' => 'Quản lý đặt phòng', 'link' => '/pdu_pms_project/public/admin/manage_bookings'],
            ['title' => 'Lịch đặt phòng', 'link' => '']
        ];
        break;
    case 'teacher':
        $breadcrumbs = [
            ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/teacher'],
            ['title' => 'Lịch đặt phòng', 'link' => '']
        ];
        break;
    case 'student':
        $breadcrumbs = [
            ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/student'],
            ['title' => 'Lịch đặt phòng', 'link' => '']
        ];
        break;
}

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group">
            <?php if ($role === 'admin'): ?>
                <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-outline-primary">
                    <i class="fas fa-table me-1"></i> Dạng bảng
                </a>
                <a href="/pdu_pms_project/public/admin/calendar_bookings" class="btn btn-primary active">
                    <i class="fas fa-calendar-alt me-1"></i> Dạng lịch
                </a>
            <?php elseif ($role === 'teacher'): ?>
                <a href="/pdu_pms_project/public/teacher/my_bookings" class="btn btn-outline-primary">
                    <i class="fas fa-table me-1"></i> Dạng bảng
                </a>
                <a href="/pdu_pms_project/public/teacher/calendar_bookings" class="btn btn-primary active">
                    <i class="fas fa-calendar-alt me-1"></i> Dạng lịch
                </a>
            <?php elseif ($role === 'student'): ?>
                <a href="/pdu_pms_project/public/student/my_bookings" class="btn btn-outline-primary">
                    <i class="fas fa-table me-1"></i> Dạng bảng
                </a>
                <a href="/pdu_pms_project/public/student/calendar_bookings" class="btn btn-primary active">
                    <i class="fas fa-calendar-alt me-1"></i> Dạng lịch
                </a>
            <?php endif; ?>
        </div>
        <?php if ($role === 'admin'): ?>
            <a href="/pdu_pms_project/public/admin/add_booking" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Thêm đặt phòng
            </a>
        <?php elseif ($role === 'teacher'): ?>
            <a href="/pdu_pms_project/public/teacher/book_room" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Đặt phòng
            </a>
        <?php elseif ($role === 'student'): ?>
            <a href="/pdu_pms_project/public/student/book_room" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Đặt phòng
            </a>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Nút tác vụ nhanh -->
            <div class="row mb-3">
                <div class="col-12 mb-3">
                    <div class="d-flex flex-wrap gap-2">
                        <button id="today-btn" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar-day me-1"></i> Hôm nay
                        </button>
                        <button id="week-btn" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar-week me-1"></i> Tuần này
                        </button>
                        <button id="month-btn" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar-alt me-1"></i> Tháng này
                        </button>
                        <button id="pending-btn" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-clock me-1"></i> Chờ duyệt
                        </button>
                        <button id="approved-btn" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-check-circle me-1"></i> Đã duyệt
                        </button>
                        <button id="refresh-btn" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i> Làm mới
                        </button>
                        <?php if ($role === 'teacher' || $role === 'student'): ?>
                            <a href="<?= '/pdu_pms_project/public/' . $role . '/book_room' ?>" class="btn btn-primary btn-sm ms-auto">
                                <i class="fas fa-plus me-1"></i> Đặt phòng mới
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bộ lọc -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-filter text-primary"></i></span>
                        <select id="room-filter" class="form-select">
                            <option value="">Tất cả phòng</option>
                            <?php if (isset($data['rooms']) && is_array($data['rooms'])): ?>
                                <?php foreach ($data['rooms'] as $room): ?>
                                    <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <?php if ($role === 'admin'): ?>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                            <select id="user-filter" class="form-select">
                                <option value="">Tất cả người dùng</option>
                                <?php if (isset($data['users']) && is_array($data['users'])): ?>
                                    <?php foreach ($data['users'] as $user): ?>
                                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-tasks text-primary"></i></span>
                        <select id="status-filter" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="được duyệt">Được duyệt</option>
                            <option value="chờ duyệt">Chờ duyệt</option>
                            <option value="từ chối">Từ chối</option>
                            <option value="đã hủy">Đã hủy</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal xem chi tiết đặt phòng -->
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingDetailModalLabel">Chi tiết đặt phòng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">Phòng:</label>
                        <span id="modal-room"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Người đặt:</label>
                        <span id="modal-user"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Thời gian bắt đầu:</label>
                        <span id="modal-start"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Thời gian kết thúc:</label>
                        <span id="modal-end"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Mã lớp:</label>
                        <span id="modal-class"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Mục đích:</label>
                        <span id="modal-purpose"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Trạng thái:</label>
                        <span id="modal-status"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if ($role === 'admin'): ?>
                        <a href="#" id="modal-approve-link" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Duyệt
                        </a>
                        <a href="#" id="modal-reject-link" class="btn btn-warning">
                            <i class="fas fa-times me-1"></i> Từ chối
                        </a>
                        <a href="#" id="modal-delete-link" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Xóa
                        </a>
                    <?php elseif ($role === 'teacher' || $role === 'student'): ?>
                        <a href="#" id="modal-cancel-link" class="btn btn-danger">
                            <i class="fas fa-times me-1"></i> Hủy đặt phòng
                        </a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Thêm CSS cho FullCalendar
$pageStyles = <<<EOT
#calendar {
    height: 100%;
    width: 100%;
}

.fc-view-harness {
    width: 100% !important;
}

.fc-scrollgrid {
    width: 100% !important;
}

.fc-scrollgrid-sync-table {
    width: 100% !important;
}

.fc-daygrid-body {
    width: 100% !important;
}

.fc-daygrid-body-balanced {
    width: 100% !important;
}

.fc-daygrid-body-unbalanced {
    width: 100% !important;
}

.fc-timegrid-body {
    width: 100% !important;
}

.fc-timegrid-body-balanced {
    width: 100% !important;
}

.fc-timegrid-slots {
    width: 100% !important;
}

.fc-timegrid-cols {
    width: 100% !important;
}

.fc-timegrid-col {
    width: 100% !important;
}

.fc-timegrid-col-frame {
    width: 100% !important;
}

.btn-group-calendar {
    margin-bottom: 15px;
}

.btn-group-calendar .btn {
    margin-right: 5px;
}

.calendar-toolbar {
    margin-bottom: 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.fc-event {
    cursor: pointer;
}
.fc-event.status-approved {
    background-color: #28a745;
    border-color: #28a745;
}
.fc-event.status-pending {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}
.fc-event.status-rejected {
    background-color: #dc3545;
    border-color: #dc3545;
}
.fc-event.status-cancelled {
    background-color: #6c757d;
    border-color: #6c757d;
}
EOT;

// Thêm JavaScript cho FullCalendar
$pageScripts = <<<EOT
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo FullCalendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        expandRows: true,
        stickyHeaderDates: true,
        locale: 'vi',
        timeZone: 'local',
        allDaySlot: false,
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',
        slotDuration: '00:30:00',
        height: 'auto',
        contentHeight: 'auto',
        nowIndicator: true,
        navLinks: true,
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6],
            startTime: '07:00',
            endTime: '22:00',
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        events: function(info, successCallback, failureCallback) {
            // Lấy giá trị từ các bộ lọc
            var roomFilter = document.getElementById('room-filter').value;
            var statusFilter = document.getElementById('status-filter').value;
            var userFilter = '';

            // Chỉ lấy user filter nếu là admin
            if (document.getElementById('user-filter')) {
                userFilter = document.getElementById('user-filter').value;
            }

            // Xác định endpoint dựa trên vai trò
            var endpoint = '';
            switch ('$role') {
                case 'admin':
                    endpoint = '/pdu_pms_project/public/admin/get_bookings_json';
                    break;
                case 'teacher':
                    endpoint = '/pdu_pms_project/public/teacher/get_bookings_json';
                    break;
                case 'student':
                    endpoint = '/pdu_pms_project/public/student/get_bookings_json';
                    break;
            }

            // Gọi API để lấy dữ liệu
            fetch(endpoint + '?start=' + info.startStr + '&end=' + info.endStr +
                  '&room_id=' + roomFilter + '&user_id=' + userFilter + '&status=' + statusFilter)
                .then(response => response.json())
                .then(data => {
                    // Chuyển đổi dữ liệu từ API sang định dạng FullCalendar
                    var events = data.map(function(booking) {
                        var statusClass = '';
                        switch(booking.status) {
                            case 'được duyệt':
                                statusClass = 'status-approved';
                                break;
                            case 'chờ duyệt':
                                statusClass = 'status-pending';
                                break;
                            case 'từ chối':
                                statusClass = 'status-rejected';
                                break;
                            case 'đã hủy':
                                statusClass = 'status-cancelled';
                                break;
                        }

                        return {
                            id: booking.id,
                            title: booking.room_name + ' - ' + (booking.class_code || ''),
                            start: booking.start_time,
                            end: booking.end_time,
                            className: statusClass,
                            extendedProps: {
                                room_id: booking.room_id,
                                room_name: booking.room_name,
                                user_id: booking.user_id,
                                user_name: booking.user_name,
                                user_role: booking.user_role,
                                class_code: booking.class_code,
                                purpose: booking.purpose,
                                status: booking.status
                            }
                        };
                    });

                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            // Hiển thị modal chi tiết khi click vào sự kiện
            var booking = info.event;
            var props = booking.extendedProps || {};

            // Kiểm tra và gán giá trị mặc định nếu không có dữ liệu
            document.getElementById('modal-room').textContent = props.room_name || 'Không xác định';

            // Hiển thị tên người dùng và vai trò
            let userDisplay = props.user_name || 'Không xác định';
            if (props.user_role) {
                let roleText = '';
                switch(props.user_role) {
                    case 'teacher':
                        roleText = 'Giảng viên';
                        break;
                    case 'student':
                        roleText = 'Sinh viên';
                        break;
                    case 'admin':
                        roleText = 'Quản trị viên';
                        break;
                    default:
                        roleText = props.user_role;
                }
                userDisplay += ' (' + roleText + ')';
            }
            document.getElementById('modal-user').textContent = userDisplay;

            // Định dạng thời gian
            const formatDateTime = (dateTimeStr) => {
                const date = new Date(dateTimeStr);
                return date.toLocaleString('vi-VN', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            };

            document.getElementById('modal-start').textContent = formatDateTime(booking.start);
            document.getElementById('modal-end').textContent = formatDateTime(booking.end);
            document.getElementById('modal-class').textContent = props.class_code || 'Không có';
            document.getElementById('modal-purpose').textContent = props.purpose || 'Không có';

            // Hiển thị trạng thái
            let statusText = props.status || 'Không xác định';
            let statusClass = '';

            switch(statusText) {
                case 'được duyệt':
                    statusClass = 'text-success';
                    statusText = 'Được duyệt';
                    break;
                case 'chờ duyệt':
                    statusClass = 'text-warning';
                    statusText = 'Chờ duyệt';
                    break;
                case 'từ chối':
                    statusClass = 'text-danger';
                    statusText = 'Từ chối';
                    break;
                case 'đã hủy':
                    statusClass = 'text-secondary';
                    statusText = 'Đã hủy';
                    break;
            }

            document.getElementById('modal-status').innerHTML = '<span class="badge bg-' + statusClass.replace('text-', '') + '">' + statusText + '</span>';

            // Thiết lập các nút hành động
            if (document.getElementById('modal-approve-link')) {
                document.getElementById('modal-approve-link').setAttribute('data-id', booking.id);
                document.getElementById('modal-reject-link').setAttribute('data-id', booking.id);
                document.getElementById('modal-delete-link').setAttribute('data-id', booking.id);

                // Ẩn/hiện nút duyệt/từ chối dựa vào trạng thái
                if (props.status === 'chờ duyệt') {
                    document.getElementById('modal-approve-link').style.display = 'inline-block';
                    document.getElementById('modal-reject-link').style.display = 'inline-block';
                } else {
                    document.getElementById('modal-approve-link').style.display = 'none';
                    document.getElementById('modal-reject-link').style.display = 'none';
                }
            }

            if (document.getElementById('modal-cancel-link')) {
                document.getElementById('modal-cancel-link').setAttribute('data-id', booking.id);

                // Chỉ hiển thị nút hủy nếu trạng thái là chờ duyệt hoặc được duyệt
                if (props.status === 'chờ duyệt' || props.status === 'được duyệt') {
                    document.getElementById('modal-cancel-link').style.display = 'inline-block';
                } else {
                    document.getElementById('modal-cancel-link').style.display = 'none';
                }
            }

            // Hiển thị modal
            var modal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
            modal.show();
        },
        dateClick: function(info) {
            // Chuyển đến trang đặt phòng với thời gian đã chọn
            var startTime = info.dateStr;
            var endTime = new Date(new Date(startTime).getTime() + 60*60*1000).toISOString().slice(0, 16);

            // Xác định URL dựa trên vai trò
            var url = '';
            switch ('$role') {
                case 'admin':
                    url = '/pdu_pms_project/public/admin/add_booking';
                    break;
                case 'teacher':
                    url = '/pdu_pms_project/public/teacher/book_room';
                    break;
                case 'student':
                    url = '/pdu_pms_project/public/student/book_room';
                    break;
            }

            window.location.href = url + '?start_time=' + startTime + '&end_time=' + endTime;
        }
    });

    calendar.render();

    // Xử lý các nút tác vụ nhanh
    document.getElementById('today-btn').addEventListener('click', function() {
        calendar.today();
        calendar.changeView('timeGridDay');
    });

    document.getElementById('week-btn').addEventListener('click', function() {
        calendar.today(); // Đặt về ngày hiện tại trước
        calendar.changeView('timeGridWeek');
    });

    document.getElementById('month-btn').addEventListener('click', function() {
        calendar.today(); // Đặt về ngày hiện tại trước
        calendar.changeView('dayGridMonth');
    });

    document.getElementById('pending-btn').addEventListener('click', function() {
        document.getElementById('status-filter').value = 'chờ duyệt';
        calendar.refetchEvents();
    });

    document.getElementById('approved-btn').addEventListener('click', function() {
        document.getElementById('status-filter').value = 'được duyệt';
        calendar.refetchEvents();
    });

    document.getElementById('refresh-btn').addEventListener('click', function() {
        // Reset các bộ lọc
        document.getElementById('room-filter').value = '';
        document.getElementById('status-filter').value = '';
        if (document.getElementById('user-filter')) {
            document.getElementById('user-filter').value = '';
        }
        calendar.refetchEvents();
    });

    // Xử lý sự kiện thay đổi bộ lọc
    document.getElementById('room-filter').addEventListener('change', function() {
        calendar.refetchEvents();
    });

    if (document.getElementById('user-filter')) {
        document.getElementById('user-filter').addEventListener('change', function() {
            calendar.refetchEvents();
        });
    }

    document.getElementById('status-filter').addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // Xử lý các nút hành động trong modal
    if (document.getElementById('modal-delete-link')) {
        document.getElementById('modal-delete-link').addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Bạn có chắc chắn muốn xóa đặt phòng này?')) {
                var bookingId = this.getAttribute('data-id');
                window.location.href = '/pdu_pms_project/public/admin/delete_booking/' + bookingId;
            }
        });
    }

    if (document.getElementById('modal-approve-link')) {
        document.getElementById('modal-approve-link').addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Bạn có chắc chắn muốn duyệt đặt phòng này?')) {
                var bookingId = this.getAttribute('data-id');
                window.location.href = '/pdu_pms_project/public/admin/approve_booking/' + bookingId;
            }
        });
    }

    if (document.getElementById('modal-reject-link')) {
        document.getElementById('modal-reject-link').addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Bạn có chắc chắn muốn từ chối đặt phòng này?')) {
                var bookingId = this.getAttribute('data-id');
                window.location.href = '/pdu_pms_project/public/admin/reject_booking/' + bookingId;
            }
        });
    }

    if (document.getElementById('modal-cancel-link')) {
        document.getElementById('modal-cancel-link').addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Bạn có chắc chắn muốn hủy đặt phòng này?')) {
                var bookingId = this.getAttribute('data-id');
                var cancelUrl = '';

                switch ('$role') {
                    case 'teacher':
                        cancelUrl = '/pdu_pms_project/public/teacher/cancel_booking/';
                        break;
                    case 'student':
                        cancelUrl = '/pdu_pms_project/public/student/cancel_booking/';
                        break;
                }

                window.location.href = cancelUrl + bookingId;
            }
        });
    }
});
EOT;

// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = $role;

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>