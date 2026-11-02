<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Profile Settings</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Mulish:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css') }}" rel="stylesheet">

<link href="{{ asset('css/client/profile.css') }}" rel="stylesheet">
</head>
<body>

{{-- Header mo (Laravel, may auth/notifs) ang gamit, hindi yung static header ng ka-group --}}
@include('client-header')

@php
  $hasPhoto = !empty($user->profile_photo);
  $avatarUrl = $hasPhoto ? asset('storage/' . $user->profile_photo) : '';

  // Safe links: kung wala pang route, may fallback URL para hindi mag-error
  $r = function ($name, $fallback) {
      return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
  };
@endphp

<!-- ============ PAGE ============ -->
<section class="ps-banner" aria-hidden="true"></section>

<main class="ps-wrap">

  <!-- Left: account card + menu -->
  <aside class="ps-side">
    <div class="ps-side-top">
      <div class="ps-avatar-wrap">
        <div class="ps-avatar ps-avatar-sm" id="sideAvatar">
          <img alt="" id="sideAvatarImg" src="{{ $avatarUrl }}" @if(!$hasPhoto) hidden @endif>
          <i class="fa-solid fa-user ps-avatar-icon" id="sideAvatarIcon" @if($hasPhoto) hidden @endif></i>
        </div>
        <button type="button" class="ps-camera ps-camera-sm" data-photo-trigger aria-label="Change profile photo"><i class="fa-solid fa-camera"></i></button>
      </div>
      <div class="ps-email" id="sideEmail">{{ $user->email }}</div>
    </div>

    <nav class="ps-menu" aria-label="Account menu">
      <a href="{{ $r('profile.edit', '/profile') }}" class="active" aria-current="page"><i class="fa-solid fa-user"></i> Profile Settings</a>
      <a href="{{ $r('client.reservations', '/reservations') }}"><i class="fa-solid fa-calendar-days"></i> Reservation</a>
      <a href="{{ $r('client.payments', '/payment-history') }}"><i class="fa-solid fa-credit-card"></i> Payment History</a>
      <a href="{{ $r('client.security', '/security') }}"><i class="fa-solid fa-shield-halved"></i> Security</a>
    </nav>

    <div class="ps-promo">
      <i class="fa-solid fa-leaf ps-promo-leaf" aria-hidden="true"></i>
      <i class="fa-solid fa-bell-concierge ps-promo-icon" aria-hidden="true"></i>
      <div class="ps-promo-text">Delicious food,<br>memorable moments.</div>
      <i class="fa-solid fa-heart ps-promo-heart" aria-hidden="true"></i>
    </div>

    <button type="button" class="ps-logout" id="sideLogout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</button>
  </aside>

  <!-- Right: profile form -->
  <section class="ps-main">

    <div class="ps-head">
      <span class="ps-head-icon"><i class="fa-solid fa-user"></i></span>
      <div>
        <h1>Profile Settings</h1>
        <p>Update your personal information and keep your account up to date.</p>
      </div>
    </div>

    @if($errors->any())
      <div class="ps-alert" role="alert" style="display:block;">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PATCH')

      <div class="ps-body">

        <!-- Photo -->
        <div class="ps-photo-col">
          <i class="fa-solid fa-leaf ps-leaf ps-leaf-1" aria-hidden="true"></i>
          <i class="fa-solid fa-leaf ps-leaf ps-leaf-2" aria-hidden="true"></i>
          <div class="ps-avatar-wrap">
            <div class="ps-avatar ps-avatar-lg" id="formAvatar">
              <img alt="Profile photo" id="formAvatarImg" src="{{ $avatarUrl }}" @if(!$hasPhoto) hidden @endif>
              <i class="fa-solid fa-user ps-avatar-icon" id="formAvatarIcon" @if($hasPhoto) hidden @endif></i>
            </div>
            <button type="button" class="ps-camera ps-camera-lg" data-photo-trigger aria-label="Change profile photo"><i class="fa-solid fa-camera"></i></button>
          </div>
          <button type="button" class="ps-change" data-photo-trigger><i class="fa-solid fa-camera"></i> Change Photo</button>
          <div class="ps-hint">JPG, PNG, Max size: 5MB</div>
          <div class="ps-photo-error" id="photoError"></div>
          <input type="file" name="photo" id="photoInput" accept=".jpg,.jpeg,.png,image/jpeg,image/png" hidden>

          @if($hasPhoto)
            <button type="submit" form="removePhotoForm" class="ps-remove">Remove</button>
          @endif
        </div>

        <!-- Fields -->
        <div class="ps-fields">
          <div class="ps-field">
            <label for="first_name"><i class="fa-solid fa-user"></i> First Name <span class="req">*</span></label>
            <input type="text" id="first_name" name="first_name" autocomplete="given-name" value="{{ old('first_name', $user->first_name) }}" required>
          </div>

          <div class="ps-field">
            <label for="last_name"><i class="fa-solid fa-user"></i> Last Name <span class="req">*</span></label>
            <input type="text" id="last_name" name="last_name" autocomplete="family-name" value="{{ old('last_name', $user->last_name) }}" required>
          </div>

          <div class="ps-field ps-span">
            <label for="email"><i class="fa-solid fa-envelope"></i> Email Address <span class="req">*</span></label>
            <input type="email" id="email" name="email" autocomplete="email" value="{{ old('email', $user->email) }}" readonly aria-describedby="emailNote" required>
            <div class="ps-note" id="emailNote">Your email is your account login and can't be changed here.</div>
          </div>

          <div class="ps-field">
            <label for="contact_number"><i class="fa-solid fa-phone"></i> Phone Number <span class="req">*</span></label>
            <input type="tel" id="contact_number" name="contact_number" autocomplete="tel" value="{{ old('contact_number', $user->contact_number) }}" required>
          </div>

          <div class="ps-field">
            <label for="secondary_phone"><i class="fa-solid fa-phone"></i> Secondary Phone Number</label>
            <input type="tel" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone', $user->secondary_phone) }}">
          </div>

          <div class="ps-field">
            <label for="dob"><i class="fa-regular fa-calendar-days"></i> Date of Birth</label>
            <input type="date" id="dob" name="dob" autocomplete="bday" value="{{ old('dob', $user->dob ?? '') }}">
          </div>

          <div class="ps-field ps-span">
            <label for="address_line1"><i class="fa-solid fa-location-dot"></i> Address Line 1</label>
            <input type="text" id="address_line1" name="address_line1" value="{{ old('address_line1', $user->address_line1) }}">
          </div>

          <div class="ps-field ps-span">
            <label for="address_line2"><i class="fa-solid fa-location-dot"></i> Address Line 2</label>
            <input type="text" id="address_line2" name="address_line2" value="{{ old('address_line2', $user->address_line2) }}">
          </div>
        </div>
      </div>

      <!-- Bottom bar -->
      <div class="ps-bottom">
        <div class="ps-info">
          <span class="ps-info-icon"><i class="fa-solid fa-circle-info"></i></span>
          <div>
            <strong>Account Information</strong>
            <span>Your information is secure and only visible to you.</span>
          </div>
        </div>

        <div class="ps-buttons">
          <button type="button" class="ps-reset" id="resetBtn"><i class="fa-solid fa-rotate"></i> Reset Changes</button>
          <button type="submit" class="ps-save" id="saveBtn"><i class="fa-regular fa-floppy-disk"></i> Save Changes</button>
        </div>
      </div>
    </form>

    {{-- Hiwalay na form para sa Remove (bawal ang nested form) --}}
    <form id="removePhotoForm" method="POST" action="{{ route('profile.photo.remove') }}">
      @csrf
      @method('DELETE')
    </form>
  </section>
</main>

<!-- ============ MODALS ============ -->

<!-- Changes saved (lalabas kapag may session('success')) -->
<div class="ps-modal" id="savedModal" role="dialog" aria-modal="true" aria-labelledby="savedTitle" hidden>
  <div class="ps-modal-box">
    <div class="ps-modal-icon ps-modal-icon-ok"><i class="fa-solid fa-check"></i></div>
    <h2 id="savedTitle">Changes Saved!</h2>
    <p>{{ session('success') ?? 'Your profile has been updated successfully.' }}</p>
    <div class="ps-modal-actions">
      <button type="button" class="ps-modal-btn ps-modal-btn-primary" data-close data-autofocus>OK</button>
    </div>
  </div>
</div>

<!-- Log out confirmation -->
<div class="ps-modal" id="logoutModal" role="dialog" aria-modal="true" aria-labelledby="logoutTitle" hidden>
  <div class="ps-modal-box">
    <div class="ps-modal-icon ps-modal-icon-out"><i class="fa-solid fa-arrow-right-from-bracket"></i></div>
    <h2 id="logoutTitle">Log out?</h2>
    <p>Are you sure you want to log out of your account?</p>
    <div class="ps-modal-actions">
      <button type="button" class="ps-modal-btn ps-modal-btn-light" data-close data-autofocus>Cancel</button>
      <button type="submit" form="logoutForm" class="ps-modal-btn ps-modal-btn-danger">Log Out</button>
    </div>
  </div>
</div>

<form id="logoutForm" method="POST" action="{{ route('logout') }}" hidden>@csrf</form>

<!-- ===================== FOOTER ===================== -->
<footer class="site-footer">

  <svg class="footer-leaves footer-leaves-top" viewBox="0 0 200 200" aria-hidden="true" focusable="false">
    <g fill="rgba(255,255,255,.28)">
      <path d="M170 10C120 8 70 40 62 98c-3 22 6 40 22 52 30-8 62-36 74-76 6-20 8-46 12-64z"/>
      <path d="M196 70c-40-10-84 10-96 54-6 20 0 38 12 50 28-2 60-20 76-58 8-18 8-34 8-46z"/>
      <path d="M92 128c-26-14-60-6-76 22-8 14-8 28-2 40 24 6 52-2 66-24 8-12 12-26 12-38z"/>
    </g>
    <g stroke="rgba(15,90,20,.45)" stroke-width="2" fill="none" stroke-linecap="round">
      <path d="M164 16C130 50 100 90 86 144"/>
      <path d="M190 76C150 100 124 134 114 172"/>
    </g>
  </svg>
  <svg class="footer-leaves footer-leaves-bottom" viewBox="0 0 200 200" aria-hidden="true" focusable="false">
    <g fill="rgba(255,255,255,.28)">
      <path d="M30 190C-8 150-4 90 36 52c18-16 40-22 60-18 8 44-4 90-34 120-10 10-22 26-32 36z"/>
      <path d="M118 70c32-18 70-8 84 24 8 20 4 40-8 54-34 4-70-14-84-48-6-12-6-24 8-30z"/>
      <path d="M120 150c26-4 50 8 56 30 2 8 0 14-4 18-26 2-52-8-62-26-4-8-2-16 10-22z"/>
    </g>
    <g stroke="rgba(15,90,20,.45)" stroke-width="2" fill="none" stroke-linecap="round">
      <path d="M34 186C46 130 62 80 92 40"/>
      <path d="M126 76C150 96 176 120 190 146"/>
    </g>
  </svg>

  <div class="footer-inner">

    <div class="footer-col footer-brand">
      <a href="{{ route('home') }}" class="footer-logo" aria-label="Lolay's Food Station and Catering Services - Home">
        <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="">
        <span class="footer-logo-text">
          <span class="footer">
            <span class="name">Lolay's <small>FOOD STATION &amp; CATERING SERVICES</small></span>
          </span>
        </span>
      </a>
      <p class="footer-about">
        Delicious food, complete setup, and professional service for all kinds of occasions.
        Let us make your special moments more memorable.
      </p>
      <div class="footer-social">
        <a href="https://facebook.com/lolayscatering" target="_blank" rel="noopener" aria-label="Lolay's on Facebook" title="Facebook">
          <i class="fa-brands fa-facebook"></i>
        </a>
        <a href="mailto:lolaycatering@gmail.com" aria-label="Email Lolay's" title="Email us">
          <svg viewBox="0 0 48 48" aria-hidden="true" focusable="false">
            <path fill="#4caf50" d="M45 16.2l-5 2.75-5 4.75L35 40h7c1.657 0 3-1.343 3-3V16.2z"/>
            <path fill="#1e88e5" d="M3 16.2l3.614 1.71L13 23.7V40H6c-1.657 0-3-1.343-3-3V16.2z"/>
            <polygon fill="#e53935" points="35,11.2 24,19.45 13,11.2 12,17 13,23.7 24,31.95 35,23.7 36,17"/>
            <path fill="#c62828" d="M3 12.298V16.2l10 7.5V11.2L9.876 8.859A4.6 4.6 0 007.298 8C4.924 8 3 9.924 3 12.298z"/>
            <path fill="#fbc02d" d="M45 12.298V16.2l-10 7.5V11.2l3.124-2.341A4.6 4.6 0 0140.702 8C43.076 8 45 9.924 45 12.298z"/>
          </svg>
        </a>
      </div>
    </div>

    <nav class="footer-col" aria-label="Quick links">
      <h3 class="footer-heading">Quick Links</h3>
      <ul class="footer-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ url('/packages') }}">Packages</a></li>
        <li><a href="{{ url('/about-us') }}">About Us</a></li>
        <li><a href="{{ url('/gallery') }}">Gallery</a></li>
        <li><a href="{{ url('/contact') }}">Contact</a></li>
      </ul>
    </nav>

    <nav class="footer-col" aria-label="Our services">
      <h3 class="footer-heading">Our Services</h3>
      <ul class="footer-links">
        <li><a href="{{ url('/packages#wedding') }}">Wedding Catering</a></li>
        <li><a href="{{ url('/packages#birthday') }}">Birthday Catering</a></li>
        <li><a href="{{ url('/packages#corporate') }}">Corporate Buffet</a></li>
        <li><a href="{{ url('/packages#baptismal') }}">Baptismal Catering</a></li>
        <li><a href="{{ url('/packages#anniversary') }}">Anniversary Catering</a></li>
      </ul>
    </nav>

    <div class="footer-col">
      <h3 class="footer-heading">Contact Us</h3>
      <ul class="footer-contact">
        <li>
          <span class="footer-icon"><i class="bi bi-geo-alt-fill"></i></span>
          <a href="https://www.google.com/maps/search/?api=1&amp;query=123+Barriada+St.+Gogon+Legazpi+City" target="_blank" rel="noopener">123 Barriada St.<br>Gogon Legazpi City</a>
        </li>
        <li>
          <span class="footer-icon"><i class="bi bi-telephone-fill"></i></span>
          <a href="tel:09876543234">09876543234</a>
        </li>
        <li>
          <span class="footer-icon"><i class="bi bi-envelope-fill"></i></span>
          <a href="mailto:lolaycatering@gmail.com">lolaycatering@gmail.com</a>
        </li>
      </ul>
    </div>

    <div class="footer-col">
      <h3 class="footer-heading">Business Hours</h3>
      <ul class="footer-contact">
        <li>
          <span class="footer-icon"><i class="bi bi-clock-fill"></i></span>
          <span>Monday - Sunday<br>8:00 AM - 7:00 PM</span>
        </li>
      </ul>
    </div>

  </div>

  <div class="footer-bottom">
    <p>&copy; {{ date('Y') }} Lolay's Food Station &amp; Catering Services. All rights reserved.</p>
  </div>

</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
  var MAX_BYTES = 5 * 1024 * 1024;
  var photoInput = document.getElementById('photoInput');
  var photoError = document.getElementById('photoError');
  var form = document.getElementById('profileForm');

  var avatars = [
    { img: document.getElementById('formAvatarImg'), icon: document.getElementById('formAvatarIcon') },
    { img: document.getElementById('sideAvatarImg'), icon: document.getElementById('sideAvatarIcon') }
  ];
  var original = avatars.map(function (a) {
    return { src: a.img.getAttribute('src') || '', shown: !a.img.hidden };
  });

  function showPhoto(src) {
    avatars.forEach(function (a) {
      a.img.src = src;
      a.img.hidden = false;
      a.icon.hidden = true;
    });
  }

  // Camera / Change Photo buttons
  document.querySelectorAll('[data-photo-trigger]').forEach(function (btn) {
    btn.addEventListener('click', function () { photoInput.click(); });
  });

  // Preview + size check
  photoInput.addEventListener('change', function (e) {
    var file = e.target.files[0];
    photoError.textContent = '';
    if (!file) return;
    if (!/^image\/(jpeg|png)$/.test(file.type)) {
      photoError.textContent = 'JPG or PNG lang ang pwede.';
      e.target.value = '';
      return;
    }
    if (file.size > MAX_BYTES) {
      photoError.textContent = 'Max 5 MB lang ang photo.';
      e.target.value = '';
      return;
    }
    showPhoto(URL.createObjectURL(file));
  });

  // Reset Changes
  document.getElementById('resetBtn').addEventListener('click', function () {
    form.reset();
    photoInput.value = '';
    photoError.textContent = '';
    avatars.forEach(function (a, i) {
      if (original[i].shown) { a.img.src = original[i].src; a.img.hidden = false; a.icon.hidden = true; }
      else { a.img.removeAttribute('src'); a.img.hidden = true; a.icon.hidden = false; }
    });
  });

  // Modals
  function openModal(m) {
    m.hidden = false;
    var f = m.querySelector('[data-autofocus]');
    if (f) f.focus();
  }
  function closeModal(m) { m.hidden = true; }

  document.querySelectorAll('.ps-modal').forEach(function (m) {
    m.querySelectorAll('[data-close]').forEach(function (b) {
      b.addEventListener('click', function () { closeModal(m); });
    });
    m.addEventListener('click', function (e) { if (e.target === m) closeModal(m); });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') document.querySelectorAll('.ps-modal').forEach(closeModal);
  });

  document.getElementById('sideLogout').addEventListener('click', function () {
    openModal(document.getElementById('logoutModal'));
  });

  @if(session('success'))
    openModal(document.getElementById('savedModal'));
  @endif
})();
</script>
<script src="{{ asset('js/client/homepage.js') }}"></script>
</body>
</html>