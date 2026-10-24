<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';
    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'phone',
        'avatar',
        'role',
        'status',
        'email_verified_at'
    ];
    protected $hidden = ['password'];
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        return $this->db->fetch($sql, [':email' => $email]);
    }
    
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        return $this->db->fetch($sql, [':username' => $username]);
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    // Sửa từ 'resident' thành 'student'
    public function getStudents() {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'student'";
        return $this->db->fetchAll($sql);
    }
    
    public function getAdmins() {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'admin'";
        return $this->db->fetchAll($sql);
    }
    
    public function updateLastLogin($userId) {
        // Kiểm tra xem có cột last_login không
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        return $this->db->query($sql, [':id' => $userId]);
    }
}