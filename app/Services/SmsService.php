<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    public function send(string $number, string $message): array
    {
        $response = Http::withToken(
            config('services.mocean.api_token')
        )->asForm()->post(
            'https://rest.moceanapi.com/rest/2/sms',
            [
                'mocean-from'        => config('services.mocean.sender_name'),
                'mocean-to'          => $number,
                'mocean-text'        => $message,
                'mocean-resp-format' => 'json',
            ]
        );

        if ($response->failed()) {
            throw new \Exception(
                'Mocean SMS failed: ' . $response->body()
            );
        }

        return $response->json();
    }
}