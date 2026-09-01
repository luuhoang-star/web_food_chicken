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
        if (! $value) {
            return null;
        }

        $upper = strtoupper(trim($value));
        if (in_array($upper, ['BEST SELLER', 'BESTSELLER', 'BÁN CHẠY', 'BÁN CHẠY NHẤT'])) {
            return 'BEST SELLER';
        }

        if (in_array($upper, ['TIẾT KIỆM', 'COMBO TIẾT KIỆM', 'SIÊU TIẾT KIỆM'])) {
            return 'TIẾT KIỆM';
        }

        return null;
    }

    public function setTagAttribute(?string $value): void
    {
        if (! $value) {
            $this->attributes['tag'] = null;

            return;
        }

        $upper = strtoupper(trim($value));
        if (in_array($upper, ['BEST SELLER', 'BESTSELLER', 'BÁN CHẠY', 'BÁN CHẠY NHẤT'])) {
            $this->attributes['tag'] = 'BEST SELLER';
        } elseif (in_array($upper, ['TIẾT KIỆM', 'COMBO TIẾT KIỆM', 'SIÊU TIẾT KIỆM'])) {
            $this->attributes['tag'] = 'TIẾT KIỆM';
        } else {
            $this->attributes['tag'] = null;
        }
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

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('images/placeholder.jpg');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        return asset('images/products/'.$this->image);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeHot($query)
    {
        return $query->where('is_hot', true);
    }

    public function scopeBestSeller($query, int $limit = 8)
    {
        return $query->available()
            ->withSum(['orderItems as sold_count' => function ($q) {
                $q->whereHas('order', fn ($o) => $o->where('order_status', '!=', 'cancelled'));
            }], 'quantity')
            ->orderByRaw('CASE WHEN tag = "BEST SELLER" THEN 1 ELSE 0 END DESC')
            ->orderByRaw('COALESCE(sold_count, 0) DESC')
            ->orderBy('order')
            ->orderBy('id')
            ->limit($limit);
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
