<div class="card" style="max-width:800px;">
    <h3><?php echo $pageTitle ?? 'Chi tiết đăng ký'; ?></h3>

    <div class="mb-3">
        <strong>Mã:</strong> <?php echo $item->registration_number; ?><br>
        <strong>Loại dịch vụ:</strong> <?php echo $item->service_type; ?><br>
        <strong>Tiêu đề:</strong> <?php echo $item->title; ?><br>
        <strong>Mô tả:</strong>
        <div class="mt-2 mb-2"><?php echo nl2br($item->description); ?></div>
        <strong>Trạng thái:</strong> <?php echo $item->status; ?><br>
        <strong>Ngày tạo:</strong> <?php echo $item->created_at; ?><br>
    </div>

    <?php if (is_admin()): ?>
    <div class="d-flex gap-2">
        <?php if ($item->status === 'Chờ xử lý'): ?>
            <form method="POST" action="<?php echo url('/services/'.$item->id.'/process'); ?>" style="display:inline-block; margin-right:6px;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-primary">Nhận xử lý</button>
            </form>
        <?php endif; ?>
        <?php if ($item->status !== 'Đã hoàn thành'): ?>
            <form method="POST" action="<?php echo url('/services/'.$item->id.'/complete'); ?>" style="display:inline-block;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-success">Đánh dấu hoàn thành</button>
            </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="<?php echo url('/services'); ?>" class="btn">Quay lại</a>
    </div>
</div>
