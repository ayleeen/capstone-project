<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Reservation::monthlyStats();

        $upcomingEvents = Reservation::where('status', '!=', 'cancelled')
            ->whereBetween('start_date', [now()->startOfDay(), now()->endOfWeek()])
            ->orderBy('start_date')->orderBy('start_time')
            ->take(5)->get();

        $recentReservations = Reservation::latest()->take(5)->get();

        $active = Reservation::where('status', '!=', 'cancelled')->get();
        $calendarEvents = $active->groupBy(fn ($r) => $r->start_date->format('Y-m-d'))
            ->map(fn ($day, $date) => [
                'date'   => $date,
                'status' => $day->count() > 1 ? 'conflict' : $day->first()->status,
                'titles' => $day->pluck('event_title')->values(),
            ])->values();

        $statusBreakdown = [];
        foreach (['confirmed', 'pending', 'cancelled'] as $s) {
            $statusBreakdown[$s] = [
                'count'   => $stats[$s],
                'percent' => $stats['total'] ? round($stats[$s] / $stats['total'] * 100) : 0,
            ];
        }

        $notifications = auth()->user()->notifications()->latest()->take(5)->get();

        $bestPackages = Reservation::selectRaw('package, COUNT(*) as total')
            ->whereNotNull('package')
            ->where('package', '!=', '')
            ->where('status', '!=', 'cancelled')
            ->groupBy('package')
            ->orderByDesc('total')
            ->take(3)->get();

        return view('admin.dashboard', compact(
            'stats',
            'statusBreakdown',
            'upcomingEvents',
            'recentReservations',
            'calendarEvents',
            'notifications',
            'bestPackages'
        ));
    }
}