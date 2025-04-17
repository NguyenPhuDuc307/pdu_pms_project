<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="content-wrapper" style="position: relative; z-index: 5; margin: 0; padding: 0; height: auto; overflow: visible;">
    <!-- Hero Section -->
    <section class="bg-primary text-white mb-5" style="position: relative; z-index: 5; margin: 0; padding: 0;">
        <div class="container py-5" style="padding-top: 100px !important;">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-3">Hệ thống Quản lý Phòng Đào tạo</h1>
                    <p class="lead mb-4">Giải pháp quản lý phòng học và tài nguyên giáo dục hiệu quả, giúp tối ưu hóa việc sử dụng không gian và thiết bị học tập.</p>
                    <div class="d-flex gap-3">
                        <a href="/pdu_pms_project/public/login" class="btn btn-light btn-lg px-4">Đăng nhập</a>
                        <a href="/pdu_pms_project/public/register" class="btn btn-outline-light btn-lg px-4">Đăng ký</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" class="img-fluid rounded shadow" alt="Modern Classroom">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" style="position: relative; z-index: 10;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Tính năng chính</h2>
                <p class="lead text-muted">Hệ thống của chúng tôi cung cấp nhiều tính năng mạnh mẽ</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white mb-3">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </div>
                            <h5 class="card-title">Đặt phòng dễ dàng</h5>
                            <p class="card-text">Tìm kiếm và đặt phòng học một cách nhanh chóng, tiện lợi với giao diện thân thiện người dùng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white mb-3">
                                <i class="fas fa-desktop fa-2x"></i>
                            </div>
                            <h5 class="card-title">Quản lý thiết bị</h5>
                            <p class="card-text">Theo dõi và quản lý thiết bị giảng dạy, đảm bảo tài nguyên được sử dụng hiệu quả.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white mb-3">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <h5 class="card-title">Báo cáo thống kê</h5>
                            <p class="card-text">Tạo báo cáo chi tiết về việc sử dụng phòng và thiết bị, giúp đưa ra quyết định dựa trên dữ liệu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- User Roles Section -->
    <section class="py-5 bg-light" style="position: relative; z-index: 10;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Phân quyền người dùng</h2>
                <p class="lead text-muted">Hệ thống phân quyền rõ ràng cho từng đối tượng</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title d-flex align-items-center">
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-user-shield"></i>
                                </span>
                                Quản trị viên
                            </h5>
                            <p class="card-text">Quản lý toàn bộ hệ thống, phê duyệt đặt phòng, quản lý người dùng và thiết lập các tham số hệ thống.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title d-flex align-items-center">
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </span>
                                Giảng viên
                            </h5>
                            <p class="card-text">Tìm kiếm và đặt phòng học, quản lý lịch dạy, báo cáo sự cố thiết bị và theo dõi lịch sử đặt phòng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title d-flex align-items-center">
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-user-graduate"></i>
                                </span>
                                Sinh viên
                            </h5>
                            <p class="card-text">Xem lịch học, tìm kiếm phòng trống, gửi yêu cầu đặt phòng học nhóm và báo cáo vấn đề về cơ sở vật chất.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5" style="position: relative; z-index: 10;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Phản hồi từ người dùng</h2>
                <p class="lead text-muted">Những đánh giá từ người dùng hệ thống</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="testimonial-card p-4 border rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="testimonial-avatar me-3 bg-primary text-white rounded-circle">NT</div>
                            <div>
                                <h5 class="mb-0">Nguyễn Thành</h5>
                                <p class="text-muted mb-0">Giảng viên</p>
                            </div>
                        </div>
                        <p class="mb-0">"Hệ thống giúp tôi tiết kiệm rất nhiều thời gian trong việc tìm kiếm và đặt phòng học. Giao diện dễ sử dụng và nhiều tính năng hữu ích."</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="testimonial-card p-4 border rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="testimonial-avatar me-3 bg-success text-white rounded-circle">TL</div>
                            <div>
                                <h5 class="mb-0">Trần Linh</h5>
                                <p class="text-muted mb-0">Quản trị viên</p>
                            </div>
                        </div>
                        <p class="mb-0">"Với tư cách là quản trị viên, hệ thống này đã giúp tôi theo dõi và quản lý việc sử dụng phòng học hiệu quả hơn nhiều so với cách làm thủ công trước đây."</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="testimonial-card p-4 border rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="testimonial-avatar me-3 bg-info text-white rounded-circle">PH</div>
                            <div>
                                <h5 class="mb-0">Phạm Hương</h5>
                                <p class="text-muted mb-0">Sinh viên</p>
                            </div>
                        </div>
                        <p class="mb-0">"Tôi rất thích việc có thể dễ dàng tìm phòng trống để học nhóm. Hệ thống đơn giản, dễ sử dụng và tiết kiệm thời gian cho chúng tôi rất nhiều."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white" style="position: relative; z-index: 10;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold mb-3">Bắt đầu sử dụng ngay hôm nay</h2>
                    <p class="lead mb-4">Tham gia cùng chúng tôi để trải nghiệm hệ thống quản lý phòng học hiện đại và hiệu quả.</p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="/pdu_pms_project/public/register" class="btn btn-light btn-lg px-4">Đăng ký ngay</a>
                        <a href="/pdu_pms_project/public/page/contact" class="btn btn-outline-light btn-lg px-4">Liên hệ</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Fix hiển thị nội dung */
    .content-wrapper {
        overflow: visible;
        width: 100%;
        position: relative;
        z-index: 1;
    }

    /* Fix khoảng trắng trên section */
    section.bg-primary {
        margin-top: 1.5rem !important;
        padding-top: 2rem !important;
    }

    /* Thêm các loại bỏ khoảng trắng đặc biệt */
    body,
    html {
        margin: 0 !important;
        padding: 0 !important;
        min-height: auto !important;
    }

    /* Loại bỏ space từ WordPress */
    body:before,
    body:after {
        content: none !important;
        display: none !important;
    }

    /* Đảm bảo container không có margin hay padding trên cùng */
    .container {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    /* Style cho feature icons */
    .feature-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
        border-radius: 50%;
    }

    /* Style cho testimonial avatars */
    .testimonial-avatar {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* Đảm bảo các section không bị đè lên */
    section {
        position: relative;
        z-index: 1;
        height: auto;
        margin-bottom: 2rem;
    }

    /* Fix footer spacing */
    section:last-of-type {
        margin-bottom: 2rem;
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>