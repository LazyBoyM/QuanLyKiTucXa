<div class="alert alert-info">
    <strong>Thông tin quan trọng:</strong>
    <ul style="margin-top: 8px; margin-left: 20px;">
        <li>Giờ nhận phòng: <?php echo setting('check_in_time', '14:00'); ?></li>
        <li>Giờ trả phòng: <?php echo setting('check_out_time', '12:00'); ?></li>
        <li>Tiền đặt cọc: <?php echo number_format(setting('default_deposit', 500000)); ?>đ (hoàn trả khi trả phòng)</li>
        <li>Giá điện: <?php echo number_format(setting('electricity_rate', 3500)); ?>đ/kWh</li>
        <li>Giá nước: <?php echo number_format(setting('water_rate', 15000)); ?>đ/m³</li>
        <li>Phí internet: <?php echo number_format(setting('internet_fee', 100000)); ?>đ/tháng</li>
    </ul>
</div>

<div class="card" style="max-width: 700px;">
    <h3>Đăng ký Ký túc xá</h3>
    <p class="text-muted mb-4">Vui lòng điền đầy đủ thông tin để đăng ký ở KTX</p>
    
    <form method="POST" action="<?php echo url('/registrations'); ?>">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Học kỳ</label>
                    <select name="semester" class="form-control" required>
                        <option value="1">Học kỳ 1</option>
                        <option value="2">Học kỳ 2</option>
                        <option value="Hè">Học kỳ hè</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Năm học</label>
                    <select name="academic_year" class="form-control" required>
                        <option value="2024-2025">2024-2025</option>
                        <option value="2025-2026">2025-2026</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Tòa nhà mong muốn</label>
            <select name="building_id" class="form-control">
                <option value="">Không yêu cầu cụ thể</option>
                <?php foreach ($buildings as $b): ?>
                <option value="<?php echo $b->id; ?>">
                    <?php echo $b->name; ?> (<?php echo $b->gender; ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Loại phòng mong muốn</label>
            <select name="desired_room_type" class="form-control" required>
                <option value="4">Phòng 4 người - 400.000đ/tháng</option>
                <option value="6">Phòng 6 người - 350.000đ/tháng</option>
                <option value="8">Phòng 8 người - 300.000đ/tháng</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Ngày dự kiến vào ở</label>
            <input type="date" name="expected_check_in" class="form-control" required 
                   value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Ghi chú (nếu có)</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Yêu cầu đặc biệt, tình trạng sức khỏe..."></textarea>
        </div>
        
        <div class="alert alert-info">
            <strong>Lưu ý:</strong> Sau khi đăng ký, đơn của bạn sẽ được Phòng Công tác sinh viên xét duyệt. 
            Kết quả sẽ được thông báo qua email và trong hệ thống.
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Gửi đơn đăng ký</button>
            <a href="<?php echo url('/registrations'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>