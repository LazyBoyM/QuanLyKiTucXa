<div class="d-flex justify-between items-center mb-4">
    <h2>Chi tiết hợp đồng</h2>
    <div>
        <a href="<?php echo url('/contracts/'.$contract->id.'/print'); ?>" class="btn">In hợp đồng</a>
        <a href="<?php echo url('/contracts'); ?>" class="btn">Quay lại</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <h4>Thông tin hợp đồng</h4>
            <table class="table">
                <tr><th width="150">Số hợp đồng:</th><td><strong><?php echo $contract->contract_number ?? 'N/A'; ?></strong></td></tr>
                <tr><th>Trạng thái:</th><td><span class="badge badge-<?php echo ($contract->status ?? '') == 'Hiệu lực' ? 'success' : 'warning'; ?>"><?php echo $contract->status ?? 'N/A'; ?></span></td></tr>
                <tr><th>Ngày vào ở:</th><td><?php echo isset($contract->check_in_date) ? date('d/m/Y', strtotime($contract->check_in_date)) : 'N/A'; ?></td></tr>
                <tr><th>Ngày kết thúc:</th><td><?php echo isset($contract->check_out_date) ? date('d/m/Y', strtotime($contract->check_out_date)) : 'Theo học kỳ'; ?></td></tr>
                <tr><th>Tiền phòng/tháng:</th><td><?php echo number_format($contract->monthly_rent ?? 0); ?>đ</td></tr>
                <tr><th>Tiền đặt cọc:</th><td><?php echo number_format($contract->deposit ?? 0); ?>đ</td></tr>
                <tr><th>Ngày tạo:</th><td><?php echo isset($contract->created_at) ? date('d/m/Y H:i', strtotime($contract->created_at)) : 'N/A'; ?></td></tr>
            </table>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <h4>Thông tin phòng</h4>
            <table class="table">
                <tr><th width="150">Tòa nhà:</th><td><?php echo $room->building_name ?? 'N/A'; ?></td></tr>
                <tr><th>Phòng:</th><td><strong><?php echo $room->room_number ?? 'N/A'; ?></strong></td></tr>
                <tr><th>Tầng:</th><td><?php echo $room->floor ?? 'N/A'; ?></td></tr>
                <tr><th>Giường:</th><td><?php echo $contract->bed_number ?? 'N/A'; ?></td></tr>
                <tr><th>Loại phòng:</th><td><?php echo $room->room_type ?? 'N/A'; ?> người</td></tr>
                <tr><th>Số người hiện tại:</th><td><?php echo $room->current_occupancy ?? 0; ?>/<?php echo $room->capacity ?? 0; ?></td></tr>
            </table>
        </div>
        
        <div class="card mt-4">
            <h4>Thông tin sinh viên</h4>
            <table class="table">
                <tr><th width="150">Họ và tên:</th><td><?php echo $student->full_name ?? 'N/A'; ?></td></tr>
                <tr><th>MSSV:</th><td><?php echo $student->student_code ?? 'N/A'; ?></td></tr>
                <tr><th>Ngày sinh:</th><td><?php echo isset($student->date_of_birth) && $student->date_of_birth ? date('d/m/Y', strtotime($student->date_of_birth)) : 'N/A'; ?></td></tr>
                <tr><th>Giới tính:</th><td><?php echo $student->gender ?? 'N/A'; ?></td></tr>
                <tr><th>Email:</th><td><?php echo $student->email ?? 'N/A'; ?></td></tr>
                <tr><th>Số điện thoại:</th><td><?php echo $student->phone ?? 'N/A'; ?></td></tr>
                <tr><th>Khoa/Lớp:</th><td><?php echo ($student->faculty ?? 'N/A') . ' - ' . ($student->class ?? 'N/A'); ?></td></tr>
            </table>
        </div>
    </div>
</div>