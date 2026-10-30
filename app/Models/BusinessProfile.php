<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $fillable = [
        'business_name',
        'description',
        'email',
        'contact_number',
        'address',
        'website',
        'facebook',
        'logo',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'business_name' => "Lolay's Catering Services",
                'description' => 'We provide delicious and affordable catering services for all kinds of events. From birthdays to corporate gatherings, we make every occasion extra special.',
                'email' => 'lolayscatering@gmail.com',
                'contact_number' => '+63 912 345 6789',
                'address' => '123 Food Street, San Pedro, Laguna',
                'website' => 'www.lolayscatering.com',
                'facebook' => 'facebook.com/lolayscatering',
            ]
        );
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('image/lolaysLogo.jpg');
    }
}
