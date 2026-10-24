<?php if (is_admin()): ?>

<!-- Thông tin trường -->
<div class="card dashboard-info-card">
    <div class="dashboard-info">
        <div>
            <div class="info-title"><?php echo setting('school_name', 'Đại học Bách Khoa'); ?></div>
            <div class="info-address"><?php echo setting('address', '123 Đường ABC, Quận 1, TP.HCM'); ?></div>
        </div>
        <div class="info-contact">
            <div><?php echo setting('contact_email', 'admin@ktx.edu.vn'); ?></div>
            <div class="info-phone"><?php echo setting('contact_phone', '0123456789'); ?></div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['total_students'] ?? 0; ?></div>
            <div class="stat-label">Sinh viên</div>
            <div class="stat-trend">Đang theo học</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['total_rooms'] ?? 0; ?></div>
            <div class="stat-label">Phòng</div>
            <div class="stat-trend"><?php echo $stats['available_rooms'] ?? 0; ?> phòng trống</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo round($stats['occupancy_rate'] ?? 0); ?>%</div>
            <div class="stat-label">Tỷ lệ lấp đầy</div>
            <div class="stat-trend"><?php echo $stats['active_contracts'] ?? 0; ?> hợp đồng</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($stats['monthly_revenue'] ?? 0); ?>đ</div>
            <div class="stat-label">Doanh thu tháng</div>
            <div class="stat-trend">Tháng <?php echo date('m/Y'); ?></div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="quick-stats-grid">
    <div class="quick-stat-card">
        <span>Đơn đăng ký chờ duyệt</span>
        <span class="badge badge-warning"><?php echo $stats['pending_registrations'] ?? 0; ?></span>
    </div>
    <div class="quick-stat-card">
        <span>Bảo trì đang xử lý</span>
        <span class="badge badge-info"><?php echo $stats['pending_maintenance'] ?? 0; ?></span>
    </div>
</div>

<!-- Recent Items -->
<div class="dashboard-row">
    <div class="card">
        <div class="card-header">
            <h4>Đơn đăng ký mới</h4>
            <a href="<?php echo url('/registrations'); ?>" class="btn btn-sm">Xem tất cả</a>
        </div>
        <?php if (!empty($recent_registrations)): ?>
        <table class="table">
            <tbody>
                <?php foreach ($recent_registrations as $reg): ?>
                <tr>
                    <td>
                        <div class="item-title"><?php echo $reg->full_name; ?></div>
                        <div class="item-subtitle"><?php echo $reg->student_code; ?></div>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $reg->status == 'Chờ duyệt' ? 'warning' : 'info'; ?>">
                            <?php echo $reg->status; ?>
                        </span>
                    </td>
                    <td class="item-date"><?php echo date('d/m/Y', strtotime($reg->created_at)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="empty-state">Chưa có đơn mới</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Yêu cầu bảo trì</h4>
            <a href="<?php echo url('/maintenance'); ?>" class="btn btn-sm">Xem tất cả</a>
        </div>
        <?php if (!empty($recent_maintenance)): ?>
        <table class="table">
            <tbody>
                <?php foreach ($recent_maintenance as $mr): ?>
                <tr>
                    <td>
                        <div class="item-title"><?php echo $mr->title; ?></div>
                        <div class="item-subtitle">Phòng <?php echo $mr->room_number; ?> — <?php echo $mr->full_name; ?></div>
                    </td>
                    <td>
                        <span class="badge badge-<?php
                            echo $mr->priority == 'Khẩn cấp' ? 'danger'
                               : ($mr->priority == 'Cao' ? 'warning' : 'info');
                        ?>"><?php echo $mr->priority; ?></span>
                    </td>
                    <td class="item-date"><?php echo date('d/m/Y', strtotime($mr->created_at)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="empty-state">Chưa có yêu cầu</p>
        <?php endif; ?>
    </div>
</div>

<!-- Building Stats -->
<div class="card">
    <h4 style="margin:0 0 14px; font-size:15px;">Thống kê theo tòa nhà</h4>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Tòa nhà</th>
                    <th class="text-center">Phòng</th>
                    <th class="text-center">Tổng giường</th>
                    <th class="text-center">Đã thuê</th>
                    <th class="text-center">Tỷ lệ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($building_stats)): ?>
                    <?php foreach ($building_stats as $b): ?>
                    <?php $rate = $b->total_beds > 0 ? round(($b->occupied_beds / $b->total_beds) * 100) : 0; ?>
                    <tr>
                        <td>
                            <span class="building-name"><?php echo $b->name; ?></span>
                            <span class="building-code">(<?php echo $b->code; ?>)</span>
                        </td>
                        <td class="text-center"><?php echo $b->total_rooms ?? 0; ?></td>
                        <td class="text-center"><?php echo $b->total_beds ?? 0; ?></td>
                        <td class="text-center"><?php echo $b->occupied_beds ?? 0; ?></td>
                        <td class="text-center">
                            <span class="badge badge-<?php
                                echo $rate >= 90 ? 'danger'
                                   : ($rate >= 60 ? 'warning' : 'info');
                            ?>"><?php echo $rate; ?>%</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="empty-state">Chưa có dữ liệu</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>

<?php if (!$has_profile): ?>
<!-- Chưa có hồ sơ -->
<div class="card empty-card">
    <div class="empty-icon">👋</div>
    <h3>Chào mừng <?php echo auth()['full_name'] ?? 'bạn'; ?>!</h3>
    <p class="empty-text">Vui lòng cập nhật hồ sơ sinh viên để sử dụng các dịch vụ KTX.</p>
    <a href="<?php echo url('/profile'); ?>" class="btn btn-primary">Cập nhật hồ sơ ngay</a>
</div>

<?php elseif (!$contract): ?>
<!-- Chưa có hợp đồng -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-label">Trạng thái</div>
            <div class="stat-value stat-value-small">Chưa ở KTX</div>
            <div class="stat-trend">Chưa có hợp đồng</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-label">Đơn đăng ký</div>
            <div class="stat-value stat-value-small">
                <?php echo $latest_registration ? $latest_registration->status : 'Chưa đăng ký'; ?>
            </div>
            <div class="stat-trend">
                <?php if ($latest_registration): ?>
                    Ngày: <?php echo date('d/m/Y', strtotime($latest_registration->registration_date)); ?>
                <?php else: ?>
                    Đăng ký ngay để được hỗ trợ
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card empty-card">
    <div class="empty-icon">🏠</div>
    <h3>Bạn chưa đăng ký ở KTX</h3>
    <p class="empty-text">
        <?php if ($latest_registration): ?>
            Đơn đang ở trạng thái: <strong><?php echo $latest_registration->status; ?></strong>
        <?php else: ?>
            Đăng ký ngay để được ở KTX với chi phí ưu đãi.
        <?php endif; ?>
    </p>
    <a href="<?php echo url($latest_registration ? '/registrations' : '/registrations/create'); ?>" class="btn btn-primary">
        <?php echo $latest_registration ? 'Xem đơn đăng ký' : 'Đăng ký KTX'; ?>
    </a>
</div>

<?php else: ?>
<!-- Đã có hợp đồng -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-label">Phòng hiện tại</div>
            <div class="stat-value"><?php echo $contract->room_number; ?></div>
            <div class="stat-trend"><?php echo $contract->building_name; ?> — Giường <?php echo $contract->bed_number; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-label">Tiền phòng tháng này</div>
            <div class="stat-value"><?php echo number_format($contract->monthly_rent); ?>đ</div>
            <div class="stat-trend">Hạn: 10/<?php echo date('m'); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-label">Hóa đơn chờ thanh toán</div>
            <div class="stat-value"><?php echo count($unpaid_invoices); ?></div>
            <div class="stat-trend <?php echo count($unpaid_invoices) > 0 ? 'down' : ''; ?>">
                <?php echo count($unpaid_invoices) > 0 ? 'Cần thanh toán' : 'Đã thanh toán hết'; ?>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-label">Ngày vào ở</div>
            <div class="stat-value stat-value-small"><?php echo date('d/m/Y', strtotime($contract->check_in_date)); ?></div>
            <div class="stat-trend">Hợp đồng <?php echo $contract->status; ?></div>
        </div>
    </div>
</div>

<div class="dashboard-row">
    <div class="card">
        <h4 style="margin:0 0 14px; font-size:15px;">Thông báo mới</h4>
        <?php if (!empty($notices)): ?>
            <?php foreach (array_slice($notices, 0, 3) as $i => $notice): ?>
            <div class="notice-item <?php echo $i > 0 ? 'notice-item-border' : ''; ?>">
                <div class="notice-header">
                    <span class="badge badge-<?php
                        echo $notice->type == 'Khẩn cấp' ? 'danger'
                           : ($notice->type == 'Quan trọng' ? 'warning' : 'info');
                    ?>"><?php echo $notice->type; ?></span>
                    <span class="notice-date"><?php echo date('d/m/Y', strtotime($notice->published_at)); ?></span>
                </div>
                <div class="notice-title"><?php echo $notice->title; ?></div>
                <div class="notice-content"><?php echo mb_substr($notice->content, 0, 80); ?>…</div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-state">Không có thông báo mới</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Hóa đơn cần thanh toán</h4>
            <?php if (!empty($unpaid_invoices)): ?>
            <a href="<?php echo url('/invoices'); ?>" class="btn btn-sm">Xem tất cả</a>
            <?php endif; ?>
        </div>
        <?php if (!empty($unpaid_invoices)): ?>
            <?php foreach (array_slice($unpaid_invoices, 0, 3) as $i => $invoice): ?>
            <div class="invoice-item <?php echo $i > 0 ? 'invoice-item-border' : ''; ?>">
                <div>
                    <div class="invoice-title">Tháng <?php echo $invoice->month; ?>/<?php echo $invoice->year; ?></div>
                    <div class="invoice-due">Hạn: <?php echo date('d/m/Y', strtotime($invoice->due_date)); ?></div>
                </div>
                <div class="invoice-amount">
                    <span><?php echo number_format($invoice->total_amount); ?>đ</span>
                    <a href="<?php echo url('/payments/create?invoice='.$invoice->id); ?>" class="btn btn-sm btn-primary">Thanh toán</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-state">Không có hóa đơn chưa thanh toán</p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4>Yêu cầu bảo trì gần đây</h4>
      <a href="<?php echo url('/maintenance/create'); ?>" 
   class="btn btn-sm btn-primary" 
   onclick="if(document.body.classList.contains('dark-mode')){this.style.color='#FFFFFF';}">
   Tạo yêu cầu
</a>
    </div>
    <?php if (!empty($maintenance_requests)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã yêu cầu</th>
                    <th>Tiêu đề</th>
                    <th>Loại</th>
                    <th>Trạng thái</th>
                    <th>Ngày gửi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maintenance_requests as $mr): ?>
                <tr>
                    <td><?php echo $mr->request_number; ?></td>
                    <td><?php echo $mr->title; ?></td>
                    <td><?php echo $mr->category; ?></td>
                    <td>
                        <span class="badge badge-<?php
                            echo $mr->status == 'Đã hoàn thành' ? 'success'
                               : ($mr->status == 'Đang xử lý' ? 'info' : 'warning');
                        ?>"><?php echo $mr->status; ?></span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($mr->created_at)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <a href="<?php echo url('/maintenance'); ?>" class="btn btn-sm w-full mt-3">Xem tất cả</a>
    <?php else: ?>
    <p class="empty-state">Chưa có yêu cầu bảo trì</p>
    <?php endif; ?>
</div>

<div class="action-buttons">
    <a href="<?php echo url('/complaints/create'); ?>" class="btn">Gửi góp ý</a>
    <a href="<?php echo url('/visitors/create'); ?>" class="btn">Đăng ký khách</a>
    <a href="<?php echo url('/check-out/create'); ?>" class="btn btn-danger">Yêu cầu trả phòng</a>
</div>

<?php endif; ?>
<?php endif; ?>