<?php
namespace Models;

use Config\Database;

class UserModel {
    private $db;

    public function __construct() {
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

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public function getUserByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addUser($username, $email, $password, $role, $class_code) {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role, class_code) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $password, $role, $class_code]);
    }

    public function updateUser($id, $username, $email, $role, $class_code) {
        $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ?, role = ?, class_code = ? WHERE id = ?");
        return $stmt->execute([$username, $email, $role, $class_code, $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
