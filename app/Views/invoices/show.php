
<div class="d-flex justify-between items-center mb-4">
    <h2>Chi tiết hóa đơn</h2>
    <div>
        <a href="<?php echo url('/invoices'); ?>" class="btn">Quay lại</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <h4 style="color: var(--apple-dark); margin-bottom: 16px;">Thông tin hóa đơn</h4>
            <table class="table" style="color: var(--apple-dark);">
                <tr>
                    <th width="150" style="color: var(--apple-gray);">Mã hóa đơn:</th>
                    <td><strong style="color: var(--apple-dark);"><?php echo $invoice->invoice_number; ?></strong></td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Tháng/Năm:</th>
                    <td style="color: var(--apple-dark);"><?php echo $invoice->month; ?>/<?php echo $invoice->year; ?></td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Ngày tạo:</th>
                    <td style="color: var(--apple-dark);"><?php echo isset($invoice->created_at) ? date('d/m/Y H:i', strtotime($invoice->created_at)) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Hạn thanh toán:</th>
                    <td style="color: var(--apple-dark);"><?php echo date('d/m/Y', strtotime($invoice->due_date)); ?></td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Trạng thái:</th>
                    <td>
                        <span class="badge badge-<?php 
                            echo $invoice->status == 'Đã thanh toán' ? 'success' : 
                                 ($invoice->status == 'Quá hạn' ? 'danger' : 'warning'); 
                        ?>">
                            <?php echo $invoice->status; ?>
                        </span>
                    </td>
                </tr>
                <?php if ($invoice->status == 'Đã thanh toán'): ?>
                <tr>
                    <th style="color: var(--apple-gray);">Ngày thanh toán:</th>
                    <td style="color: var(--apple-dark);"><?php echo isset($invoice->paid_at) ? date('d/m/Y H:i', strtotime($invoice->paid_at)) : 'N/A'; ?></td>
                </tr>
                <?php endif; ?>
            </table>
            
            <?php if ($invoice->status != 'Đã thanh toán'): ?>
            <div class="mt-4">
                <a href="<?php echo url('/payments/create?invoice='.$invoice->id); ?>" class="btn btn-primary w-full">
                    Thanh toán ngay
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <h4 style="color: var(--apple-dark); margin-bottom: 16px;">Thông tin sinh viên</h4>
            <table class="table" style="color: var(--apple-dark);">
                <tr>
                    <th width="150" style="color: var(--apple-gray);">Họ và tên:</th>
                    <td style="color: var(--apple-dark);"><?php echo $student->full_name ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">MSSV:</th>
                    <td style="color: var(--apple-dark);"><?php echo $student->student_code ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Email:</th>
                    <td style="color: var(--apple-dark);"><?php echo $student->email ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Số điện thoại:</th>
                    <td style="color: var(--apple-dark);"><?php echo $student->phone ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Phòng:</th>
                    <td style="color: var(--apple-dark);"><?php echo $room->room_number ?? 'N/A'; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="card mt-4">
            <h4 style="color: var(--apple-dark); margin-bottom: 16px;">Chi tiết chi phí</h4>
            <table class="table" style="color: var(--apple-dark);">
                <tr>
                    <th style="color: var(--apple-gray);">Tiền phòng:</th>
                    <td class="text-right" style="color: var(--apple-dark);"><?php echo number_format($invoice->room_fee ?? 0); ?>đ</td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Tiền điện (<?php echo $invoice->electricity_usage ?? 0; ?> kWh):</th>
                    <td class="text-right" style="color: var(--apple-dark);"><?php echo number_format($invoice->electricity_fee ?? 0); ?>đ</td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Tiền nước (<?php echo $invoice->water_usage ?? 0; ?> m³):</th>
                    <td class="text-right" style="color: var(--apple-dark);"><?php echo number_format($invoice->water_fee ?? 0); ?>đ</td>
                </tr>
                <tr>
                    <th style="color: var(--apple-gray);">Phí internet:</th>
                    <td class="text-right" style="color: var(--apple-dark);"><?php echo number_format($invoice->internet_fee ?? 0); ?>đ</td>
                </tr>
                <?php if (($invoice->other_fees ?? 0) > 0): ?>
                <tr>
                    <th style="color: var(--apple-gray);">Phí khác:</th>
                    <td class="text-right" style="color: var(--apple-dark);"><?php echo number_format($invoice->other_fees); ?>đ</td>
                </tr>
                <?php endif; ?>
                <tr style="border-top: 2px solid var(--apple-border);">
                    <th style="color: var(--apple-dark);"><strong>Tổng cộng:</strong></th>
                    <td class="text-right"><strong style="font-size: 18px; color: var(--apple-dark);"><?php echo number_format($invoice->total_amount); ?>đ</strong></td>
                </tr>
            </table>
            
            <div class="alert alert-info mt-3" style="color: var(--apple-dark);">
                <strong>Đơn giá:</strong><br>
                - Giá điện: <?php echo number_format($electricity_rate); ?>đ/kWh<br>
                - Giá nước: <?php echo number_format($water_rate); ?>đ/m³<br>
                - Phí internet: <?php echo number_format($internet_fee); ?>đ/tháng
            </div>
        </div>
    </div>
</div>