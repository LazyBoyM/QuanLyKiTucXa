-- ========================================
-- XÓA DATABASE CŨ VÀ TẠO MỚI
-- ========================================
DROP DATABASE IF EXISTS dormflow_db;
CREATE DATABASE dormflow_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dormflow_db;

-- ========================================
-- BẢNG USERS
-- ========================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    avatar VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- BẢNG STUDENTS
-- ========================================
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    student_code VARCHAR(20) NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('Nam', 'Nữ', 'Khác') DEFAULT 'Nam',
    faculty VARCHAR(100),
    class VARCHAR(50),
    academic_year VARCHAR(10),
    permanent_address TEXT,
    id_card VARCHAR(20),
    health_insurance VARCHAR(30),
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- BẢNG BUILDINGS
-- ========================================
CREATE TABLE buildings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    floors INT DEFAULT 5,
    gender ENUM('Nam', 'Nữ', 'Tất cả') DEFAULT 'Tất cả',
    address TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- BẢNG ROOMS
-- ========================================
CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    building_id INT NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    floor INT NOT NULL,
    room_type ENUM('2', '4', '6', '8') DEFAULT '4',
    capacity INT DEFAULT 4,
    current_occupancy INT DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('Trống', 'Còn chỗ', 'Đã đầy', 'Bảo trì') DEFAULT 'Trống',
    amenities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    UNIQUE KEY unique_room (building_id, room_number)
);

-- ========================================
-- BẢNG ROOM_BEDS
-- ========================================
CREATE TABLE room_beds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    bed_number VARCHAR(5) NOT NULL,
    student_id INT NULL,
    check_in_date DATE,
    status ENUM('Trống', 'Đã thuê', 'Bảo trì') DEFAULT 'Trống',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
    UNIQUE KEY unique_bed (room_id, bed_number)
);

-- ========================================
-- BẢNG REGISTRATIONS
-- ========================================
CREATE TABLE registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_number VARCHAR(30) UNIQUE NOT NULL,
    student_id INT NOT NULL,
    building_id INT,
    desired_room_type ENUM('2', '4', '6', '8'),
    semester VARCHAR(20),
    academic_year VARCHAR(10),
    registration_date DATE,
    expected_check_in DATE,
    status ENUM('Chờ duyệt', 'Đã duyệt', 'Đã xếp phòng', 'Đã hủy', 'Từ chối') DEFAULT 'Chờ duyệt',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- BẢNG CONTRACTS
-- ========================================
CREATE TABLE contracts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contract_number VARCHAR(30) UNIQUE NOT NULL,
    registration_id INT NOT NULL,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    bed_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE,
    monthly_rent DECIMAL(10,2) NOT NULL,
    deposit DECIMAL(10,2) DEFAULT 0,
    status ENUM('Hiệu lực', 'Đã kết thúc', 'Đã hủy') DEFAULT 'Hiệu lực',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (bed_id) REFERENCES room_beds(id) ON DELETE CASCADE
);

-- ========================================
-- BẢNG INVOICES
-- ========================================
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(30) UNIQUE NOT NULL,
    contract_id INT NOT NULL,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    month INT NOT NULL,
    year INT NOT NULL,
    room_fee DECIMAL(10,2) DEFAULT 0,
    electricity_usage INT DEFAULT 0,
    electricity_fee DECIMAL(10,2) DEFAULT 0,
    water_usage INT DEFAULT 0,
    water_fee DECIMAL(10,2) DEFAULT 0,
    internet_fee DECIMAL(10,2) DEFAULT 0,
    other_fees DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('Chưa thanh toán', 'Đã thanh toán', 'Quá hạn') DEFAULT 'Chưa thanh toán',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);
ALTER TABLE `invoices` ADD COLUMN `invoice_type` VARCHAR(50) NULL DEFAULT NULL;
-- ========================================
-- BẢNG PAYMENTS
-- ========================================
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_number VARCHAR(30) UNIQUE NOT NULL,
    invoice_id INT NOT NULL,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Tiền mặt', 'Chuyển khoản', 'MoMo', 'VNPay') NOT NULL,
    transaction_id VARCHAR(100),
    status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    payment_date TIMESTAMP NULL,
    received_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- BẢNG MAINTENANCE_REQUESTS
-- ========================================
CREATE TABLE maintenance_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_number VARCHAR(30) UNIQUE NOT NULL,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    category ENUM('Điện', 'Nước', 'Điều hòa', 'Cửa', 'Nội thất', 'Vệ sinh', 'Khác') NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('Thấp', 'Trung bình', 'Cao', 'Khẩn cấp') DEFAULT 'Trung bình',
    status ENUM('Chờ xử lý', 'Đang xử lý', 'Đã hoàn thành', 'Đã hủy') DEFAULT 'Chờ xử lý',
    assigned_to INT,
    completed_at TIMESTAMP NULL,
    completed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- BẢNG COMPLAINTS
-- ========================================
CREATE TABLE complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_number VARCHAR(30) UNIQUE NOT NULL,
    student_id INT NOT NULL,
    category ENUM('Tiếng ồn', 'Vệ sinh', 'An ninh', 'Nhân viên', 'Cơ sở vật chất', 'Khác') NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    status ENUM('Chờ xử lý', 'Đang xem xét', 'Đã giải quyết', 'Từ chối') DEFAULT 'Chờ xử lý',
    response TEXT,
    responded_by INT,
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (responded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- BẢNG VISITORS
-- ========================================
CREATE TABLE visitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    id_card VARCHAR(30),
    phone VARCHAR(20),
    relationship VARCHAR(50),
    purpose VARCHAR(200),
    check_in_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    check_out_time TIMESTAMP NULL,
    status ENUM('checked_in', 'checked_out') DEFAULT 'checked_in',
    registered_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (registered_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================================
-- BẢNG NOTICES (ĐÃ FIX DATES)
-- ========================================
CREATE TABLE notices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('Chung', 'Quan trọng', 'Khẩn cấp', 'Sự kiện') DEFAULT 'Chung',
    created_by INT NOT NULL,
    published_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================================
-- BẢNG CHECK_OUT_REQUESTS
-- ========================================
CREATE TABLE check_out_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_number VARCHAR(30) UNIQUE NOT NULL,
    contract_id INT NOT NULL,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    request_date DATE NOT NULL,
    reason TEXT,
    status ENUM('Chờ duyệt', 'Đã duyệt', 'Đã hoàn thành', 'Đã hủy') DEFAULT 'Chờ duyệt',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- BẢNG ACTIVITIES
-- ========================================
CREATE TABLE activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- BẢNG SETTINGS
-- ========================================
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- BẢNG RULES
-- ========================================
CREATE TABLE rules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('Chung', 'An ninh', 'Vệ sinh', 'Phòng cháy', 'Khác') DEFAULT 'Chung',
    penalty VARCHAR(200),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================================
-- BẢNG VIOLATIONS
-- ========================================
CREATE TABLE violations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    violation_number VARCHAR(30) UNIQUE NOT NULL,
    student_id INT NOT NULL,
    rule_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    penalty VARCHAR(200),
    penalty_amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('Chờ xử lý', 'Đã xử lý', 'Đã hủy') DEFAULT 'Chờ xử lý',
    reported_by INT NOT NULL,
    processed_by INT,
    processed_at TIMESTAMP NULL,
    invoice_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (rule_id) REFERENCES rules(id) ON DELETE SET NULL,
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL
);

-- ========================================
-- BẢNG SERVICE_REGISTRATIONS (Đăng ký dịch vụ bởi sinh viên)
-- ========================================
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

-- ========================================
-- DỮ LIỆU MẶC ĐỊNH
-- ========================================

-- Admin (password: password)
INSERT INTO users (username, email, full_name, password, role, status) VALUES 
('admin', 'admin@ktx.edu.vn', 'Administrator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Buildings
INSERT INTO buildings (code, name, floors, gender) VALUES
('A', 'Tòa A', 5, 'Nam'),
('B', 'Tòa B', 5, 'Nữ'),
('C', 'Tòa C', 3, 'Tất cả');

-- Rooms - Tòa A
INSERT INTO rooms (building_id, room_number, floor, room_type, capacity, price, status) VALUES
(1, 'A101', 1, '4', 4, 400000, 'Trống'),
(1, 'A102', 1, '4', 4, 400000, 'Trống'),
(1, 'A103', 1, '6', 6, 350000, 'Trống'),
(1, 'A104', 1, '8', 8, 300000, 'Trống'),
(1, 'A201', 2, '4', 4, 400000, 'Trống'),
(1, 'A202', 2, '4', 4, 400000, 'Trống'),
(1, 'A203', 2, '6', 6, 350000, 'Trống'),
(1, 'A204', 2, '8', 8, 300000, 'Trống'),
(1, 'A301', 3, '4', 4, 400000, 'Trống'),
(1, 'A302', 3, '4', 4, 400000, 'Trống');

-- Rooms - Tòa B
INSERT INTO rooms (building_id, room_number, floor, room_type, capacity, price, status) VALUES
(2, 'B101', 1, '4', 4, 400000, 'Trống'),
(2, 'B102', 1, '4', 4, 400000, 'Trống'),
(2, 'B103', 1, '6', 6, 350000, 'Trống'),
(2, 'B104', 1, '8', 8, 300000, 'Trống'),
(2, 'B201', 2, '4', 4, 400000, 'Trống'),
(2, 'B202', 2, '4', 4, 400000, 'Trống'),
(2, 'B203', 2, '6', 6, 350000, 'Trống'),
(2, 'B204', 2, '8', 8, 300000, 'Trống'),
(2, 'B301', 3, '4', 4, 400000, 'Trống'),
(2, 'B302', 3, '4', 4, 400000, 'Trống');

-- Rooms - Tòa C
INSERT INTO rooms (building_id, room_number, floor, room_type, capacity, price, status) VALUES
(3, 'C101', 1, '2', 2, 800000, 'Trống'),
(3, 'C102', 1, '2', 2, 800000, 'Trống'),
(3, 'C201', 2, '2', 2, 800000, 'Trống'),
(3, 'C202', 2, '2', 2, 800000, 'Trống'),
(3, 'C301', 3, '2', 2, 800000, 'Trống'),
(3, 'C302', 3, '2', 2, 800000, 'Trống');

-- Room Beds
INSERT INTO room_beds (room_id, bed_number, status)
SELECT id, '1', 'Trống' FROM rooms WHERE capacity >= 1
UNION ALL SELECT id, '2', 'Trống' FROM rooms WHERE capacity >= 2
UNION ALL SELECT id, '3', 'Trống' FROM rooms WHERE capacity >= 3
UNION ALL SELECT id, '4', 'Trống' FROM rooms WHERE capacity >= 4
UNION ALL SELECT id, '5', 'Trống' FROM rooms WHERE capacity >= 5
UNION ALL SELECT id, '6', 'Trống' FROM rooms WHERE capacity >= 6
UNION ALL SELECT id, '7', 'Trống' FROM rooms WHERE capacity >= 7
UNION ALL SELECT id, '8', 'Trống' FROM rooms WHERE capacity >= 8;

-- Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('school_name', 'Đại học ABCXYZ'),
('dormitory_name', 'HUY Belpfi KTX'),
('contact_email', 'admin@ktx.edu.vn'),
('contact_phone', '0123456789'),
('address', '120 yên lãng'),
('electricity_rate', '3500'),
('water_rate', '15000'),
('internet_fee', '100000'),
('default_deposit', '500000'),
('check_in_time', '14:00'),
('check_out_time', '12:00'),
('visitor_hours', '06:00 - 22:00');

-- Rules
INSERT INTO rules (title, content, category, penalty, created_by) VALUES
('Giữ gìn vệ sinh chung', 'Không xả rác bừa bãi, giữ vệ sinh phòng ở và khu vực chung', 'Vệ sinh', 'Cảnh cáo, trực nhật', 1),
('Giờ giấc KTX', 'Đóng cửa sau 22:00, không ra vào sau giờ quy định', 'Chung', 'Cảnh cáo, phạt 100.000đ', 1),
('An ninh trật tự', 'Không gây ồn ào sau 22:00, không tụ tập đông người', 'An ninh', 'Cảnh cáo, đình chỉ nội trú', 1),
('Phòng cháy chữa cháy', 'Không sử dụng bếp gas, nến, thiết bị dễ cháy nổ trong phòng', 'Phòng cháy', 'Phạt 500.000đ, đình chỉ nội trú', 1);