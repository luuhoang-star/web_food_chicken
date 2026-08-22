<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // e.g. GAO-839210
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('district'); // Quận Cầu Giấy...
            $table->string('address'); // Số nhà, ngõ, toà nhà
            $table->text('driver_note')->nullable(); // Ghi chú cho tài xế
            $table->string('payment_method')->default('cod'); // cod, momo, vnpay, zalopay
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('order_status')->default('pending'); // pending, confirmed, cooking, delivering, completed, cancelled
            $table->decimal('subtotal', 12, 0)->default(0); // Tạm tính
            $table->decimal('shipping_fee', 12, 0)->default(0); // Phí ship
            $table->decimal('discount', 12, 0)->default(0);
            $table->decimal('total_amount', 12, 0)->default(0); // Tổng thanh toán
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
