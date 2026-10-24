<?php
// app/Models/Complaint.php
namespace App\Models;

use App\Core\Model;

class Complaint extends Model {
    protected $table = 'complaints';
    protected $fillable = [
        'complaint_number',
        'student_id',
        'category',
        'title',
        'description',
        'is_anonymous',
        'status',
        'assigned_to',
        'response',
        'responded_by',
        'responded_at'
    ];
    
    public function generateComplaintNumber() {
        return 'CP' . date('Ymd') . rand(100, 999);
    }
    
    public function getAllWithDetails() {
        $sql = "SELECT c.*, s.full_name, s.student_code
                FROM {$this->table} c
                LEFT JOIN students s ON c.student_id = s.id
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql);
    }
}