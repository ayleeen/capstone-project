<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lolay's Catering Services - Reservation</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">

    <link href="{{ asset('css/admin-reservation.css') }}" rel="stylesheet">
    
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

                    <a href="{{ route('admin.reservation') }}" class="nav-link-custom active">
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

                    <a href="{{ route('admin.archive') }}" class="nav-link-custom "><i class="bi bi-archive"></i> Archive</a>
                    <a href="{{ route('admin.audit') }}" class="nav-link-custom "><i class="bi bi-clipboard-check"></i> Audit Logs</a>

              <a href="{{ route('admin.reports') }}" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>

                    <a href="{{ route('admin.settings') }}" class="nav-link-custom">
                        <i class="bi bi-gear"></i>
                        Settings
                    </a>

                </nav>

                <div class="promo-card p-3 text-center mt-auto">

                    <div class="promo-ico rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                        <img src="{{ asset('image/download.png') }}" alt="Promo">
                    </div>

                    <h4 class="mb-1">
                        Delicious Events<br>
                        Memorable Moments
                    </h4>

                    <p class="mb-0">
                        We make every occasion extra special.
                    </p>

                </div>

                <form method="POST"
                      action="{{ route('logout') }}"
                      class="mt-3">

                    @csrf

                    <button type="submit"
                            class="logout-link d-flex align-items-center justify-content-center gap-2 py-2 w-100 border-0 bg-transparent">

                        <i class="bi bi-box-arrow-right"></i>
                        Logout

                    </button>

                </form>

            </div>

        </div>


        <!-- MAIN CONTENT -->
        <div class="col-lg-10">
    @if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
            <!-- TOPBAR -->
            <div class="topbar">

                <h1 class="page-title">
                    Reservation
                </h1>

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


            <!-- STATISTICS -->
            <div class="stat-cards">

                <div class="stat-card">

                    <div class="stat-icon green">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="stat-body">

                        <div class="stat-label">
                            Total Reservation
                        </div>

                        <div class="stat-value" id="statTotal">
                            {{ $stats['total'] ?? 0 }}
                        </div>

                        <span class="stat-tag green">
                            This month
                        </span>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon gold">
                        <i class="fa-regular fa-clock"></i>
                    </div>

                    <div class="stat-body">

                        <div class="stat-label">
                            Pending Reservations
                        </div>

                        <div class="stat-value" id="statPending">
                            {{ $stats['pending'] ?? 0 }}
                        </div>

                        <span class="stat-tag gold">
                            Needs action
                        </span>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon green">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div class="stat-body">

                        <div class="stat-label">
                            Confirmed Reservations
                        </div>

                        <div class="stat-value" id="statConfirmed">
                            {{ $stats['confirmed'] ?? 0 }}
                        </div>

                        <span class="stat-tag green">
                            This month
                        </span>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon red">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>

                    <div class="stat-body">

                        <div class="stat-label">
                            Cancelled Reservation
                        </div>

                        <div class="stat-value" id="statCancelled">
                            {{ $stats['cancelled'] ?? 0 }}
                        </div>

                        <span class="stat-tag red">
                            This month
                        </span>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon green">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>

                    <div class="stat-body">

                        <div class="stat-label">
                            Total Sales
                        </div>

                        <div class="stat-value" id="statSales">
                            ₱{{ number_format($stats['sales'] ?? 0, 2) }}
                        </div>

                        <span class="stat-tag green">
                            This month
                        </span>

                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-icon purple">
                        <i class="fa-solid fa-star"></i>
                    </div>

                    <div class="stat-body">

                        <div class="stat-label">
                            Upcoming Events<br>
                            This Week
                        </div>

                        <div class="stat-value" id="statUpcoming">
                            {{ $stats['upcoming'] ?? 0 }}
                        </div>

                        <span class="stat-tag purple">
                            This week
                        </span>

                    </div>

                </div>

            </div>


            <!-- TABLE AND DETAILS PANEL -->
            <div class="content-row">

                <div class="card table-card" id="tableCardWrap">

                    <div class="table-card-header">

                        <h2>
                            All Reservation
                        </h2>

                    </div>


                    <!-- FILTERS -->
                    <div class="table-filters">

                        <div class="search-box">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            <input type="text"
                                   id="searchInput"
                                   placeholder="Search by client, event or package...">

                        </div>


                        <div class="filter-group">

                            <label>Status</label>

                            <select id="statusFilter"
                                    class="form-select">

                                <option value="">
                                    All Statuses
                                </option>

                                <option value="confirmed">
                                    Confirmed
                                </option>

                                <option value="pending">
                                    Pending
                                </option>

                                <option value="cancelled">
                                    Cancelled
                                </option>

                            </select>

                        </div>


                        <div class="filter-group">

                            <label>Event Type</label>

                            <select id="eventTypeFilter"
                                    class="form-select">

                                <option value="">
                                    All Types
                                </option>

                                <option value="wedding">
                                    Wedding
                                </option>

                                <option value="birthday">
                                    Birthday
                                </option>

                                <option value="corporate">
                                    Corporate Event
                                </option>

                                <option value="debut">
                                    Debut
                                </option>

                                <option value="christening">
                                    Christening
                                </option>

                            </select>

                        </div>


                        <div class="filter-group">

                            <label>Date Range</label>

                            <div class="date-range-box">

                                <i class="fa-regular fa-calendar"></i>

                                <span id="dateRangeLabel">
                                    {{ now()->format('F 1, Y') }}
                                    –
                                    {{ now()->format('F t, Y') }}
                                </span>

                            </div>

                        </div>


                        <button type="button"
        class="btn btn-new-reservation"
        id="newReservationBtn"
        data-bs-toggle="modal"
        data-bs-target="#newReservationModal">
    <i class="fa-solid fa-plus"></i>
    New Reservation
</button>


                        <button type="button"
                                class="btn btn-export"
                                id="exportBtn">

                            <i class="fa-solid fa-arrow-up-from-bracket"></i>
                            Export

                        </button>

                    </div>


                    <!-- RESERVATION TABLE -->
                    <div class="table-responsive">

                        <table class="reservation-table">

                            <thead>

                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Event Type</th>
                                    <th>Date</th>
                                    <th>Package</th>
                                    <th>Guest</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>

                            </thead>


                            <tbody id="reservationTableBody">

                                @forelse($reservations as $r)

                                    <tr data-id="{{ $r->id }}">

                                        <td>
                                            RES-{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}
                                        </td>

                                        <td>
                                            {{ $r->full_name }}
                                        </td>

                                        <td>
                                            {{ $r->event_type === 'corporate'
                                                ? 'Corporate Event'
                                                : ucfirst($r->event_type) }}
                                        </td>

                                        <td>
                                            {{ optional($r->start_date)->format('F j, Y') }}
                                        </td>

                                        <td>
                                            {{ $r->package ?? 'N/A' }}
                                        </td>

                                        <td>
                                            {{ $r->guests }}
                                        </td>

                                        <td>
                                            <span class="status-pill {{ $r->status }}">
                                                {{ ucfirst($r->status) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="payment-pill {{ $r->payment_status ?? 'unpaid' }}">
                                                {{ ucfirst($r->payment_status ?? 'unpaid') }}
                                            </span>
                                        </td>

                                        <td>
                                            ₱{{ number_format($r->total_amount ?? 0, 2) }}
                                        </td>

                                        <td>

                                            <div class="action-icons">

                                                <button type="button"
                                                        class="view-btn"
                                                        data-id="{{ $r->id }}"
                                                        title="View">

                                                    <i class="fa-solid fa-eye"></i>

                                                </button>


                                                <button type="button"
                                                        class="edit-btn"
                                                        data-id="{{ $r->id }}"
                                                        title="Edit">

                                                    <i class="fa-solid fa-pen"></i>

                                                </button>


                                                <div class="action-menu-wrap">

                                                    <button type="button"
                                                            class="more-btn"
                                                            data-id="{{ $r->id }}"
                                                            title="More">

                                                        <i class="fa-solid fa-ellipsis-vertical"></i>

                                                    </button>


                                                    <div class="action-menu">

                                                        <button type="button"
                                                                class="view-details-btn"
                                                                data-id="{{ $r->id }}">

                                                            <i class="fa-solid fa-eye"></i>
                                                            View Details

                                                        </button>


                                                        <button type="button"
                                                                class="edit-reservation-btn"
                                                                data-id="{{ $r->id }}">

                                                            <i class="fa-solid fa-pen"></i>
                                                            Edit Reservation

                                                        </button>


                                                        <button type="button"
                                                                class="delete-reservation-btn danger"
                                                                data-id="{{ $r->id }}">

                                                            <i class="fa-solid fa-trash"></i>
                                                            Delete Reservation

                                                        </button>

                                                    </div>

                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="10" class="text-center">
                                            No reservations found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    <div id="noResultsMsg"
                         class="no-results d-none">

                        No reservations found.

                    </div>


                  

                        <div class="table-footer">

    <span id="tableFooter">
        Showing {{ $reservations->count() }} items
    </span>

    <div class="pagination-controls"
         id="paginationControls">
    </div>

</div>

                    </div>

            


              
<!-- DETAILS PANEL -->
<!-- <div class="card details-panel" id="detailsPanel">

    <div class="details-panel-header">
        <h3 id="detailsPanelTitle">
            Reservation Details
        </h3>

        <button class="details-close-btn" id="closeDetailsPanel" type="button">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="details-panel-body" id="detailsPanelBody">
    </div>
</div> -->


                    


<!-- NEW RESERVATION MODAL -->
<div class="modal fade" id="newReservationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content view-modal">
      <form id="newReservationForm" method="POST" action="{{ route('admin.reservation.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">New Reservation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="edit-field-grid">
            <div class="edit-field"><label>Full Name</label><input type="text" class="form-control" name="full_name" required></div>
            <div class="edit-field"><label>Email Address</label><input type="email" class="form-control" name="email" required></div>
            <div class="edit-field"><label>Contact Number</label><input type="tel" class="form-control" name="phone" required></div>
            <div class="edit-field"><label>Event Type</label>
              <select class="form-select" name="event_type" required>
                <option value="wedding">Wedding</option>
                <option value="birthday">Birthday</option>
                <option value="corporate">Corporate Event</option>
                <option value="debut">Debut</option>
                <option value="christening">Christening</option>
              </select>
            </div>
            <div class="edit-field"><label>Event Title</label><input type="text" class="form-control" name="event_title" required></div>
            <div class="edit-field"><label>Number of Guests</label><input type="number" min="1" class="form-control" name="guests" required></div>
            <div class="edit-field"><label>Start Date</label><input type="date" class="form-control" name="start_date" required></div>
            <div class="edit-field"><label>Start Time</label><input type="time" class="form-control" name="start_time"></div>
            <div class="edit-field"><label>End Date</label><input type="date" class="form-control" name="end_date"></div>
            <div class="edit-field"><label>End Time</label><input type="time" class="form-control" name="end_time"></div>
            <div class="edit-field full"><label>Location / Venue</label><input type="text" class="form-control" name="location"></div>
            <div class="edit-field"><label>Package</label><input type="text" class="form-control" name="package"></div>
            <div class="edit-field"><label>Total Amount</label><input type="number" min="0" step="0.01" class="form-control" name="total_amount" value="0"></div>
            <div class="edit-field"><label>Status</label>
              <select class="form-select" name="status">
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
              </select>
            </div>
            <div class="edit-field"><label>Payment Status</label>
              <select class="form-select" name="payment_status">
                <option value="unpaid">Unpaid</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
              </select>
            </div>
            <div class="edit-field full"><label>Notes</label><textarea class="form-control" name="notes" rows="3"></textarea></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-new-reservation"><i class="fa-solid fa-floppy-disk"></i> Save Reservation</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- VIEW / EDIT RESERVATION MODAL -->
<div class="modal fade" id="viewReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content view-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsPanelTitle">Reservation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody"></div>
        </div>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade"
     id="deleteConfirmModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content confirm-modal">

            <div class="confirm-modal-body">

                <div class="confirm-icon-circle danger">
                    <i class="fa-solid fa-trash"></i>
                </div>

                <h4 class="confirm-title">
                    Delete Reservation
                </h4>

                <p class="confirm-subtitle">
                    Are you sure you want to delete this reservation?
                    This action cannot be undone.
                </p>

                <div class="confirm-client-card">

                    <img src=""
                         alt=""
                         class="confirm-client-avatar"
                         id="deleteClientAvatar">

                    <div class="confirm-client-info">

                        <div class="confirm-client-name"
                             id="deleteClientName">
                        </div>

                        <div class="confirm-client-meta">

                            <span id="deleteClientEmail"></span>
                            <span id="deleteClientPhone"></span>

                        </div>

                    </div>

                </div>

                <div class="confirm-actions">

                    <button type="button"
                            class="btn-confirm-cancel"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="button"
                            class="btn-confirm-danger"
                            id="confirmDeleteBtn">

                        <i class="fa-solid fa-trash"></i>
                        Delete Reservation

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- CANCEL CONFIRMATION MODAL -->
<div class="modal fade"
     id="cancelConfirmModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content confirm-modal">

            <div class="confirm-modal-body">

                <div class="confirm-icon-circle warning">
                    <i class="fa-solid fa-ban"></i>
                </div>

                <h4 class="confirm-title">
                    Cancel Reservation
                </h4>

                <p class="confirm-subtitle">
                    Are you sure you want to cancel this reservation?
                </p>

                <div class="confirm-client-card">

                    <img src=""
                         alt=""
                         class="confirm-client-avatar"
                         id="cancelClientAvatar">

                    <div class="confirm-client-info">

                        <div class="confirm-client-name"
                             id="cancelClientName">
                        </div>

                        <div class="confirm-client-meta">

                            <span id="cancelClientEmail"></span>
                            <span id="cancelClientPhone"></span>

                        </div>

                    </div>

                </div>

                <div class="confirm-actions">

                    <button type="button"
                            class="btn-confirm-cancel"
                            data-bs-dismiss="modal">

                        Keep Reservation

                    </button>

                    <button type="button"
                            class="btn-confirm-warning"
                            id="confirmCancelBtn">

                        <i class="fa-solid fa-ban"></i>
                        Cancel Reservation

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- REQUIRED HIDDEN FORMS FOR JAVASCRIPT -->
<form id="deleteForm"
      method="POST"
      style="display:none;">

    @csrf
    @method('DELETE')

</form>


<form id="cancelForm"
      method="POST"
      style="display:none;">

    @csrf
    @method('PATCH')

</form>
<script>
    window.reservationData = @php
        echo json_encode(
            $reservations
                ->mapWithKeys(function ($r) {
                    return [
                        $r->id => [
                            'id' => $r->id,
                            'full_name' => $r->full_name,
                            'email' => $r->email,
                            'phone' => $r->phone,
                            'event_type' => $r->event_type,
                            'event_title' => $r->event_title,
                            'location' => $r->location,
                            'start_date' => optional($r->start_date)->format('Y-m-d'),
                            'start_time' => $r->start_time,
                            'end_date' => optional($r->end_date)->format('Y-m-d'),
                            'end_time' => $r->end_time,
                            'guests' => $r->guests,
                            'status' => $r->status,
                            'payment_status' => $r->payment_status ?? 'unpaid',
                            'package' => $r->package,
                            'total_amount' => $r->total_amount ?? 0,
                            'notes' => $r->notes,
                            'proof_of_payment' => $r->proof_of_payment ? asset('storage/' . $r->proof_of_payment) : null,
                            'deleteUrl' => route('admin.reservation.destroy', $r->id),
                            'cancelUrl' => route('admin.reservation.cancel', $r->id),
                            'updateUrl' => route('admin.reservation.update', $r->id),
                            'sendConfirmationUrl' => route('admin.reservation.sendConfirmation', $r->id ),
                            'price_per_head'   => $r->price_per_head,
                            'custom_menu'      => $r->custom_menu ?? [],
                            'menu_sets'        => $r->menu_sets ?? [],
                            'addons'           => $r->addons ?? [],
                            'food_restriction' => $r->food_restriction,
                        ]
                    ];
                })
                ->toArray()
        );
    @endphp
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
  window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/admin-reservation.js') }}"></script>
<script>
  window.addEventListener('load', function () {
    var id = new URLSearchParams(window.location.search).get('view');
    if (!id) return;

    var btn = document.querySelector('.view-btn[data-id="' + CSS.escape(id) + '"]');
    if (btn) btn.click();

    // alisin ang ?view= para hindi ulit bumukas kapag nag-refresh
    history.replaceState(null, '', window.location.pathname);
  });
</script>

</body>
</html>