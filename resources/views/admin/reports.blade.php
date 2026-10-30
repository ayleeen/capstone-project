<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Reports</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">

    <link href="{{ asset('css/reports.css') }}" rel="stylesheet">
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

                    <a href="{{ route('admin.archive') }}" class="nav-link-custom "><i class="bi bi-archive"></i> Archive</a>
                    <a href="{{ route('admin.audit') }}" class="nav-link-custom "><i class="bi bi-clipboard-check"></i> Audit Logs</a>
          <a href="{{ route('admin.reports') }}" class="nav-link-custom active"><i class="bi bi-bar-chart"></i> Reports</a>
          <a href="{{ route('admin.settings') }}" class="nav-link-custom"><i class="bi bi-gear"></i> Settings</a>
        </nav>
        <div class="promo-card p-3 text-center mt-auto">
          <div class="promo-ico rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
            <img src="{{ asset('image/download.png') }}" alt="">
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
          <h1 class="page-title mb-0">Reports</h1>
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

      <!-- Stat cards -->
      <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2">
          <div class="stat-card-report h-100">
            <div class="stat-mini-toggle"></div>
            <div class="stat-ico-report ico-green"><i class="bi bi-cash-coin"></i></div>
            <div class="stat-label-report">Total Revenue</div>
            <div class="stat-value-report" id="statTotalRevenue">₱0</div>
            <div class="stat-trend-report" id="statTotalRevenueSub"></div>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="stat-card-report h-100">
            <div class="stat-mini-toggle"></div>
            <div class="stat-ico-report ico-green"><i class="bi bi-calendar-check"></i></div>
            <div class="stat-label-report">Total Bookings</div>
            <div class="stat-value-report" id="statTotalBookings">0</div>
            <div class="stat-trend-report" id="statTotalBookingsSub"></div>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="stat-card-report h-100">
            <div class="stat-mini-toggle"></div>
            <div class="stat-ico-report ico-yellow"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-label-report">Pending Payments</div>
            <div class="stat-value-report" id="statPendingPayments">₱0</div>
            <div class="stat-trend-report" id="statPendingPaymentsSub"></div>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="stat-card-report h-100">
            <div class="stat-mini-toggle"></div>
            <div class="stat-ico-report ico-pink"><i class="bi bi-star-fill"></i></div>
            <div class="stat-label-report">Most Popular Package</div>
            <div class="stat-value-report stat-value-report-text" id="statPopularPackage">—</div>
            <div class="stat-trend-report" id="statPopularPackageSub"></div>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="stat-card-report h-100">
            <div class="stat-mini-toggle"></div>
            <div class="stat-ico-report ico-blue"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="stat-label-report">Monthly Growth</div>
            <div class="stat-value-report" id="statMonthlyGrowth">—</div>
            <div class="stat-trend-report" id="statMonthlyGrowthSub"></div>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="stat-card-report h-100">
            <div class="stat-mini-toggle"></div>
            <div class="stat-ico-report ico-mint"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label-report">Repeat Customer Rate</div>
            <div class="stat-value-report" id="statRepeatRate">—</div>
            <div class="stat-trend-report" id="statRepeatRateSub"></div>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <!-- LEFT: filters, charts, table -->
        <div class="col-lg-9">

          <!-- Filter bar -->
          <div class="filter-bar-report d-flex align-items-center gap-2 mb-3 flex-wrap">
            <button type="button" class="date-range-btn" id="dateRangeBtn">
              <i class="bi bi-calendar3"></i> <span id="dateRangeLabel">Select date range</span>
            </button>

<select id="eventTypeFilter" class="form-select">
  <option value="">All Event Types</option>
  <option value="wedding">Wedding</option>
  <option value="birthday">Birthday</option>
  <option value="corporate">Corporate</option>
  <option value="christening">Christening</option>
  <option value="debut">Debut</option>
</select>

<select id="customerTypeFilter" class="form-select">
  <option value="">All Customer Types</option>
  <option value="new">New</option>
  <option value="repeat">Returning</option>
</select>

<select id="paymentStatusFilter" class="form-select">
  <option value="">All Payment Status</option>
  <option value="paid">Paid</option>
  <option value="partial">Partial</option>
  <option value="unpaid">Unpaid</option>
</select>

<select id="paymentStateFilter" class="form-select">
  <option value="">All Payment States</option>
  <option value="confirmed">Confirmed</option>
  <option value="pending">Pending</option>
</select>

            <button type="button" class="reset-filters-btn" id="resetFiltersBtn">
              <i class="bi bi-arrow-clockwise"></i> Reset
            </button>
          </div>

          <!-- Row: Sales Overview + Bookings by Event Type -->
          <div class="row g-3 mb-3">
            <div class="col-lg-7">
              <div class="chart-panel h-100">
                <div class="chart-panel-header">
                  <div class="chart-panel-title">Sales Overview</div>
<select id="salesPeriodSelect" class="chart-period-select">
  <option value="3">3M</option>
  <option value="6" selected>6M</option>
  <option value="12">1Y</option>
</select>
                </div>
                <div class="chart-canvas-wrap">
                  <canvas id="salesOverviewChart"></canvas>
                  <div class="chart-empty-state" id="salesOverviewEmpty">
                    <i class="bi bi-bar-chart-line"></i>
                    <div>No sales data available</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="chart-panel h-100">
                <div class="chart-panel-header">
                  <div class="chart-panel-title">Bookings by Event Type</div>
                </div>
                <div class="chart-canvas-wrap">
                  <canvas id="bookingsChart"></canvas>
                  <div class="chart-empty-state" id="bookingsEmpty">
                    <i class="bi bi-calendar-week"></i>
                    <div>No booking data available</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Row: Payment Status + Monthly Performance -->
          <div class="row g-3 mb-3">
            <div class="col-lg-5">
              <div class="chart-panel h-100">
                <div class="chart-panel-header">
                  <div class="chart-panel-title">Payment Status</div>
                </div>
                <div class="payment-status-body">
                  <div class="chart-canvas-wrap chart-canvas-wrap-sm">
                    <canvas id="paymentStatusChart"></canvas>
                    <div class="chart-empty-state" id="paymentStatusEmpty">
                      <i class="bi bi-pie-chart"></i>
                      <div>No payment data</div>
                    </div>
                  </div>
                  <div class="payment-legend" id="paymentLegend">
                    <div class="payment-legend-item">
                      <span class="legend-dot legend-dot-paid"></span>
                      <div>
                        <div class="legend-label">Paid</div>
                        <div class="legend-value" id="legendPaid">0% (₱0)</div>
                      </div>
                    </div>
                    <div class="payment-legend-item">
                      <span class="legend-dot legend-dot-partial"></span>
                      <div>
                        <div class="legend-label">Partial</div>
                        <div class="legend-value" id="legendPartial">0% (₱0)</div>
                      </div>
                    </div>
                    <div class="payment-legend-item">
                      <span class="legend-dot legend-dot-unpaid"></span>
                      <div>
                        <div class="legend-label">Unpaid</div>
                        <div class="legend-value" id="legendUnpaid">0% (₱0)</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-7">
              <div class="chart-panel h-100">
                <div class="chart-panel-header">
                  <div class="chart-panel-title">Monthly Performance</div>
                </div>
<div class="chart-canvas-wrap">
  <canvas id="monthlyPerformanceChart"></canvas>
  <div class="chart-empty-state" id="monthlyPerformanceEmpty">
    <i class="bi bi-graph-up"></i>
    <div>No performance data yet</div>
  </div>
</div>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="reports-table-card">
            <div class="table-responsive">
              <table class="table mb-0 align-middle reports-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Event</th>
                    <th>Customer</th>
                    <th>Package</th>
                    <th>Revenue</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody id="reportsTableBody"></tbody>
              </table>
            </div>
            <div class="reports-table-footer">
              <span id="reportsTableFooterText">Showing 0 of 0 entries</span>
              <div class="pagination-report" id="paginationReport">
                <button type="button" class="page-btn" disabled>‹</button>
                <button type="button" class="page-btn active">1</button>
                <button type="button" class="page-btn" disabled>›</button>
              </div>
            </div>
          </div>

        </div>

        <!-- RIGHT: Insights + Quick Actions -->
        <div class="col-lg-3 right-col-sticky">
          <div class="panel-white mb-3">
            <div class="panel-heading"><i class="bi bi-lightbulb-fill"></i> Insights</div>
            <div class="insights-list">
              <div class="insight-row">
                <div class="insight-ico"><i class="bi bi-trophy-fill"></i></div>
                <div>
                  <div class="insight-title">Top Performing Package</div>
                  <div class="insight-value" id="insightTopPackage">—</div>
                  <div class="insight-sub" id="insightTopPackageSub">No data yet</div>
                </div>
              </div>
              <div class="insight-row">
                <div class="insight-ico"><i class="bi bi-calendar2-week-fill"></i></div>
                <div>
                  <div class="insight-title">Best Sales Month</div>
                  <div class="insight-value" id="insightBestMonth">—</div>
                  <div class="insight-sub" id="insightBestMonthSub">No data yet</div>
                </div>
              </div>
              <div class="insight-row">
                <div class="insight-ico"><i class="bi bi-person-badge-fill"></i></div>
                <div>
                  <div class="insight-title">Highest Paying Client</div>
                  <div class="insight-value" id="insightTopClient">—</div>
                  <div class="insight-sub" id="insightTopClientSub">No data yet</div>
                </div>
              </div>
              <div class="insight-row">
                <div class="insight-ico"><i class="bi bi-calendar-plus-fill"></i></div>
                <div>
                  <div class="insight-title">Upcoming High-Value Events</div>
                  <div class="insight-value" id="insightUpcoming">—</div>
                  <div class="insight-sub" id="insightUpcomingSub">No data yet</div>
                </div>
              </div>
            </div>
          </div>

          <div class="panel-white">
            <div class="panel-heading">Quick Actions</div>
            <div class="quick-actions-list">
              <!-- <button type="button" class="quick-action-btn qa-pdf" id="qaGenerateSalesReport">
                <i class="bi bi-file-earmark-pdf-fill"></i> Generate Sales Report (PDF)
              </button> -->
              <button type="button" class="quick-action-btn qa-excel" id="qaExportExcel">
                <i class="bi bi-file-earmark-excel-fill"></i> Export to Excel
              </button>
              <!-- <button type="button" class="quick-action-btn qa-print" id="qaPrintReport">
                <i class="bi bi-printer-fill"></i> Print Report
              </button> -->
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  window.reportsDataUrl = "{{ route('admin.reports.data') }}";
  window.reportsReservationUrl = "{{ route('admin.reservation') }}";
</script>
<script src="{{ asset('js/reports.js') }}"></script>
</body>
</html>