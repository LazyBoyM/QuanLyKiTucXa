<?php
namespace App\Controllers;

use App\Core\Controller;

class RuleController extends Controller {
    
    public function index() {
        $category = $_GET['category'] ?? '';
        
        $sql = "SELECT * FROM rules";
        $params = [];
        
        if ($category) {
            $sql .= " WHERE category = :category";
            $params[':category'] = $category;
        }
        
        $sql .= " ORDER BY category, id DESC";
        
        $rules = $this->db->fetchAll($sql, $params);
        
        // Lấy danh sách category để lọc
        $categories = $this->db->fetchAll("SELECT DISTINCT category FROM rules ORDER BY category");
        
        $data = [
            'title' => 'Nội quy KTX - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Nội quy ký túc xá',
            'rules' => $rules,
            'categories' => $categories,
            'current_category' => $category
        ];
        
        $this->view('rules/index', $data);
    }
    
    public function create() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/rules');
        }
        
        $data = [
            'title' => 'Thêm nội quy - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Thêm nội quy mới'
        ];
        
        $this->view('rules/create', $data);
    }
    
    public function store() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/rules');
        }
        
        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'category' => $_POST['category'] ?? 'Chung',
            'penalty' => $_POST['penalty'] ?? null,
            'created_by' => auth()['id']
        ];
        
        if (empty($data['title']) || empty($data['content'])) {
            flash('error', 'Vui lòng nhập đầy đủ tiêu đề và nội dung');
            return redirect('/rules/create');
        }
        
        $id = $this->db->insert('rules', $data);
        
        if ($id) {
            flash('success', 'Thêm nội quy thành công');
        } else {
            flash('error', 'Có lỗi xảy ra');
        }
        
        return redirect('/rules');
    }
    
    public function edit($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/rules');
        }
        
        $rule = $this->db->fetch("SELECT * FROM rules WHERE id = :id", [':id' => $id]);
        
        if (!$rule) {
            flash('error', 'Không tìm thấy nội quy');
            return redirect('/rules');
        }
        
        $data = [
            'title' => 'Sửa nội quy - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Sửa nội quy',
            'rule' => $rule
        ];
        
        $this->view('rules/edit', $data);
    }
    
    public function update($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/rules');
        }
        
        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'category' => $_POST['category'] ?? 'Chung',
            'penalty' => $_POST['penalty'] ?? null
        ];
        
        $this->db->update('rules', $data, ['id' => $id]);
        
        flash('success', 'Cập nhật nội quy thành công');
        return redirect('/rules');
    }
    
    public function delete($id) {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/rules');
        }
        
        $this->db->delete('rules', ['id' => $id]);
        
        flash('success', 'Xóa nội quy thành công');
        return redirect('/rules');
    }
}