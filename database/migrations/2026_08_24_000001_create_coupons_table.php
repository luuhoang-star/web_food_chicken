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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // VD: GAO20K, FREESHIP50
            $table->string('name'); // Tên chương trình ưu đãi
            $table->string('type')->default('fixed'); // 'fixed' (trừ tiền) hoặc 'percent' (trừ %)
            $table->decimal('value', 12, 0); // Số tiền giảm (vd: 20000) hoặc % (vd: 10)
            $table->decimal('min_order_amount', 12, 0)->default(0); // Giá trị đơn tối thiểu để áp dụng
            $table->decimal('max_discount', 12, 0)->nullable(); // Giảm tối đa (áp dụng cho loại percent)
            $table->integer('usage_limit')->nullable(); // Giới hạn lượt sử dụng
            $table->integer('used_count')->default(0); // Số lượt đã dùng
            $table->timestamp('expires_at')->nullable(); // Hạn sử dụng
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
