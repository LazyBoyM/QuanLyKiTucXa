<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/rules'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Thêm nội quy mới</h2>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="<?php echo url('/rules'); ?>">
        <div class="form-group">
            <label class="form-label">Tiêu đề <span style="color: var(--apple-red);">*</span></label>
            <input type="text" name="title" class="form-control" required placeholder="VD: Giữ gìn vệ sinh chung">
        </div>
        
        <div class="form-group">
            <label class="form-label">Danh mục</label>
            <select name="category" class="form-control">
                <option value="Chung">Chung</option>
                <option value="An ninh">An ninh</option>
                <option value="Vệ sinh">Vệ sinh</option>
                <option value="Phòng cháy">Phòng cháy</option>
                <option value="Khác">Khác</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Nội dung <span style="color: var(--apple-red);">*</span></label>
            <textarea name="content" class="form-control" rows="5" required placeholder="Nhập nội dung nội quy..."></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Hình phạt</label>
            <input type="text" name="penalty" class="form-control" placeholder="VD: Cảnh cáo, phạt 100.000đ...">
        </div>
        
        <div class="d-flex gap-2" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Thêm nội quy</button>
            <a href="<?php echo url('/rules'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>