<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css') }}" rel="stylesheet">
<link href="{{ asset('css/client/reservation-process-5.css') }}" rel="stylesheet">
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

@php
  $s1 = $reservation['step1'];
  $s2 = $reservation['step2'];
  $total = $package->price ?? 0;
  $down = $total * 0.5;
  $remaining = $total - $down;
  $date = \Carbon\Carbon::parse($s1['selected_date']);
@endphp

<div class="container" style="max-width:1080px;">

  <div class="steps-wrap">
    <div class="step done"><div class="circle">1</div><div class="step-label">Step 1<br>Reservation</div></div>
    <div class="step done"><div class="circle">2</div><div class="step-label">Step 2<br>Event Details</div></div>
    <div class="step done"><div class="circle">3</div><div class="step-label">Step 3<br>Menu Selection</div></div>
    <div class="step done"><div class="circle">4</div><div class="step-label">Step 4<br>Confirmation</div></div>
    <div class="step active"><div class="circle">5</div><div class="step-label">Step 5<br>Payment</div></div>
  </div>

  <form method="POST" action="{{ route('reservation.confirm') }}" enctype="multipart/form-data" id="paymentForm">
    @csrf

    <div class="payment-grid">

      <!-- LEFT -->
      <div class="pay-card">
        <h3 class="card-title">Event Information</h3>

        <div class="event-info-row">
          <div class="info-item">
            <div class="info-icon"><i class="fa-regular fa-calendar"></i></div>
            <div class="info-label">Date</div>
            <div class="info-value" id="infoDate">{{ $date->format('F j, Y') }}</div>
            <div class="info-value" id="infoDay">{{ $date->format('l') }}</div>
          </div>
          <div class="info-item">
            <div class="info-icon"><i class="fa-regular fa-clock"></i></div>
            <div class="info-label">Time</div>
            <div class="info-value" id="infoTime">
              {{ \Carbon\Carbon::parse($s1['start_time'])->format('g:i A') }} -
              {{ \Carbon\Carbon::parse($s1['end_time'])->format('g:i A') }}
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon"><i class="fa-solid fa-users"></i></div>
            <div class="info-label">Pax</div>
            <div class="info-value" id="infoPax">{{ $s1['pax'] }}</div>
          </div>
          <div class="info-item">
            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="info-label">Venue</div>
            <div class="info-value" id="infoVenue">{{ $s2['event_street'] }}</div>
          </div>
        </div>

        <hr class="card-divider">

        <h3 class="card-title">Reservation Summary</h3>

        <div id="priceRows">
          <div class="sum-row">
            <span>{{ $package->package_name ?? 'Package' }}</span>
            <span>₱{{ number_format($total, 2) }}</span>
          </div>
        </div>

        <hr class="summary-divider">

        <div class="sum-row"><span>Sub Total</span><span id="subTotal">₱{{ number_format($total, 2) }}</span></div>
        <div class="sum-row green"><span>50% Down Payment</span><span id="downPayment">₱{{ number_format($down, 2) }}</span></div>
        <div class="sum-row"><span>Remaining Balance</span><span id="remainingBalance">₱{{ number_format($remaining, 2) }}</span></div>

        <div class="amount-box">
          <span>Amount to Pay:</span>
          <span id="amountToPay">₱{{ number_format($down, 2) }}</span>
        </div>
      </div>

      <!-- MIDDLE -->
      <div class="middle-col">
        <div class="pay-card qr-card">
          <h3 class="card-title">Scan to Pay</h3>
          <p class="card-sub">Scan the QR code using your preferred app</p>
          <div class="qr-frame">
            <div class="merchant-name" id="merchantName">Lolay's Food Station &amp; Catering</div>
            <img id="qrImage" src="{{ asset('image/payment-qr.png') }}" alt="Payment QR code">
          </div>
        </div>

        <div class="pay-card upload-card">
          <h3 class="card-title">Upload Proof of Payment</h3>
          <input type="file" name="proof" id="proofFile" class="form-control form-control-sm"
                 accept=".jpg,.jpeg,.png,.pdf" required>
          <div class="upload-note">Accepted File: JPG, JPEG, PNG, PDF<br>Max file size: 5MB</div>
          <div class="upload-error" id="uploadError">
            @error('proof') {{ $message }} @enderror
          </div>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="pay-card reminder-card">
        <h3 class="card-title">Payment Reminder</h3>
        <ul class="reminder-list">
          <li><i class="fa-regular fa-circle-check"></i><span>A 50% down payment is required to confirm your reservation.</span></li>
          <li><i class="fa-regular fa-circle-check"></i><span>The remaining balance must be settled on or during the event date.</span></li>
          <li><i class="fa-regular fa-circle-check"></i><span>Please upload a clear proof of payment after completing your transaction.</span></li>
          <li><i class="fa-regular fa-circle-check"></i><span>A confirmation notification will be sent once your payment has been successfully verified.</span></li>
          <li><i class="fa-regular fa-circle-check"></i><span>Reservations are not considered confirmed until the down payment has been verified by our staff.</span></li>
        </ul>
      </div>
    </div>

    <div class="summary-actions">
      <a href="{{ route('reservation.process4') }}" class="btn btn-cancel-outline">Back</a>
      <button type="submit" class="btn btn-choice" id="confirmBtn">Confirm Reservation</button>
    </div>
  </form>

</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successTitle" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content success-content text-center">
      <div class="modal-body p-4">
        <div class="success-icon"><i class="fa-solid fa-check"></i></div>
        <h5 class="success-title" id="successTitle">Reservation Successful!</h5>
        <p class="success-text">Thank you! Your reservation has been submitted and your proof of payment was received.</p>

        <div class="success-summary">
          <div class="ss-row"><span>Date</span><strong>{{ $date->format('F j, Y') }}</strong></div>
          <div class="ss-row"><span>Time</span><strong>{{ \Carbon\Carbon::parse($s1['start_time'])->format('g:i A') }} - {{ \Carbon\Carbon::parse($s1['end_time'])->format('g:i A') }}</strong></div>
          <div class="ss-row"><span>Pax</span><strong>{{ $s1['pax'] }}</strong></div>
          <div class="ss-row"><span>Venue</span><strong>{{ $s2['event_street'] }}</strong></div>
          <div class="ss-row ss-total"><span>Down payment sent</span><strong>₱{{ number_format($down, 2) }}</strong></div>
        </div>

        <p class="success-note"><i class="fa-regular fa-clock"></i> We'll notify you once our staff has verified your 50% down payment. Your reservation is confirmed after verification.</p>

        <a href="{{ route('reservation.done') }}" class="btn btn-choice" id="doneBtn">Back to Home</a>
      </div>
    </div>
  </div>
</div>

@if(session('reservation_success'))
<script>
  document.addEventListener('DOMContentLoaded', function () {
    sessionStorage.clear();
    new bootstrap.Modal(document.getElementById('successModal')).show();
  });
</script>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/client/reservation-process-5.js') }}"></script>
</body>
</html>