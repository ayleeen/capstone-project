<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'is_active',
        'image_path',
        'prep_time',
        'spicy_level',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}