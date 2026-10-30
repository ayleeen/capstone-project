<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Login & Security</title>
<link href="{{ asset('css/nav.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">

    <link href="{{ asset('css/security.css') }}" rel="stylesheet">
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
          <a href="calendar.html" class="nav-link-custom"><i class="bi bi-calendar3"></i> Calendar/ Schedule</a>
          <a href="packages.html" class="nav-link-custom"><i class="bi bi-box-seam"></i> Packages</a>
          <a href="menu-management.html" class="nav-link-custom"><i class="bi bi-cup-hot"></i> Menu</a>
          <a href="customer.html" class="nav-link-custom"><i class="bi bi-person"></i> Customer</a>
          <a href="reports.html" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>
          <a href="settings.html" class="nav-link-custom active"><i class="bi bi-gear"></i> Settings</a>
          <a href="archive.html" class="nav-link-custom"><i class="bi bi-archive"></i> Archive</a>
          <a href="audit.html" class="nav-link-custom"><i class="bi bi-clipboard-check"></i> Audit</a>
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
          <h1 class="page-title">Login &amp; Security</h1>
          <p class="page-subtitle">Manage your password and keep your account secure.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <a href="notifications.html" class="icon-btn" aria-label="Notifications"><i class="bi bi-bell"></i></a>
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

      <!-- ============ Change Password ============ -->
      <form class="sec-card" id="passwordForm" novalidate>
        <div class="sec-head">
          <span class="sec-icon"><i class="bi bi-lock"></i></span>
          <div>
            <h2 class="sec-title">Change Password</h2>
            <p class="sec-sub">Update your password to keep your account secure.</p>
          </div>
        </div>

        <div class="row g-4 mt-1">
          <div class="col-md-4">
            <label class="field-label" for="currentPassword">Current Password</label>
            <div class="pw-field">
              <input type="password" id="currentPassword" placeholder="Enter your current password" autocomplete="current-password">
              <button type="button" class="pw-toggle" data-target="currentPassword" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
            </div>
            <div class="field-error" id="currentError"></div>
          </div>
          <div class="col-md-4">
            <label class="field-label" for="newPassword">New Password</label>
            <div class="pw-field">
              <input type="password" id="newPassword" placeholder="Enter your new password" autocomplete="new-password">
              <button type="button" class="pw-toggle" data-target="newPassword" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
            </div>
            <div class="field-error" id="newError"></div>
          </div>
          <div class="col-md-4">
            <label class="field-label" for="confirmPassword">Confirm New Password</label>
            <div class="pw-field">
              <input type="password" id="confirmPassword" placeholder="Re-enter your new password" autocomplete="new-password">
              <button type="button" class="pw-toggle" data-target="confirmPassword" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
            </div>
            <div class="field-error" id="confirmError"></div>
          </div>
        </div>

        <div class="pw-bottom">
         <div class="pw-meta" id="pwMeta" hidden>
          <div class="strength-box">
            <div class="field-label mb-2">Password Strength</div>
            <div class="strength-row">
              <div class="strength-bars" id="strengthBars">
                <span></span><span></span><span></span><span></span>
              </div>
              <span class="strength-text" id="strengthText"></span>
            </div>
          </div>

          <div class="req-box">
            <div class="field-label mb-2">Password Requirements</div>
            <ul class="req-list" id="reqList">
              <li data-req="length"><i class="bi bi-check-circle-fill"></i> At least 8 characters</li>
              <li data-req="mix"><i class="bi bi-check-circle-fill"></i> Include letters and numbers</li>
            </ul>
          </div>
         </div>

          <div class="pw-submit">
            <button type="submit" class="btn-update"><i class="bi bi-lock"></i> Update Password</button>
          </div>
        </div>
      </form>

      <!-- ============ Recent Login Activity ============ -->
      <section class="sec-card">
        <div class="sec-head">
          <span class="sec-icon"><i class="bi bi-clock-history"></i></span>
          <div class="flex-grow-1">
            <h2 class="sec-title">Recent Login Activity</h2>
            <p class="sec-sub">View your latest sign-in sessions and devices.</p>
          </div>
          <button type="button" class="btn-viewall" id="viewAllBtn"><i class="bi bi-eye"></i> <span id="viewAllText">View All</span></button>
        </div>

        <div class="table-responsive">
          <table class="activity-table">
            <thead>
              <tr>
                <th>Device</th>
                <th>Browser</th>
                <th>Location</th>
                <th>Date &amp; Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="activityBody"></tbody>
          </table>
        </div>
      </section>

      <!-- ============ Security Tips ============ -->
      <section class="sec-card tips-card">
        <div class="sec-head mb-0">
          <span class="sec-icon"><i class="bi bi-shield-check"></i></span>
          <div>
            <h2 class="sec-title">Security Tips</h2>
            <p class="sec-sub">Use a strong password, avoid sharing your account, and keep your login details private.</p>
          </div>
        </div>
      </section>

    </div>
  </div>
</div>

<!-- Alert Modal -->
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
<script src="{{ asset('js/security.js') }}"></script>
</body>
</html>