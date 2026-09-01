<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subtag',
        'description',
        'price',
        'original_price',
        'image',
        'tag',
        'rating',
        'review_count',
        'is_hot',
        'is_active',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'original_price' => 'decimal:0',
        'rating' => 'decimal:1',
        'review_count' => 'integer',
        'is_hot' => 'boolean',
        'is_active' => 'boolean',
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

    public function items(): HasMany
    {
        return $this->hasMany(ComboItem::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
