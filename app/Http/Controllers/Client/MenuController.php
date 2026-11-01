<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AddonPrice;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function index()
    {
        $categories = [
            'pasta'     => 'Pasta',
            'vegetable' => 'Vegetable',
            'salad'     => 'Salad',
            'beef'      => 'Beef',
            'chicken'   => 'Chicken',
            'pork'      => 'Pork',
            'fish'      => 'Fish',
            'dessert'   => 'Dessert',
            'juice'     => 'Juice',
        ];

        $menuItems = MenuItem::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($item) => strtolower(trim($item->category)));

        return view('menu', compact('menuItems', 'categories'));
    }


public function customize()
{
    $reservation = session('reservation', []);
    $pax = (int) data_get($reservation, 'step1.pax', 0);

    if ($pax < 1) {
        return redirect()->route('reservation.process1');
    }

    $draftId = session('reservation_draft_id');

    // Regular dishes for Custom Menu selection
    $menuItems = MenuItem::where('is_active', true)
        ->where('is_addon', false)
        ->orderBy('name')
        ->get()
        ->map(fn ($item) => [
            'id'          => $item->id,
            'name'        => $item->name,
            'category'    => strtolower(trim($item->category)),
            'description' => $item->description ?? '',
            'image'       => $item->image ? asset('storage/' . $item->image) : null,
        ])
        ->values();

    // Add-on dishes with their individual serving-tier prices
    $addonItems = MenuItem::where('is_active', true)
        ->where('is_addon', true)
        ->with('addonPrices')
        ->orderBy('name')
        ->get()
        ->map(fn ($item) => [
            'id'       => $item->id,
            'name'     => $item->name,
            'category' => strtolower(trim($item->category)),
            'prices'   => $item->addonPrices
                ->mapWithKeys(fn ($price) => [
                    (int) $price->servings => (float) $price->price
                ])
                ->all(),
        ])
        ->values();

    return view('customize-menu', compact(
        'menuItems',
        'addonItems',
        'pax',
        'draftId'
    ));
}

}