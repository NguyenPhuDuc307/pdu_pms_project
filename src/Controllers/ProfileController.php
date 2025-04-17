<?php

namespace Controllers;

use Models\UserModel;

require_once __DIR__ . '/../Helpers/AlertHelper.php';

use \AlertHelper;

class ProfileController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);
        return ['user' => $user];
    }

    public function updateProfile($data)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        if (isset($data['update_profile'])) {
            $userId = $_SESSION['user_id'];
            $fullName = $data['fullname'] ?? '';
            $email = $data['email'] ?? '';
            $phone = $data['phone'] ?? '';

            // Cập nhật thông tin người dùng
            $success = $this->userModel->updateUserProfile($userId, $fullName, $email, $phone);

            if ($success) {
                // Cập nhật session
                $_SESSION['full_name'] = $fullName;

                AlertHelper::success(AlertHelper::PROFILE_UPDATED);
                header('Location: /pdu_pms_project/public/profile');
                exit;
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
                header('Location: /pdu_pms_project/public/profile');
                exit;
            }
        }

        return [];
    }

    public function changePassword($data)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        if (isset($data['change_password'])) {
            $userId = $_SESSION['user_id'];
            $currentPassword = $data['current_password'] ?? '';
            $newPassword = $data['new_password'] ?? '';
            $confirmPassword = $data['confirm_password'] ?? '';

            // Kiểm tra mật khẩu hiện tại
            $user = $this->userModel->getUserById($userId);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                AlertHelper::error('Mật khẩu hiện tại không đúng');
                header('Location: /pdu_pms_project/public/profile');
                exit;
            }

            // Kiểm tra mật khẩu mới và xác nhận mật khẩu
            if ($newPassword !== $confirmPassword) {
                AlertHelper::error(AlertHelper::PASSWORD_MISMATCH);
                header('Location: /pdu_pms_project/public/profile');
                exit;
            }

            // Cập nhật mật khẩu
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $success = $this->userModel->updateUserPassword($userId, $hashedPassword);

            if ($success) {
                AlertHelper::success(AlertHelper::PASSWORD_CHANGED);
                header('Location: /pdu_pms_project/public/profile');
                exit;
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
                header('Location: /pdu_pms_project/public/profile');
                exit;
            }
        }

        return [];
    }
}
