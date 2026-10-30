<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        $profile = BusinessProfile::current();

        // Ilagay dito ang pangalan ng view na ginagamit ng dati mong /logo route
        return view('admin.logo', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = BusinessProfile::current();

        $data = $request->validate([
            'business_name'  => 'required|string|max:255',
            'description'    => 'nullable|string|max:500',
            'email'          => 'required|email|max:255',
            'contact_number' => 'required|string|max:50',
            'address'        => 'nullable|string|max:255',
            'website'        => 'nullable|string|max:255',
            'facebook'       => 'nullable|string|max:255',
            'logo'           => 'nullable|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = $request->file('logo')->store('business', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = null;
        } else {
            unset($data['logo']);
        }

        $profile->update($data);

       $profile->refresh();

return response()->json([
    'message'  => 'Business profile updated successfully.',
    'logo_url' => $profile->logo ? asset('storage/' . $profile->logo) : null,
]);
    }
}