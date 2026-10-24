<?php
namespace App\Controllers;

use App\Core\Controller;

class ContractController extends Controller {
    
    public function index() {
        if (is_admin()) {
            $sql = "SELECT c.*, s.full_name, s.student_code, r.room_number, b.bed_number
                    FROM contracts c
                    JOIN students s ON c.student_id = s.id
                    JOIN rooms r ON c.room_id = r.id
                    JOIN room_beds b ON c.bed_id = b.id
                    ORDER BY c.created_at DESC";
            $contracts = $this->db->fetchAll($sql);
            
            $stats = [
                'active' => $this->db->fetch("SELECT COUNT(*) as count FROM contracts WHERE status = 'Hiệu lực'")->count ?? 0,
                'ended' => $this->db->fetch("SELECT COUNT(*) as count FROM contracts WHERE status = 'Đã kết thúc'")->count ?? 0
            ];
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if ($student) {
                $sql = "SELECT c.*, r.room_number, b.name as building_name, bed.bed_number
                        FROM contracts c
                        JOIN rooms r ON c.room_id = r.id
                        JOIN buildings b ON r.building_id = b.id
                        JOIN room_beds bed ON c.bed_id = bed.id
                        WHERE c.student_id = :student_id
                        ORDER BY c.created_at DESC";
                $contracts = $this->db->fetchAll($sql, [':student_id' => $student->id]);
            } else {
                $contracts = [];
            }
            $stats = null;
        }
        
        $data = [
            'title' => 'Hợp đồng - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Quản lý hợp đồng',
            'contracts' => $contracts,
            'stats' => $stats
        ];
        
        $this->view('contracts/index', $data);
    }
    
    public function show($id) {
    if (is_admin()) {
        $contract = $this->db->fetch("
            SELECT c.*, s.full_name, s.student_code, s.email, s.phone, s.date_of_birth, s.gender,
                   s.faculty, s.class, s.academic_year,
                   r.room_number, r.floor, r.room_type, r.capacity, r.current_occupancy, r.price,
                   b.name as building_name, b.code as building_code,
                   bed.bed_number
            FROM contracts c
            JOIN students s ON c.student_id = s.id
            JOIN rooms r ON c.room_id = r.id
            JOIN buildings b ON r.building_id = b.id
            JOIN room_beds bed ON c.bed_id = bed.id
            WHERE c.id = :id
        ", [':id' => $id]);
    } else {
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Không tìm thấy thông tin');
            return redirect('/contracts');
        }
        
        $contract = $this->db->fetch("
            SELECT c.*, r.room_number, r.floor, r.room_type, r.capacity, r.current_occupancy, r.price,
                   b.name as building_name, b.code as building_code,
                   bed.bed_number
            FROM contracts c
            JOIN rooms r ON c.room_id = r.id
            JOIN buildings b ON r.building_id = b.id
            JOIN room_beds bed ON c.bed_id = bed.id
            WHERE c.id = :id AND c.student_id = :student_id
        ", [':id' => $id, ':student_id' => $student->id]);
    }
    
    if (!$contract) {
        flash('error', 'Không tìm thấy hợp đồng hoặc không có quyền xem');
        return redirect('/contracts');
    }
    
    // Tạo object room từ dữ liệu contract
    $room = (object)[
        'building_name' => $contract->building_name ?? 'N/A',
        'room_number' => $contract->room_number ?? 'N/A',
        'floor' => $contract->floor ?? 'N/A',
        'room_type' => $contract->room_type ?? 'N/A',
        'current_occupancy' => $contract->current_occupancy ?? 0,
        'capacity' => $contract->capacity ?? 0,
        'price' => $contract->price ?? 0
    ];
    
    // Tạo object student nếu chưa có (cho admin)
    if (!isset($contract->full_name)) {
        $studentData = $this->db->fetch("SELECT * FROM students WHERE id = :id", [':id' => $contract->student_id]);
        $student = $studentData ?: (object)[
            'full_name' => 'N/A',
            'student_code' => 'N/A',
            'email' => 'N/A',
            'phone' => 'N/A',
            'date_of_birth' => null,
            'gender' => 'N/A',
            'faculty' => 'N/A',
            'class' => 'N/A',
            'academic_year' => 'N/A'
        ];
    } else {
        $student = $contract;
    }
    
    $data = [
        'title' => 'Chi tiết hợp đồng - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Chi tiết hợp đồng #' . $contract->contract_number,
        'contract' => $contract,
        'room' => $room,
        'student' => $student
    ];
    
    $this->view('contracts/show', $data);
}
}