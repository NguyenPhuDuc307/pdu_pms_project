<?php

namespace Controllers;

use Models\MaintenanceRequestModel;
use Models\RoomModel;
use Models\EquipmentModel;

require_once __DIR__ . '/../Helpers/AlertHelper.php';

use \AlertHelper;

class MaintenanceController
{
    private $maintenanceRequestModel;
    private $roomModel;
    private $equipmentModel;

    public function __construct()
    {
        $this->maintenanceRequestModel = new MaintenanceRequestModel();
        $this->roomModel = new RoomModel();
        $this->equipmentModel = new EquipmentModel();
    }

    // Hiển thị trang quản lý yêu cầu bảo trì cho admin
    public function adminIndex()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $status = $_GET['status'] ?? 'all';

        if ($status != 'all') {
            $requests = $this->maintenanceRequestModel->getRequestsByStatus($status);
        } else {
            $requests = $this->maintenanceRequestModel->getAllRequests();
        }

        // Lấy số lượng yêu cầu theo từng trạng thái để hiển thị trên giao diện
        $pending_count = count($this->maintenanceRequestModel->getRequestsByStatus('đang chờ'));
        $processing_count = count($this->maintenanceRequestModel->getRequestsByStatus('đang xử lý'));
        $resolved_count = count($this->maintenanceRequestModel->getRequestsByStatus('đã xử lý'));
        $rejected_count = count($this->maintenanceRequestModel->getRequestsByStatus('từ chối'));

        return [
            'requests' => $requests,
            'current_status' => $status,
            'pending_count' => $pending_count,
            'processing_count' => $processing_count,
            'resolved_count' => $resolved_count,
            'rejected_count' => $rejected_count,
            'urgent_requests' => $this->maintenanceRequestModel->getUrgentRequests()
        ];
    }

    // Hiển thị chi tiết yêu cầu bảo trì cho admin
    public function viewRequest($data)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $data['id'] ?? null;
        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/admin/maintenance_requests');
            exit;
        }

        $request = $this->maintenanceRequestModel->getRequestById($id);
        if (!$request) {
            AlertHelper::error(AlertHelper::MAINTENANCE_NOT_FOUND);
            header('Location: /pdu_pms_project/public/admin/maintenance_requests');
            exit;
        }

        return ['request' => $request];
    }

    // Cập nhật trạng thái yêu cầu bảo trì
    public function updateRequestStatus($data)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $data['id'] ?? null;
            $status = $data['status'] ?? '';
            $adminNotes = $data['admin_notes'] ?? '';

            if (!$id || !$status) {
                AlertHelper::error(AlertHelper::INVALID_INPUT);
                header('Location: /pdu_pms_project/public/admin/maintenance_requests');
                exit;
            }

            $success = $this->maintenanceRequestModel->updateRequestStatus($id, $status, $adminNotes);
            if ($success) {
                // Nếu yêu cầu được đánh dấu là đã xử lý và có equipment_id, cập nhật trạng thái thiết bị
                if ($status === 'đã xử lý') {
                    $request = $this->maintenanceRequestModel->getRequestById($id);
                    if ($request && $request['equipment_id']) {
                        // Tìm thiết bị trong phòng
                        $room_equipments = $this->roomModel->getRoomEquipments($request['room_id']);
                        foreach ($room_equipments as $equipment) {
                            if ($equipment['equipment_id'] == $request['equipment_id']) {
                                // Cập nhật thông tin bảo trì
                                $this->equipmentModel->updateEquipmentMaintenance(
                                    $equipment['id'],
                                    date('Y-m-d'),
                                    'hoạt động'
                                );
                                break;
                            }
                        }
                    }
                }

                AlertHelper::success(AlertHelper::ACTION_COMPLETED);
                header('Location: /pdu_pms_project/public/admin/maintenance_requests');
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
                header('Location: /pdu_pms_project/public/admin/maintenance_requests');
            }
            exit;
        }

        header('Location: /pdu_pms_project/public/admin/maintenance_requests');
        exit;
    }

    // Xóa yêu cầu bảo trì
    public function deleteRequest($data)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $id = $data['id'] ?? null;
        if (!$id) {
            AlertHelper::error(AlertHelper::INVALID_INPUT);
            header('Location: /pdu_pms_project/public/admin/maintenance_requests');
            exit;
        }

        $success = $this->maintenanceRequestModel->deleteRequest($id);
        if ($success) {
            AlertHelper::success(AlertHelper::ACTION_COMPLETED);
            header('Location: /pdu_pms_project/public/admin/maintenance_requests');
        } else {
            AlertHelper::error(AlertHelper::ACTION_FAILED);
            header('Location: /pdu_pms_project/public/admin/maintenance_requests');
        }
        exit;
    }

    // Hiển thị trang yêu cầu bảo trì cho giáo viên/sinh viên
    public function userIndex()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $requests = $this->maintenanceRequestModel->getRequestsByUser($userId);

        return [
            'requests' => $requests,
            'rooms' => $this->roomModel->getAllRooms()
        ];
    }

    // Hiển thị trang tạo yêu cầu bảo trì mới cho giáo viên/sinh viên
    public function createRequest($data)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /pdu_pms_project/public/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roomId = $data['room_id'] ?? null;
            $equipmentId = !empty($data['equipment_id']) ? $data['equipment_id'] : null;
            $issueDescription = $data['issue_description'] ?? '';
            $priority = $data['priority'] ?? 'trung bình';
            $userId = $_SESSION['user_id'];

            if (!$roomId || !$issueDescription) {
                AlertHelper::error(AlertHelper::INVALID_INPUT);
                return [
                    'error' => AlertHelper::INVALID_INPUT,
                    'rooms' => $this->roomModel->getAllRooms(),
                    'equipments' => $roomId ? $this->roomModel->getRoomEquipments($roomId) : []
                ];
            }

            $success = $this->maintenanceRequestModel->addRequest($roomId, $equipmentId, $userId, $issueDescription, $priority);
            if ($success) {
                AlertHelper::success(AlertHelper::ACTION_COMPLETED);
                header('Location: /pdu_pms_project/public/maintenance');
                exit;
            } else {
                AlertHelper::error(AlertHelper::ACTION_FAILED);
                return [
                    'error' => AlertHelper::ACTION_FAILED,
                    'rooms' => $this->roomModel->getAllRooms(),
                    'equipments' => $roomId ? $this->roomModel->getRoomEquipments($roomId) : []
                ];
            }
        }

        $roomId = $data['room_id'] ?? null;
        return [
            'rooms' => $this->roomModel->getAllRooms(),
            'equipments' => $roomId ? $this->roomModel->getRoomEquipments($roomId) : []
        ];
    }

    // API để lấy thiết bị trong phòng
    public function getRoomEquipments($data)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => AlertHelper::PERMISSION_DENIED]);
            exit;
        }

        $roomId = $data['room_id'] ?? null;
        if (!$roomId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => AlertHelper::INVALID_INPUT]);
            exit;
        }

        $equipments = $this->roomModel->getRoomEquipments($roomId);

        header('Content-Type: application/json');
        echo json_encode(['equipments' => $equipments]);
        exit;
    }
}
