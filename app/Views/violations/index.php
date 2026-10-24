<div class="d-flex justify-between items-center mb-4">
    <h2>Danh sách vi phạm</h2>
    <?php if (is_admin()): ?>
    <a href="<?php echo url('/violations/create'); ?>" class="btn btn-primary">+ Thêm vi phạm</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã VP</th>
                    <th>Tiêu đề</th>
                    <?php if (is_admin()): ?>
                    <th>Sinh viên</th>
                    <th>Phòng</th>
                    <?php endif; ?>
                    <th>Hình phạt</th>
                    <th>Tiền phạt</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($violations)): ?>
                    <?php foreach ($violations as $v): ?>
                    <tr>
                        <td><strong><?php echo $v->violation_number; ?></strong></td>
                        <td><?php echo $v->title; ?></td>
                        <?php if (is_admin()): ?>
                        <td>
                            <?php echo $v->full_name ?? 'N/A'; ?><br>
                            <small class="text-muted"><?php echo $v->student_code ?? ''; ?></small>
                        </td>
                        <td><?php echo $v->room_number ?? 'N/A'; ?></td>
                        <?php endif; ?>
                        <td><?php echo $v->penalty ?: 'Chưa có'; ?></td>
                        <td>
                            <?php if ($v->penalty_amount > 0): ?>
                            <strong style="color: var(--apple-red);"><?php echo number_format($v->penalty_amount); ?>đ</strong>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo ($v->status ?? '') == 'Đã xử lý' ? 'success' : 'warning'; ?>">
                                <?php echo $v->status ?? 'Chờ xử lý'; ?>
                            </span>
                        </td>
                        <td><?php echo isset($v->created_at) ? date('d/m/Y', strtotime($v->created_at)) : 'N/A'; ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo url('/violations/'.$v->id); ?>" class="btn btn-sm">Chi tiết</a>
                                <?php if (is_admin() && ($v->status ?? '') == 'Chờ xử lý'): ?>
                                <a href="<?php echo url('/violations/'.$v->id.'/process'); ?>" 
                                   class="btn btn-sm btn-success" 
                                   onclick="return confirm('Xác nhận đã xử lý vi phạm này?')">Xử lý</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?php echo is_admin() ? '9' : '7'; ?>" class="text-center text-muted py-4">
                            Chưa có vi phạm nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>