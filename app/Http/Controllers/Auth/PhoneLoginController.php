<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PhoneLoginController extends Controller
{
    // Turns 09171234567, +639171234567 or 9171234567 into 09171234567
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '63')) return '0' . substr($phone, 2);
        if (str_starts_with($phone, '9'))  return '0' . $phone;

        return $phone;
    }

    public function sendCode(Request $request, SmsService $sms)
    {
        $request->validate(['phone' => 'required|string|min:10|max:15']);

        $phone = $this->normalizePhone($request->phone);

        // contact_number may be saved as 09..., +639... or 639... so check all forms
$digits = substr($phone, 1); // 9171234567
$user = User::whereIn('contact_number', [
    $phone,              // 09171234567
    '63' . $digits,      // 639171234567
    '+63' . $digits,     // +639171234567
    $digits,             // 9171234567
])->first();

        if (!$user) {
            return response()->json([
                'message' => 'No account found with this phone number.'
            ], 404);
        }

        // Block spamming: only 1 code per 60 seconds
        $recent = DB::table('phone_otps')
            ->where('phone', $phone)
            ->where('created_at', '>', now()->subSeconds(60))
            ->exists();

        if ($recent) {
            return response()->json([
                'message' => 'Please wait 60 seconds before asking for a new code.'
            ], 429);
        }

        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        DB::table('phone_otps')->where('phone', $phone)->delete();

        DB::table('phone_otps')->insert([
            'phone'      => $phone,
            'code'       => Hash::make($code),
            'channel'    => 'sms',
            'expires_at' => now()->addMinutes(5),
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (env('SMS_LOG_ONLY', false)) {
    \Illuminate\Support\Facades\Log::info("PHONE LOGIN CODE for {$phone}: {$code}");
    $sent = true;
} else {
    $sent = $sms->send($phone, "Your Lolay's Catering login code is {$code}. It expires in 5 minutes.");
}

        if (!$sent) {
            return response()->json([
                'message' => 'Could not send the SMS. Please try again.'
            ], 500);
        }

        return response()->json(['message' => 'Code sent!']);
    }
    private function findUserByPhone(string $phone): ?User
{
    $digits = substr($phone, 1); // 9171234567

    return User::whereIn('contact_number', [
        $phone, '63' . $digits, '+63' . $digits, $digits,
    ])->first();
}

public function verifyCode(Request $request)
{
    $request->validate([
        'phone' => 'required|string|min:10|max:15',
        'code'  => 'required|digits:4',
    ]);

    $phone = $this->normalizePhone($request->phone);

    $otp = DB::table('phone_otps')->where('phone', $phone)->first();

    if (!$otp) {
        return response()->json(['message' => 'Please request a code first.'], 422);
    }

    if (now()->greaterThan($otp->expires_at)) {
        DB::table('phone_otps')->where('phone', $phone)->delete();
        return response()->json(['message' => 'This code has expired. Request a new one.'], 422);
    }

    if ($otp->attempts >= 5) {
        DB::table('phone_otps')->where('phone', $phone)->delete();
        return response()->json(['message' => 'Too many wrong tries. Request a new code.'], 429);
    }

    if (!Hash::check($request->code, $otp->code)) {
        DB::table('phone_otps')->where('phone', $phone)->increment('attempts');
        return response()->json(['message' => 'Wrong code. Please try again.'], 422);
    }

    $user = $this->findUserByPhone($phone);

    if (!$user) {
        return response()->json(['message' => 'Account not found.'], 404);
    }

    // Code is correct: use it only once, then log in
    DB::table('phone_otps')->where('phone', $phone)->delete();

    Auth::login($user, true);
    $request->session()->regenerate();

    return response()->json([
        'redirect' => $user->is_admin ? route('admin.dashboard') : route('home'),
    ]);
}
}