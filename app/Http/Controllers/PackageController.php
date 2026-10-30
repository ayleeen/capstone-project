<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    /**
     * Display all packages.
     */
    public function index()
    {
        $packages = Package::latest()->get();

        return view('admin.packages', compact('packages'));
    }

    /**
     * Show the form for creating a package.
     */
    public function create()
    {
        $menuItems = MenuItem::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $menuItemsJson = $menuItems->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'category' => $m->category ?: 'Others',
        ])->values();

        return view('admin.add-package', compact('menuItems', 'menuItemsJson'));
    }

    /**
     * Store a new package.
     */
 
public function store(Request $request)
{
    $validated = $request->validate([
        'package_name' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        'category' => ['required', 'string', 'max:255'],
        'guest_min' => ['required', 'integer', 'min:1'],
        'guest_max' => ['required', 'integer', 'gte:guest_min'],
        'description' => ['required', 'string'],
        'status' => ['nullable', 'boolean'],
        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:5120'],
        'inclusions' => ['nullable', 'array'],
        'inclusions.*' => ['nullable', 'string', 'max:255'],
    ]);

    $validated['inclusions'] = collect($request->input('inclusions', []))
        ->map(fn ($item) => trim($item))
        ->filter(fn ($item) => $item !== '')
        ->values()
        ->all();

    if ($request->hasFile('photo')) {
        $validated['photo'] = $request->file('photo')
            ->store('packages', 'public');
    }

    $validated['status'] = $request->has('status');

    $package = Package::create($validated);

    return redirect()
        ->route('admin.packages')
        ->with('success', 'Package added successfully!');
}


    /**
     * Display one package.
     */
    public function show(Package $package)
    {
        $package->load('menuItems');

        return view('admin.package-details', compact('package'));
    }

    /**
     * Show the edit form.
     */
public function edit(Package $package)
{
    return view('admin.edit-package', compact('package'));
}

 
public function update(Request $request, Package $package)
{
    $validated = $request->validate([
        'package_name' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        'category' => ['required', 'string', 'max:255'],
        'guest_min' => ['required', 'integer', 'min:1'],
        'guest_max' => ['required', 'integer', 'gte:guest_min'],
        'description' => ['required', 'string'],
        'status' => ['nullable', 'boolean'],
        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:5120'],
        'inclusions' => ['nullable', 'array'],
        'inclusions.*' => ['nullable', 'string', 'max:255'],
    ]);

    $validated['inclusions'] = collect($request->input('inclusions', []))
        ->map(fn ($item) => trim($item))
        ->filter(fn ($item) => $item !== '')
        ->values()
        ->all();

    if ($request->hasFile('photo')) {
        if ($package->photo) {
            Storage::disk('public')->delete($package->photo);
        }

        $validated['photo'] = $request->file('photo')
            ->store('packages', 'public');
    }

    $validated['status'] = $request->has('status');

    $package->update($validated);

    return redirect()
        ->route('admin.packages')
        ->with('success', 'Package updated successfully!');
}


    /**
     * Delete package.
     */
    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages')->with('success', 'Package archived.');
    }
}