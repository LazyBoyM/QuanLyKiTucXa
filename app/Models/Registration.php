<?php
// app/Models/Registration.php
namespace App\Models;

use App\Core\Model;

class Registration extends Model {
    protected $table = 'registrations';
    protected $fillable = [
        'registration_number',
        'student_id',
        'building_id',
        'desired_room_type',
        'semester',
        'academic_year',
        'registration_date',
        'expected_check_in',
        'status',
        'approved_by',
        'approved_at',
        'notes'
    ];
    
    public function generateRegistrationNumber() {
        return 'REG' . date('Ymd') . rand(100, 999);
    }
    
    public function getAllWithDetails() {
        $sql = "SELECT r.*, s.full_name, s.student_code, b.name as building_name
                FROM {$this->table} r
                JOIN students s ON r.student_id = s.id
                LEFT JOIN buildings b ON r.building_id = b.id
                ORDER BY r.created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'Chờ duyệt'";
        $result = $this->db->fetch($sql);
        return $result->count ?? 0;
    }
}