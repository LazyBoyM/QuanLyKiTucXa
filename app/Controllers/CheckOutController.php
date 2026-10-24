<?php
namespace App\Controllers;

use App\Core\Controller;

class CheckOutController extends Controller {
    
    public function index() {
    if (is_admin()) {
        $sql = "SELECT co.*, s.full_name, s.student_code, r.room_number
                FROM check_out_requests co
                JOIN students s ON co.student_id = s.id
                JOIN rooms r ON co.room_id = r.id
                ORDER BY 
                    CASE co.status 
                        WHEN 'Chờ duyệt' THEN 1
                        WHEN 'Đã duyệt' THEN 2
                        ELSE 3
                    END,
                    co.created_at DESC";
        $requests = $this->db->fetchAll($sql);
        
        // Thêm thống kê
        $pending_count = $this->db->fetch("SELECT COUNT(*) as count FROM check_out_requests WHERE status = 'Chờ duyệt'")->count ?? 0;
        $approved_count = $this->db->fetch("SELECT COUNT(*) as count FROM check_out_requests WHERE status = 'Đã duyệt'")->count ?? 0;
        $completed_count = $this->db->fetch("SELECT COUNT(*) as count FROM check_out_requests WHERE status = 'Đã hoàn thành'")->count ?? 0;
    } else {
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if ($student) {
            $sql = "SELECT co.*, r.room_number
                    FROM check_out_requests co
                    JOIN rooms r ON co.room_id = r.id
                    WHERE co.student_id = :student_id
                    ORDER BY co.created_at DESC";
            $requests = $this->db->fetchAll($sql, [':student_id' => $student->id]);
        } else {
            $requests = [];
        }
        $pending_count = $approved_count = $completed_count = 0;
    }
    
    $data = [
        'title' => 'Yêu cầu trả phòng - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Yêu cầu trả phòng',
        'requests' => $requests,
        'pending_count' => $pending_count,
        'approved_count' => $approved_count,
        'completed_count' => $completed_count
    ];
    
    $this->view('check-out/index', $data);
}
    
    public function create() {
    $student = $this->db->fetch("
        SELECT * FROM students WHERE user_id = :user_id
    ", [':user_id' => auth()['id']]);
    
    if (!$student) {
        flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
        return redirect('/profile');
    }
    
    // Kiểm tra có hợp đồng hiệu lực không
    $contract = $this->db->fetch("
        SELECT c.*, r.room_number, r.room_type, b.name as building_name
        FROM contracts c
        JOIN rooms r ON c.room_id = r.id
        JOIN buildings b ON r.building_id = b.id
        WHERE c.student_id = :student_id AND c.status = 'Hiệu lực'
    ", [':student_id' => $student->id]);
    
    if (!$contract) {
        flash('error', 'Bạn không có hợp đồng hiệu lực');
        return redirect('/check-out');
    }
    
    // Kiểm tra đã có yêu cầu đang chờ chưa
    $existing = $this->db->fetch("
        SELECT * FROM check_out_requests 
        WHERE student_id = :student_id AND status IN ('Chờ duyệt', 'Đã duyệt')
    ", [':student_id' => $student->id]);
    
    if ($existing) {
        flash('error', 'Bạn đã có yêu cầu trả phòng đang được xử lý');
        return redirect('/check-out');
    }
    
    $data = [
        'title' => 'Yêu cầu trả phòng - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Yêu cầu trả phòng',
        'contract' => $contract
    ];
    
    $this->view('check-out/create', $data);
}
    
    public function store() {
        $student = $this->db->fetch("
            SELECT id FROM students WHERE user_id = :user_id
        ", [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Không tìm thấy thông tin sinh viên');
            return redirect('/check-out');
        }
        
        $contract = $this->db->fetch("
            SELECT * FROM contracts 
            WHERE student_id = :student_id AND status = 'Hiệu lực'
        ", [':student_id' => $student->id]);
        
        if (!$contract) {
            flash('error', 'Bạn không có hợp đồng hiệu lực');
            return redirect('/check-out');
        }
        
        // Tạo mã yêu cầu
        $requestNumber = 'CO' . date('Ymd') . rand(100, 999);
        
        $data = [
            'request_number' => $requestNumber,
            'contract_id' => $contract->id,
            'student_id' => $student->id,
            'room_id' => $contract->room_id,
            'request_date' => $_POST['request_date'],
            'reason' => $_POST['reason'] ?? null,
            'notes' => $_POST['notes'] ?? null,
            'status' => 'Chờ duyệt'
        ];
        
        $id = $this->db->insert('check_out_requests', $data);
        
        if ($id) {
            flash('success', 'Yêu cầu trả phòng đã được gửi! Mã yêu cầu: ' . $requestNumber);
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại');
        }
        
        return redirect('/check-out');
    }
    
   public function approve() {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/dashboard');
    }
    
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        flash('error', 'Thiếu thông tin yêu cầu');
        return redirect('/check-out');
    }
    
    // Kiểm tra yêu cầu tồn tại
    $request = $this->db->fetch("SELECT * FROM check_out_requests WHERE id = :id", [':id' => $id]);
    
    if (!$request) {
        flash('error', 'Không tìm thấy yêu cầu');
        return redirect('/check-out');
    }
    
    if ($request->status != 'Chờ duyệt') {
        flash('error', 'Yêu cầu không ở trạng thái chờ duyệt');
        return redirect('/check-out');
    }
    
    // Cập nhật trạng thái
    $this->db->update('check_out_requests', [
        'status' => 'Đã duyệt',
        'approved_by' => auth()['id'],
        'approved_at' => date('Y-m-d H:i:s')
    ], ['id' => $id]);
    
    flash('success', 'Đã duyệt yêu cầu trả phòng');
    return redirect('/check-out');
}

public function complete() {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/dashboard');
    }
    
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        flash('error', 'Thiếu thông tin yêu cầu');
        return redirect('/check-out');
    }
    
    $request = $this->db->fetch("SELECT * FROM check_out_requests WHERE id = :id", [':id' => $id]);
    
    if (!$request) {
        flash('error', 'Không tìm thấy yêu cầu');
        return redirect('/check-out');
    }
    
    if ($request->status != 'Đã duyệt') {
        flash('error', 'Yêu cầu chưa được duyệt');
        return redirect('/check-out');
    }
    
    // Bắt đầu transaction
    $this->db->beginTransaction();
    
    try {
        // 1. Cập nhật trạng thái yêu cầu
        $this->db->update('check_out_requests', [
            'status' => 'Đã hoàn thành',
            'completed_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
        
        // 2. Cập nhật hợp đồng
        $this->db->update('contracts', [
            'status' => 'Đã kết thúc',
            'check_out_date' => date('Y-m-d')
        ], ['id' => $request->contract_id]);
        
        // 3. Cập nhật giường - trả về trống
            $this->db->query("
                UPDATE room_beds 
                SET status = 'Trống', student_id = NULL, check_in_date = NULL
                WHERE id = (SELECT bed_id FROM contracts WHERE id = :contract_id)
            ", [':contract_id' => $request->contract_id]);
        
        // 4. Giảm số người trong phòng
        $this->db->query("
            UPDATE rooms 
            SET current_occupancy = current_occupancy - 1,
                status = CASE 
                    WHEN current_occupancy - 1 = 0 THEN 'Trống'
                    WHEN current_occupancy - 1 < capacity THEN 'Còn chỗ'
                    ELSE status
                END
            WHERE id = :room_id
        ", [':room_id' => $request->room_id]);
        
        $this->db->commit();
        
        flash('success', 'Đã hoàn thành trả phòng');
    } catch (\Exception $e) {
        $this->db->rollback();
        flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
    
    return redirect('/check-out');
}
}