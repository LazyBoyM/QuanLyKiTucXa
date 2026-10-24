<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/complaints'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Phản hồi góp ý</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <h4>Thông tin góp ý</h4>
            <table class="table">
                <tr>
                    <th width="150">Mã góp ý:</th>
                    <td><strong><?php echo $complaint->complaint_number; ?></strong></td>
                </tr>
                <tr>
                    <th>Tiêu đề:</th>
                    <td><?php echo $complaint->title; ?></td>
                </tr>
                <tr>
                    <th>Danh mục:</th>
                    <td><?php echo $complaint->category; ?></td>
                </tr>
                <tr>
                    <th>Trạng thái:</th>
                    <td>
                        <span class="badge badge-<?php echo $complaint->status == 'Chờ xử lý' ? 'warning' : 'info'; ?>">
                            <?php echo $complaint->status; ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Ngày gửi:</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($complaint->created_at)); ?></td>
                </tr>
            </table>
            
            <div class="mt-4">
                <h4>Nội dung</h4>
                <div style="background: var(--apple-light); padding: 16px; border-radius: var(--radius-md);">
                    <?php echo nl2br($complaint->description); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <h4>Phản hồi</h4>
            
            <form method="POST" action="<?php echo url('/complaints/'.$complaint->id.'/respond'); ?>">
                <div class="form-group">
                    <label class="form-label">Nội dung phản hồi</label>
                    <textarea name="response" class="form-control" rows="6" required placeholder="Nhập nội dung phản hồi..."></textarea>
                </div>
                
                <div class="form-group">
                    <label class="d-flex items-center gap-2">
                        <input type="checkbox" name="resolve" value="1">
                        <span>Đánh dấu đã giải quyết</span>
                    </label>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                    <a href="<?php echo url('/complaints'); ?>" class="btn">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>