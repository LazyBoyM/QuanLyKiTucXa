<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $title ?? 'DormFlow'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/apple.css">
    <style>
        html, body { height: 100%; overflow: hidden; }
        .layout { display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 260px; height: 100vh; overflow-y: auto; overflow-x: hidden; flex-shrink: 0; }
        .main-content { flex: 1; height: 100vh; overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column; }
        .header { flex-shrink: 0; }
        .content { flex: 1; padding: 24px; }
        footer { flex-shrink: 0; padding: 16px 24px; border-top: 1px solid rgba(0,0,0,0.05); background: var(--apple-white); }
        .sidebar::-webkit-scrollbar, .main-content::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track, .main-content::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb, .main-content::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 3px; }

        #ajax-loader {
            position: fixed;
            top: 0; left: 0;
            height: 3px;
            width: 0%;
            background: var(--apple-blue, #007AFF);
            z-index: 9999;
            opacity: 0;
            transition: width 0.3s ease, opacity 0.4s ease;
            border-radius: 0 2px 2px 0;
        }
        #ajax-loader.active { opacity: 1; }
        #ajax-loader.done   { width: 100% !important; opacity: 0; }

        #contentArea { transition: opacity 0.15s ease; }
        #contentArea.loading { opacity: 0.45; pointer-events: none; }
        
        /* Dark mode toggle button */
        #darkModeToggle {
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 6px 10px;
            border-radius: 20px;
            transition: background 0.2s;
        }
        
        #darkModeToggle:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        [data-theme="dark"] #darkModeToggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>


<script src="<?php echo BASE_URL; ?>/assets/js/validation.js"></script>

<body>

<div id="ajax-loader"></div>

<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><?php echo setting('dormitory_name', 'DormFlow'); ?></h2>
            <p style="font-size: 11px; color: var(--apple-gray); margin-top: 4px;">
                <?php echo setting('school_name', 'Đại học ABC'); ?>
            </p>
        </div>

        <nav class="sidebar-nav">
            <?php if (is_logged_in()): ?>

            <div class="nav-section">
                <div class="nav-title">Chính</div>
                <a href="<?php echo url('/dashboard'); ?>" class="nav-item" data-title="Dashboard">Dashboard</a>

                <?php if (is_admin()): ?>
                <a href="<?php echo url('/students'); ?>"     class="nav-item" data-title="Sinh viên">Sinh viên</a>
                <a href="<?php echo url('/rooms'); ?>"        class="nav-item" data-title="Phòng">Phòng</a>
                <a href="<?php echo url('/registrations'); ?>" class="nav-item" data-title="Đăng ký">Đăng ký</a>
                <a href="<?php echo url('/contracts'); ?>"    class="nav-item" data-title="Hợp đồng">Hợp đồng</a>
                <a href="<?php echo url('/invoices'); ?>"     class="nav-item" data-title="Hóa đơn">Hóa đơn</a>
                <a href="<?php echo url('/payments'); ?>"     class="nav-item" data-title="Thanh toán">Thanh toán</a>
                <a href="<?php echo url('/reports'); ?>"      class="nav-item" data-title="Báo cáo">Báo cáo</a>
                <a href="<?php echo url('/settings'); ?>"     class="nav-item" data-title="Cài đặt">Cài đặt</a>
                <?php else: ?>
                <a href="<?php echo url('/profile'); ?>"       class="nav-item" data-title="Hồ sơ">Hồ sơ</a>
                <a href="<?php echo url('/registrations'); ?>" class="nav-item" data-title="Đăng ký KTX">Đăng ký KTX</a>
                <a href="<?php echo url('/contracts'); ?>"     class="nav-item" data-title="Hợp đồng">Hợp đồng</a>
                <a href="<?php echo url('/invoices'); ?>"      class="nav-item" data-title="Hóa đơn">Hóa đơn</a>
                <a href="<?php echo url('/payments'); ?>"      class="nav-item" data-title="Thanh toán">Thanh toán</a>
                <?php endif; ?>
            </div>

            <div class="nav-section">
                <div class="nav-title">Tiện ích</div>
                <a href="<?php echo url('/maintenance'); ?>" class="nav-item" data-title="Bảo trì">Bảo trì</a>
                <a href="<?php echo url('/notices'); ?>"     class="nav-item" data-title="Thông báo">Thông báo</a>
                <a href="<?php echo url('/services'); ?>"    class="nav-item" data-title="Dịch vụ">Dịch vụ</a>
                <a href="<?php echo url('/complaints'); ?>"  class="nav-item" data-title="Góp ý - Khiếu nại">Góp ý - Khiếu nại</a>
                <a href="<?php echo url('/visitors'); ?>"    class="nav-item" data-title="Khách thăm">Khách thăm</a>
                <a href="<?php echo url('/check-out'); ?>"   class="nav-item" data-title="Trả phòng">Trả phòng</a>
                <a href="<?php echo url('/rules'); ?>" class="nav-item" data-ajax>Nội quy</a>
                <a href="<?php echo url('/violations'); ?>" class="nav-item" data-ajax>Vi phạm</a>
            </div>

            <div class="nav-section" style="margin-top: 20px;">
                <a href="<?php echo url('/logout'); ?>" class="nav-item" data-no-ajax
                   style="color: var(--apple-red);">Đăng xuất</a>
            </div>

            <?php else: ?>
            <div class="nav-section">
                <a href="<?php echo url('/login'); ?>"    class="nav-item" data-no-ajax>Đăng nhập</a>
                <a href="<?php echo url('/register'); ?>" class="nav-item" data-no-ajax>Đăng ký</a>
            </div>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="main-content" id="mainContent">
        <header class="header">
            <h3 id="pageTitle"><?php echo $pageTitle ?? 'Dashboard'; ?></h3>
            <div class="d-flex items-center gap-3">
                <!-- Nút Dark Mode - SỬA LẠI ONCLICK -->
                <button id="darkModeToggle" onclick="toggleDarkMode()">
                    <span id="darkModeIcon">🌙</span>
                </button>
                
                <?php if (is_logged_in()): ?>
                <span><?php echo auth()['full_name'] ?? 'User'; ?></span>
                <?php if (is_admin()): ?>
                <span class="badge" style="background:rgba(255,59,48,0.12); color:var(--apple-red);">Admin</span>
                <?php else: ?>
                <span class="badge" style="background:rgba(0,122,255,0.12); color:var(--apple-blue);">Sinh viên</span>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </header>

        <div class="content" id="contentArea">
            <?php if ($message = flash('success')): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($message = flash('error')): ?>
            <div class="alert alert-error"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php echo $content; ?>
        </div>

        <footer>
            <div class="d-flex justify-between items-center">
                <p style="color:var(--apple-gray); font-size:13px; margin:0;">
                    &copy; <?php echo date('Y'); ?> <?php echo setting('school_name', 'Đại học Bách Khoa'); ?> —
                    <?php echo setting('dormitory_name', 'DormFlow'); ?>
                </p>
                <p style="color:var(--apple-gray); font-size:13px; margin:0;">
                    <?php echo setting('contact_email', 'admin@ktx.edu.vn'); ?> |
                    <?php echo setting('contact_phone', '0123456789'); ?>
                </p>
            </div>
        </footer>
    </main>
</div>

<script>
console.log('Dark Mode script loaded!');

function toggleDarkMode() {
    var body = document.body;
    var icon = document.getElementById('darkModeIcon');
    
    if (body.classList.contains('dark-mode')) {
        // Chuyển về Light Mode
        body.classList.remove('dark-mode');
        removeDarkStyles();
        icon.textContent = '🌙';
        localStorage.setItem('theme', 'light');
    } else {
        // Chuyển sang Dark Mode
        body.classList.add('dark-mode');
        applyDarkStyles();
        icon.textContent = '☀️';
        localStorage.setItem('theme', 'dark');
    }
}

// Áp dụng Dark Mode
function applyDarkStyles() {
    var style = document.getElementById('dark-mode-style');
    if (!style) {
        style = document.createElement('style');
        style.id = 'dark-mode-style';
        document.head.appendChild(style);
    }
    
    style.textContent = `
        body { background: #000000 !important; color: #FFFFFF !important; }
        
        /* Sidebar */
        .sidebar { background: #1C1C1E !important; border-right-color: #38383A !important; }
        .sidebar-header h2, .sidebar-header p { color: #FFFFFF !important; }
        .nav-item { color: #FFFFFF !important; }
        .nav-item:hover { background: rgba(255, 255, 255, 0.08) !important; }
        .nav-item.active { background: #0A84FF !important; color: #FFFFFF !important; }
        .nav-title { color: #98989D !important; }
        
        /* Header */
        .header { background: #1C1C1E !important; border-bottom-color: #38383A !important; }
        .header h3 { color: #FFFFFF !important; }
        
        /* Cards */
        .card { background: #1C1C1E !important; border: 1px solid #38383A !important; }
        .card h3, .card h4, .card p { color: #FFFFFF !important; }
        .stat-card { background: #1C1C1E !important; }
        .stat-value { color: #FFFFFF !important; }
        .stat-label { color: #98989D !important; }
        .stat-trend { color: #98989D !important; }
        
        /* Tables */
        .table { color: #FFFFFF !important; }
        .table th { color: #98989D !important; background: rgba(255, 255, 255, 0.03) !important; border-bottom-color: #38383A !important; }
        .table td { color: #FFFFFF !important; border-bottom-color: rgba(255, 255, 255, 0.05) !important; }
        .table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }
        
        /* Forms */
        .form-control { background: #1C1C1E !important; border-color: #38383A !important; color: #FFFFFF !important; }
        .form-control::placeholder { color: #636366 !important; }
        .form-label { color: #FFFFFF !important; }
        select.form-control { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2398989D' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important; }
        
        /* Buttons */
        .btn { color: #0A84FF !important; }
        .btn:hover { background: rgba(10, 132, 255, 0.1) !important; }
        .btn-primary { background: #0A84FF !important; color: #FFFFFF !important; }
        .btn-danger { background: #FF453A !important; color: #FFFFFF !important; }
        .btn-success { background: #30D158 !important; color: #FFFFFF !important; }
        .btn-sm { color: #0A84FF !important; }
        
        /* Badges */
        .badge { background: #2C2C2E !important; color: #FFFFFF !important; }
        .badge-success { background: rgba(48, 209, 88, 0.15) !important; color: #30D158 !important; }
        .badge-warning { background: rgba(255, 159, 10, 0.15) !important; color: #FF9F0A !important; }
        .badge-danger { background: rgba(255, 69, 58, 0.15) !important; color: #FF453A !important; }
        .badge-info { background: rgba(10, 132, 255, 0.15) !important; color: #0A84FF !important; }
        
        /* Alerts */
        .alert-success { background: rgba(48, 209, 88, 0.1) !important; border-color: rgba(48, 209, 88, 0.2) !important; color: #30D158 !important; }
        .alert-error { background: rgba(255, 69, 58, 0.1) !important; border-color: rgba(255, 69, 58, 0.2) !important; color: #FF453A !important; }
        .alert-warning { background: rgba(255, 159, 10, 0.1) !important; border-color: rgba(255, 159, 10, 0.2) !important; color: #FF9F0A !important; }
        .alert-info { background: rgba(10, 132, 255, 0.1) !important; border-color: rgba(10, 132, 255, 0.2) !important; color: #0A84FF !important; }
        
        /* Text */
        .text-muted { color: #98989D !important; }
        
        /* Footer */
        footer { background: #1C1C1E !important; border-top-color: #38383A !important; }
        footer p { color: #98989D !important; }
        
        /* Auth */
        .auth-container { background: linear-gradient(180deg, #000000 0%, #1C1C1E 100%) !important; }
        .auth-card { background: #1C1C1E !important; }
        .auth-header h1 { color: #FFFFFF !important; }
        .auth-header p { color: #98989D !important; }
        .auth-footer { color: #98989D !important; border-top-color: #38383A !important; }


        /* ÉP BUỘC NÚT PRIMARY CHỮ TRẮNG */
        .dark-mode .btn-primary,
        .dark-mode .btn.btn-primary,
        .dark-mode a.btn-primary,
        .dark-mode button.btn-primary {
            background: #0A84FF !important;
            color: #FFFFFF !important;
            -webkit-text-fill-color: #FFFFFF !important;
        }
        
        .dark-mode .btn-primary:hover {
            background: #0077EE !important;
            color: #FFFFFF !important;
            -webkit-text-fill-color: #FFFFFF !important;
        }
    `;
}

// Xóa Dark Mode
function removeDarkStyles() {
    var style = document.getElementById('dark-mode-style');
    if (style) {
        style.remove();
    }
}

// Khởi tạo theme khi load trang
(function() {
    var savedTheme = localStorage.getItem('theme');
    var body = document.body;
    var icon = document.getElementById('darkModeIcon');
    
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        applyDarkStyles();
        if (icon) icon.textContent = '☀️';
    } else {
        if (icon) icon.textContent = '🌙';
    }
})();

// Test ngay khi load
console.log('Body has dark-mode?', document.body.classList.contains('dark-mode'));
</script>

<script>
(function () {
    var mainContent = document.getElementById('mainContent');
    var contentArea = document.getElementById('contentArea');
    var pageTitle   = document.getElementById('pageTitle');
    var loader      = document.getElementById('ajax-loader');
    var sidebarNav  = document.querySelector('.sidebar-nav');

    var SKIP_PATHS = ['/logout', '/login', '/register'];
    var currentCtrl = null;
    var loaderTimer = null;

    function loaderStart() {
        clearTimeout(loaderTimer);
        loader.style.width = '0%';
        loader.className = 'active';
        setTimeout(function () { loader.style.width = '40%'; }, 50);
        setTimeout(function () { loader.style.width = '70%'; }, 350);
        setTimeout(function () { loader.style.width = '85%'; }, 800);
    }

    function loaderDone() {
        loader.style.width = '100%';
        loaderTimer = setTimeout(function () { loader.className = 'done'; }, 250);
    }

    function loaderFail() {
        loader.style.background = 'var(--apple-red, #FF3B30)';
        loaderDone();
        setTimeout(function () { loader.style.background = 'var(--apple-blue, #007AFF)'; }, 1000);
    }

    function highlightActive() {
        var path = window.location.pathname.replace(/\/$/, '');
        document.querySelectorAll('.nav-item').forEach(function (item) {
            var href = item.getAttribute('href');
            if (!href) return;
            var cleanHref = href.replace(/\/$/, '');
            item.classList.toggle('active',
                path === cleanHref || path.startsWith(cleanHref + '/')
            );
        });
    }

    function updatePageTitle(html) {
        var temp = document.createElement('div');
        temp.innerHTML = html;
        var meta = temp.querySelector('[data-page-title]');
        if (meta) {
            var t = meta.getAttribute('data-page-title');
            pageTitle.textContent = t;
            document.title = t + ' — DormFlow';
            return;
        }

        var activeItem = document.querySelector('.nav-item.active');
        if (activeItem) {
            var t = activeItem.getAttribute('data-title') || activeItem.textContent.trim();
            pageTitle.textContent = t;
            document.title = t + ' — DormFlow';
        }
    }

    function initAlerts(scope) {
        (scope || document).querySelectorAll('.alert:not([data-hiding])').forEach(function (el) {
            el.dataset.hiding = 'true';
            setTimeout(function () {
                el.style.transition = 'opacity 0.4s ease';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 400);
            }, 3000);
        });
    }

    function loadContent(url) {
        if (currentCtrl) currentCtrl.abort();
        currentCtrl = new AbortController();

        loaderStart();
        contentArea.classList.add('loading');

        fetch(url, {
            signal: currentCtrl.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text();
        })
        .then(function (html) {
            contentArea.innerHTML = html;
            highlightActive();
            updatePageTitle(html);
            contentArea.classList.remove('loading');
            mainContent.scrollTop = 0;
            loaderDone();
            initAlerts(contentArea);
        })
        .catch(function (err) {
            if (err.name === 'AbortError') return;
            contentArea.classList.remove('loading');
            loaderFail();
            contentArea.innerHTML =
                '<div class="alert alert-error">Không thể tải trang. ' +
                '<a href="' + url + '" style="font-weight:600;text-decoration:underline">Thử lại</a></div>';
        });
    }

    sidebarNav.addEventListener('click', function (e) {
        var item = e.target.closest('.nav-item');
        if (!item) return;
        if (item.hasAttribute('data-no-ajax')) return;

        var href = item.getAttribute('href');
        if (!href || href === '#') return;

        var skip = SKIP_PATHS.some(function (p) {
            return href === p || href.endsWith(p);
        });
        if (skip) return;

        e.preventDefault();

        var cleanCurrent = window.location.pathname.replace(/\/$/, '');
        var cleanHref    = href.replace(/\/$/, '');
        if (cleanCurrent === cleanHref) return;

        history.pushState({ url: href }, '', href);
        loadContent(href);
    });

    window.addEventListener('popstate', function () {
        loadContent(window.location.href);
    });

    history.replaceState({ url: window.location.href }, '', window.location.href);
    highlightActive();
    initAlerts();
})();
</script>

</body>
</html>