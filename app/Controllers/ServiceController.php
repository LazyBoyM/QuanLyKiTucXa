<?php
namespace App\Controllers;

use App\Core\Controller;

class ServiceController extends Controller {

    public function index() {
        if (is_admin()) {
            $sql = "SELECT sr.*, s.full_name, s.student_code FROM service_registrations sr JOIN students s ON sr.student_id = s.id ORDER BY sr.created_at DESC";
            $items = $this->db->fetchAll($sql);
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", [':user_id' => auth()['id']]);
            if ($student) {
                $items = $this->db->fetchAll("SELECT sr.* FROM service_registrations sr WHERE sr.student_id = :student_id ORDER BY sr.created_at DESC", [':student_id' => $student->id]);
            } else {
                $items = [];
            }
        }

        $data = [
            'title' => 'Đăng ký dịch vụ - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Đăng ký dịch vụ',
            'items' => $items
        ];

        $this->view('services/index', $data);
    }

    public function create() {
        // Ensure student exists and has contract
        $student = $this->db->fetch("SELECT s.* FROM students s WHERE s.user_id = :user_id", [':user_id' => auth()['id']]);
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
            return redirect('/profile');
        }

        $contract = $this->db->fetch("SELECT c.*, r.room_number FROM contracts c JOIN rooms r ON c.room_id = r.id WHERE c.student_id = :student_id AND c.status = 'Hiệu lực'", [':student_id' => $student->id]);
        if (!$contract) {
            flash('error', 'Bạn chưa có hợp đồng hiệu lực');
            return redirect('/services');
        }

        $data = [
            'title' => 'Đăng ký dịch vụ - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Đăng ký dịch vụ mới',
            'contract' => $contract,
            'service_types' => ['Giặt ủi', 'Gửi xe', 'Dọn phòng', 'Sửa chữa', 'Khác']
        ];

        $this->view('services/create', $data);
    }

    public function store() {
        // CSRF protection
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token không hợp lệ');
            return redirect('/services/create');
        }

        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", [':user_id' => auth()['id']]);
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
            return redirect('/profile');
        }

        $contract = $this->db->fetch("SELECT * FROM contracts WHERE student_id = :student_id AND status = 'Hiệu lực'", [':student_id' => $student->id]);
        if (!$contract) {
            flash('error', 'Bạn chưa có hợp đồng hiệu lực');
            return redirect('/services');
        }

        $regNumber = 'SR' . date('Ymd') . rand(100,999);

        $data = [
            'registration_number' => $regNumber,
            'student_id' => $student->id,
            'service_type' => $_POST['service_type'] ?? 'Khác',
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'requested_date' => $_POST['requested_date'] ?? date('Y-m-d'),
            'status' => 'Chờ xử lý'
        ];

        $id = $this->db->insert('service_registrations', $data);

        if ($id) {
            flash('success', 'Đăng ký dịch vụ thành công. Mã: ' . $regNumber);
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại');
        }

        return redirect('/services');
    }

    public function show($id) {
        if (is_admin()) {
            $item = $this->db->fetch("SELECT sr.*, s.full_name, s.student_code FROM service_registrations sr JOIN students s ON sr.student_id = s.id WHERE sr.id = :id", [':id' => $id]);
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", [':user_id' => auth()['id']]);
            if (!$student) {
                flash('error', 'Không tìm thấy thông tin');
                return redirect('/services');
            }
            $item = $this->db->fetch("SELECT sr.* FROM service_registrations sr WHERE sr.id = :id AND sr.student_id = :student_id", [':id' => $id, ':student_id' => $student->id]);
        }

        if (!$item) {
            flash('error', 'Không tìm thấy đăng ký');
            return redirect('/services');
        }

        $data = [
            'title' => 'Chi tiết đăng ký - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Chi tiết đăng ký #' . $item->registration_number,
            'item' => $item
        ];

        $this->view('services/show', $data);
    }

    public function process($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/services');
        }

        // CSRF check for state change
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token không hợp lệ');
            return redirect('/services');
        }

        $this->db->update('service_registrations', [
            'status' => 'Đang xử lý',
            'assigned_to' => auth()['id']
        ], ['id' => $id]);

        flash('success', 'Đã nhận xử lý đăng ký');
        return redirect('/services');
    }

    public function complete($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/services');
        }

        // CSRF check for state change
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token không hợp lệ');
            return redirect('/services');
        }

        $this->db->update('service_registrations', [
            'status' => 'Đã hoàn thành',
            'processed_at' => date('Y-m-d H:i:s'),
            'processed_by' => auth()['id']
        ], ['id' => $id]);

        flash('success', 'Đã hoàn thành đăng ký dịch vụ');
        return redirect('/services');
    }
}
