document.addEventListener('DOMContentLoaded', function() {
    var mainContent = document.getElementById('mainContent');
    var contentDiv = document.querySelector('.content');
    var headerTitle = document.querySelector('.header h3');
    var navItems = document.querySelectorAll('.nav-item');
    var currentPath = window.location.pathname;
    
    // ========== HIGHLIGHT ACTIVE MENU ==========
    function highlightActive() {
        var path = window.location.pathname;
        navItems.forEach(function(item) {
            var href = item.getAttribute('href');
            if (href) {
                var cleanHref = href.replace('/dormflow', '').replace(/\/$/, '');
                var cleanPath = path.replace('/dormflow', '').replace(/\/$/, '');
                
                item.classList.remove('active');
                if (cleanPath === cleanHref || cleanPath.startsWith(cleanHref + '/')) {
                    item.classList.add('active');
                }
            }
        });
    }
    
    highlightActive();
    
    // ========== XỬ LÝ CLICK LINK - LOAD BẰNG AJAX ==========
    navItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            
            // Bỏ qua link logout, login, register
            if (!href || href === '#' || href === '#!' || 
                href.indexOf('/logout') !== -1 || 
                href.indexOf('/login') !== -1 || 
                href.indexOf('/register') !== -1) {
                return true;
            }
            
            // Ngăn chặn reload trang
            e.preventDefault();
            e.stopPropagation();
            
            // Cập nhật URL trên thanh địa chỉ
            history.pushState({}, '', href);
            
            // Load nội dung mới
            loadContent(href);
            
            // Highlight menu
            navItems.forEach(function(l) {
                l.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    
    // ========== XỬ LÝ NÚT BACK/FORWARD ==========
    window.addEventListener('popstate', function() {
        loadContent(window.location.href);
        highlightActive();
    });
    
    // ========== HÀM LOAD NỘI DUNG QUA AJAX ==========
    function loadContent(url) {
        // Hiển thị loading
        contentDiv.innerHTML = '<div style="text-align: center; padding: 60px 20px; color: var(--apple-gray);">Đang tải...</div>';
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            
            // Lấy content mới
            var newContent = doc.querySelector('.content');
            var newTitle = doc.querySelector('title');
            var newPageTitle = doc.querySelector('.header h3');
            
            // Cập nhật nội dung
            if (newContent) {
                contentDiv.innerHTML = newContent.innerHTML;
            }
            
            // Cập nhật title
            if (newTitle) {
                document.title = newTitle.innerText;
            }
            
            // Cập nhật page title
            if (newPageTitle) {
                headerTitle.innerText = newPageTitle.innerText;
            }
            
            // Scroll lên đầu content
            mainContent.scrollTop = 0;
        })
        .catch(function(error) {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="alert alert-error">Lỗi tải trang. Vui lòng thử lại!</div>';
        });
    }
    
    // ========== TỰ ĐỘNG ẨN ALERT ==========
    setInterval(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (!alert.dataset.hiding) {
                alert.dataset.hiding = 'true';
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                }, 3000);
            }
        });
    }, 1000);
});