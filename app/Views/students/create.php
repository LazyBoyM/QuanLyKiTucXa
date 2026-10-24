<div class="card" style="max-width: 800px;">
    <h3>Thêm sinh viên mới</h3>
    
    <form method="POST" action="<?php echo url('/students'); ?>">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                    <input type="text" name="student_code" class="form-control" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="date_of_birth" class="form-control">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Giới tính</label>
                    <select name="gender" class="form-control">
                        <option value="">Chọn</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Khoa</label>
                    <input type="text" name="faculty" class="form-control">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Lớp</label>
                    <input type="text" name="class" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Khóa học</label>
            <input type="text" name="academic_year" class="form-control" placeholder="VD: K64">
        </div>
        
        <div class="form-group">
            <label class="form-label">Địa chỉ thường trú</label>
            <textarea name="permanent_address" class="form-control" rows="2"></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">CMND/CCCD</label>
            <input type="text" name="id_card" class="form-control">
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Thêm sinh viên</button>
            <a href="<?php echo url('/students'); ?>" class="btn">Hủy</a>
        </div>
    </form>
</div>