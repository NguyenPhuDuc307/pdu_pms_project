<?php
$pageTitle = "Đăng ký";
include __DIR__ . '/../layouts/header.php';
?>

<style>
    body {
        padding-top: 0;
        margin-top: 0;
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        min-height: 100vh;
    }

    /* Reset padding và margin */
    .container-fluid,
    .row,
    .col,
    .card {
        margin-top: 0;
        padding-top: 0;
    }

    /* Container chứa nội dung đăng ký */
    .auth-container {
        margin-top: 80px;
        padding-bottom: 40px;
    }

    /* Card styling */
    .auth-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        background-color: #fff;
        transition: all 0.3s ease;
    }

    .auth-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    }

    .auth-card .card-header {
        padding: 1.75rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background: rgba(255, 255, 255, 0.9);
    }

    .auth-card .card-header h4 {
        color: #0d6efd;
    }

    .auth-card .card-body {
        padding: 2.5rem;
    }

    .auth-card .card-footer {
        padding: 1.5rem;
        background-color: rgba(0, 0, 0, 0.02);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Logo styling */
    .auth-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .auth-logo i {
        font-size: 2.5rem;
        margin-right: 0.75rem;
        color: #0d6efd;
    }

    /* Form elements */
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .form-control,
    .form-select {
        padding: 0.75rem 1rem 0.75rem 3rem;
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: all 0.2s ease;
        font-size: 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    .input-group {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }

    .form-check {
        margin-top: 1rem;
        margin-bottom: 1.5rem;
    }

    .btn-primary {
        padding: 0.75rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
        letter-spacing: 0.5px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
    }

    /* Links styling */
    .auth-links a {
        color: #0d6efd;
        text-decoration: none;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .auth-links a:hover {
        color: #084298;
        text-decoration: underline;
    }

    /* Responsive fixes */
    @media (max-width: 768px) {
        .auth-container {
            margin-top: 40px;
        }

        .auth-card .card-body {
            padding: 1.5rem;
        }
    }
</style>

<!-- Sử dụng container riêng biệt cho trang đăng ký -->
<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="auth-card shadow">
                    <div class="card-header text-center">
                        <h4 class="mb-0 fw-bold">Đăng ký tài khoản mới</h4>
                    </div>
                    <div class="card-body">
                        <div class="auth-logo">
                            <i class="fas fa-school"></i>
                            <h5 class="mb-0 fw-bold">PDU - PMS</h5>
                        </div>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php
                                echo $_SESSION['success'];
                                unset($_SESSION['success']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="/pdu_pms_project/public/register/process" method="post" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                                    </div>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập tên đăng nhập.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Nhập địa chỉ email" required>
                                    </div>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập địa chỉ email hợp lệ.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="full_name" class="form-label">Họ và tên đầy đủ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <i class="fas fa-id-card input-icon"></i>
                                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nhập họ và tên đầy đủ" required>
                                    </div>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập họ và tên đầy đủ.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                                    </div>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập mật khẩu.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                                    </div>
                                    <div class="invalid-feedback">
                                        Mật khẩu xác nhận không khớp.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <i class="fas fa-user-tag input-icon"></i>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="" selected disabled>Chọn vai trò</option>
                                            <option value="teacher">Giảng viên</option>
                                            <option value="student">Sinh viên</option>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">
                                        Vui lòng chọn vai trò.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 d-none" id="class_code_container">
                                    <label for="class_code" class="form-label">Mã lớp học <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <i class="fas fa-graduation-cap input-icon"></i>
                                        <input type="text" class="form-control" id="class_code" name="class_code" placeholder="Nhập mã lớp học">
                                    </div>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập mã lớp học.
                                    </div>
                                    <small class="text-muted">Chỉ dành cho sinh viên</small>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="fw-bold">điều khoản sử dụng</a> <span class="text-danger">*</span></label>
                                <div class="invalid-feedback">
                                    Bạn phải đồng ý với điều khoản sử dụng.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary py-2" name="register">
                                    <i class="fas fa-user-plus me-2"></i>Đăng ký tài khoản
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center auth-links">
                        <p class="mb-0">Đã có tài khoản? <a href="/pdu_pms_project/public/login" class="fw-bold"><i class="fas fa-sign-in-alt me-1"></i>Đăng nhập ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Điều khoản sử dụng -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều khoản sử dụng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Quy định chung</h6>
                <p>Hệ thống PDU PMS được phát triển và quản lý bởi Phòng Đào tạo. Việc sử dụng hệ thống này đồng nghĩa với việc bạn đồng ý tuân thủ các điều khoản và điều kiện sử dụng.</p>

                <h6>2. Quyền và trách nhiệm người dùng</h6>
                <p>- Cung cấp thông tin chính xác và đầy đủ khi đăng ký tài khoản<br>
                    - Bảo mật thông tin tài khoản và mật khẩu của mình<br>
                    - Chịu trách nhiệm cho mọi hoạt động diễn ra dưới tài khoản của mình<br>
                    - Sử dụng hệ thống đúng mục đích và tuân thủ quy định của nhà trường</p>

                <h6>3. Bảo mật thông tin</h6>
                <p>Thông tin cá nhân của bạn sẽ được bảo mật và chỉ được sử dụng cho mục đích quản lý hệ thống PDU PMS. Chúng tôi cam kết không chia sẻ thông tin của bạn cho bên thứ ba nếu không được sự đồng ý.</p>

                <h6>4. Điều khoản đặt phòng</h6>
                <p>- Việc đặt phòng cần được thực hiện đúng quy trình và phải được phê duyệt<br>
                    - Người dùng cần tuân thủ lịch đặt phòng đã được duyệt<br>
                    - Trường hợp hủy đặt phòng cần thông báo trước ít nhất 24 giờ</p>

                <h6>5. Quy định sử dụng phòng và thiết bị</h6>
                <p>- Sử dụng phòng học và thiết bị đúng mục đích<br>
                    - Giữ gìn, bảo quản tài sản trong phòng học<br>
                    - Báo cáo kịp thời các sự cố về phòng học và thiết bị</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation
    (function() {
        'use strict'

        // Fetch all forms we want to apply validation to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    // Check if passwords match
                    var password = document.getElementById('password')
                    var confirmPassword = document.getElementById('confirm_password')

                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp')
                        event.preventDefault()
                        event.stopPropagation()
                    } else {
                        confirmPassword.setCustomValidity('')
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()

    // Show/hide class code field based on role selection
    document.getElementById('role').addEventListener('change', function() {
        var classCodeContainer = document.getElementById('class_code_container')
        var classCodeInput = document.getElementById('class_code')

        if (this.value === 'student') {
            classCodeContainer.classList.remove('d-none')
            classCodeInput.setAttribute('required', 'required')
        } else {
            classCodeContainer.classList.add('d-none')
            classCodeInput.removeAttribute('required')
        }
    })
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>