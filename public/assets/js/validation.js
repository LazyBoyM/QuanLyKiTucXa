// ========================================
// VALIDATION CHO TOÀN HỆ THỐNG
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Số điện thoại - 10 số
    document.querySelectorAll('input[name="phone"], input[type="tel"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    });
    
    // CMND/CCCD - tối đa 12 số
    document.querySelectorAll('input[name="id_card"], input[name="idCard"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);
        });
    });
    
    // Mã số sinh viên - chữ và số, tối đa 10
    document.querySelectorAll('input[name="student_code"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-z0-9]/g, '').slice(0, 10);
        });
    });
    
    // Số phòng - chữ và số
    document.querySelectorAll('input[name="room_number"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-z0-9]/g, '').slice(0, 10);
        });
    });
    
    // Chỉ cho phép số
    document.querySelectorAll('input[data-type="number"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
    
    // Chỉ cho phép chữ
    document.querySelectorAll('input[data-type="text"]').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-zÀ-ỹ\s]/g, '');
        });
    });
    
});