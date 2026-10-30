<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Lolay's Catering Services - Packages</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/packages.css') }}" rel="stylesheet">
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
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
          </a>
          <a href="{{ route('admin.reservation') }}" class="nav-link-custom">
            <i class="bi bi-journal-text"></i> Reservation
          </a>
          <a href="{{ route('admin.calendar') }}" class="nav-link-custom">
            <i class="bi bi-calendar3"></i> Calendar/ Schedule
          </a>
          <a href="{{ route('admin.packages') }}" class="nav-link-custom active">
            <i class="bi bi-box-seam"></i> Packages
          </a>
          <a href="{{ route('admin.menu') }}" class="nav-link-custom">
            <i class="bi bi-egg-fried"></i> Menu
          </a>
          <a href="{{ route('admin.customer') }}" class="nav-link-custom">
            <i class="bi bi-person"></i> Customer
            <a href="{{ route('admin.archive') }}" class="nav-link-custom "><i class="bi bi-archive"></i> Archive</a>
            <a href="{{ route('admin.audit') }}" class="nav-link-custom "><i class="bi bi-clipboard-check"></i> Audit Logs</a>
          </a>
    <a href="{{ route('admin.reports') }}" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>

          <a href="{{ route('admin.settings') }}" class="nav-link-custom">
            <i class="bi bi-gear"></i> Settings
          </a>
        </nav>

        <div class="promo-card p-3 text-center mt-auto">
          <div class="promo-ico rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
            <img src="{{ asset('image/download.png') }}" alt="">
          </div>
          <h4 class="mb-1">Delicious Events<br>Memorable Moments</h4>
          <p class="mb-0">We make every occasion extra special.</p>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
          @csrf
          <button type="submit" class="logout-link d-flex align-items-center justify-content-center gap-2 py-2 w-100 border-0 bg-transparent">
            <i class="bi bi-box-arrow-right"></i> Logout
          </button>
        </form>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="col-lg-10">

      <!-- Topbar -->
      <div class="topbar">
        <h1 class="page-title">Packages</h1>
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

<!-- Stat cards -->
<div class="stat-cards pkg-stat-cards">

  <div class="stat-card">
    <div class="stat-icon green"><i class="fa-solid fa-calendar-check"></i></div>
    <div class="stat-body">
      <div class="stat-label">Total Packages</div>
      <div class="stat-value" id="statTotal">{{ $packages->count() }}</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon gold"><i class="fa-regular fa-clock"></i></div>
    <div class="stat-body">
      <div class="stat-label">Best Selling</div>
      <div class="stat-value stat-value-text">—</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
    <div class="stat-body">
      <div class="stat-label">Average Price</div>
      <div class="stat-value">₱{{ number_format($packages->avg('price') ?? 0, 2) }}</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon green"><i class="fa-solid fa-sack-dollar"></i></div>
    <div class="stat-body">
      <div class="stat-label">Total Sales</div>
      <div class="stat-value">₱{{ number_format($packages->sum('price'), 2) }}</div>
    </div>
  </div>

  <a href="{{ route('admin.packages.create') }}" class="btn btn-new-reservation pkg-add-btn" id="addPackageBtn">
    <i class="fa-solid fa-plus"></i> Add New Package
  </a>

</div>

      <!-- TABLE + PACKAGE DETAILS PANEL -->
      <div class="content-row">

        <div class="card table-card" id="tableCardWrap">

          <div class="table-filters">
            <div class="search-box">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="searchInput" placeholder="Search package...">
            </div>

            <div class="filter-group">
              <label>Category</label>
              <select id="categoryFilter" class="form-select">
                <option value="">All Categories</option>
                <option value="wedding">Wedding</option>
                <option value="birthday">Birthday</option>
                <option value="corporate">Corporate</option>
                <option value="debut">Debut</option>
              </select>
            </div>

            <div class="filter-group">
              <label>Status</label>
              <select id="statusFilter" class="form-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>

            <div class="filter-group">
              <label>Sort by</label>
              <select id="sortFilter" class="form-select">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="price-high">Price: High to Low</option>
                <option value="price-low">Price: Low to High</option>
              </select>
            </div>
          </div>

          <div class="table-responsive">
            <table class="reservation-table">
              <thead>
                <tr>
                  <th>Package</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th>Guest</th>
                  <th>Status</th>
                  <th>Bookings</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="packageTableBody">
                <!-- rows rendered by packages.js -->
              </tbody>
            </table>
          </div>

          <div id="noResultsMsg" class="no-results d-none">No packages found.</div>
          <div class="table-footer">
            <span id="tableFooter">Showing 0 of 0 items</span>
          </div>
        </div>

        <!-- Package Details slide-out panel -->
<!-- VIEW PACKAGE MODAL -->
<div class="modal fade" id="viewPackageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content pkg-view-modal">
      <div class="modal-header">
        <h5 class="modal-title" id="viewPkgTitle">Package Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewPkgBody"></div>
    </div>
  </div>
</div>

<!-- Delete Package Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content confirm-modal">
      <div class="confirm-modal-body">
        <div class="confirm-icon-circle danger">
          <i class="fa-solid fa-trash"></i>
        </div>
        <h4 class="confirm-title">Delete Package</h4>
        <p class="confirm-subtitle">Are you sure you want to delete this package? This action cannot be undone.</p>

        <div class="confirm-client-card">
          <img src="" alt="" class="confirm-client-avatar" id="deletePackageImg">
          <div class="confirm-client-info">
            <div class="confirm-client-name" id="deletePackageName"></div>
            <div class="confirm-client-meta">
              <span id="deletePackageCategory"></span>
            </div>
          </div>
        </div>

        <form id="deleteForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="confirm-actions">
            <button type="button" class="btn-confirm-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-confirm-danger" id="confirmDeleteBtn">
              <i class="fa-solid fa-trash"></i> Delete Package
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@php
$packagesJs = $packages->map(function ($p) {
    $photo = $p->photo ? asset('storage/' . $p->photo) : '';
    return [
        'id'          => $p->id,
        'name'        => $p->package_name,
        'description' => $p->description,
        'category'    => strtolower($p->category),
        'price'       => (float) $p->price,
        'guestMin'    => $p->guest_min,
        'guestMax'    => $p->guest_max,
        'status'      => $p->status ? 'active' : 'inactive',
        'bookings'    => 0,
        'image'       => $photo,
        'inclusions'  => is_array($p->inclusions) ? array_values(array_filter($p->inclusions)) : [],
        'editUrl'     => route('admin.packages.edit', $p),
        'deleteUrl'   => route('admin.packages.destroy', $p),
    ];
})->values();
@endphp

<script>
    const packages = @json($packagesJs);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/packages.js') }}"></script>
</body>
</html>