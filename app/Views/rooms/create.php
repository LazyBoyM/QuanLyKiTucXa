<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/rooms'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Thêm phòng mới</h2>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <h4 style="margin-bottom: 20px;">Thông tin phòng</h4>
            
            <form method="POST" action="<?php echo url('/rooms'); ?>">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Số phòng <span style="color: var(--apple-red);">*</span></label>
                            <input type="text" name="room_number" class="form-control" placeholder="VD: 101" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Tầng <span style="color: var(--apple-red);">*</span></label>
                            <input type="number" name="floor" class="form-control" value="1" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tòa nhà <span style="color: var(--apple-red);">*</span></label>
                    <select name="building_id" class="form-control" required>
                        <option value="">Chọn tòa nhà</option>
                        <?php foreach ($buildings as $building): ?>
                        <option value="<?php echo $building->id; ?>">
                            <?php echo $building->name; ?> (<?php echo $building->code; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Loại phòng</label>
                            <select name="room_type" class="form-control">
                                <option value="4">Phòng 4 người</option>
                                <option value="6">Phòng 6 người</option>
                                <option value="8">Phòng 8 người</option>
                                <option value="2">Phòng 2 người</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Sức chứa <span style="color: var(--apple-red);">*</span></label>
                            <input type="number" name="capacity" class="form-control" value="4" min="1" max="8" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Giá phòng/tháng (VNĐ) <span style="color: var(--apple-red);">*</span></label>
                    <input type="number" name="price" class="form-control" value="400000" required>
                </div>
                
                <div class="d-flex gap-2" style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">Thêm phòng</button>
                    <a href="<?php echo url('/rooms'); ?>" class="btn">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card">
            <h4 style="margin-bottom: 16px;">Hướng dẫn</h4>
            <ul style="color: var(--apple-gray); line-height: 1.8; padding-left: 20px;">
                <li>Nhập số phòng theo đúng format (VD: A101, B202)</li>
                <li>Chọn tòa nhà phù hợp với loại phòng</li>
                <li>Sức chứa phòng sẽ tự động tạo giường tương ứng</li>
                <li>Giá phòng sẽ áp dụng cho mỗi sinh viên/tháng</li>
            </ul>
            
            <div class="alert alert-info mt-4">
                <strong>Lưu ý:</strong> Sau khi thêm phòng, hệ thống sẽ tự động tạo các giường tương ứng với sức chứa.
            </div>
        </div>
    </div>
</div>