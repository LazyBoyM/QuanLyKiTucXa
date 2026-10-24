<div class="d-flex justify-between items-center mb-4">
    <h2>Hợp đồng</h2>
</div>

<?php if (is_admin() && isset($stats)): ?>
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['active'] ?? 0; ?></div>
            <div class="stat-label">Hợp đồng hiệu lực</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['ended'] ?? 0; ?></div>
            <div class="stat-label">Đã kết thúc</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Số hợp đồng</th>
                <?php if (is_admin()): ?>
                <th>Sinh viên</th>
                <th>MSSV</th>
                <?php endif; ?>
                <th>Phòng</th>
                <th>Giường</th>
                <th>Ngày vào</th>
                <th>Tiền phòng/tháng</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($contracts)): ?>
                <?php foreach ($contracts as $c): ?>
                <tr>
                    <td><strong><?php echo $c->contract_number; ?></strong></td>
                    <?php if (is_admin()): ?>
                    <td><?php echo $c->full_name ?? 'N/A'; ?></td>
                    <td><?php echo $c->student_code ?? 'N/A'; ?></td>
                    <?php endif; ?>
                    <td><?php echo $c->room_number ?? 'N/A'; ?></td>
                    <td><?php echo $c->bed_number ?? 'N/A'; ?></td>
                    <td><?php echo isset($c->check_in_date) ? date('d/m/Y', strtotime($c->check_in_date)) : 'N/A'; ?></td>
                    <td><?php echo number_format($c->monthly_rent ?? 0); ?>đ</td>
                    <td>
                        <span class="badge badge-<?php echo ($c->status ?? '') == 'Hiệu lực' ? 'success' : 'warning'; ?>">
                            <?php echo $c->status ?? 'N/A'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo url('/contracts/'.$c->id); ?>" class="btn btn-sm">Chi tiết</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo is_admin() ? '9' : '6'; ?>" class="text-center text-muted py-4">
                        Chưa có hợp đồng nào
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>