<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/payments'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Thanh toán MoMo</h2>
</div>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h4 style="margin-bottom: 20px;">Thông tin thanh toán</h4>
    
    <table class="table">
        <tr>
            <th width="150">Mã thanh toán:</th>
            <td><strong><?php echo $payment->payment_number; ?></strong></td>
        </tr>
        <tr>
            <th>Hóa đơn:</th>
            <td><?php echo $payment->invoice_number ?? 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Số tiền:</th>
            <td><strong style="font-size: 18px;"><?php echo number_format($payment->amount); ?>đ</strong></td>
        </tr>
        <tr>
            <th>Người thanh toán:</th>
            <td><?php echo auth()['full_name']; ?></td>
        </tr>
        <tr>
            <th>Trạng thái:</th>
            <td><span class="badge badge-warning">Chờ thanh toán</span></td>
        </tr>
    </table>
    
    <div class="alert alert-info mt-4">
        <strong>Demo Sandbox:</strong><br>
        SĐT đăng nhập: 0909113911<br>
        OTP: 000000<br>
        Mật khẩu: 123456
    </div>
    
    <div class="alert alert-warning mt-3">
        Sau khi thanh toán, MoMo sẽ tự động chuyển về trang kết quả.
    </div>
    
    <a href="<?php echo $momoData['payUrl']; ?>" class="btn btn-primary w-full" target="_blank">
        Mở App MoMo để thanh toán
    </a>
    
    <p class="text-muted text-center mt-3" style="font-size: 13px;">
        Nút trên sẽ mở ứng dụng MoMo. Nếu không được, hãy quét mã QR trong app.
    </p>
</div>