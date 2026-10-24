<?php
namespace App\Controllers;

use App\Core\Controller;

class NoticeController extends Controller {
    
    public function index() {
        // Lấy tất cả thông báo đã publish và chưa hết hạn
    // Treat '0000-00-00 00:00:00' as no-expiry as some inserts may have
    // stored an empty datetime as the MySQL zero-date. Also keep the
    // published_at <= NOW() filter so future-dated notices don't show.
    $sql = "SELECT * FROM notices 
        WHERE published_at <= NOW() 
        AND (expires_at IS NULL OR expires_at = '0000-00-00 00:00:00' OR expires_at >= NOW())
        ORDER BY 
                    CASE type 
                        WHEN 'Khẩn cấp' THEN 1
                        WHEN 'Quan trọng' THEN 2
                        WHEN 'Sự kiện' THEN 3
                        ELSE 4
                    END,
                    published_at DESC";
        $notices = $this->db->fetchAll($sql);
        
        $data = [
            'title' => 'Thông báo - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Thông báo',
            'notices' => $notices
        ];
        
        $this->view('notices/index', $data);
    }
    
    public function create() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/notices');
        }
        
        $data = [
            'title' => 'Tạo thông báo - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Tạo thông báo mới'
        ];
        
        $this->view('notices/create', $data);
    }
    
    public function store() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/notices');
        }
        
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $type = $_POST['type'] ?? 'Chung';

        // Convert HTML5 datetime-local (e.g. 2026-04-19T10:00) to MySQL datetime (Y-m-d H:i:s)
        $publishedAtInput = $_POST['published_at'] ?? '';
        if (!empty($publishedAtInput)) {
            $dt = \DateTime::createFromFormat('Y-m-d\TH:i', $publishedAtInput);
            if ($dt) {
                $publishedAt = $dt->format('Y-m-d H:i:s');
            } else {
                // fallback: try replace T and let MySQL handle it
                $publishedAt = str_replace('T', ' ', $publishedAtInput);
            }
        } else {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $expiresAtInput = $_POST['expires_at'] ?? '';
        if (!empty($expiresAtInput)) {
            $dt2 = \DateTime::createFromFormat('Y-m-d\TH:i', $expiresAtInput);
            if ($dt2) {
                $expiresAt = $dt2->format('Y-m-d H:i:s');
            } else {
                $expiresAt = str_replace('T', ' ', $expiresAtInput);
            }
        } else {
            $expiresAt = null;
        }
        
        if (empty($title) || empty($content)) {
            flash('error', 'Vui lòng nhập đầy đủ tiêu đề và nội dung');
            return redirect('/notices/create');
        }
        
        $data = [
            'title' => $title,
            'content' => $content,
            'type' => $type,
            'created_by' => auth()['id'],
            'published_at' => $publishedAt,
            'expires_at' => $expiresAt
        ];
        
        $id = $this->db->insert('notices', $data);
        
        if ($id) {
            flash('success', 'Đã đăng thông báo!');
        } else {
            flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
        
        return redirect('/notices');
    }
    
    public function delete($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/notices');
        }
        
        $this->db->delete('notices', ['id' => $id]);
        
        flash('success', 'Đã xóa thông báo!');
        return redirect('/notices');
    }
}