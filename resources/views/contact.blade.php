<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Contact Us</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{asset('css/client/homepage.css')}}" rel="stylesheet">
<link href="{{asset('css/client/contact.css')}}" rel="stylesheet">
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
    <a href="{{ route('menu') }}">Menu</a>
    <a href="{{ route('gallery') }}">Gallery</a>
    <a href="{{ route('contact') }}" class="active">Contact</a>
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
<section class="contact-hero">
  <div class="contact-hero-circle">
    <h1>Contact Us</h1>
    <span class="contact-hero-line"></span>
    <p>We'd love to make your event memorable.</p>
  </div>
</section>

<!-- Content -->
<section class="contact-section">
  <div class="contact-panel">

    <div class="contact-grid">

      <!-- LEFT: Get in Touch -->
      <div class="contact-card contact-info-card">
        <h2>Get in Touch</h2>
        <p class="contact-lead">Have questions or want to book an event? We're here to help!<br>Feel free to reach out to us through any of the following channels.</p>

        <div class="contact-item-row">
          <div class="contact-item-icon"><i class="bi bi-geo-alt-fill"></i></div>
          <div>
            <div class="ci-label">Address</div>
            <div class="ci-text">821 Barriada Road, Legazpi City, Albay<br>(in front of Kalayaan Park)</div>
          </div>
        </div>

        <div class="contact-item-row">
          <div class="contact-item-icon"><i class="bi bi-telephone-fill"></i></div>
          <div>
            <div class="ci-label">Phone</div>
            <div class="ci-text"> +63 920 938 0067 / +63 935 818 9298</div>
          </div>
        </div>

        <div class="contact-item-row">
          <div class="contact-item-icon"><i class="bi bi-envelope-fill"></i></div>
          <div>
            <div class="ci-label">Email</div>
            <div class="ci-text">lolayscatering@gmail.com</div>
          </div>
        </div>

        <div class="contact-item-row">
          <div class="contact-item-icon"><i class="bi bi-clock-fill"></i></div>
          <div>
            <div class="ci-label">Business Hours</div>
            <div class="ci-text">Monday - Sunday<br>8:00 AM - 7:00 PM</div>
          </div>
        </div>

        <!-- Google map -->
       <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3885.12090329645!2d123.7472943!3d13.1547729!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a101279b0f9013%3A0xaecfd15d6cc15d80!2sLolay's%20Food%20Station%20%26%20Catering%20Services(NEW%20LOCATION)!5e0!3m2!1sen!2sph!4v1793023737081!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
      </div><!-- /LEFT card -->

      <!-- RIGHT: Send Us a Message -->
      <div class="contact-card contact-form-card">
        <h2>Send Us a Message</h2>
        <p class="contact-lead">Fill out the form below and we'll get back to you as soon as possible.</p>

        <form id="contactForm" class="contact-form" novalidate>
          <div class="cf-row">
            <div class="cf-field">
              <label for="cfName">Full Name *</label>
              <input type="text" id="cfName" name="name" placeholder="Juan Dela Cruz" autocomplete="name">
              <small class="cf-error"></small>
            </div>
            <div class="cf-field">
              <label for="cfEmail">Email Address *</label>
              <input type="email" id="cfEmail" name="email" placeholder="you@example.com" autocomplete="email">
              <small class="cf-error"></small>
            </div>
          </div>

          <div class="cf-row">
            <div class="cf-field">
              <label for="cfPhone">Phone Number *</label>
              <input type="tel" id="cfPhone" name="phone" placeholder="09XX XXX XXXX" autocomplete="tel">
              <small class="cf-error"></small>
            </div>
            <div class="cf-field cf-select">
              <label for="cfEvent">Event Type *</label>
              <select id="cfEvent" name="eventType">
                <option value="" selected disabled>Select an event type</option>
                <option>Wedding</option>
                <option>Birthday</option>
                <option>Debut</option>
                <option>Corporate Event</option>
                <option>Baptismal / Christening</option>
                <option>Anniversary</option>
                <option>Other</option>
              </select>
              <i class="bi bi-caret-down-fill cf-caret"></i>
              <small class="cf-error"></small>
            </div>
          </div>

          <div class="cf-field cf-message">
            <label for="cfMessage">Message *</label>
            <textarea id="cfMessage" name="message" placeholder="Tell us about your event, preferred date, number of guests, and any special requests..."></textarea>
            <small class="cf-error"></small>
          </div>

          <button type="submit" class="btn-send"><i class="fa-regular fa-paper-plane"></i> Send Message</button>
          <div class="cf-status" id="cfStatus" role="status" aria-live="polite"></div>
        </form>
      </div><!-- /RIGHT card -->

    </div><!-- /contact-grid -->

    <!-- CTA bar -->
    <div class="contact-cta-inner">
      <div class="contact-cta-icon"><i class="bi bi-star-fill"></i></div>
      <div class="contact-cta-title">Planning an event?</div>
      <span class="contact-cta-sep"></span>
      <div class="contact-cta-desc">Let us bring your vision to life with delicious food and professional service.</div>
      <button type="button" class="btn-cta-reserve reserve-now-btn"><i class="fa-regular fa-calendar-check"></i> Reserve Now</button>
    </div>

  </div><!-- /contact-panel -->
</section>



<!-- Footer -->
<?php include 'footer.html'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('js/client/gallery.js')}}"></script>
</body>
</html>