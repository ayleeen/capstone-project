<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;

class NewReservationNotification extends Notification
{
    public function __construct(public Reservation $reservation) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

public function toArray($notifiable): array
{
    return [
        'reservation_id' => $this->reservation->id,
        'message'        => 'New reservation from ' . $this->reservation->full_name,
        'event_date'     => $this->reservation->start_date,
    ];
}
}