<?php
namespace App\Controllers;

use App\Core\Controller;

class ViolationController extends Controller {
    
    public function index() {
    if (is_admin()) {
        $sql = "SELECT v.*, s.full_name, s.student_code, rm.room_number,
                       r.title as rule_title
                FROM violations v
                JOIN students s ON v.student_id = s.id
                LEFT JOIN contracts c ON s.id = c.student_id AND c.status = 'Hiệu lực'
                LEFT JOIN rooms rm ON c.room_id = rm.id
                LEFT JOIN rules r ON v.rule_id = r.id
                ORDER BY v.created_at DESC";
        $violations = $this->db->fetchAll($sql);
    } else {
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if ($student) {
            $sql = "SELECT v.*, r.title as rule_title
                    FROM violations v
                    LEFT JOIN rules r ON v.rule_id = r.id
                    WHERE v.student_id = :student_id
                    ORDER BY v.created_at DESC";
            $violations = $this->db->fetchAll($sql, [':student_id' => $student->id]);
        } else {
            $violations = [];
        }
    }
    
    $data = [
        'title' => 'Vi phạm - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Danh sách vi phạm',
        'violations' => $violations
    ];
    
    $this->view('violations/index', $data);
}
    
    public function create() {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền truy cập');
        return redirect('/violations');
    }
    
    // Lấy danh sách sinh viên đang ở KTX (có hợp đồng hiệu lực)
    $students = $this->db->fetchAll("
        SELECT s.*, r.room_number
        FROM students s
        JOIN contracts c ON s.id = c.student_id
        JOIN rooms r ON c.room_id = r.id
        WHERE c.status = 'Hiệu lực'
        ORDER BY s.full_name
    ");
    
    // Lấy danh sách nội quy
    $rules = $this->db->fetchAll("SELECT * FROM rules ORDER BY category, title");
    
    $data = [
        'title' => 'Thêm vi phạm - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Thêm vi phạm mới',
        'students' => $students,
        'rules' => $rules
    ];
    
    $this->view('violations/create', $data);
}
    
    public function store() {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/violations');
    }
    
    $violationNumber = 'VP' . date('Ymd') . rand(100, 999);
    $penaltyAmount = $_POST['penalty_amount'] ?? 0;
    
    $data = [
        'violation_number' => $violationNumber,
        'student_id' => $_POST['student_id'],
        'rule_id' => $_POST['rule_id'] ?: null,
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'penalty' => $_POST['penalty'] ?? null,
        'penalty_amount' => $penaltyAmount,
        'reported_by' => auth()['id']
    ];
    
    $this->db->beginTransaction();
    
    try {
        // 1. Lưu vi phạm
        $violationId = $this->db->insert('violations', $data);
        
        // 2. Nếu có tiền phạt > 0, tạo hóa đơn
        if ($penaltyAmount > 0) {
            $student = $this->db->fetch("SELECT * FROM students WHERE id = :id", [':id' => $_POST['student_id']]);
            
            // Lấy hợp đồng hiệu lực của sinh viên
            $contract = $this->db->fetch("
                SELECT * FROM contracts 
                WHERE student_id = :student_id AND status = 'Hiệu lực'
                LIMIT 1
            ", [':student_id' => $_POST['student_id']]);
            
            if ($contract) {
                $invoiceNumber = 'PHAT' . date('Ymd') . rand(100, 999);
                $dueDate = date('Y-m-d', strtotime('+7 days'));
                
                $invoiceId = $this->db->insert('invoices', [
                    'invoice_number' => $invoiceNumber,
                    'contract_id' => $contract->id,
                    'student_id' => $_POST['student_id'],
                    'room_id' => $contract->room_id,
                    'month' => date('m'),
                    'year' => date('Y'),
                    'room_fee' => 0,
                    'electricity_usage' => 0,
                    'electricity_fee' => 0,
                    'water_usage' => 0,
                    'water_fee' => 0,
                    'internet_fee' => 0,
                    'other_fees' => $penaltyAmount,
                    'total_amount' => $penaltyAmount,
                    'due_date' => $dueDate,
                    'status' => 'Chưa thanh toán'
                ]);
                
                // Cập nhật invoice_id vào violation
                $this->db->update('violations', ['invoice_id' => $invoiceId], ['id' => $violationId]);
            }
        }
        
        $this->db->commit();
        
        $message = 'Thêm vi phạm thành công! Mã: ' . $violationNumber;
        if ($penaltyAmount > 0) {
            $message .= ' Hóa đơn tiền phạt ' . number_format($penaltyAmount) . 'đ đã được tạo.';
        }
        flash('success', $message);
        
    } catch (\Exception $e) {
        $this->db->rollback();
        flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
    
    return redirect('/violations');
}
    
    public function show($id) {
    $violation = $this->db->fetch("
        SELECT v.*, s.full_name, s.student_code, s.phone,
               r.title as rule_title, r.content as rule_content,
               u.full_name as reporter_name
        FROM violations v
        JOIN students s ON v.student_id = s.id
        LEFT JOIN rules r ON v.rule_id = r.id
        LEFT JOIN users u ON v.reported_by = u.id
        WHERE v.id = :id
    ", [':id' => $id]);
    
    if (!$violation) {
        flash('error', 'Không tìm thấy vi phạm');
        return redirect('/violations');
    }
    
    $data = [
        'title' => 'Chi tiết vi phạm - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Chi tiết vi phạm #' . $violation->violation_number,
        'violation' => $violation
    ];
    
    $this->view('violations/show', $data);
}
    
    public function process($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/violations');
        }
        
        $this->db->update('violations', [
            'status' => 'Đã xử lý',
            'processed_by' => auth()['id'],
            'processed_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
        
        flash('success', 'Đã xử lý vi phạm');
        return redirect('/violations');
    }
}