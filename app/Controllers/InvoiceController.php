<?php
namespace App\Controllers;

use App\Core\Controller;

class InvoiceController extends Controller {
    
    public function index() {
        if (is_admin()) {
            $sql = "SELECT i.*, s.full_name, s.student_code, r.room_number
                    FROM invoices i
                    LEFT JOIN students s ON i.student_id = s.id
                    LEFT JOIN rooms r ON i.room_id = r.id
                    ORDER BY i.year DESC, i.month DESC, i.created_at DESC";
            $invoices = $this->db->fetchAll($sql);
            
            $stats = [
                'total_invoices' => $this->db->fetch("SELECT COUNT(*) as count FROM invoices")->count ?? 0,
                'paid_invoices' => $this->db->fetch("SELECT COUNT(*) as count FROM invoices WHERE status = 'Đã thanh toán'")->count ?? 0,
                'pending_invoices' => $this->db->fetch("SELECT COUNT(*) as count FROM invoices WHERE status IN ('Chưa thanh toán', 'Quá hạn')")->count ?? 0,
                'total_amount' => $this->db->fetch("SELECT SUM(total_amount) as total FROM invoices WHERE status IN ('Chưa thanh toán', 'Quá hạn')")->total ?? 0
            ];
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if ($student) {
                $sql = "SELECT i.*, r.room_number
                        FROM invoices i
                        LEFT JOIN rooms r ON i.room_id = r.id
                        WHERE i.student_id = :student_id
                        ORDER BY i.year DESC, i.month DESC";
                $invoices = $this->db->fetchAll($sql, [':student_id' => $student->id]);
            } else {
                $invoices = [];
            }
            $stats = null;
        }
        
        $data = [
            'title' => 'Hóa đơn - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Quản lý hóa đơn',
            'invoices' => $invoices,
            'stats' => $stats
        ];
        
        $this->view('invoices/index', $data);
    }
    
    public function show($id) {
        if (is_admin()) {
            $invoice = $this->db->fetch("SELECT * FROM invoices WHERE id = :id", [':id' => $id]);
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if (!$student) {
                flash('error', 'Không tìm thấy thông tin');
                return redirect('/invoices');
            }
            
            $invoice = $this->db->fetch("
                SELECT * FROM invoices 
                WHERE id = :id AND student_id = :student_id
            ", [':id' => $id, ':student_id' => $student->id]);
        }
        
        if (!$invoice) {
            flash('error', 'Không tìm thấy hóa đơn hoặc không có quyền xem');
            return redirect('/invoices');
        }
        
        // Lấy thông tin sinh viên
        $student = $this->db->fetch("
            SELECT * FROM students WHERE id = :id
        ", [':id' => $invoice->student_id]);
        
        if (!$student) {
            $student = (object)[
                'full_name' => 'N/A',
                'student_code' => 'N/A',
                'email' => 'N/A',
                'phone' => 'N/A'
            ];
        }
        
        // Lấy thông tin phòng - SỬA LỖI Ở ĐÂY
        $room = $this->db->fetch("
            SELECT r.*, b.name as building_name 
            FROM rooms r
            LEFT JOIN buildings b ON r.building_id = b.id
            WHERE r.id = :id
        ", [':id' => $invoice->room_id]);
        
        // Nếu không tìm thấy phòng, tạo object rỗng
        if (!$room) {
            $room = (object)[
                'room_number' => 'N/A',
                'building_name' => 'N/A',
                'floor' => 'N/A'
            ];
        }
        
        $data = [
            'title' => 'Chi tiết hóa đơn - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Chi tiết hóa đơn #' . $invoice->invoice_number,
            'invoice' => $invoice,
            'student' => $student,
            'room' => $room,
            'electricity_rate' => setting('electricity_rate', 3500),
            'water_rate' => setting('water_rate', 15000),
            'internet_fee' => setting('internet_fee', 100000)
        ];
        
        $this->view('invoices/show', $data);
    }
    
    public function create() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/dashboard');
        }
        
        $contracts = $this->db->fetchAll("
            SELECT c.*, s.full_name, s.student_code, r.room_number, r.price as room_price
            FROM contracts c
            JOIN students s ON c.student_id = s.id
            JOIN rooms r ON c.room_id = r.id
            WHERE c.status = 'Hiệu lực'
            ORDER BY r.room_number
        ");
        
        $data = [
            'title' => 'Tạo hóa đơn - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Tạo hóa đơn mới',
            'contracts' => $contracts,
            'electricity_rate' => setting('electricity_rate', 3500),
            'water_rate' => setting('water_rate', 15000),
            'internet_fee' => setting('internet_fee', 100000)
        ];
        
        $this->view('invoices/create', $data);
    }
    
    public function store() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/dashboard');
        }
        
        $contractId = $_POST['contract_id'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $electricityUsage = $_POST['electricity_usage'] ?? 0;
        $waterUsage = $_POST['water_usage'] ?? 0;
        
        $contract = $this->db->fetch("
            SELECT c.*, r.price as room_price
            FROM contracts c
            JOIN rooms r ON c.room_id = r.id
            WHERE c.id = :id
        ", [':id' => $contractId]);
        
        if (!$contract) {
            flash('error', 'Hợp đồng không tồn tại');
            return redirect('/invoices/create');
        }
        
        $electricityRate = setting('electricity_rate', 3500);
        $waterRate = setting('water_rate', 15000);
        $internetFee = setting('internet_fee', 100000);
        
        $roomFee = $contract->room_price;
        $electricityFee = $electricityUsage * $electricityRate;
        $waterFee = $waterUsage * $waterRate;
        $totalAmount = $roomFee + $electricityFee + $waterFee + $internetFee;
        
        $invoiceNumber = 'INV' . date('Ymd') . rand(100, 999);
        $dueDate = date('Y-m-d', strtotime('+10 days'));
        
        $data = [
            'invoice_number' => $invoiceNumber,
            'contract_id' => $contractId,
            'student_id' => $contract->student_id,
            'room_id' => $contract->room_id,
            'month' => $month,
            'year' => $year,
            'room_fee' => $roomFee,
            'electricity_usage' => $electricityUsage,
            'electricity_fee' => $electricityFee,
            'water_usage' => $waterUsage,
            'water_fee' => $waterFee,
            'internet_fee' => $internetFee,
            'total_amount' => $totalAmount,
            'due_date' => $dueDate,
            'status' => 'Chưa thanh toán'
        ];
        
        $id = $this->db->insert('invoices', $data);
        
        if ($id) {
            flash('success', 'Tạo hóa đơn thành công! Mã hóa đơn: ' . $invoiceNumber);
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
        
        return redirect('/invoices');
    }
    
    public function generateMonthly() {
        if (!is_admin()) {
            flash('error', 'Không có quyền');
            return redirect('/dashboard');
        }
        
        $month = $_POST['month'] ?? date('m');
        $year = $_POST['year'] ?? date('Y');
        
        $contracts = $this->db->fetchAll("
            SELECT c.*, r.price as room_price
            FROM contracts c
            JOIN rooms r ON c.room_id = r.id
            WHERE c.status = 'Hiệu lực'
        ");
        
        $created = 0;
        $electricityRate = setting('electricity_rate', 3500);
        $waterRate = setting('water_rate', 15000);
        $internetFee = setting('internet_fee', 100000);
        
        foreach ($contracts as $contract) {
            $existing = $this->db->fetch("
                SELECT id FROM invoices 
                WHERE contract_id = :contract_id AND month = :month AND year = :year
            ", [':contract_id' => $contract->id, ':month' => $month, ':year' => $year]);
            
            if ($existing) continue;
            
            $electricityUsage = rand(30, 80);
            $waterUsage = rand(2, 6);
            
            $roomFee = $contract->room_price;
            $electricityFee = $electricityUsage * $electricityRate;
            $waterFee = $waterUsage * $waterRate;
            $totalAmount = $roomFee + $electricityFee + $waterFee + $internetFee;
            
            $invoiceNumber = 'INV' . $year . $month . str_pad($contract->id, 4, '0', STR_PAD_LEFT);
            $dueDate = date('Y-m-d', strtotime("$year-$month-10 +1 month"));
            
            $this->db->insert('invoices', [
                'invoice_number' => $invoiceNumber,
                'contract_id' => $contract->id,
                'student_id' => $contract->student_id,
                'room_id' => $contract->room_id,
                'month' => $month,
                'year' => $year,
                'room_fee' => $roomFee,
                'electricity_usage' => $electricityUsage,
                'electricity_fee' => $electricityFee,
                'water_usage' => $waterUsage,
                'water_fee' => $waterFee,
                'internet_fee' => $internetFee,
                'total_amount' => $totalAmount,
                'due_date' => $dueDate,
                'status' => 'Chưa thanh toán'
            ]);
            
            $created++;
        }
        
        flash('success', "Đã tạo $created hóa đơn tháng $month/$year");
        return redirect('/invoices');
    }
}