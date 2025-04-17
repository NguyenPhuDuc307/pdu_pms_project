<?php

namespace Controllers;

use Models\UserModel;

require_once __DIR__ . '/../Helpers/AlertHelper.php';

use \AlertHelper;

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login($data)
    {
        if (isset($data['login'])) {
            $username = $data['username'];
            $password = $data['password'];
            $user = $this->userModel->getUserByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Thêm thông báo đăng nhập thành công
                AlertHelper::success(AlertHelper::LOGIN_SUCCESS);

                header('Location: /pdu_pms_project/public/' . $user['role']);
                exit;
            }
            return ['error' => AlertHelper::LOGIN_FAILED];
        }
        return [];
    }

    public function register($data)
    {
        if (isset($data['register'])) {
            $username = $data['username'];
            $email = $data['email'];
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $role = $data['role'] ?? 'student';
            $class_code = $data['class_code'] ?? null;
            $full_name = $data['full_name'] ?? $username;

            // Kiểm tra xem username đã tồn tại chưa
            $existingUser = $this->userModel->getUserByUsername($username);
            if ($existingUser) {
                return ['error' => AlertHelper::USER_EXISTS];
            }

            if ($this->userModel->addUser($username, $email, $password, $role, $class_code, $full_name)) {
                return ['success' => AlertHelper::REGISTER_SUCCESS];
            }
            return ['error' => AlertHelper::REGISTER_FAILED];
        }
        return [];
    }

    public function logout()
    {
        session_destroy();

        // Tạo session mới để lưu thông báo
        session_start();
        AlertHelper::info(AlertHelper::LOGOUT_SUCCESS);

        header('Location: /pdu_pms_project/public/login');
        exit;
    }
}
