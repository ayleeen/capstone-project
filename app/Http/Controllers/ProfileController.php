<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function edit(Request $request): View
    {
                return view('profile', [
                'user' => $request->user(),
            ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact_number'  => 'nullable|string|max:30',
            'secondary_phone' => 'nullable|string|max:30',
            'address_line1'   => 'nullable|string|max:255',
            'address_line2'   => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('photo')->store('profiles', 'public');
        }

        unset($data['photo']);

        // Naka-MustVerifyEmail ang model: kapag nagpalit ng email, kailangan ulit i-verify
        $emailChanged = $data['email'] !== $user->email;
        if ($emailChanged) {
            $data['email_verified_at'] = null;
        }

        $user->forceFill($data)->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
            return back()->with('success', 'Profile updated. Please verify your new email address.');
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove the user's profile photo.
     */
    public function removePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->forceFill(['profile_photo' => null])->save();
        }

        return back()->with('success', 'Profile photo removed.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}