<?php
namespace App\Controllers;

use App\Core\Controller;

class ComplaintController extends Controller {
    
    public function index() {
        if (is_admin()) {
            $sql = "SELECT c.*, s.full_name, s.student_code
                    FROM complaints c
                    LEFT JOIN students s ON c.student_id = s.id
                    ORDER BY 
                        CASE c.status 
                            WHEN 'Chờ xử lý' THEN 1
                            WHEN 'Đang xem xét' THEN 2
                            ELSE 3
                        END,
                        c.created_at DESC";
            $complaints = $this->db->fetchAll($sql);
            
            $stats = [
                'pending' => $this->db->fetch("SELECT COUNT(*) as count FROM complaints WHERE status = 'Chờ xử lý'")->count ?? 0,
                'processing' => $this->db->fetch("SELECT COUNT(*) as count FROM complaints WHERE status = 'Đang xem xét'")->count ?? 0,
                'resolved' => $this->db->fetch("SELECT COUNT(*) as count FROM complaints WHERE status = 'Đã giải quyết'")->count ?? 0
            ];
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if ($student) {
                $sql = "SELECT * FROM complaints 
                        WHERE student_id = :student_id 
                        ORDER BY created_at DESC";
                $complaints = $this->db->fetchAll($sql, [':student_id' => $student->id]);
            } else {
                $complaints = [];
            }
            $stats = null;
        }
        
        $data = [
            'title' => 'Góp ý - Khiếu nại - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Góp ý - Khiếu nại',
            'complaints' => $complaints,
            'stats' => $stats
        ];
        
        $this->view('complaints/index', $data);
    }
    
    public function create() {
        $student = $this->db->fetch("SELECT * FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
            return redirect('/profile');
        }
        
        $data = [
            'title' => 'Gửi góp ý - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Gửi góp ý - Khiếu nại'
        ];
        
        $this->view('complaints/create', $data);
    }
    
    public function store() {
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
            return redirect('/profile');
        }
        
        $data = [
            'complaint_number' => 'CP' . date('Ymd') . rand(100, 999),
            'student_id' => $student->id,
            'category' => $_POST['category'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'is_anonymous' => isset($_POST['is_anonymous']) ? 1 : 0,
            'status' => 'Chờ xử lý'
        ];
        
        $id = $this->db->insert('complaints', $data);
        
        if ($id) {
            flash('success', 'Đã gửi góp ý! Cảm ơn bạn.');
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
        
        return redirect('/complaints');
    }
    
    public function show($id) {
        if (is_admin()) {
            $complaint = $this->db->fetch("
                SELECT c.*, s.full_name, s.student_code
                FROM complaints c
                LEFT JOIN students s ON c.student_id = s.id
                WHERE c.id = :id
            ", [':id' => $id]);
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if (!$student) {
                flash('error', 'Không tìm thấy thông tin');
                return redirect('/complaints');
            }
            
            $complaint = $this->db->fetch("
                SELECT * FROM complaints 
                WHERE id = :id AND student_id = :student_id
            ", [':id' => $id, ':student_id' => $student->id]);
        }
        
        if (!$complaint) {
            flash('error', 'Không tìm thấy góp ý hoặc không có quyền xem');
            return redirect('/complaints');
        }
        
        $data = [
            'title' => 'Chi tiết góp ý - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Chi tiết góp ý #' . $complaint->complaint_number,
            'complaint' => $complaint
        ];
        
        $this->view('complaints/show', $data);
    }
    
    public function respond($id) {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/complaints');
    }
    
    $complaint = $this->db->fetch("SELECT * FROM complaints WHERE id = :id", [':id' => $id]);
    
    if (!$complaint) {
        flash('error', 'Không tìm thấy góp ý');
        return redirect('/complaints');
    }
    
    $data = [
        'title' => 'Phản hồi góp ý - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Phản hồi góp ý',
        'complaint' => $complaint
    ];
    
    $this->view('complaints/respond', $data);
}
    
    public function storeResponse($id) {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/complaints');
    }
    
    $status = isset($_POST['resolve']) ? 'Đã giải quyết' : 'Đang xem xét';
    
    $this->db->update('complaints', [
        'status' => $status,
        'response' => $_POST['response'],
        'responded_by' => auth()['id'],
        'responded_at' => date('Y-m-d H:i:s')
    ], ['id' => $id]);
    
    flash('success', 'Đã gửi phản hồi!');
    return redirect('/complaints');
}
}