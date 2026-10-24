<?php
// app/Models/Invoice.php
namespace App\Models;

use App\Core\Model;

class Invoice extends Model {
    protected $table = 'invoices';
    protected $fillable = [
        'invoice_number',
        'contract_id',
        'student_id',
        'room_id',
        'month',
        'year',
        'room_fee',
        'electricity_usage',
        'electricity_fee',
        'water_usage',
        'water_fee',
        'internet_fee',
        'other_fees',
        'total_amount',
        'due_date',
        'status',
        'paid_at'
    ];
    
    public function generateInvoiceNumber() {
        return 'INV' . date('Ymd') . rand(100, 999);
    }
    
    public function getPendingInvoices() {
        $sql = "SELECT i.*, s.full_name as student_name, s.student_code
                FROM {$this->table} i
                LEFT JOIN students s ON i.student_id = s.id
                WHERE i.status = 'Chưa thanh toán'
                ORDER BY i.due_date ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function getByStudentId($studentId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE student_id = :student_id 
                ORDER BY year DESC, month DESC";
        return $this->db->fetchAll($sql, [':student_id' => $studentId]);
    }
    
    public function getAllWithDetails() {
        $sql = "SELECT i.*, s.full_name as student_name, s.student_code, r.room_number
                FROM {$this->table} i
                LEFT JOIN students s ON i.student_id = s.id
                LEFT JOIN rooms r ON i.room_id = r.id
                ORDER BY i.year DESC, i.month DESC";
        return $this->db->fetchAll($sql);
    }
}