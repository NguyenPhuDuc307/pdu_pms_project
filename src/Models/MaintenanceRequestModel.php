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

    // Lấy số lượng yêu cầu bảo trì theo trạng thái
    public function getRequestsCountByStatus($status)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM maintenance_requests WHERE status = ?");
        $stmt->execute([$status]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Lấy tổng số yêu cầu bảo trì
    public function getTotalRequestsCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM maintenance_requests");
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
    
    // Lấy thống kê yêu cầu bảo trì theo tháng
    public function getMonthlyStats($year = null)
    {
        if ($year === null) {
            $year = date('Y'); // Mặc định là năm hiện tại
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                MONTH(created_at) as month,
                COUNT(*) as total_count,
                SUM(CASE WHEN status = 'đang chờ' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'đang xử lý' THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN status = 'đã xử lý' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'từ chối' THEN 1 ELSE 0 END) as rejected_count,
                SUM(CASE WHEN priority = 'khẩn cấp' THEN 1 ELSE 0 END) as urgent_count,
                SUM(CASE WHEN priority = 'cao' THEN 1 ELSE 0 END) as high_count,
                SUM(CASE WHEN priority = 'trung bình' THEN 1 ELSE 0 END) as medium_count,
                SUM(CASE WHEN priority = 'thấp' THEN 1 ELSE 0 END) as low_count
            FROM maintenance_requests
            WHERE YEAR(created_at) = ?
            GROUP BY MONTH(created_at)
            ORDER BY month ASC
        ");
        
        $stmt->execute([$year]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Lấy thống kê so sánh giữa các tháng gần đây (thường là 6 tháng gần nhất)
    public function getRecentMonthlyComparison($monthsCount = 6)
    {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month_year,
                COUNT(*) as total_count,
                SUM(CASE WHEN status = 'đang chờ' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'đang xử lý' THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN status = 'đã xử lý' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'từ chối' THEN 1 ELSE 0 END) as rejected_count
            FROM maintenance_requests
            WHERE created_at >= DATE_SUB(LAST_DAY(NOW()), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month_year ASC
        ");
        
        $stmt->execute([$monthsCount]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
