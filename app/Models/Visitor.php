<?php
// app/Models/Visitor.php
namespace App\Models;

use App\Core\Model;

class Visitor extends Model {
    protected $table = 'visitors';
    protected $fillable = [
        'student_id',
        'room_id',
        'visitor_name',
        'id_card',
        'phone',
        'relationship',
        'purpose',
        'check_in_time',
        'check_out_time',
        'registered_by'
    ];
    
    public function getAllWithDetails() {
        $sql = "SELECT v.*, s.full_name as student_name, s.student_code, r.room_number
                FROM {$this->table} v
                JOIN students s ON v.student_id = s.id
                JOIN rooms r ON v.room_id = r.id
                ORDER BY v.check_in_time DESC";
        return $this->db->fetchAll($sql);
    }
}