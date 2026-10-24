<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\MomoService;

class PaymentController extends Controller {
    
    public function index() {
        if (is_admin()) {
            $sql = "SELECT p.*, i.invoice_number, s.full_name as student_name, s.student_code
                    FROM payments p
                    JOIN invoices i ON p.invoice_id = i.id
                    JOIN students s ON p.student_id = s.id
                    ORDER BY p.created_at DESC";
            $payments = $this->db->fetchAll($sql);
            
            $stats = [
                'total_revenue' => $this->db->fetch("SELECT SUM(amount) as total FROM payments WHERE status = 'success'")->total ?? 0,
                'monthly_revenue' => $this->db->fetch("SELECT SUM(amount) as total FROM payments WHERE status = 'success' AND MONTH(payment_date) = MONTH(NOW())")->total ?? 0,
                'total_transactions' => $this->db->fetch("SELECT COUNT(*) as count FROM payments")->count ?? 0,
                'momo_transactions' => $this->db->fetch("SELECT COUNT(*) as count FROM payments WHERE payment_method = 'MoMo'")->count ?? 0
            ];
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if ($student) {
                $sql = "SELECT p.*, i.invoice_number
                        FROM payments p
                        JOIN invoices i ON p.invoice_id = i.id
                        WHERE p.student_id = :student_id
                        ORDER BY p.created_at DESC";
                $payments = $this->db->fetchAll($sql, [':student_id' => $student->id]);
            } else {
                $payments = [];
            }
            $stats = null;
        }
        
        $data = [
            'title' => 'Thanh toán - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Lịch sử thanh toán',
            'payments' => $payments,
            'stats' => $stats
        ];
        
        $this->view('payments/index', $data);
    }
    
    public function create() {
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
            return redirect('/profile');
        }
        
        $invoiceId = $_GET['invoice'] ?? 0;
        $invoice = null;
        
        if ($invoiceId) {
            $invoice = $this->db->fetch("
                SELECT i.*, s.full_name, s.student_code
                FROM invoices i
                JOIN students s ON i.student_id = s.id
                WHERE i.id = :id AND i.student_id = :student_id
            ", [':id' => $invoiceId, ':student_id' => $student->id]);
        }
        
        $unpaidInvoices = $this->db->fetchAll("
            SELECT * FROM invoices 
            WHERE student_id = :student_id AND status IN ('Chưa thanh toán', 'Quá hạn')
            ORDER BY due_date ASC
        ", [':student_id' => $student->id]);
        
        $data = [
            'title' => 'Thanh toán - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Thanh toán hóa đơn',
            'invoice' => $invoice,
            'unpaidInvoices' => $unpaidInvoices
        ];
        
        $this->view('payments/create', $data);
    }
    
    public function store() {
        $invoiceId = $_POST['invoice_id'];
        $paymentMethod = $_POST['payment_method'];
        
        $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
            [':user_id' => auth()['id']]);
        
        if (!$student) {
            flash('error', 'Vui lòng cập nhật hồ sơ sinh viên');
            return redirect('/profile');
        }
        
        $invoice = $this->db->fetch("
            SELECT * FROM invoices 
            WHERE id = :id AND student_id = :student_id
        ", [':id' => $invoiceId, ':student_id' => $student->id]);
        
        if (!$invoice) {
            flash('error', 'Hóa đơn không tồn tại hoặc không thuộc về bạn');
            return redirect('/payments');
        }
        
        $paymentNumber = 'PAY' . date('Ymd') . rand(100, 999);
        $amount = (int)$invoice->total_amount;
        
        $paymentId = $this->db->insert('payments', [
            'payment_number' => $paymentNumber,
            'invoice_id' => $invoiceId,
            'student_id' => $student->id,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'pending'
        ]);
        
        if ($paymentMethod == 'MoMo') {
            $momoService = new \App\Services\MomoService();
            $orderInfo = 'Thanh toan KTX - ' . $paymentNumber;
            $orderId = $paymentNumber . '_' . time();
            
            $response = $momoService->createPayment($orderInfo, $amount, $orderId, $paymentId);
            
            if (isset($response['payUrl']) && !empty($response['payUrl'])) {
                $_SESSION['momo_payment_' . $paymentId] = [
                    'payUrl' => $response['payUrl'],
                    'qrCodeUrl' => $response['qrCodeUrl'] ?? null,
                    'orderId' => $orderId
                ];
                
                return redirect('/payments/' . $paymentId . '/momo');
            } else {
                $errorMsg = $response['message'] ?? 'Không thể tạo thanh toán MoMo';
                flash('error', $errorMsg);
                return redirect('/payments');
            }
        }
        
        // Tiền mặt - thành công luôn
        $this->markPaymentSuccess($paymentId, $invoiceId);
        
        flash('success', 'Thanh toán thành công!');
        return redirect('/payments');
    }
    
    public function momoPayment($id) {
        $payment = $this->db->fetch("
            SELECT p.*, i.invoice_number, i.total_amount, s.full_name
            FROM payments p
            JOIN invoices i ON p.invoice_id = i.id
            JOIN students s ON p.student_id = s.id
            WHERE p.id = :id
        ", [':id' => $id]);
        
        if (!$payment) {
            flash('error', 'Không tìm thấy thanh toán');
            return redirect('/payments');
        }
        
        $momoData = $_SESSION['momo_payment_' . $id] ?? null;
        
        if (!$momoData) {
            flash('error', 'Phiên thanh toán đã hết hạn');
            return redirect('/payments');
        }
        
        $data = [
            'title' => 'Thanh toán MoMo - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Thanh toán MoMo',
            'payment' => $payment,
            'momoData' => $momoData
        ];
        
        $this->view('payments/momo', $data);
    }
    
    public function momoCallback() {
        $data = $_GET;
        
        error_log("MoMo Callback: " . json_encode($data));
        
        $paymentId = $data['extraData'] ?? 0;
        $resultCode = $data['resultCode'] ?? '';
        $transId = $data['transId'] ?? '';
        
        if ($resultCode == '0' && $paymentId > 0) {
            $payment = $this->db->fetch("SELECT * FROM payments WHERE id = :id", [':id' => $paymentId]);
            
            if ($payment && $payment->status == 'pending') {
                $this->markPaymentSuccess($paymentId, $payment->invoice_id, $transId);
                flash('success', 'Thanh toán MoMo thành công! Cảm ơn bạn.');
            } else {
                flash('info', 'Thanh toán đã được xử lý trước đó.');
            }
            
        } elseif ($resultCode == '1006') {
            flash('warning', 'Bạn đã hủy thanh toán MoMo.');
        } else {
            $message = $data['message'] ?? 'Thanh toán thất bại';
            flash('error', 'Thanh toán thất bại: ' . $message);
        }
        
        return redirect('/payments');
    }
    
    public function momoIpn() {
        http_response_code(200);
        echo json_encode(['message' => 'OK']);
        exit;
    }
    
    public function show($id) {
        if (is_admin()) {
            $payment = $this->db->fetch("SELECT * FROM payments WHERE id = :id", [':id' => $id]);
        } else {
            $student = $this->db->fetch("SELECT id FROM students WHERE user_id = :user_id", 
                [':user_id' => auth()['id']]);
            
            if (!$student) {
                flash('error', 'Không tìm thấy thông tin');
                return redirect('/payments');
            }
            
            $payment = $this->db->fetch("
                SELECT * FROM payments 
                WHERE id = :id AND student_id = :student_id
            ", [':id' => $id, ':student_id' => $student->id]);
        }
        
        if (!$payment) {
            flash('error', 'Không tìm thấy thanh toán');
            return redirect('/payments');
        }
        
        $invoice = $this->db->fetch("SELECT * FROM invoices WHERE id = :id", [':id' => $payment->invoice_id]);
        $student = $this->db->fetch("SELECT * FROM students WHERE id = :id", [':id' => $payment->student_id]);
        
        $data = [
            'title' => 'Chi tiết thanh toán - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Chi tiết thanh toán',
            'payment' => $payment,
            'invoice' => $invoice,
            'student' => $student
        ];
        
        $this->view('payments/show', $data);
    }
    
    // ==================== PRIVATE METHODS ====================
    
    /**
     * Đánh dấu thanh toán thành công và cập nhật trạng thái liên quan
     */
    private function markPaymentSuccess($paymentId, $invoiceId, $transactionId = null) {
        $data = [
            'status' => 'success',
            'payment_date' => date('Y-m-d H:i:s')
        ];
        
        if ($transactionId) {
            $data['transaction_id'] = $transactionId;
        }
        
        $this->db->update('payments', $data, ['id' => $paymentId]);
        $this->db->update('invoices', [
            'status' => 'Đã thanh toán',
            'paid_at' => date('Y-m-d H:i:s')
        ], ['id' => $invoiceId]);
        
        // Cập nhật trạng thái vi phạm nếu là hóa đơn tiền phạt
        $this->updateViolationStatus($invoiceId);
    }
    
    /**
     * Cập nhật trạng thái vi phạm khi thanh toán tiền phạt thành công
     */
    private function updateViolationStatus($invoiceId) {
        // Kiểm tra invoice có phải là tiền phạt không (other_fees > 0 và room_fee = 0)
        $invoice = $this->db->fetch("
            SELECT * FROM invoices WHERE id = :id
        ", [':id' => $invoiceId]);
        
        if ($invoice && $invoice->other_fees > 0 && $invoice->room_fee == 0) {
            // Tìm vi phạm liên quan đến hóa đơn này
            $violation = $this->db->fetch("
                SELECT * FROM violations WHERE invoice_id = :invoice_id
            ", [':invoice_id' => $invoiceId]);
            
            if ($violation && $violation->status == 'Chờ xử lý') {
                $this->db->update('violations', [
                    'status' => 'Đã xử lý',
                    'processed_by' => auth()['id'] ?? 1,
                    'processed_at' => date('Y-m-d H:i:s')
                ], ['id' => $violation->id]);
                
                error_log("Violation #{$violation->id} auto-processed after payment");
            }
        }
    }
}