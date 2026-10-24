<?php
// app/Models/Student.php
namespace App\Models;

use App\Core\Model;

class Student extends Model {
    protected $table = 'students';
    protected $fillable = [
        'user_id',
        'student_code',
        'full_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'faculty',
        'class',
        'academic_year',
        'permanent_address',
        'id_card',
        'health_insurance',
        'avatar'
    ];
    
    public function findByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        return $this->db->fetch($sql, [':user_id' => $userId]);
    }
    
    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE full_name LIKE :keyword 
                   OR student_code LIKE :keyword 
                   OR email LIKE :keyword 
                   OR phone LIKE :keyword";
        return $this->db->fetchAll($sql, [':keyword' => "%{$keyword}%"]);
    }
}