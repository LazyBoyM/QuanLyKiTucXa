<div class="d-flex justify-between items-center mb-4">
    <h2>Nội quy ký túc xá</h2>
    <?php if (is_admin()): ?>
    <a href="<?php echo url('/rules/create'); ?>" class="btn btn-primary">+ Thêm nội quy</a>
    <?php endif; ?>
</div>

<?php if (!empty($categories)): ?>
<div class="card mb-4">
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo url('/rules'); ?>" class="btn btn-sm <?php echo empty($current_category) ? 'btn-primary' : ''; ?>">Tất cả</a>
        <?php foreach ($categories as $cat): ?>
        <a href="<?php echo url('/rules?category=' . $cat->category); ?>" 
           class="btn btn-sm <?php echo $current_category == $cat->category ? 'btn-primary' : ''; ?>">
            <?php echo $cat->category; ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <?php if (!empty($rules)): ?>
        <?php foreach ($rules as $rule): ?>
        <div class="mb-4 pb-4" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="d-flex justify-between items-start mb-2">
                <div>
                    <div class="d-flex items-center gap-3 mb-2">
                        <span class="badge badge-<?php 
                            echo $rule->category == 'An ninh' ? 'danger' : 
                                 ($rule->category == 'Vệ sinh' ? 'info' : 'warning'); 
                        ?>"><?php echo $rule->category; ?></span>
                        <h4 style="margin: 0;"><?php echo $rule->title; ?></h4>
                    </div>
                    <p style="margin-bottom: 8px;"><?php echo nl2br($rule->content); ?></p>
                    <?php if ($rule->penalty): ?>
                    <p class="text-danger" style="font-size: 13px;">
                        <strong>Hình phạt:</strong> <?php echo $rule->penalty; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php if (is_admin()): ?>
                <div class="d-flex gap-2">
                    <a href="<?php echo url('/rules/'.$rule->id.'/edit'); ?>" class="btn btn-sm">Sửa</a>
                    <a href="<?php echo url('/rules/'.$rule->id.'/delete'); ?>" 
                       class="btn btn-sm text-danger" onclick="return confirm('Xóa nội quy này?')">Xóa</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-muted py-4">Chưa có nội quy nào</p>
    <?php endif; ?>
</div>