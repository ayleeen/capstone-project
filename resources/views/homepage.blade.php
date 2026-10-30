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
 <!-- Top navbar -->
<!-- @include('client-header') -->
<header class="site">
  <div class="brand">
    <img src="../image/lolaysLogo.jpg" alt="Lolay's logo" class="logo-img">
    <div class="name">Lolay's<small>FOOD STATION &amp;<br>CATERING SERVICES</small></div>
  </div>
  <nav class="main-nav" id="siteNavLinks">
    <a href="{{ route('home') }}" class="active">Home</a>
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
  @php
    $user = auth()->user();
    $avatar = !empty($user->profile_photo)
        ? asset('storage/' . $user->profile_photo)
        : 'https://ui-avatars.com/api/?background=random&name=' . urlencode($user->name);
  @endphp

  <div class="dropdown profile-dropdown">
    <button class="profile-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="{{ $avatar }}" class="profile-avatar" alt="">
      <span class="profile-text">
        <span class="profile-name">{{ $user->name }}</span>
        <span class="profile-role">Customer</span>
      </span>
      <i class="bi bi-chevron-down profile-caret"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end profile-menu">
      <li>
        <a class="dropdown-item" href="{{ route('profile.edit') }}">
          <i class="bi bi-person"></i> My Profile
        </a>
      </li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form method="POST" action="{{ route('logout') }}" onsubmit="sessionStorage.clear()">
          @csrf
          <button type="submit" class="dropdown-item text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
          </button>
        </form>
      </li>
    </ul>
  </div>
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

<div class="row g-3 mb-4">

  <div class="col-6 col-md-3">
    <div class="service-card">
      <img src="{{ asset('image//wedding.jpg') }}" alt="Wedding">
      <div class="service-title">Wedding</div>
    </div>
  </div>

  <div class="col-6 col-md-3">
    <div class="service-card">
      <img src="{{ asset('image/859884544_2577629652700102_5420390211546807245_n.jpg') }}" alt="Birthday">
      <div class="service-title">Birthday</div>
    </div>
  </div>

  <div class="col-6 col-md-3">
    <div class="service-card">
      <img src="{{ asset('image/christening.jpg') }}" alt="Corporate Event">
      <div class="service-title">Christening</div>
    </div>
  </div>

  <div class="col-6 col-md-3">
    <div class="service-card">
      <img src="{{ asset('image/769013166_1490124979797930_7668920173544849492_n.jpg') }}" alt="Debut">
      <div class="service-title">Debut</div>
    </div>
  </div>

</div>
  <!-- Featured Packages -->
  <div class="section-header" id="featuredPackages"><span>Featured Packages</span></div>

  @if($packages->count())
    <div class="fp-row mb-4">
      @foreach($packages as $package)
        @php
          // Hinihiwalay ang description sa bawat bagong linya o comma para maging bullets
          $items = preg_split('/[\r\n,]+/', (string) $package->description, -1, PREG_SPLIT_NO_EMPTY);
        @endphp

        <div class="fp-card">
          <div class="fp-info">
            <span class="fp-tag">{{ $package->package_name }}</span>

            <div class="fp-pax">
              <i class="bi bi-people"></i>
              {{ $package->guest_min }} - {{ $package->guest_max }} PAX
            </div>

            <ul class="fp-list">
              @foreach(array_slice($items, 0, 4) as $item)
                <li>{{ trim($item) }}</li>
              @endforeach
            </ul>

            <div class="fp-price">₱ {{ number_format($package->price, 0) }}</div>
          </div>

          <div class="fp-photo">
            @if($package->photo)
              <img src="{{ asset('storage/' . $package->photo) }}" alt="{{ $package->package_name }}">
            @else
              <div class="fp-photo-empty"><i class="bi bi-image"></i></div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="section-empty-note mb-4">
      <i class="bi bi-box-seam"></i>
      No packages available yet
    </div>
  @endif

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
<?php include 'footer.html'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('js/client/homepage.js')}}"></script>
</body>
</html>