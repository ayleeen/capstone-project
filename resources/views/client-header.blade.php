<header class="site">
  <div class="brand">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="Lolay's logo" class="logo-img">
    <div class="name">Lolay's<small>FOOD STATION &amp;<br>CATERING SERVICES</small></div>
  </div>



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
            : 'https://ui-avatars.com/api/?background=1f6b34&color=fff&name=' . urlencode($user->name);
      @endphp

      <div class="dropdown profile-dropdown">
        <button class="profile-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="{{ $avatar }}" class="profile-avatar" alt="">
          <span class="profile-text">
            <span class="profile-name">{{ $user->name }}</span>
            <span class="profile-role">{{ $user->is_admin ? 'Administrator' : 'Customer' }}</span>
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