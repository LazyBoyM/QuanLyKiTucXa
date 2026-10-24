<?php
namespace App\Controllers;

use App\Core\Controller;

class ReportController extends Controller {
    
    public function index() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/dashboard');
        }
        
        $data = [
            'title' => 'Báo cáo thống kê - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Báo cáo thống kê'
        ];
        
        $this->view('reports/index', $data);
    }
    
    public function financial() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/dashboard');
        }
        
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        // Tổng doanh thu theo tháng
        $monthlyRevenue = $this->db->fetch("
            SELECT SUM(amount) as total FROM payments 
            WHERE status = 'success' AND MONTH(payment_date) = :month AND YEAR(payment_date) = :year
        ", [':month' => $month, ':year' => $year])->total ?? 0;
        
        // Doanh thu theo phương thức thanh toán
        $revenueByMethod = $this->db->fetchAll("
            SELECT payment_method, SUM(amount) as total, COUNT(*) as count
            FROM payments 
            WHERE status = 'success' AND MONTH(payment_date) = :month AND YEAR(payment_date) = :year
            GROUP BY payment_method
        ", [':month' => $month, ':year' => $year]);
        
        // Doanh thu 6 tháng gần đây
        $last6Months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $m = date('m', strtotime($date));
            $y = date('Y', strtotime($date));
            
            $revenue = $this->db->fetch("
                SELECT SUM(amount) as total FROM payments 
                WHERE status = 'success' AND MONTH(payment_date) = :month AND YEAR(payment_date) = :year
            ", [':month' => $m, ':year' => $y])->total ?? 0;
            
            $last6Months[] = [
                'month' => $m . '/' . $y,
                'revenue' => $revenue
            ];
        }
        
        // Tổng tiền chưa thu
        $unpaidTotal = $this->db->fetch("
            SELECT SUM(total_amount) as total FROM invoices 
            WHERE status IN ('Chưa thanh toán', 'Quá hạn')
        ")->total ?? 0;
        
        // Số hóa đơn quá hạn
        $overdueCount = $this->db->fetch("
            SELECT COUNT(*) as count FROM invoices 
            WHERE status = 'Quá hạn' OR (status = 'Chưa thanh toán' AND due_date < NOW())
        ")->count ?? 0;
        
        $data = [
            'title' => 'Báo cáo tài chính - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Báo cáo tài chính',
            'month' => $month,
            'year' => $year,
            'monthlyRevenue' => $monthlyRevenue,
            'revenueByMethod' => $revenueByMethod,
            'last6Months' => $last6Months,
            'unpaidTotal' => $unpaidTotal,
            'overdueCount' => $overdueCount
        ];
        
        $this->view('reports/financial', $data);
    }
    
    public function occupancy() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/dashboard');
        }
        
        // Thống kê tổng quan
        $totalRooms = $this->db->fetch("SELECT COUNT(*) as count FROM rooms")->count ?? 0;
        $totalBeds = $this->db->fetch("SELECT SUM(capacity) as total FROM rooms")->total ?? 0;
        $occupiedBeds = $this->db->fetch("SELECT SUM(current_occupancy) as total FROM rooms")->total ?? 0;
        $availableBeds = $totalBeds - $occupiedBeds;
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
        
        // Thống kê theo tòa nhà
        $buildingStats = $this->db->fetchAll("
            SELECT b.name, b.code,
                   COUNT(r.id) as room_count,
                   SUM(r.capacity) as total_beds,
                   SUM(r.current_occupancy) as occupied_beds
            FROM buildings b
            LEFT JOIN rooms r ON b.id = r.building_id
            GROUP BY b.id
            ORDER BY b.code
        ");
        
        // Thống kê theo loại phòng
        $roomTypeStats = $this->db->fetchAll("
            SELECT room_type,
                   COUNT(*) as room_count,
                   SUM(capacity) as total_beds,
                   SUM(current_occupancy) as occupied_beds
            FROM rooms
            GROUP BY room_type
            ORDER BY room_type
        ");
        
        // Phòng còn trống
        $availableRooms = $this->db->fetchAll("
            SELECT r.*, b.name as building_name
            FROM rooms r
            JOIN buildings b ON r.building_id = b.id
            WHERE r.status IN ('Trống', 'Còn chỗ') AND r.current_occupancy < r.capacity
            ORDER BY b.code, r.floor, r.room_number
            LIMIT 10
        ");
        
        $data = [
            'title' => 'Báo cáo công suất - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Báo cáo công suất phòng',
            'totalRooms' => $totalRooms,
            'totalBeds' => $totalBeds,
            'occupiedBeds' => $occupiedBeds,
            'availableBeds' => $availableBeds,
            'occupancyRate' => $occupancyRate,
            'buildingStats' => $buildingStats,
            'roomTypeStats' => $roomTypeStats,
            'availableRooms' => $availableRooms
        ];
        
        $this->view('reports/occupancy', $data);
    }
    
    public function export() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/dashboard');
        }
        
        $type = $_GET['type'] ?? 'financial';
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        // Xuất CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="bao-cao-' . $type . '-' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
        
        if ($type == 'financial') {
            fputcsv($output, ['BÁO CÁO TÀI CHÍNH', $month . '/' . $year]);
            fputcsv($output, []);
            fputcsv($output, ['STT', 'Mã thanh toán', 'Sinh viên', 'Số tiền', 'Phương thức', 'Ngày thanh toán']);
            
            $payments = $this->db->fetchAll("
                SELECT p.*, s.full_name, s.student_code
                FROM payments p
                JOIN students s ON p.student_id = s.id
                WHERE p.status = 'success' 
                AND MONTH(p.payment_date) = :month 
                AND YEAR(p.payment_date) = :year
                ORDER BY p.payment_date DESC
            ", [':month' => $month, ':year' => $year]);
            
            $stt = 1;
            foreach ($payments as $p) {
                fputcsv($output, [
                    $stt++,
                    $p->payment_number,
                    $p->full_name . ' (' . $p->student_code . ')',
                    number_format($p->amount) . 'đ',
                    $p->payment_method,
                    date('d/m/Y H:i', strtotime($p->payment_date))
                ]);
            }
        } else {
            fputcsv($output, ['BÁO CÁO CÔNG SUẤT PHÒNG']);
            fputcsv($output, []);
            fputcsv($output, ['Tòa nhà', 'Tổng phòng', 'Tổng giường', 'Đã thuê', 'Còn trống', 'Tỷ lệ lấp đầy']);
            
            $stats = $this->db->fetchAll("
                SELECT b.name, b.code,
                       COUNT(r.id) as room_count,
                       SUM(r.capacity) as total_beds,
                       SUM(r.current_occupancy) as occupied_beds
                FROM buildings b
                LEFT JOIN rooms r ON b.id = r.building_id
                GROUP BY b.id
            ");
            
            foreach ($stats as $s) {
                $available = $s->total_beds - $s->occupied_beds;
                $rate = $s->total_beds > 0 ? round(($s->occupied_beds / $s->total_beds) * 100) : 0;
                fputcsv($output, [
                    $s->name . ' (' . $s->code . ')',
                    $s->room_count ?? 0,
                    $s->total_beds ?? 0,
                    $s->occupied_beds ?? 0,
                    $available,
                    $rate . '%'
                ]);
            }
        }
        
        fclose($output);
        exit;
    }
}