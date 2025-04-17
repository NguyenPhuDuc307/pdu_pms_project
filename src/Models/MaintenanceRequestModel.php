<?php

namespace Models;

use Config\Database;

class MaintenanceRequestModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy tất cả yêu cầu bảo trì
    public function getAllRequests()
    {
        $stmt = $this->db->prepare("
            SELECT mr.*, 
                   r.name as room_name, 
                   e.name as equipment_name,
                   u.username as user_name,
                   u.role as user_role
            FROM maintenance_requests mr
            JOIN rooms r ON mr.room_id = r.id
            LEFT JOIN equipments e ON mr.equipment_id = e.id
            JOIN users u ON mr.user_id = u.id
            ORDER BY 
                CASE mr.status 
                    WHEN 'đang chờ' THEN 1
                    WHEN 'đang xử lý' THEN 2
                    WHEN 'đã xử lý' THEN 3
                    WHEN 'từ chối' THEN 4
                END,
                CASE mr.priority
                    WHEN 'khẩn cấp' THEN 1
                    WHEN 'cao' THEN 2
                    WHEN 'trung bình' THEN 3
                    WHEN 'thấp' THEN 4
                END,
                mr.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy yêu cầu bảo trì theo trạng thái
    public function getRequestsByStatus($status)
    {
        $stmt = $this->db->prepare("
            SELECT mr.*, 
                   r.name as room_name, 
                   e.name as equipment_name,
                   u.username as user_name,
                   u.role as user_role
            FROM maintenance_requests mr
            JOIN rooms r ON mr.room_id = r.id
            LEFT JOIN equipments e ON mr.equipment_id = e.id
            JOIN users u ON mr.user_id = u.id
            WHERE mr.status = ?
            ORDER BY 
                CASE mr.priority
                    WHEN 'khẩn cấp' THEN 1
                    WHEN 'cao' THEN 2
                    WHEN 'trung bình' THEN 3
                    WHEN 'thấp' THEN 4
                END,
                mr.created_at DESC
        ");
        $stmt->execute([$status]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy yêu cầu bảo trì theo người dùng
    public function getRequestsByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT mr.*, 
                   r.name as room_name, 
                   e.name as equipment_name
            FROM maintenance_requests mr
            JOIN rooms r ON mr.room_id = r.id
            LEFT JOIN equipments e ON mr.equipment_id = e.id
            WHERE mr.user_id = ?
            ORDER BY mr.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy yêu cầu bảo trì theo phòng học
    public function getRequestsByRoom($roomId)
    {
        $stmt = $this->db->prepare("
            SELECT mr.*, 
                   r.name as room_name, 
                   e.name as equipment_name,
                   u.username as user_name,
                   u.role as user_role
            FROM maintenance_requests mr
            JOIN rooms r ON mr.room_id = r.id
            LEFT JOIN equipments e ON mr.equipment_id = e.id
            JOIN users u ON mr.user_id = u.id
            WHERE mr.room_id = ?
            ORDER BY mr.created_at DESC
        ");
        $stmt->execute([$roomId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết yêu cầu bảo trì theo ID
    public function getRequestById($id)
    {
        $stmt = $this->db->prepare("
            SELECT mr.*, 
                   r.name as room_name, 
                   e.name as equipment_name,
                   u.username as user_name,
                   u.role as user_role
            FROM maintenance_requests mr
            JOIN rooms r ON mr.room_id = r.id
            LEFT JOIN equipments e ON mr.equipment_id = e.id
            JOIN users u ON mr.user_id = u.id
            WHERE mr.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Thêm yêu cầu bảo trì mới
    public function addRequest($roomId, $equipmentId, $userId, $issueDescription, $priority = 'trung bình')
    {
        $stmt = $this->db->prepare("
            INSERT INTO maintenance_requests 
            (room_id, equipment_id, user_id, issue_description, priority, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'đang chờ', NOW())
        ");
        return $stmt->execute([$roomId, $equipmentId, $userId, $issueDescription, $priority]);
    }

    // Cập nhật trạng thái yêu cầu bảo trì
    public function updateRequestStatus($id, $status, $adminNotes = null)
    {
        $resolvedAt = ($status == 'đã xử lý') ? 'NOW()' : 'NULL';
        
        $stmt = $this->db->prepare("
            UPDATE maintenance_requests 
            SET status = ?, 
                resolved_at = " . $resolvedAt . ", 
                admin_notes = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$status, $adminNotes, $id]);
    }

    // Xóa yêu cầu bảo trì
    public function deleteRequest($id)
    {
        $stmt = $this->db->prepare("DELETE FROM maintenance_requests WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // Lấy số lượng yêu cầu bảo trì đang chờ xử lý
    public function getPendingRequestsCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM maintenance_requests WHERE status IN ('đang chờ', 'đang xử lý')");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    // Lấy các yêu cầu bảo trì khẩn cấp
    public function getUrgentRequests()
    {
        $stmt = $this->db->prepare("
            SELECT mr.*, 
                   r.name as room_name, 
                   e.name as equipment_name,
                   u.username as user_name
            FROM maintenance_requests mr
            JOIN rooms r ON mr.room_id = r.id
            LEFT JOIN equipments e ON mr.equipment_id = e.id
            JOIN users u ON mr.user_id = u.id
            WHERE mr.priority = 'khẩn cấp' AND mr.status IN ('đang chờ', 'đang xử lý')
            ORDER BY mr.created_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
} 