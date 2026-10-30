<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $phone, string $message): bool
    {
        $deviceId = config('services.textbee.device_id');
        $apiKey   = config('services.textbee.api_key');

        if (!$deviceId || !$apiKey) {
            Log::error('TextBee: missing TEXTBEE_DEVICE_ID or TEXTBEE_API_KEY in .env');
            return false;
        }

        try {
            $response = Http::withHeaders(['x-api-key' => $apiKey])
                ->timeout(15)
                ->post("https://api.textbee.dev/api/v1/gateway/devices/{$deviceId}/send-sms", [
                    'recipients' => [$this->formatPhone($phone)],
                    'message'    => $message,
                ]);
        } catch (\Throwable $e) {
            Log::error('TextBee request error: ' . $e->getMessage());
            return false;
        }

        if ($response->failed()) {
            Log::error('TextBee SMS failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '63')) return '+' . $phone;
        if (str_starts_with($phone, '0'))  return '+63' . substr($phone, 1);

        return '+63' . $phone;
    }
}