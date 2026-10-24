<div class="d-flex justify-between items-center mb-4">
    <h2>Thông báo</h2>
    <?php if (is_admin()): ?>
    <a href="<?php echo url('/notices/create'); ?>" class="btn btn-primary">+ Tạo thông báo</a>
    <?php endif; ?>
</div>

<div class="card">
    <?php if (!empty($notices)): ?>
        <?php foreach ($notices as $notice): ?>
        <div class="mb-4 pb-4" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="d-flex items-center gap-3 mb-2">
                <span class="badge badge-<?php 
                    echo ($notice->type ?? '') == 'Khẩn cấp' ? 'danger' : 
                         (($notice->type ?? '') == 'Quan trọng' ? 'warning' : 
                         (($notice->type ?? '') == 'Sự kiện' ? 'success' : 'info')); 
                ?>"><?php echo $notice->type ?? 'Chung'; ?></span>
                <span class="text-muted"><?php echo isset($notice->published_at) ? date('d/m/Y H:i', strtotime($notice->published_at)) : 'N/A'; ?></span>
            </div>
            <h4 style="margin-bottom: 8px;"><?php echo $notice->title ?? 'N/A'; ?></h4>
            <p style="color: var(--apple-dark); line-height: 1.6;"><?php echo nl2br($notice->content ?? ''); ?></p>
            <?php if (is_admin()): ?>
            <div class="mt-3">
                <a href="<?php echo url('/notices/'.$notice->id.'/delete'); ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Xóa thông báo này?')">Xóa</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-muted py-4">Không có thông báo nào</p>
    <?php endif; ?>
</div>