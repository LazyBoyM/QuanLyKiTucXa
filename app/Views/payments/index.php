<div class="d-flex justify-between items-center mb-4">
    <h2>Lịch sử thanh toán</h2>
    <?php if (!is_admin()): ?>
    <a href="<?php echo url('/payments/create'); ?>" class="btn btn-primary">Thanh toán hóa đơn</a>
    <?php endif; ?>
</div>

<?php if (is_admin() && isset($stats)): ?>
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($stats['total_revenue'] ?? 0); ?>đ</div>
            <div class="stat-label">Tổng doanh thu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($stats['monthly_revenue'] ?? 0); ?>đ</div>
            <div class="stat-label">Tháng <?php echo date('m/Y'); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['total_transactions'] ?? 0; ?></div>
            <div class="stat-label">Giao dịch</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['momo_transactions'] ?? 0; ?></div>
            <div class="stat-label">Qua MoMo</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Mã thanh toán</th>
                <th>Hóa đơn</th>
                <?php if (is_admin()): ?>
                <th>Sinh viên</th>
                <?php endif; ?>
                <th>Số tiền</th>
                <th>Phương thức</th>
                <th>Trạng thái</th>
                <th>Ngày thanh toán</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $p): ?>
                <tr>
                    <td><strong><?php echo $p->payment_number; ?></strong></td>
                    <td><?php echo $p->invoice_number ?? 'N/A'; ?></td>
                    <?php if (is_admin()): ?>
                    <td>
                        <?php echo $p->student_name ?? 'N/A'; ?><br>
                        <small class="text-muted"><?php echo $p->student_code ?? ''; ?></small>
                    </td>
                    <?php endif; ?>
                    <td><strong><?php echo number_format($p->amount); ?>đ</strong></td>
                    <td>
                        <span class="badge badge-<?php echo $p->payment_method == 'MoMo' ? 'info' : ''; ?>">
                            <?php echo $p->payment_method; ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?php 
                            echo $p->status == 'success' ? 'success' : 
                                 ($p->status == 'pending' ? 'warning' : 'danger'); 
                        ?>">
                            <?php 
                            if ($p->status == 'success') echo 'Thành công';
                            elseif ($p->status == 'pending') echo 'Đang xử lý';
                            else echo 'Thất bại';
                            ?>
                        </span>
                    </td>
                    <td><?php echo $p->payment_date ? date('d/m/Y H:i', strtotime($p->payment_date)) : '-'; ?></td>
                    <td>
                        <a href="<?php echo url('/payments/'.$p->id); ?>" class="btn btn-sm">Chi tiết</a>
                        <?php if ($p->status == 'pending' && $p->payment_method == 'MoMo'): ?>
                        <a href="<?php echo url('/payments/'.$p->id.'/momo'); ?>" class="btn btn-sm btn-primary">Thanh toán</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo is_admin() ? '8' : '7'; ?>" class="text-center text-muted py-4">
                        Chưa có thanh toán nào
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>