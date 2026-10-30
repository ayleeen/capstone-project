<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use App\Traits\Auditable;

class Package extends Model
{
    
    use SoftDeletes, Auditable; 
    protected $fillable = [
        'package_name',
        'price',
        'category',
        'guest_min',
        'guest_max',
        'description',
        'status',
        'photo',
        'inclusions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
        'inclusions' => 'array',
    ];

        public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_package')
                    ->withTimestamps();
    }   
}