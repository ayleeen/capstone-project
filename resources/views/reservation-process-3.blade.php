<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu Selection</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css') }}" rel="stylesheet">
<link href="{{ asset('css/client/reservation-process-3.css') }}" rel="stylesheet">
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
  <div class="step done">
    <div class="circle">3</div>
    <div class="step-label">Step 3<br>Menu Selection</div>
  </div>
  <div class="step ">
    <div class="circle">4</div>
    <div class="step-label">Step 4<br>Confirmation</div>
  </div>
   <div class="step">
    <div class="circle">5</div>
    <div class="step-label">Step 5<br>Payment</div>
  </div>
</div>

<div class="container choice-wrap">
  <div class="section-heading">
    <span class="line"></span>
    <h2>How would you like to continue</h2>
    <span class="line"></span>
  </div>

  <div class="row justify-content-center g-4 choice-row">
    <div class="col-md-5">
      <div class="card choice-card">
        <div class="choice-icon"><i class="fa-solid fa-utensils"></i></div>
        <h3>Customize Menu</h3>
        <p>Create a menu that fits your event style and preference.</p>
        <a href="{{ route('reservation.customize-menu', request()->query()) }}" class="btn btn-choice" id="customizeMenuBtn" data-choice="customize">Create Custom Menu</a>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card choice-card">
        <div class="choice-icon"><i class="fa-solid fa-utensils"></i></div>
        <h3>Package Menu</h3>
        <p>Create a menu that fits your event style and preference.</p>
        <a href="{{ route('reservation.package-menu', request()->query()) }}"
   class="btn btn-choice" id="packageMenuBtn" data-choice="package">Package Menu</a>
      </div>
    </div>
  </div>

  <div class="text-center choice-cancel">
    <a href="{{ route('reservation.process2', request()->query()) }}" class="btn btn-cancel-outline" id="cancelBtn">Cancel</a>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/client/reservation-process-3.js') }}"></script>
</body>
</html>