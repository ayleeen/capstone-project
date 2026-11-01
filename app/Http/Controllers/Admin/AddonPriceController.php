<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddonPrice;
use Illuminate\Http\Request;

class AddonPriceController extends Controller
{
    public function index()
    {
        return view('admin.addon-prices', [
            'prices' => AddonPrice::orderBy('servings')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'prices'   => ['required', 'array'],
            'prices.*' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($data['prices'] as $id => $price) {
            AddonPrice::whereKey($id)->update(['price' => $price]);
        }

        return back()->with('status', 'Add-on prices updated.');
    }
}