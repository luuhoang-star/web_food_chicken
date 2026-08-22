<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'sauce',
        'spice_level',
        'toppings',
        'note',
        'total_item_price',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'quantity' => 'integer',
        'toppings' => 'array',
        'total_item_price' => 'decimal:0',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
