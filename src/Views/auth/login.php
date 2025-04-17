<?php
$pageTitle = "Đăng nhập";
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

    /* Container chứa nội dung đăng nhập */
    .auth-container {
        margin-top: 120px;
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

    .form-control {
        padding: 0.75rem 1rem 0.75rem 3rem;
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: all 0.2s ease;
        font-size: 1rem;
    }

    .form-control:focus {
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
            margin-top: 80px;
        }

        .auth-card .card-body {
            padding: 1.5rem;
        }
    }
</style>

<!-- Sử dụng container riêng biệt cho trang đăng nhập -->
<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="auth-card shadow">
                    <div class="card-header text-center">
                        <h4 class="mb-0 fw-bold">Đăng nhập hệ thống</h4>
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

                        <form action="/pdu_pms_project/public/login/authenticate" method="post">
                            <div class="mb-4">
                                <label for="username" class="form-label">Tên đăng nhập</label>
                                <div class="input-group">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                                </div>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                                <label class="form-check-label" for="rememberMe">Ghi nhớ đăng nhập</label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="login" class="btn btn-primary py-2">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center auth-links">
                        <p class="mb-2">Chưa có tài khoản? <a href="/pdu_pms_project/public/register" class="fw-bold">Đăng ký ngay</a></p>
                        <p class="mb-0"><a href="/pdu_pms_project/public/forgot-password"><i class="fas fa-key me-1"></i>Quên mật khẩu?</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>