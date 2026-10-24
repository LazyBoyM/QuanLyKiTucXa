<?php
// app/Models/CheckOutRequest.php
namespace App\Models;

use App\Core\Model;

class CheckOutRequest extends Model {
    protected $table = 'check_out_requests';
    protected $fillable = [
        'request_number',
        'contract_id',
        'student_id',
        'room_id',
        'request_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'completed_at',
        'notes'
    ];
    
    public function generateRequestNumber() {
        return 'CO' . date('Ymd') . rand(100, 999);
    }
    
    public function getAllWithDetails() {
        $sql = "SELECT c.*, s.full_name, s.student_code, r.room_number
                FROM {$this->table} c
                JOIN students s ON c.student_id = s.id
                JOIN rooms r ON c.room_id = r.id
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql);
    }
}