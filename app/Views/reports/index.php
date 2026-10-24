<div class="d-flex justify-between items-center mb-4">
    <h2>Báo cáo thống kê</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card" style="padding: 32px 24px;">
            <h3 style="margin-bottom: 8px;">Tài chính</h3>
            <p style="color: var(--apple-gray); margin-bottom: 24px;">Doanh thu, thanh toán, công nợ</p>
            <a href="<?php echo url('/reports/financial'); ?>" class="btn btn-primary">Xem báo cáo</a>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card" style="padding: 32px 24px;">
            <h3 style="margin-bottom: 8px;">Công suất</h3>
            <p style="color: var(--apple-gray); margin-bottom: 24px;">Tỷ lệ lấp đầy, phòng trống</p>
            <a href="<?php echo url('/reports/occupancy'); ?>" class="btn btn-primary">Xem báo cáo</a>
        </div>
    </div>
</div>

<div class="card mt-4">
    <h4>Xuất dữ liệu</h4>
    <p style="color: var(--apple-gray); margin-bottom: 16px;">Tải báo cáo dạng CSV</p>
    <div class="d-flex gap-3">
        <a href="<?php echo url('/reports/export?type=financial'); ?>" class="btn">Báo cáo tài chính (CSV)</a>
        <a href="<?php echo url('/reports/export?type=occupancy'); ?>" class="btn">Báo cáo công suất (CSV)</a>
    </div>
</div>