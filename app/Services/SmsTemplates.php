<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

class SmsTemplates
{
    public static function confirmation(Reservation $r): string
    {
        return "Lolay's Catering: Hi {$r->full_name}! "
            . "Your reservation " . self::code($r) . " is CONFIRMED. "
            . "{$r->event_title} on " . self::when($r) . ". "
            . "Guests: {$r->guests}. Thank you!";
    }

    public static function cancellation(Reservation $r): string
    {
        return "Lolay's Catering: Hi {$r->full_name}, "
            . "your reservation " . self::code($r) . " has been CANCELLED. "
            . "Please contact us if you have questions.";
    }

    private static function code(Reservation $r): string
    {
        return 'RES-' . str_pad($r->id, 4, '0', STR_PAD_LEFT);
    }

    private static function when(Reservation $r): string
    {
        $date = optional($r->start_date)->format('M j, Y');

        if ($r->start_time) {
            $date .= ' ' . Carbon::parse($r->start_time)->format('g:i A');
        }

        return $date;
    }
}