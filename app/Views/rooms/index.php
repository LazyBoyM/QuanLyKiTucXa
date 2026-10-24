<div class="d-flex justify-between items-center mb-4">
    <h2>Quản lý phòng</h2>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/rooms/floor-plan'); ?>" class="btn">Sơ đồ tầng</a>
        <?php if (is_admin()): ?>
        <a href="<?php echo url('/rooms/create'); ?>" class="btn btn-primary">+ Thêm phòng</a>
        <?php endif; ?>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats->total_rooms ?? 0; ?></div>
            <div class="stat-label">Tổng phòng</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats->available_rooms ?? 0; ?></div>
            <div class="stat-label">Phòng trống</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats->occupied_beds ?? 0; ?>/<?php echo $stats->total_beds ?? 0; ?></div>
            <div class="stat-label">Giường đã thuê</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats->full_rooms ?? 0; ?></div>
            <div class="stat-label">Phòng đã đầy</div>
        </div>
    </div>
</div>

<!-- Lọc theo tòa nhà -->
<div class="card mb-4">
    <div class="d-flex gap-2">
        <a href="<?php echo url('/rooms'); ?>" class="btn btn-sm <?php echo !isset($_GET['building']) ? 'btn-primary' : ''; ?>">Tất cả</a>
        <?php 
        $buildings = $this->db->fetchAll("SELECT * FROM buildings ORDER BY code");
        foreach ($buildings as $b): 
        ?>
        <a href="<?php echo url('/rooms?building='.$b->id); ?>" 
           class="btn btn-sm <?php echo isset($_GET['building']) && $_GET['building'] == $b->id ? 'btn-primary' : ''; ?>">
            <?php echo $b->name; ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Room Grid -->
<div class="room-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
    <?php if (!empty($rooms)): ?>
        <?php foreach ($rooms as $room): 
            // Lấy tên tòa nhà
            $building = $this->db->fetch("SELECT name, code FROM buildings WHERE id = :id", [':id' => $room->building_id]);
        ?>
        <a href="<?php echo url('/rooms/'.$room->id); ?>" style="text-decoration: none; color: inherit;">
            <div class="room-card" style="
                border: 1px solid <?php 
                    echo $room->status == 'Trống' ? '#10b981' : 
                         ($room->status == 'Đã đầy' ? '#ef4444' : 
                         ($room->status == 'Bảo trì' ? '#f59e0b' : '#3b82f6')); 
                ?>; 
                border-radius: 12px; 
                padding: 20px; 
                transition: all 0.2s ease; 
                background: white;
                cursor: pointer;
            ">
                <h3 style="margin: 0 0 12px 0; font-size: 20px;">Phòng <?php echo $room->room_number; ?></h3>
                <p style="color: var(--apple-gray); margin-bottom: 4px; font-size: 13px;">
                    <?php echo $building ? $building->name : 'N/A'; ?> - Tầng <?php echo $room->floor; ?>
                </p>
                <p style="color: var(--apple-gray); margin-bottom: 4px; font-size: 13px;">
                    <?php echo $room->current_occupancy; ?>/<?php echo $room->capacity; ?> người
                </p>
                <p style="color: var(--apple-dark); margin-bottom: 12px; font-size: 14px; font-weight: 500;">
                    <?php echo number_format($room->price); ?>đ/tháng
                </p>
                <span style="
                    display: inline-block; 
                    padding: 4px 10px; 
                    border-radius: 20px; 
                    font-size: 12px;
                    background: <?php 
                        echo $room->status == 'Trống' ? 'rgba(16, 185, 129, 0.12)' : 
                             ($room->status == 'Đã đầy' ? 'rgba(239, 68, 68, 0.12)' : 
                             ($room->status == 'Bảo trì' ? 'rgba(245, 158, 11, 0.12)' : 'rgba(59, 130, 246, 0.12)')); 
                    ?>; 
                    color: <?php 
                        echo $room->status == 'Trống' ? '#065f46' : 
                             ($room->status == 'Đã đầy' ? '#991b1b' : 
                             ($room->status == 'Bảo trì' ? '#92400e' : '#1e40af')); 
                    ?>;">
                    <?php 
                    if ($room->status == 'Trống') echo 'Còn trống';
                    elseif ($room->status == 'Đã đầy') echo 'Đã đầy';
                    elseif ($room->status == 'Còn chỗ') echo 'Còn chỗ';
                    else echo 'Bảo trì';
                    ?>
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
            <p class="text-muted">Chưa có phòng nào</p>
            <?php if (is_admin()): ?>
            <a href="<?php echo url('/rooms/create'); ?>" class="btn btn-primary mt-3">+ Thêm phòng mới</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.room-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
</style>