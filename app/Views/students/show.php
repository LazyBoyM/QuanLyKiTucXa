<div class="d-flex justify-between items-center mb-4">
    <h2>Chi tiết sinh viên</h2>
    <div>
        <?php if (is_admin()): ?>
        <a href="<?php echo url('/students/'.$student->id.'/edit'); ?>" class="btn">Sửa</a>
        <?php endif; ?>
        <a href="<?php echo url('/students'); ?>" class="btn">Quay lại</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div style="font-size: 64px; margin-bottom: 16px;">👤</div>
            <h3><?php echo $student->full_name; ?></h3>
            <p class="text-muted">MSSV: <?php echo $student->student_code ?: 'Chưa có'; ?></p>
            <span class="badge badge-<?php echo $student->status == 'active' ? 'success' : 'warning'; ?>">
                <?php echo $student->status == 'active' ? 'Đang học' : 'Chưa kích hoạt'; ?>
            </span>
        </div>
        
        <?php if ($contract): ?>
        <div class="card mt-4">
            <h4>Phòng hiện tại</h4>
            <p><strong>Phòng:</strong> <?php echo $contract->room_number; ?></p>
            <p><strong>Tòa:</strong> <?php echo $contract->building_name; ?></p>
            <p><strong>Giường:</strong> <?php echo $contract->bed_number; ?></p>
            <p><strong>Ngày vào:</strong> <?php echo date('d/m/Y', strtotime($contract->check_in_date)); ?></p>
            <p><strong>Tiền phòng/tháng:</strong> <?php echo number_format($contract->monthly_rent); ?>đ</p>
        </div>
        <?php else: ?>
        <div class="card mt-4">
            <p class="text-muted text-center py-3">Chưa có hợp đồng</p>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <h4>Thông tin cá nhân</h4>
            <table class="table">
                <tr>
                    <th width="150">Mã sinh viên:</th>
                    <td><strong><?php echo $student->student_code ?: 'Chưa cập nhật'; ?></strong></td>
                </tr>
                <tr>
                    <th>Họ và tên:</th>
                    <td><?php echo $student->full_name; ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo $student->email ?: 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>Số điện thoại:</th>
                    <td><?php echo $student->phone ?: 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>Ngày sinh:</th>
                    <td><?php echo $student->date_of_birth ? date('d/m/Y', strtotime($student->date_of_birth)) : 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>Giới tính:</th>
                    <td><?php echo $student->gender ?: 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>Khoa:</th>
                    <td><?php echo $student->faculty ?: 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>Lớp:</th>
                    <td><?php echo $student->class ?: 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>Khóa học:</th>
                    <td><?php echo $student->academic_year ?: 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>Địa chỉ thường trú:</th>
                    <td><?php echo $student->permanent_address ?: 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>CMND/CCCD:</th>
                    <td><?php echo $student->id_card ?: 'Chưa cập nhật'; ?></td>
                </tr>
                <tr>
                    <th>Ngày tạo:</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($student->created_at)); ?></td>
                </tr>
            </table>
        </div>
        
        <div class="card mt-4">
            <div class="d-flex justify-between items-center mb-3">
                <h4>Đơn đăng ký KTX</h4>
                <a href="<?php echo url('/registrations?student='.$student->id); ?>" class="btn btn-sm">Xem tất cả</a>
            </div>
            <?php
            $registrations = $this->db->fetchAll("
                SELECT * FROM registrations 
                WHERE student_id = :student_id 
                ORDER BY created_at DESC LIMIT 3
            ", [':student_id' => $student->id]);
            ?>
            <?php if (!empty($registrations)): ?>
                <?php foreach ($registrations as $reg): ?>
                <div class="mb-3 pb-3" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div class="d-flex justify-between items-center">
                        <div>
                            <strong><?php echo $reg->registration_number; ?></strong>
                            <span class="badge badge-<?php 
                                echo $reg->status == 'Đã xếp phòng' ? 'success' : 
                                     ($reg->status == 'Đã duyệt' ? 'info' : 'warning'); 
                            ?>"><?php echo $reg->status; ?></span>
                        </div>
                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($reg->registration_date)); ?></small>
                    </div>
                    <p class="text-muted" style="margin-top: 4px; font-size: 13px;">
                        Học kỳ <?php echo $reg->semester; ?> - Năm học <?php echo $reg->academic_year; ?>
                    </p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted text-center py-2">Chưa có đơn đăng ký</p>
            <?php endif; ?>
        </div>
        
        <div class="card mt-4">
            <div class="d-flex justify-between items-center mb-3">
                <h4>Hóa đơn gần đây</h4>
                <a href="<?php echo url('/invoices?student='.$student->id); ?>" class="btn btn-sm">Xem tất cả</a>
            </div>
            <?php
            $invoices = $this->db->fetchAll("
                SELECT * FROM invoices 
                WHERE student_id = :student_id 
                ORDER BY year DESC, month DESC LIMIT 3
            ", [':student_id' => $student->id]);
            ?>
            <?php if (!empty($invoices)): ?>
                <?php foreach ($invoices as $inv): ?>
                <div class="d-flex justify-between items-center mb-3 pb-3" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div>
                        <strong>Tháng <?php echo $inv->month; ?>/<?php echo $inv->year; ?></strong>
                        <div class="text-muted" style="font-size: 12px;"><?php echo $inv->invoice_number; ?></div>
                    </div>
                    <div class="text-right">
                        <strong><?php echo number_format($inv->total_amount); ?>đ</strong>
                        <div>
                            <span class="badge badge-<?php echo $inv->status == 'Đã thanh toán' ? 'success' : 'warning'; ?>">
                                <?php echo $inv->status; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted text-center py-2">Chưa có hóa đơn</p>
            <?php endif; ?>
        </div>
    </div>
</div>