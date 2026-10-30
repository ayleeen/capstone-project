<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Menu</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css')}}" rel="stylesheet">
<link href="{{ asset('css/client/menu.css')}}" rel="stylesheet">
</head>
<body>

<!-- Top navbar -->
<header class="site">
  <div class="brand">
    <img src="../image/lolaysLogo.jpg" alt="Lolay's logo" class="logo-img">
    <div class="name">Lolay's<small>FOOD STATION &amp;<br>CATERING SERVICES</small></div>
  </div>
  <nav class="main-nav" id="siteNavLinks">
    <a href="{{ route('home') }}">Home</a>
    <a href="{{ route('about-us') }}">About Us</a>
    <a href="{{ route('package') }}">Packages</a>
    <a href="{{ route('menu') }}" class="active">Menu</a>
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

<button type="button" class="nav-toggle-btn" id="navToggleBtn" aria-label="Toggle menu">
    <i class="bi bi-list"></i>
</button>
  </div>
</header>

<!-- Hero -->
<section class="menu-hero">
  <div class="menu-hero-script">Our</div>
  <h1>Menu</h1>
  <p>No description added yet.</p>
</section>

<div class="container" style="max-width:1100px;">

  <!-- Filter pills -->
<!-- Filter pills -->
<div class="menu-filter-pills">
  <button type="button" class="btn active" data-filter="all"><i class="bi bi-grid"></i> All Menu</button>
  @foreach ($categories as $key => $label)
    <button type="button" class="btn" data-filter="{{ $key }}">
      <i class="bi {{ $key === 'juice' ? 'bi-cup-straw' : 'bi-egg-fried' }}"></i> {{ $label }}
    </button>
  @endforeach
</div>

  <!-- ===== "All Menu" view — the original category panels ===== -->
<div id="menuAllView">
  <div class="row g-3">
    @foreach ($categories as $key => $label)
      <div class="col-lg-4 menu-section" data-section="{{ $key }}">
        <div class="menu-cat-panel">
          <div class="menu-cat-title">
            <i class="bi {{ $key === 'juice' ? 'bi-cup-straw' : 'bi-egg-fried' }}"></i> {{ $label }}
          </div>
          @include('partials.menu-items', ['items' => $menuItems->get($key, collect())])
        </div>
      </div>
    @endforeach
  </div>
</div>



  <!-- ===== Single-category view — shown when a specific pill is clicked ===== -->
  <div id="menuGridView" class="menu-grid-panel" style="display:none;">
    <div class="menu-item-grid" id="menuGridContainer">
      <!-- rendered by menu.js -->
    </div>
  </div>

  <!-- Custom Menu -->
  <div class="custom-menu-bar">
    <div class="custom-menu-icon"><i class="bi bi-stars"></i></div>
    <div>
      <div class="custom-menu-title">Custom Menu</div>
      <div class="custom-menu-desc">No description added yet.</div>
    </div>
    <button type="button" class="btn-inquire"><i class="bi bi-chat-dots me-1"></i>Inquire Now</button>
  </div>

</div>

<!-- Footer -->
<?php include 'footer.html'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/client/menu.js') }}"></script>
</body>
</html>