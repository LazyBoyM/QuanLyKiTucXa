<div class="d-flex justify-between items-center mb-4">
    <h2>Đơn đăng ký KTX</h2>
    <?php if (!is_admin()): ?>
    <a href="<?php echo url('/registrations/create'); ?>" class="btn btn-primary">Đăng ký mới</a>
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
</div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Sinh viên</th>
                <th>MSSV</th>
                <th>Tòa mong muốn</th>
                <th>Loại phòng</th>
                <th>Ngày đăng ký</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($registrations)): ?>
                <?php foreach ($registrations as $r): ?>
                <tr>
                    <td><strong><?php echo $r->registration_number; ?></strong></td>
<td><?php echo $r->full_name ?? 'N/A'; ?></td>
<td><?php echo $r->student_code ?? 'N/A'; ?></td>
<td><?php echo $r->building_name ?? 'Chưa chọn'; ?></td>
<td><?php echo $r->desired_room_type ?? 'N/A'; ?> người/phòng</td>
<td><?php echo isset($r->registration_date) ? date('d/m/Y', strtotime($r->registration_date)) : 'N/A'; ?></td>
<td>
    <span class="badge badge-<?php 
        echo ($r->status ?? '') == 'Đã xếp phòng' ? 'success' : 
             (($r->status ?? '') == 'Đã duyệt' ? 'info' : 
             (($r->status ?? '') == 'Chờ duyệt' ? 'warning' : 'danger')); 
    ?>"><?php echo $r->status ?? 'N/A'; ?></span>
</td>
                    <td>
                        <a href="<?php echo url('/registrations/'.$r->id); ?>" class="btn btn-sm">Chi tiết</a>
                        <?php if (is_admin()): ?>
                            <?php if ($r->status == 'Chờ duyệt'): ?>
                            <a href="<?php echo url('/registrations/'.$r->id.'/approve'); ?>" 
                            class="btn btn-sm btn-success" 
                            onclick="return confirm('Duyệt đơn này?')">Duyệt</a>
                            <?php elseif ($r->status == 'Đã duyệt'): ?>
                            <a href="<?php echo url('/registrations/'.$r->id.'/assign-room'); ?>" 
                            class="btn btn-sm btn-primary">Xếp phòng</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted">Chưa có đơn đăng ký nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>