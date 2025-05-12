<?php
// Đảm bảo người dùng đã đăng nhập với vai trò admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Thiết lập thông tin cho page_header
$pageTitle = "Quản lý yêu cầu bảo trì";
$pageSubtitle = "Quản lý và xử lý các yêu cầu bảo trì thiết bị và phòng học";
$pageIcon = "fas fa-wrench";
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Admin', 'link' => '/pdu_pms_project/public/admin'],
    ['title' => 'Quản lý yêu cầu bảo trì', 'link' => '']
];

// Bắt đầu output buffering
ob_start();
?>

<div class="container-fluid">
    <!-- Page Header -->
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <!-- Thống kê yêu cầu bảo trì -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số yêu cầu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= isset($data['requests']) ? count($data['requests']) : 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Đang chờ xử lý</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $pendingCount = 0;
                                if (isset($data['requests'])) {
                                    foreach ($data['requests'] as $req) {
                                        if ($req['status'] === 'đang chờ') $pendingCount++;
                                    }
                                }
                                echo $pendingCount;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Đang xử lý</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $inProgressCount = 0;
                                if (isset($data['requests'])) {
                                    foreach ($data['requests'] as $req) {
                                        if ($req['status'] === 'đang xử lý') $inProgressCount++;
                                    }
                                }
                                echo $inProgressCount;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hoàn thành</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $completedCount = 0;
                                if (isset($data['requests'])) {
                                    foreach ($data['requests'] as $req) {
                                        if ($req['status'] === 'đã xử lý') $completedCount++;
                                    }
                                }
                                echo $completedCount;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê so sánh theo tháng -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Thống kê yêu cầu bảo trì theo tháng</h6>
            <div>
                <select id="chartTypeSelector" class="form-select form-select-sm d-inline-block w-auto me-2">
                    <option value="bar">Biểu đồ cột</option>
                    <option value="line">Biểu đồ đường</option>
                    <option value="doughnut">Biểu đồ tròn</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-area mb-4">
                <canvas id="maintenanceMonthlyChart" style="min-height: 300px;"></canvas>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="maintenanceMonthlyTable">
                    <thead class="table-light">
                        <tr>
                            <th>Tháng</th>
                            <th>Tổng số</th>
                            <th>Đang chờ</th>
                            <th>Đang xử lý</th>
                            <th>Hoàn thành</th>
                            <th>Từ chối</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['monthly_comparison']) && count($data['monthly_comparison']) > 0): ?>
                            <?php foreach($data['monthly_comparison'] as $monthly): ?>
                                <?php 
                                    $monthYear = explode('-', $monthly['month_year']);
                                    $monthName = date('m/Y', strtotime($monthly['month_year'] . '-01'));
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($monthName) ?></td>
                                    <td><?= htmlspecialchars($monthly['total_count']) ?></td>
                                    <td><?= htmlspecialchars($monthly['pending_count']) ?></td>
                                    <td><?= htmlspecialchars($monthly['in_progress_count']) ?></td>
                                    <td><?= htmlspecialchars($monthly['completed_count']) ?></td>
                                    <td><?= htmlspecialchars($monthly['rejected_count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có dữ liệu thống kê</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Danh sách yêu cầu bảo trì -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách yêu cầu bảo trì</h6>
            <div>
                <select id="statusFilter" class="form-select form-select-sm d-inline-block w-auto me-2">
                    <option value="">Tất cả trạng thái</option>
                    <option value="đang chờ">Đang chờ</option>
                    <option value="đang xử lý">Đang xử lý</option>
                    <option value="đã xử lý">Đã xử lý</option>
                    <option value="từ chối">Đã hủy</option>
                </select>
                <select id="priorityFilter" class="form-select form-select-sm d-inline-block w-auto">
                    <option value="">Tất cả mức độ ưu tiên</option>
                    <option value="thấp">Thấp</option>
                    <option value="trung bình">Trung bình</option>
                    <option value="cao">Cao</option>
                    <option value="khẩn cấp">Khẩn cấp</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Phòng</th>
                            <th>Thiết bị</th>
                            <th>Người yêu cầu</th>
                            <th>Mô tả</th>
                            <th>Mức độ ưu tiên</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['requests']) && is_array($data['requests'])): ?>
                            <?php foreach ($data['requests'] as $request): ?>
                                <tr>
                                    <td><?= htmlspecialchars($request['id']) ?></td>
                                    <td><?= htmlspecialchars($request['room_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($request['equipment_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($request['requester_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($request['issue_description'] ?? 'Không có mô tả') ?></td>
                                    <td>
                                        <span class="badge bg-<?= getPriorityBadgeColor($request['priority']) ?>">
                                            <?= htmlspecialchars(getPriorityLabel($request['priority'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getStatusBadgeColor($request['status']) ?>">
                                            <?= htmlspecialchars(getStatusLabel($request['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($request['created_at']) ?></td>
                                    <td>
                                        <a href="/pdu_pms_project/public/admin/view_request?id=<?= $request['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-primary update-status-btn"
                                            data-id="<?= $request['id'] ?>"
                                            data-current-status="<?= $request['status'] ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updateStatusModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-request-btn"
                                            data-id="<?= $request['id'] ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteRequestModal">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Không có yêu cầu bảo trì nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cập Nhật Trạng Thái -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Cập nhật trạng thái yêu cầu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm" action="/pdu_pms_project/public/admin/update_request_status" method="post">
                    <input type="hidden" id="request_id" name="id">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="đang chờ">Đang chờ</option>
                            <option value="đang xử lý">Đang xử lý</option>
                            <option value="đã xử lý">Đã xử lý</option>
                            <option value="từ chối">Từ chối</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="updateStatusForm" class="btn btn-primary">Cập nhật</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xóa Yêu Cầu -->
<div class="modal fade" id="deleteRequestModal" tabindex="-1" aria-labelledby="deleteRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRequestModalLabel">Xác nhận xóa yêu cầu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa yêu cầu bảo trì này?</p>
                <p class="text-danger">Lưu ý: Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý sự kiện khi nhấn nút cập nhật trạng thái
        document.querySelectorAll('.update-status-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const currentStatus = this.getAttribute('data-current-status');

                document.getElementById('request_id').value = id;
                document.getElementById('status').value = currentStatus;
            });
        });

        // Xử lý sự kiện khi nhấn nút xóa yêu cầu
        document.querySelectorAll('.delete-request-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('confirmDeleteBtn').href = '/pdu_pms_project/public/admin/delete_request?id=' + id;
            });
        });

        // Xử lý lọc theo trạng thái
        document.getElementById('statusFilter').addEventListener('change', function() {
            filterTable();
        });

        // Xử lý lọc theo mức độ ưu tiên
        document.getElementById('priorityFilter').addEventListener('change', function() {
            filterTable();
        });

        function filterTable() {
            const statusFilter = document.getElementById('statusFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;

            const table = document.querySelector('.datatable');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function(row) {
                let showRow = true;

                if (statusFilter) {
                    const statusCell = row.querySelector('td:nth-child(7)');
                    if (statusCell && !statusCell.textContent.toLowerCase().includes(statusFilter)) {
                        showRow = false;
                    }
                }

                if (priorityFilter) {
                    const priorityCell = row.querySelector('td:nth-child(6)');
                    if (priorityCell && !priorityCell.textContent.toLowerCase().includes(priorityFilter)) {
                        showRow = false;
                    }
                }

                row.style.display = showRow ? '' : 'none';
            });
        }
    });
</script>

<script>
// Monthly maintenance chart initialization
document.addEventListener('DOMContentLoaded', function() {
    // Chart configuration
    initializeMaintenanceMonthlyChart();
    
    // Handle chart type change
    document.getElementById('chartTypeSelector').addEventListener('change', function() {
        initializeMaintenanceMonthlyChart(this.value);
    });
});

// Function to initialize the maintenance monthly chart
function initializeMaintenanceMonthlyChart(chartType = 'bar') {
    const ctx = document.getElementById('maintenanceMonthlyChart').getContext('2d');
    
    // Check if chart already exists and destroy it
    if (window.maintenanceChart) {
        window.maintenanceChart.destroy();
    }
    
    // Prepare data from PHP
    <?php if (isset($data['monthly_comparison']) && count($data['monthly_comparison']) > 0): ?>
        const labels = [<?php 
            $labelOutput = [];
            foreach($data['monthly_comparison'] as $monthly) {
                $monthYear = explode('-', $monthly['month_year']);
                $monthName = date('m/Y', strtotime($monthly['month_year'] . '-01'));
                $labelOutput[] = "'" . $monthName . "'";
            }
            echo implode(', ', $labelOutput);
        ?>];
        
        const totalData = [<?php 
            $dataOutput = [];
            foreach($data['monthly_comparison'] as $monthly) {
                $dataOutput[] = $monthly['total_count'];
            }
            echo implode(', ', $dataOutput);
        ?>];
        
        const pendingData = [<?php 
            $dataOutput = [];
            foreach($data['monthly_comparison'] as $monthly) {
                $dataOutput[] = $monthly['pending_count'];
            }
            echo implode(', ', $dataOutput);
        ?>];
        
        const inProgressData = [<?php 
            $dataOutput = [];
            foreach($data['monthly_comparison'] as $monthly) {
                $dataOutput[] = $monthly['in_progress_count'];
            }
            echo implode(', ', $dataOutput);
        ?>];
        
        const completedData = [<?php 
            $dataOutput = [];
            foreach($data['monthly_comparison'] as $monthly) {
                $dataOutput[] = $monthly['completed_count'];
            }
            echo implode(', ', $dataOutput);
        ?>];
        
        const rejectedData = [<?php 
            $dataOutput = [];
            foreach($data['monthly_comparison'] as $monthly) {
                $dataOutput[] = $monthly['rejected_count'];
            }
            echo implode(', ', $dataOutput);
        ?>];
    <?php else: ?>
        const labels = [];
        const totalData = [];
        const pendingData = [];
        const inProgressData = [];
        const completedData = [];
        const rejectedData = [];
    <?php endif; ?>
    
    // Create chart datasets
    const datasets = [];
    
    if (chartType === 'doughnut') {
        // For doughnut/pie charts, we need a different data structure
        datasets.push({
            label: 'Tổng số',
            data: totalData.reduce((sum, val) => sum + parseInt(val), 0),
            backgroundColor: 'rgba(78, 115, 223, 0.8)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1
        });
        
        // More meaningful to show status distribution in a doughnut chart
        const totalCounts = {
            'Đang chờ': pendingData.reduce((sum, val) => sum + parseInt(val), 0),
            'Đang xử lý': inProgressData.reduce((sum, val) => sum + parseInt(val), 0),
            'Hoàn thành': completedData.reduce((sum, val) => sum + parseInt(val), 0),
            'Từ chối': rejectedData.reduce((sum, val) => sum + parseInt(val), 0)
        };
        
        // Convert to chart format
        const doughnutLabels = Object.keys(totalCounts);
        const doughnutData = Object.values(totalCounts);
        const doughnutColors = [
            'rgba(255, 193, 7, 0.8)',   // warning - yellow
            'rgba(23, 162, 184, 0.8)',  // info - blue
            'rgba(40, 167, 69, 0.8)',   // success - green
            'rgba(220, 53, 69, 0.8)'    // danger - red
        ];
        
        // Create new chart
        window.maintenanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: doughnutLabels,
                datasets: [{
                    data: doughnutData,
                    backgroundColor: doughnutColors,
                    borderColor: doughnutColors.map(color => color.replace('0.8', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Phân bổ trạng thái yêu cầu bảo trì'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else {
        // For bar and line charts
        datasets.push({
            label: 'Tổng số',
            data: totalData,
            backgroundColor: 'rgba(78, 115, 223, 0.8)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1,
            tension: 0.4,
            fill: chartType === 'line' ? false : true
        });
        
        datasets.push({
            label: 'Đang chờ',
            data: pendingData,
            backgroundColor: 'rgba(255, 193, 7, 0.8)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1,
            tension: 0.4,
            fill: chartType === 'line' ? false : true
        });
        
        datasets.push({
            label: 'Đang xử lý',
            data: inProgressData,
            backgroundColor: 'rgba(23, 162, 184, 0.8)',
            borderColor: 'rgba(23, 162, 184, 1)',
            borderWidth: 1,
            tension: 0.4,
            fill: chartType === 'line' ? false : true
        });
        
        datasets.push({
            label: 'Hoàn thành',
            data: completedData,
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1,
            tension: 0.4,
            fill: chartType === 'line' ? false : true
        });
        
        datasets.push({
            label: 'Từ chối',
            data: rejectedData,
            backgroundColor: 'rgba(220, 53, 69, 0.8)',
            borderColor: 'rgba(220, 53, 69, 1)',
            borderWidth: 1,
            tension: 0.4,
            fill: chartType === 'line' ? false : true
        });
        
        // Create new chart
        window.maintenanceChart = new Chart(ctx, {
            type: chartType,
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'So sánh yêu cầu bảo trì theo tháng'
                    }
                }
            }
        });
    }
}
</script>

<?php
// Helper functions for display
function getPriorityLabel($priority)
{
    switch ($priority) {
        case 'thấp':
            return 'Thấp';
        case 'trung bình':
            return 'Trung bình';
        case 'cao':
            return 'Cao';
        case 'khẩn cấp':
            return 'Khẩn cấp';
        default:
            return 'Không xác định';
    }
}

function getPriorityBadgeColor($priority)
{
    switch ($priority) {
        case 'thấp':
            return 'success';
        case 'trung bình':
            return 'info';
        case 'cao':
            return 'warning';
        case 'khẩn cấp':
            return 'danger';
        default:
            return 'secondary';
    }
}

function getStatusLabel($status)
{
    switch ($status) {
        case 'đang chờ':
            return 'Đang chờ';
        case 'đang xử lý':
            return 'Đang xử lý';
        case 'đã xử lý':
            return 'Đã xử lý';
        case 'từ chối':
            return 'Từ chối';
        default:
            return 'Không xác định';
    }
}

function getStatusBadgeColor($status)
{
    switch ($status) {
        case 'đang chờ':
            return 'warning';
        case 'đang xử lý':
            return 'info';
        case 'đã xử lý':
            return 'success';
        case 'từ chối':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>

<?php
// Lấy nội dung đã buffer
$pageContent = ob_get_clean();

// Set page role
$pageRole = 'admin';

// Include the main layout
include dirname(dirname(__DIR__)) . '/Views/layouts/main_layout.php';
?>