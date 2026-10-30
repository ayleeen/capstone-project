<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Customer</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">

    <link href="{{ asset('css/customer.css') }}" rel="stylesheet">
<link href="{{ asset ('css/nav.css') }}" rel="stylesheet">
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

                    <a href="{{ route('admin.customer') }}" class="nav-link-custom active">
                        <i class="bi bi-person"></i>
                        Customer
                    </a>
          <a href="{{ route('admin.archive') }}" class="nav-link-custom "><i class="bi bi-archive"></i> Archive</a>
          <a href="{{ route('admin.audit') }}" class="nav-link-custom "><i class="bi bi-clipboard-check"></i> Audit Logs</a>
          <a href="{{ route('admin.reports') }}" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>
              <a href="{{ route('admin.settings') }}" class="nav-link-custom">
        <i class="bi bi-gear"></i> Settings
    </a>
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
          <h1 class="page-title mb-1">Customer</h1>
          <p class="page-subtitle">View and manage your customer records.</p>
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

      <div class="stat-cards cust-stat-cards">
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa-solid fa-users"></i></div>
          <div class="stat-body">
            <div class="stat-label">Total Customers</div>
            <div class="stat-value" id="statTotal">0</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon pink"><i class="fa-solid fa-user-plus"></i></div>
          <div class="stat-body">
            <div class="stat-label">New Customers</div>
            <div class="stat-value" id="statNew">0</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa-solid fa-star"></i></div>
          <div class="stat-body">
            <div class="stat-label">Repeat Customers</div>
            <div class="stat-value" id="statRepeat">0</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon gold"><i class="fa-solid fa-envelope"></i></div>
          <div class="stat-body">
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value" id="statBookings">0</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green"><i class="fa-solid fa-sack-dollar"></i></div>
          <div class="stat-body">
            <div class="stat-label">Total Sales</div>
            <div class="stat-value" id="statSales">$ 0</div>
          </div>
        </div>

        
      </div>

      <!-- Table -->
      <div class="content-row">

        <div class="card table-card">

          <div class="table-filters">
            <div class="search-box">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="searchInput" placeholder="Search by name, email or ID...">
            </div>

            <div class="filter-group">
              <label>Customer Type</label>
              <select id="typeFilter" class="form-select">
                <option value="">All Categories</option>
                <option value="regular">Regular</option>
                <option value="vip">VIP</option>
                <option value="new">New</option>
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
                <option value="spent-high">Spent: High to Low</option>
                <option value="spent-low">Spent: Low to High</option>
              </select>
            </div>
          </div>

          <div class="table-responsive">
            <table class="reservation-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Contact</th>
                  <th>Customer Type</th>
                  <th>Total Bookings</th>
                  <th>Total Spent</th>
                  <th>Last Activity</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="customerTableBody">
                <!-- rows rendered by customer.js -->
              </tbody>
            </table>
          </div>
          <div id="noResultsMsg" class="no-results d-none">No customers found.</div>
          <div class="table-footer">
            <span id="tableFooter">Showing 0 of 0 items</span>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<!-- Customer Details Modal (pop-up) -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content details-modal">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsPanelTitle">Customer Details</h5>
        <div class="details-header-actions" id="detailsHeaderActions">
          <button type="button" class="details-header-btn edit" id="detailsHeaderEditBtn" title="Edit Customer"><i class="fa-solid fa-pen"></i></button>
          <button type="button" class="details-header-btn danger" id="detailsHeaderDeleteBtn" title="Delete Customer"><i class="fa-solid fa-trash"></i></button>
          <button type="button" class="details-header-btn" data-bs-dismiss="modal" title="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
      </div>
      <div class="modal-body" id="detailsPanelBody">
        <!-- populated by JS -->
      </div>
    </div>
  </div>
</div>

<!-- Add New Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content reservation-modal">
      <div class="modal-header">
        <div>
          <h5 class="modal-title">Add New Customer</h5>
          <p class="modal-subtitle">Fill in the details below to add a new customer.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="addCustomerForm">
        <div class="modal-body">

          <div class="modal-section-title">Personal Information</div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="modal-field-label">Full Name</label>
              <input type="text" class="form-control" id="cFullName" required>
            </div>
            <div class="col-md-6">
              <label class="modal-field-label">Email Address</label>
              <input type="email" class="form-control" id="cEmail" required>
            </div>
            <div class="col-md-6">
              <label class="modal-field-label">Phone Number</label>
              <input type="tel" class="form-control" id="cPhone" required>
            </div>
            <div class="col-md-6">
              <label class="modal-field-label">Date of Birth</label>
              <input type="date" class="form-control" id="cDob">
            </div>

            <div class="col-12">
              <label class="modal-field-label">Address</label>
              <input type="text" class="form-control" id="cAddress" required>
            </div>

            <div class="col-md-4">
              <label class="modal-field-label">City/ Municipality</label>
              <input type="text" class="form-control" id="cCity" required>
            </div>
            <div class="col-md-4">
              <label class="modal-field-label">Province</label>
              <input type="text" class="form-control" id="cProvince" required>
            </div>
            <div class="col-md-4">
              <label class="modal-field-label">Zip/ Postal Code</label>
              <input type="text" class="form-control input-muted-bg" id="cZip" required>
            </div>
          </div>

          <div class="modal-section-title">Additional Information</div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="modal-field-label">Customer Type</label>
              <input type="text" class="form-control" id="cType" placeholder="e.g. Regular, VIP, New">
            </div>
            <div class="col-md-6">
              <label class="modal-field-label">Preferred Contact Method</label>
              <input type="text" class="form-control" id="cPreferredContactMethod" placeholder="e.g. Email, Phone, SMS">
            </div>
          </div>

          <div class="mb-1">
            <label class="modal-field-label">Notes (Optional)</label>
            <textarea class="form-control" id="cNotes" rows="6"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-new-reservation"><i class="fa-solid fa-floppy-disk"></i> Save Customer</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content reservation-modal">
      <div class="modal-header">
        <div>
          <h5 class="modal-title">Edit Customer</h5>
          <p class="modal-subtitle">Update customer information below.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editCustomerForm">
        <div class="modal-body">

          <div class="modal-section-title">Personal Information</div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="modal-field-label">Full Name</label>
              <input type="text" class="form-control" id="eFullName" required>
            </div>
            <div class="col-md-6">
              <label class="modal-field-label">Email Address</label>
              <input type="email" class="form-control" id="eEmail" required>
            </div>
            <div class="col-md-6">
              <label class="modal-field-label">Phone Number</label>
              <input type="tel" class="form-control" id="ePhone" required>
            </div>
            <div class="col-md-6">
              <label class="modal-field-label">Date of Birth</label>
              <input type="date" class="form-control" id="eDob">
            </div>

            <div class="col-12">
              <label class="modal-field-label">Address</label>
              <input type="text" class="form-control" id="eAddress">
            </div>

            <div class="col-md-4">
              <label class="modal-field-label">City/ Municipality</label>
              <input type="text" class="form-control" id="eCity">
            </div>
            <div class="col-md-4">
              <label class="modal-field-label">Province</label>
              <input type="text" class="form-control" id="eProvince">
            </div>
            <div class="col-md-4">
              <label class="modal-field-label">Zip/ Postal Code</label>
              <input type="text" class="form-control input-muted-bg" id="eZip">
            </div>
          </div>

          <div class="modal-section-title">Additional Information</div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="modal-field-label">Customer Type</label>
              <input type="text" class="form-control" id="eCustomerType" placeholder="e.g. Regular, VIP, New">
            </div>
            <div class="col-md-6">
              <label class="modal-field-label">Preferred Contact Method</label>
              <input type="text" class="form-control" id="ePreferredContactMethod" placeholder="e.g. Email, Phone, SMS">
            </div>
          </div>

          <div class="mb-1">
            <label class="modal-field-label">Notes (Optional)</label>
            <textarea class="form-control" id="eNotesText" rows="6" placeholder="Add a note about this customer..."></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-delete-modal me-auto" id="editDeleteBtn"><i class="fa-solid fa-trash"></i> Delete Customer</button>
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-new-reservation"><i class="fa-solid fa-floppy-disk"></i> Save Customer</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Delete Customer Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content confirm-modal">
      <div class="confirm-modal-body">
        <div class="confirm-icon-circle danger">
          <i class="fa-solid fa-trash"></i>
        </div>
        <h4 class="confirm-title">Delete Customer</h4>
        <p class="confirm-subtitle">Are you sure you want to delete this customer? This action cannot be undone.</p>

        <div class="confirm-client-card">
          <img src="" alt="" class="confirm-client-avatar" id="deleteCustomerAvatar">
          <div class="confirm-client-info">
            <div class="confirm-client-name" id="deleteCustomerName"></div>
            <div class="confirm-client-meta">
              <span id="deleteCustomerContact"></span>
            </div>
          </div>
        </div>

        <div class="confirm-actions">
          <button type="button" class="btn-confirm-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn-confirm-danger" id="confirmDeleteBtn"><i class="fa-solid fa-trash"></i> Delete Customer</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Alert Modal (replaces all browser alert() popups) -->
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

    <script>
  window.CUSTOMERS = @json($customers);
  window.CUSTOMER_BASE = "{{ url('/admin/customers') }}";
</script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
   <script src="{{ asset('js/customer.js') }}"></script>
</body>
</html>