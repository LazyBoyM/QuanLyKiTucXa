<div class="d-flex justify-between items-center mb-4">
    <h2>Quản lý hóa đơn</h2>
</div>

<?php if (is_admin()): ?>
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['total_invoices'] ?? 0; ?></div>
            <div class="stat-label">Tổng hóa đơn</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['paid_invoices'] ?? 0; ?></div>
            <div class="stat-label">Đã thanh toán</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['pending_invoices'] ?? 0; ?></div>
            <div class="stat-label">Chưa thanh toán</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($stats['total_amount'] ?? 0); ?>đ</div>
            <div class="stat-label">Tổng tiền chưa thu</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Mã hóa đơn</th>
                <th>Sinh viên</th>
                <th>MSSV</th>
                <th>Phòng</th>
                <th>Tháng/Năm</th>
                <th>Tổng tiền</th>
                <th>Hạn thanh toán</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($invoices)): ?>
                <?php foreach ($invoices as $inv): ?>
                <tr>
                    <td><strong><?php echo $inv->invoice_number; ?></strong></td>
                    <td><?php echo $inv->full_name ?? 'N/A'; ?></td>
                    <td><?php echo $inv->student_code ?? 'N/A'; ?></td>
                    <td><?php echo $inv->room_number ?? 'N/A'; ?></td>
                    <td><?php echo $inv->month; ?>/<?php echo $inv->year; ?></td>
                    <td><strong><?php echo number_format($inv->total_amount); ?>đ</strong></td>
                    <td><?php echo date('d/m/Y', strtotime($inv->due_date)); ?></td>
                    <td>
                        <span class="badge badge-<?php 
                            echo $inv->status == 'Đã thanh toán' ? 'success' : 
                                 ($inv->status == 'Quá hạn' ? 'danger' : 'warning'); 
                        ?>">
                            <?php echo $inv->status; ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo url('/invoices/'.$inv->id); ?>" class="btn btn-sm">Chi tiết</a>
                        <?php if ($inv->status != 'Đã thanh toán' && is_admin()): ?>
                        <a href="<?php echo url('/payments/create?invoice='.$inv->id); ?>" class="btn btn-sm btn-primary">Thanh toán</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Chưa có hóa đơn nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>