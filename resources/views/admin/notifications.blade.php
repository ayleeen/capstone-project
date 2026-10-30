<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Notifications</title>
<link href="{{ asset('css/nav.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">

    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
</head>
<body>
<div class="container-fluid p-3">
  <div class="row g-3">

    <!-- SIDEBAR -->
    <div class="col-lg-2">
      <div class="sidebar d-flex flex-column">
        <div class="brand-box">
          <div class="brand-logo">
                  <a href="{{ route('admin.logo') }}">
    <img src="{{ $businessProfile->logo_url }}" alt="Business Logo">
</a>
                  
          </div>
            @php
    $businessName = trim(
        $businessProfile->business_name ?? "Lolay's Catering Services"
    );

    $words = preg_split('/\s+/', $businessName);
    $firstLine = implode(' ', array_slice($words, 0, 2));
    $secondLine = implode(' ', array_slice($words, 2));
@endphp

<div class="brand-name">
    {{ $firstLine }}@if($secondLine !== '')<br>{{ $secondLine }}@endif
</div>
        </div>
       <nav class="flex-grow-1">
          <a href="dashboard.html" class="nav-link-custom"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
          <a href="reservation.html" class="nav-link-custom"><i class="bi bi-journal-text"></i> Reservation</a>
          <a href="calendar.html" class="nav-link-custom "><i class="bi bi-calendar3"></i> Calendar/ Schedule</a>
          <a href="packages.html" class="nav-link-custom "><i class="bi bi-box-seam"></i> Packages</a>
          <a href="menu-management.html" class="nav-link-custom "><i class="bi bi-cup-hot"></i> Menu</a>
          <a href="customer.html" class="nav-link-custom"><i class="bi bi-person"></i> Customer</a>
           <a href="reports.html" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>
          <a href="settings.html" class="nav-link-custom active" ><i class="bi bi-gear"></i> Settings</a>
          <a href="archive.html" class="nav-link-custom "><i class="bi bi-archive"></i> Archive</a>
           <a href="audit.html" class="nav-link-custom "><i class="bi bi-clipboard-check"></i> Audit</a>
        </nav>
        <div class="promo-card p-3 text-center mt-auto">
          <div class="promo-ico rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
            <img src="../image/download.png" alt="">
          </div>
          <h4 class="mb-1">Delicious Events<br>Memorable Moments</h4>
          <p class="mb-0">We make every occasion extra special.</p>
        </div>

        <a href="#" class="logout-link d-flex align-items-center justify-content-center gap-2 mt-3 py-2">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="col-lg-10">

      <!-- Topbar -->
      <div class="topbar">
        <div>
          <a href="settings.html" class="back-link"><i class="bi bi-arrow-left"></i> Back</a>
          <h1 class="page-title">Notifications</h1>
          <p class="page-subtitle">Stay updated on reservations, payments, and system activity.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
@auth
  @php
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();
  @endphp

  <div class="dropdown">
    <button class="icon-btn position-relative border-0 bg-transparent"
            type="button"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false"
            aria-label="Notifications">
      <i class="bi bi-bell"></i>
      @if($unreadCount > 0)
        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
          {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
      @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-0 shadow" style="width: 320px;">
      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
        <strong>Notifications</strong>
        @if($unreadCount > 0)
          <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button class="btn btn-link btn-sm p-0 text-decoration-none">Mark all as read</button>
          </form>
        @endif
      </div>

      <div style="max-height: 320px; overflow-y: auto;">
        @forelse($recentNotifications as $notif)
          <div class="px-3 py-2 border-bottom {{ $notif->read_at ? '' : 'bg-light' }}">
            <div class="fw-semibold small">{{ $notif->data['title'] ?? 'Notification' }}</div>
            <div class="small text-muted">{{ $notif->data['message'] ?? '' }}</div>
            <div class="text-secondary" style="font-size: 12px;">{{ $notif->created_at->diffForHumans() }}</div>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0 small">Wala pang notifications.</p>
        @endforelse
      </div>
    </div>
  </div>
@endauth
          <div class="dropdown">
            <button class="admin-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="https://i.pravatar.cc/100?img=12" class="admin-avatar" alt="">
              <span class="text-start">
                <span class="d-block" style="font-weight:600;font-size:13px;">Admin User</span>
                <span class="d-block" style="font-size:10.5px;color:var(--muted);">Administrator</span>
              </span>
              <i class="bi bi-chevron-down admin-caret"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end profile-menu">
              <li><a class="dropdown-item" href="profile.html"><i class="bi bi-person"></i> My Profile</a></li>
              <li><a class="dropdown-item" href="settings.html"><i class="bi bi-gear"></i> Settings</a></li>
              <li><hr class="dropdown-divider"></li>
                            <li>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="dropdown-item text-danger">
      <i class="bi bi-box-arrow-right"></i> Logout
    </button>
  </form>
</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Tabs + Mark all as read -->
      <div class="notif-toolbar">
        <div class="notif-tabs" role="tablist" id="notifTabs">
          <button type="button" class="notif-tab active" data-filter="all" role="tab" aria-selected="true">All</button>
          <button type="button" class="notif-tab" data-filter="reservation" role="tab" aria-selected="false">Reservations</button>
          <button type="button" class="notif-tab" data-filter="payment" role="tab" aria-selected="false">Payments</button>
          <button type="button" class="notif-tab" data-filter="system" role="tab" aria-selected="false">System</button>
        </div>
        <button type="button" class="btn-mark-read" id="markAllReadBtn">Mark all as read</button>
      </div>

      <!-- Notification list -->
      <div class="notif-list" id="notifList">
        <!-- rendered by notifications.js -->
      </div>

      <div class="notif-empty d-none" id="notifEmpty">
        <i class="bi bi-bell-slash"></i>
        <p id="notifEmptyText">No notifications yet.</p>
      </div>

      <!-- Notification Preferences -->
      <button type="button" class="notif-item notif-pref" id="prefsBtn">
        <span class="notif-icon"><i class="bi bi-bell"></i></span>
        <span class="notif-text">
          <span class="notif-title">Notification Preferences</span>
          <span class="notif-desc">Choose which kinds of notifications you want to receive.</span>
        </span>
        <i class="bi bi-arrow-right notif-arrow"></i>
      </button>

    </div>
  </div>
</div>

<!-- Notification Details Modal (pop-up) -->
<div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content notif-modal">
      <div class="modal-header">
        <div class="d-flex align-items-center gap-3">
          <span class="notif-icon notif-icon-sm" id="notifModalIcon"><i class="bi bi-bell"></i></span>
          <div>
            <h5 class="modal-title" id="notifModalTitle">Notification</h5>
            <span class="notif-type-badge" id="notifModalType"></span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="notif-modal-label">Message</div>
        <p class="notif-modal-message" id="notifModalMessage"></p>
        <div class="notif-modal-label">Received</div>
        <p class="notif-modal-time" id="notifModalTime"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Close</button>
        <a href="#" class="btn btn-new-reservation d-none" id="notifModalLink">View</a>
      </div>
    </div>
  </div>
</div>

<!-- Notification Preferences Modal -->
<div class="modal fade" id="prefsModal" tabindex="-1" aria-labelledby="prefsModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content notif-modal">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="prefsModalTitle">Notification Preferences</h5>
          <p class="modal-subtitle">Choose which kinds of notifications you want to receive.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="prefsForm">
        <div class="modal-body">
          <div class="pref-row">
            <div>
              <div class="pref-title">Reservations</div>
              <div class="pref-desc">New requests, confirmations and event reminders.</div>
            </div>
            <div class="form-check form-switch m-0">
              <input class="form-check-input" type="checkbox" role="switch" id="prefReservation" aria-label="Reservation notifications">
            </div>
          </div>
          <div class="pref-row">
            <div>
              <div class="pref-title">Payments</div>
              <div class="pref-desc">Payments submitted or waiting for verification.</div>
            </div>
            <div class="form-check form-switch m-0">
              <input class="form-check-input" type="checkbox" role="switch" id="prefPayment" aria-label="Payment notifications">
            </div>
          </div>
          <div class="pref-row">
            <div>
              <div class="pref-title">System</div>
              <div class="pref-desc">Updates to your account and business information.</div>
            </div>
            <div class="form-check form-switch m-0">
              <input class="form-check-input" type="checkbox" role="switch" id="prefSystem" aria-label="System notifications">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-new-reservation"><i class="fa-regular fa-floppy-disk"></i> Save Preferences</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Alert Modal (used instead of browser alert()) -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content confirm-modal">
      <div class="confirm-modal-body">
        <div class="confirm-icon-circle info" id="alertIcon">
          <i class="fa-solid fa-circle-info" id="alertIconGlyph"></i>
        </div>
        <h4 class="confirm-title" id="alertTitle">Notice</h4>
        <p class="confirm-subtitle" id="alertMessage"></p>
        <div class="confirm-actions">
          <button type="button" class="btn-confirm-ok" data-bs-dismiss="modal" id="alertOkBtn">OK</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Reservation JavaScript -->
<script src="{{ asset('js/notifications.js') }}"></script>
</body>
</html>