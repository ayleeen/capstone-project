<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

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
        return view('admin.add-package');
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
    ]);

    if ($request->hasFile('photo')) {
        $validated['photo'] = $request->file('photo')
            ->store('packages', 'public');
    }

    $validated['status'] = $request->has('status');

    Package::create($validated);

    return redirect()
        ->route('admin.packages')
        ->with('success', 'Package added successfully!');
}

    /**
     * Display one package.
     */
    public function show(Package $package)
    {
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
    ]);

    if ($request->hasFile('photo')) {
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

        return redirect()
            ->route('admin.packages')
            ->with('success', 'Package deleted successfully!');
    }
}
