<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Support\Facades\Mail;
use App\Services\SmsService;



class ReservationController extends Controller
{
    public function index(Request $request)
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

        $reservations = $query->latest('start_date')->get();

        return view('admin.reservation', compact('reservations', 'stats'));
    }

    // public function store(Request $request)
    // {
    //     Reservation::create($this->validated($request));

    //     return redirect()->route('admin.reservation')->with('success', 'Reservation created.');
    // }
public function store(Request $request)
{
    Reservation::create($this->validated($request));

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
    // =========================
    // SEND EMAIL
    // =========================

    Mail::to($reservation->email)
        ->send(
            new ReservationConfirmationMail($reservation)
        );


    // =========================
    // PREPARE PHONE NUMBER
    // =========================

    $phone = preg_replace(
        '/\D/',
        '',
        $reservation->phone
    );

    // Convert:
    // 09064112439
    // into:
    // 639064112439

    if (str_starts_with($phone, '0')) {
        $phone = '63' . substr($phone, 1);
    }


    // =========================
    // SMS MESSAGE
    // =========================

    $smsMessage =
        "Lolay's Catering Services: " .
        "Hello {$reservation->full_name}! " .
        "Your reservation RES-" .
        str_pad(
            $reservation->id,
            4,
            '0',
            STR_PAD_LEFT
        ) .
        " has been confirmed. " .
        "Event: {$reservation->event_title}. " .
        "Date: " .
        $reservation->start_date->format('F j, Y') .
        ". Guests: {$reservation->guests}. " .
        "Thank you for choosing Lolay's Catering Services.";


    // =========================
    // SEND SMS THROUGH MOCEAN
    // =========================

    $smsService->send(
        $phone,
        $smsMessage
    );


    // =========================
    // RETURN TO RESERVATION PAGE
    // =========================

    return redirect()
        ->route('admin.reservation')
        ->with(
            'success',
            'Confirmation sent successfully via email and SMS.'
        );
}

    public function cancel(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);

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