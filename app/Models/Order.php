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
}
