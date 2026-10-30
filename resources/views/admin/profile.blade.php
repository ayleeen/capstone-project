<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Profile Setting</title>
<link href="{{ asset('css/nav.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">

    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
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
          <h1 class="page-title">Profile Setting</h1>
          <p class="page-subtitle">Manage your account, business information, notification, and more.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <a href="notifications.html" class="icon-btn" aria-label="Notifications"><i class="bi bi-bell"></i></a>
          <div class="dropdown">
            <button class="admin-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="https://i.pravatar.cc/100?img=12" class="admin-avatar" id="adminAvatar" alt="">
              <span class="text-start">
                <span class="d-block" id="adminName" style="font-weight:600;font-size:13px;">Admin User</span>
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

      <!-- Profile panel -->
      <div class="profile-panel">
        <form class="profile-card" id="profileForm" novalidate>

          <!-- Profile picture -->
          <div class="photo-card">
            <h2 class="photo-title">Profile Picture</h2>
            <p class="photo-hint">Upload a profile picture (JPG,PNG, Max 2 MB)</p>

            <div class="photo-circle">
              <img id="photoPreview" src="" alt="Profile picture">
              <i class="bi bi-person-fill photo-empty d-none" id="photoEmpty"></i>
            </div>

            <input type="file" id="photoInput" accept=".jpg,.jpeg,.png,image/jpeg,image/png" hidden>
            <button type="button" class="btn-photo" id="changePhotoBtn"><i class="bi bi-upload"></i> Change Photo</button>
            <div><button type="button" class="btn-photo-remove" id="removePhotoBtn">Remove</button></div>
          </div>

          <!-- Fields -->
          <div class="profile-fields">
            <h2 class="section-title">Basic Information</h2>

            <div class="mb-2">
              <label class="field-label" for="fullName">Full Name <span class="req">*</span></label>
              <input type="text" class="form-control" id="fullName" autocomplete="name">
            </div>
            <div class="mb-2">
              <label class="field-label" for="email">Email Address</label>
              <input type="email" class="form-control" id="email" autocomplete="email">
            </div>
            <div class="row g-2 mb-4">
              <div class="col-md-6">
                <label class="field-label" for="phone">Phone Number</label>
                <input type="tel" class="form-control" id="phone" autocomplete="tel">
              </div>
              <div class="col-md-6">
                <label class="field-label" for="secondaryPhone">Secondary Phone Number</label>
                <input type="tel" class="form-control" id="secondaryPhone">
              </div>
            </div>

            <h2 class="section-title">Additional Information</h2>
            <p class="section-sub">Update your personal details (Optional)</p>

            <div class="mb-2">
              <label class="field-label" for="address1">Address Line 1</label>
              <input type="text" class="form-control" id="address1" autocomplete="address-line1">
            </div>
            <div class="mb-3">
              <label class="field-label" for="address2">Address Line 2</label>
              <input type="text" class="form-control" id="address2" autocomplete="address-line2">
            </div>

            <div class="profile-actions">
              <button type="submit" class="btn-save-profile">Save Changes</button>
            </div>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<!-- Remove Photo Confirmation Modal -->
<div class="modal fade" id="removePhotoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content confirm-modal">
      <div class="confirm-modal-body">
        <div class="confirm-icon-circle danger">
          <i class="fa-regular fa-trash-can"></i>
        </div>
        <h4 class="confirm-title">Remove Photo</h4>
        <p class="confirm-subtitle">Are you sure you want to remove your profile picture? Remember to save your changes.</p>
        <div class="confirm-actions">
          <button type="button" class="btn-confirm-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn-confirm-danger" id="confirmRemovePhotoBtn"><i class="fa-regular fa-trash-can"></i> Remove Photo</button>
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
<script src="{{ asset('js/profile.js') }}"></script>
</body>
</html>