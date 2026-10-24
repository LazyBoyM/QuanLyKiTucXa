<div class="card" style="max-width:700px;">
    <h3>Đăng ký dịch vụ</h3>
    <p class="text-muted mb-4">Chọn loại dịch vụ và mô tả yêu cầu của bạn.</p>

    <form method="POST" action="<?php echo url('/services'); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label class="form-label">Loại dịch vụ</label>
            <select name="service_type" class="form-control" required>
                <?php foreach ($service_types as $st): ?>
                <option value="<?php echo $st; ?>"><?php echo $st; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" required placeholder="Tiêu đề ngắn gọn">
        </div>

        <div class="form-group">
            <label class="form-label">Mô tả chi tiết</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Mô tả chi tiết về yêu cầu"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Ngày yêu cầu</label>
            <input type="date" name="requested_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Gửi đăng ký</button>
            <a href="<?php echo url('/services'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>
