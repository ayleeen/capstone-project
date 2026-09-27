<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Dashboard</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
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
          <a href="{{ route('admin.dashboard') }}" class="nav-link-custom active">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
</a>
          <a href="{{ route('admin.reservation') }}" class="nav-link-custom"><i class="bi bi-journal-text"></i> Reservation</a>
          <a href="{{ route('admin.calendar') }}" class="nav-link-custom"><i class="bi bi-calendar3"></i> Calendar/ Schedule</a>
          <a href="{{ route('admin.packages') }}" class="nav-link-custom"><i class="bi bi-box-seam"></i> Packages</a>
          <a href="{{ route('admin.menu') }}" class="nav-link-custom "><i class="bi bi-egg-fried"></i> Menu</a>
           <a href="{{ route('admin.customer') }}" class="nav-link-custom "><i class="bi bi-person"></i> Customer</a>
          <a href="reports.html" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>
          <a href="reviews.html" class="nav-link-custom"><i class="bi bi-star"></i> Reviews</a>
          <a href="settings.html" class="nav-link-custom"><i class="bi bi-gear"></i> Settings</a>
      </nav> 
    </div>
    </div>
    

    <!-- MAIN CONTENT -->
    <div class="col-lg-10">

      <!-- Topbar -->
      <div class="topbar">
        <h1 class="page-title">Dashboard</h1>
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

      <div class="row g-3 mb-3">
        <div class="col-lg-4 d-flex flex-column gap-3">
<div class="panel">
  <div class="panel-title mb-2">Reservation Status</div>
  <div class="legend-row">
    <span><span class="dot" style="background:#2f7d3c;"></span>Confirmed</span>
    <span>{{ $statusBreakdown['confirmed']['percent'] }}% ({{ $statusBreakdown['confirmed']['count'] }})</span>
  </div>
  <div class="legend-row">
    <span><span class="dot" style="background:#e0a419;"></span>Pending</span>
    <span>{{ $statusBreakdown['pending']['percent'] }}% ({{ $statusBreakdown['pending']['count'] }})</span>
  </div>
  <div class="legend-row">
    <span><span class="dot" style="background:#d9534f;"></span>Cancelled</span>
    <span>{{ $statusBreakdown['cancelled']['percent'] }}% ({{ $statusBreakdown['cancelled']['count'] }})</span>
  </div>
</div>
          <div class="panel">
            <div class="panel-title mb-3">Quick Actions</div>
            <div class="row g-2 text-center">
              <div class="col-4">
                <a href="{{ route('admin.reservation', ['new' => 1]) }}" class="quick-action text-decoration-none d-block">
                  <div class="qa-icon" style="background:#2f7d3c;"><i class="bi bi-plus-lg"></i></div>
                  New Reservation
                </a>
              </div>
              <div class="col-4">
                <div class="quick-action">
                  <div class="qa-icon" style="background:#e0a419;"><i class="bi bi-calendar3"></i></div>
                  View Calendar
                </div>
              </div>
              <div class="col-4">
                <div class="quick-action">
                  <div class="qa-icon" style="background:#3f8ed9;"><i class="bi bi-graph-up"></i></div>
                  Generate Report
                </div>
              </div>
              <div class="col-4">
                <div class="quick-action">
                  <div class="qa-icon" style="background:#7a4fd1;"><i class="bi bi-box-seam"></i></div>
                  Add Package
                </div>
              </div>
              <div class="col-4">
                <div class="quick-action">
                  <div class="qa-icon" style="background:#d9534f;"><i class="bi bi-people"></i></div>
                  Manage Client
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="panel-title mb-0">Upcoming Events</div>
              <a href="#" class="panel-link">View All</a>
            </div>
            <div id="upcomingEvents">
              <div class="empty-note">No upcoming events</div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              
              <button class="btn btn-sm btn-light border" id="calPrev"><i class="bi bi-chevron-left"></i></button>
              <div class="panel-title mb-0" id="calTitle">Loading…</div>
              
              <button class="btn btn-sm btn-light border" id="calNext"><i class="bi bi-chevron-right"></i></button>
            </div>
            <table class="mini-cal">
              <thead><tr><th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th></tr></thead>
              <tbody id="calBody"></tbody>
            </table>
            <div class="d-flex gap-3 mt-2" style="font-size:10.5px;">
              <span><span class="dot" style="background:#2f7d3c;"></span>Confirmed</span>
              <span><span class="dot" style="background:#e0a419;"></span>Pending</span>
              <span><span class="dot" style="background:#d9534f;"></span>Conflict</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom row: reservations table | notifications + best selling -->
      <div class="row g-3">
        <div class="col-lg-8">
          <div class="panel">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="panel-title mb-0">Recent Reservation</div>
              <a href="#" class="panel-link">View All</a>
            </div>
            <div class="table-responsive">
              <table class="table dash-table mb-0">
                <thead>
                  <tr>
                    <th>ID</th><th>Client</th><th>Event Type</th><th>Package</th><th>Status</th><th>Payment</th><th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="7" class="empty-note">No reservations yet</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4 d-flex flex-column gap-3">
          <div class="panel">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="panel-title mb-0">Notifications</div>
              <a href="#" class="panel-link">View All</a>
            </div>
            <div id="notifList">
              <div class="empty-note">No notifications</div>
            </div>
          </div>

          <div class="panel">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="panel-title mb-0">Best-Selling Package</div>
              <a href="#" class="panel-link">View All</a>
            </div>
            <div id="pkgList">
              <div class="empty-note">No package data</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>