<?php
// app/Models/Maintenance.php
namespace App\Models;

use App\Core\Model;

class Maintenance extends Model {
    protected $table = 'maintenance_requests';
    protected $fillable = [
        'request_number',
        'student_id',
        'room_id',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'completed_at',
        'feedback',
        'rating'
    ];
    
    public function generateRequestNumber() {
        return 'MR' . date('Ymd') . rand(100, 999);
    }
    
    public function getAllWithDetails() {
        $sql = "SELECT mr.*, s.full_name, s.student_code, r.room_number
                FROM {$this->table} mr
                JOIN students s ON mr.student_id = s.id
                JOIN rooms r ON mr.room_id = r.id
                ORDER BY 
                    CASE mr.priority 
                        WHEN 'Khẩn cấp' THEN 1
                        WHEN 'Cao' THEN 2
                        WHEN 'Trung bình' THEN 3
                        ELSE 4
                    END,
                    mr.created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getByStudentId($studentId) {
        $sql = "SELECT mr.*, r.room_number
                FROM {$this->table} mr
                JOIN rooms r ON mr.room_id = r.id
                WHERE mr.student_id = :student_id
                ORDER BY mr.created_at DESC";
        return $this->db->fetchAll($sql, [':student_id' => $studentId]);
    }
    
    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status IN ('Chờ xử lý', 'Đang xử lý')";
        $result = $this->db->fetch($sql);
        return $result->count ?? 0;
    }
}