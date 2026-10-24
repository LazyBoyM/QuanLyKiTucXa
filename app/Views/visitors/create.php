<div class="card" style="max-width: 600px;">
    <h3>Đăng ký khách đến thăm</h3>
    
    <form method="POST" action="<?php echo url('/visitors'); ?>">
        <div class="form-group">
            <label class="form-label">Họ tên khách</label>
            <input type="text" name="visitor_name" class="form-control" required>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Số CMND/CCCD</label>
                    <input type="text" name="id_card" class="form-control">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Mối quan hệ</label>
            <select name="relationship" class="form-control">
                <option value="Người thân">Người thân</option>
                <option value="Bạn bè">Bạn bè</option>
                <option value="Khác">Khác</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Mục đích đến thăm</label>
            <input type="text" name="purpose" class="form-control" placeholder="VD: Thăm, giao đồ...">
        </div>
        
        <div class="alert alert-info">
            <strong>Lưu ý:</strong> Khách phải xuất trình CMND/CCCD khi vào KTX.
            Thời gian thăm: 6:00 - 22:00 hàng ngày.
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Đăng ký</button>
            <a href="<?php echo url('/visitors'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>