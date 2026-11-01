<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;   
use App\Traits\Auditable;
class Reservation extends Model
{
use SoftDeletes, Auditable; 
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'event_type',
        'event_title',
        'location',
        'landmark',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'guests',
        'status',
        'payment_status',
        'package',
        'package_id',
        'food_restriction',
        'total_amount',
        'down_payment',
        'proof_of_payment',
'notes','package','package_id','price_per_head','custom_menu','menu_sets','addons','food_restriction',
    ];


protected $casts = [
    'start_date' => 'date:F j, Y',
    'end_date' => 'date:F j, Y',
    'price_per_head' => 'integer',
    'custom_menu' => 'array',
    'menu_sets' => 'array',
    'addons' => 'array',
];


    public static function monthlyStats(): array
    {
        // Assumed created_at; switch to start_date if your reservation page counts that way
        $month = static::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);

        return [
            'total'     => (clone $month)->count(),
            'pending'   => (clone $month)->where('status', 'pending')->count(),
            'confirmed' => (clone $month)->where('status', 'confirmed')->count(),
            'cancelled' => (clone $month)->where('status', 'cancelled')->count(),
            'sales'     => (clone $month)->where('status', 'confirmed')->sum('total_amount'),
            'upcoming'  => static::where('status', '!=', 'cancelled')
                                ->whereBetween('start_date', [now()->startOfDay(), now()->endOfWeek()])
                                ->count(),
        ];
    }

    const DAILY_CAPACITY = 1500;

// ['2026-10-20' => 1200, '2026-10-25' => 1500, ...]
public static function bookedGuestsByDate(): array
{
    return static::where('status', '!=', 'cancelled')
        ->selectRaw('DATE(start_date) as d, SUM(guests) as total')
        ->groupBy('d')
        ->pluck('total', 'd')
        ->map(fn ($v) => (int) $v)
        ->all();
}

public static function remainingCapacity(string $date, ?int $exceptId = null): int
{
    $booked = static::where('status', '!=', 'cancelled')
        ->whereDate('start_date', $date)
        ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
        ->sum('guests');

    return max(0, self::DAILY_CAPACITY - (int) $booked);
}
}