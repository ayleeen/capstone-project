<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Gallery</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{asset('css/client/homepage.css')}}" rel="stylesheet">
<link href="{{asset('css/client/gallery.css')}}" rel="stylesheet">
</head>
<body>

<!-- Top navbar -->
<header class="site">
  <div class="brand">
    <img src="../image/lolaysLogo.jpg" alt="Lolay's logo" class="logo-img">
    <div class="name">Lolay's<small>FOOD STATION &amp;<br>CATERING SERVICES</small></div>
  </div>
  <nav class="main-nav" id="siteNavLinks">
    <a href="{{ route('home') }}" >Home</a>
    <a href="{{ route('about-us') }}">About Us</a>
    <a href="{{ route('package') }}">Packages</a>
    <a href="{{ route('menu') }}">Menu</a>
    <a href="{{ route('gallery') }}" class="active">Gallery</a>
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

<button type="button" class="nav-toggle-btn" id="navToggleBtn" aria-label="Toggle menu">
    <i class="bi bi-list"></i>
</button>
  </div>
</header>

<!-- Gallery hero -->
<section class="gallery-hero">
  <div class="gallery-hero-circle">
    <h1>Gallery</h1>
    <span class="gallery-hero-line"></span>
    <p>Discover the magic of our catering services.</p>
  </div>
</section>

<!-- Gallery panel -->
<main class="gallery-wrap">
  <div class="gallery-panel">

    <div class="g-filters" id="gFilters">
      <button type="button" class="g-filter active" data-filter="all">ALL</button>
      <button type="button" class="g-filter" data-filter="weddings"><i class="fa-solid fa-ring"></i> WEDDINGS</button>
      <button type="button" class="g-filter" data-filter="birthdays"><i class="fa-solid fa-cake-candles"></i> BIRTHDAYS</button>
      <button type="button" class="g-filter" data-filter="corporate"><i class="fa-solid fa-briefcase"></i> CORPORATE EVENTS</button>
      <button type="button" class="g-filter" data-filter="special"><i class="fa-solid fa-location-dot"></i> SPECIAL OCCASION</button>
    </div>

    <div class="g-scroll">
      <div class="g-grid" id="gGrid"></div>
    </div>

    <div class="g-cta">
      <div class="g-cta-icon"><i class="fa-solid fa-star"></i></div>
      <div class="g-cta-text">
        <h4>Planning an event?</h4>
        <p>Let's make your special moment unforgettable.</p>
      </div>
      <a href="reservation-process-1.html">
        <button type="button" class="btn-reserve g-cta-btn reserve-now-btn"><i class="fa-regular fa-comment"></i> Reserve Now</button>
      </a>
    </div>
  </div>
</main>



<!-- Details popup -->
<div class="g-modal" id="gModal" aria-hidden="true">
  <div class="g-modal-box" role="dialog" aria-modal="true" aria-labelledby="gModalTitle">
    <button type="button" class="g-modal-close" id="gModalClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>
    <div class="g-modal-img" id="gModalImg"></div>
    <h3 id="gModalTitle"></h3>
    <span class="g-tag" id="gModalTag"></span>
    <p id="gModalDesc"></p>
    <button type="button" class="btn-reserve reserve-now-btn">Reserve Now</button>
  </div>
</div>
<!-- Footer -->
<?php include 'footer.html'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('js/client/gallery.js')}}"></script>
</body>
</html>