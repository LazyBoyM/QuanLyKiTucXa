<?php
namespace App\Controllers;

use App\Core\Controller;

class RegistrationController extends Controller {
    
    public function index() {
    if (is_admin()) {
        // Admin xem tất cả đơn - ĐẢM BẢO JOIN ĐÚNG
        $sql = "SELECT r.*, 
                       s.full_name, 
                       s.student_code, 
                       b.name as building_name
                FROM registrations r
                LEFT JOIN students s ON r.student_id = s.id
                LEFT JOIN buildings b ON r.building_id = b.id
                ORDER BY 
                    CASE r.status 
                        WHEN 'Chờ duyệt' THEN 1
                        WHEN 'Đã duyệt' THEN 2
                        WHEN 'Đã xếp phòng' THEN 3
                        ELSE 4
                    END,
                    r.created_at DESC";
        $registrations = $this->db->fetchAll($sql);
        
        $pending_count = $this->db->fetch("SELECT COUNT(*) as count FROM registrations WHERE status = 'Chờ duyệt'")->count ?? 0;
        $approved_count = $this->db->fetch("SELECT COUNT(*) as count FROM registrations WHERE status = 'Đã duyệt'")->count ?? 0;
    } else {
        // Sinh viên chỉ xem đơn của mình
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if ($student) {
            $sql = "SELECT r.*, b.name as building_name
                    FROM registrations r
                    LEFT JOIN buildings b ON r.building_id = b.id
                    WHERE r.student_id = :student_id
                    ORDER BY r.created_at DESC";
            $registrations = $this->db->fetchAll($sql, [':student_id' => $student->id]);
        } else {
            $registrations = [];
        }
        $pending_count = $approved_count = 0;
    }
    
    $data = [
        'title' => 'Đơn đăng ký KTX - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Quản lý đơn đăng ký',
        'registrations' => $registrations,
        'pending_count' => $pending_count,
        'approved_count' => $approved_count
    ];
    
    $this->view('registrations/index', $data);
}
    
    public function create() {
        // Lấy thông tin sinh viên từ user đang đăng nhập
        $student = $this->db->fetch("
            SELECT * FROM students WHERE user_id = :user_id
        ", [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên trước khi đăng ký KTX');
            return redirect('/profile');
        }
        
        // Kiểm tra đã có đơn đăng ký đang chờ xử lý chưa
        $existing = $this->db->fetch("
            SELECT * FROM registrations 
            WHERE student_id = :student_id 
            AND status IN ('Chờ duyệt', 'Đã duyệt')
        ", [':student_id' => $student->id]);
        
        if ($existing) {
            flash('error', 'Bạn đã có đơn đăng ký đang được xử lý. Vui lòng chờ kết quả.');
            return redirect('/registrations');
        }
        
        // Kiểm tra đã có hợp đồng hiệu lực chưa
        $contract = $this->db->fetch("
            SELECT * FROM contracts 
            WHERE student_id = :student_id AND status = 'Hiệu lực'
        ", [':student_id' => $student->id]);
        
        if ($contract) {
            flash('error', 'Bạn đang có hợp đồng hiệu lực. Không thể đăng ký thêm.');
            return redirect('/registrations');
        }
        
        $buildings = $this->db->fetchAll("
            SELECT * FROM buildings 
            WHERE gender IN ('Tất cả', :gender)
            ORDER BY code
        ", [':gender' => $student->gender ?? 'Tất cả']);
        
        $data = [
            'title' => 'Đăng ký KTX',
            'pageTitle' => 'Đăng ký Ký túc xá',
            'student' => $student,
            'buildings' => $buildings
        ];
        
        ob_start();
        require __DIR__ . '/../Views/registrations/create.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/app.php';
    }
    
    public function store() {
        $student = $this->db->fetch("
            SELECT id FROM students WHERE user_id = :user_id
        ", [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên trước');
            return redirect('/profile');
        }
        
        // Tạo mã đơn
        $registrationNumber = 'REG' . date('Ymd') . rand(100, 999);
        
        $data = [
            'registration_number' => $registrationNumber,
            'student_id' => $student->id,
            'building_id' => $_POST['building_id'] ?: null,
            'desired_room_type' => $_POST['desired_room_type'],
            'semester' => $_POST['semester'],
            'academic_year' => $_POST['academic_year'],
            'registration_date' => date('Y-m-d'),
            'expected_check_in' => $_POST['expected_check_in'],
            'notes' => $_POST['notes'] ?? null,
            'status' => 'Chờ duyệt'
        ];
        
        $id = $this->db->insert('registrations', $data);
        
        if ($id) {
            flash('success', 'Đăng ký thành công! Mã đơn: ' . $registrationNumber . '. Vui lòng chờ duyệt.');
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
        
        return redirect('/registrations');
    }
    
    public function show($id) {
    $registration = $this->db->fetch("
        SELECT r.*, b.name as building_name, b.code as building_code
        FROM registrations r
        LEFT JOIN buildings b ON r.building_id = b.id
        WHERE r.id = :id
    ", [':id' => $id]);
    
    if (!$registration) {
        flash('error', 'Không tìm thấy đơn đăng ký');
        return redirect('/registrations');
    }
    
    // Lấy thông tin sinh viên
    $student = $this->db->fetch("
        SELECT * FROM students WHERE id = :student_id
    ", [':student_id' => $registration->student_id]);
    
    // Nếu không tìm thấy student, tạo object rỗng để tránh lỗi
    if (!$student) {
        $student = (object)[
            'full_name' => 'N/A',
            'student_code' => 'N/A',
            'email' => 'N/A',
            'phone' => 'N/A',
            'faculty' => 'N/A',
            'class' => 'N/A',
            'academic_year' => 'N/A'
        ];
    }
    
    // Kiểm tra quyền xem
    if (!is_admin()) {
        $currentStudent = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if (!$currentStudent || $currentStudent->id != $registration->student_id) {
            flash('error', 'Bạn không có quyền xem đơn này');
            return redirect('/registrations');
        }
    }
    
    // Kiểm tra đã có hợp đồng chưa
    $contract = $this->db->fetch("
        SELECT c.*, r.room_number, bed.bed_number
        FROM contracts c
        JOIN rooms r ON c.room_id = r.id
        JOIN room_beds bed ON c.bed_id = bed.id
        WHERE c.registration_id = :registration_id
    ", [':registration_id' => $id]);
    
    $data = [
        'title' => 'Chi tiết đơn đăng ký - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Chi tiết đơn #' . $registration->registration_number,
        'registration' => $registration,
        'student' => $student,
        'contract' => $contract
    ];
    
    $this->view('registrations/show', $data);
}
    
    public function approve($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/registrations');
        }
        
        $registration = $this->db->fetch("SELECT * FROM registrations WHERE id = :id", [':id' => $id]);
        
        if (!$registration) {
            flash('error', 'Không tìm thấy đơn');
            return redirect('/registrations');
        }
        
        if ($registration->status != 'Chờ duyệt') {
            flash('error', 'Đơn không ở trạng thái chờ duyệt');
            return redirect('/registrations');
        }
        
        $this->db->update('registrations', [
            'status' => 'Đã duyệt',
            'approved_by' => auth()['id'],
            'approved_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
        
        flash('success', 'Đã duyệt đơn đăng ký! Tiếp theo hãy xếp phòng cho sinh viên.');
        return redirect('/registrations');
    }
    
    public function assignRoom($id) {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/registrations');
    }
    
    // Lấy thông tin đơn đăng ký VÀ thông tin sinh viên
    $registration = $this->db->fetch("
        SELECT r.*, s.id as student_id, s.full_name, s.gender, s.student_code
        FROM registrations r
        JOIN students s ON r.student_id = s.id
        WHERE r.id = :id
    ", [':id' => $id]);
    
    if (!$registration) {
        flash('error', 'Không tìm thấy đơn');
        return redirect('/registrations');
    }
    
    if ($registration->status != 'Đã duyệt') {
        flash('error', 'Đơn chưa được duyệt');
        return redirect('/registrations');
    }
    
    // Lấy danh sách tòa nhà phù hợp với giới tính
    $buildings = $this->db->fetchAll("
        SELECT * FROM buildings 
        WHERE gender IN ('Tất cả', :gender)
        ORDER BY code
    ", [':gender' => $registration->gender ?? 'Tất cả']);
    
    // Tạo object student để truyền vào view
    $student = (object)[
        'full_name' => $registration->full_name,
        'student_code' => $registration->student_code,
        'gender' => $registration->gender
    ];
    
    $data = [
        'title' => 'Xếp phòng - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Xếp phòng cho sinh viên',
        'registration' => $registration,
        'student' => $student,
        'buildings' => $buildings,
        'default_deposit' => setting('default_deposit', 500000)
    ];
    
    $this->view('registrations/assign-room', $data);
}
    
    public function storeAssignment($id) {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/registrations');
    }
    
    $registration = $this->db->fetch("SELECT * FROM registrations WHERE id = :id", [':id' => $id]);
    
    if (!$registration || $registration->status != 'Đã duyệt') {
        flash('error', 'Đơn không hợp lệ');
        return redirect('/registrations');
    }
    
    $roomId = $_POST['room_id'];
    $bedId = $_POST['bed_id'];
    $checkInDate = $_POST['check_in_date'];
    $deposit = $_POST['deposit'] ?? setting('default_deposit', 500000);
    
    $room = $this->db->fetch("SELECT * FROM rooms WHERE id = :id", [':id' => $roomId]);
    
    if (!$room) {
        flash('error', 'Phòng không tồn tại');
        return back();
    }
    
    $bed = $this->db->fetch("SELECT * FROM room_beds WHERE id = :id AND status = 'Trống'", [':id' => $bedId]);
    
    if (!$bed) {
        flash('error', 'Giường đã có người thuê');
        return back();
    }
    
    $this->db->beginTransaction();
    
    try {
        // 1. Cập nhật giường
        $this->db->update('room_beds', [
            'status' => 'Đã thuê',
            'student_id' => $registration->student_id,
            'check_in_date' => $checkInDate
        ], ['id' => $bedId]);
        
        // 2. Cập nhật phòng
        $this->db->query("
            UPDATE rooms SET current_occupancy = current_occupancy + 1,
            status = CASE 
                WHEN current_occupancy + 1 >= capacity THEN 'Đã đầy'
                ELSE 'Còn chỗ'
            END
            WHERE id = :id
        ", [':id' => $roomId]);
        
        // 3. Tạo hợp đồng
        $contractNumber = 'CT' . date('Ymd') . rand(100, 999);
        $contractId = $this->db->insert('contracts', [
            'contract_number' => $contractNumber,
            'registration_id' => $id,
            'student_id' => $registration->student_id,
            'room_id' => $roomId,
            'bed_id' => $bedId,
            'check_in_date' => $checkInDate,
            'monthly_rent' => $room->price,
            'deposit' => $deposit,
            'status' => 'Hiệu lực'
        ]);
        
        // 4. TẠO HÓA ĐƠN TIỀN CỌC (THANH TOÁN NGAY)
        $invoiceNumber = 'INV' . date('Ymd') . rand(100, 999);
        $month = date('m');
        $year = date('Y');
        $dueDate = date('Y-m-d', strtotime('+7 days')); // Hạn 7 ngày
        
        $invoiceId = $this->db->insert('invoices', [
            'invoice_number' => $invoiceNumber,
            'contract_id' => $contractId,
            'student_id' => $registration->student_id,
            'room_id' => $roomId,
            'month' => $month,
            'year' => $year,
            'room_fee' => 0,
            'electricity_usage' => 0,
            'electricity_fee' => 0,
            'water_usage' => 0,
            'water_fee' => 0,
            'internet_fee' => 0,
            'other_fees' => $deposit,
            'total_amount' => $deposit,
            'due_date' => $dueDate,
            'status' => 'Chưa thanh toán',
            'invoice_type' => 'deposit' // Đánh dấu là hóa đơn tiền cọc
        ]);
        
        // 5. Cập nhật trạng thái đơn
        $this->db->update('registrations', [
            'status' => 'Đã xếp phòng'
        ], ['id' => $id]);
        
        $this->db->commit();
        
        flash('success', 'Xếp phòng thành công! Hợp đồng: ' . $contractNumber . '. Sinh viên cần thanh toán tiền cọc ' . number_format($deposit) . 'đ trước khi vào ở.');
        
    } catch (\Exception $e) {
        $this->db->rollback();
        flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
    
    return redirect('/registrations');
}
}