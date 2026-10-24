<?php
namespace App\Controllers;

use App\Core\Controller;

class VisitorController extends Controller {
    
    public function index() {
    if (is_admin()) {
        $sql = "SELECT v.*, s.full_name as student_name, s.student_code, r.room_number
                FROM visitors v
                JOIN students s ON v.student_id = s.id
                JOIN rooms r ON v.room_id = r.id
                ORDER BY v.check_in_time DESC";
        $visitors = $this->db->fetchAll($sql);
        } else {
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if ($student) {
            // include student's full name so the view can always use ->student_name
            $sql = "SELECT v.*, s.full_name as student_name, s.student_code, r.room_number
                    FROM visitors v
                    JOIN students s ON v.student_id = s.id
                    JOIN rooms r ON v.room_id = r.id
                    WHERE v.student_id = :student_id
                    ORDER BY v.check_in_time DESC";
            $visitors = $this->db->fetchAll($sql, [':student_id' => $student->id]);
        } else {
            $visitors = [];
        }
    }
    
    $data = [
        'title' => 'Quản lý khách - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Quản lý khách đến thăm',
        'visitors' => $visitors,
        'visitor_hours' => setting('visitor_hours', '06:00 - 22:00')
    ];
    
    $this->view('visitors/index', $data);
}
    
    public function create() {
        // Kiểm tra sinh viên có hợp đồng không
        $student = $this->db->fetch("SELECT * FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên trước');
            return redirect('/profile');
        }
        
        $contract = $this->db->fetch("
            SELECT * FROM contracts 
            WHERE student_id = :student_id AND status = 'Hiệu lực'
        ", [':student_id' => $student->id]);
        
        if (!$contract) {
            flash('error', 'Bạn chưa có hợp đồng hiệu lực');
            return redirect('/visitors');
        }
        
        $data = [
            'title' => 'Đăng ký khách - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Đăng ký khách đến thăm',
            'visitor_hours' => setting('visitor_hours', '06:00 - 22:00')
        ];
        
        $this->view('visitors/create', $data);
    }
    
    public function store() {
        // Kiểm tra giờ thăm
        $visitorHours = setting('visitor_hours', '06:00 - 22:00');
        
        // Parse giờ thăm
        if (preg_match('/(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})/', $visitorHours, $matches)) {
            $startHour = (int)$matches[1];
            $startMin = (int)$matches[2];
            $endHour = (int)$matches[3];
            $endMin = (int)$matches[4];
            
            $currentHour = (int)date('H');
            $currentMin = (int)date('i');
            
            $currentTime = $currentHour * 60 + $currentMin;
            $startTime = $startHour * 60 + $startMin;
            $endTime = $endHour * 60 + $endMin;
            
            if ($currentTime < $startTime || $currentTime > $endTime) {
                flash('error', 'Chỉ được đăng ký khách trong khoảng ' . $visitorHours);
                return redirect('/visitors/create');
            }
        }
        
        // Lấy thông tin sinh viên
        $student = $this->db->fetch("SELECT * FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Không tìm thấy thông tin sinh viên');
            return redirect('/visitors');
        }
        
        // Lấy hợp đồng hiệu lực
        $contract = $this->db->fetch("
            SELECT * FROM contracts 
            WHERE student_id = :student_id AND status = 'Hiệu lực'
        ", [':student_id' => $student->id]);
        
        if (!$contract) {
            flash('error', 'Bạn chưa có hợp đồng hiệu lực');
            return redirect('/visitors');
        }
        
        // Validate dữ liệu
        if (empty($_POST['visitor_name'])) {
            flash('error', 'Vui lòng nhập tên khách');
            return redirect('/visitors/create');
        }
        
        $data = [
            'student_id' => $student->id,
            'room_id' => $contract->room_id,
            'visitor_name' => $_POST['visitor_name'],
            'id_card' => $_POST['id_card'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'relationship' => $_POST['relationship'] ?? null,
            'purpose' => $_POST['purpose'] ?? null,
            'registered_by' => auth()['id'],
            'status' => 'checked_in'
        ];
        
        $id = $this->db->insert('visitors', $data);
        
        if ($id) {
            flash('success', 'Đã đăng ký khách thành công!');
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
        
        return redirect('/visitors');
    }
    
    public function checkout($id) {
        $this->db->update('visitors', [
            'check_out_time' => date('Y-m-d H:i:s'),
            'status' => 'checked_out'
        ], ['id' => $id]);
        
        flash('success', 'Đã check-out thành công!');
        return redirect('/visitors');
    }
}