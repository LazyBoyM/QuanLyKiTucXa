import database/migrations/001_create_all_tables.sql vào MySQL

Công nghệ sử dụng:
Backend: PHP thuần (MVC tự xây dựng)

Database: MySQL

Frontend: HTML, CSS, JavaScript

Thanh toán: Tích hợp MoMo Sandbox

PHÂN QUYỀN NGƯỜI DÙNG
Vai trò Quyền hạn
Admin Toàn quyền: quản lý sinh viên, phòng, đơn đăng ký, hợp đồng, hóa đơn, thanh toán, bảo trì, thông báo, góp ý, khách, trả phòng, báo cáo, cài đặt
Student Xem hồ sơ cá nhân, đăng ký KTX, xem hợp đồng, xem hóa đơn, thanh toán, tạo yêu cầu bảo trì, gửi góp ý, đăng ký khách, yêu cầu trả phòng

Middleware kiểm tra quyền:

// Auth.php - Kiểm tra đã đăng nhập
// Admin.php - Kiểm tra role = 'admin'
// Student.php - Kiểm tra role = 'student'

┌─────────────────────────────────────────────────────────────────────────────┐
│ LUỒNG ĐĂNG KÝ KTX │
└─────────────────────────────────────────────────────────────────────────────┘

1. SINH VIÊN ĐĂNG KÝ TÀI KHOẢN
   └── /register → Tạo user (role=student)

2. SINH VIÊN CẬP NHẬT HỒ SƠ
   └── /profile → Thêm MSSV, khoa, lớp...

3. SINH VIÊN TẠO ĐƠN ĐĂNG KÝ KTX
   └── /registrations/create
   ├── Chọn học kỳ, năm học
   ├── Chọn tòa nhà mong muốn
   ├── Chọn loại phòng (2,4,6,8 người)
   └── Ngày dự kiến vào ở
   → Trạng thái: "Chờ duyệt"

### DormFlow — Quản lý ký túc xá

Phiên bản rút gọn của tài liệu dự án, mô tả cấu trúc, cách cài đặt và chạy dự án trên môi trường local (XAMPP).

## Tổng quan

DormFlow là một ứng dụng quản lý ký túc xá viết bằng PHP thuần (MVC tùy chỉnh). Chức năng chính: quản lý sinh viên, phòng, đăng ký, hợp đồng, hóa đơn, thanh toán (MoMo), yêu cầu bảo trì, thông báo, khách và báo cáo.

## Yêu cầu

- PHP 7.4+ (khuyến nghị 8.x)
- MySQL / MariaDB
- Composer (nếu cần cài thêm package)
- XAMPP (Apache + MySQL) trên Windows — project thường đặt trong `htdocs`

## Cấu trúc thư mục chính

- `app/` — Controllers, Models, Views, Core (Router, Controller, DB), Helpers, Middleware
- `config/` — cấu hình ứng dụng và database
- `database/migrations/` — các file SQL để tạo bảng và seed dữ liệu mẫu
- `public/` — entry point `index.php`, assets (css/js)
- `routes/` — định nghĩa route (web.php)
- `storage/` — cache, uploads, logs, invoices
- `vendor/` — thư viện composer

Ví dụ nhanh: `app/Views/layouts/app.php` chứa menu sidebar, `app/Controllers/ServiceController.php` xử lý đăng ký dịch vụ.

## Cấu hình (ENV)

Tạo file `.env` ở thư mục gốc (nếu chưa có) với nội dung ví dụ:

```
DB_HOST=127.0.0.1
DB_NAME=dormflow_db
DB_USER=root
DB_PASS=
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/dormflow
```

Lưu ý: `public/index.php` hiện đặt `BASE_URL` là `/dormflow`. Nếu bạn đặt dự án trực tiếp trong root của localhost, chỉnh lại `BASE_URL` theo đường dẫn.

## Tạo cơ sở dữ liệu

1. Nếu muốn tạo toàn bộ schema và seed mẫu (gỡ hết DB hiện có):
   - Dùng `database/migrations/001_create_all_tables.sql` (chú ý: file này có lệnh `DROP DATABASE IF EXISTS`).
   - Import file này qua phpMyAdmin hoặc MySQL CLI để tái tạo database và dữ liệu mẫu.

2. Nếu chỉ cần thêm bảng mới (an toàn hơn):
   - Dùng `database/migrations/002_add_service_registrations.sql` (tạo bảng `service_registrations` nếu chưa có).

Ví dụ chạy trong PowerShell (MySQL cần hoạt động):

```powershell
mysql -u root -p dormflow_db < "C:\xampp\htdocs\dormflow\database\migrations\002_add_service_registrations.sql"
```

Hoặc paste SQL vào phpMyAdmin → chọn database `dormflow_db` → tab SQL → chạy.

## Cài đặt và chạy

1. Đặt project vào `C:\xampp\htdocs\dormflow` (hoặc thư mục tương ứng trong XAMPP).
2. Khởi động Apache và MySQL (XAMPP Control Panel).
3. Tạo file `.env` như trên và đảm bảo database đã tồn tại.
4. (Tùy chọn) Cài composer nếu có dependencies: `composer install`.
5. Mở trình duyệt: `http://localhost/dormflow` (hoặc URL bạn cấu hình).

## Routes chính (tóm tắt)

- `/` — Dashboard
- `/login`, `/register`, `/logout` — Authentication
- `/students`, `/rooms`, `/registrations`, `/contracts`, `/invoices`, `/payments` — quản lý chính (Auth required)
- `/maintenance`, `/complaints`, `/visitors`, `/check-out`, `/notices`, `/rules`, `/violations` — các tiện ích
- `/services` — (mới) quản lý đăng ký dịch vụ

Xem chi tiết trong `routes/web.php`.

## Menu

Menu sidebar được định nghĩa trong `app/Views/layouts/app.php`. Tôi đã thêm mục "Dịch vụ" trỏ tới `/services`.

## Bảo mật & middleware

- CSRF: helper `csrf_field()` và `verify_csrf()` trong `app/Helpers/functions.php`.
- Middleware: `app/Middleware/Auth.php`, `Admin.php`, `Student.php` (kiểm tra session / role).

## Kiểm thử nhanh

1. Admin mẫu đã seed: username `admin` / password `password` (đã hash trong seed).
2. Truy cập:
   - `/services/create` để tạo đăng ký dịch vụ (role student cần profile + hợp đồng)
   - Admin: `/services` để xem và xử lý đăng ký

## Ghi chú khi thay đổi DB

- Tránh chạy `001_create_all_tables.sql` trên database có dữ liệu nếu bạn không muốn mất dữ liệu.
- Ưu tiên dùng các file `00x_*.sql` bổ sung hoặc viết migration mới để thay đổi schema an toàn.

## Tiếp theo (tùy chọn tôi có thể làm)

- Tạo script chạy migrations tự động từ thư mục `database/migrations/`.
- Thêm seed mẫu cho `service_registrations`.
- Hiển thị/ẩn menu theo role hoặc thêm icon cho item.

---

Nếu bạn cần README bằng tiếng Anh hoặc muốn bổ sung sơ đồ ER dưới dạng file ảnh, nói tôi sẽ thêm.
