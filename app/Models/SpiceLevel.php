<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpiceLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'level',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('level', 'asc')->orderBy('id', 'asc');
    }
}
