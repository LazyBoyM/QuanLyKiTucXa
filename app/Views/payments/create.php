<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/payments'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Thanh toán hóa đơn</h2>
</div>

<div class="card" style="max-width: 700px;">
    <?php if ($invoice): ?>
    <!-- Thanh toán hóa đơn cụ thể -->
    <div class="alert alert-info mb-4">
        <strong>Hóa đơn:</strong> <?php echo $invoice->invoice_number; ?><br>
        <strong>Tháng/Năm:</strong> <?php echo $invoice->month; ?>/<?php echo $invoice->year; ?><br>
        <strong>Số tiền:</strong> <?php echo number_format($invoice->total_amount); ?>đ
    </div>
    
    <form method="POST" action="<?php echo url('/payments'); ?>">
        <input type="hidden" name="invoice_id" value="<?php echo $invoice->id; ?>">
        
        <div class="form-group">
            <label class="form-label">Số tiền thanh toán</label>
            <input type="number" name="amount" class="form-control" value="<?php echo $invoice->total_amount; ?>" readonly>
        </div>
        
        <div class="form-group">
            <label class="form-label">Phương thức thanh toán</label>
            <div class="d-flex gap-3">
                <label class="d-flex items-center gap-2">
                    <input type="radio" name="payment_method" value="Tiền mặt" checked>
                    <span>Tiền mặt</span>
                </label>
                <label class="d-flex items-center gap-2">
                    <input type="radio" name="payment_method" value="MoMo">
                    <span>MoMo QR</span>
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Ghi chú</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú (nếu có)"></textarea>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
            <a href="<?php echo url('/payments'); ?>" class="btn">Hủy</a>
        </div>
    </form>
    
    <?php elseif (!empty($unpaidInvoices)): ?>
    <!-- Chọn hóa đơn để thanh toán -->
    <form method="POST" action="<?php echo url('/payments'); ?>">
        <div class="form-group">
            <label class="form-label">Chọn hóa đơn cần thanh toán</label>
            <select name="invoice_id" id="invoiceSelect" class="form-control" required>
                <option value="">-- Chọn hóa đơn --</option>
                <?php foreach ($unpaidInvoices as $inv): ?>
                <option value="<?php echo $inv->id; ?>" data-amount="<?php echo $inv->total_amount; ?>">
                    Tháng <?php echo $inv->month; ?>/<?php echo $inv->year; ?> - 
                    <?php echo number_format($inv->total_amount); ?>đ
                    <?php if ($inv->status == 'Quá hạn'): ?>(Quá hạn)<?php endif; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Số tiền</label>
            <input type="number" name="amount" id="amountInput" class="form-control" readonly>
        </div>
        
        <div class="form-group">
            <label class="form-label">Phương thức thanh toán</label>
            <div class="d-flex gap-3">
                <label class="d-flex items-center gap-2">
                    <input type="radio" name="payment_method" value="Tiền mặt" checked>
                    <span>Tiền mặt</span>
                </label>
                <label class="d-flex items-center gap-2">
                    <input type="radio" name="payment_method" value="MoMo">
                    <span>MoMo QR</span>
                </label>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Tạo thanh toán</button>
            <a href="<?php echo url('/payments'); ?>" class="btn">Hủy</a>
        </div>
    </form>
    
    <script>
    document.getElementById('invoiceSelect').addEventListener('change', function() {
        var selected = this.options[this.selectedIndex];
        var amount = selected.dataset.amount || '';
        document.getElementById('amountInput').value = amount;
    });
    </script>
    
    <?php else: ?>
    <div class="text-center py-4">
        <p class="text-muted mb-4">Bạn không có hóa đơn nào cần thanh toán</p>
        <a href="<?php echo url('/payments'); ?>" class="btn btn-primary">Quay lại</a>
    </div>
    <?php endif; ?>
</div>