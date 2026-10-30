<!-- <!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Calendar / Schedule</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{asset('css/calendar.css')}}" rel="stylesheet">


</head>
<body>
<div class="container-fluid p-3">
  <div class="row g-3">

   
    <div class="col-lg-2">
      <div class="sidebar d-flex flex-column">
        <div class="brand-box">
          <div class="brand-logo">
            <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="">
          </div>
          <div class="brand-name">Lolay's Catering<br>Services</div>
        </div>
        <nav class="flex-grow-1">
<nav class="flex-grow-1">

    <a href="{{ route('admin.dashboard') }}" class="nav-link-custom">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <a href="{{ route('admin.reservation') }}" class="nav-link-custom ">
        <i class="bi bi-journal-text"></i> Reservation
    </a>

    <a href="{{ route('admin.calendar') }}" class="nav-link-custom active">
        <i class="bi bi-calendar3"></i> Calendar/ Schedule
    </a>

    <a href="{{ route('admin.packages') }}" class="nav-link-custom">
        <i class="bi bi-box-seam"></i> Packages
    </a>

    <a href="{{ route('admin.menu') }}" class="nav-link-custom">
        <i class="bi bi-egg-fried"></i> Menu
    </a>

    <a href="{{ route('admin.customer') }}" class="nav-link-custom">
        <i class="bi bi-person"></i> Customer
    </a>
<a href="{{ route('admin.archive') }}" class="nav-link-custom "><i class="bi bi-archive"></i> Archive</a>

    <a href="reports.html" class="nav-link-custom">
        <i class="bi bi-bar-chart"></i> Reports
    </a>

    <a href="reviews.html" class="nav-link-custom">
        <i class="bi bi-star"></i> Reviews
    </a>

    <a href="settings.html" class="nav-link-custom">
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

  
    <div class="col-lg-10">

   
   <div class="topbar">
        <h1 class="page-title">Calendar/Schedule</h1>
        <div class="d-flex align-items-center gap-3">
          <div class="icon-btn"><i class="bi bi-bell"></i></div>
          <div class="dropdown">
            <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
              <img src="https://i.pravatar.cc/100?img=12" class="admin-avatar" alt="Admin">
              <div>
                <div style="font-weight:600;font-size:13px;">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                <div style="font-size:10.5px;color:var(--muted);">Administrator</div>
              </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    <i class="bi bi-box-arrow-right"></i> Logout
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </div>

      
      <div class="cal-summary-row mb-3">
        <div class="summary-card">
          <div class="summary-ico ico-green"><i class="bi bi-calendar-event"></i></div>
          <div>
            <div class="summary-value" id="summaryEvents">0</div>
            <div class="summary-label" id="summaryEventsLabel">Total Events This Week</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="summary-ico ico-purple"><i class="bi bi-people"></i></div>
          <div>
            <div class="summary-value" id="summaryGuests">0</div>
            <div class="summary-label">Total Guest</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="summary-ico ico-pink"><i class="bi bi-cash-stack"></i></div>
          <div>
            <div class="summary-value" id="summarySales">₱0</div>
            <div class="summary-label" id="summarySalesLabel">Total Sales this Week</div>
          </div>
        </div>
        <div class="summary-card schedule-summary-card">
          <div class="schedule-summary-title">Schedule Summary</div>
          <div class="schedule-summary-row"><span class="dot-status confirmed"></span> Confirmed <span class="schedule-summary-pct" id="summaryConfirmedPct">0 (0%)</span></div>
          <div class="schedule-summary-row"><span class="dot-status pending"></span> Pending <span class="schedule-summary-pct" id="summaryPendingPct">0 (0%)</span></div>
          <div class="schedule-summary-row"><span class="dot-status cancelled"></span> Cancelled <span class="schedule-summary-pct" id="summaryCancelledPct">0 (0%)</span></div>
        </div>
      </div>

      
      <div class="cal-controls mb-3">
        <div class="view-tabs">
          <button type="button" class="view-tab" data-view="day">Day</button>
          <button type="button" class="view-tab active" data-view="week">Week</button>
          <button type="button" class="view-tab" data-view="month">Month</button>
          <button type="button" class="view-tab" data-view="agenda">Agenda</button>
        </div>

        <div class="date-nav">
          <button type="button" class="date-nav-btn" id="prevBtn"><i class="bi bi-chevron-left"></i></button>
          <span class="date-nav-label" id="dateNavLabel">May 19 - May 25, 2026</span>
          <button type="button" class="date-nav-btn" id="nextBtn"><i class="bi bi-chevron-right"></i></button>
        </div>

        <div class="cal-actions">
          <button type="button" class="btn-today" id="todayBtn">Today</button>
          <button type="button" class="btn-filter"><i class="bi bi-funnel"></i> Filter</button>
          <button type="button" class="btn-new-reservation"><i class="bi bi-plus-circle-fill"></i> New Reservation</button>
        </div>
      </div>

      <div class="row g-3">
       
        <div class="col-lg-9">

         
            <div class="cal-view-panel" id="dayView">
 
            <div class="day-summary-card mb-3">
              <div class="day-summary-date">
                <div class="day-summary-label" id="daySummaryLabel">Wednesday</div>
                <div class="day-summary-value" id="daySummaryDate">May 22, 2026</div>
              </div>
              <div class="day-summary-stats">
                <div class="day-summary-stat"><i class="bi bi-calendar-event"></i> <span id="dayStatEvents">5</span> Events</div>
                <div class="day-summary-stat"><i class="bi bi-clock"></i> <span id="dayStatBooked">8h</span> Booked</div>
                <div class="day-summary-stat"><i class="bi bi-cash-stack"></i> <span id="dayStatRevenue">₱150,000</span></div>
              </div>
              <a href="#" class="day-summary-more">More Details</a>
            </div>
 
            <div class="day-grid-card">
              <div class="day-grid-title" id="dayGridTitle">Wednesday, May 22, 2026</div>
              <div class="day-grid" id="dayGrid">
              
              </div>
            </div>
 
          </div>

        
          <div class="cal-view-panel" id="weekView">
            <div class="week-grid-card">
              <div class="week-grid" id="weekGrid">
              
              </div>
            </div>
          </div>

     
          <div class="cal-view-panel d-none" id="monthView">
            <div class="cal-placeholder">
              <i class="bi bi-calendar3"></i>
              <div>Month view coming soon.</div>
            </div>
          </div>

        
          <div class="cal-view-panel d-none" id="agendaView">
            <div class="cal-placeholder">
              <i class="bi bi-list-ul"></i>
              <div>Agenda view coming soon.</div>
            </div>
          </div>

        </div>

        
        <div class="col-lg-3 right-col-sticky">
          <div class="panel-white mb-3">
            <div class="panel-heading">Event Type</div>
            <div class="legend-list">
              <div class="legend-row"><span class="legend-dot dot-wedding"></span> Wedding</div>
              <div class="legend-row"><span class="legend-dot dot-corporate"></span> Corporate Event</div>
              <div class="legend-row"><span class="legend-dot dot-baptismal"></span> Baptismal</div>
              <div class="legend-row"><span class="legend-dot dot-birthday"></span> Birthday</div>
              <div class="legend-row"><span class="legend-dot dot-debut"></span> Private</div>
            </div>
          </div>

          <div class="panel-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="panel-heading mb-0">Upcoming Events</div>
              <a href="#" class="see-all-link">See All</a>
            </div>
            <div id="upcomingEventsList" class="upcoming-list">
              
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/calendar/js') }}"></script>



</body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services -Schedule Management</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{asset('css/calendar1.css')}}" rel="stylesheet">

</head>
<body>
<div class="container-fluid p-3">
  <div class="row g-3">

   
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
<a href="{{ route('admin.dashboard') }}" class="nav-link-custom ">
      <i class="bi bi-grid-1x2-fill"></i> Dashboard
  </a>
            <a href="{{ route('admin.reservation') }}" class="nav-link-custom"><i class="bi bi-journal-text"></i> Reservation</a>
            <a href="{{ route('admin.calendar') }}" class="nav-link-custom active"><i class="bi bi-calendar3"></i> Calendar/ Schedule</a>
            <a href="{{ route('admin.packages') }}" class="nav-link-custom"><i class="bi bi-box-seam"></i> Packages</a>
            <a href="{{ route('admin.menu') }}" class="nav-link-custom "><i class="bi bi-egg-fried"></i> Menu</a>
            <a href="{{ route('admin.customer') }}" class="nav-link-custom "><i class="bi bi-person"></i> Customer</a>
            <a href="{{ route('admin.archive') }}" class="nav-link-custom "><i class="bi bi-archive"></i> Archive</a>
            <a href="{{ route('admin.audit') }}" class="nav-link-custom "><i class="bi bi-clipboard-check"></i> Audit Logs</a>
            <a href="{{ route('admin.reports') }}" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>
            <a href="{{ route('admin.settings') }}" class="nav-link-custom"><i class="bi bi-gear"></i> Settings</a>
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

   
    <div class="col-lg-10">

     
      <div class="topbar">
        <div>
          <h1 class="page-title mb-1">Schedule Management</h1>
          <p class="page-subtitle">Manage your bookings, appointments, and event schedule.</p>
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

          <!-- Profile dropdown -->
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

     
      <div class="top-row">
        <div class="stat-grid" aria-label="Summary">
          <div class="stat-card">
            <span class="stat-icon sq green"><i class="bi bi-calendar-check-fill"></i></span>
            <div class="stat-body">
              <div class="stat-label">Selected Date</div>
              <div class="stat-value" id="dsDate"></div>
              <div class="stat-sub" id="dsWeekday"></div>
            </div>
          </div>
          <div class="stat-card">
            <span class="stat-icon circle yellow"><i class="bi bi-calendar-event-fill"></i></span>
            <div class="stat-body">
              <div class="stat-label">Total Events</div>
              <div class="stat-value" id="stEvents">0</div>
              <div class="stat-sub" id="stRange1"></div>
            </div>
          </div>
          <div class="stat-card">
            <span class="stat-icon circle green"><i class="bi bi-people-fill"></i></span>
            <div class="stat-body">
              <div class="stat-label">Total Guests</div>
              <div class="stat-value" id="stGuests">0</div>
              <div class="stat-sub" id="stRange2"></div>
            </div>
          </div>
          <div class="stat-card">
            <span class="stat-icon circle green"><i class="bi bi-currency-dollar"></i></span>
            <div class="stat-body">
              <div class="stat-label">Total Sales</div>
              <div class="stat-value" id="stSales">₱0</div>
                <div class="stat-sub" id="stRange3"></div>
              
            </div>
          </div>
        </div>

        <button type="button" class="btn-new-res" data-bs-toggle="modal" data-bs-target="#reservationModal">
          <i class="bi bi-plus-lg"></i> Add new reservation
        </button>
      </div>

      <div class="layout">
        <section class="cal-card" aria-label="Reservation calendar">
          <div class="cal-toolbar">
            <div class="view-tabs" role="group" aria-label="Calendar view">
              <button type="button" data-view="day">Day</button>
              <button type="button" data-view="week">Week</button>
              <button type="button" data-view="month">Month</button>
              <button type="button" data-view="agenda">Agenda</button>
            </div>

            <div class="cal-nav">
              <button type="button" class="nav-sq" id="prevBtn" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
              <h2 class="cal-range"><i class="bi bi-calendar3"></i><span id="calTitle" aria-live="polite"></span></h2>
              <button type="button" class="nav-sq" id="nextBtn" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
              <button type="button" class="btn-today" id="todayBtn">Today</button>
            </div>
          </div>

          <div class="type-filters" id="typeFilters" aria-label="Filter by event type"></div>

          <div id="calendar"></div>
        </section>

        <div class="side-col">
          <aside class="upcoming-card" aria-label="Upcoming events">
            <h2>Upcoming events</h2>
            <ul id="upcomingList" class="upcoming-list"></ul>
          </aside>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <span class="type-badge" id="mType"></span>
          <h5 class="modal-title mt-2" id="eventModalTitle"></h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="detail-list">
          <dt>Date</dt><dd id="mDate"></dd>
          <dt>Time</dt><dd id="mTime"></dd>
          <dt>Guests</dt><dd id="mGuests"></dd>
          <dt>Venue</dt><dd id="mVenue"></dd>
          <dt>Package</dt><dd id="mPackage"></dd>
          <dt>Status</dt><dd id="mStatus"></dd>
        </dl>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="resModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form class="modal-content res-modal" id="reservationForm" novalidate>

      <div class="modal-header res-head">
        <div>
          <h5 class="modal-title" id="resModalTitle">New Reservation</h5>
          <p>Fill in the details below to add a new reservation.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body res-body">

        <h6 class="res-section">Client Information</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="res-label" for="rName">Full Name</label>
            <input type="text" class="form-control res-input" id="rName" required autocomplete="name">
            <div class="invalid-feedback">Enter the client's full name.</div>
          </div>
          <div class="col-md-6">
            <label class="res-label" for="rEmail">Email Address</label>
            <input type="email" class="form-control res-input" id="rEmail" required autocomplete="email">
            <div class="invalid-feedback">Enter a valid email address.</div>
          </div>
          <div class="col-md-6">
            <label class="res-label" for="rPhone">Phone Number</label>
            <input type="tel" class="form-control res-input" id="rPhone" required autocomplete="tel">
            <div class="invalid-feedback">Enter a phone number.</div>
          </div>
          <div class="col-md-6">
            <label class="res-label" for="rType">Event Type</label>
            <select class="form-select res-input" id="rType" required>
              <option value=""></option>
              <option value="wedding">Wedding</option>
              <option value="corporate">Corporate Event</option>
              <option value="baptismal">Baptismal</option>
              <option value="birthday">Birthday</option>
              <option value="private">Private</option>
            </select>
            <div class="invalid-feedback">Choose an event type.</div>
          </div>
        </div>

        <h6 class="res-section">Event Details</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="res-label" for="rTitle">Event Title</label>
            <input type="text" class="form-control res-input" id="rTitle" required>
            <div class="invalid-feedback">Enter an event title.</div>
          </div>
          <div class="col-md-6">
            <label class="res-label" for="rVenue">Location / Venue</label>
            <input type="text" class="form-control res-input" id="rVenue" required>
            <div class="invalid-feedback">Enter the venue.</div>
          </div>

          <div class="col-md-6">
            <label class="res-label" for="rStartDate">Start Date &amp; Time</label>
            <div class="row g-2">
              <div class="col-6">
                <input type="date" class="form-control res-input" id="rStartDate" required>
                <div class="invalid-feedback">Pick a date.</div>
              </div>
              <div class="col-6">
                <input type="time" class="form-control res-input" id="rStartTime" aria-label="Start time" required>
                <div class="invalid-feedback">Pick a time.</div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <label class="res-label" for="rEndDate">End Date &amp; Time</label>
            <div class="row g-2">
              <div class="col-6">
                <input type="date" class="form-control res-input" id="rEndDate" required>
                <div class="invalid-feedback">Pick a date.</div>
              </div>
              <div class="col-6">
                <input type="time" class="form-control res-input" id="rEndTime" aria-label="End time" required>
                <div class="invalid-feedback" id="rEndFeedback">Set an end time after the start.</div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <label class="res-label" for="rGuests">Number of Guests</label>
            <input type="number" class="form-control res-input" id="rGuests" min="1" required>
            <div class="invalid-feedback">Enter at least 1 guest.</div>
          </div>
          <div class="col-md-6">
            <label class="res-label" for="rStatus">Status</label>
            <select class="form-select res-input" id="rStatus" required>
              <option value=""></option>
              <option value="pending">Pending payment</option>
              <option value="confirmed">Confirmed</option>
            </select>
            <div class="invalid-feedback">Choose a status.</div>
          </div>

          <div class="row g-3">
          <div class="col-md-4">
            <label class="res-label" for="rPackage">Package </label>
            <select class="form-select res-input" id="rPackage" required>
              <option value=""></option>
              <option>Grand Wedding</option>
              <option>Baptismal Package</option>
              <option>Party Package A</option>
              <option>Party Package B</option>
              <option>Debut Package</option>
              <option>Corporate Lunch</option>
              <option>Seminar Snacks</option>
              <option>Private Dining</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="res-label" for="rStaff">Assigned Staff (Optional)</label>
            <select class="form-select res-input" id="rStaff">
              <option value=""></option>
              <option>Team A</option>
              <option>Team B</option>
              <option>Team C</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="res-label" for="rBudget">Estimated Budget (Optional)</label>
            <select class="form-select res-input" id="rBudget">
              <option value=""></option>
              <option data-amount="12500">Under ₱25,000</option>
              <option data-amount="37500">₱25,000 – ₱50,000</option>
              <option data-amount="75000">₱50,000 – ₱100,000</option>
              <option data-amount="150000">₱100,000 – ₱200,000</option>
              <option data-amount="250000">Above ₱200,000</option>
            </select>
          </div>
        </div>

          <h6 class="res-section">Additional Options</h6>
          <div class="col-12">
            <label class="res-label" for="rNotes">Special Request / Notes</label>
            <textarea class="form-control res-input" id="rNotes" rows="3"></textarea>
          </div>
        </div>

      </div>

      <div class="modal-footer res-foot">
        <button type="button" class="btn btn-res-cancel" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-res-create"><i class="bi bi-floppy"></i> Create Reservation</button>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered success-dialog">
    <div class="modal-content success-modal">
      <div class="success-icon"><i class="bi bi-check-lg"></i></div>
      <h5 id="successTitle">Reservation Successful!</h5>
      <p class="success-text">The reservation has been added to the calendar.</p>
      <dl class="detail-list success-summary">
        <dt>Event</dt><dd id="sType"></dd>
        <dt>Client</dt><dd id="sClient"></dd>
        <dt>Date</dt><dd id="sDate"></dd>
        <dt>Time</dt><dd id="sTime"></dd>
        <dt>Guests</dt><dd id="sGuests"></dd>
      </dl>
      <button type="button" class="btn btn-res-create justify-content-center w-100" data-bs-dismiss="modal">Done</button>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
  window.CALENDAR_EVENTS = @json($events);
</script>
<script src="{{ asset('js/calendar1.js') }}"></script>

</body>
</html>