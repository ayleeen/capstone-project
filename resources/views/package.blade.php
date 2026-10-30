<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Packages</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css')}}" rel="stylesheet">
<link href="{{ asset('css/client/package.css')}}" rel="stylesheet">
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
    <a href="{{ route('package') }}" class="active">Packages</a>
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

<!-- Hero -->
<section class="pkg-hero">
  <div class="pkg-hero-script">Choose your</div>
  <h1>PACKAGES</h1>
  <p>No description added yet.</p>
</section>

<div class="container" style="max-width:1100px;">

  <!-- Filter tabs -->
 <div class="pkg-tabs">
  <a href="package.html" class="btn active" id="allPackagesTab">All Packages</a>
<a href="{{ route('add-ons') }}" class="btn" id="addOnsTab">Add - ons</a>
</div>

<!-- Package list -->
@if($packages->count())
    <div class="row g-4">
        @foreach($packages as $package)
            <div class="col-md-4">
                <div class="package-card">
                    @if($package->photo)
                        <img src="{{ asset('storage/' . $package->photo) }}" alt="{{ $package->package_name }}" class="package-img">
                    @else
                        <div class="package-img package-img-empty"></div>
                    @endif

                    <div class="package-body">
                        <h5>{{ $package->package_name }}</h5>
                        <p class="text-muted mb-1">{{ $package->category }}</p>

<p>{{ $package->description }}</p>

@if(!empty($package->inclusions))
    <div class="package-inclusions">
        <strong>Package Inclusions:</strong>
        <ul>
            @foreach($package->inclusions as $inclusion)
                <li>{{ $inclusion }}</li>
            @endforeach
        </ul>
    </div>
@endif

<p>{{ $package->guest_min }} - {{ $package->guest_max }} guests</p>


                        <div class="package-footer">
                            <strong>₱{{ number_format($package->price, 2) }}</strong>
                            <a href="{{ route('reservation.process1') }}" class="btn btn-success">Reserve</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <p>No packages available yet</p>
    </div>
@endif

  <!-- Custom Packages -->
  <div class="custom-pkg-bar">
    <div class="custom-pkg-icon"><i class="bi bi-stars"></i></div>
    <div>
      <div class="custom-pkg-title">Custom Packages</div>
      <div class="custom-pkg-desc">No description added yet.</div>
    </div>
    <button type="button" class="btn-inquire"><i class="bi bi-chat-dots me-1"></i>Inquire Now</button>
  </div>

</div>

<!-- Footer -->
<?php include 'footer.html'; ?>
<script>
  window.packageData = @json($packages);
  window.initialPackageId = @json(session('reservation.step3.package_id'));
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/client/homepage.js')}}"></script>
</body>
</html>