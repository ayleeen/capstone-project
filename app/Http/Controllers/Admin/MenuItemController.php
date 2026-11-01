<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use App\Models\AddonPrice;

class MenuItemController extends Controller
{
public function index(Request $request)
{
    $query = MenuItem::query();

    if ($request->filled('tab') && $request->tab === 'inactive') {
        $query->where('is_active', false);
    }

    if ($search = $request->input('search')) {
        $query->where('name', 'like', "%{$search}%");
    }

    if ($request->filled('category') && $request->category !== 'All Categories') {
        $query->where('category', $request->category);
    }

    if ($request->filled('status') && $request->status !== 'All Status') {
        $query->where('is_active', $request->status === 'Active');
    }

    switch ($request->input('sort')) {
        case 'Oldest First':
            $query->oldest();
            break;
        case 'Price: High to Low':
            $query->orderByDesc('price');
            break;
        case 'Price: Low to High':
            $query->orderBy('price');
            break;
        default:
            $query->latest();
    }

    $menuItems = $query->paginate(10)->withQueryString();

    $categories = MenuItem::select('category')
        ->distinct()
        ->pluck('category');

    $stats = [
        'total'            => MenuItem::count(),
        'totalCategories'  => $categories->count(),
        'popularCategory'  => MenuItem::select('category')
                                ->groupBy('category')
                                ->orderByRaw('COUNT(*) DESC')
                                ->value('category') ?? '—',
        'totalSales'       => 0, // wala pang sales data na naka-link dito
    ];

    $categoryCounts = MenuItem::selectRaw('category, COUNT(*) as total')
        ->groupBy('category')
        ->pluck('total', 'category');

    return view('admin.menu', compact('menuItems', 'categories', 'categoryCounts', 'stats'));
}

    public function create()
    {
        return view('admin.add-menu');
    }


public function store(Request $request)
{
    $data = $this->validated($request);
    unset($data['addon_prices']);

    if ($request->hasFile('image')) {
        $data['image_path'] = $request->file('image')->store('menu-items', 'public');
    }

    $menuItem = MenuItem::create($data);

    $this->syncAddonPrices($menuItem, $request);

    return redirect()->route('admin.menu')->with('success', 'Menu item added.');
}


public function edit(MenuItem $menuItem)
{
    $menuItem->load('addonPrices');

    return view('admin.edit-menu', compact('menuItem'));
}

public function update(Request $request, MenuItem $menuItem)
{
    $data = $this->validated($request);
    unset($data['addon_prices']);

    if ($request->hasFile('image')) {
        if ($menuItem->image_path) {
            \Storage::disk('public')->delete($menuItem->image_path);
        }

        $data['image_path'] = $request->file('image')->store('menu-items', 'public');
    }

    $menuItem->update($data);

    $this->syncAddonPrices($menuItem, $request);

    return redirect()->route('admin.menu')->with('success', 'Menu item updated.');
}


    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image_path) {
            \Storage::disk('public')->delete($menuItem->image_path);
        }

        $menuItem->delete();

        return redirect()->route('admin.menu')->with('success', 'Menu item deleted.');
    }


private function validated(Request $request): array
{
    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'required|string',
        'category'    => 'required|in:pasta,vegetable,salad,beef,chicken,pork,fish,dessert,juice',
        'price'       => 'nullable|numeric|min:0',
        'is_active'  => 'sometimes|boolean',
        'is_addon'   => 'sometimes|boolean',

        'addon_prices'    => 'required_if:is_addon,1|array',
        'addon_prices.30' => 'required_if:is_addon,1|numeric|min:0',
        'addon_prices.40' => 'required_if:is_addon,1|numeric|min:0',
        'addon_prices.50' => 'required_if:is_addon,1|numeric|min:0',

        'prep_time'   => 'nullable|string|max:100',
        'spicy_level' => 'nullable|in:Mild,Medium,Spicy,Very Spicy',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    ]);

    $data['is_active'] = $request->has('is_active');
    $data['is_addon'] = $request->boolean('is_addon');

    return $data;
}
            
private function syncAddonPrices(MenuItem $menuItem, Request $request): void
{
    if (!$menuItem->is_addon) {
        $menuItem->addonPrices()->delete();
        return;
    }

    foreach ([30, 40, 50] as $servings) {
        $menuItem->addonPrices()->updateOrCreate(
            ['servings' => $servings],
            ['price' => $request->input("addon_prices.$servings")]
        );
    }
}

}