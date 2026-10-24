<?php
namespace App\Controllers;

use App\Core\Controller;

class SettingController extends Controller {
    
    public function index() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập');
            return redirect('/dashboard');
        }
        
        // Lấy tất cả settings
        $settings = [];
        $results = $this->db->fetchAll("SELECT * FROM settings");
        foreach ($results as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
        
        $data = [
            'title' => 'Cài đặt - DormFlow',
            'pageTitle' => 'Cài đặt hệ thống',
            'settings' => $settings
        ];
        
        $this->view('settings/index', $data);
    }
    
    public function update() {
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền thực hiện');
            return redirect('/dashboard');
        }
        
        $settings = [
            'school_name' => $_POST['school_name'] ?? 'Đại học Bách Khoa',
            'dormitory_name' => $_POST['dormitory_name'] ?? 'DormFlow',
            'contact_email' => $_POST['contact_email'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'electricity_rate' => $_POST['electricity_rate'] ?? 3500,
            'water_rate' => $_POST['water_rate'] ?? 15000,
            'internet_fee' => $_POST['internet_fee'] ?? 100000,
            'default_deposit' => $_POST['default_deposit'] ?? 500000,
            'check_in_time' => $_POST['check_in_time'] ?? '14:00',
            'check_out_time' => $_POST['check_out_time'] ?? '12:00',
            'visitor_hours' => $_POST['visitor_hours'] ?? '06:00 - 22:00'
        ];
        
        foreach ($settings as $key => $value) {
            // Kiểm tra xem setting đã tồn tại chưa
            $exists = $this->db->fetch("SELECT id FROM settings WHERE setting_key = :key", [':key' => $key]);
            
            if ($exists) {
                $this->db->update('settings', ['setting_value' => $value], ['setting_key' => $key]);
            } else {
                $this->db->insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
            }
        }
        
        flash('success', 'Cài đặt đã được lưu thành công!');
        return redirect('/settings');
    }
}