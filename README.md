# 🍗 GAO - Gà Sốt & Cơm Hà Nội
> **Hệ Thống Website Đặt Món Trực Tuyến & Quản Trị Nhà Hàng F&B Chuẩn POS/SaaS**

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/TailwindCSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Pest_PHP-Tested_100%25-00D8A5?style=for-the-badge" alt="Pest PHP">
</p>

---

## 🌟 Tổng Quan Dự Án

**GAO - Gà Sốt & Cơm** là nền tảng đặt món ăn trực tuyến chuyên nghiệp được xây dựng tối ưu cho các chuỗi nhà hàng, quán ăn F&B. Dự án kết hợp giữa **trải nghiệm đặt món mượt mà cho thực khách (Storefront UX)** và **hệ thống vận hành, tiếp nhận đơn hàng tức thời cho nhà hàng (Admin POS Dashboard)**.

---

## ✨ Tính Năng Nổi Bật

### 🛒 1. Dành Cho Khách Hàng (Storefront)
- **Giao Diện Hiện Đại & Chuẩn Mobile-First**: Tốc độ tải trang tức thì, hiệu ứng mượt mà, tối ưu hiển thị hoàn hảo trên mọi thiết bị di động và máy tính.
- **Tuỳ Biến Món Ăn Chuyên Sâu**:
  - Tự do lựa chọn hương vị sốt độc quyền (Sốt Cay Hàn, Sốt Mật Ong, Sốt Bơ Tỏi, Sốt Chua Ngọt...).
  - Chọn cấp độ cay phù hợp khẩu vị.
  - Thêm topping (Trứng ốp la, Phô mai tan chảy, Kim chi...), gọi thêm món ăn kèm và nước ngọt.
  - Hỗ trợ mua các hũ sốt lẻ hoặc chọn gói Combo tiết kiệm.
- **Giỏ Hàng Thông Minh (Smart Cart Drawer)**:
  - Tự động lưu giỏ hàng vào `localStorage` (không sợ mất khi reload trang hoặc mất kết nối).
  - Thanh tiến trình **Freeship trực quan** thúc đẩy khách đặt thêm món.
  - Gợi ý **Voucher giảm giá 1-chạm** tự động tính toán mã tối ưu nhất.
  - **Thuật toán Gợi Ý Món Động (Dynamic Smart Upsell)**: Tự động phân tích ngữ cảnh giỏ hàng (chưa có nước ➔ gợi ý đồ uống, có cơm ➔ gợi ý trứng/canh, có gà chiên ➔ gợi ý khoai/salad).
- **Thanh Toán Đa Dạng & Tiện Lợi**:
  - Thanh toán khi nhận hàng (COD).
  - **VietQR Động**: Tự động sinh mã QR chuẩn Napas247 với chính xác số tiền và cú pháp mã đơn hàng.
  - Hỗ trợ ví điện tử MoMo, ZaloPay.
- **Tra Cứu Đơn Hàng Trực Tuyến (`/tra-cuu-don`)**: Khách hàng có thể tra cứu hành trình chuẩn bị món và giao hàng theo Số điện thoại hoặc Mã đơn hàng theo thời gian thực.

---

### 📊 2. Dành Cho Quản Trị Nhà Hàng (Admin Dashboard)
- **Tiếp Nhận Đơn Hàng Thời Gian Thực**:
  - **Chuông âm thanh + Thông báo Popup** nổi bật ngay khi có đơn hàng mới (Realtime Polling).
  - Chuyển trạng thái đơn hàng 1-chạm (Chờ duyệt ➔ Đang làm ➔ Đang giao ➔ Hoàn thành).
  - Huỷ đơn hàng có ghi nhận lý do rõ ràng.
  - In phiếu chế biến / hoá đơn và xuất báo cáo doanh thu ra file **Excel / CSV**.
- **Quản Lý Thực Đơn & Giá Bán Tối Ưu (3-Giây Tác Vụ)**:
  - Sửa giá bán trực tiếp trên bảng và **tự động lưu khi ấn Enter hoặc click ra ngoài**.
  - Bật / Tắt trạng thái mở bán (`Đang bán` ⇄ `Hết món`) tức thì không cần tải lại trang.
  - Bật / Tắt quyền gợi ý giỏ hàng (`⭐ Gợi ý` / `☆ Tắt`) cho từng món.
  - Hỗ trợ thao tác hàng loạt (**Bulk Actions**): Mở bán, Hết món, Bật/Tắt gợi ý, Xoá món.
- **Quản Lý Danh Mục, Vị Sốt & Topping**: Toàn quyền thêm, sửa, phân loại và sắp xếp thứ tự hiển thị.
- **Quản Lý Mã Giảm Giá (Coupons)**: Thiết lập mã giảm theo % hoặc số tiền cố định, giá trị đơn tối thiểu, giới hạn mức giảm tối đa.
- **Quản Lý Nội Dung Trang Chủ**: Tùy chỉnh Banner Hero, khối Cam kết chất lượng, Feedback đánh giá của khách hàng.
- **Cài Đặt Cửa Hàng & Tích Hợp**:
  - Thông tin liên hệ, giờ mở - đóng cửa, địa chỉ quán.
  - Bảng giá phí giao hàng linh hoạt theo từng quận/khu vực.
  - Cấu hình thông tin tài khoản ngân hàng thụ hưởng VietQR.
  - **Tích hợp Telegram Bot**: Tự động bắn tin nhắn báo đơn về nhóm chat Telegram của chủ quán / nhân viên bếp.

---

## 🛠️ Công Nghệ Sử Dụng (Tech Stack)

| Thành phần | Công nghệ |
| :--- | :--- |
| **Backend Framework** | Laravel 12 (PHP 8.4) |
| **Frontend Reactive** | Alpine.js 3.x + Vanilla JS Store |
| **UI Styling** | TailwindCSS v4 + Blade Template Engine |
| **Database** | MySQL 8.0 / MariaDB |
| **Testing Suite** | Pest PHP 3.x (58+ Feature Tests, Pass 100%) |
| **Code Formatter** | Laravel Pint (Chuẩn PSR-12 / Laravel Style) |
| **Integrations** | VietQR API, Telegram Bot Webhook, GitHub CI/CD Deploy Webhook |

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Cục Bộ

### Yêu cầu môi trường:
- PHP >= 8.2 (Khuyên dùng PHP 8.4)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL >= 8.0 hoặc MariaDB
- Tiện ích mở rộng PHP: `bcmath`, `curl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`

### Các bước cài đặt:

```bash
# 1. Clone mã nguồn về máy
git clone https://github.com/luuhoang-star/web_food_chicken.git
cd web_food_chicken

# 2. Cài đặt các gói phụ thuộc PHP và JavaScript
composer install
npm install

# 3. Tạo file cấu hình môi trường (.env)
cp .env.example .env
php artisan key:generate

# 4. Cấu hình kết nối Cơ sở dữ liệu trong file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=chicken_db
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Chạy Migration và nạp dữ liệu mẫu đầy đủ (Thực đơn, Sốt, Topping, Đơn hàng, Tài khoản Admin)
php artisan migrate:fresh --seed

# 6. Biên dịch giao diện Frontend
npm run build
# hoặc chạy chế độ phát triển:
npm run dev

# 7. Khởi chạy máy chủ nội bộ
php artisan serve
```

> Nếu sử dụng **Laravel Herd** hoặc **Laragon / Valet**, bạn có thể truy cập trực tiếp qua domain ảo: `http://chicken.test`

---

## 🔐 Tài Khoản Quản Trị Mặc Định

Sau khi chạy lệnh `php artisan db:seed`, bạn có thể đăng nhập vào trang quản trị:

- **Đường dẫn Admin**: `/admin/login` (hoặc `http://chicken.test/admin`)
- **Email đăng nhập**: `admin@gao.vn`
- **Mật khẩu**: `password` (hoặc `admin123`)

---

## 🧪 Kiểm Thử & Đảm Bảo Chất Lượng

Dự án được bao phủ toàn diện với bộ test Feature Tests (Pest PHP):

```bash
# Chạy toàn bộ test suite
php artisan test

# Chạy test với định dạng rút gọn
php artisan test --compact

# Kiểm tra và tự động định dạng mã nguồn theo chuẩn Laravel Pint
vendor/bin/pint --format agent
```

---

## 📂 Cấu Trúc Thư Mục Chính

```text
app/
├── Http/Controllers/
│   ├── Admin/                  # Bộ điều khiển dành cho trang Quản trị (Orders, Products, Sauces, Settings...)
│   ├── HomeController.php      # Trang chủ & Giới thiệu
│   ├── MenuController.php      # Thực đơn & Đặt món
│   ├── OrderController.php     # Xử lý tạo đơn hàng & Áp dụng coupon
│   └── OrderTrackingController.php # Tra cứu hành trình đơn
├── Models/                     # Eloquent Models (Order, Product, Sauce, Coupon, SiteSetting...)
├── Services/                   # Business Logic & Telegram Notification Service
└── View/Composers/             # GaoStoreComposer nạp dữ liệu toàn cục tối ưu cache

resources/
├── views/
│   ├── admin/                  # Giao diện quản trị Admin POS
│   ├── layouts/                # Base layouts (app, admin)
│   ├── modals/                 # Modal tuỳ chỉnh món, giỏ hàng (Cart Drawer), checkout
│   ├── pages/                  # Trang chủ, Thực đơn, Tra cứu đơn
│   └── sections/               # Các khối thành phần giao diện

public/
├── js/gao-store.js             # Alpine.js Global State & Smart Dynamic Upsell Engine
└── images/                     # Tài nguyên hình ảnh món ăn & banner
```

---

## 📄 Bản Quyền & Giấy Phép

Dự án được phát triển và phát hành dưới giấy phép mã nguồn mở **[MIT License](LICENSE)**.

---
<p align="center">Được phát triển với đam mê dành cho ẩm thực gà sốt hảo hạng 🍗✨</p>
