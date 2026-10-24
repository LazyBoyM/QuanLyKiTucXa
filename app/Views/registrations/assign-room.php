<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/registrations'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Xếp phòng cho sinh viên</h2>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <h4>Thông tin sinh viên</h4>
            <p><strong><?php echo $student->full_name ?? 'N/A'; ?></strong></p>
            <p class="text-muted">MSSV: <?php echo $student->student_code ?? 'N/A'; ?></p>
            <p class="text-muted">Giới tính: <?php echo $student->gender ?? 'N/A'; ?></p>
            <p class="text-muted">Loại phòng mong muốn: <?php echo $registration->desired_room_type ?? 'N/A'; ?> người</p>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <h4>Chọn phòng và giường</h4>
            
            <form method="POST" action="<?php echo url('/registrations/'.$registration->id.'/assign-room'); ?>">
                <div class="form-group">
                    <label class="form-label">Tòa nhà</label>
                    <select name="building_id" id="buildingSelect" class="form-control" required>
                        <option value="">Chọn tòa</option>
                        <?php foreach ($buildings as $b): ?>
                        <option value="<?php echo $b->id; ?>">
                            <?php echo $b->name; ?> (<?php echo $b->gender; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phòng</label>
                    <select name="room_id" id="roomSelect" class="form-control" required>
                        <option value="">Chọn tòa trước</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Giường</label>
                    <select name="bed_id" id="bedSelect" class="form-control" required>
                        <option value="">Chọn phòng trước</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Ngày vào ở</label>
                    <input type="date" name="check_in_date" class="form-control" required 
                           value="<?php echo $registration->expected_check_in ?? date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tiền đặt cọc</label>
                    <input type="number" name="deposit" class="form-control" 
                           value="<?php echo $default_deposit ?? 500000; ?>">
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Xác nhận xếp phòng</button>
                    <a href="<?php echo url('/registrations'); ?>" class="btn">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('buildingSelect').addEventListener('change', function() {
    var buildingId = this.value;
    var roomSelect = document.getElementById('roomSelect');
    var bedSelect = document.getElementById('bedSelect');
    
    roomSelect.innerHTML = '<option value="">Đang tải...</option>';
    bedSelect.innerHTML = '<option value="">Chọn phòng trước</option>';
    
    if (!buildingId) {
        roomSelect.innerHTML = '<option value="">Chọn tòa trước</option>';
        return;
    }
    
    fetch('<?php echo url('/api/rooms-by-building'); ?>?building_id=' + buildingId)
        .then(res => res.json())
        .then(rooms => {
            roomSelect.innerHTML = '<option value="">Chọn phòng</option>';
            rooms.forEach(function(room) {
                if (room.available_beds > 0) {
                    roomSelect.innerHTML += '<option value="' + room.id + '">' +
                        'Phòng ' + room.room_number + ' - Còn ' + room.available_beds + ' giường - ' +
                        new Intl.NumberFormat().format(room.price) + 'đ/tháng</option>';
                }
            });
        })
        .catch(err => {
            roomSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
        });
});

document.getElementById('roomSelect').addEventListener('change', function() {
    var roomId = this.value;
    var bedSelect = document.getElementById('bedSelect');
    
    bedSelect.innerHTML = '<option value="">Đang tải...</option>';
    
    if (!roomId) {
        bedSelect.innerHTML = '<option value="">Chọn phòng trước</option>';
        return;
    }
    
    fetch('<?php echo url('/api/available-beds'); ?>?room_id=' + roomId)
        .then(res => res.json())
        .then(beds => {
            bedSelect.innerHTML = '<option value="">Chọn giường</option>';
            beds.forEach(function(bed) {
                bedSelect.innerHTML += '<option value="' + bed.id + '">Giường ' + bed.bed_number + '</option>';
            });
        })
        .catch(err => {
            bedSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
        });
});
</script>