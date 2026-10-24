<div class="d-flex items-center mb-4">
    <a href="<?php echo url('/reports'); ?>" class="btn btn-sm" style="margin-right: 16px;">← Quay lại</a>
    <h2 style="margin: 0;">Báo cáo công suất</h2>
</div>

<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $totalRooms; ?></div>
            <div class="stat-label">Tổng phòng</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $totalBeds; ?></div>
            <div class="stat-label">Tổng giường</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $occupiedBeds; ?></div>
            <div class="stat-label">Đã thuê</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $occupancyRate; ?>%</div>
            <div class="stat-label">Tỷ lệ lấp đầy</div>
        </div>
    </div>
</div>

<div class="card">
    <h4>Thống kê theo tòa nhà</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Tòa nhà</th>
                <th class="text-right">Số phòng</th>
                <th class="text-right">Tổng giường</th>
                <th class="text-right">Đã thuê</th>
                <th class="text-right">Còn trống</th>
                <th class="text-right">Tỷ lệ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($buildingStats as $b): ?>
            <tr>
                <td><?php echo $b->name; ?></td>
                <td class="text-right"><?php echo $b->room_count ?? 0; ?></td>
                <td class="text-right"><?php echo $b->total_beds ?? 0; ?></td>
                <td class="text-right"><?php echo $b->occupied_beds ?? 0; ?></td>
                <td class="text-right"><?php echo ($b->total_beds ?? 0) - ($b->occupied_beds ?? 0); ?></td>
                <td class="text-right"><?php echo $b->total_beds > 0 ? round(($b->occupied_beds / $b->total_beds) * 100) : 0; ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card mt-4">
    <h4>Phòng còn trống</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Tòa</th>
                <th>Phòng</th>
                <th class="text-right">Sức chứa</th>
                <th class="text-right">Đã thuê</th>
                <th class="text-right">Còn trống</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($availableRooms as $r): ?>
            <tr>
                <td><?php echo $r->building_name; ?></td>
                <td><?php echo $r->room_number; ?></td>
                <td class="text-right"><?php echo $r->capacity; ?></td>
                <td class="text-right"><?php echo $r->current_occupancy; ?></td>
                <td class="text-right"><?php echo $r->capacity - $r->current_occupancy; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($availableRooms)): ?>
            <tr><td colspan="5" class="text-center text-muted py-3">Không có phòng trống</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>