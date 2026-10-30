<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MenuItem; // palitan kung iba ang pangalan ng model mo

class MenuController extends Controller
{
    public function index()
    {
        // key = value sa database, value = label na lalabas
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
            $menuItems = MenuItem::where('is_active', true)
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

            return view('customize-menu', compact('menuItems'));
        }
    public function storeCustomize(\Illuminate\Http\Request $request)
        {
            $data = $request->validate([
                'menu_ids'    => ['required', 'array', 'size:9'],
                'menu_ids.*'  => ['integer', 'exists:menu_items,id'],
                'restriction' => ['nullable', 'string', 'max:500'],
            ]);

            session(['reservation.custom_menu' => $data]);

            return response()->json(['ok' => true]);
        }
}