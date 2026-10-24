<div class="d-flex justify-between items-center mb-4">
    <h2>Chi tiết phòng <?php echo $room->room_number; ?></h2>
    <div>
        <?php if (is_admin()): ?>
        <a href="<?php echo url('/rooms/'.$room->id.'/edit'); ?>" class="btn">Sửa</a>
        <?php endif; ?>
        <a href="<?php echo url('/rooms'); ?>" class="btn">Quay lại</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div style="font-size: 64px; margin-bottom: 16px;">🚪</div>
            <h2>Phòng <?php echo $room->room_number; ?></h2>
            <p style="color: var(--apple-gray); margin-bottom: 12px;">
                Tầng <?php echo $room->floor; ?> - 
                <?php 
                // Lấy tên tòa nhà
                $building = $this->db->fetch("SELECT name FROM buildings WHERE id = :id", [':id' => $room->building_id]);
                echo $building ? $building->name : 'N/A';
                ?>
            </p>
            <span class="badge badge-<?php 
                echo $room->status == 'Trống' ? 'success' : 
                     ($room->status == 'Đã đầy' ? 'danger' : 
                     ($room->status == 'Bảo trì' ? 'warning' : 'info')); 
            ?>">
                <?php 
                if ($room->status == 'Trống') echo 'Còn trống';
                elseif ($room->status == 'Đã đầy') echo 'Đã đầy';
                elseif ($room->status == 'Còn chỗ') echo 'Còn chỗ';
                else echo 'Bảo trì';
                ?>
            </span>
        </div>
        
        <?php if (!empty($room->amenities)): ?>
        <div class="card mt-4">
            <h4>Tiện nghi</h4>
            <p style="color: var(--apple-gray);"><?php echo nl2br($room->amenities); ?></p>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <h4>Thông tin phòng</h4>
            <table class="table">
                <tr>
                    <th width="150">Loại phòng:</th>
                    <td><?php echo $room->room_type; ?> người/phòng</td>
                </tr>
                <tr>
                    <th>Sức chứa:</th>
                    <td><?php echo $room->capacity; ?> người</td>
                </tr>
                <tr>
                    <th>Hiện tại:</th>
                    <td><?php echo $room->current_occupancy; ?> người</td>
                </tr>
                <tr>
                    <th>Còn trống:</th>
                    <td><?php echo $room->capacity - $room->current_occupancy; ?> chỗ</td>
                </tr>
                <tr>
                    <th>Giá phòng:</th>
                    <td><strong><?php echo number_format($room->price); ?>đ/tháng</strong></td>
                </tr>
            </table>
        </div>
        
        <?php if (!empty($residents)): ?>
        <div class="card mt-4">
            <h4>Danh sách sinh viên đang ở</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>MSSV</th>
                        <th>Họ tên</th>
                        <th>Ngày vào</th>
                        <th>Giường</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($residents as $r): ?>
                    <tr>
                        <td><strong><?php echo $r->student_code ?: 'N/A'; ?></strong></td>
                        <td><?php echo $r->full_name; ?></td>
                        <td><?php echo $r->check_in_date ? date('d/m/Y', strtotime($r->check_in_date)) : 'N/A'; ?></td>
                        <td><?php echo $r->bed_number ?? 'N/A'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="card mt-4">
            <p class="text-muted text-center py-3">Chưa có sinh viên nào ở phòng này</p>
        </div>
        <?php endif; ?>
    </div>
</div>