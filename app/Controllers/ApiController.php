<?php
namespace App\Controllers;

use App\Core\Controller;

class ApiController extends Controller {
    
    public function roomsByBuilding() {
        $buildingId = $_GET['building_id'] ?? 0;
        
        $sql = "SELECT r.*, 
                       (r.capacity - r.current_occupancy) as available_beds
                FROM rooms r
                WHERE r.building_id = :building_id 
                AND r.status IN ('Trống', 'Còn chỗ')
                ORDER BY r.room_number";
        
        $rooms = $this->db->fetchAll($sql, [':building_id' => $buildingId]);
        
        header('Content-Type: application/json');
        echo json_encode($rooms);
        exit;
    }
    
    public function availableBeds() {
        $roomId = $_GET['room_id'] ?? 0;
        
        $sql = "SELECT * FROM room_beds 
                WHERE room_id = :room_id AND status = 'Trống'
                ORDER BY bed_number";
        
        $beds = $this->db->fetchAll($sql, [':room_id' => $roomId]);
        
        header('Content-Type: application/json');
        echo json_encode($beds);
        exit;
    }

    public function paymentStatus($id) {
    $payment = $this->db->fetch("SELECT status FROM payments WHERE id = :id", [':id' => $id]);
    
    header('Content-Type: application/json');
    echo json_encode(['status' => $payment->status ?? 'pending']);
    exit;
}
}