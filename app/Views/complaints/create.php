<div class="card" style="max-width: 700px;">
    <h3>Gửi góp ý - Khiếu nại</h3>
    <p class="text-muted mb-4">Mọi ý kiến của bạn sẽ được Ban quản lý KTX xem xét và phản hồi</p>
    
    <form method="POST" action="<?php echo url('/complaints'); ?>">
        <div class="form-group">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Danh mục</label>
            <select name="category" class="form-control" required>
                <option value="">Chọn danh mục</option>
                <option value="Tiếng ồn">Tiếng ồn</option>
                <option value="Vệ sinh">Vệ sinh</option>
                <option value="An ninh">An ninh</option>
                <option value="Nhân viên">Nhân viên</option>
                <option value="Cơ sở vật chất">Cơ sở vật chất</option>
                <option value="Khác">Khác</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Nội dung chi tiết</label>
            <textarea name="description" class="form-control" rows="6" required 
                      placeholder="Mô tả chi tiết vấn đề hoặc góp ý của bạn..."></textarea>
        </div>
        
        <div class="form-group">
            <label class="d-flex items-center gap-2">
                <input type="checkbox" name="is_anonymous" value="1">
                <span>Gửi ẩn danh</span>
            </label>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Gửi góp ý</button>
            <a href="<?php echo url('/complaints'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>