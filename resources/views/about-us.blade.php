<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - About Us</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css')}}" rel="stylesheet">
<link href="{{ asset('css/client/about-us.css')}}" rel="stylesheet">
</head>
<body>

@include('client-header')

<!-- Hero -->
<section class="about-hero">
  <h1>About Us</h1>
  <p>Learn more about Lolay’s Food Station & Catering Services, our story, our passion, and our commitment to serving you.</p>
</section>

<div class="container" style="max-width:1100px;">

  <!-- Our Story + Mission/Vision/Values -->
  <div class="row g-4 align-items-start" style="margin-top:36px;">
    <div class="col-lg-4">
      <div class="story-label">Our Story</div>
      <div class="story-heading">No heading added yet</div>
      <p class="story-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum..</p>
      <button type="button" class="btn-hero-primary" style="background:var(--brand-green);color:#fff;">View Our Full Packages</button>
    </div>

    <div class="col-lg-4">
      <div class="story-img-placeholder"><img src="../image/catering.jpg" alt=""></div>
    </div>

    <div class="col-lg-4">
      <div class="mv-item">
        <div class="mv-icon"><i class="bi bi-bullseye"></i></div>
        <div>
          <div class="mv-title">Our Mission</div>
          <div class="mv-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
        </div>
      </div>
      <div class="mv-item">
        <div class="mv-icon"><i class="bi bi-eye"></i></div>
        <div>
          <div class="mv-title">Our Vision</div>
          <div class="mv-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
        </div>
      </div>
      <div class="mv-item">
        <div class="mv-icon"><i class="bi bi-heart"></i></div>
        <div>
          <div class="mv-title">Our Values</div>
          <div class="mv-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Stats -->
<section class="about-stats" style="margin-top:40px;">
  <div class="stat-block">
    <div class="sb-icon"><i class="bi bi-calendar-check"></i></div>
    <div>
      <div class="sb-value">25</div>
      <div class="sb-label">Years of Service</div>
    </div>
  </div>
  <div class="stat-block">
    <div class="sb-icon"><i class="bi bi-people-fill"></i></div>
    <div>
      <div class="sb-value">500+</div>
      <div class="sb-label">Events Catered</div>
    </div>
  </div>
  <div class="stat-block">
    <div class="sb-icon"><i class="bi bi-emoji-smile"></i></div>
    <div>
      <div class="sb-value">95%</div>
      <div class="sb-label">Customer Satisfaction</div>
    </div>
  </div>
  <div class="stat-block">
    <div class="sb-icon"><i class="bi bi-egg-fried"></i></div>
    <div>
      <div class="sb-value">25</div>
      <div class="sb-label">Menu Selections</div>
    </div>
  </div>
</section>

<!-- Visit Us -->
<div class="container" style="max-width:1100px;">
  <div class="row g-3 mb-5" style="margin-top:10px;">
    <div class="col-lg-8">
      <div class="visit-panel">
        <div class="row g-3">
          <div class="col-md-7">
            <div class="visit-title">Visit Us</div>
            <div class="visit-row">
              <div class="visit-icon"><i class="bi bi-geo-alt"></i></div>
              <div class="visit-text">123 Barriada St. Gogon Legazpi City</div>
            </div>
            <div class="visit-row">
              <div class="visit-icon"><i class="bi bi-telephone"></i></div>
              <div class="visit-text">09876543234</div>
            </div>
            <div class="visit-row">
              <div class="visit-icon"><i class="bi bi-envelope"></i></div>
              <div class="visit-text">
lolaycatering@gmail.com</div>
            </div>
            <div class="visit-row">
              <div class="visit-icon"><i class="bi bi-clock"></i></div>
              <div class="visit-text">Monday - Sunday
8:00 AM - 7:00 PM</div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="visit-blurb">
              <i class="bi bi-cup-hot"></i>
              <p>No description added yet.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="map-placeholder">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3885.120903500503!2d123.74471401065834!3d13.154772887124174!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a101279b0f9013%3A0xaecfd15d6cc15d80!2sLolay's%20Food%20Station%20%26%20Catering%20Services(NEW%20LOCATION)!5e0!3m2!1sen!2sph!4v1787463809060!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
     
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