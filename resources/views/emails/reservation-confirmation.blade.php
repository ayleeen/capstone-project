<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reservation Confirmation</title>
</head>

<body style="margin:0; padding:0; background:#f4f4f4; font-family:Arial, sans-serif;">

    <div style="max-width:600px; margin:30px auto; background:#ffffff; padding:30px; border-radius:10px;">

        <h2 style="margin-top:0;">
            Lolay's Catering Services
        </h2>

        <p>
            Dear <strong>{{ $reservation->full_name }}</strong>,
        </p>

        <p>
            Thank you for choosing Lolay's Catering Services.
            We are pleased to confirm your reservation.
        </p>

        <hr>

        <h3>Reservation Details</h3>

        <p>
            <strong>Reservation ID:</strong>
            RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}
        </p>

        <p>
            <strong>Event:</strong>
            {{ $reservation->event_title }}
        </p>

        <p>
            <strong>Event Type:</strong>
            {{ ucfirst($reservation->event_type) }}
        </p>

        <p>
            <strong>Date:</strong>
            {{ $reservation->start_date->format('F j, Y') }}
        </p>

        @if($reservation->start_time)
            <p>
                <strong>Time:</strong>
                {{ $reservation->start_time }}
            </p>
        @endif

        @if($reservation->location)
            <p>
                <strong>Location:</strong>
                {{ $reservation->location }}
            </p>
        @endif

        <p>
            <strong>Number of Guests:</strong>
            {{ $reservation->guests }}
        </p>

        @if($reservation->package)
            <p>
                <strong>Package:</strong>
                {{ $reservation->package }}
            </p>
        @endif

        <p>
            <strong>Status:</strong>
            {{ ucfirst($reservation->status) }}
        </p>

        <hr>

        <p>
            If you have any questions regarding your reservation,
            please contact us.
        </p>

        <p>
            Thank you for choosing
            <strong>Lolay's Catering Services</strong>.
        </p>

    </div>

</body>
</html>