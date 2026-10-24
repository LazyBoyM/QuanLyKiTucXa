<div class="d-flex justify-between items-center mb-4">
    <h2>Chi tiết yêu cầu</h2>
    <div>
        <?php if (is_admin()): ?>
        <a href="<?php echo url('/maintenance'); ?>" class="btn">Quay lại</a>
        <?php else: ?>
        <a href="<?php echo url('/maintenance'); ?>" class="btn">Quay lại</a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div style="font-size: 48px; margin-bottom: 12px;">🔧</div>
            <h3><?php echo $request->title ?? 'N/A'; ?></h3>
            <p class="text-muted">Mã: <?php echo $request->request_number ?? 'N/A'; ?></p>
            <p>
                <span class="badge badge-<?php echo ($request->priority ?? '') == 'Khẩn cấp' ? 'danger' : (($request->priority ?? '') == 'Cao' ? 'warning' : 'info'); ?>">
                    <?php echo $request->priority ?? 'N/A'; ?>
                </span>
            </p>
            <p>
                <span class="badge badge-<?php echo ($request->status ?? '') == 'Đã hoàn thành' ? 'success' : (($request->status ?? '') == 'Đang xử lý' ? 'info' : 'warning'); ?>">
                    <?php echo $request->status ?? 'N/A'; ?>
                </span>
            </p>
        </div>

        <div class="card mt-4">
            <h4>Thông tin</h4>
            <p><strong>Phòng:</strong> <?php echo $request->room_number ?? 'N/A'; ?></p>
            <?php if (is_admin()): ?>
            <p><strong>Người yêu cầu:</strong> <?php echo $request->full_name ?? 'N/A'; ?></p>
            <p><strong>SĐT:</strong> <?php echo $request->phone ?? 'N/A'; ?></p>
            <?php endif; ?>
            <?php if (!empty($request->assigned_to)): ?>
            <p><strong>Người xử lý:</strong> <?php echo $request->assigned_to; ?></p>
            <?php endif; ?>
            <?php if (!empty($request->completed_at)): ?>
            <p><strong>Hoàn thành lúc:</strong> <?php echo date('d/m/Y H:i', strtotime($request->completed_at)); ?></p>
            <?php endif; ?>
            <p><strong>Ngày tạo:</strong> <?php echo isset($request->created_at) ? date('d/m/Y H:i', strtotime($request->created_at)) : 'N/A'; ?></p>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <h4>Mô tả</h4>
            <p style="line-height:1.6; color:var(--apple-dark);"><?php echo nl2br($request->description ?? ''); ?></p>
        </div>

        <div class="card mt-4">
            <h4>Hành động</h4>
            <div>
                <?php if (is_admin() && ($request->status ?? '') == 'Chờ xử lý'): ?>
                <a href="<?php echo url('/maintenance/'.$request->id.'/process'); ?>" class="btn btn-primary">Nhận xử lý</a>
                <?php endif; ?>

                <?php if (is_admin() && ($request->status ?? '') == 'Đang xử lý'): ?>
                <a href="<?php echo url('/maintenance/'.$request->id.'/complete'); ?>" class="btn btn-success">Hoàn thành</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
