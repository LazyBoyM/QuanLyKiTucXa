<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/notices'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Tạo thông báo mới</h2>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="<?php echo url('/notices'); ?>">
        <div class="form-group">
            <label class="form-label">Tiêu đề <span style="color: var(--apple-red);">*</span></label>
            <input type="text" name="title" class="form-control" required>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Loại thông báo</label>
                    <select name="type" class="form-control">
                        <option value="Chung">Chung</option>
                        <option value="Quan trọng">Quan trọng</option>
                        <option value="Khẩn cấp">Khẩn cấp</option>
                        <option value="Sự kiện">Sự kiện</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Ngày đăng</label>
                    <input type="datetime-local" name="published_at" class="form-control" 
                           value="<?php echo date('Y-m-d\TH:i'); ?>">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Ngày hết hạn (để trống nếu không có)</label>
            <input type="datetime-local" name="expires_at" class="form-control">
        </div>
        
        <div class="form-group">
            <label class="form-label">Nội dung <span style="color: var(--apple-red);">*</span></label>
            <textarea name="content" class="form-control" rows="6" required></textarea>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Đăng thông báo</button>
            <a href="<?php echo url('/notices'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>