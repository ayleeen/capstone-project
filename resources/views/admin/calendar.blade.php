<!DOCTYPE html>
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

    <!-- SIDEBAR -->
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

    <!-- MAIN CONTENT -->
    <div class="col-lg-10">

      <!-- Topbar -->
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

      <!-- SHARED RESERVATION SUMMARY (updates for both Day and Week views) -->
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

      <!-- View controls -->
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
        <!-- LEFT: calendar views -->
        <div class="col-lg-9">

          <!-- DAY VIEW -->
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
                <!-- rows injected by JS -->
              </div>
            </div>
 
          </div>

          <!-- WEEK VIEW -->
          <div class="cal-view-panel" id="weekView">
            <div class="week-grid-card">
              <div class="week-grid" id="weekGrid">
                <!-- header + rows injected by JS -->
              </div>
            </div>
          </div>

          <!-- MONTH VIEW (placeholder) -->
          <div class="cal-view-panel d-none" id="monthView">
            <div class="cal-placeholder">
              <i class="bi bi-calendar3"></i>
              <div>Month view coming soon.</div>
            </div>
          </div>

          <!-- AGENDA VIEW (placeholder) -->
          <div class="cal-view-panel d-none" id="agendaView">
            <div class="cal-placeholder">
              <i class="bi bi-list-ul"></i>
              <div>Agenda view coming soon.</div>
            </div>
          </div>

        </div>

        <!-- RIGHT: legend + upcoming events -->
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
              <!-- injected by JS -->
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
</html>