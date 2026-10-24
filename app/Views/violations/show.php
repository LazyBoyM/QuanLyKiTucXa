<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/violations'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Chi tiết vi phạm</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <h4>Thông tin vi phạm</h4>
            <table class="table">
                <tr>
                    <th width="150">Mã vi phạm:</th>
                    <td><strong><?php echo $violation->violation_number; ?></strong></td>
                </tr>
                <tr>
                    <th>Tiêu đề:</th>
                    <td><?php echo $violation->title; ?></td>
                </tr>
                <tr>
                    <th>Mô tả:</th>
                    <td><?php echo nl2br($violation->description); ?></td>
                </tr>
                <tr>
                    <th>Hình phạt:</th>
                    <td><?php echo $violation->penalty ?: 'Chưa có'; ?></td>
                </tr>
                <tr>
                    <th>Tiền phạt:</th>
                    <td>
                        <?php if ($violation->penalty_amount > 0): ?>
                        <strong style="color: var(--apple-red);"><?php echo number_format($violation->penalty_amount); ?>đ</strong>
                        <?php else: ?>
                        0đ
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Trạng thái:</th>
                    <td>
                        <span class="badge badge-<?php echo $violation->status == 'Đã xử lý' ? 'success' : 'warning'; ?>">
                            <?php echo $violation->status; ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Ngày tạo:</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($violation->created_at)); ?></td>
                </tr>
                <tr>
                    <th>Người báo cáo:</th>
                    <td><?php echo $violation->reporter_name ?? 'N/A'; ?></td>
                </tr>
            </table>
            
            <?php if (is_admin() && $violation->status == 'Chờ xử lý'): ?>
            <div class="mt-4">
                <a href="<?php echo url('/violations/'.$violation->id.'/process'); ?>" 
                   class="btn btn-success w-full" onclick="return confirm('Xác nhận đã xử lý vi phạm này?')">
                    Đánh dấu đã xử lý
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($violation->penalty_amount > 0 && $violation->invoice_id): ?>
        <div class="card mt-4">
            <h4>Tiền phạt</h4>
            <table class="table">
                <tr>
                    <th width="150">Số tiền phạt:</th>
                    <td><strong style="color: var(--apple-red);"><?php echo number_format($violation->penalty_amount); ?>đ</strong></td>
                </tr>
                <tr>
                    <th>Trạng thái thanh toán:</th>
                    <td>
                        <?php 
                        $invoice = $this->db->fetch("SELECT * FROM invoices WHERE id = :id", [':id' => $violation->invoice_id]);
                        if ($invoice): 
                        ?>
                        <span class="badge badge-<?php echo $invoice->status == 'Đã thanh toán' ? 'success' : 'warning'; ?>">
                            <?php echo $invoice->status; ?>
                        </span>
                        <?php if ($invoice->status != 'Đã thanh toán'): ?>
                        <a href="<?php echo url('/payments/create?invoice='.$invoice->id); ?>" class="btn btn-sm btn-primary" style="margin-left: 12px;">Thanh toán ngay</a>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="badge badge-warning">Chưa có hóa đơn</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <h4>Thông tin sinh viên</h4>
            <table class="table">
                <tr>
                    <th width="150">Họ tên:</th>
                    <td><?php echo $violation->full_name ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>MSSV:</th>
                    <td><?php echo $violation->student_code ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Số điện thoại:</th>
                    <td><?php echo $violation->phone ?? 'N/A'; ?></td>
                </tr>
            </table>
        </div>
        
        <?php if ($violation->rule_title): ?>
        <div class="card mt-4">
            <h4>Nội quy liên quan</h4>
            <table class="table">
                <tr>
                    <th width="150">Tiêu đề:</th>
                    <td><?php echo $violation->rule_title; ?></td>
                </tr>
                <tr>
                    <th>Nội dung:</th>
                    <td><?php echo nl2br($violation->rule_content); ?></td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>