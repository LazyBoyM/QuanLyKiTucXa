-- Migration: create service_registrations (safe, does not drop DB)
CREATE TABLE IF NOT EXISTS service_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_number VARCHAR(30) UNIQUE NOT NULL,
    student_id INT NOT NULL,
    service_type ENUM('Giặt ủi','Gửi xe','Dọn phòng','Sửa chữa','Khác') NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    requested_date DATE NULL,
    status ENUM('Chờ xử lý','Đang xử lý','Đã hoàn thành','Đã hủy') DEFAULT 'Chờ xử lý',
    assigned_to INT NULL,
    processed_at TIMESTAMP NULL,
    processed_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
