<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Menu Selection</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{asset('css/client/customize-menu.css')}}" rel="stylesheet">
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
    <a href="{{ route('gallery') }}">Gallery</a>
    <a href="{{ route('contact') }}">Contact</a>
  </nav>
  <div class="header-actions">
    <button class="btn-reserve"><i class="fa-regular fa-calendar-check"></i> Reserve Now</button>
    <div class="avatar">
      <img src="../image/701717185732655728.jpg" alt="">
    </div>
  </div>
       <!-- Shown when logged in -->
    <div class="user-area" id="userArea" hidden>
      <div class="notif-wrap">
        <button type="button" class="notif-btn" id="notifBtn" aria-label="Notifications" aria-expanded="false">
          <i class="fa-regular fa-bell"></i>
          <span class="notif-badge" id="notifBadge" hidden>0</span>
        </button>
        <div class="notif-menu" id="notifMenu" hidden>
          <div class="notif-head">Notifications</div>
          <div class="notif-empty">No new notifications.</div>
        </div>
      </div>
      <div class="profile-wrap">
        <button type="button" class="user-chip" id="profileBtn" aria-haspopup="true" aria-expanded="false" aria-label="Account menu">
          <span class="user-avatar"><i class="fa-solid fa-user"></i></span>
          <span class="user-name" id="userName"></span>
          <i class="fa-solid fa-chevron-down chip-caret" aria-hidden="true"></i>
        </button>
        <div class="profile-menu" id="profileMenu" hidden>
          <div class="pm-title"><i class="fa-solid fa-gear"></i> Settings</div>
          <a href="profile.html" class="pm-item"><i class="fa-regular fa-id-card"></i> Profile Settings</a>
          <a href="reservation.html" class="pm-item"><i class="fa-solid fa-user-gear"></i> Reservations</a>
          <a href="payment-history.html" class="pm-item"><i class="fa-solid fa-receipt"></i> Payment History</a>
          <a href="security.html" class="pm-item"><i class="fa-solid fa-shield-halved"></i> Security</a>
          <div class="pm-divider"></div>
          <button type="button" class="pm-item pm-logout" id="logoutBtn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log out</button>
        </div>
      </div>
    </div>
</header>

<!-- Hero -->
<section class="hero">
  <h1>Great Events Start with Great Planning.</h1>
  <span class="hero-highlight">Reserve Now!</span>
  <p>Check Availability and secure your date in just a few steps.</p>
</section>

<div class="container" style="max-width:1080px;">

  <!-- Step indicator -->
<div class="steps-wrap">
  <div class="step done">
    <div class="circle">1</div>
    <div class="step-label">Step 1<br>Reservation</div>
  </div>
  <div class="step done">
    <div class="circle">2</div>
    <div class="step-label">Step 2<br>Event Details</div>
  </div>
  <div class="step done">
    <div class="circle">3</div>
    <div class="step-label">Step 3<br>Menu Selection</div>
  </div>
  <div class="step">
    <div class="circle">4</div>
    <div class="step-label">Step 4<br>Confirmation</div>
  </div>
   <div class="step">
    <div class="circle">5</div>
    <div class="step-label">Step 5<br>Payment</div>
  </div>
</div>
@php $pax = (int) data_get(session('reservation'), 'step1.pax', 0); @endphp

<div class="panel tier-panel mt-3">
  <div class="panel-title"><i class="bi bi-cash-coin"></i> Choose your price per head</div>
  <div class="tier-grid" id="tierGrid">
@php
  $tierInfo = [
    400 => '6 dishes · 2 of Chicken/Beef/Pork',
    500 => '7 dishes · includes Salad',
    600 => '8 dishes · Pasta or Vegetable',
    700 => 'All 9 dishes',
  ];
@endphp
@foreach ([400, 500, 600, 700] as $t)
  <button type="button" class="tier-btn" data-price="{{ $t }}">
    <strong>₱{{ number_format($t) }}</strong><small>per head</small>
    <span class="tier-total">₱{{ number_format($t * $pax) }} for {{ $pax }} pax</span>
    <span class="tier-total">{{ $tierInfo[$t] }}</span>
  </button>
@endforeach
  </div>
</div>

<!-- MENU SETS PANEL -->
<div class="menu-sets-panel card mb-4">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="mb-1">
                    <i class="bi bi-collection"></i>
                    Menu Sets
                </h5>
                <small class="text-muted">
                    Create separate menu sets for your reservation.
                    Each set can have up to 50 guests.
                </small>
            </div>

            <div class="text-end">
                <span class="badge bg-primary" id="menuSetCount">
                    0 Sets Created
                </span>
                <div class="small text-muted mt-1" id="menuSetLimit">
                    Loading set requirements...
                </div>
            </div>
        </div>

        <!-- SET LIST -->
        <div id="menuSetsList" class="d-flex flex-wrap gap-2 mb-3">
            <div class="text-muted small">
                No menu sets created yet.
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button"
                    class="btn btn-primary"
                    id="createMenuSetBtn">
                <i class="bi bi-plus-circle"></i>
                Create Set
            </button>

            <button type="button"
                    class="btn btn-outline-primary"
                    id="duplicateMenuSetBtn"
                    disabled>
                <i class="bi bi-copy"></i>
                Duplicate Active Set
            </button>
        </div>

        <small class="text-muted d-block mt-2">
            Create Set starts with an empty menu. Duplicate Set copies
            the active set's selections, which you can customize separately.
        </small>

    </div>
</div>
<!-- END MENU SETS PANEL -->
  <!-- Main content -->
  <div class="row g-3 mt-1 mb-4">

    <!-- Menu Item Catalog: one course at a time -->
    <div class="col-lg-7">
      <div class="panel h-100">
        <div class="panel-title"><i class="bi bi-journal-text"></i> Menu Item Catalog</div>

        <!-- Course path: Pasta > Vegetables > Salad > Main Courses > Dessert > Rice > Beverages -->
        <ol class="course-steps" id="courseSteps" aria-label="Menu courses"></ol>

        <div class="course-notice" id="courseNotice" role="status" aria-live="polite" hidden></div>

        <div class="course-head">
          <div>
            <h3 class="course-title" id="courseTitle"></h3>
            <p class="course-rule" id="courseRule"></p>
          </div>
          <span class="course-count" id="courseCount"></span>
        </div>

        <!-- Beef / Chicken / Pork / Fish (only shown for Main Courses) -->
        <div class="category-tabs" id="subTabs" hidden></div>

        <div id="menuCatalogList">
          <div class="empty-note">
            <i class="bi bi-journal-x"></i>
            No menu items available yet
          </div>
        </div>

        <div class="course-nav">
          <button type="button" class="btn-course-back" id="backCourseBtn"><i class="bi bi-arrow-left"></i> Back</button>
          <button type="button" class="btn-course-next" id="nextCourseBtn">Next <i class="bi bi-arrow-right"></i></button>
        </div>
      </div>
    </div>

    <!-- Right column -->
    <div class="col-lg-5 d-flex flex-column gap-3">
      <div class="panel">
        <div class="panel-title"><i class="bi bi-flag-fill"></i> Food Restriction</div>
        <input type="text" class="restriction-input" id="restrictionInput" placeholder="Type here...">
      </div>

      <div class="panel custom-menu-panel">
        <div class="panel-title"><i class="bi bi-bookmark-fill"></i> Your Custom Menu</div>

        <div class="menu-progress">
          <div class="menu-progress-bar"><span id="progressFill"></span></div>
          <small id="progressText">0 of 0 courses complete</small>
        </div>

<div id="customMenu"></div>
<div id="customAddons"></div>
<div id="cmTotal"></div>
        <!-- Fixed, laging nasa dulo, hindi pinipili -->
<div class="custom-menu-fixed">
  <section class="cm-course">
    <div class="cm-course-head" style="cursor:default">
      <span class="cm-course-name"><i class="fa-solid fa-bowl-rice"></i> Rice</span>
      <span class="cm-course-req ok"><i class="bi bi-check-circle-fill"></i> Included</span>
    </div>
    <div class="custom-menu-item"><span class="cmi-name">Steamed Rice</span></div>
  </section>
  <section class="cm-course">
    <div class="cm-course-head" style="cursor:default">
      <span class="cm-course-name"><i class="fa-solid fa-glass-water"></i> Purified Water</span>
      <span class="cm-course-req ok"><i class="bi bi-check-circle-fill"></i> Included</span>
    </div>
    <div class="custom-menu-item"><span class="cmi-name">Purified Drinking Water</span></div>
  </section>
</div>
      </div>
    </div>
  </div>
  <div class="panel addon-panel mb-4">
  <div class="panel-title"><i class="bi bi-plus-circle-fill"></i> Add-ons (optional, per tray)</div>
  <p class="course-rule">Dagdag na ulam na per serving ang bayad. Idadagdag ito sa Grand Total.</p>
  <div class="addon-form">
    <select id="addonDish" class="restriction-input"></select>
    <select id="addonServings" class="restriction-input"></select>
    <button type="button" class="btn-course-next" id="addonAddBtn">Add</button>
  </div>
</div>
  <!-- Action bar -->
  <div class="action-bar mb-4">
    <button type="button" class="btn-cancel" id="cancelBtn">Back</button>
    <button type="button" class="btn-continue" id="continueBtn">Continue to Confirmation</button>
  </div>

</div>

<!-- Menu not complete yet -->
<div class="modal fade" id="incompleteModal" tabindex="-1" aria-labelledby="incompleteTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content mc-modal">
      <div class="mc-modal-body">
        <div class="mc-icon warning"><i class="bi bi-exclamation-triangle"></i></div>
        <h4 class="mc-title" id="incompleteTitle">Your menu isn't complete yet</h4>
        <p class="mc-text" id="incompleteText">Please finish these courses before continuing:</p>
        <ul class="mc-list" id="missingList"></ul>
        <button type="button" class="mc-btn-primary" data-bs-dismiss="modal" id="incompleteOkBtn">Continue choosing</button>
      </div>
    </div>
  </div>
</div>

<!-- Cancel reservation -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content mc-modal">
      <div class="mc-modal-body">
        <div class="mc-icon danger"><i class="bi bi-x-circle"></i></div>
        <h4 class="mc-title" id="cancelTitle">Cancel your reservation?</h4>
        <p class="mc-text">Your menu choices will be discarded.</p>
        <div class="mc-actions">
          <button type="button" class="mc-btn-secondary" data-bs-dismiss="modal">Keep Reservation</button>
          <button type="button" class="mc-btn-danger" id="confirmCancelBtn">Yes, Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>



<?php include 'footer.html'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
window.MENU_ITEMS = @json($menuItems);
window.ADDON_ITEMS = @json($addonItems);
window.PAX = {{ $pax }};
window.RESERVATION_DRAFT_ID = @json($draftId);

window.NEXT_URL = "{{ route('reservation.process4') }}";
window.CANCEL_URL = "{{ route('reservation.process3') }}";
window.SAVE_URL = "{{ route('reservation.customize-menu.store') }}";
window.CSRF = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/client/customize-menu.js') }}"></script>
</body>
</html>