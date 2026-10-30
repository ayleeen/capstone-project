<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports');
    }

    public function data(Request $request)
    {
        $base = $this->filtered($request, true);
        $notCancelled = (clone $base)->where('status', '!=', 'cancelled');

        // ---------- SUMMARY CARDS ----------
        $totalRevenue = (clone $notCancelled)->where('payment_status', 'paid')->sum('total_amount');
        $totalBookings = (clone $notCancelled)->count();
        $pendingPayments = (clone $notCancelled)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->sum('total_amount');

        $popular = (clone $notCancelled)
            ->whereNotNull('package')
            ->selectRaw('package, COUNT(*) as total')
            ->groupBy('package')
            ->orderByDesc('total')
            ->first();

        // Monthly growth: this month vs last month (ignores filters)
        $thisMonth = Reservation::where('status', '!=', 'cancelled')
            ->whereYear('start_date', now()->year)
            ->whereMonth('start_date', now()->month)->count();
        $lastMonthDate = now()->subMonthNoOverflow();
        $lastMonth = Reservation::where('status', '!=', 'cancelled')
            ->whereYear('start_date', $lastMonthDate->year)
            ->whereMonth('start_date', $lastMonthDate->month)->count();
        $growth = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : null;

        // Repeat customer rate (by email)
        $emails = (clone $notCancelled)->whereNotNull('email')->pluck('email')->unique();
        $repeatEmails = $this->repeatEmails();
        $repeatRate = $emails->count() > 0
            ? round(($emails->intersect($repeatEmails)->count() / $emails->count()) * 100, 1)
            : null;

        // ---------- SALES OVERVIEW / MONTHLY PERFORMANCE ----------
        $months = in_array((int) $request->input('months'), [3, 6, 12]) ? (int) $request->input('months') : 6;
        $monthly = $this->monthlySeries($request, $months);

        // ---------- BOOKINGS BY EVENT TYPE ----------
        $byEvent = (clone $notCancelled)
            ->selectRaw('event_type, COUNT(*) as total')
            ->groupBy('event_type')
            ->pluck('total', 'event_type');

        // ---------- PAYMENT STATUS ----------
        $paymentRows = (clone $notCancelled)
            ->selectRaw('payment_status, COUNT(*) as total, SUM(total_amount) as amount')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');

        $payment = [];
        foreach (['paid', 'partial', 'unpaid'] as $s) {
            $payment[$s] = [
                'count'  => (int) ($paymentRows[$s]->total ?? 0),
                'amount' => (float) ($paymentRows[$s]->amount ?? 0),
            ];
        }

        // ---------- INSIGHTS ----------
        $bestMonth = collect($monthly)->sortByDesc('revenue')->first();

        $topClient = (clone $notCancelled)
            ->where('payment_status', 'paid')
            ->selectRaw('full_name, SUM(total_amount) as amount')
            ->groupBy('full_name')
            ->orderByDesc('amount')
            ->first();

        $upcomingHigh = Reservation::where('status', '!=', 'cancelled')
            ->whereDate('start_date', '>=', now()->toDateString())
            ->orderByDesc('total_amount')
            ->limit(3)
            ->get(['event_title', 'full_name', 'start_date', 'total_amount'])
            ->map(fn ($r) => [
                'title'  => $r->event_title,
                'client' => $r->full_name,
                'date'   => optional($r->start_date)->format('M j, Y'),
                'amount' => (float) $r->total_amount,
            ]);

        // ---------- TABLE ROWS ----------
        $rows = (clone $base)
            ->orderByDesc('start_date')
            ->limit(500)
            ->get()
            ->map(fn ($r) => [
                'id'             => $r->id,
                'code'           => 'RES-' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
                'event'          => $r->event_title,
                'customer'       => $r->full_name,
                'package'        => $r->package,
                'amount'         => (float) $r->total_amount,
                'status'         => $r->status,
                'payment_status' => $r->payment_status ?? 'unpaid',
                'date'           => optional($r->start_date)->format('M j, Y'),
            ]);

        return response()->json([
            'summary' => [
                'total_revenue'    => (float) $totalRevenue,
                'total_bookings'   => $totalBookings,
                'pending_payments' => (float) $pendingPayments,
                'popular_package'  => $popular ? ['name' => $popular->package, 'count' => (int) $popular->total] : null,
                'monthly_growth'   => $growth,
                'repeat_rate'      => $repeatRate,
            ],
            'monthly'           => $monthly,
            'bookings_by_event' => $byEvent,
            'payment_status'    => $payment,
            'insights' => [
                'top_package'         => $popular ? ['name' => $popular->package, 'count' => (int) $popular->total] : null,
                'best_month'          => ($bestMonth && $bestMonth['revenue'] > 0) ? $bestMonth : null,
                'top_client'          => $topClient ? ['name' => $topClient->full_name, 'amount' => (float) $topClient->amount] : null,
                'upcoming_high_value' => $upcomingHigh,
            ],
            'rows' => $rows,
        ]);
    }

    // ------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------

    private function filtered(Request $request, bool $withDates)
    {
        $q = Reservation::query();

        if ($withDates) {
            if ($request->filled('date_from')) {
                $q->whereDate('start_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $q->whereDate('start_date', '<=', $request->date_to);
            }
        }

        if ($request->filled('event_type')) {
            $q->where('event_type', $request->event_type);
        }

        if ($request->filled('payment_status')) {
            $q->where('payment_status', $request->payment_status);
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('customer_type')) {
            $repeat = $this->repeatEmails();
            if ($request->customer_type === 'repeat') {
                $q->whereIn('email', $repeat);
            } elseif ($request->customer_type === 'new') {
                $q->whereNotIn('email', $repeat);
            }
        }

        return $q;
    }

    private function repeatEmails()
    {
        return Reservation::where('status', '!=', 'cancelled')
            ->whereNotNull('email')
            ->selectRaw('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');
    }

    private function monthlySeries(Request $request, int $months): array
    {
        $start = now()->startOfMonth()->subMonths($months - 1);

        $rows = $this->filtered($request, false)
            ->where('status', '!=', 'cancelled')
            ->whereDate('start_date', '>=', $start->toDateString())
            ->selectRaw("DATE_FORMAT(start_date, '%Y-%m') as ym,
                         COUNT(*) as bookings,
                         SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as revenue")
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $series = [];
        for ($i = 0; $i < $months; $i++) {
            $d = (clone $start)->addMonths($i);
            $key = $d->format('Y-m');
            $series[] = [
                'month'    => $d->format('M Y'),
                'bookings' => (int) ($rows[$key]->bookings ?? 0),
                'revenue'  => (float) ($rows[$key]->revenue ?? 0),
            ];
        }

        return $series;
    }
}