<div class="d-flex justify-between items-center mb-4">
    <h2>Quản lý khách đến thăm</h2>
    <a href="<?php echo url('/visitors/create'); ?>" class="btn btn-primary">+ Đăng ký khách</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Khách</th>
                <th>CMND/CCCD</th>
                <th>Sinh viên</th>
                <th>Phòng</th>
                <th>Mục đích</th>
                <th>Thời gian vào</th>
                <th>Thời gian ra</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($visitors)): ?>
                <?php foreach ($visitors as $v): ?>
                <tr>
                    <td>
                        <strong><?php echo $v->visitor_name; ?></strong><br>
                        <small class="text-muted"><?php echo $v->phone; ?></small>
                    </td>
                    <td><?php echo $v->id_card ?: 'N/A'; ?></td>
                    <td><?php echo $v->student_name ?? '-'; ?></td>
                    <td><?php echo $v->room_number; ?></td>
                    <td><?php echo $v->purpose ?: 'Thăm'; ?></td>
                    <td><?php echo date('H:i d/m/Y', strtotime($v->check_in_time)); ?></td>
                    <td><?php echo $v->check_out_time ? date('H:i d/m/Y', strtotime($v->check_out_time)) : '-'; ?></td>
                    <td>
                        <span class="badge badge-<?php echo $v->status == 'checked_in' ? 'success' : 'warning'; ?>">
                            <?php echo $v->status == 'checked_in' ? 'Đang ở' : 'Đã ra'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($v->status == 'checked_in'): ?>
                        <a href="<?php echo url('/visitors/'.$v->id.'/checkout'); ?>" class="btn btn-sm btn-warning">Check-out</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center text-muted">Chưa có khách nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>