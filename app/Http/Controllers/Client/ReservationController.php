<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Package;  
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\NewReservationNotification;
use Illuminate\Support\Facades\Notification;


class ReservationController extends Controller
{
    // STEP 1


public function process1()
{
    session()->forget('reservation_submitted');

    $bookedGuests  = Reservation::bookedGuestsByDate();
    $dailyCapacity = Reservation::DAILY_CAPACITY;

    return view('reservation-process-1', compact('bookedGuests', 'dailyCapacity'));
}
    
public function storeProcess1(Request $request)
{
    $data = $request->validate([
        'selected_date' => 'required|date|after_or_equal:today',
        'start_time'    => 'required',
        'end_time'      => 'required',
        'pax'           => 'required|integer|min:1',
    ]);

    $remaining = Reservation::remainingCapacity($data['selected_date']);

    if ($remaining < 50) {
        return back()
            ->withErrors(['selected_date' => 'Sorry, that date is fully booked.'])
            ->withInput();
    }

    if ((int) $data['pax'] > $remaining) {
        return back()
            ->withErrors(['pax' => "Only {$remaining} guest slots are left on that date."])
            ->withInput();
    }

    session(['reservation.step1' => $data]);

    return redirect()->route('reservation.process2');
}

    // STEP 2
    public function process2()
    {
        $step1 = session('reservation.step1');

        if (!$step1) {
            return redirect()->route('reservation.process1');
        }

        return view('reservation-process-2', compact('step1'));
    }

    public function storeProcess2(Request $request)
    {
        $data = $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'suffix'          => 'nullable|string|max:20',
            'contact_number'  => 'required|string|max:20',
            'email'           => 'required|email',
            'event_type'      => 'required|string',
            'event_street'    => 'required|string|max:255',
            'landmark'        => 'nullable|string|max:255',
            'special_request' => 'nullable|string|max:500',
        ]);

        session(['reservation.step2' => $data]);

        return redirect()->route('reservation.process3');
    }

    // STEP 3
    public function process3()
    {
        if (!session('reservation.step1') || !session('reservation.step2')) {
            return redirect()->route('reservation.process1');
        }

        return view('reservation-process-3');
    }

public function packageMenu()
{
    if (!session('reservation.step1') || !session('reservation.step2')) {
        return redirect()->route('reservation.process1');
    }

    $packages = Package::where('status', true)->get()->map(function ($p) {
        // description: isang item bawat linya ang ginagawang bullet
        $lines = preg_split('/\r\n|\r|\n/', (string) $p->description, -1, PREG_SPLIT_NO_EMPTY);

        return [
            'id'         => $p->id,
            'title'      => $p->package_name,
            'price'      => '₱' . number_format($p->price, 2),
            'inclusions' => array_merge(
                [
                    $p->category,
                    $p->guest_min . ' - ' . $p->guest_max . ' guests',
                ],
                $lines
            ),
            'image'      => $p->photo ? asset('storage/' . $p->photo) : null,
        ];
    });

    return view('package-menu', compact('packages'));
}

        public function storePackageMenu(Request $request)
    {
        $data = $request->validate([
            'package_id'       => 'required',
            'food_restriction' => 'nullable|string|max:500',
        ], [
            'package_id.required' => 'Please select a package.',
        ]);

        session(['reservation.step3' => $data]);

        return redirect()->route('reservation.process4');
    }

    public function process4()
    {
        $reservation = session('reservation');

        if (!isset($reservation['step1'], $reservation['step2'], $reservation['step3'])) {
            return redirect()->route('reservation.process1');
        }

        $package = isset($reservation['step3']['package_id'])
    ? Package::find($reservation['step3']['package_id'])
    : null;

        return view('reservation-process-4', compact('reservation', 'package'));
    }
public function cancel()
{
    session()->forget(['reservation', 'reservation_submitted']);

    return redirect()->route('home');
}

public function process5()
{
    $reservation = session('reservation');

    if (!isset($reservation['step1'], $reservation['step2'], $reservation['step3'])) {
        return redirect()->route('reservation.process1');
    }

    $package = Package::find($reservation['step3']['package_id']);

    return view('reservation-process-5', compact('reservation', 'package'));
}
public function confirm(Request $request)
{   
      if (session('reservation_submitted')) {
        return redirect()->route('reservation.process5');
    }

    $r = session('reservation');

    // Session expired or steps missing: start over
     if (!isset($r['step1'], $r['step2'], $r['step3'])) {
        return redirect()->route('reservation.process1');
    }

    // Capacity check: the date may have filled up while the client was on this page
    $remaining = Reservation::remainingCapacity($r['step1']['selected_date']);

    if ((int) $r['step1']['pax'] > $remaining) {
        return redirect()->route('reservation.process1')
            ->withErrors(['selected_date' => 'Sorry, this date no longer has enough slots. Please choose another date.']);
    }

    $request->validate([
        'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    $package   = Package::findOrFail($r['step3']['package_id']);
    $proofPath = $request->file('proof')->store('payment-proofs', 'public');

    $reservation = Reservation::create([
        'user_id'          => auth()->id(),
        'full_name'        => trim($r['step2']['first_name'] . ' ' . $r['step2']['last_name'] . ' ' . ($r['step2']['suffix'] ?? '')),
        'email'            => $r['step2']['email'],
        'phone'            => $r['step2']['contact_number'],
        'event_type'       => $r['step2']['event_type'],
        'event_title'      => ucfirst($r['step2']['event_type']) . ' Event',
        'location'         => $r['step2']['event_street'],
        'landmark'         => $r['step2']['landmark'] ?? null,
        'start_date'       => $r['step1']['selected_date'],
        'end_date'         => $r['step1']['selected_date'],
        'start_time'       => $r['step1']['start_time'],
        'end_time'         => $r['step1']['end_time'],
        'guests'           => $r['step1']['pax'],
        'package'          => $package->package_name,
        'package_id'       => $package->id,
        'food_restriction' => $r['step3']['food_restriction'] ?? null,
        'notes'            => $r['step2']['special_request'] ?? null,
        'total_amount'     => $package->price,
        'down_payment'     => $package->price * 0.5,
        'proof_of_payment' => $proofPath,
        'status'           => 'pending',
        'payment_status'   => 'unpaid',
    ]);

    $admins = User::where('is_admin', true)->get();
    Notification::send($admins, new NewReservationNotification($reservation));

     return redirect()->route('reservation.process5')->with('reservation_success', true);

}
public function done()
{
    session()->forget(['reservation', 'reservation_submitted']);

    return redirect()->route('home');
}
public function storeCustomMenu(Request $request)
{
    $data = $request->validate([
        'menu_ids'    => ['required', 'array', 'size:9'],
        'menu_ids.*'  => ['integer', 'exists:menu_items,id'],
        'restriction' => ['nullable', 'string', 'max:500'],
    ]);

    session(['reservation.step3' => [
        'type'             => 'custom',
        'menu_ids'         => $data['menu_ids'],
        'food_restriction' => $data['restriction'] ?? null,
    ]]);

    return response()->json(['ok' => true]);
}
}