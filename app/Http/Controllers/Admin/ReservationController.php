<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Support\Facades\Mail;
use App\Services\SmsService;
use App\Services\SmsTemplates;
use App\Models\Customer;


class ReservationController extends Controller
{
    public function index(Request $request)
    {
        // Iisang formula para sa dashboard at reservation page (nasa model)
        $stats = Reservation::monthlyStats();

        $query = Reservation::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('event_title', 'like', "%{$search}%")
                  ->orWhere('event_type', 'like', "%{$search}%")
                  ->orWhere('package', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        $reservations = $query->orderByDesc('id')->get();

        return view('admin.reservation', compact('reservations', 'stats'));
    }

    // public function store(Request $request)
    // {
    //     Reservation::create($this->validated($request));

    //     return redirect()->route('admin.reservation')->with('success', 'Reservation created.');
    // }
public function store(Request $request)
{
    $data = $this->validated($request);

    Customer::firstOrCreate(
        ['email' => $data['email']],
        [
            'name'          => $data['full_name'],
            'phone'         => $data['phone'],
            'customer_type' => 'new',
            'status'        => 'active',
        ]
    );

    Reservation::create($data);

    return redirect()
        ->route('admin.reservation')
        ->with('success', 'Reservation created successfully.');
}

    public function update(Request $request, Reservation $reservation)
    {
        $reservation->update($this->validated($request));

        return redirect()->route('admin.reservation')->with('success', 'Reservation updated.');
    }

public function sendConfirmation(
    Reservation $reservation,
    SmsService $smsService
) {
    // Cancelled reservations cannot be confirmed
    if ($reservation->status === 'cancelled') {
        return redirect()
            ->route('admin.reservation')
            ->with('error', 'This reservation is cancelled, so it cannot be confirmed.');
    }

    // SEND EMAIL (if this fails, the status will not change)
    Mail::to($reservation->email)
        ->send(new ReservationConfirmationMail($reservation));

    // PENDING -> CONFIRMED
    if ($reservation->status === 'pending') {
        $reservation->update(['status' => 'confirmed']);
    }

    // SEND SMS (the message says "CONFIRMED", so this runs after the status update)
    $smsSent = $smsService->send(
        $reservation->phone,
        SmsTemplates::confirmation($reservation)
    );

    return redirect()
        ->route('admin.reservation')
        ->with(
            $smsSent ? 'success' : 'error',
            $smsSent
                ? 'Reservation confirmed. Confirmation sent via email and SMS.'
                : 'Reservation confirmed and email sent, but the SMS failed. Check the TextBee device and storage/logs/laravel.log.'
        );
}

public function cancel(Reservation $reservation, SmsService $smsService)
{
    $reservation->update(['status' => 'cancelled']);

    $smsService->send($reservation->phone, SmsTemplates::cancellation($reservation));

    return redirect()->route('admin.reservation')->with('success', 'Reservation cancelled.');
}

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('admin.reservation')->with('success', 'Reservation deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email',
            'phone'          => 'required|string|max:30',
            'event_type'     => 'required|in:wedding,birthday,corporate,debut,christening',
            'event_title'    => 'required|string|max:255',
            'location'       => 'nullable|string|max:255',
            'start_date'     => 'required|date',
            'start_time'     => 'nullable',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'end_time'       => 'nullable',
            'guests'         => 'required|integer|min:1',
            'status'         => 'required|in:pending,confirmed,cancelled',
            'payment_status' => 'sometimes|in:unpaid,partial,paid',
            'package'        => 'nullable|string|max:255',
            'total_amount'   => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        $data['total_amount'] = $data['total_amount'] ?? 0;

        return $data;
    }
    
}