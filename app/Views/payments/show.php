<div class="d-flex justify-between items-center mb-4">
    <h2>Chi tiết thanh toán</h2>
    <a href="<?php echo url('/payments'); ?>" class="btn">Quay lại</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <h4>Thông tin thanh toán</h4>
            <table class="table">
                <tr>
                    <th width="150">Mã thanh toán:</th>
                    <td><strong><?php echo $payment->payment_number; ?></strong></td>
                </tr>
                <tr>
                    <th>Số tiền:</th>
                    <td><strong><?php echo number_format($payment->amount); ?>đ</strong></td>
                </tr>
                <tr>
                    <th>Phương thức:</th>
                    <td><?php echo $payment->payment_method; ?></td>
                </tr>
                <tr>
                    <th>Trạng thái:</th>
                    <td>
                        <span class="badge badge-<?php 
                            echo $payment->status == 'success' ? 'success' : 
                                 ($payment->status == 'pending' ? 'warning' : 'danger'); 
                        ?>">
                            <?php 
                            if ($payment->status == 'success') echo 'Thành công';
                            elseif ($payment->status == 'pending') echo 'Đang xử lý';
                            else echo 'Thất bại';
                            ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Ngày tạo:</th>
                    <td><?php echo isset($payment->created_at) ? date('d/m/Y H:i', strtotime($payment->created_at)) : 'N/A'; ?></td>
                </tr>
                <?php if ($payment->payment_date): ?>
                <tr>
                    <th>Ngày thanh toán:</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($payment->payment_date)); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($payment->transaction_id): ?>
                <tr>
                    <th>Mã giao dịch:</th>
                    <td><?php echo $payment->transaction_id; ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <?php if ($invoice): ?>
        <div class="card mt-4">
            <h4>Thông tin hóa đơn</h4>
            <table class="table">
                <tr>
                    <th width="150">Mã hóa đơn:</th>
                    <td><?php echo $invoice->invoice_number; ?></td>
                </tr>
                <tr>
                    <th>Tháng/Năm:</th>
                    <td><?php echo $invoice->month; ?>/<?php echo $invoice->year; ?></td>
                </tr>
                <tr>
                    <th>Tổng tiền:</th>
                    <td><?php echo number_format($invoice->total_amount); ?>đ</td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-6">
        <?php if ($student): ?>
        <div class="card">
            <h4>Thông tin sinh viên</h4>
            <table class="table">
                <tr><th width="150">Họ tên:</th><td><?php echo $student->full_name; ?></td></tr>
                <tr><th>MSSV:</th><td><?php echo $student->student_code; ?></td></tr>
                <tr><th>Email:</th><td><?php echo $student->email; ?></td></tr>
                <tr><th>Số điện thoại:</th><td><?php echo $student->phone; ?></td></tr>
            </table>
        </div>
        <?php endif; ?>
        
        <?php if ($payment->status == 'success'): ?>
        <div class="card mt-4 text-center">
            <div style="font-size: 48px; color: var(--apple-green); margin-bottom: 12px;">✓</div>
            <h4 style="color: var(--apple-green);">Thanh toán thành công</h4>
            <p class="text-muted">Cảm ơn bạn đã thanh toán!</p>
        </div>
        <?php elseif ($payment->status == 'pending' && $payment->payment_method == 'MoMo'): ?>
        <div class="card mt-4 text-center">
            <a href="<?php echo url('/payments/'.$payment->id.'/momo'); ?>" class="btn btn-primary w-full">
                Tiếp tục thanh toán MoMo
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>