<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'event_type',
        'event_title',
        'location',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'guests',
        'status',
        'payment_status',
        'package',
        'total_amount',
        'notes',
    ];

    protected $casts = [
    'start_date' => 'date:F j, Y',
    'end_date'   => 'date:F j, Y',
    ];
}