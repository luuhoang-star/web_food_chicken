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
        if (! Schema::hasTable('heroes')) {
            Schema::create('heroes', function (Blueprint $table) {
                $table->id();
                $table->string('badge')->nullable()->default('✦ GÀ CHIÊN + SỐT ĐẬM VỊ');
                $table->string('title')->default('GÀ GIÒN.');
                $table->string('title_highlight')->nullable()->default('SỐT ĐẬM.');
                $table->text('subtitle')->nullable();
                $table->string('cta_primary_text')->default('🍗 ĐẶT MÓN NGAY');
                $table->string('cta_primary_url')->default('/menu');
                $table->string('cta_secondary_text')->nullable()->default('🔥 XEM 4 VỊ SỐT');
                $table->string('cta_secondary_url')->nullable()->default('/menu');
                $table->string('delivery_time')->nullable()->default('Giao 25–40p');
                $table->string('hot_status')->nullable()->default('Luôn nóng giòn');
                $table->string('rating')->nullable()->default('Đánh giá 4.9/5');
                $table->string('image')->nullable();
                $table->decimal('price', 12, 0)->default(49000);
                $table->string('floating_badge')->nullable()->default('🔥 MÓN MỚI RA MẮT • ĐẶT NGAY');
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
        Schema::dropIfExists('heroes');
    }
};
