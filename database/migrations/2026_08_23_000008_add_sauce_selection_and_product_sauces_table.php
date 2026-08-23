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
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'sauce_selection')) {
                $table->enum('sauce_selection', ['none', 'fixed', 'required'])->default('none')->after('sauce_id');
            }
        });

        if (! Schema::hasTable('product_sauces')) {
            Schema::create('product_sauces', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('sauce_id')->constrained('sauces')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_id', 'sauce_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sauces');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sauce_selection')) {
                $table->dropColumn('sauce_selection');
            }
        });
    }
};
