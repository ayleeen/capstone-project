{{-- resources/views/client-header.blade.php
     Header ng lahat ng client pages (design ng ka-group): may bell at Settings dropdown.
     Auth-aware, may totoong pangalan/photo at links. May sariling CSS (client-header.css) at JS. --}}
@php
  $hdrUser   = auth()->user();
  $hdrName   = $hdrUser ? trim(($hdrUser->first_name ?? '') . ' ' . ($hdrUser->last_name ?? '')) : '';
  if ($hdrUser && $hdrName === '') { $hdrName = $hdrUser->name ?? 'My Account'; }
  $hdrPhoto  = $hdrUser && !empty($hdrUser->profile_photo) ? asset('storage/' . $hdrUser->profile_photo) : null;

  // Kung wala pa ang route name, may fallback URL para hindi mag-error
  $hdrRoute = function ($name, $fallback) {
      return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url($fallback);
  };

  // Optional: ipasa mula sa controller/view composer bilang $clientNotifications
  // Format: [['text' => '...', 'time' => '...', 'url' => '...'], ...]
  $hdrNotifs = collect($clientNotifications ?? []);
@endphp

<link href="{{ asset('css/client/client-header.css') }}" rel="stylesheet">

<header class="site">
  <a href="{{ $hdrRoute('home', '/') }}" class="brand">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="Lolay's logo" class="logo-img">
    <div class="name">Lolay's<small>FOOD STATION &amp;<br>CATERING SERVICES</small></div>
  </a>

  <nav class="main-nav" aria-label="Main">
    <a href="{{ $hdrRoute('home', '/') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
    <a href="{{ $hdrRoute('about-us', '/about-us') }}" class="{{ request()->routeIs('about-us') ? 'active' : '' }}">About Us</a>
    <a href="{{ $hdrRoute('package', '/packages') }}" class="{{ request()->routeIs('package') ? 'active' : '' }}">Packages</a>
    <a href="{{ $hdrRoute('menu', '/menu') }}" class="{{ request()->routeIs('menu') ? 'active' : '' }}">Menu</a>
    <a href="{{ $hdrRoute('gallery', '/gallery') }}" class="{{ request()->routeIs('gallery') ? 'active' : '' }}">Gallery</a>
    <a href="{{ $hdrRoute('contact', '/contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
  </nav>

  <div class="header-actions">
    <a href="{{ $hdrRoute('reservation.process1', '/reservation-process-1') }}" class="btn-reserve"><i class="fa-regular fa-calendar-check"></i> Reserve Now</a>

    @guest
      <a href="{{ $hdrRoute('login', '/login') }}" class="btn-login" id="loginBtn"><i class="fa-regular fa-user"></i> Login/Register</a>
    @endguest

    @auth
      <div class="user-area" id="userArea">
        <div class="notif-wrap">
          <button type="button" class="notif-btn" id="notifBtn" aria-label="Notifications" aria-expanded="false">
            <i class="fa-regular fa-bell"></i>
            @if($hdrNotifs->count())
              <span class="notif-badge" id="notifBadge">{{ $hdrNotifs->count() > 99 ? '99+' : $hdrNotifs->count() }}</span>
            @endif
          </button>
          <div class="notif-menu" id="notifMenu" hidden>
            <div class="notif-head">Notifications</div>
            @forelse($hdrNotifs as $n)
              <a class="notif-item" href="{{ is_array($n) ? ($n['url'] ?? '#') : '#' }}">
                {{ is_array($n) ? ($n['text'] ?? '') : $n }}
                @if(is_array($n) && !empty($n['time']))<small>{{ $n['time'] }}</small>@endif
              </a>
            @empty
              <div class="notif-empty">No new notifications.</div>
            @endforelse
          </div>
        </div>

        <div class="profile-wrap">
          <button type="button" class="user-chip" id="profileBtn" aria-haspopup="true" aria-expanded="false" aria-label="Account menu">
            <span class="user-avatar">
              @if($hdrPhoto)
                <img src="{{ $hdrPhoto }}" alt="">
              @else
                <i class="fa-solid fa-user"></i>
              @endif
            </span>
            <span class="user-name">{{ $hdrName }}</span>
            <i class="fa-solid fa-chevron-down chip-caret" aria-hidden="true"></i>
          </button>
          <div class="profile-menu" id="profileMenu" hidden>
            <div class="pm-title"><i class="fa-solid fa-gear"></i> Settings</div>
            <a href="{{ $hdrRoute('profile.edit', '/profile') }}" class="pm-item"><i class="fa-regular fa-id-card"></i> Profile Settings</a>
            <a href="{{ $hdrRoute('client.reservations', '/reservations') }}" class="pm-item"><i class="fa-solid fa-user-gear"></i> Reservations</a>
            <a href="{{ $hdrRoute('client.payments', '/payment-history') }}" class="pm-item"><i class="fa-solid fa-receipt"></i> Payment History</a>
            <a href="{{ $hdrRoute('client.security', '/security') }}" class="pm-item"><i class="fa-solid fa-shield-halved"></i> Security</a>
            <div class="pm-divider"></div>
            <button type="button" class="pm-item pm-logout" id="hdrLogoutBtn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log out</button>
          </div>
        </div>
      </div>

      <form id="hdrLogoutForm" method="POST" action="{{ route('logout') }}" hidden>@csrf</form>
    @endauth
  </div>
</header>

@auth
<script>
(function () {
  var nBtn = document.getElementById('notifBtn');
  var nMenu = document.getElementById('notifMenu');
  var pBtn = document.getElementById('profileBtn');
  var pMenu = document.getElementById('profileMenu');
  if (!nBtn || !pBtn) return;

  function setNotif(open) {
    nMenu.hidden = !open;
    nBtn.setAttribute('aria-expanded', String(open));
    if (open) setProfile(false);
  }
  function setProfile(open) {
    pMenu.hidden = !open;
    pBtn.setAttribute('aria-expanded', String(open));
    if (open) { nMenu.hidden = true; nBtn.setAttribute('aria-expanded', 'false'); }
  }

  nBtn.addEventListener('click', function (e) { e.stopPropagation(); setNotif(nMenu.hidden); });
  pBtn.addEventListener('click', function (e) { e.stopPropagation(); setProfile(pMenu.hidden); });
  document.addEventListener('click', function (e) {
    if (!nMenu.contains(e.target)) setNotif(false);
    if (!pMenu.contains(e.target)) setProfile(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { setNotif(false); setProfile(false); }
  });

  // Log out: kung may confirm modal ang page (Profile / Reservations), doon dadaan; kung wala, diretso na
  document.getElementById('hdrLogoutBtn').addEventListener('click', function () {
    setProfile(false);
    var side = document.getElementById('sideLogout');
    if (side) side.click();
    else document.getElementById('hdrLogoutForm').submit();
  });
})();
</script>
@endauth