<?php
namespace App\Controllers;

use App\Core\Controller;

class RoomController extends Controller {
    
    public function index() {
        $buildingId = $_GET['building'] ?? null;
        
        $sql = "SELECT r.*, b.name as building_name, b.code as building_code
                FROM rooms r
                LEFT JOIN buildings b ON r.building_id = b.id";
        
        $params = [];
        if ($buildingId) {
            $sql .= " WHERE r.building_id = :building_id";
            $params[':building_id'] = $buildingId;
        }
        
        $sql .= " ORDER BY b.code, r.floor, r.room_number";
        
        $rooms = $this->db->fetchAll($sql, $params);
        $stats = $this->getRoomStats($buildingId);
        
        $data = [
            'title' => 'Quản lý phòng - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Quản lý phòng',
            'rooms' => $rooms,
            'stats' => $stats
        ];
        
        $this->view('rooms/index', $data);
    }
    
    private function getRoomStats($buildingId = null) {
        $sql = "SELECT 
                    COUNT(*) as total_rooms,
                    SUM(CASE WHEN status IN ('Trống', 'Còn chỗ') THEN 1 ELSE 0 END) as available_rooms,
                    SUM(CASE WHEN status = 'Đã đầy' THEN 1 ELSE 0 END) as full_rooms,
                    SUM(capacity) as total_beds,
                    SUM(current_occupancy) as occupied_beds
                FROM rooms";
        
        $params = [];
        if ($buildingId) {
            $sql .= " WHERE building_id = :building_id";
            $params[':building_id'] = $buildingId;
        }
        
        return $this->db->fetch($sql, $params);
    }
    
    public function floorPlan() {
        $sql = "SELECT r.*, b.name as building_name 
                FROM rooms r
                LEFT JOIN buildings b ON r.building_id = b.id
                ORDER BY b.code, r.floor, r.room_number";
        $rooms = $this->db->fetchAll($sql);
        
        $floors = [];
        foreach ($rooms as $room) {
            $floors[$room->floor][] = $room;
        }
        ksort($floors);
        
        $data = [
            'title' => 'Sơ đồ phòng - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Sơ đồ phòng',
            'floors' => $floors
        ];
        
        $this->view('rooms/floor-plan', $data);
    }
    
    public function create() {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền truy cập');
        return redirect('/dashboard');
    }
    
    // Lấy danh sách tòa nhà
    $buildings = $this->db->fetchAll("SELECT * FROM buildings ORDER BY code");
    
    $data = [
        'title' => 'Thêm phòng mới - ' . setting('dormitory_name', 'DormFlow'),
        'pageTitle' => 'Thêm phòng mới',
        'buildings' => $buildings
    ];
    
    $this->view('rooms/create', $data);
}
    
    public function store() {
    if (!is_admin()) {
        flash('error', 'Bạn không có quyền thực hiện');
        return redirect('/dashboard');
    }
    
    $buildingId = $_POST['building_id'] ?? 0;
    $roomNumber = $_POST['room_number'] ?? '';
    $floor = $_POST['floor'] ?? 1;
    $roomType = $_POST['room_type'] ?? '4';
    $capacity = $_POST['capacity'] ?? 4;
    $price = $_POST['price'] ?? 400000;
    
    // Validate
    if (empty($buildingId) || empty($roomNumber) || empty($price)) {
        flash('error', 'Vui lòng điền đầy đủ thông tin');
        return redirect('/rooms/create');
    }
    
    // Kiểm tra phòng đã tồn tại chưa
    $existing = $this->db->fetch("
        SELECT id FROM rooms 
        WHERE building_id = :building_id AND room_number = :room_number
    ", [':building_id' => $buildingId, ':room_number' => $roomNumber]);
    
    if ($existing) {
        flash('error', 'Phòng này đã tồn tại!');
        return redirect('/rooms/create');
    }
    
    // Thêm phòng
    $roomId = $this->db->insert('rooms', [
        'building_id' => $buildingId,
        'room_number' => $roomNumber,
        'floor' => $floor,
        'room_type' => $roomType,
        'capacity' => $capacity,
        'current_occupancy' => 0,
        'price' => $price,
        'status' => 'Trống'
    ]);
    
    if ($roomId) {
        // Tạo giường cho phòng
        for ($i = 1; $i <= $capacity; $i++) {
            $this->db->insert('room_beds', [
                'room_id' => $roomId,
                'bed_number' => (string)$i,
                'status' => 'Trống'
            ]);
        }
        
        flash('success', 'Thêm phòng thành công!');
    } else {
        flash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
    }
    
    return redirect('/rooms');
}
    
    public function show($id) {
        $room = $this->db->fetch("
            SELECT r.*, b.name as building_name, b.code as building_code
            FROM rooms r
            LEFT JOIN buildings b ON r.building_id = b.id
            WHERE r.id = :id
        ", [':id' => $id]);
        
        if (!$room) {
            flash('error', 'Không tìm thấy phòng!');
            return redirect('/rooms');
        }
        
        // Lấy danh sách sinh viên trong phòng
        $residents = $this->db->fetchAll("
            SELECT s.full_name, s.student_code, b.bed_number, b.check_in_date
            FROM room_beds b
            JOIN students s ON b.student_id = s.id
            WHERE b.room_id = :room_id AND b.status = 'Đã thuê'
            ORDER BY b.bed_number
        ", [':room_id' => $id]);
        
        $data = [
            'title' => 'Chi tiết phòng - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Chi tiết phòng ' . $room->room_number,
            'room' => $room,
            'residents' => $residents
        ];
        
        $this->view('rooms/show', $data);
    }
    
    public function edit($id) {
        $room = $this->db->fetch("SELECT * FROM rooms WHERE id = :id", [':id' => $id]);
        
        if (!$room) {
            flash('error', 'Không tìm thấy phòng!');
            return redirect('/rooms');
        }
        
        $buildings = $this->db->fetchAll("SELECT * FROM buildings ORDER BY code");
        
        $data = [
            'title' => 'Sửa thông tin phòng - ' . setting('dormitory_name', 'DormFlow'),
            'pageTitle' => 'Sửa thông tin phòng',
            'room' => $room,
            'buildings' => $buildings
        ];
        
        $this->view('rooms/edit', $data);
    }
    
    public function update($id) {
        $data = [
            'building_id' => $_POST['building_id'],
            'room_number' => $_POST['room_number'],
            'floor' => $_POST['floor'],
            'room_type' => $_POST['room_type'] ?? '4',
            'capacity' => $_POST['capacity'] ?? 4,
            'price' => $_POST['price'],
            'status' => $_POST['status'] ?? 'Trống'
        ];
        
        $this->db->update('rooms', $data, ['id' => $id]);
        
        flash('success', 'Cập nhật phòng thành công!');
        return redirect('/rooms');
    }
    
    public function delete($id) {
        // Kiểm tra phòng có người ở không
        $occupied = $this->db->fetch("
            SELECT COUNT(*) as count FROM room_beds 
            WHERE room_id = :room_id AND status = 'Đã thuê'
        ", [':room_id' => $id]);
        
        if ($occupied && $occupied->count > 0) {
            flash('error', 'Không thể xóa phòng đang có người ở!');
            return redirect('/rooms');
        }
        
        // Xóa giường trước
        $this->db->delete('room_beds', ['room_id' => $id]);
        
        // Xóa phòng
        $this->db->delete('rooms', ['id' => $id]);
        
        flash('success', 'Xóa phòng thành công!');
        return redirect('/rooms');
    }
}