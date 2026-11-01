<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class MenuItem extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'is_active',
        'is_addon',
        'image_path',
        'prep_time',
        'spicy_level',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_addon' => 'boolean',
    ];
    public function addonPrices()
{
    return $this->hasMany(AddonPrice::class);
}
}
