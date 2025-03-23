<?php
namespace Controllers;

use Models\UserModel;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login($data) {
        if (isset($data['login'])) {
            $username = $data['username'];
            $password = $data['password'];
            $user = $this->userModel->getUserByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                header('Location: /pdu_pms_project/public/' . $user['role']);
                exit;
            }
            return ['error' => 'Sai tên đăng nhập hoặc mật khẩu'];
        }
        return [];
    }

    public function register($data) {
        if (isset($data['register'])) {
            $username = $data['username'];
            $email = $data['email'];
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $role = $data['role'] ?? 'student';
            $class_code = $data['class_code'] ?? null;
            if ($this->userModel->addUser($username, $email, $password, $role, $class_code)) {
                return ['success' => 'Đăng ký thành công! Vui lòng đăng nhập.'];
            }
            return ['error' => 'Đăng ký thất bại, vui lòng thử lại.'];
        }
        return [];
    }

    public function logout() {
        session_destroy();
        header('Location: /pdu_pms_project/public/login');
        exit;
    }
}
