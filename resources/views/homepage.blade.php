<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Home</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{asset('css/client/homepage.css')}}" rel="stylesheet">
</head>
<body>

<!-- Top navbar -->
<header class="site">
  <div class="brand">
    <img src="../image/lolaysLogo.jpg" alt="Lolay's logo" class="logo-img">
    <div class="name">Lolay's<small>FOOD STATION &amp;<br>CATERING SERVICES</small></div>
  </div>
  <nav class="main-nav" id="siteNavLinks">
    <a href="{{ route('home') }}" class="active">Home</a>
    <a href="{{ route('about-us') }}">About Us</a>
    <a href="{{ route('package') }}">Packages</a>
    <a href="#">Menu</a>
    <a href="#">Gallery</a>
    <a href="#">Contact</a>
  </nav>
  <div class="header-actions">
<button type="button" class="btn-reserve" id="reserveNowNavBtn">
    <i class="fa-regular fa-calendar-check"></i> Reserve Now
</button>

@guest
    <a href="{{ route('login') }}" class="btn-outline-nav">
        <i class="bi bi-person"></i> Login/Register
    </a>
@endguest

@auth
    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
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

<!-- Hero carousel -->
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>

  <div class="carousel-inner">

    <div class="carousel-item active">
      <section class="hero-home hero-slide-1">
        <div class="hero-home-inner">
          <h1>Make Every Event</h1>
          <span class="hero-home-highlight">Memorable</span>
          <p class="hero-placeholder">No description added yet.</p>
          <div class="hero-home-actions">
            <button type="button" class="btn-hero-primary reserve-now-btn">Reserve Now</button>
            <button type="button" class="btn-hero-outline view-packages-btn">View Packages</button>
          </div>
        </div>
      </section>
    </div>

    <div class="carousel-item">
      <section class="hero-home hero-slide-2">
        <div class="hero-home-inner">
          <h1>Delicious Food</h1>
          <span class="hero-home-highlight">Every Time</span>
          <p class="hero-placeholder">No description added yet.</p>
          <div class="hero-home-actions">
            <button type="button" class="btn-hero-primary reserve-now-btn">Reserve Now</button>
            <button type="button" class="btn-hero-outline view-packages-btn">View Packages</button>
          </div>
        </div>
      </section>
    </div>

    <div class="carousel-item">
      <section class="hero-home hero-slide-3">
        <div class="hero-home-inner">
          <h1>Book Your Event</h1>
          <span class="hero-home-highlight">Today</span>
          <p class="hero-placeholder">No description added yet.</p>
          <div class="hero-home-actions">
            <button type="button" class="btn-hero-primary reserve-now-btn">Reserve Now</button>
            <button type="button" class="btn-hero-outline view-packages-btn">View Packages</button>
          </div>
        </div>
      </section>
    </div>

  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="hero-carousel-arrow"><i class="bi bi-chevron-left"></i></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="hero-carousel-arrow"><i class="bi bi-chevron-right"></i></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<!-- Trust badges -->
<section class="trust-badges">
  <div class="trust-badge">
    <div class="tb-icon"><i class="bi bi-award"></i></div>
    <div>
      <div class="tb-title">Trusted Since &mdash;</div>
      <div class="tb-sub">No description added yet.</div>
    </div>
  </div>
  <div class="trust-badge">
    <div class="tb-icon"><i class="bi bi-egg-fried"></i></div>
    <div>
      <div class="tb-title">Quality &amp; Taste</div>
      <div class="tb-sub">No description added yet.</div>
    </div>
  </div>
  <div class="trust-badge">
    <div class="tb-icon"><i class="bi bi-people"></i></div>
    <div>
      <div class="tb-title">Professional Team</div>
      <div class="tb-sub">No description added yet.</div>
    </div>
  </div>
  <div class="trust-badge">
    <div class="tb-icon"><i class="bi bi-shield-check"></i></div>
    <div>
      <div class="tb-title">Easy &amp; Secure</div>
      <div class="tb-sub">No description added yet.</div>
    </div>
  </div>
</section>

<div class="container" style="max-width:1100px;">

  <!-- Our Services -->
  <div class="section-header"><span>Our Services</span></div>
  <div class="section-empty-note mb-4">
    <i class="bi bi-grid-3x3-gap"></i>
    No services available yet
  </div>

  <!-- Featured Packages -->
  <div class="section-header" id="featuredPackages"><span>Featured Packages</span></div>
  <div class="section-empty-note mb-4">
    <i class="bi bi-box-seam"></i>
    No packages available yet
  </div>

  <!-- How It Works -->
  <div class="row g-3 mb-5">
    <div class="col-lg-8">
      <div class="section-header hiw-header"><span>How It Works</span></div>
      <div class="hiw-row">
        <div class="hiw-step">
          <div class="hiw-icon"><i class="bi bi-person-plus"></i></div>
          <div class="hiw-label">Create Account</div>
        </div>
        <div class="hiw-step">
          <div class="hiw-icon"><i class="bi bi-calendar2-check"></i></div>
          <div class="hiw-label">Check Available Date</div>
        </div>
        <div class="hiw-step">
          <div class="hiw-icon"><i class="bi bi-journal-text"></i></div>
          <div class="hiw-label">Select Package/Menu</div>
        </div>
        <div class="hiw-step">
          <div class="hiw-icon"><i class="bi bi-send-check"></i></div>
          <div class="hiw-label">Submit Reservation</div>
        </div>
        <div class="hiw-step">
          <div class="hiw-icon"><i class="bi bi-qr-code"></i></div>
          <div class="hiw-label">Upload QR Payment</div>
        </div>
        <div class="hiw-step">
          <div class="hiw-icon"><i class="bi bi-check2-circle"></i></div>
          <div class="hiw-label">Receive Confirmation</div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="testimonial-panel">
        <div class="tp-title">What Our Clients Say</div>
        <div class="section-empty-note" style="padding:24px 10px;">
          <i class="bi bi-chat-square-text"></i>
          No reviews yet
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Footer -->
<footer class="site-footer">
  <div class="logo-circle">🍽️</div>
  <div class="footer-text">Lolay's<br>FOOD STATION &amp; CATERING SERVICES</div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('js/client/homepage.js')}}"></script>
</body>
</html>