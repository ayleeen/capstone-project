<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $this->syncFromUsers();

        $customers = Customer::latest('id')
            ->get()
            ->map(fn ($c) => $this->format($c))
            ->values();

        return view('admin.customer', compact('customers'));
    }

    /**
     * Every client who registered (users.is_admin = 0) gets a customer record.
     * Customers deleted by the admin are not brought back.
     */
    private function syncFromUsers(): void
    {
        $linked = Customer::withTrashed()->whereNotNull('user_id')->pluck('user_id');

        $users = User::where('is_admin', 0)->whereNotIn('id', $linked)->get();

        Customer::withoutEvents(function () use ($users) {
            foreach ($users as $u) {
                $customer = new Customer([
                    'user_id'       => $u->id,
                    'name'          => trim($u->first_name . ' ' . $u->last_name),
                    'email'         => $u->email,
                    'phone'         => $u->contact_number ?: '—',
                    'customer_type' => 'new',
                    'status'        => 'active',
                ]);
                $customer->created_at = $u->created_at;
                $customer->save();
            }
        });
    }

    public function store(Request $request)
    {
        $customer = Customer::create($this->validated($request));

        return response()->json($this->format($customer), 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request, $customer));

        return response()->json($this->format($customer->fresh()));
    }


public function destroy(Customer $customer)
{
    $oldValues = $customer->getAttributes();

    $customer->delete();

    \App\Models\AuditLog::record(
        'delete',
        $customer,
        $oldValues,
        null,
        'Moved to archive: Customer #' . $customer->id
            . ($customer->name ? ' (' . $customer->name . ')' : '')
    );

    return response()->json(['ok' => true]);
}


    private function format(Customer $c): array
    {
        $stats = $this->bookingStats($c);

        return [
            'dbId'   => $c->id,
            'id' => 'CUS-' . $c->created_at->format('Y') . '-' . str_pad($c->id, 3, '0', STR_PAD_LEFT),
            'name'   => $c->name,
            'email'  => $c->email,
            'phone'  => $c->phone,
            'dob'    => $c->dob?->format('Y-m-d'),
            'address'  => $c->address,
            'city'     => $c->city,
            'province' => $c->province,
            'zip'      => $c->zip,
            'avatar'   => 'https://ui-avatars.com/api/?size=100&background=429646&color=fff&name=' . urlencode($c->name),
            'customerType'           => $c->customer_type ?: 'new',
            'preferredContactMethod' => $c->preferred_contact_method,
            'status'   => $c->status,
            'isNew'    => $c->created_at->gt(now()->subDays(30)),
            'isRepeat' => $stats['totalBookings'] > 1,
            'totalBookings' => $stats['totalBookings'],
            'totalSpent'    => $stats['totalSpent'],
            'avgPerBooking' => $stats['totalBookings'] > 0
                ? round($stats['totalSpent'] / $stats['totalBookings'], 2)
                : 0,
            'lastActivity' => $c->updated_at->toDateString(),
            'bookings'     => $stats['bookings'],
            'notes'        => $c->notes ?? [],
        ];
    }

    private function bookingStats(Customer $c): array
    {
        $reservations = Reservation::query()
            ->where(function ($q) use ($c) {
                $q->whereRaw('LOWER(email) = ?', [strtolower($c->email)]);
                if ($c->user_id) {
                    $q->orWhere('user_id', $c->user_id);
                }
            })
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('start_date')
            ->get();

        return [
            'totalBookings' => $reservations->count(),
            'totalSpent'    => (float) $reservations
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_amount'),
            'bookings'      => $reservations->map(fn ($r) => [
                'date'   => $r->start_date,
                'name'   => $r->event_title ?: $r->event_type,
                'status' => ucfirst($r->status),
                'price'  => (float) $r->total_amount,
            ])->values()->all(),
        ];
    }
}