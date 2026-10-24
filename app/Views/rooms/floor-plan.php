<div class="d-flex justify-between items-center mb-4">
    <h2>🗺️ Sơ đồ tầng</h2>
    <a href="<?php echo url('/rooms'); ?>" class="btn">← Quay lại</a>
</div>

<?php foreach ($floors as $floor => $rooms): ?>
<div class="card mb-4">
    <h3>Tầng <?php echo $floor; ?></h3>
    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px;">
        <?php foreach ($rooms as $room): ?>
        <a href="<?php echo url('/rooms/'.$room->id); ?>" style="text-decoration: none;">
            <div style="padding: 15px; border-radius: 8px; text-align: center; background: <?php 
                echo $room->status == 'available' ? '#d1fae5' : 
                     ($room->status == 'full' ? '#fee2e2' : '#fef3c7'); 
            ?>; border: 2px solid <?php 
                echo $room->status == 'available' ? '#10b981' : 
                     ($room->status == 'full' ? '#ef4444' : '#f59e0b'); 
            ?>;">
                <strong><?php echo $room->room_number; ?></strong>
                <p style="margin: 5px 0 0 0; font-size: 12px;">
                    <?php echo $room->current_occupancy; ?>/<?php echo $room->capacity; ?>
                </p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>