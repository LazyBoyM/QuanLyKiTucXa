<div class="d-flex justify-between items-center mb-4">
    <h2>Yêu cầu bảo trì</h2>
    <?php if (!is_admin()): ?>
    <a href="<?php echo url('/maintenance/create'); ?>" class="btn btn-primary">+ Tạo yêu cầu</a>
    <?php endif; ?>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Mã yêu cầu</th>
                <th>Tiêu đề</th>
                <?php if (is_admin()): ?>
                <th>Người yêu cầu</th>
                <?php endif; ?>
                <th>Phòng</th>
                <th>Loại</th>
                <th>Ưu tiên</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $r): ?>
                <tr>
                    <td><strong><?php echo $r->request_number ?? 'N/A'; ?></strong></td>
                    <td><?php echo $r->title ?? 'N/A'; ?></td>
                    <?php if (is_admin()): ?>
                    <td>
                        <?php echo $r->full_name ?? 'N/A'; ?><br>
                        <small class="text-muted"><?php echo $r->student_code ?? ''; ?></small>
                    </td>
                    <?php endif; ?>
                    <td><?php echo $r->room_number ?? 'N/A'; ?></td>
                    <td>
                        <?php 
                        $categories = [
                            'electrical' => 'Điện',
                            'plumbing' => 'Nước',
                            'furniture' => 'Nội thất',
                            'other' => 'Khác'
                        ];
                        echo $categories[$r->category] ?? ($r->category ?? 'N/A');
                        ?>
                    </td>
                    <td>
                        <span class="badge badge-<?php 
                            echo ($r->priority ?? '') == 'Khẩn cấp' ? 'danger' : 
                                 (($r->priority ?? '') == 'Cao' ? 'warning' : 'info'); 
                        ?>"><?php echo $r->priority ?? 'N/A'; ?></span>
                    </td>
                    <td>
                        <span class="badge badge-<?php 
                            echo ($r->status ?? '') == 'Đã hoàn thành' ? 'success' : 
                                 (($r->status ?? '') == 'Đang xử lý' ? 'info' : 'warning'); 
                        ?>"><?php echo $r->status ?? 'N/A'; ?></span>
                    </td>
                    <td><?php echo isset($r->created_at) ? date('d/m/Y', strtotime($r->created_at)) : 'N/A'; ?></td>
                    <td>
                        <a href="<?php echo url('/maintenance/'.$r->id); ?>" class="btn btn-sm">Chi tiết</a>
                        <?php if (is_admin() && ($r->status ?? '') == 'Chờ xử lý'): ?>
                        <a href="<?php echo url('/maintenance/'.$r->id.'/process'); ?>" class="btn btn-sm btn-primary">Nhận xử lý</a>
                        <?php endif; ?>
                        <?php if (is_admin() && ($r->status ?? '') == 'Đang xử lý'): ?>
                        <a href="<?php echo url('/maintenance/'.$r->id.'/complete'); ?>" class="btn btn-sm btn-success">Hoàn thành</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo is_admin() ? '9' : '7'; ?>" class="text-center text-muted py-4">
                        Chưa có yêu cầu bảo trì nào
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>