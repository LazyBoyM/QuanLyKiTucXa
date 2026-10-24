<div class="card">
    <h3><?php echo $pageTitle ?? 'Đăng ký dịch vụ'; ?></h3>

    <?php if (!is_admin()): ?>
    <div class="mb-3">
        <a href="<?php echo url('/services/create'); ?>" class="btn btn-primary">Tạo đăng ký dịch vụ mới</a>
    </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Mã</th>
                <th>Loại</th>
                <th>Tiêu đề</th>
                <th>Trạng thái</th>
                <th>Ngày</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $it): ?>
            <tr>
                <td><?php echo $it->id; ?></td>
                <td><?php echo $it->registration_number; ?></td>
                <td><?php echo $it->service_type; ?></td>
                <td><?php echo $it->title; ?></td>
                <td><?php echo $it->status; ?></td>
                <td><?php echo date('d/m/Y', strtotime($it->created_at)); ?></td>
                <td>
                    <a href="<?php echo url('/services/'.$it->id); ?>" class="btn">Xem</a>
                    <?php if (is_admin()): ?>
                        <?php if ($it->status === 'Chờ xử lý'): ?>
                            <form method="POST" action="<?php echo url('/services/'.$it->id.'/process'); ?>" style="display:inline-block; margin-left:6px;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm">Nhận xử lý</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($it->status !== 'Đã hoàn thành'): ?>
                            <form method="POST" action="<?php echo url('/services/'.$it->id.'/complete'); ?>" style="display:inline-block; margin-left:6px;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm">Hoàn thành</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
