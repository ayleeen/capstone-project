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
    // Start a fresh reservation
    session()->forget([
        'reservation',
        'reservation_submitted',
    ]);

    // Generate a unique ID for this reservation draft
    session([
        'reservation_draft_id' => (string) \Illuminate\Support\Str::uuid(),
    ]);

    $bookedGuests  = Reservation::bookedGuestsByDate();
    $dailyCapacity = Reservation::DAILY_CAPACITY;

    return view('reservation-process-1', compact(
        'bookedGuests',
        'dailyCapacity'
    ));
}
    
public function storeProcess1(Request $request)
{
    $data = $request->validate([
        'selected_date' => 'required|date|after_or_equal:today',
        'start_time'    => 'required|date_format:H:i',
        'end_time'      => 'nullable',
        'pax'           => 'required|integer|min:1',
    ]);

    // Automatically calculate end time: 4 hours after start time
    $start = \Carbon\Carbon::createFromFormat('H:i', $data['start_time']);
    $end = $start->copy()->addHours(4);

    $data['end_time'] = $end->format('H:i');

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

    $step3 = $reservation['step3'];

    $package = isset($step3['package_id'])
        ? Package::find($step3['package_id'])
        : null;

    $pax = (int) $reservation['step1']['pax'];
    $perHead = (int) ($step3['price_per_head'] ?? 0);
    $customTotal = $perHead * $pax;

    $customItems = collect();
    $menuSets = [];

    if (($step3['type'] ?? null) === 'custom') {

        // Support both new multiple-set and old single-menu formats.
        $savedSets = $step3['menu_sets'] ?? [
            [
                'pax' => $pax,
                'menu_ids' => $step3['menu_ids'] ?? [],
            ]
        ];

        foreach ($savedSets as $index => $set) {

            $ids = array_map('intval', $set['menu_ids'] ?? []);

            $items = \App\Models\MenuItem::whereIn('id', $ids)
                ->get()
                ->sortBy(fn ($item) => array_search($item->id, $ids))
                ->values();

            $menuSets[] = [
                'set_number' => $index + 1,
                'pax' => (int) ($set['pax'] ?? 0),
                'items' => $items,
            ];

            // Keep flattened items for compatibility with the existing Blade view.
            $customItems = $customItems->concat($items);
        }
    }

    [$addons, $addonsTotal] = $this->resolveAddons($step3);

    $isCustomFlow = (($step3['type'] ?? null) === 'custom');

    $grandTotal = $isCustomFlow
        ? $customTotal + $addonsTotal
        : ($package->price ?? 0);

    return view('reservation-process-4', compact(
        'reservation',
        'package',
        'customItems',
        'menuSets',
        'pax',
        'perHead',
        'customTotal',
        'addons',
        'addonsTotal',
        'grandTotal'
    ));
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

    $step3 = $reservation['step3'];
    $isCustom = ($step3['type'] ?? null) === 'custom';
    $customItems = collect();
    $menuSets = [];
    $pax = (int) $reservation['step1']['pax'];

    $addons = collect();
    $addonsTotal = 0;

    if ($isCustom) {
        $package = null;

        // Support new multiple-set and old single-menu formats.
        $savedSets = $step3['menu_sets'] ?? [
            [
                'pax' => $pax,
                'menu_ids' => $step3['menu_ids'] ?? [],
            ]
        ];

        foreach ($savedSets as $index => $set) {
            $ids = array_map('intval', $set['menu_ids'] ?? []);
            $items = \App\Models\MenuItem::whereIn('id', $ids)
                ->get()
                ->sortBy(fn ($item) => array_search($item->id, $ids))
                ->values();

            $menuSets[] = [
                'set_number' => $index + 1,
                'pax' => (int) ($set['pax'] ?? 0),
                'items' => $items,
            ];

            $customItems = $customItems->concat($items);
        }

        $perHead = (int) ($step3['price_per_head'] ?? 0);
        $customTotal = $perHead * $pax;

        [$addons, $addonsTotal] = $this->resolveAddons($step3);

        $total = $customTotal + $addonsTotal;
        $packageName = "Custom Menu (₱{$perHead}/head)";

    } else {
        $package = Package::findOrFail($step3['package_id']);
        $perHead = 0;
        $customTotal = 0;
        $total = (float) $package->price;
        $packageName = $package->package_name;
    }

    $down = $total * 0.5;
    $remaining = $total - $down;

    return view('reservation-process-5', compact(
        'reservation',
        'package',
        'customItems',
        'menuSets',
        'isCustom',
        'pax',
        'perHead',
        'customTotal',
        'addons',
        'addonsTotal',
        'total',
        'down',
        'remaining',
        'packageName'
    ));
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

    $isCustom = ($r['step3']['type'] ?? null) === 'custom';


if ($isCustom) {
    $package = null;

    $perHead = (int) $r['step3']['price_per_head'];

    // Get selected add-ons and their prices
    [$addonList, $addonsTotal] = $this->resolveAddons($r['step3']);

    $total = ($perHead * (int) $r['step1']['pax']) + $addonsTotal;

    $packageName = "Custom Menu (₱{$perHead}/head)";

    $menuSets = [];
    $customMenu = [];

    $sets = $r['step3']['menu_sets'] ?? [];

    // Backward compatibility for old reservations
    if (empty($sets) && !empty($r['step3']['menu_ids'])) {
        $sets = [[
            'pax' => (int) $r['step1']['pax'],
            'menu_ids' => $r['step3']['menu_ids'],
        ]];
    }

    foreach ($sets as $index => $set) {
        $ids = array_map('intval', $set['menu_ids'] ?? []);

        $items = \App\Models\MenuItem::whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $orderedItems = collect($ids)
            ->map(fn ($id) => $items->get($id))
            ->filter();

        $formattedItems = $orderedItems
            ->map(fn ($i) => [
                'category' => strtolower(trim($i->category)),
                'name' => $i->name,
            ])
            ->values()
            ->all();

        $menuSets[] = [
            'set_number' => $index + 1,
            'pax' => (int) ($set['pax'] ?? 0),
            'items' => $formattedItems,
        ];

        // Keep the old flat custom_menu format
        $customMenu = array_merge($customMenu, $formattedItems);
    }

    $addons = $addonList->values()->all();

}else {
    $package = Package::findOrFail($r['step3']['package_id']);

    $perHead = null;
    $total = $package->price;
    $packageName = $package->package_name;

    $customMenu = null;
    $addons = null;
}

$proofPath = $request->file('proof')->store('payment-proofs', 'public');


$reservation = Reservation::create([
    'user_id'          => auth()->id(),
    'full_name'        => trim(
        $r['step2']['first_name'] . ' ' .
        $r['step2']['last_name'] . ' ' .
        ($r['step2']['suffix'] ?? '')
    ),
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

    'package'          => $packageName,
    'package_id'       => $package?->id,
    'price_per_head'   => $perHead,
    'custom_menu'      => $customMenu,
    'menu_sets'        => $menuSets ?? null,
    'addons'           => $addons,

    'food_restriction' => $r['step3']['food_restriction'] ?? null,
    'notes'            => $r['step2']['special_request'] ?? null,

    'total_amount'     => $total,
    'down_payment'     => $total * 0.5,

    'proof_of_payment' => $proofPath,
    'status'           => 'pending',
    'payment_status'   => 'partial',
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
$reservation = session('reservation', []);
$pax = (int) data_get($reservation, 'step1.pax', 0);

if ($pax < 1) {
    return response()->json([
        'message' => 'Reservation details are missing. Please restart your reservation.',
    ], 422);
}

$data = $request->validate([
        'menu_sets'          => ['required', 'array', 'min:1'],
        'menu_sets.*.pax'    => ['required', 'integer', 'min:1', 'max:50'],
        'menu_sets.*.menu_ids'   => ['required', 'array', 'min:1'],
        'menu_sets.*.menu_ids.*' => ['integer', 'exists:menu_items,id'],

        'price_per_head' => ['required', 'integer', 'in:400,500,600,700'],
        'restriction'    => ['nullable', 'string', 'max:500'],

        'addons'            => ['nullable', 'array'],
        'addons.*.id'       => ['required', 'integer', 'distinct', 'exists:menu_items,id'],
        'addons.*.servings' => ['required', 'integer', 'in:30,40,50'],
    ]);

    // Validate total pax and number of sets.
// Validate exact pax allocation per set.
$requiredSets = (int) ceil($pax / 50);

if (count($data['menu_sets']) !== $requiredSets) {
    return response()->json([
        'message' => 'The number of menu sets does not match the reservation pax.',
    ], 422);
}

foreach ($data['menu_sets'] as $index => $set) {
    $expectedPax = min(50, $pax - ($index * 50));

    if ((int) $set['pax'] !== $expectedPax) {
        return response()->json([
            'message' => 'Set ' . ($index + 1) . ' must have exactly ' . $expectedPax . ' pax.',
        ], 422);
    }
}

    // Validate each add-on dish and its own pax-tier price.
    // Existing add-on validation retained.
    foreach ($data['addons'] ?? [] as $addon) {
        $item = \App\Models\MenuItem::find($addon['id']);

        $hasPrice = \App\Models\AddonPrice::where('menu_item_id', $addon['id'])
            ->where('servings', $addon['servings'])
            ->exists();

        if (!$item || !$item->is_addon || !$hasPrice) {
            return response()->json([
                'message' => 'One of your selected add-ons is unavailable or has no price for the selected pax tier. Please refresh and select again.',
            ], 422);
        }
    }

    // Existing Custom Menu tier rules.
    $tiers = [
        700 => [[['pasta'],1],[['vegetable'],1],[['salad'],1],[['beef'],1],[['chicken'],1],[['pork'],1],[['fish'],1],[['dessert'],1],[['juice'],1]],
        600 => [[['pasta','vegetable'],1],[['salad'],1],[['beef'],1],[['chicken'],1],[['pork'],1],[['fish'],1],[['dessert'],1],[['juice'],1]],
        500 => [[['pasta','vegetable'],1],[['salad'],1],[['beef','chicken','pork'],2],[['fish'],1],[['dessert'],1],[['juice'],1]],
        400 => [[['pasta','vegetable'],1],[['beef','chicken','pork'],2],[['fish'],1],[['dessert'],1],[['juice'],1]],
    ];

    $rules = $tiers[$data['price_per_head']];
    $expect = array_sum(array_column($rules, 1));

    // Validate every menu set independently.
    foreach ($data['menu_sets'] as $index => $set) {
        if (count($set['menu_ids']) !== count(array_unique($set['menu_ids']))) {
    return response()->json([
        'message' => 'Set ' . ($index + 1) . ' contains duplicate menu items.',
    ], 422);
}
$items = \App\Models\MenuItem::whereIn('id', $set['menu_ids'])
    ->where('is_active', true)
    ->where('is_addon', false)
    ->get();

$cats = $items->map(fn ($i) => strtolower(trim($i->category)));

        $valid = $items->count() === $expect;

        foreach ($rules as [$allowed, $count]) {
            if ($cats->filter(fn ($c) => in_array($c, $allowed))->count() !== $count) {
                $valid = false;
            }
        }

        if (!$valid) {
            return response()->json([
                'message' => 'Set ' . ($index + 1) . ' does not match the selected price tier.',
            ], 422);
        }
    }

    session(['reservation.step3' => [
        'type'             => 'custom',
        'menu_sets'        => $data['menu_sets'],
        'price_per_head'   => $data['price_per_head'],
        'addons'           => $data['addons'] ?? [],
        'food_restriction' => $data['restriction'] ?? null,
    ]]);

    return response()->json(['ok' => true]);
}
private function resolveAddons(array $step3): array
{
    $rows = $step3['addons'] ?? [];

    if (!$rows) {
        return [collect(), 0];
    }

    $list = collect($rows)->map(function ($row) {
        $item = \App\Models\MenuItem::find($row['id']);

        $priceRow = \App\Models\AddonPrice::where('menu_item_id', $row['id'])
            ->where('servings', $row['servings'])
            ->first();

        if (!$item || !$item->is_addon || !$priceRow) {
            abort(409, 'An add-on is no longer available at the selected pax tier. Please go back and select it again.');
        }

        return [
            'id'       => (int) $item->id,
            'name'     => $item->name,
            'servings' => (int) $row['servings'],
            'price'    => (float) $priceRow->price,
        ];
    });

    return [$list, $list->sum('price')];
}
}