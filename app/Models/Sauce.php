<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sauce extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'tag',
        'subtitle',
        'short_desc',
        'description',
        'image',
        'price',
        'is_available',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function supportedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_sauces')->withTimestamps();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTaglineAttribute(): string
    {
        return $this->subtitle ?? $this->short_desc ?? '';
    }

    public function setTaglineAttribute(string $value): void
    {
        $this->subtitle = $value;
        $this->short_desc = $value;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('id', 'asc');
    }
}
