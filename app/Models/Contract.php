<?php
// app/Models/Contract.php
namespace App\Models;

use App\Core\Model;

class Contract extends Model {
    protected $table = 'contracts';
    protected $fillable = [
        'contract_number',
        'registration_id',
        'student_id',
        'room_id',
        'bed_id',
        'check_in_date',
        'check_out_date',
        'monthly_rent',
        'deposit',
        'status'
    ];
    
    public function generateContractNumber() {
        return 'CT' . date('Ymd') . rand(100, 999);
    }
    
    public function getAllWithDetails() {
        $sql = "SELECT c.*, s.full_name, s.student_code, r.room_number, b.bed_number
                FROM {$this->table} c
                JOIN students s ON c.student_id = s.id
                JOIN rooms r ON c.room_id = r.id
                JOIN room_beds b ON c.bed_id = b.id
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getActiveCount() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'Hiệu lực'";
        $result = $this->db->fetch($sql);
        return $result->count ?? 0;
    }
    
    public function getByStudentId($studentId) {
        $sql = "SELECT c.*, r.room_number, r.room_type, b.name as building_name, bed.bed_number
                FROM {$this->table} c
                JOIN rooms r ON c.room_id = r.id
                JOIN buildings b ON r.building_id = b.id
                JOIN room_beds bed ON c.bed_id = bed.id
                WHERE c.student_id = :student_id AND c.status = 'Hiệu lực'";
        return $this->db->fetch($sql, [':student_id' => $studentId]);
    }
}