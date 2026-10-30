<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpVerificationController extends Controller
{
    public function show(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => ['required', 'digits:4']]);
        $user = $request->user();

        if ($user->otp_attempts >= 5) {
            return back()->withErrors(['otp' => 'Too many attempts. Please request a new code.']);
        }
        if (!$user->otp_code || now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Code expired. Please request a new one.']);
        }
        if (!Hash::check($request->otp, $user->otp_code)) {
            $user->increment('otp_attempts');
            return back()->withErrors(['otp' => 'Incorrect code.']);
        }

        $user->forceFill([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
        ])->save();
        $user->markEmailAsVerified();

        return redirect()->intended(route('home'));
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }
        self::sendCode($user);
        return back()->with('status', 'A new code has been sent to your email.');
    }

    public static function sendCode($user): void
    {
        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $user->forceFill([
            'otp_code' => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ])->save();

        Mail::raw(
            "Your Lolay's verification code is {$code}. It expires in 10 minutes.",
            fn ($m) => $m->to($user->email)->subject('Your verification code')
        );
    }
}