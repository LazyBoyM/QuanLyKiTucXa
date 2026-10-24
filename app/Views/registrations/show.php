<div class="d-flex justify-between items-center mb-4">
    <h2>Chi tiết đơn đăng ký</h2>
    <a href="<?php echo url('/registrations'); ?>" class="btn">Quay lại</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <h4><?php echo $registration->registration_number; ?></h4>
            <span class="badge badge-<?php 
                echo $registration->status == 'Đã xếp phòng' ? 'success' : 
                     ($registration->status == 'Đã duyệt' ? 'info' : 
                     ($registration->status == 'Chờ duyệt' ? 'warning' : 'danger')); 
            ?>"><?php echo $registration->status; ?></span>
            
            <?php if (is_admin() && $registration->status == 'Chờ duyệt'): ?>
            <div class="d-flex gap-2 mt-4">
                <a href="<?php echo url('/registrations/'.$registration->id.'/approve'); ?>" class="btn btn-success w-full">Duyệt đơn</a>
                <a href="<?php echo url('/registrations/'.$registration->id.'/reject'); ?>" class="btn btn-danger w-full">Từ chối</a>
            </div>
            <?php endif; ?>
            
            <?php if (is_admin() && $registration->status == 'Đã duyệt'): ?>
            <a href="<?php echo url('/registrations/'.$registration->id.'/assign-room'); ?>" class="btn btn-primary w-full mt-4">Xếp phòng</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <h4>Thông tin sinh viên</h4>
            <table class="table">
                <tr><th width="150">Họ và tên:</th><td><?php echo $student->full_name ?? 'N/A'; ?></td></tr>
                <tr><th>MSSV:</th><td><?php echo $student->student_code ?? 'N/A'; ?></td></tr>
                <tr><th>Email:</th><td><?php echo $student->email ?? 'N/A'; ?></td></tr>
                <tr><th>Số điện thoại:</th><td><?php echo $student->phone ?? 'N/A'; ?></td></tr>
                <tr><th>Khoa:</th><td><?php echo $student->faculty ?? 'N/A'; ?></td></tr>
                <tr><th>Lớp:</th><td><?php echo $student->class ?? 'N/A'; ?></td></tr>
                <tr><th>Khóa:</th><td><?php echo $student->academic_year ?? 'N/A'; ?></td></tr>
            </table>
        </div>
        
        <div class="card mt-4">
            <h4>Thông tin đăng ký</h4>
            <table class="table">
                <tr><th width="150">Học kỳ:</th><td><?php echo $registration->semester ?? 'N/A'; ?></td></tr>
                <tr><th>Năm học:</th><td><?php echo $registration->academic_year ?? 'N/A'; ?></td></tr>
                <tr><th>Tòa mong muốn:</th><td><?php echo $registration->building_name ?? 'Không yêu cầu'; ?></td></tr>
                <tr><th>Loại phòng:</th><td><?php echo $registration->desired_room_type ?? 'N/A'; ?> người/phòng</td></tr>
                <tr><th>Ngày đăng ký:</th><td><?php echo isset($registration->registration_date) ? date('d/m/Y', strtotime($registration->registration_date)) : 'N/A'; ?></td></tr>
                <tr><th>Ngày dự kiến vào:</th><td><?php echo isset($registration->expected_check_in) ? date('d/m/Y', strtotime($registration->expected_check_in)) : 'N/A'; ?></td></tr>
                <?php if (!empty($registration->notes)): ?>
                <tr><th>Ghi chú:</th><td><?php echo $registration->notes; ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
        
        <?php if ($contract): ?>
        <div class="card mt-4">
            <h4>Hợp đồng đã tạo</h4>
            <table class="table">
                <tr><th width="150">Số hợp đồng:</th><td><?php echo $contract->contract_number; ?></td></tr>
                <tr><th>Phòng:</th><td><?php echo $contract->room_number; ?></td></tr>
                <tr><th>Giường:</th><td><?php echo $contract->bed_number; ?></td></tr>
                <tr><th>Ngày vào:</th><td><?php echo date('d/m/Y', strtotime($contract->check_in_date)); ?></td></tr>
                <tr><th>Tiền phòng/tháng:</th><td><?php echo number_format($contract->monthly_rent); ?>đ</td></tr>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>