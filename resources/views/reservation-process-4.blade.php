<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmation</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css') }}" rel="stylesheet">
<link href="{{ asset('css/client/reservation-process-4.css') }}" rel="stylesheet">
</head>
<body>

<header class="site">
  <div class="brand">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="Lolay's logo" class="logo-img">
    <div class="name">Lolay's<small>FOOD STATION &amp;<br>CATERING SERVICES</small></div>
  </div>
  <nav class="main-nav" id="siteNavLinks">
    <a href="{{ route('home') }}">Home</a>
    <a href="{{ route('about-us') }}">About Us</a>
    <a href="{{ route('package') }}">Packages</a>
    <a href="{{ route('menu') }}">Menu</a>
    <a href="{{ route('gallery') }}">Gallery</a>
    <a href="{{ route('contact') }}">Contact</a>
  </nav>
  <div class="header-actions">
    <a href="{{ route('reservation.process1') }}" class="btn-reserve" id="reserveNowNavBtn">
      <i class="fa-regular fa-calendar-check"></i> Reserve Now
    </a>
    @auth
      <form method="POST" action="{{ route('logout') }}" style="display: inline;"
            onsubmit="sessionStorage.clear()">
        @csrf
        <button type="submit" class="btn-outline-nav">
          <i class="bi bi-box-arrow-right"></i> Logout
        </button>
      </form>
    @endauth
  </div>
</header>

<section class="hero">
  <h1>Great Events Start with Great Planning.</h1>
  <span class="hero-highlight">Reserve Now!</span>
  <p>Check Availability and secure your date in just a few steps.</p>
</section>

<div class="container" style="max-width:1080px;">

<div class="steps-wrap">
  <div class="step done"><div class="circle">1</div><div class="step-label">Step 1<br>Reservation</div></div>
  <div class="step done"><div class="circle">2</div><div class="step-label">Step 2<br>Event Details</div></div>
  <div class="step done"><div class="circle">3</div><div class="step-label">Step 3<br>Menu Selection</div></div>
  <div class="step active"><div class="circle">4</div><div class="step-label">Step 4<br>Confirmation</div></div>
  <div class="step"><div class="circle">5</div><div class="step-label">Step 5<br>Payment</div></div>
</div>

@php
  $s1 = $reservation['step1'];
  $s2 = $reservation['step2'];
  $s3 = $reservation['step3'];
@endphp

<div class="container summary-wrap">
  <div class="section-heading">
    <span class="line"></span>
    <h2>Review Your Reservation</h2>
    <span class="line"></span>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="summary-card">
        <h3>Event Details</h3>
        <div class="summary-field"><div class="label">Event Date</div>
          <div class="value" id="sumDate">{{ \Carbon\Carbon::parse($s1['selected_date'])->format('F j, Y') }}</div></div>
        <div class="summary-field"><div class="label">Time</div>
          <div class="value" id="sumTime">{{ \Carbon\Carbon::parse($s1['start_time'])->format('g:i A') }} - {{ \Carbon\Carbon::parse($s1['end_time'])->format('g:i A') }}</div></div>
        <div class="summary-field"><div class="label">Type of Event</div>
          <div class="value" id="sumEventType">{{ ucfirst($s2['event_type']) }}</div></div>
        <div class="summary-field"><div class="label">Number of Guest</div>
          <div class="value" id="sumGuests">{{ $s1['pax'] }}</div></div>
        <div class="summary-field"><div class="label">Food Restriction</div>
          <div class="value" id="sumRestriction">{{ $s3['food_restriction'] ?? 'None' }}</div></div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="summary-card">
        <h3>Price Breakdown</h3>
        <div id="priceRows">
          <div class="summary-field">
            <div class="label">{{ $package->package_name ?? 'Package' }}</div>
            <div class="value">₱{{ number_format($package->price ?? 0, 2) }}</div>
          </div>
        </div>
        <hr class="price-divider">
        <div class="grand-total-row">
          <span class="gt-label">Grand Total:</span>
          <span class="gt-value" id="sumTotal">₱{{ number_format($package->price ?? 0, 2) }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="summary-card">
        <h3>Contact Information</h3>
        <div class="summary-field"><div class="label">Name</div>
          <div class="value" id="sumName">{{ $s2['first_name'] }} {{ $s2['last_name'] }} {{ $s2['suffix'] ?? '' }}</div></div>
        <div class="summary-field"><div class="label">Email</div>
          <div class="value" id="sumEmail">{{ $s2['email'] }}</div></div>
        <div class="summary-field"><div class="label">Contact Number</div>
          <div class="value" id="sumPhone">{{ $s2['contact_number'] }}</div></div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="summary-card">
        <h3>Venue Information</h3>
        <div class="summary-field"><div class="label">Event Address</div>
          <div class="value" id="sumAddress">{{ $s2['event_street'] }}</div></div>
        <div class="summary-field"><div class="label">Landmark</div>
          <div class="value" id="sumLandmark">{{ $s2['landmark'] ?? '—' }}</div></div>
      </div>
    </div>
  </div>

  <div class="summary-actions">
    <button type="button" class="btn btn-cancel-outline" id="cancelBtn">Cancel</button>
    <a href="{{ route('reservation.process5') }}" class="btn btn-choice" id="continuePaymentBtn">Continue to Payment</a>
  </div>
</div>

<!-- Cancel confirmation modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cancel reservation?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        This will discard everything you've entered so far. This can't be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-cancel-outline" data-bs-dismiss="modal">Keep Reservation</button>
        <form method="POST" action="{{ route('reservation.cancel') }}" id="cancelForm">
          @csrf
          <button type="submit" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/client/reservation-process-4.js') }}"></script>
</body>
</html>