<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Details</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css') }}" rel="stylesheet">
<link href="{{ asset('css/client/reservation-process-2.css') }}" rel="stylesheet">
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

    @guest
      <a href="{{ route('login') }}" class="btn-outline-nav">
        <i class="bi bi-person"></i> Login/Register
      </a>
    @endguest

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
  <div class="step done">
    <div class="circle">1</div>
    <div class="step-label">Step 1<br>Reservation</div>
  </div>
  <div class="step done">
    <div class="circle">2</div>
    <div class="step-label">Step 2<br>Event Details</div>
  </div>
  <div class="step">
    <div class="circle">3</div>
    <div class="step-label">Step 3<br>Menu Selection</div>
  </div>
  <div class="step">
    <div class="circle">4</div>
    <div class="step-label">Step 4<br>Confirmation</div>
  </div>
   <div class="step">
    <div class="circle">5</div>
    <div class="step-label">Step 5<br>Payment</div>
  </div>
</div>

<div class="content">
  <!-- Sidebar -->
  <div>
    <div class="card">
  <h3>Your Reservation Summary</h3>

  @if(!empty($step1))
    <div class="summary-row">
      <strong>Date:</strong>
      {{ \Carbon\Carbon::parse($step1['selected_date'])->format('F j, Y') }}
    </div>
    <div class="summary-row">
      <strong>Time:</strong>
      {{ \Carbon\Carbon::parse($step1['start_time'])->format('g:i A') }}
      -
      {{ \Carbon\Carbon::parse($step1['end_time'])->format('g:i A') }}
    </div>
    <div class="summary-row">
      <strong>Guests:</strong> {{ $step1['pax'] }}
    </div>
    <a href="{{ route('reservation.process1') }}">Change date</a>
  @else
    <p>You haven't selected a reservation date yet.</p>
    <a href="{{ route('reservation.process1') }}" class="btn">Go to Step 1: Reservation</a>
  @endif
</div>

    <div class="card need-help" style="margin-top:18px;">
      <h3>Need Help?</h3>
      <p>Our team is here to assist you with your event planning.</p>
      <div class="contact-row"><span class="ico">&#128222;</span> 0988 - 254 - 0454</div>
      <div class="contact-row"><span class="ico">&#9993;</span> lulaykinemeshu@gmail.com</div>
    </div>
  </div>

  <!-- Form -->
  <div class="card form-card">
    <h3 class="form-title"><i class="fa-regular fa-calendar"></i> Event Details</h3>
    <div class="form-sub">Tell us about your event</div>

    <form id="eventDetailsForm" method="POST" action="{{ route('reservation.process2.store') }}">
  @csrf
      <div class="row g-3">
        <div class="col-md-5">
          <label class="form-label">First Name:</label>
          <input type="text" class="form-control" name="first_name" required
       value="{{ old('first_name', session('reservation.step2.first_name')) }}">
        </div>
        <div class="col-md-5">
          <label class="form-label">Last Name:</label>
          <input type="text" class="form-control" name="last_name" required
       value="{{ old('last_name', session('reservation.step2.last_name')) }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">Suffix <span class="opt">(optional)</span></label>
          <input type="text" class="form-control" name="suffix"
       value="{{ old('suffix', session('reservation.step2.suffix')) }}">
        </div>
      </div>

      <div class="row g-3 mt-1">
        <div class="col-md-6">
          <label class="form-label">Contact Number:</label>
          <input type="tel" class="form-control" name="contact_number" required
       value="{{ old('contact_number', session('reservation.step2.contact_number')) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email Address:</label>
          <input type="email" class="form-control" name="email" required
       value="{{ old('email', session('reservation.step2.email')) }}">
        </div>
      </div>

     <div class="row g-3 mt-1">
  <div class="col-md-6">
    <div class="row g-3">
      <div class="col-12">
  <label class="form-label">Type of Event:</label>

  @php $eventType = old('event_type', session('reservation.step2.event_type')); @endphp
  <select class="form-select" name="event_type" required>
    <option value="" disabled {{ $eventType ? '' : 'selected' }}>Select event type</option>
    @foreach(['wedding' => 'Wedding', 'birthday' => 'Birthday', 'corporate' => 'Corporate Event', 'debut' => 'Debut', 'christening' => 'Christening', 'other' => 'Other'] as $val => $label)
      <option value="{{ $val }}" {{ $eventType === $val ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
</div>
      <div class="col-12">
        <label class="form-label">Event Street / house no. / building</label>
        <input type="text" class="form-control" name="event_street" required
       value="{{ old('event_street', session('reservation.step2.event_street')) }}">
      </div>
      <div class="col-12">
        <label class="form-label">Landmark <span class="opt">(optional)</span>:</label>
        <input type="text" class="form-control" name="landmark"
       value="{{ old('landmark', session('reservation.step2.landmark')) }}">
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <label class="form-label">Map Preview</label>
    <div class="map-preview h-100" id="mapPreview" title="Map preview placeholder — wire up to Google Maps or your preferred map provider">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d242.820234554468!2d123.74733493284616!3d13.154593089910632!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a101279b0f9013%3A0xaecfd15d6cc15d80!2sLolay's%20Food%20Station%20%26%20Catering%20Services(NEW%20LOCATION)!5e0!3m2!1sen!2sph!4v1785314365128!5m2!1sen!2sph" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
    </div>
  </div>
</div>

      <hr class="form-divider">

      <h4 class="service-title"><i class="fa-regular fa-calendar"></i> EVENT SERVICE OPTION</h4>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Special Request <span class="opt">(optional)</span>:</label>
          <input type="text" class="form-control" name="special_request"
       value="{{ old('special_request', session('reservation.step2.special_request')) }}">
        </div>
      </div>
      @if ($errors->any())
  <div class="alert alert-danger mt-3">{{ implode(', ', $errors->all()) }}</div>
@endif
      <div class="form-actions">
<a href="{{ route('reservation.process1') }}" class="btn btn-cancel-outline" id="cancelBtn">Back</a>
        <button type="submit" class="btn btn-continue-full">Continue to Menu Selection</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/client/reservation-process-2.js') }}"></script>

</body>
</html>