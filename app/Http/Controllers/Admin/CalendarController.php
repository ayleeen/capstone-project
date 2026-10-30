<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class CalendarController extends Controller
{
    // Calendar lang ang may "baptismal" at "private"; iba ang tawag sa database
    private const TYPE_MAP = [
        'christening' => 'baptismal',
        'debut'       => 'private',
        'other'       => 'private',
    ];

    public function index()
    {
        $events = Reservation::orderBy('start_date')->get()->map(function ($r) {
            return [
                'id'      => $r->id,
                'name'    => $r->event_title,
                'client'  => $r->full_name,
                'type'    => self::TYPE_MAP[$r->event_type] ?? $r->event_type,
                'date'    => $r->start_date->format('Y-m-d'),
                'endDate' => ($r->end_date ?? $r->start_date)->format('Y-m-d'),
                'start'   => substr($r->start_time ?: '08:00', 0, 5),
                'end'     => substr($r->end_time ?: '17:00', 0, 5),
                'guests'  => (int) $r->guests,
                'status'  => $r->status,
                'venue'   => $r->location,
                'pkg'     => $r->package,
                'sales'   => (float) $r->total_amount,
            ];
        });

        return view('admin.calendar', compact('events'));
    }
}