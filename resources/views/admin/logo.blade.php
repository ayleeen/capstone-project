<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Lolay's Catering Services - Business Profile</title>
<link href="{{ asset('css/nav.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">

    <link href="{{ asset('css/logo.css') }}" rel="stylesheet">
</head>
<body>
<div class="container-fluid p-3">
  <div class="row g-3">

    <!-- SIDEBAR -->
    <div class="col-lg-2">
      <div class="sidebar d-flex flex-column">
        <div class="brand-box">
          <div class="brand-logo">
                  <a href="{{ route('admin.logo') }}"><img src="{{ $profile->logo_url }}" alt=""></a>

<!-- <div class="brand-name">{{ $profile->business_name }}</div> -->
                  
          </div>
          

@php
    $businessName = trim($profile->business_name ?? "Lolay's Catering Services");
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

                   <a href="{{ route('admin.reports') }}" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>

                    <a href="{{ route('admin.settings') }}" class="nav-link-custom active">
                        <i class="bi bi-gear"></i>
                        Settings
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
        <div class="page-heading">
          <div>
             <a href="{{ route('admin.settings') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back </a>
            <h1 class="page-title">Business Profile</h1>
            <p class="page-subtitle">Manage your business information and how it appears to your customers.</p>
          </div>
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
                <span class="d-block" style="font-weight:600;font-size:13px;">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                <span class="d-block" style="font-size:10.5px;color:var(--muted);">Administrator</span>
              </span>
              <i class="bi bi-chevron-down admin-caret"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end profile-menu">
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

      <div class="settings-layout">

        <!-- Business Information -->
        <form class="settings-card" id="businessForm"
      action="{{ route('admin.logo.update') }}" method="POST" enctype="multipart/form-data">
  @csrf
          <div class="settings-card-header">
            <i class="fa-solid fa-store"></i>
            <h2>Business Information</h2>
          </div>

          <div class="settings-grid">

            <!-- Logo column -->
            <div class="logo-column">
              <div class="field-label">Business Logo</div>
              <div class="logo-preview-box">
                <img id="logoPreview" src="{{ $profile->logo_url }}" data-default="{{ asset('image/lolaysLogo.jpg') }}" alt="Business logo">
                <div id="logoEmpty" class="logo-empty d-none">
                  <i class="fa-regular fa-image"></i>
                  <span>No logo uploaded</span>
                </div>
              </div>

              <input type="file" name="logo" id="logoInput" accept=".jpg,.jpeg,.png,.svg,image/jpeg,image/png,image/svg+xml" hidden>
              <div class="logo-actions">
                <button type="button" class="btn-logo btn-logo-change" id="changeLogoBtn"><i class="fa-solid fa-rotate"></i> Change Logo</button>
                <button type="button" class="btn-logo btn-logo-remove" id="removeLogoBtn"><i class="fa-regular fa-trash-can"></i> Remove</button>
              </div>
              <p class="logo-hint">Recommended size: 500x500px.<br>JPG, PNG or SVG. Max size 2MB.</p>
            </div>

            <!-- Fields column -->
            <div class="fields-column">
              <div class="mb-3">
                <label class="field-label" for="bizName">Business Name</label>
                <input type="text" class="form-control" id="bizName" name="business_name"
       value="{{ old('business_name', $profile->business_name) }}" required>
              </div>

              <div class="mb-3">
                <label class="field-label" for="bizDescription">Business Description</label>
                <div class="textarea-wrap">
                  <textarea class="form-control" id="bizDescription" name="description" rows="4" maxlength="500">{{ old('description', $profile->description) }}</textarea>
<span class="char-count" id="descCount">{{ strlen($profile->description ?? '') }}/500</span>
                  <span class="char-count" id="descCount">0/500</span>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-7">
                  <label class="field-label" for="bizEmail">Business Email</label>
                  <div class="input-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" class="form-control" id="bizEmail" name="email"
       value="{{ old('email', $profile->email) }}" required>
                  </div>
                </div>
                <div class="col-md-5">
                  <label class="field-label" for="bizPhone">Contact Number</label>
                  <div class="input-wrap">
                    <i class="fa-solid fa-phone"></i>
                    <input type="tel" class="form-control" id="bizPhone" name="contact_number"
       value="{{ old('contact_number', $profile->contact_number) }}" required>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="field-label" for="bizAddress">Business Address</label>
                <div class="input-wrap">
                  <i class="fa-solid fa-location-dot"></i>
                  <input type="text" class="form-control" id="bizAddress" name="address"
       value="{{ old('address', $profile->address) }}">
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="field-label" for="bizWebsite">Website</label>
                  <div class="input-wrap">
                    <i class="fa-solid fa-link"></i>
                    <input type="text" class="form-control" id="bizWebsite" name="website"
       value="{{ old('website', $profile->website) }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="field-label" for="bizFacebook">Facebook Page</label>
                  <div class="input-wrap">
                    <i class="fa-brands fa-facebook"></i>
                    <input type="text" class="form-control" id="bizFacebook" name="facebook"
       value="{{ old('facebook', $profile->facebook) }}">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="settings-actions">
            <button type="submit" class="btn-save"><i class="fa-regular fa-floppy-disk"></i> Save Changes</button>
          </div>
        </form>

        <!-- Need Help -->
        <aside class="help-card">
          <div class="help-card-header">
            <i class="fa-regular fa-life-ring"></i>
            <h3>Need Help?</h3>
          </div>
          <p>Having trouble or need assistance? Our support team is here to help.</p>
          <button type="button" class="btn-support" id="contactSupportBtn"><i class="fa-regular fa-envelope"></i> Contact Support</button>
        </aside>

      </div>
    </div>
  </div>
</div>

<!-- Contact Support Modal -->
<div class="modal fade" id="supportModal" tabindex="-1" aria-labelledby="supportModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content support-modal">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="supportModalTitle">Contact Support</h5>
          <p class="modal-subtitle">Tell us how we can help and we'll get back to you.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="supportForm">
        <div class="modal-body">
          <div class="mb-3">
            <label class="modal-field-label" for="supportSubject">Subject</label>
            <select class="form-select" id="supportSubject" required>
              <option value="" selected disabled>Select a topic</option>
              <option>General question</option>
              <option>Technical issue</option>
              <option>Billing</option>
              <option>Other</option>
            </select>
          </div>
          <div>
            <label class="modal-field-label" for="supportMessage">Message</label>
            <textarea class="form-control" id="supportMessage" rows="5" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-new-reservation"><i class="fa-regular fa-paper-plane"></i> Send Message</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Remove Logo Confirmation Modal -->
<div class="modal fade" id="removeLogoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content confirm-modal">
      <div class="confirm-modal-body">
        <div class="confirm-icon-circle danger">
          <i class="fa-regular fa-trash-can"></i>
        </div>
        <h4 class="confirm-title">Remove Logo</h4>
        <p class="confirm-subtitle">Are you sure you want to remove your business logo? You can upload a new one anytime. Remember to save your changes.</p>
        <div class="confirm-actions">
          <button type="button" class="btn-confirm-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn-confirm-danger" id="confirmRemoveLogoBtn"><i class="fa-regular fa-trash-can"></i> Remove Logo</button>
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

<!-- Business Profile JavaScript -->
<script src="{{ asset('js/logo.js') }}"></script>
</body>
</html>