<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/maintenance'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Tạo yêu cầu bảo trì</h2>
</div>

<div class="card" style="max-width: 700px;">
    <div class="alert alert-info mb-4">
        <strong>Phòng hiện tại:</strong> <?php echo $contract->room_number; ?>
    </div>
    
    <form method="POST" action="<?php echo url('/maintenance'); ?>">
        <div class="form-group">
            <label class="form-label">Tiêu đề <span style="color: var(--apple-red);">*</span></label>
            <input type="text" name="title" class="form-control" required 
                   placeholder="VD: Bóng đèn phòng tắm không sáng">
        </div>
        
        <div class="form-group">
            <label class="form-label">Loại sự cố <span style="color: var(--apple-red);">*</span></label>
            <select name="category" class="form-control" required>
                <option value="">Chọn loại</option>
                <option value="electrical">Điện</option>
                <option value="plumbing">Nước</option>
                <option value="furniture">Nội thất</option>
                <option value="other">Khác</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Mức độ ưu tiên</label>
            <select name="priority" class="form-control">
                <option value="Thấp">Thấp - Có thể chờ</option>
                <option value="Trung bình" selected>Trung bình</option>
                <option value="Cao">Cao - Ảnh hưởng sinh hoạt</option>
                <option value="Khẩn cấp">Khẩn cấp - Nguy hiểm</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Mô tả chi tiết <span style="color: var(--apple-red);">*</span></label>
            <textarea name="description" class="form-control" rows="5" required 
                      placeholder="Mô tả càng chi tiết càng tốt..."></textarea>
        </div>
        
        <div class="alert alert-warning">
            <strong>Lưu ý:</strong> Với sự cố khẩn cấp ngoài giờ hành chính, vui lòng gọi trực tiếp 
            <?php echo setting('contact_phone', '0123456789'); ?>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
            <a href="<?php echo url('/maintenance'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>