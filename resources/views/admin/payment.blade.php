<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Payment Method</title>
<link href="{{ asset('css/nav.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">

    <link href="{{ asset('css/payment-method.css') }}" rel="stylesheet">
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

          <h1 class="page-title">Payment Method</h1>
          <p class="page-subtitle">Manage your account, business information, notification, and more.</p>
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

      <!-- Payment panel -->
      <form class="pay-panel" id="paymentForm">

        <div class="pay-grid">

          <!-- QR code -->
          <section class="qr-section">
            <h2 class="pay-heading">Change QR Code Payment</h2>

            <div class="qr-box">
              <img id="qrPreview" class="d-none" src="" alt="Payment QR code">
              <div class="qr-empty" id="qrEmpty">
                <i class="bi bi-qr-code"></i>
                <span>No QR code uploaded</span>
              </div>
            </div>

            <input type="file" id="qrInput" accept=".jpg,.jpeg,.png,.svg,image/jpeg,image/png,image/svg+xml" hidden>
            <div class="qr-actions">
              <button type="button" class="btn-qr btn-qr-change" id="changeQrBtn"><i class="bi bi-upload"></i> Change QR Code</button>
              <button type="button" class="btn-qr btn-qr-remove" id="removeQrBtn">Remove</button>
            </div>
            <p class="qr-hint">JPG, PNG or SVG. Max size 2MB.</p>
          </section>

          <!-- Accepted methods -->
          <section class="methods-card">
            <div class="methods-header">
              <h2 class="methods-title">Accepted Payment Methods</h2>
              <p class="methods-sub">Enable or disable the payment methods you want to accept.</p>
            </div>

            <div class="method-row">
              <span class="method-icon"><i class="bi bi-cash-coin"></i></span>
              <label class="method-text" for="pm-cod">
                <span class="method-name">Cash on Delivery (COD)</span>
                <span class="method-desc">Accept cash payment upon delivery or pickup.</span>
              </label>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="pm-cod" data-method="cod">
              </div>
            </div>

            <div class="method-row">
              <span class="method-icon"><i class="bi bi-wallet2"></i></span>
              <label class="method-text" for="pm-gcash">
                <span class="method-name">GCash</span>
                <span class="method-desc">Pay using GCash e-wallet</span>
              </label>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="pm-gcash" data-method="gcash">
              </div>
            </div>

            <div class="method-row">
              <span class="method-icon"><i class="bi bi-phone"></i></span>
              <label class="method-text" for="pm-paymaya">
                <span class="method-name">PayMaya</span>
                <span class="method-desc">Pay using PayMaya e-wallet</span>
              </label>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="pm-paymaya" data-method="paymaya">
              </div>
            </div>

            <div class="method-row">
              <span class="method-icon"><i class="bi bi-bank"></i></span>
              <label class="method-text" for="pm-bank">
                <span class="method-name">Bank Transfer</span>
                <span class="method-desc">Accept payment by direct bank transfer.</span>
              </label>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="pm-bank" data-method="bank">
              </div>
            </div>
          </section>

        </div>

        <div class="pay-actions">
          <button type="submit" class="btn-save-pay">Save Changes</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Remove QR Confirmation Modal -->
<div class="modal fade" id="removeQrModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content confirm-modal">
      <div class="confirm-modal-body">
        <div class="confirm-icon-circle danger">
          <i class="fa-regular fa-trash-can"></i>
        </div>
        <h4 class="confirm-title">Remove QR Code</h4>
        <p class="confirm-subtitle">Are you sure you want to remove your payment QR code? Customers won't see it until you upload a new one. Remember to save your changes.</p>
        <div class="confirm-actions">
          <button type="button" class="btn-confirm-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn-confirm-danger" id="confirmRemoveQrBtn"><i class="fa-regular fa-trash-can"></i> Remove QR Code</button>
        </div>
      </div>
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
<script src="{{ asset('js/payment-method.js') }}"></script>
</body>
</html>