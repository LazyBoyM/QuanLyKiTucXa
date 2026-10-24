<div class="card">
    <h3>Cài đặt hệ thống</h3>
    
    <form method="POST" action="<?php echo url('/settings'); ?>" id="settingsForm">
        <div class="form-group">
            <label class="form-label">Tên trường đại học</label>
            <input type="text" name="school_name" class="form-control" 
                   value="<?php echo $settings['school_name'] ?? 'Đại học Bách Khoa'; ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Tên KTX</label>
            <input type="text" name="dormitory_name" class="form-control" 
                   value="<?php echo $settings['dormitory_name'] ?? 'DormFlow'; ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Email liên hệ</label>
            <input type="email" name="contact_email" class="form-control" 
                   value="<?php echo $settings['contact_email'] ?? 'admin@ktx.edu.vn'; ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="contact_phone" class="form-control" 
                   value="<?php echo $settings['contact_phone'] ?? '0123456789'; ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Địa chỉ</label>
            <textarea name="address" class="form-control" rows="2"><?php echo $settings['address'] ?? '123 Đường ABC, Quận 1, TP.HCM'; ?></textarea>
        </div>
        
        <hr style="margin: 24px 0; border: none; border-top: 1px solid var(--apple-border);">
        
        <h4 style="margin-bottom: 16px;">Cài đặt giá dịch vụ</h4>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Giá điện (đ/kWh)</label>
                    <input type="number" name="electricity_rate" class="form-control" 
                           value="<?php echo $settings['electricity_rate'] ?? 3500; ?>">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Giá nước (đ/m³)</label>
                    <input type="number" name="water_rate" class="form-control" 
                           value="<?php echo $settings['water_rate'] ?? 15000; ?>">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Phí internet (đ/tháng)</label>
                    <input type="number" name="internet_fee" class="form-control" 
                           value="<?php echo $settings['internet_fee'] ?? 100000; ?>">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tiền đặt cọc mặc định (đ)</label>
                    <input type="number" name="default_deposit" class="form-control" 
                           value="<?php echo $settings['default_deposit'] ?? 500000; ?>">
                </div>
            </div>
        </div>
        
        <hr style="margin: 24px 0; border: none; border-top: 1px solid var(--apple-border);">
        
        <h4 style="margin-bottom: 16px;">Quy định KTX</h4>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Giờ check-in</label>
                    <input type="time" name="check_in_time" class="form-control" 
                           value="<?php echo $settings['check_in_time'] ?? '14:00'; ?>">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Giờ check-out</label>
                    <input type="time" name="check_out_time" class="form-control" 
                           value="<?php echo $settings['check_out_time'] ?? '12:00'; ?>">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Giờ thăm khách</label>
            <input type="text" name="visitor_hours" class="form-control" 
                   value="<?php echo $settings['visitor_hours'] ?? '06:00 - 22:00'; ?>" placeholder="VD: 06:00 - 22:00">
        </div>
        
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
            <button type="reset" class="btn">Đặt lại</button>
        </div>
    </form>
</div>

<div class="card mt-4">
    <h4>Thông tin phiên bản</h4>
    <table class="table">
        <tr>
            <th width="200">Phiên bản hệ thống:</th>
            <td>DormFlow v1.0.0</td>
        </tr>
        <tr>
            <th>Ngày cập nhật cuối:</th>
            <td><?php echo date('d/m/Y H:i'); ?></td>
        </tr>
        <tr>
            <th>PHP Version:</th>
            <td><?php echo phpversion(); ?></td>
        </tr>
    </table>
</div>