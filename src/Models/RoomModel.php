<?php

namespace Models;

use Config\Database;

class RoomModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy tổng số phòng học
    public function getTotalRooms()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM rooms");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Lấy danh sách các phòng được sử dụng nhiều nhất
    public function getMostUsedRooms($limit = 5)
    {
        $limit = (int)$limit; // Ép kiểu để đảm bảo an toàn

        // Trả về tên các cột phù hợp với view
        $stmt = $this->db->prepare(
            "SELECT r.id, r.name as room_number, r.capacity, 
         '' as type, COUNT(b.id) AS booking_count 
         FROM rooms r
         LEFT JOIN bookings b ON r.id = b.room_id
         GROUP BY r.id
         ORDER BY booking_count DESC
         LIMIT $limit"
        );

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllRooms()
    {
        $stmt = $this->db->prepare("SELECT * FROM rooms");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRoomById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Thêm phương thức để lấy danh sách phòng trống trong khoảng thời gian
    public function getAvailableRoomsByTimeRange($start_time, $end_time, $participants)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM rooms 
             WHERE status = 'trống' AND capacity >= ?"
        );
        $stmt->execute([$participants]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addRoom($name, $capacity)
    {
        $stmt = $this->db->prepare("INSERT INTO rooms (name, capacity, status) VALUES (?, ?, 'trống')");
        return $stmt->execute([$name, $capacity]);
    }

    public function updateRoom($id, $name, $capacity, $status)
    {
        $stmt = $this->db->prepare("UPDATE rooms SET name = ?, capacity = ?, status = ? WHERE id = ?");
        return $stmt->execute([$name, $capacity, $status, $id]);
    }

    public function deleteRoom($id)
    {
        $stmt = $this->db->prepare("DELETE FROM rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
