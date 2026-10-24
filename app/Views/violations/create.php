<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/violations'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Thêm vi phạm mới</h2>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <form method="POST" action="<?php echo url('/violations'); ?>">
                <div class="form-group">
                    <label class="form-label">Sinh viên vi phạm <span style="color: var(--apple-red);">*</span></label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Chọn sinh viên</option>
                        <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s->id; ?>">
                            <?php echo $s->full_name; ?> - <?php echo $s->student_code; ?> (Phòng <?php echo $s->room_number; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nội quy vi phạm</label>
                    <select name="rule_id" class="form-control">
                        <option value="">Chọn nội quy (nếu có)</option>
                        <?php foreach ($rules as $r): ?>
                        <option value="<?php echo $r->id; ?>">
                            [<?php echo $r->category; ?>] <?php echo $r->title; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tiêu đề vi phạm <span style="color: var(--apple-red);">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="VD: Gây ồn ào sau 22h">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Mô tả chi tiết <span style="color: var(--apple-red);">*</span></label>
                    <textarea name="description" class="form-control" rows="4" required placeholder="Mô tả chi tiết hành vi vi phạm..."></textarea>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Hình phạt</label>
                            <input type="text" name="penalty" class="form-control" placeholder="VD: Cảnh cáo">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Tiền phạt (VNĐ)</label>
                            <input type="number" name="penalty_amount" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2" style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">Thêm vi phạm</button>
                    <a href="<?php echo url('/violations'); ?>" class="btn">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card">
            <h4 style="margin-bottom: 16px;">Hướng dẫn</h4>
            <ul style="color: var(--apple-gray); line-height: 1.8; padding-left: 20px;">
                <li>Chọn sinh viên từ danh sách</li>
                <li>Có thể chọn nội quy bị vi phạm</li>
                <li>Nhập đầy đủ thông tin vi phạm</li>
                <li>Hình phạt và tiền phạt có thể để trống</li>
            </ul>
        </div>
    </div>
</div>