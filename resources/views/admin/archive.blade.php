<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Archive</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">
    <link href="{{ asset('css/nav.css') }}" rel="stylesheet">
    <link href="{{ asset('css/archive.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                              <a href="{{ route('admin.dashboard') }}" class="nav-link-custom">
                        <i class="bi bi-grid-1x2-fill"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.reservation') }}" class="nav-link-custom ">
                        <i class="bi bi-journal-text"></i>
                        Reservation
                    </a>

                    <a href="{{ route('admin.calendar') }}" class="nav-link-custom">
                        <i class="bi bi-calendar3"></i>
                        Calendar/ Schedule
                    </a>

                    <a href="{{ route('admin.packages') }}" class="nav-link-custom">
                        <i class="bi bi-box-seam"></i>
                        Packages
                    </a>

                    <a href="{{ route('admin.menu') }}" class="nav-link-custom">
                        <i class="bi bi-egg-fried"></i>
                        Menu
                    </a>

                    <a href="{{ route('admin.customer') }}" class="nav-link-custom">
                        <i class="bi bi-person"></i>
                        Customer
                    </a>
          <a href="{{ route('admin.archive') }}" class="nav-link-custom active"><i class="bi bi-archive"></i> Archive</a>
          <a href="{{ route('admin.audit') }}" class="nav-link-custom "><i class="bi bi-clipboard-check"></i> Audit Logs</a>
          <a href="{{ route('admin.reports') }}" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>
              <a href="{{ route('admin.settings') }}" class="nav-link-custom">
        <i class="bi bi-gear"></i> Settings
    </a>
        </nav>
        <div class="promo-card p-3 text-center mt-auto">
          <div class="promo-ico mx-auto mb-2"><img src="../image/download.png" alt=""></div>
          <h4 class="mb-1">Delicious Events<br>Memorable Moments</h4>
          <p class="mb-0">We make every occasion extra special.</p>
        </div>
        <a href="#" class="logout-link d-flex align-items-center justify-content-center gap-2 mt-3 py-2">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </div>
    </div>

    <!-- MAIN -->
    <div class="col-lg-10">
      <div class="topbar">
        <div>
          <h1 class="page-title">Archive</h1>
          <p class="page-sub">View and manage archive records. You can restore or permanently delete them anytime.</p>
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
              <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i> Settings</a></li>
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

<!-- Tab cards -->
<div class="row g-3 mb-3">

        <div class="col-md-3">
        <button class="archive-card active" data-tab="reservations">
            <span class="ac-icon">
                <i class="bi bi-journal-text"></i>
            </span>
            <span>
                <span class="ac-label">Archive Reservations</span>
                <span class="ac-count" id="count-reservations">0</span>
                <span class="ac-note">Records you've archived</span>
            </span>
        </button>
    </div>

    <div class="col-md-3">
        <button class="archive-card" data-tab="packages">
            <span class="ac-icon">
                <i class="bi bi-box-seam"></i>
            </span>
            <span>
                <span class="ac-label">Archive Packages</span>
                <span class="ac-count" id="count-packages">0</span>
                <span class="ac-note">Records you've archived</span>
            </span>
        </button>
    </div>

    <div class="col-md-3">
        <button class="archive-card" data-tab="menu">
            <span class="ac-icon">
                <i class="bi bi-egg-fried"></i>
            </span>
            <span>
                <span class="ac-label">Archive Menu</span>
                <span class="ac-count" id="count-menu">0</span>
                <span class="ac-note">Records you've archived</span>
            </span>
        </button>
    </div>

    <div class="col-md-3">
        <button class="archive-card" data-tab="customers">
            <span class="ac-icon">
                <i class="bi bi-people"></i>
            </span>
            <span>
                <span class="ac-label">Archive Customers</span>
                <span class="ac-count" id="count-customers">0</span>
                <span class="ac-note">Records you've archived</span>
            </span>
        </button>
    </div>

</div>

      <!-- Filters -->
      <div class="panel filter-bar mb-3">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" id="searchInput" placeholder="Search archive...">
        </div>
        <div class="filter-group"><label for="categoryFilter">Category</label><select id="categoryFilter" class="form-select form-select-sm"></select></div>
        <div class="filter-group"><label for="statusFilter">Status</label><select id="statusFilter" class="form-select form-select-sm"></select></div>
        <div class="filter-group"><label for="sortFilter">Sort by</label>
          <select id="sortFilter" class="form-select form-select-sm">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="name">Name A–Z</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="table-title" id="tableTitle">Archive Reservation</h2>
          <div class="bulk-bar" id="bulkBar" hidden>
            <span id="bulkCount">0 selected</span>
            <button class="btn-restore" id="bulkRestore">Restore</button>
            <button class="btn-delete" id="bulkDelete">Delete</button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="archive-table">
            <thead id="tableHead"></thead>
            <tbody id="tableBody"></tbody>
          </table>
        </div>
        <div class="empty-note d-none" id="emptyMsg">Nothing archived here. Records you archive will show up in this list.</div>
        <div class="table-footer" id="tableFooter"></div>
      </div>
    </div>
  </div>
</div>

<!-- Confirm modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content confirm-modal">
      <div class="p-4 text-center">
        <div class="confirm-icon" id="confirmIcon"></div>
        <h5 id="confirmTitle" class="mb-2"></h5>
        <p id="confirmText" class="text-muted small mb-4"></p>
        <div class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmBtn"></button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast-msg" id="toastMsg" role="status"></div>
<script>
  window.ARCHIVE_DATA = @json($archive);
  window.ARCHIVE_URL  = "{{ route('admin.archive') }}";
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Reservation JavaScript -->
<script src="{{ asset('js/archive.js') }}"></script>
</body>
</html>