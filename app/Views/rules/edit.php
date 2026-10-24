<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/rules'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Sửa nội quy</h2>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="<?php echo url('/rules/'.$rule->id); ?>">
        <div class="form-group">
            <label class="form-label">Tiêu đề <span style="color: var(--apple-red);">*</span></label>
            <input type="text" name="title" class="form-control" value="<?php echo $rule->title; ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Danh mục</label>
            <select name="category" class="form-control">
                <option value="Chung" <?php echo $rule->category == 'Chung' ? 'selected' : ''; ?>>Chung</option>
                <option value="An ninh" <?php echo $rule->category == 'An ninh' ? 'selected' : ''; ?>>An ninh</option>
                <option value="Vệ sinh" <?php echo $rule->category == 'Vệ sinh' ? 'selected' : ''; ?>>Vệ sinh</option>
                <option value="Phòng cháy" <?php echo $rule->category == 'Phòng cháy' ? 'selected' : ''; ?>>Phòng cháy</option>
                <option value="Khác" <?php echo $rule->category == 'Khác' ? 'selected' : ''; ?>>Khác</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Nội dung <span style="color: var(--apple-red);">*</span></label>
            <textarea name="content" class="form-control" rows="5" required><?php echo $rule->content; ?></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Hình phạt</label>
            <input type="text" name="penalty" class="form-control" value="<?php echo $rule->penalty; ?>">
        </div>
        
        <div class="d-flex gap-2" style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="<?php echo url('/rules'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>