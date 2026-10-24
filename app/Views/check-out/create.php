<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/check-out'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Yêu cầu trả phòng</h2>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <h4 style="margin-bottom: 20px;">Thông tin yêu cầu</h4>
            
            <form method="POST" action="<?php echo url('/check-out'); ?>">
                <div class="form-group">
                    <label class="form-label">Ngày dự kiến trả phòng <span style="color: var(--apple-red);">*</span></label>
                    <input type="date" name="request_date" class="form-control" required 
                           value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>"
                           min="<?php echo date('Y-m-d'); ?>">
                    <small class="text-muted" style="display: block; margin-top: 4px;">Chọn ngày bạn dự kiến rời khỏi KTX</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Lý do trả phòng <span style="color: var(--apple-red);">*</span></label>
                    <select name="reason" class="form-control" required>
                        <option value="">-- Chọn lý do --</option>
                        <option value="Kết thúc học kỳ">Kết thúc học kỳ</option>
                        <option value="Chuyển phòng">Chuyển phòng khác</option>
                        <option value="Ra trường">Tốt nghiệp / Ra trường</option>
                        <option value="Nghỉ học">Nghỉ học / Bảo lưu</option>
                        <option value="Lý do cá nhân">Lý do cá nhân</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Ghi chú thêm</label>
                    <textarea name="notes" class="form-control" rows="4" 
                              placeholder="Nhập ghi chú hoặc yêu cầu đặc biệt (nếu có)..."></textarea>
                </div>
                
                <div class="d-flex gap-2" style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                    <a href="<?php echo url('/check-out'); ?>" class="btn">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card">
            <h4 style="margin-bottom: 16px;">Thông tin phòng</h4>
            
            <div style="background: rgba(0,122,255,0.05); padding: 16px; border-radius: var(--radius-md); margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <span style="font-size: 32px;">🚪</span>
                    <div>
                        <h3 style="margin: 0 0 4px 0; font-size: 20px;">Phòng <?php echo $contract->room_number; ?></h3>
                        <span class="badge badge-success">Đang ở</span>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px;">
                    <div>
                        <div class="text-muted" style="font-size: 12px;">Tiền phòng/tháng</div>
                        <div style="font-weight: 600;"><?php echo number_format($contract->monthly_rent); ?>đ</div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 12px;">Ngày vào ở</div>
                        <div><?php echo date('d/m/Y', strtotime($contract->check_in_date)); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-warning">
                <strong>⚠️ Lưu ý quan trọng:</strong>
                <ul style="margin-top: 12px; margin-left: 20px; line-height: 1.6;">
                    <li>Bạn cần thanh toán hết các hóa đơn còn nợ trước khi trả phòng</li>
                    <li>Tiền đặt cọc sẽ được hoàn trả sau khi kiểm tra phòng</li>
                    <li>Vui lòng dọn dẹp phòng sạch sẽ trước khi trả</li>
                    <li>Thông báo trước ít nhất 7 ngày để được xử lý</li>
                </ul>
            </div>
            
            <div class="alert alert-info">
                <strong>📞 Cần hỗ trợ?</strong><br>
                Liên hệ Ban quản lý KTX:<br>
                📧 <?php echo setting('contact_email', 'admin@ktx.edu.vn'); ?><br>
                📞 <?php echo setting('contact_phone', '0123456789'); ?>
            </div>
        </div>
    </div>
</div>