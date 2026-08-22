<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'image',
        'tag',
        'rating',
        'review_count',
        'subtag',
        'default_sauce',
        'is_hot',
        'is_available',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'original_price' => 'decimal:0',
        'rating' => 'decimal:1',
        'review_count' => 'integer',
        'is_hot' => 'boolean',
        'is_available' => 'boolean',
        'order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
