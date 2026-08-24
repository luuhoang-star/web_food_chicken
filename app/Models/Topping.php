<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'icon',
        'is_active',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'is_active' => 'boolean',
    ];

    public function getIsAvailableAttribute(): bool
    {
        return (bool) ($this->is_active ?? true);
    }

    public function setIsAvailableAttribute(bool $value): void
    {
        $this->attributes['is_active'] = $value;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('id', 'asc');
    }
}
