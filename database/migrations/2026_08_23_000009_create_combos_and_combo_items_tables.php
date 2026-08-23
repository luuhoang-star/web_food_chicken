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
        if (! Schema::hasTable('combos')) {
            Schema::create('combos', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('subtag')->nullable();
                $table->text('description')->nullable();
                $table->decimal('price', 12, 0)->default(0);
                $table->decimal('original_price', 12, 0)->nullable();
                $table->string('image')->nullable();
                $table->string('tag')->nullable();
                $table->decimal('rating', 2, 1)->default(5.0);
                $table->integer('review_count')->default(0);
                $table->boolean('is_hot')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('combo_items')) {
            Schema::create('combo_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('combo_id')->constrained('combos')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('item_name');
                $table->integer('quantity')->default(1);
                $table->string('note')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_items');
        Schema::dropIfExists('combos');
    }
};
