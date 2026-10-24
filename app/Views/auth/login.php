<div class="auth-header">
    <h1>DormFlow</h1>
    <p>Đăng nhập vào tài khoản của bạn</p>
</div>

<?php if ($error = flash('error')): ?>
<div class="alert alert-error">
    <?php echo $error; ?>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo url('/login'); ?>">
    <div class="form-group">
        <label class="form-label">Email</label>
        <input 
            type="email" 
            name="email" 
            class="form-control"
            value="admin@ktx.edu.vn"
            placeholder="your@email.com"
            required
            autofocus
        >
    </div>
    
    <div class="form-group">
        <label class="form-label">Mật khẩu</label>
        <input 
            type="password" 
            name="password" 
            class="form-control"
            value="password"
            placeholder="••••••••"
            required
        >
    </div>
    
    <div class="form-group">
        <label class="d-flex items-center gap-2">
            <input type="checkbox" name="remember">
            <span>Ghi nhớ đăng nhập</span>
        </label>
    </div>
    
    <button type="submit" class="btn btn-primary w-full">
        Đăng nhập
    </button>
    
    <div class="auth-footer">
        Chưa có tài khoản? <a href="<?php echo url('/register'); ?>">Đăng ký</a>
    </div>
</form>

</div>