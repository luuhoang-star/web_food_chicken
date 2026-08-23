<?php

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
