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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('sauce_id')->nullable()->constrained('sauces')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 0)->default(0);
            $table->decimal('original_price', 12, 0)->nullable();
            $table->string('image')->nullable();
            $table->string('tag')->nullable(); // BEST SELLER, MỚI, TIẾT KIỆM
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->integer('review_count')->default(0);
            $table->string('subtag')->nullable(); // 🍚 Cơm dẻo + Gà sốt cay
            $table->string('default_sauce')->nullable(); // Tên vị sốt mặc định
            $table->boolean('is_hot')->default(false); // Hiển thị ở mục Món Được Gọi Nhiều
            $table->boolean('is_available')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
