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
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'is_active' => 'boolean',
    ];
}
