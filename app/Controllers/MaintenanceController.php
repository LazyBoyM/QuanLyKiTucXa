<?php
namespace App\Controllers;

use App\Core\Controller;

class MaintenanceController extends Controller {
    
    public function index() {
        if (is_admin()) {
            $sql = "SELECT mr.*, s.full_name, s.student_code, r.room_number
                    FROM maintenance_requests mr
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
            $requests = $this->db->fetchAll($sql);
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if ($student) {
                $sql = "SELECT mr.*, r.room_number
                        FROM maintenance_requests mr
                        JOIN rooms r ON mr.room_id = r.id
                        WHERE mr.student_id = :student_id
                        ORDER BY mr.created_at DESC";
                $requests = $this->db->fetchAll($sql, [':student_id' => $student->id]);
            } else {
                $requests = [];
            }
        }
        
        $data = [
            'title' => 'Yêu cầu bảo trì - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Danh sách yêu cầu bảo trì',
            'requests' => $requests
        ];
        
        $this->view('maintenance/index', $data);
    }
    
    public function create() {
        $student = $this->db->fetch("
            SELECT s.* FROM students s WHERE s.user_id = :user_id
        ", [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
            return redirect('/profile');
        }
        
        $contract = $this->db->fetch("
            SELECT c.*, r.room_number
            FROM contracts c
            JOIN rooms r ON c.room_id = r.id
            WHERE c.student_id = :student_id AND c.status = 'Hiệu lực'
        ", [':student_id' => $student->id]);
        
        if (!$contract) {
            flash('error', 'Bạn chưa có hợp đồng hiệu lực');
            return redirect('/maintenance');
        }
        
        $data = [
            'title' => 'Tạo yêu cầu bảo trì - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Tạo yêu cầu bảo trì',
            'contract' => $contract
        ];
        
        $this->view('maintenance/create', $data);
    }
    
    public function store() {
        $student = $this->db->fetch("
            SELECT id FROM students WHERE user_id = :user_id
        ", [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
            return redirect('/profile');
        }
        
        $contract = $this->db->fetch("
            SELECT * FROM contracts 
            WHERE student_id = :student_id AND status = 'Hiệu lực'
        ", [':student_id' => $student->id]);
        
        if (!$contract) {
            flash('error', 'Bạn chưa có hợp đồng hiệu lực');
            return redirect('/maintenance');
        }
        
        $requestNumber = 'MR' . date('Ymd') . rand(100, 999);
        
        $data = [
            'request_number' => $requestNumber,
            'student_id' => $student->id,
            'room_id' => $contract->room_id,
            'category' => $_POST['category'] ?? 'Khác',
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'priority' => $_POST['priority'] ?? 'Trung bình',
            'status' => 'Chờ xử lý'
        ];
        
        $id = $this->db->insert('maintenance_requests', $data);
        
        if ($id) {
            flash('success', 'Đã gửi yêu cầu bảo trì! Mã yêu cầu: ' . $requestNumber);
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
        
        return redirect('/maintenance');
    }
    
    public function show($id) {
        if (is_admin()) {
            $request = $this->db->fetch("
                SELECT mr.*, s.full_name, s.student_code, s.phone, r.room_number
                FROM maintenance_requests mr
                JOIN students s ON mr.student_id = s.id
                JOIN rooms r ON mr.room_id = r.id
                WHERE mr.id = :id
            ", [':id' => $id]);
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if (!$student) {
                flash('error', 'Không tìm thấy thông tin');
                return redirect('/maintenance');
            }
            
            $request = $this->db->fetch("
                SELECT mr.*, r.room_number
                FROM maintenance_requests mr
                JOIN rooms r ON mr.room_id = r.id
                WHERE mr.id = :id AND mr.student_id = :student_id
            ", [':id' => $id, ':student_id' => $student->id]);
        }
        
        if (!$request) {
            flash('error', 'Không tìm thấy yêu cầu');
            return redirect('/maintenance');
        }
        
        $data = [
            'title' => 'Chi tiết yêu cầu - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Chi tiết yêu cầu #' . $request->request_number,
            'request' => $request
        ];
        
        $this->view('maintenance/show', $data);
    }
    
    public function process($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/maintenance');
        }
        
        $this->db->update('maintenance_requests', [
            'status' => 'Đang xử lý',
            'assigned_to' => auth()['id']
        ], ['id' => $id]);
        
        flash('success', 'Đã nhận xử lý yêu cầu');
        return redirect('/maintenance');
    }
    
    public function complete($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/maintenance');
        }
        
        $this->db->update('maintenance_requests', [
            'status' => 'Đã hoàn thành',
            'completed_at' => date('Y-m-d H:i:s'),
            'completed_by' => auth()['id']
        ], ['id' => $id]);
        
        flash('success', 'Đã hoàn thành yêu cầu bảo trì');
        return redirect('/maintenance');
    }
}