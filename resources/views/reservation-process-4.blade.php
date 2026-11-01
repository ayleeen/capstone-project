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
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&display=swap" rel="stylesheet">
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
  $isCustom = ($s3['type'] ?? null) === 'custom';
@endphp

<div class="container summary-wrap">

  <div class="res-card">

    <div class="res-head">
      <div class="res-title">
        <span class="res-title-icon"><i class="fa-regular fa-calendar-days"></i></span>
        <h2>Reservation Summary</h2>
      </div>
      <div class="date-pill">
        <i class="fa-regular fa-calendar-days"></i>
        <span>{{ \Carbon\Carbon::parse($s1['selected_date'])->format('F j, Y (l)') }}</span>
      </div>
    </div>

    <div class="res-body">

      {{-- LEFT --}}
      <div class="res-col res-left">

        <section class="res-section">
          <h3><i class="fa-regular fa-calendar-days"></i> Event Details</h3>
          <div class="info-row"><i class="fa-regular fa-clock"></i><span class="info-label">Time</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($s1['start_time'])->format('g:i A') }} - {{ \Carbon\Carbon::parse($s1['end_time'])->format('g:i A') }}</span></div>
          <div class="info-row"><i class="fa-solid fa-users"></i><span class="info-label">Number of Guests</span>
            <span class="info-value">{{ $s1['pax'] }}</span></div>
          <div class="info-row"><i class="fa-solid fa-utensils"></i><span class="info-label">Type of Event</span>
            <span class="info-value">{{ ucfirst($s2['event_type']) }}</span></div>
          <div class="info-row"><i class="fa-solid fa-bell-concierge"></i><span class="info-label">Food Restriction</span>
            <span class="info-value">{{ $s3['food_restriction'] ?? 'None' }}</span></div>
        </section>

        <section class="res-section dashed">
          <h3><i class="fa-solid fa-user"></i> Contact Information</h3>
          <div class="info-row"><i class="fa-regular fa-envelope"></i><span class="info-label">Name</span>
            <span class="info-value">{{ $s2['first_name'] }} {{ $s2['last_name'] }} {{ $s2['suffix'] ?? '' }}</span></div>
          <div class="info-row"><i class="fa-solid fa-envelope"></i><span class="info-label">Email</span>
            <span class="info-value">{{ $s2['email'] }}</span></div>
          <div class="info-row"><i class="fa-solid fa-phone"></i><span class="info-label">Contact Number</span>
            <span class="info-value">{{ $s2['contact_number'] }}</span></div>
        </section>

        <section class="res-section dashed">
          <h3><i class="fa-solid fa-location-dot"></i> Venue Information</h3>
          <div class="info-row top"><i class="fa-solid fa-map-location-dot"></i><span class="info-label">Event Address</span>
            <span class="info-value">{{ $s2['event_street'] }}</span></div>
          <div class="info-row"><i class="fa-solid fa-location-dot"></i><span class="info-label">Landmark</span>
            <span class="info-value">{{ $s2['landmark'] ?? '—' }}</span></div>
        </section>

      </div>

      {{-- RIGHT --}}
      <div class="res-col res-right">

        <div class="price-head">
          <span class="price-head-icon"><i class="fa-solid fa-wallet"></i></span>
          <h3>Price Breakdown</h3>
        </div>

@if ($isCustom)

    <div class="price-row">
        <span>
            Custom Menu
            <br>
            <small>₱{{ number_format($perHead) }} × {{ $pax }} pax</small>
        </span>
        <span>₱{{ number_format($customTotal, 2) }}</span>
    </div>

    {{-- MENU SETS --}}
    @foreach ($menuSets as $set)

        <div class="price-row menu-line">
            <span>
                <strong>Set {{ $set['set_number'] }}</strong>
                <small class="d-block">{{ $set['pax'] }} pax</small>
            </span>
            <span></span>
        </div>

        @foreach ($set['items'] as $item)
            <div class="price-row menu-line">
                <span>
                    <small>{{ ucfirst($item->category) }}</small>
                    {{ $item->name }}
                </span>
                <span class="incl">Included</span>
            </div>
        @endforeach

    @endforeach

    {{-- EXISTING ADD-ONS --}}
    @foreach ($addons as $a)
        <div class="price-row menu-line">
            <span>
                <small>Add-on</small>
                {{ $a['name'] }} ({{ $a['servings'] }} pax)
            </span>
            <span>₱{{ number_format($a['price'], 2) }}</span>
        </div>
    @endforeach

    <div class="price-row menu-line">
        <span><small>Rice</small> Steamed Rice</span>
        <span class="incl">Included</span>
    </div>

    <div class="price-row menu-line">
        <span><small>Water</small> Purified Drinking Water</span>
        <span class="incl">Included</span>
    </div>

@else
          <div class="price-row">
            <span>{{ $package->package_name ?? 'Package' }}</span>
            <span>₱{{ number_format($package->price ?? 0, 2) }}</span>
          </div>
        @endif

<div class="grand-total-row">
  <span class="gt-label">Grand Total</span>
  <span class="gt-value">₱{{ number_format($grandTotal, 2) }}</span>
</div>

        <div class="res-note">
          <i class="fa-solid fa-circle-info"></i>
          <p>This is a reservation summary. Final amount may vary based on additional requests or changes to your event details.</p>
          <i class="fa-solid fa-leaf note-leaf" aria-hidden="true"></i>
        </div>

      </div>
    </div>
  </div>

  <div class="summary-actions">
    <button type="button" class="btn btn-cancel-outline" id="cancelBtn">Back</button>
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

<!-- ===================== FOOTER (galing sa grupo, naka-route na) ===================== -->
<footer class="site-footer">
  <div class="footer-inner">

    <div class="footer-col footer-brand">
      <a href="{{ route('home') }}" class="footer-logo" aria-label="Lolay's - Home">
        <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="">
        <span class="footer-logo-text footer">
          <span class="name">Lolay's <small>FOOD STATION &amp; CATERING SERVICES</small></span>
        </span>
      </a>
      <p class="footer-about">
        Delicious food, complete setup, and professional service for all kinds of occasions.
        Let us make your special moments more memorable.
      </p>
      <div class="footer-social">
        <a href="https://facebook.com/lolayscatering" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
          <i class="fa-brands fa-facebook"></i>
        </a>
        <a href="mailto:lolaycatering@gmail.com" aria-label="Email Lolay's" title="Email us">
          <svg viewBox="0 0 48 48" aria-hidden="true" focusable="false">
            <path fill="#4caf50" d="M45 16.2l-5 2.75-5 4.75L35 40h7c1.657 0 3-1.343 3-3V16.2z"/>
            <path fill="#1e88e5" d="M3 16.2l3.614 1.71L13 23.7V40H6c-1.657 0-3-1.343-3-3V16.2z"/>
            <polygon fill="#e53935" points="35,11.2 24,19.45 13,11.2 12,17 13,23.7 24,31.95 35,23.7 36,17"/>
            <path fill="#c62828" d="M3 12.298V16.2l10 7.5V11.2L9.876 8.859A4.6 4.6 0 007.298 8C4.924 8 3 9.924 3 12.298z"/>
            <path fill="#fbc02d" d="M45 12.298V16.2l-10 7.5V11.2l3.124-2.341A4.6 4.6 0 0140.702 8C43.076 8 45 9.924 45 12.298z"/>
          </svg>
        </a>
      </div>
    </div>

    <nav class="footer-col" aria-label="Quick links">
      <h3 class="footer-heading">Quick Links</h3>
      <ul class="footer-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('package') }}">Packages</a></li>
        <li><a href="{{ route('about-us') }}">About Us</a></li>
        <li><a href="{{ route('gallery') }}">Gallery</a></li>
        <li><a href="{{ route('contact') }}">Contact</a></li>
      </ul>
    </nav>

    <nav class="footer-col" aria-label="Our services">
      <h3 class="footer-heading">Our Services</h3>
      <ul class="footer-links">
        <li><a href="{{ route('package') }}#wedding">Wedding Catering</a></li>
        <li><a href="{{ route('package') }}#birthday">Birthday Catering</a></li>
        <li><a href="{{ route('package') }}#corporate">Corporate Buffet</a></li>
        <li><a href="{{ route('package') }}#baptismal">Baptismal Catering</a></li>
        <li><a href="{{ route('package') }}#anniversary">Anniversary Catering</a></li>
      </ul>
    </nav>

    <div class="footer-col">
      <h3 class="footer-heading">Contact Us</h3>
      <ul class="footer-contact">
        <li>
          <span class="footer-icon"><i class="bi bi-geo-alt-fill"></i></span>
          <a href="https://www.google.com/maps/search/?api=1&amp;query=123+Barriada+St.+Gogon+Legazpi+City" target="_blank" rel="noopener">123 Barriada St.<br>Gogon Legazpi City</a>
        </li>
        <li>
          <span class="footer-icon"><i class="bi bi-telephone-fill"></i></span>
          <a href="tel:09876543234">09876543234</a>
        </li>
        <li>
          <span class="footer-icon"><i class="bi bi-envelope-fill"></i></span>
          <a href="mailto:lolaycatering@gmail.com">lolaycatering@gmail.com</a>
        </li>
      </ul>
    </div>

    <div class="footer-col">
      <h3 class="footer-heading">Business Hours</h3>
      <ul class="footer-contact">
        <li>
          <span class="footer-icon"><i class="bi bi-clock-fill"></i></span>
          <span>Monday - Sunday<br>8:00 AM - 7:00 PM</span>
        </li>
      </ul>
    </div>

  </div>

  <div class="footer-bottom">
    <p>&copy; <span id="footerYear">2026</span> Lolay's Food Station &amp; Catering Services. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/client/reservation-process-4.js') }}"></script>
<script>
  (function () {
    var y = document.getElementById('footerYear');
    if (y) y.textContent = new Date().getFullYear();
  })();
</script>
</body>
</html>