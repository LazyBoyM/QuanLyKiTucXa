<div class="auth-header">
    <h1>DormFlow</h1>
    <p>Tạo tài khoản sinh viên</p>
</div>

<?php if ($error = flash('error')): ?>
<div class="alert alert-error">
    <?php echo $error; ?>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo url('/register'); ?>">
    <div class="form-group">
        <label class="form-label">Họ và tên</label>
        <input 
            type="text" 
            name="full_name" 
            class="form-control" 
            value="<?php echo old('full_name'); ?>"
            placeholder="Nguyễn Văn A"
            required
        >
    </div>
    
    <div class="form-group">
        <label class="form-label">Email</label>
        <input 
            type="email" 
            name="email" 
            class="form-control" 
            value="<?php echo old('email'); ?>"
            placeholder="example@sv.edu.vn"
            required
        >
    </div>
    
    <div class="form-group">
        <label class="form-label">Số điện thoại</label>
        <input 
            type="tel" 
            name="phone" 
            class="form-control" 
            value="<?php echo old('phone'); ?>"
            placeholder="0912345678"
            required
        >
    </div>
    
    <div class="form-group">
        <label class="form-label">Mật khẩu</label>
        <input 
            type="password" 
            name="password" 
            class="form-control" 
            placeholder="Ít nhất 6 ký tự"
            required
        >
    </div>
    
    <div class="form-group">
        <label class="form-label">Xác nhận mật khẩu</label>
        <input 
            type="password" 
            name="password_confirmation" 
            class="form-control" 
            placeholder="Nhập lại mật khẩu"
            required
        >
    </div>
    
    <button type="submit" class="btn btn-primary w-full">
        Đăng ký
    </button>
    
    <div class="auth-footer">
        Đã có tài khoản? <a href="<?php echo url('/login'); ?>">Đăng nhập</a>
    </div>
</form>