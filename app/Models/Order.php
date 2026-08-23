<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_phone',
        'district',
        'address',
        'driver_note',
        'payment_method',
        'payment_status',
        'order_status',
        'subtotal',
        'shipping_fee',
        'discount',
        'total_amount',
    ];

    protected $casts = [
        'subtotal' => 'decimal:0',
        'shipping_fee' => 'decimal:0',
        'discount' => 'decimal:0',
        'total_amount' => 'decimal:0',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->order_status) {
            'pending' => 'Đã đặt đơn',
            'confirmed' => 'Đã xác nhận',
            'preparing', 'processing' => 'Đang chuẩn bị',
            'delivering', 'shipping' => 'Đang giao hàng',
            'completed' => 'Đã giao thành công',
            'cancelled' => 'Đã hủy đơn',
            default => 'Đang xử lý',
        };
    }

    public function getStatusStepAttribute(): int
    {
        return match ($this->order_status) {
            'pending' => 1,
            'confirmed' => 2,
            'preparing', 'processing' => 3,
            'delivering', 'shipping' => 4,
            'completed' => 5,
            default => 1,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->order_status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'preparing', 'processing' => 'bg-orange-100 text-orange-800 border-orange-200',
            'delivering', 'shipping' => 'bg-blue-100 text-blue-800 border-blue-200',
            'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match (strtolower((string) $this->payment_method)) {
            'cod' => 'Tiền mặt khi nhận hàng (COD)',
            'momo' => 'Ví điện tử MoMo',
            'vnpay' => 'VNPay QR',
            'zalopay' => 'Ví ZaloPay',
            'bank_transfer' => 'Chuyển khoản Ngân hàng',
            default => strtoupper((string) $this->payment_method),
        };
    }
}
