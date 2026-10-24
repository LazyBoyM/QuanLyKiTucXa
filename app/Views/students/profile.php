<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/dashboard'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Hồ sơ sinh viên</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <p class="text-muted" style="margin-bottom: 24px;">Cập nhật thông tin cá nhân của bạn</p>
            
            <?php if ($errors = flash('errors')): ?>
            <div class="alert alert-error">
                Vui lòng kiểm tra lại thông tin bên dưới
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo url('/profile'); ?>">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Mã số sinh viên</label>
                            <input type="text" name="student_code" class="form-control" 
                                   value="<?php echo old('student_code', $student->student_code ?? ''); ?>">
                            <?php if (isset($errors['student_code'])): ?>
                            <p class="text-danger" style="font-size: 12px; margin-top: 4px;"><?php echo $errors['student_code']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Họ và tên <span style="color: var(--apple-red);">*</span></label>
                            <input type="text" name="full_name" class="form-control" 
                                   value="<?php echo old('full_name', $student->full_name ?? ''); ?>" required>
                            <?php if (isset($errors['full_name'])): ?>
                            <p class="text-danger" style="font-size: 12px; margin-top: 4px;"><?php echo $errors['full_name']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo old('email', $student->email ?? ''); ?>">
                            <?php if (isset($errors['email'])): ?>
                            <p class="text-danger" style="font-size: 12px; margin-top: 4px;"><?php echo $errors['email']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?php echo old('phone', $student->phone ?? ''); ?>">
                            <?php if (isset($errors['phone'])): ?>
                            <p class="text-danger" style="font-size: 12px; margin-top: 4px;"><?php echo $errors['phone']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control" 
                                   value="<?php echo old('date_of_birth', $student->date_of_birth ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-control">
                                <option value="">Chọn</option>
                                <option value="Nam" <?php echo ($student->gender ?? '') == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?php echo ($student->gender ?? '') == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Khác" <?php echo ($student->gender ?? '') == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Khoa</label>
                            <input type="text" name="faculty" class="form-control" 
                                   value="<?php echo old('faculty', $student->faculty ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Lớp</label>
                            <input type="text" name="class" class="form-control" 
                                   value="<?php echo old('class', $student->class ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Khóa học</label>
                    <input type="text" name="academic_year" class="form-control" 
                           value="<?php echo old('academic_year', $student->academic_year ?? ''); ?>" placeholder="VD: K64">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Địa chỉ thường trú</label>
                    <textarea name="permanent_address" class="form-control" rows="2"><?php echo old('permanent_address', $student->permanent_address ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Số CMND/CCCD</label>
                    <input type="text" name="id_card" class="form-control" 
                           value="<?php echo old('id_card', $student->id_card ?? ''); ?>">
                </div>
                
                <div class="d-flex gap-2" style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    <a href="<?php echo url('/dashboard'); ?>" class="btn">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <h4 style="margin-bottom: 16px;">Hướng dẫn</h4>
            <ul style="color: var(--apple-gray); line-height: 1.8; padding-left: 20px;">
                <li>Mã số sinh viên là bắt buộc để đăng ký KTX</li>
                <li>Họ tên phải trùng với giấy tờ tùy thân</li>
                <li>Email và số điện thoại dùng để nhận thông báo</li>
                <li>CMND/CCCD cần chính xác để làm hợp đồng</li>
            </ul>
            
            <div class="alert alert-info mt-4">
                <strong>Lưu ý:</strong> Sau khi cập nhật hồ sơ, bạn có thể đăng ký KTX ngay.
            </div>
        </div>
        
        <div class="card mt-4">
            <h4 style="margin-bottom: 16px;">Thông tin KTX</h4>
            <p style="color: var(--apple-gray); margin-bottom: 8px;">
                <strong>Giờ check-in:</strong> <?php echo setting('check_in_time', '14:00'); ?>
            </p>
            <p style="color: var(--apple-gray); margin-bottom: 8px;">
                <strong>Giờ check-out:</strong> <?php echo setting('check_out_time', '12:00'); ?>
            </p>
            <p style="color: var(--apple-gray); margin-bottom: 8px;">
                <strong>Tiền đặt cọc:</strong> <?php echo number_format(setting('default_deposit', 500000)); ?>đ
            </p>
            <p style="color: var(--apple-gray);">
                <strong>Liên hệ:</strong> <?php echo setting('contact_phone', '0123456789'); ?>
            </p>
        </div>
    </div>
</div>