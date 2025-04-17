<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Lịch đặt phòng";
$pageSubtitle = "Xem lịch đặt phòng theo dạng lịch";
$pageIcon = "fas fa-calendar-alt";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý đặt phòng', 'link' => '/pdu_pms_project/public/admin/manage_bookings'],
    ['title' => 'Lịch đặt phòng', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group">
            <a href="/pdu_pms_project/public/admin/manage_bookings" class="btn btn-outline-primary">
                <i class="fas fa-table me-1"></i> Dạng bảng
            </a>
            <a href="/pdu_pms_project/public/admin/calendar_bookings" class="btn btn-primary active">
                <i class="fas fa-calendar-alt me-1"></i> Dạng lịch
            </a>
        </div>
        <a href="/pdu_pms_project/public/admin/add_booking" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Thêm đặt phòng
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row mb-3">
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
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                        <select id="user-filter" class="form-select">
                            <option value="">Tất cả người dùng</option>
                            <?php if (isset($data['users']) && is_array($data['users'])): ?>
                                <?php foreach ($data['users'] as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= $user['role'] ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-check-circle text-primary"></i></span>
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
                        <label class="fw-bold">Trạng thái:</label>
                        <span id="modal-status"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="modal-edit-link" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa
                    </a>
                    <a href="#" id="modal-delete-link" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Xóa
                    </a>
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
    height: 900px;
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
        locale: 'vi',
        timeZone: 'local',
        allDaySlot: false,
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',
        slotDuration: '00:30:00',
        height: 'auto',
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
            // Lấy các bộ lọc
            var roomFilter = document.getElementById('room-filter').value;
            var userFilter = document.getElementById('user-filter').value;
            var statusFilter = document.getElementById('status-filter').value;
            
            // Gọi API để lấy dữ liệu
            fetch('/pdu_pms_project/public/admin/get_bookings_json?start=' + info.startStr + '&end=' + info.endStr + 
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
                            title: booking.room_name + ' - ' + (booking.teacher_name || booking.student_name),
                            start: booking.start_time,
                            end: booking.end_time,
                            extendedProps: {
                                room_name: booking.room_name,
                                user_name: booking.teacher_name || booking.student_name,
                                class_code: booking.class_code,
                                status: booking.status,
                                room_id: booking.room_id,
                                teacher_id: booking.teacher_id,
                                student_id: booking.student_id
                            },
                            classNames: [statusClass]
                        };
                    });
                    
                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error fetching bookings:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            // Hiển thị modal chi tiết khi click vào sự kiện
            var booking = info.event;
            
            document.getElementById('modal-room').textContent = booking.extendedProps.room_name;
            document.getElementById('modal-user').textContent = booking.extendedProps.user_name;
            document.getElementById('modal-start').textContent = new Date(booking.start).toLocaleString('vi-VN');
            document.getElementById('modal-end').textContent = new Date(booking.end).toLocaleString('vi-VN');
            document.getElementById('modal-class').textContent = booking.extendedProps.class_code || 'Không có';
            
            // Hiển thị trạng thái
            var statusText = '';
            var statusClass = '';
            switch(booking.extendedProps.status) {
                case 'được duyệt':
                    statusText = 'Được duyệt';
                    statusClass = 'text-success';
                    break;
                case 'chờ duyệt':
                    statusText = 'Chờ duyệt';
                    statusClass = 'text-warning';
                    break;
                case 'từ chối':
                    statusText = 'Từ chối';
                    statusClass = 'text-danger';
                    break;
                case 'đã hủy':
                    statusText = 'Đã hủy';
                    statusClass = 'text-secondary';
                    break;
            }
            
            document.getElementById('modal-status').textContent = statusText;
            document.getElementById('modal-status').className = statusClass;
            
            // Cập nhật link chỉnh sửa và xóa
            document.getElementById('modal-edit-link').href = '/pdu_pms_project/public/admin/edit_booking/' + booking.id;
            document.getElementById('modal-delete-link').setAttribute('data-id', booking.id);
            
            // Hiển thị modal
            var bookingDetailModal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
            bookingDetailModal.show();
        },
        dateClick: function(info) {
            // Chuyển đến trang thêm đặt phòng với thời gian đã chọn
            var startTime = info.dateStr;
            var endTime = new Date(new Date(startTime).getTime() + 60*60*1000).toISOString().slice(0, 16);
            window.location.href = '/pdu_pms_project/public/admin/add_booking?start_time=' + startTime + '&end_time=' + endTime;
        }
    });
    
    calendar.render();
    
    // Xử lý sự kiện thay đổi bộ lọc
    document.getElementById('room-filter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    document.getElementById('user-filter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    document.getElementById('status-filter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    // Xử lý xóa đặt phòng
    document.getElementById('modal-delete-link').addEventListener('click', function(e) {
        e.preventDefault();
        
        if (confirm('Bạn có chắc chắn muốn xóa đặt phòng này?')) {
            var bookingId = this.getAttribute('data-id');
            window.location.href = '/pdu_pms_project/public/admin/delete_booking/' + bookingId;
        }
    });
});
EOT;

// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>