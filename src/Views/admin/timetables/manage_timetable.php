<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Quản lý lịch dạy";
$pageSubtitle = "Quản lý thời khóa biểu và lịch dạy của giảng viên";
$pageIcon = "fas fa-calendar-alt";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý lịch dạy', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <?php include dirname(dirname(__DIR__)) . '/components/page_header.php'; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3 text-end">
                <a href="/pdu_pms_project/public/admin/add_timetable" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Thêm lịch học
                </a>
                <a href="/pdu_pms_project/public/admin/auto_schedule" class="btn btn-info">
                    <i class="fas fa-magic me-1"></i> Tự động xếp lịch
                </a>
            </div>

            <form action="/pdu_pms_project/public/admin/manage_timetables" method="get" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="department">Khoa</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">Tất cả</option>
                                <?php if (isset($data['departments']) && is_array($data['departments'])): ?>
                                    <?php foreach ($data['departments'] as $dept): ?>
                                        <option value="<?= htmlspecialchars($dept['id'] ?? '') ?>" <?= (isset($_GET['department']) && $_GET['department'] == $dept['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="semester">Học kỳ</label>
                            <select class="form-select" id="semester" name="semester">
                                <option value="">Tất cả</option>
                                <?php if (isset($data['semesters']) && is_array($data['semesters'])): ?>
                                    <?php foreach ($data['semesters'] as $sem): ?>
                                        <option value="<?= htmlspecialchars($sem['id'] ?? '') ?>" <?= (isset($_GET['semester']) && $_GET['semester'] == $sem['id']) ? 'selected' : '' ?>><?= htmlspecialchars($sem['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="day_of_week">Ngày trong tuần</label>
                            <select class="form-select" id="day_of_week" name="day_of_week">
                                <option value="">Tất cả</option>
                                <option value="1" <?= (isset($_GET['day_of_week']) && $_GET['day_of_week'] == '1') ? 'selected' : '' ?>>Thứ 2</option>
                                <option value="2" <?= (isset($_GET['day_of_week']) && $_GET['day_of_week'] == '2') ? 'selected' : '' ?>>Thứ 3</option>
                                <option value="3" <?= (isset($_GET['day_of_week']) && $_GET['day_of_week'] == '3') ? 'selected' : '' ?>>Thứ 4</option>
                                <option value="4" <?= (isset($_GET['day_of_week']) && $_GET['day_of_week'] == '4') ? 'selected' : '' ?>>Thứ 5</option>
                                <option value="5" <?= (isset($_GET['day_of_week']) && $_GET['day_of_week'] == '5') ? 'selected' : '' ?>>Thứ 6</option>
                                <option value="6" <?= (isset($_GET['day_of_week']) && $_GET['day_of_week'] == '6') ? 'selected' : '' ?>>Thứ 7</option>
                                <option value="0" <?= (isset($_GET['day_of_week']) && $_GET['day_of_week'] == '0') ? 'selected' : '' ?>>Chủ nhật</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="class_code">Mã lớp</label>
                            <input type="text" class="form-control" id="class_code" name="class_code" value="<?= isset($_GET['class_code']) ? htmlspecialchars($_GET['class_code']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="teacher">Giảng viên</label>
                            <select class="form-select" id="teacher" name="teacher">
                                <option value="">Tất cả</option>
                                <?php if (isset($data['teachers']) && is_array($data['teachers'])): ?>
                                    <?php foreach ($data['teachers'] as $teacher): ?>
                                        <option value="<?= htmlspecialchars($teacher['id'] ?? '') ?>" <?= (isset($_GET['teacher']) && $_GET['teacher'] == $teacher['id']) ? 'selected' : '' ?>><?= htmlspecialchars($teacher['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Tìm kiếm
                        </button>
                        <a href="/pdu_pms_project/public/admin/manage_timetables" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover datatable" id="timetablesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Mã lớp</th>
                            <th>Tên môn học</th>
                            <th>Giảng viên</th>
                            <th>Phòng</th>
                            <th>Ngày</th>
                            <th>Thời gian</th>
                            <th>Số học viên</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['timetables']) && is_array($data['timetables'])): ?>
                            <?php foreach ($data['timetables'] as $timetable): ?>
                                <?php
                                // Xác định ngày từ start_time
                                $startDate = isset($timetable['start_time']) ? new DateTime($timetable['start_time']) : null;
                                $endDate = isset($timetable['end_time']) ? new DateTime($timetable['end_time']) : null;

                                // Xác định ngày trong tuần
                                $dayOfWeek = $startDate ? $startDate->format('w') : null;
                                $dayNames = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                                $dayName = isset($dayNames[$dayOfWeek]) ? $dayNames[$dayOfWeek] : 'Không xác định';

                                // Định dạng ngày tháng
                                $formattedDate = $startDate ? $startDate->format('d/m/Y') : '';

                                // Định dạng thời gian
                                $startTime = $startDate ? $startDate->format('H:i') : '';
                                $endTime = $endDate ? $endDate->format('H:i') : '';

                                // Xác định trạng thái phòng
                                $roomStatus = '';
                                if (empty($timetable['room_name'])) {
                                    $roomStatus = '<span class="badge bg-warning">Chưa xếp phòng</span>';
                                } else {
                                    $roomStatus = '<span class="badge bg-success">Đã xếp phòng</span>';
                                }
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($timetable['class_code'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($timetable['subject'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($timetable['teacher_name'] ?? '') ?></td>
                                    <td>
                                        <?php if (!empty($timetable['room_name'])): ?>
                                            <span class="badge bg-info"><?= htmlspecialchars($timetable['room_name'] ?? '') ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa xếp phòng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold"><?= $dayName ?></span><br>
                                        <small class="text-muted"><?= $formattedDate ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?= $startTime ?> - <?= $endTime ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= intval($timetable['participants'] ?? 0) ?> học viên</span>
                                    </td>
                                    <td><?= $roomStatus ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/pdu_pms_project/public/admin/edit_timetable/<?= $timetable['id'] ?? '' ?>" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="confirmDelete(<?= $timetable['id'] ?? 0 ?>, '<?= htmlspecialchars($timetable['class_code'] ?? '') ?>')" class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php if (empty($timetable['room_name'])): ?>
                                                <a href="/pdu_pms_project/public/admin/auto_schedule?timetable_id=<?= $timetable['id'] ?? '' ?>" class="btn btn-sm btn-success" title="Tự động xếp phòng">
                                                    <i class="fas fa-magic"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Không có lịch dạy nào được tìm thấy</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, classCode) {
        if (confirm('Bạn có chắc chắn muốn xóa lịch học lớp "' + classCode + '"?')) {
            window.location.href = '/pdu_pms_project/public/admin/delete_timetable/' + id;
        }
    }
</script>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(dirname(__DIR__))) . '/Views/layouts/main_layout.php';
?>