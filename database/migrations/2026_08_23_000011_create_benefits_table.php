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
        if (! Schema::hasTable('benefits')) {
            Schema::create('benefits', function (Blueprint $table) {
                $table->id();
                $table->string('icon')->default('⏱️');
                $table->string('color_class')->nullable()->default('bg-red-50 text-red-600');
                $table->string('title');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('benefits');
    }
};
