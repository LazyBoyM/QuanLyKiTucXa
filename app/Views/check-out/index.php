<div class="d-flex justify-between items-center mb-4">
    <h2>Yêu cầu trả phòng</h2>
    <?php if (!is_admin()): ?>
    <a href="<?php echo url('/check-out/create'); ?>" class="btn btn-primary">+ Tạo yêu cầu</a>
    <?php endif; ?>
</div>

<?php if (is_admin()): ?>
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $pending_count ?? 0; ?></div>
            <div class="stat-label">Chờ duyệt</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $approved_count ?? 0; ?></div>
            <div class="stat-label">Đã duyệt</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $completed_count ?? 0; ?></div>
            <div class="stat-label">Đã hoàn thành</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã yêu cầu</th>
                    <?php if (is_admin()): ?>
                    <th>Sinh viên</th>
                    <th>MSSV</th>
                    <?php endif; ?>
                    <th>Phòng</th>
                    <th>Ngày dự kiến</th>
                    <th>Lý do</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><strong><?php echo $r->request_number; ?></strong></td>
                        <?php if (is_admin()): ?>
                        <td><?php echo $r->full_name ?? 'N/A'; ?></td>
                        <td><?php echo $r->student_code ?? 'N/A'; ?></td>
                        <?php endif; ?>
                        <td><?php echo $r->room_number ?? 'N/A'; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($r->request_date)); ?></td>
                        <td><?php echo $r->reason ?: 'Kết thúc học kỳ'; ?></td>
                        <td>
                            <span class="badge badge-<?php 
                                echo $r->status == 'Đã hoàn thành' ? 'success' : 
                                     ($r->status == 'Đã duyệt' ? 'info' : 
                                     ($r->status == 'Chờ duyệt' ? 'warning' : 'danger')); 
                            ?>">
                                <?php echo $r->status; ?>
                            </span>
                        </td>
                        <td>
    <div class="d-flex gap-2">
        <?php if (is_admin() && $r->status == 'Chờ duyệt'): ?>
        <form method="POST" action="<?php echo url('/check-out/approve'); ?>" style="display: inline;">
            <input type="hidden" name="id" value="<?php echo $r->id; ?>">
            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Duyệt yêu cầu này?')">Duyệt</button>
        </form>
        <?php endif; ?>
        
        <?php if (is_admin() && $r->status == 'Đã duyệt'): ?>
        <form method="POST" action="<?php echo url('/check-out/complete'); ?>" style="display: inline;">
            <input type="hidden" name="id" value="<?php echo $r->id; ?>">
            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Xác nhận hoàn thành trả phòng?')">Hoàn thành</button>
        </form>
        <?php endif; ?>
        
        <?php if (!is_admin() && $r->status == 'Chờ duyệt'): ?>
        <span class="text-muted">Đang chờ duyệt</span>
        <?php endif; ?>
    </div>
</td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?php echo is_admin() ? '8' : '6'; ?>" class="text-center text-muted py-4">
                            <p style="margin-bottom: 8px;">Chưa có yêu cầu trả phòng nào</p>
                            <?php if (!is_admin()): ?>
                            <a href="<?php echo url('/check-out/create'); ?>" class="btn btn-sm btn-primary">Tạo yêu cầu mới</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!is_admin() && !empty($requests)): ?>
<div class="alert alert-info mt-4">
    <strong>Lưu ý:</strong> Yêu cầu trả phòng sẽ được Ban quản lý xem xét. 
    Bạn cần hoàn tất các khoản thanh toán còn nợ trước khi trả phòng.
</div>
<?php endif; ?>