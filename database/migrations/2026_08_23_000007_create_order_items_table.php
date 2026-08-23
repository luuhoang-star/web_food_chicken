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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('item_type')->default('product'); // 'product', 'sauce', 'combo'
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('sauce_id')->nullable()->constrained('sauces')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('price', 12, 0)->default(0); // Đơn giá
            $table->integer('quantity')->default(1);
            $table->string('sauce')->nullable(); // Vị sốt đã chọn (nếu có)
            $table->string('spice_level')->nullable(); // Độ cay (nếu có)
            $table->json('toppings')->nullable(); // Toppings mảng JSON
            $table->text('note')->nullable(); // Ghi chú cho quán
            $table->decimal('total_item_price', 12, 0)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
