<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/reports'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Báo cáo tài chính</h2>
</div>

<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($monthlyRevenue); ?>đ</div>
            <div class="stat-label">Doanh thu tháng <?php echo $month; ?>/<?php echo $year; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($unpaidTotal); ?>đ</div>
            <div class="stat-label">Công nợ chưa thu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $overdueCount; ?></div>
            <div class="stat-label">Hóa đơn quá hạn</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <h4>Doanh thu 6 tháng gần đây</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Tháng</th>
                        <th class="text-right">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($last6Months as $m): ?>
                    <tr>
                        <td><?php echo $m['month']; ?></td>
                        <td class="text-right"><?php echo number_format($m['revenue']); ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <h4>Theo phương thức thanh toán</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Phương thức</th>
                        <th class="text-right">Số GD</th>
                        <th class="text-right">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueByMethod as $r): ?>
                    <tr>
                        <td><?php echo $r->payment_method; ?></td>
                        <td class="text-right"><?php echo $r->count; ?></td>
                        <td class="text-right"><?php echo number_format($r->total); ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($revenueByMethod)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>