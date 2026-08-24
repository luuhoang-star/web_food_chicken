<?php

namespace App\Providers;

use App\View\Composers\GaoStoreComposer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', GaoStoreComposer::class);

        // Tự động kiểm tra và khởi tạo bảng coupons nếu chưa có trong Database
        try {
            if (! Schema::hasTable('coupons')) {
                Schema::create('coupons', function (Blueprint $table) {
                    $table->id();
                    $table->string('code')->unique();
                    $table->string('name');
                    $table->string('type')->default('fixed');
                    $table->decimal('value', 12, 0);
                    $table->decimal('min_order_amount', 12, 0)->default(0);
                    $table->decimal('max_discount', 12, 0)->nullable();
                    $table->integer('usage_limit')->nullable();
                    $table->integer('used_count')->default(0);
                    $table->timestamp('expires_at')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });

                DB::table('coupons')->insertOrIgnore([
                    [
                        'code' => 'GAO15K',
                        'name' => 'Ưu đãi đơn đầu tiên cho bạn mới',
                        'type' => 'fixed',
                        'value' => 15000,
                        'min_order_amount' => 80000,
                        'max_discount' => null,
                        'usage_limit' => 100,
                        'used_count' => 0,
                        'expires_at' => now()->addMonths(6),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'code' => 'FREESHIP50',
                        'name' => 'Miễn phí giao hàng đơn từ 120k',
                        'type' => 'fixed',
                        'value' => 20000,
                        'min_order_amount' => 120000,
                        'max_discount' => null,
                        'usage_limit' => 200,
                        'used_count' => 0,
                        'expires_at' => now()->addMonths(12),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            // Log if needed
        }
    }
}
