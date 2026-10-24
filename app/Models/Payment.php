<?php
// app/Models/Payment.php
namespace App\Models;

use App\Core\Model;

class Payment extends Model {
    protected $table = 'payments';
    protected $fillable = [
        'payment_number',
        'invoice_id',
        'student_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_date',
        'received_by',
        'notes'
    ];
    
    public function generatePaymentNumber() {
        return 'PAY' . date('Ymd') . rand(100, 999);
    }
    
    public function getAllWithDetails() {
        $sql = "SELECT p.*, i.invoice_number, i.month, i.year,
                       s.full_name as student_name, s.student_code
                FROM {$this->table} p
                LEFT JOIN invoices i ON p.invoice_id = i.id
                LEFT JOIN students s ON p.student_id = s.id
                ORDER BY p.payment_date DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getByStudentId($studentId) {
        $sql = "SELECT p.*, i.invoice_number, i.month, i.year
                FROM {$this->table} p
                LEFT JOIN invoices i ON p.invoice_id = i.id
                WHERE p.student_id = :student_id
                ORDER BY p.payment_date DESC";
        return $this->db->fetchAll($sql, [':student_id' => $studentId]);
    }
    
    public function getMonthlyRevenue($month = null, $year = null) {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
        
        $sql = "SELECT SUM(amount) as total FROM {$this->table} 
                WHERE MONTH(payment_date) = :month 
                AND YEAR(payment_date) = :year";
        $result = $this->db->fetch($sql, [':month' => $month, ':year' => $year]);
        return $result->total ?? 0;
    }
}