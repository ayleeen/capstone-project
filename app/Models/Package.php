<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'package_name',
        'price',
        'category',
        'guest_min',
        'guest_max',
        'description',
        'status',
        'photo',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
    ];
}