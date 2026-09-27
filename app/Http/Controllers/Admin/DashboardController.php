<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
{
    $month = Reservation::whereMonth('start_date', now()->month)
                        ->whereYear('start_date', now()->year);

    $stats = [
        'total'     => (clone $month)->count(),
        'pending'   => (clone $month)->where('status', 'pending')->count(),
        'confirmed' => (clone $month)->where('status', 'confirmed')->count(),
        'cancelled' => (clone $month)->where('status', 'cancelled')->count(),
        'sales'     => (clone $month)->where('status', 'confirmed')->sum('total_amount'),
        'upcoming'  => Reservation::whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])
                                  ->where('status', '!=', 'cancelled')->count(),
    ];

    $totalForStatus = $stats['confirmed'] + $stats['pending'] + $stats['cancelled'];

    $statusBreakdown = [
        'confirmed' => [
            'count'   => $stats['confirmed'],
            'percent' => $totalForStatus > 0 ? round($stats['confirmed'] / $totalForStatus * 100) : 0,
        ],
        'pending' => [
            'count'   => $stats['pending'],
            'percent' => $totalForStatus > 0 ? round($stats['pending'] / $totalForStatus * 100) : 0,
        ],
        'cancelled' => [
            'count'   => $stats['cancelled'],
            'percent' => $totalForStatus > 0 ? round($stats['cancelled'] / $totalForStatus * 100) : 0,
        ],
    ];

    return view('admin.dashboard', compact('stats', 'statusBreakdown'));
}
}