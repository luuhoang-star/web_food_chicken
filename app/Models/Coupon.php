<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:0',
        'min_order_amount' => 'decimal:0',
        'max_discount' => 'decimal:0',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValidFor(float $subtotal): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'Mã giảm giá này hiện không còn hiệu lực.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'Mã giảm giá này đã hết hạn sử dụng.'];
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Mã giảm giá này đã hết số lượt sử dụng.'];
        }

        if ($this->min_order_amount > 0 && $subtotal < $this->min_order_amount) {
            return [
                'valid' => false,
                'message' => 'Đơn hàng chưa đạt mức tối thiểu ' . number_format($this->min_order_amount, 0, ',', '.') . 'đ để dùng mã này.',
            ];
        }

        return ['valid' => true, 'message' => 'Áp dụng mã giảm giá thành công!'];
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percent') {
            $discount = ($subtotal * $this->value) / 100;
            if ($this->max_discount && $this->max_discount > 0 && $discount > $this->max_discount) {
                return (float) $this->max_discount;
            }
            return (float) $discount;
        }

        return (float) min($this->value, $subtotal);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            });
    }
}
