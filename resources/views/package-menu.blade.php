<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Package Selection</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css') }}" rel="stylesheet">
<link href="{{ asset('css/client/package-menu.css') }}" rel="stylesheet">
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
    <div class="step"><div class="circle">4</div><div class="step-label">Step 4<br>Confirmation</div></div>
    <div class="step"><div class="circle">5</div><div class="step-label">Step 5<br>Payment</div></div>
  </div>

  <form method="POST" action="{{ route('reservation.package-menu.store') }}" id="packageForm">
    @csrf
    <input type="hidden" name="package_id" id="selectedPackageId"
           value="{{ session('reservation.step3.package_id') }}">

    <div class="panel">
      <h3>Package Offered</h3>
      <div class="package-grid" id="packageGrid">
        <!-- rendered by JS -->
      </div>

      <div class="food-restriction">
        <label for="foodRestriction">Food Restriction</label>
        <textarea id="foodRestriction" name="food_restriction" placeholder="">{{ old('food_restriction', session('reservation.step3.food_restriction')) }}</textarea>
      </div>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger mt-3">{{ implode(', ', $errors->all()) }}</div>
    @endif

    <div class="action-bar mb-4">
      <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
      <button type="submit" class="btn-continue" id="continueBtn" disabled>Continue to Confirmation</button>
    </div>
  </form>

</div>


<script>
    window.packageData = @json($packages);

    window.initialPackageId = @json(
        session('reservation.step3.package_id')
    );
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/client/package-menu.js') }}"></script>

</body>
</html>