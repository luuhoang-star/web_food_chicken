<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sauce_id',
        'sauce_selection', // 'none', 'fixed', 'required'
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

    public function getTagAttribute(?string $value): ?string
    {
        if ($value === 'MỚI' || in_array($value, ['MÓN HOT', 'ĐƯỢC YÊU THÍCH', 'BÁN CHẠY', 'BÁN CHẠY NHẤT', 'THANH MÁT', 'THANH VỊ', 'ĂN VẶT'])) {
            return null;
        }

        return $value;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sauce(): BelongsTo
    {
        return $this->belongsTo(Sauce::class);
    }

    public function sauces(): BelongsToMany
    {
        return $this->belongsToMany(Sauce::class, 'product_sauces')->withTimestamps();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function requiresSauceChoice(): bool
    {
        return $this->sauce_selection === 'required';
    }

    public function hasFixedSauce(): bool
    {
        return $this->sauce_selection === 'fixed';
    }

    public function hasNoSauce(): bool
    {
        return $this->sauce_selection === 'none';
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeHot($query)
    {
        return $query->where('is_hot', true);
    }

    public function scopeCombos($query)
    {
        return $query->whereHas('category', fn ($q) => $q->where('slug', 'combo'));
    }

    public function scopeUpsell($query)
    {
        return $query->whereHas('category', fn ($q) => $q->whereIn('slug', ['drink', 'side']));
    }
}
