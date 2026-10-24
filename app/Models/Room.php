<?php
namespace App\Models;

use App\Core\Model;

class Room extends Model {
    protected $table = 'rooms';
    protected $fillable = [
        'room_number',
        'floor',
        'building',
        'type',
        'capacity',
        'current_occupancy',
        'price',
        'deposit',
        'area',
        'description',
        'facilities',
        'status',
        'images'
    ];
    
    public function getAvailableRooms() {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'available' AND current_occupancy < capacity";
        return $this->db->fetchAll($sql);
    }
    
    public function getRoomsByFloor($floor) {
        $sql = "SELECT * FROM {$this->table} WHERE floor = :floor ORDER BY room_number";
        return $this->db->fetchAll($sql, [':floor' => $floor]);
    }
    
    public function getRoomWithResidents($roomId) {
        $sql = "SELECT r.*, 
                       res.id as resident_id, res.full_name, res.phone, res.email,
                       b.check_in_date, b.check_out_date
                FROM {$this->table} r
                LEFT JOIN beds b ON r.id = b.room_id
                LEFT JOIN residents res ON b.resident_id = res.id
                WHERE r.id = :room_id AND b.status = 'active'";
        return $this->db->fetchAll($sql, [':room_id' => $roomId]);
    }
    
    public function updateOccupancy($roomId) {
        $sql = "UPDATE {$this->table} r 
                SET current_occupancy = (
                    SELECT COUNT(*) FROM beds b 
                    WHERE b.room_id = r.id AND b.status = 'occupied'
                ),
                status = CASE 
                    WHEN current_occupancy >= capacity THEN 'full'
                    WHEN current_occupancy > 0 THEN 'occupied'
                    ELSE 'available'
                END
                WHERE id = :room_id";
        return $this->db->query($sql, [':room_id' => $roomId]);
    }
    
    public function getStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total_rooms,
                    SUM(capacity) as total_beds,
                    SUM(current_occupancy) as occupied_beds,
                    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_rooms,
                    SUM(CASE WHEN status = 'full' THEN 1 ELSE 0 END) as full_rooms,
                    SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_rooms
                FROM {$this->table}";
        return $this->db->fetch($sql);
    }
}