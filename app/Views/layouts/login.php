<div class="auth-header">
    <h1>DormFlow</h1>
    <p>Đăng nhập để tiếp tục</p>
</div>

<?php if ($error = flash('error')): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo url('/login'); ?>">
    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" 
               value="admin@dormflow.com" placeholder="your@email.com" required>
    </div>
    
    <div class="form-group">
        <label class="form-label">Mật khẩu</label>
        <input type="password" name="password" class="form-control" 
               value="password" placeholder="••••••••" required>
    </div>
    
    <button type="submit" class="btn btn-primary w-full">Đăng nhập</button>
</form>

<div class="auth-footer">
    Chưa có tài khoản? <a href="<?php echo url('/register'); ?>">Đăng ký</a>
</div>