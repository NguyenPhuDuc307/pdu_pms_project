<?php
// Sử dụng đường dẫn tuyệt đối để tránh lỗi
$title = 'Liên hệ với chúng tôi';
$pageTitle = 'Liên hệ với chúng tôi';
$pageSubtitle = 'Thông tin liên hệ Trường Đại Học Phương Đông tại Trung Kính';
$pageIcon = 'fas fa-phone-volume';
$breadcrumbs = [
    ['title' => 'Trang chủ', 'link' => '/pdu_pms_project/public/'],
    ['title' => 'Liên hệ', 'link' => '']
];
include __DIR__ . '/../layouts/header.php';
// Không cần include sidebar vì không tồn tại
?>

<style>
    /* Contact page styles */
    .contact-section {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #0d6efd;
    }

    .contact-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e7f1ff;
        border-radius: 50%;
        color: #0d6efd;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .contact-info-card {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .contact-info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .contact-info-card .card-body {
        padding: 1.5rem;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e7f1ff;
        border-radius: 50%;
        color: #0d6efd;
        margin-right: 0.75rem;
        transition: all 0.3s ease;
    }

    .social-icon:hover {
        background-color: #0d6efd;
        color: white;
    }

    /* Form styling */
    .form-floating {
        margin-bottom: 1rem;
    }

    .form-floating>label {
        padding-left: 1rem;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .map-container {
        height: 400px;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
</style>

<div class="container-fluid px-4 py-5">
    <?php include __DIR__ . '/../components/page_header.php'; ?>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Left Column - Contact Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <h2 class="h4 mb-0">Thông tin liên hệ</h2>
                    </div>

                    <div class="contact-info-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-map-marker-alt text-primary fs-4 me-3"></i>
                                <h3 class="h5 mb-0">Địa chỉ</h3>
                            </div>
                            <p><strong>Cơ sở chính:</strong> 171 Trung Kính, Yên Hòa, Cầu Giấy, Hà Nội</p>
                            <p class="mb-0"><strong>Cơ sở 2:</strong> Số 4 Ngõ Chùa Hưng Ký, phố Minh Khai, Quận Hai Bà Trưng, Hà Nội</p>
                        </div>
                    </div>

                    <div class="contact-info-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-phone-alt text-primary fs-4 me-3"></i>
                                <h3 class="h5 mb-0">Điện thoại</h3>
                            </div>
                            <p><strong>Tổng đài:</strong> 024-3784-8513 (14/15/16/17/18)</p>
                            <p><strong>Tuyển sinh:</strong> 024.3784.7110 / 09.1551.7110</p>
                            <p class="mb-0"><strong>Fax:</strong> 024-3784-8512</p>
                        </div>
                    </div>

                    <div class="contact-info-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-envelope text-primary fs-4 me-3"></i>
                                <h3 class="h5 mb-0">Email</h3>
                            </div>
                            <p><strong>Tuyển sinh:</strong> tuyensinh@phuongdong.edu.vn</p>
                            <p><strong>Đào tạo:</strong> daotao@phuongdong.edu.vn</p>
                            <p class="mb-0"><strong>Hỗ trợ:</strong> support@phuongdong.edu.vn</p>
                        </div>
                    </div>

                    <div class="contact-info-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock text-primary fs-4 me-3"></i>
                                <h3 class="h5 mb-0">Giờ làm việc</h3>
                            </div>
                            <p><strong>Thứ Hai - Thứ Sáu:</strong> 7:30 - 17:30</p>
                            <p class="mb-0"><strong>Thứ Bảy:</strong> 8:00 - 12:00</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="h5 mb-3">Kết nối với chúng tôi</h3>
                        <div class="d-flex">
                            <a href="https://www.facebook.com/daihocphuongdong.pdu" class="social-icon">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="h4 mb-0">Phòng ban chức năng</h3>
                    </div>

                    <div class="accordion" id="departmentAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTuyenSinh">
                                    Phòng Tuyển sinh
                                </button>
                            </h2>
                            <div id="collapseTuyenSinh" class="accordion-collapse collapse" data-bs-parent="#departmentAccordion">
                                <div class="accordion-body">
                                    <p><strong>Điện thoại:</strong> 024.3784.7110</p>
                                    <p><strong>Email:</strong> tuyensinh@phuongdong.edu.vn</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDaoTao">
                                    Phòng Đào tạo
                                </button>
                            </h2>
                            <div id="collapseDaoTao" class="accordion-collapse collapse" data-bs-parent="#departmentAccordion">
                                <div class="accordion-body">
                                    <p><strong>Điện thoại:</strong> 024-3784-8513</p>
                                    <p><strong>Email:</strong> daotao@phuongdong.edu.vn</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKhoaHoc">
                                    Phòng Khoa học Công nghệ
                                </button>
                            </h2>
                            <div id="collapseKhoaHoc" class="accordion-collapse collapse" data-bs-parent="#departmentAccordion">
                                <div class="accordion-body">
                                    <p><strong>Điện thoại:</strong> 024-3784-8516</p>
                                    <p><strong>Email:</strong> khcn@phuongdong.edu.vn</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCTSV">
                                    Phòng Công tác Sinh viên
                                </button>
                            </h2>
                            <div id="collapseCTSV" class="accordion-collapse collapse" data-bs-parent="#departmentAccordion">
                                <div class="accordion-body">
                                    <p><strong>Điện thoại:</strong> 024-3784-8517</p>
                                    <p><strong>Email:</strong> ctsv@phuongdong.edu.vn</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Contact Form and Map -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h2 class="h4 mb-0">Gửi tin nhắn cho chúng tôi</h2>
                    </div>

                    <form id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nameInput" placeholder="Họ và tên">
                                    <label for="nameInput">Họ và tên</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="emailInput" placeholder="Email">
                                    <label for="emailInput">Email</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phoneInput" placeholder="Số điện thoại">
                                    <label for="phoneInput">Số điện thoại</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="subjectSelect">
                                        <option selected disabled value="">Chọn chủ đề</option>
                                        <option value="inquiry">Thông tin chung</option>
                                        <option value="admission">Tuyển sinh</option>
                                        <option value="technical">Hỗ trợ kỹ thuật</option>
                                        <option value="cooperation">Hợp tác đối ngoại</option>
                                        <option value="other">Khác</option>
                                    </select>
                                    <label for="subjectSelect">Chủ đề</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="messageTextarea" style="height: 150px" placeholder="Nội dung tin nhắn"></textarea>
                                    <label for="messageTextarea">Nội dung tin nhắn</label>
                                </div>
                            </div>
                            <div class="col-12 d-flex">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreeCheck">
                                    <label class="form-check-label" for="agreeCheck">
                                        Tôi đồng ý với các <a href="#" class="text-decoration-none">điều khoản bảo mật</a> của trường
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h2 class="h4 mb-0">Bản đồ</h2>
                    </div>

                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.21617845767!2d105.7907483!3d21.0240997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4cd0c66f05%3A0x566ca1fedaeae5d!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBQaMawxqFuZyDEkMO0bmc!5e0!3m2!1svi!2s!4v1679921288607!5m2!1svi!2s" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>