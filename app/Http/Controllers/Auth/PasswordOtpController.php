<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class PasswordOtpController extends Controller
{
    // Ilagay ang email, magpadala ng code
    public function sendCode(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $email = strtolower($request->email);

        if (User::where('email', $email)->exists()) {
            $this->generateAndSend($email);
        }

        $request->session()->put('reset_email', $email);

        return redirect()->route('password.otp')
            ->with('status', 'If that email is registered, we sent a 6-digit code.');
    }

    public function showVerify(Request $request)
    {
        $email = $request->session()->get('reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-reset-otp', ['email' => $email]);
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $email = $request->session()->get('reset_email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        $attemptKey = 'pwd-otp:' . $email;

        if (RateLimiter::tooManyAttempts($attemptKey, 5)) {
            $minutes = ceil(RateLimiter::availableIn($attemptKey) / 60);
            return back()->withErrors(['otp' => "Too many attempts. Try again in {$minutes} minute(s)."]);
        }

        $hash = Cache::get('pwd_reset:' . $email);

        if (! $hash || ! Hash::check($request->otp, $hash)) {
            RateLimiter::hit($attemptKey, 600);
            return back()->withErrors(['otp' => 'Incorrect or expired code.']);
        }

        Cache::forget('pwd_reset:' . $email);
        RateLimiter::clear($attemptKey);
        $request->session()->put('reset_verified', $email);

        return redirect()->route('password.new');
    }

    public function resend(Request $request)
    {
        $email = $request->session()->get('reset_email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        if (User::where('email', $email)->exists()) {
            $this->generateAndSend($email);
        }

        return back()->with('status', 'A new code has been sent.');
    }

    // Para sa Step 2B (bagong password page)
    public function showNewPassword(Request $request)
    {
        if (! $request->session()->has('reset_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.new-password');
    }

    public function updatePassword(Request $request)
    {
        $email = $request->session()->get('reset_verified');
        if (! $email) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();
        }

        $request->session()->forget(['reset_email', 'reset_verified']);

        return redirect()->route('login')
            ->with('status', 'Your password has been reset. You can now log in.');
    }

    private function generateAndSend(string $email): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put('pwd_reset:' . $email, Hash::make($code), now()->addMinutes(10));

        Mail::raw(
            "Your Lolay's password reset code is {$code}. It expires in 10 minutes. If you did not request this, you can ignore this email.",
            fn ($m) => $m->to($email)->subject('Your password reset code')
        );
    }
}