<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'avatar',
        'avatar_bg',
        'content',
        'rating',
        'location',
        'verified',
        'is_active',
        'order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function getCommentAttribute(): string
    {
        return (string) ($this->content ?? '');
    }

    public function setCommentAttribute(string $value): void
    {
        $this->content = $value;
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
