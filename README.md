# 🍗 GAO - Gà Sốt & Cơm Hà Nội

> Hệ thống website đặt món trực tuyến & quản trị nhà hàng F&B hiện đại (Storefront + Admin POS).

<p align="left">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpinedotjs&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/TailwindCSS-v4-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Pest_PHP-100%25_Passed-00D8A5?style=flat-square" alt="Pest PHP">
</p>

---

## ⚡ Tính Năng Cốt Lõi

| Phân hệ | Tính năng nổi bật |
| :--- | :--- |
| **🛍️ Khách Hàng (Storefront)** | • Giao diện chuẩn Mobile-First, tốc độ tải tức thì.<br>• Tùy chọn vị sốt, cấp độ cay, topping, combo & hũ sốt lẻ.<br>• Giỏ hàng lưu `localStorage`, thanh tiến trình Freeship & gợi ý Voucher.<br>• **Thuật toán Gợi ý món động (Smart Upsell)** theo ngữ cảnh giỏ hàng.<br>• Thanh toán linh hoạt: COD, **VietQR động** (tự tạo mã QR chuẩn số tiền), MoMo.<br>• Tra cứu hành trình đơn hàng trực tuyến (`/tra-cuu-don`). |
| **📊 Quản Trị (Admin POS)** | • **Chuông âm thanh + Popup** thông báo đơn mới tức thời (Realtime polling).<br>• Xử lý đơn hàng, in phiếu chế biến & xuất báo cáo **Excel / CSV**.<br>• Sửa giá nhanh tự lưu, bật/tắt **Hết món** & **Gợi ý Upsell** 1-chạm.<br>• Quản lý thực đơn, danh mục, vị sốt, topping & mã giảm giá (Coupons).<br>• Tích hợp **Telegram Bot** tự động gửi thông báo đơn về điện thoại. |

---

## 🚀 Cài Đặt Nhanh (Quick Start)

```bash
# 1. Clone repo & cài đặt dependencies
git clone https://github.com/luuhoang-star/web_food_chicken.git
cd web_food_chicken
composer install && npm install

# 2. Cấu hình môi trường & tạo App Key
cp .env.example .env
php artisan key:generate

# 3. Chạy migration & nạp dữ liệu mẫu
php artisan migrate --seed

# 4. Build giao diện & khởi chạy
npm run build
php artisan serve
```

> Hoặc truy cập trực tiếp qua domain ảo Herd / Laragon: `http://chicken.test`

---

## 🔐 Tài Khoản Quản Trị

- **URL Admin**: `/admin/login` (hoặc `http://chicken.test/admin`)
- **Email**: `admin@gao.vn`
- **Mật khẩu**: `password`

---

## 🧪 Kiểm Thử & Định Dạng Code

```bash
php artisan test --compact       # Chạy 58+ Feature Tests (Pest PHP)
vendor/bin/pint --format agent   # Chuẩn hóa code theo Laravel Pint
```

---

## 📄 Bản Quyền
Phát hành dưới giấy phép mã nguồn mở **[MIT License](LICENSE)**.
