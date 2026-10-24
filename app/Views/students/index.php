<div class="d-flex justify-between items-center mb-4">
    <h2>Danh sách sinh viên</h2>
    <?php if (is_admin()): ?>
    <a href="<?php echo url('/students/create'); ?>" class="btn btn-primary">+ Thêm sinh viên</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="d-flex justify-between items-center mb-3">
        <form method="GET" action="<?php echo url('/students'); ?>" class="d-flex gap-2" style="width: 300px;">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, MSSV, email..." 
                   value="<?php echo $_GET['search'] ?? ''; ?>">
            <button type="submit" class="btn btn-primary">Tìm</button>
        </form>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>MSSV</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Khoa</th>
                <th>Lớp</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($students)): ?>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td><strong><?php echo $s->student_code ?: 'N/A'; ?></strong></td>
                    <td><?php echo $s->full_name; ?></td>
                    <td><?php echo $s->email ?: 'N/A'; ?></td>
                    <td><?php echo $s->phone ?: 'N/A'; ?></td>
                    <td><?php echo $s->faculty ?: 'N/A'; ?></td>
                    <td><?php echo $s->class ?: 'N/A'; ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?php echo url('/students/'.$s->id); ?>" class="btn btn-sm">Xem</a>
                            <?php if (is_admin()): ?>
                            <a href="<?php echo url('/students/'.$s->id.'/edit'); ?>" class="btn btn-sm">Sửa</a>
                            <a href="<?php echo url('/students/'.$s->id.'/delete'); ?>" 
                               onclick="return confirm('Xóa sinh viên này sẽ xóa tất cả dữ liệu liên quan?')" 
                               class="btn btn-sm text-danger">Xóa</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Chưa có sinh viên nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>