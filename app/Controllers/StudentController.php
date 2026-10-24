<?php
namespace App\Controllers;

use App\Core\Controller;

class StudentController extends Controller {
    
    /**
     * Danh sách sinh viên
     */
    public function index() {
        $search = $_GET['search'] ?? '';
        
        $sql = "SELECT s.*, u.email as user_email 
                FROM students s
                LEFT JOIN users u ON s.user_id = u.id";
        
        $params = [];
        if ($search) {
            $sql .= " WHERE s.full_name LIKE :search 
                       OR s.student_code LIKE :search 
                       OR s.email LIKE :search";
            $params[':search'] = "%{$search}%";
        }
        
        $sql .= " ORDER BY s.created_at DESC";
        
        $students = $this->db->fetchAll($sql, $params);
        
        $data = [
            'title' => 'Quản lý sinh viên - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Danh sách sinh viên',
            'students' => $students
        ];
        
        $this->view('students/index', $data);
    }
    
    /**
     * Form thêm sinh viên
     */
    public function create() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/dashboard');
        }
        
        $data = [
            'title' => 'Thêm sinh viên - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Thêm sinh viên mới'
        ];
        
        $this->view('students/create', $data);
    }
    
    /**
     * Lưu sinh viên mới
     */
    public function store() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/dashboard');
        }
        
        $data = [
            'student_code' => $_POST['student_code'] ?: null,
            'full_name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?: null,
            'phone' => $_POST['phone'] ?: null,
            'date_of_birth' => $_POST['date_of_birth'] ?: null,
            'gender' => $_POST['gender'] ?: null,
            'faculty' => $_POST['faculty'] ?: null,
            'class' => $_POST['class'] ?: null,
            'academic_year' => $_POST['academic_year'] ?: null,
            'permanent_address' => $_POST['permanent_address'] ?: null,
            'id_card' => $_POST['id_card'] ?: null
        ];
        
        $id = $this->db->insert('students', $data);
        
        if ($id) {
            flash('success', 'Thêm sinh viên thành công');
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại');
        }
        
        return redirect('/students');
    }
    
    /**
     * Chi tiết sinh viên
     */
    public function show($id) {
    $student = $this->db->fetch("SELECT s.*, u.status as status FROM students s LEFT JOIN users u ON s.user_id = u.id WHERE s.id = :id", [':id' => $id]);
        
        if (!$student) {
            flash('error', 'Không tìm thấy sinh viên');
            return redirect('/students');
        }
        
        // Lấy hợp đồng hiện tại
        $contract = $this->db->fetch("
            SELECT c.*, r.room_number, r.room_type, b.name as building_name, bed.bed_number
            FROM contracts c
            JOIN rooms r ON c.room_id = r.id
            JOIN buildings b ON r.building_id = b.id
            JOIN room_beds bed ON c.bed_id = bed.id
            WHERE c.student_id = :student_id AND c.status = 'Hiệu lực'
        ", [':student_id' => $id]);
        
        $data = [
            'title' => 'Chi tiết sinh viên - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Chi tiết sinh viên',
            'student' => $student,
            'contract' => $contract
        ];
        
        $this->view('students/show', $data);
    }
    
    /**
     * Form sửa sinh viên
     */
    public function edit($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/dashboard');
        }
        
    $student = $this->db->fetch("SELECT s.*, u.status as status FROM students s LEFT JOIN users u ON s.user_id = u.id WHERE s.id = :id", [':id' => $id]);
        
        if (!$student) {
            flash('error', 'Không tìm thấy sinh viên');
            return redirect('/students');
        }
        
        $data = [
            'title' => 'Sửa sinh viên - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Sửa thông tin sinh viên',
            'student' => $student
        ];
        
        $this->view('students/edit', $data);
    }
    
    /**
     * Cập nhật sinh viên
     */
    public function update($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/dashboard');
        }
        
        $data = [
            'student_code' => $_POST['student_code'] ?: null,
            'full_name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?: null,
            'phone' => $_POST['phone'] ?: null,
            'date_of_birth' => $_POST['date_of_birth'] ?: null,
            'gender' => $_POST['gender'] ?: null,
            'faculty' => $_POST['faculty'] ?: null,
            'class' => $_POST['class'] ?: null,
            'academic_year' => $_POST['academic_year'] ?: null,
            'permanent_address' => $_POST['permanent_address'] ?: null,
            'id_card' => $_POST['id_card'] ?: null
        ];
        
        $updated = $this->db->update('students', $data, ['id' => $id]);
        
        if ($updated !== false) {
            flash('success', 'Cập nhật thành công');
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại');
        }
        
        return redirect('/students');
    }
    
    /**
     * Xóa sinh viên
     */
    public function delete($id) {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/dashboard');
    }
    
    try {
        $this->db->beginTransaction();
        
        // Tắt FK check
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        
        // Xóa tất cả
        $tables = ['complaints', 'maintenance_requests', 'visitors', 'payments', 'invoices', 'check_out_requests', 'contracts', 'registrations'];
        foreach ($tables as $table) {
            $this->db->query("DELETE FROM {$table} WHERE student_id = :id", [':id' => $id]);
        }
        
        $student = $this->db->fetch("SELECT user_id FROM students WHERE id = :id", [':id' => $id]);
        $userId = $student ? $student->user_id : null;
        
        $this->db->query("DELETE FROM students WHERE id = :id", [':id' => $id]);
        
        if ($userId) {
            $this->db->query("DELETE FROM users WHERE id = :id", [':id' => $userId]);
        }
        
        // Bật lại FK check
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
        
        $this->db->commit();
        
        flash('success', 'Đã xóa sinh viên thành công');
        
    } catch (\Exception $e) {
        $this->db->rollback();
        // Đảm bảo bật lại FK check nếu có lỗi
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
        flash('error', 'Không thể xóa sinh viên: ' . $e->getMessage());
    }
    
    return redirect('/students');
}
    
    /**
     * Hồ sơ cá nhân (cho sinh viên)
     */
    public function profile() {
        $userId = auth()['id'];
        
        $student = $this->db->fetch("
            SELECT * FROM students WHERE user_id = :user_id
        ", [':user_id' => $userId]);
        
        if (!$student) {
            $student = (object)[
                'student_code' => '',
                'full_name' => auth()['full_name'] ?? '',
                'email' => auth()['email'] ?? '',
                'phone' => '',
                'date_of_birth' => '',
                'gender' => '',
                'faculty' => '',
                'class' => '',
                'academic_year' => '',
                'permanent_address' => '',
                'id_card' => ''
            ];
        }
        
        $data = [
            'title' => 'Hồ sơ cá nhân - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Hồ sơ sinh viên',
            'student' => $student
        ];
        
        $this->view('students/profile', $data);
    }
    
    /**
     * Cập nhật hồ sơ cá nhân
     */
    public function updateProfile() {
        $userId = auth()['id'];
        
        // Validate
        $errors = [];
        
        if (empty($_POST['full_name'])) {
            $errors['full_name'] = 'Vui lòng nhập họ tên';
        }
        
        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        if (!empty($_POST['student_code'])) {
            $existing = $this->db->fetch("
                SELECT id FROM students WHERE student_code = :code AND user_id != :user_id
            ", [':code' => $_POST['student_code'], ':user_id' => $userId]);
            
            if ($existing) {
                $errors['student_code'] = 'Mã số sinh viên đã được sử dụng';
            }
        }
        
        if (!empty($errors)) {
            flash('errors', $errors);
            flash('old', $_POST);
            return redirect('/profile');
        }
        
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", [':user_id' => $userId]);
        
        $data = [
            'student_code' => $_POST['student_code'] ?: null,
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'] ?: null,
            'phone' => $_POST['phone'] ?: null,
            'date_of_birth' => $_POST['date_of_birth'] ?: null,
            'gender' => $_POST['gender'] ?: null,
            'faculty' => $_POST['faculty'] ?: null,
            'class' => $_POST['class'] ?: null,
            'academic_year' => $_POST['academic_year'] ?: null,
            'permanent_address' => $_POST['permanent_address'] ?: null,
            'id_card' => $_POST['id_card'] ?: null
        ];
        
        if ($student) {
            $this->db->update('students', $data, ['id' => $student->id]);
        } else {
            $data['user_id'] = $userId;
            $this->db->insert('students', $data);
        }
        
        $_SESSION['user']['full_name'] = $_POST['full_name'];
        
        flash('success', 'Cập nhật hồ sơ thành công');
        return redirect('/profile');
    }
}