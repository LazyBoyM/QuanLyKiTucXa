// Lưu vị trí scroll trước khi rời trang
window.addEventListener('beforeunload', function() {
    var mainContent = document.querySelector('.main-content');
    var scrollTop = mainContent ? mainContent.scrollTop : window.scrollY;
    sessionStorage.setItem('scrollPosition', scrollTop);
});

// Khôi phục vị trí scroll sau khi tải trang
window.addEventListener('load', function() {
    var scrollPosition = sessionStorage.getItem('scrollPosition');
    if (scrollPosition) {
        var mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.scrollTop = parseInt(scrollPosition);
        } else {
            window.scrollTo(0, parseInt(scrollPosition));
        }
        sessionStorage.removeItem('scrollPosition');
    }
});

// Ngăn chặn scroll restoration mặc định của trình duyệt
if (history.scrollRestoration) {
    history.scrollRestoration = 'manual';
}