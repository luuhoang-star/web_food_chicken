<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\ComboAdminController;
use App\Http\Controllers\Admin\ContentAdminController;
use App\Http\Controllers\Admin\CouponAdminController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ProfileAdminController;
use App\Http\Controllers\Admin\SauceAdminController;
use App\Http\Controllers\Admin\SettingAdminController;
use App\Http\Controllers\DeployWebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - GAO Gà Sốt & Cơm Hà Nội
|--------------------------------------------------------------------------
| 1. Home ('/') -> Khám phá thương hiệu, món hot, combo, review
| 2. Menu ('/menu') -> Thực đơn đặt món chính thức
| 3. Product ('/product/{slug}') -> Xem chi tiết 1 món và tuỳ chỉnh
| 4. Orders API ('/api/orders') -> Đặt hàng và lưu giỏ hàng
| 5. Order Tracking ('/tra-cuu-don') -> Tra cứu trạng thái đơn hàng
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/cam-ket', [HomeController::class, 'quality'])->name('quality');
Route::get('/tra-cuu-don', [OrderTrackingController::class, 'index'])->name('order.tracking');

// Redirect any old sauce routes directly to Menu
Route::get('/sot', fn () => redirect()->route('menu'))->name('sauces.index');
Route::get('/sot/{slug}', fn (string $slug) => redirect()->route('menu', ['sauce' => $slug]))->name('sauces.show');
Route::get('/sauces', fn () => redirect()->route('menu'))->name('sauces');
Route::get('/sauces/{slug}', fn (string $slug) => redirect()->route('menu', ['sauce' => $slug]));

Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/api/orders', [OrderController::class, 'store'])->name('orders.store');
Route::post('/api/coupons/apply', [OrderController::class, 'applyCoupon'])->name('coupons.apply');

// Automated Deployment Webhook (GitHub / CI/CD)
Route::match(['get', 'post'], '/webhook/deploy', [DeployWebhookController::class, 'handle'])->name('webhook.deploy');

/*
|--------------------------------------------------------------------------
| Admin Management Routes ('/admin')
|--------------------------------------------------------------------------
*/

// Admin Guest Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Authenticated Routes
    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // 1. Quản lý Đơn hàng & API chuông báo & Xuất Excel
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [OrderAdminController::class, 'exportCsv'])->name('orders.export');
        Route::patch('/orders/{id}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('/api/orders/check-new', [OrderAdminController::class, 'checkNewOrders'])->name('orders.check-new');

        // 2. Quản lý Món ăn & Giá bán (Full CRUD)
        Route::get('/products', [ProductAdminController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductAdminController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductAdminController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductAdminController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductAdminController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductAdminController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{id}/price', [ProductAdminController::class, 'updatePrice'])->name('products.update-price');
        Route::patch('/products/{id}/toggle-availability', [ProductAdminController::class, 'toggleAvailability'])->name('products.toggle-availability');
        Route::post('/products/bulk-action', [ProductAdminController::class, 'bulkAction'])->name('products.bulk-action');

        // 3. Quản lý Danh mục món ăn (Categories CRUD)
        Route::get('/categories', [CategoryAdminController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryAdminController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryAdminController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryAdminController::class, 'destroy'])->name('categories.destroy');
        Route::patch('/categories/{id}/toggle', [CategoryAdminController::class, 'toggle'])->name('categories.toggle');

        // 4. Quản lý Combo món ăn (Combos CRUD)
        Route::get('/combos', [ComboAdminController::class, 'index'])->name('combos.index');
        Route::get('/combos/create', [ComboAdminController::class, 'create'])->name('combos.create');
        Route::post('/combos', [ComboAdminController::class, 'store'])->name('combos.store');
        Route::get('/combos/{id}/edit', [ComboAdminController::class, 'edit'])->name('combos.edit');
        Route::put('/combos/{id}', [ComboAdminController::class, 'update'])->name('combos.update');
        Route::delete('/combos/{id}', [ComboAdminController::class, 'destroy'])->name('combos.destroy');
        Route::patch('/combos/{id}/toggle', [ComboAdminController::class, 'toggle'])->name('combos.toggle');

        // 5. Quản lý Vị sốt & Topping
        Route::get('/sauces', [SauceAdminController::class, 'index'])->name('sauces.index');
        Route::patch('/sauces/{id}', [SauceAdminController::class, 'updateSauce'])->name('sauces.update');
        Route::patch('/toppings/{id}', [SauceAdminController::class, 'updateTopping'])->name('toppings.update');
        Route::patch('/toppings/{id}/toggle', [SauceAdminController::class, 'toggleTopping'])->name('toppings.toggle');

        // 6. Quản lý Mã giảm giá & Voucher (Coupons CRUD)
        Route::get('/coupons', [CouponAdminController::class, 'index'])->name('coupons.index');
        Route::post('/coupons', [CouponAdminController::class, 'store'])->name('coupons.store');
        Route::put('/coupons/{id}', [CouponAdminController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{id}', [CouponAdminController::class, 'destroy'])->name('coupons.destroy');
        Route::patch('/coupons/{id}/toggle', [CouponAdminController::class, 'toggle'])->name('coupons.toggle');

        // 7. Quản lý Nội dung trang chủ & Đánh giá
        Route::get('/content', [ContentAdminController::class, 'index'])->name('content.index');
        Route::post('/content/hero', [ContentAdminController::class, 'updateHero'])->name('content.hero.update');
        Route::patch('/content/benefit/{id}', [ContentAdminController::class, 'updateBenefit'])->name('content.benefit.update');
        Route::post('/content/testimonial', [ContentAdminController::class, 'storeTestimonial'])->name('content.testimonial.store');
        Route::put('/content/testimonial/{id}', [ContentAdminController::class, 'updateTestimonial'])->name('content.testimonial.update');
        Route::delete('/content/testimonial/{id}', [ContentAdminController::class, 'deleteTestimonial'])->name('content.testimonial.delete');

        // 8. Quản lý Cài đặt quán, Giao hàng, VietQR, SEO & Telegram
        Route::get('/settings', [SettingAdminController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingAdminController::class, 'update'])->name('settings.update');
        Route::patch('/settings/toggle-store-status', [SettingAdminController::class, 'toggleStoreStatus'])->name('settings.toggle-store-status');
        Route::post('/settings/test-telegram', [SettingAdminController::class, 'testTelegram'])->name('settings.test-telegram');

        // 9. Tài khoản cá nhân & Đổi mật khẩu
        Route::get('/profile', [ProfileAdminController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ProfileAdminController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [ProfileAdminController::class, 'updatePassword'])->name('profile.password');
    });
});
