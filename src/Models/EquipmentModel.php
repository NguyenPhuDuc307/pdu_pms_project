<?php

namespace Models;

use Config\Database;

class EquipmentModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy tất cả thiết bị
    public function getAllEquipments()
    {
        $stmt = $this->db->prepare("SELECT * FROM equipments ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy thông tin thiết bị theo id
    public function getEquipmentById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM equipments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Thêm thiết bị mới
    public function addEquipment($name, $description, $maintenance_period)
    {
        $stmt = $this->db->prepare("INSERT INTO equipments (name, description, maintenance_period) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $description, $maintenance_period]);
    }

    // Cập nhật thông tin thiết bị
    public function updateEquipment($id, $name, $description, $maintenance_period)
    {
        $stmt = $this->db->prepare("UPDATE equipments SET name = ?, description = ?, maintenance_period = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $maintenance_period, $id]);
    }

    // Xóa thiết bị
    public function deleteEquipment($id)
    {
        $stmt = $this->db->prepare("DELETE FROM equipments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Thêm thiết bị vào phòng
    public function addEquipmentToRoom($room_id, $equipment_id, $quantity, $notes = null)
    {
        // Kiểm tra xem thiết bị đã có trong phòng chưa
        $stmt = $this->db->prepare("SELECT id, quantity FROM room_equipments WHERE room_id = ? AND equipment_id = ?");
        $stmt->execute([$room_id, $equipment_id]);
        $existingEquipment = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Lấy thông tin chu kỳ bảo trì
        $equipmentInfo = $this->getEquipmentById($equipment_id);
        $maintenancePeriod = $equipmentInfo['maintenance_period'] ?? 90; // Default: 90 ngày

        // Tính ngày bảo trì tiếp theo
        $lastMaintenance = date('Y-m-d');
        $nextMaintenance = date('Y-m-d', strtotime("+{$maintenancePeriod} days"));

        if ($existingEquipment) {
            // Cập nhật số lượng nếu thiết bị đã tồn tại
            $newQuantity = $existingEquipment['quantity'] + $quantity;
            $stmt = $this->db->prepare("UPDATE room_equipments SET quantity = ?, notes = ? WHERE id = ?");
            return $stmt->execute([$newQuantity, $notes, $existingEquipment['id']]);
        } else {
            // Thêm mới nếu thiết bị chưa có trong phòng
            $stmt = $this->db->prepare("
                INSERT INTO room_equipments 
                (room_id, equipment_id, quantity, last_maintenance, next_maintenance, status, notes) 
                VALUES (?, ?, ?, ?, ?, 'hoạt động', ?)
            ");
            return $stmt->execute([$room_id, $equipment_id, $quantity, $lastMaintenance, $nextMaintenance, $notes]);
        }
    }

    // Cập nhật thông tin thiết bị trong phòng
    public function updateRoomEquipment($id, $quantity, $status, $notes = null)
    {
        $stmt = $this->db->prepare("UPDATE room_equipments SET quantity = ?, status = ?, notes = ? WHERE id = ?");
        return $stmt->execute([$quantity, $status, $notes, $id]);
    }

    // Xóa thiết bị khỏi phòng
    public function removeEquipmentFromRoom($id)
    {
        $stmt = $this->db->prepare("DELETE FROM room_equipments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Cập nhật thông tin bảo trì thiết bị
    public function updateEquipmentMaintenance($id, $last_maintenance = null, $status = 'hoạt động')
    {
        if (!$last_maintenance) {
            $last_maintenance = date('Y-m-d');
        }

        // Lấy thông tin thiết bị và chu kỳ bảo trì
        $stmt = $this->db->prepare("
            SELECT e.maintenance_period 
            FROM room_equipments re
            JOIN equipments e ON re.equipment_id = e.id
            WHERE re.id = ?
        ");
        $stmt->execute([$id]);
        $equipment = $stmt->fetch(\PDO::FETCH_ASSOC);
        $maintenancePeriod = $equipment['maintenance_period'] ?? 90; // Default: 90 ngày

        // Tính ngày bảo trì tiếp theo
        $nextMaintenance = date('Y-m-d', strtotime($last_maintenance . " +{$maintenancePeriod} days"));

        $stmt = $this->db->prepare("
            UPDATE room_equipments 
            SET last_maintenance = ?, next_maintenance = ?, status = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$last_maintenance, $nextMaintenance, $status, $id]);
    }

    // Lấy danh sách thiết bị cần bảo trì
    public function getEquipmentsNeedingMaintenance()
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT re.*, r.name as room_name, e.name as equipment_name 
            FROM room_equipments re
            JOIN rooms r ON re.room_id = r.id
            JOIN equipments e ON re.equipment_id = e.id
            WHERE re.next_maintenance <= ? OR re.status = 'bảo trì'
            ORDER BY re.next_maintenance ASC
        ");
        $stmt->execute([$today]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
} 