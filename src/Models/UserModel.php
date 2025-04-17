<?php

namespace Models;

use Config\Database;

class UserModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy tổng số người dùng
    public function getTotalUsers()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Lấy số lượng người dùng theo vai trò
    public function getUserCountByRole($role)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users WHERE role = ?");
        $stmt->execute([$role]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Lấy danh sách người dùng đăng ký gần đây
    public function getRecentUsers($limit = 10)
    {
        // Chuyển $limit thành integer để đảm bảo an toàn
        $limit = (int)$limit;

        // Sử dụng cột created_at để sắp xếp (đã được thêm vào bảng)
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT $limit");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUsersCountLastMonth()
    {
        $query = "SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public function getUserByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAllUsers()
    {
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addUser($username, $email, $password, $role, $class_code, $full_name = null)
    {
        $full_name = $full_name ?? $username;
        $created_at = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role, class_code, full_name, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $password, $role, $class_code, $full_name, $created_at]);
    }

    public function updateUser($id, $username, $email, $role, $class_code, $full_name = null)
    {
        $full_name = $full_name ?? $username;
        $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ?, role = ?, class_code = ?, full_name = ? WHERE id = ?");
        return $stmt->execute([$username, $email, $role, $class_code, $full_name, $id]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Update user profile information
     *
     * @param int $id User ID
     * @param string $fullName Full name
     * @param string $email Email address
     * @param string $phone Phone number
     * @return bool Success status
     */
    public function updateUserProfile($id, $fullName, $email, $phone = null)
    {
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        return $stmt->execute([$fullName, $email, $phone, $id]);
    }

    /**
     * Update user password
     *
     * @param int $id User ID
     * @param string $hashedPassword Hashed password
     * @return bool Success status
     */
    public function updateUserPassword($id, $hashedPassword)
    {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

    /**
     * Get all users with a specific role
     *
     * @param string $role Role to filter by (admin, teacher, student)
     * @return array List of users with the specified role
     */
    public function getUsersByRole($role)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
