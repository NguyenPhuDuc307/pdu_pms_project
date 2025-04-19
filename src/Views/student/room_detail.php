<?php
$pageTitle = "Chi Tiết Phòng Học";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/pdu_pms_project/public/student/dashboard">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/pdu_pms_project/public/student/search_rooms">Tìm kiếm phòng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết phòng học</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if (isset($data['room']) && $data['room']): ?>
        <div class="row">
            <!-- Thông tin phòng -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Thông tin phòng học</h5>
                        <span class="badge <?php echo $data['room']['status'] === 'trống' ? 'bg-success' : ($data['room']['status'] === 'đã đặt' ? 'bg-danger' : 'bg-warning'); ?>">
                            <?php echo htmlspecialchars($data['room']['status']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-2 text-center">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                    <i class="fas fa-door-open fa-3x text-primary"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h2 class="mb-1"><?php echo htmlspecialchars($data['room']['name']); ?></h2>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($data['room']['location']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-tags text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Loại phòng</h6>
                                                <p class="mb-0 fw-bold"><?php echo htmlspecialchars($data['room']['room_type_name']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-users text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Sức chứa</h6>
                                                <p class="mb-0 fw-bold"><?php echo htmlspecialchars($data['room']['capacity']); ?> người</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3">Thông tin chi tiết</h5>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 200px;">Mã phòng:</th>
                                        <td><?php echo htmlspecialchars($data['room']['id']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tên phòng:</th>
                                        <td><?php echo htmlspecialchars($data['room']['name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Loại phòng:</th>
                                        <td><?php echo htmlspecialchars($data['room']['room_type_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Sức chứa:</th>
                                        <td><?php echo htmlspecialchars($data['room']['capacity']); ?> người</td>
                                    </tr>
                                    <tr>
                                        <th>Vị trí:</th>
                                        <td><?php echo htmlspecialchars($data['room']['location']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Trạng thái:</th>
                                        <td>
                                            <span class="badge <?php echo $data['room']['status'] === 'trống' ? 'bg-success' : ($data['room']['status'] === 'đã đặt' ? 'bg-danger' : 'bg-warning'); ?>">
                                                <?php echo htmlspecialchars($data['room']['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if (!empty($data['room']['notes'])): ?>
                                        <tr>
                                            <th>Ghi chú:</th>
                                            <td><?php echo nl2br(htmlspecialchars($data['room']['notes'])); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <?php if ($data['room']['status'] === 'trống'): ?>
                                <a href="/pdu_pms_project/public/student/book_room/<?php echo $data['room']['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-2"></i>Đặt phòng này
                                </a>
                            <?php endif; ?>
                            <a href="/pdu_pms_project/public/maintenance/create?room_id=<?php echo $data['room']['id']; ?>" class="btn btn-outline-warning">
                                <i class="fas fa-tools me-2"></i>Yêu cầu sửa chữa
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Danh sách thiết bị trong phòng -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-desktop me-2"></i>Danh sách thiết bị</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['room']['equipment'])): ?>
                            <div class="alert alert-info">
                                Không có thông tin về thiết bị trong phòng học này.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tên thiết bị</th>
                                            <th>Số lượng</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày bảo trì gần nhất</th>
                                            <th>Ngày bảo trì tiếp theo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['room']['equipment'] as $equipment): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php
                                                        $icon = 'desktop';
                                                        switch (strtolower($equipment['name'])) {
                                                            case 'máy chiếu':
                                                                $icon = 'projector';
                                                                break;
                                                            case 'máy in':
                                                                $icon = 'print';
                                                                break;
                                                            case 'micro':
                                                                $icon = 'microphone';
                                                                break;
                                                            case 'bảng tương tác':
                                                                $icon = 'chalkboard';
                                                                break;
                                                        }
                                                        ?>
                                                        <i class="fas fa-<?php echo $icon; ?> me-2 text-muted"></i>
                                                        <?php echo htmlspecialchars($equipment['name']); ?>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($equipment['quantity']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $equipment['status'] === 'hoạt động' ? 'bg-success' : ($equipment['status'] === 'bảo trì' ? 'bg-warning' : 'bg-danger'); ?>">
                                                        <?php echo htmlspecialchars($equipment['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $equipment['last_maintenance'] ? date('d/m/Y', strtotime($equipment['last_maintenance'])) : 'N/A'; ?></td>
                                                <td><?php echo $equipment['next_maintenance'] ? date('d/m/Y', strtotime($equipment['next_maintenance'])) : 'N/A'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Thông tin bên phải -->
            <div class="col-md-4">
                <!-- Các khung giờ sắp tới -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lịch sắp tới</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['upcomingClasses'])): ?>
                            <div class="alert alert-info">
                                Không có lớp học nào sắp diễn ra trong phòng học này.
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($data['upcomingClasses'] as $class): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($class['class_code']); ?></span>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y', strtotime($class['start_time'])); ?>
                                            </small>
                                        </div>
                                        <p class="fw-semibold mb-1">
                                            <?php echo $class['teacher_name'] ? htmlspecialchars($class['teacher_name']) : ($class['student_name'] ? htmlspecialchars($class['student_name']) : 'N/A'); ?>
                                        </p>
                                        <p class="text-muted small mb-0">
                                            <?php echo date('H:i', strtotime($class['start_time'])); ?> -
                                            <?php echo date('H:i', strtotime($class['end_time'])); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Khung giờ trống -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Khung giờ trống</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['availableSlots'])): ?>
                            <div class="alert alert-info">
                                Không có thông tin về khung giờ trống cho phòng học này.
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($data['availableSlots'] as $slot): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0 fw-semibold"><?php echo date('d/m/Y', strtotime($slot['date'])); ?></p>
                                                <p class="mb-0 text-muted small">
                                                    <?php echo date('H:i', strtotime($slot['start_time'])); ?> -
                                                    <?php echo date('H:i', strtotime($slot['end_time'])); ?>
                                                </p>
                                            </div>
                                            <a href="/pdu_pms_project/public/student/book_room/<?php echo $data['room']['id']; ?>?start=<?php echo urlencode($slot['start_time']); ?>&end=<?php echo urlencode($slot['end_time']); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Báo cáo vấn đề -->
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Báo cáo vấn đề</h5>
                    </div>
                    <div class="card-body">
                        <form action="/pdu_pms_project/public/student/report_issue" method="post">
                            <input type="hidden" name="room_id" value="<?php echo $data['room']['id']; ?>">

                            <div class="mb-3">
                                <label for="issue_type" class="form-label">Loại vấn đề</label>
                                <select class="form-select" id="issue_type" name="issue_type" required>
                                    <option value="" selected disabled>Chọn loại vấn đề</option>
                                    <option value="thiết bị">Vấn đề về thiết bị</option>
                                    <option value="cơ sở vật chất">Vấn đề về cơ sở vật chất</option>
                                    <option value="khác">Vấn đề khác</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="equipment_id" class="form-label">Thiết bị gặp vấn đề</label>
                                <select class="form-select" id="equipment_id" name="equipment_id">
                                    <option value="" selected>Chọn thiết bị (nếu có)</option>
                                    <?php if (!empty($data['room']['equipment'])): ?>
                                        <?php foreach ($data['room']['equipment'] as $equipment): ?>
                                            <option value="<?php echo $equipment['id']; ?>">
                                                <?php echo htmlspecialchars($equipment['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả vấn đề</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">Mức độ ưu tiên</label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="thấp">Thấp</option>
                                    <option value="trung bình" selected>Trung bình</option>
                                    <option value="cao">Cao</option>
                                    <option value="khẩn cấp">Khẩn cấp</option>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi báo cáo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            Không tìm thấy thông tin phòng học.
        </div>
    <?php endif; ?>
</div>

<script>
    // Hide equipment selection if issue type is not about equipment
    document.addEventListener('DOMContentLoaded', function() {
        const issueTypeSelect = document.getElementById('issue_type');
        const equipmentSelect = document.getElementById('equipment_id');
        const equipmentContainer = equipmentSelect.closest('.mb-3');

        issueTypeSelect.addEventListener('change', function() {
            if (this.value === 'thiết bị') {
                equipmentContainer.style.display = 'block';
                equipmentSelect.setAttribute('required', 'required');
            } else {
                equipmentContainer.style.display = 'none';
                equipmentSelect.removeAttribute('required');
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>