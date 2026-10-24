<div class="d-flex justify-between items-center mb-4">
    <h2>Góp ý - Khiếu nại</h2>
    <?php if (!is_admin()): ?>
    <a href="<?php echo url('/complaints/create'); ?>" class="btn btn-primary">+ Gửi góp ý</a>
    <?php endif; ?>
</div>

<?php if (is_admin() && isset($stats)): ?>
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['pending'] ?? 0; ?></div>
            <div class="stat-label">Chờ xử lý</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['processing'] ?? 0; ?></div>
            <div class="stat-label">Đang xem xét</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['resolved'] ?? 0; ?></div>
            <div class="stat-label">Đã giải quyết</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <?php if (!empty($complaints)): ?>
        <?php foreach ($complaints as $c): ?>
        <div class="mb-4 pb-4" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="d-flex justify-between items-start mb-2">
                <div>
                    <div class="d-flex items-center gap-3 mb-1">
                        <span class="badge badge-<?php 
                            echo $c->status == 'Đã giải quyết' ? 'success' : 
                                 ($c->status == 'Đang xem xét' ? 'info' : 'warning'); 
                        ?>"><?php echo $c->status; ?></span>
                        <span class="badge"><?php echo $c->category; ?></span>
                        <span class="text-muted"><?php echo date('d/m/Y', strtotime($c->created_at)); ?></span>
                        <span class="text-muted">#<?php echo $c->complaint_number; ?></span>
                    </div>
                    <h4 style="margin-bottom: 8px;"><?php echo $c->title; ?></h4>
                    <p style="margin-bottom: 8px;"><?php echo nl2br($c->description); ?></p>
                    <small class="text-muted">
                        <?php echo $c->is_anonymous ? 'Ẩn danh' : ($c->full_name ?? 'N/A'); ?>
                    </small>
                </div>
                <?php if (is_admin() && $c->status != 'Đã giải quyết'): ?>
                <a href="<?php echo url('/complaints/'.$c->id.'/respond'); ?>" class="btn btn-sm btn-primary">Phản hồi</a>
                <?php endif; ?>
            </div>
            
            <?php if ($c->response): ?>
            <div style="background: rgba(0,122,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;">
                <strong>Phản hồi từ Ban quản lý:</strong>
                <p style="margin-top: 4px;"><?php echo nl2br($c->response); ?></p>
                <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($c->responded_at)); ?></small>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-muted py-4">Chưa có góp ý nào</p>
    <?php endif; ?>
</div>