<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'dob', 'address', 'city', 'province', 'zip',
        'customer_type', 'preferred_contact_method', 'status', 'notes',
    ];

    protected $casts = [
        'notes' => 'array',
        'dob'   => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}