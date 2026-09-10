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
        'item_type',
        'product_id',
        'sauce_id',
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

    public function getFormattedToppingsAttribute(): string
    {
        if (empty($this->toppings)) {
            return '';
        }

        if (is_array($this->toppings)) {
            $names = array_filter(array_map(function ($t) {
                if (is_array($t)) {
                    return $t['name'] ?? '';
                }

                return is_string($t) ? $t : '';
            }, $this->toppings));

            return implode(', ', $names);
        }

        return (string) $this->toppings;
    }

    public function sauceModel(): BelongsTo
    {
        return $this->belongsTo(Sauce::class, 'sauce_id');
    }
}
