<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'is_active' => 'boolean',
    ];
}
