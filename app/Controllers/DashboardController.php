<?php
namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller {

    /**
     * Trang chủ - tự động chuyển hướng theo trạng thái đăng nhập
     */
    public function index() {
        // Chưa đăng nhập → chuyển về login
        if (!is_logged_in()) {
            return redirect('/login');
        }
        
        // Đã đăng nhập → lấy data và hiển thị view dashboard.php
        if (is_admin()) {
            $data = $this->getAdminData();
        } else {
            $data = $this->getStudentData();
        }

        $this->view('dashboard', $data);
    }

    // ==================== ADMIN DATA ====================

    private function getAdminData() {
        return [
            'title'                => setting('dormitory_name', 'DormFlow') . ' - Dashboard',
            'pageTitle'            => 'Tổng quan ' . setting('dormitory_name', 'Ký túc xá'),
            'stats'                => [
                'total_students'        => $this->db->fetch("SELECT COUNT(*) as c FROM students")->c ?? 0,
                'active_contracts'      => $this->db->fetch("SELECT COUNT(*) as c FROM contracts WHERE status = 'Hiệu lực'")->c ?? 0,
                'total_rooms'           => $this->db->fetch("SELECT COUNT(*) as c FROM rooms")->c ?? 0,
                'available_rooms'       => $this->db->fetch("SELECT COUNT(*) as c FROM rooms WHERE status IN ('Trống','Còn chỗ')")->c ?? 0,
                'occupancy_rate'        => round($this->db->fetch("SELECT (SUM(current_occupancy)*100.0/NULLIF(SUM(capacity),0)) as rate FROM rooms")->rate ?? 0),
                'pending_registrations' => $this->db->fetch("SELECT COUNT(*) as c FROM registrations WHERE status = 'Chờ duyệt'")->c ?? 0,
                'pending_maintenance'   => $this->db->fetch("SELECT COUNT(*) as c FROM maintenance_requests WHERE status IN ('Chờ xử lý','Đang xử lý')")->c ?? 0,
                'monthly_revenue'       => $this->db->fetch("SELECT COALESCE(SUM(amount),0) as total FROM payments WHERE status = 'success' AND MONTH(payment_date)=MONTH(NOW()) AND YEAR(payment_date)=YEAR(NOW())")->total ?? 0,
            ],
            'building_stats'       => $this->db->fetchAll("
                SELECT b.name, b.code,
                       COUNT(r.id) AS total_rooms,
                       COALESCE(SUM(r.capacity), 0) AS total_beds,
                       COALESCE(SUM(r.current_occupancy), 0) AS occupied_beds
                FROM buildings b
                LEFT JOIN rooms r ON b.id = r.building_id
                GROUP BY b.id, b.name, b.code
                ORDER BY b.code
            "),
            'recent_registrations' => $this->db->fetchAll("
                SELECT r.*, s.full_name, s.student_code, b.name AS building_name
                FROM registrations r
                JOIN students s ON r.student_id = s.id
                LEFT JOIN buildings b ON r.building_id = b.id
                ORDER BY r.created_at DESC
                LIMIT 5
            "),
            'recent_maintenance'   => $this->db->fetchAll("
                SELECT mr.*, s.full_name, rm.room_number
                FROM maintenance_requests mr
                JOIN students s ON mr.student_id = s.id
                JOIN rooms rm ON mr.room_id = rm.id
                WHERE mr.status IN ('Chờ xử lý','Đang xử lý')
                ORDER BY
                    CASE mr.priority
                        WHEN 'Khẩn cấp' THEN 1
                        WHEN 'Cao' THEN 2
                        WHEN 'Trung bình' THEN 3
                        ELSE 4
                    END,
                    mr.created_at DESC
                LIMIT 5
            "),
        ];
    }

    // ==================== STUDENT DATA ====================

    private function getStudentData() {
        $userId  = auth()['id'];
        $student = $this->db->fetch(
            "SELECT * FROM students WHERE user_id = :uid",
            [':uid' => $userId]
        );

        $base = [
            'title'     => 'Ký túc xá - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Trang cá nhân',
            'notices'   => $this->getRecentNotices(),
        ];

        // Chưa có hồ sơ sinh viên
        if (!$student) {
            return array_merge($base, [
                'has_profile'         => false,
                'student'             => null,
                'contract'            => null,
                'latest_registration' => null,
                'unpaid_invoices'     => [],
                'maintenance_requests'=> [],
            ]);
        }

        // Lấy hợp đồng hiệu lực
        $contract = $this->db->fetch("
            SELECT c.*, r.room_number, r.room_type,
                   b.name AS building_name, bed.bed_number
            FROM contracts c
            JOIN rooms r ON c.room_id = r.id
            JOIN buildings b ON r.building_id = b.id
            JOIN room_beds bed ON c.bed_id = bed.id
            WHERE c.student_id = :sid AND c.status = 'Hiệu lực'
            LIMIT 1
        ", [':sid' => $student->id]);

        // Có hồ sơ nhưng chưa có hợp đồng
        if (!$contract) {
            $latest_registration = $this->db->fetch("
                SELECT * FROM registrations
                WHERE student_id = :sid
                ORDER BY created_at DESC
                LIMIT 1
            ", [':sid' => $student->id]);

            return array_merge($base, [
                'has_profile'         => true,
                'student'             => $student,
                'contract'            => null,
                'latest_registration' => $latest_registration,
                'unpaid_invoices'     => [],
                'maintenance_requests'=> [],
            ]);
        }

        // Có hợp đồng
        return array_merge($base, [
            'has_profile'         => true,
            'student'             => $student,
            'contract'            => $contract,
            'latest_registration' => null,
            'unpaid_invoices'     => $this->db->fetchAll("
                SELECT * FROM invoices
                WHERE student_id = :sid
                  AND status IN ('Chưa thanh toán','Quá hạn')
                ORDER BY due_date ASC
            ", [':sid' => $student->id]),
            'maintenance_requests'=> $this->db->fetchAll("
                SELECT * FROM maintenance_requests
                WHERE student_id = :sid
                ORDER BY created_at DESC
                LIMIT 5
            ", [':sid' => $student->id]),
        ]);
    }

    private function getRecentNotices() {
        return $this->db->fetchAll("
            SELECT * FROM notices
            WHERE published_at <= NOW()
              AND (expires_at IS NULL OR expires_at >= NOW())
            ORDER BY
                CASE type
                    WHEN 'Khẩn cấp' THEN 1
                    WHEN 'Quan trọng' THEN 2
                    ELSE 3
                END,
                published_at DESC
            LIMIT 5
        ");
    }
}