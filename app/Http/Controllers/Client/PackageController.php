<?php // app/Http/Controllers/Client/PackageController.php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageController extends Controller
{
public function index()
{
    $packages = Package::where('status', true)
        ->orderBy('category')
        ->orderBy('price')
        ->get();

    return view('package', compact('packages'));
}
}