<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Add Menu Item</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="{{ asset('css/add-menu.css')}}" rel="stylesheet">
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
<nav class="flex-grow-1">

    <a href="{{ route('admin.dashboard') }}" class="nav-link-custom">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <a href="{{ route('admin.reservation') }}" class="nav-link-custom">
        <i class="bi bi-journal-text"></i> Reservation
    </a>

    <a href="{{ route('admin.calendar') }}" class="nav-link-custom">
        <i class="bi bi-calendar3"></i> Calendar/ Schedule
    </a>

    <a href="{{ route('admin.packages') }}" class="nav-link-custom">
        <i class="bi bi-box-seam"></i> Packages
    </a>

    <a href="{{ route('admin.menu') }}" class="nav-link-custom active">
        <i class="bi bi-egg-fried"></i> Menu
    </a>

    <a href="{{ route('admin.customer') }}" class="nav-link-custom">
        <i class="bi bi-person"></i> Customer
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
      <div class="topbar align-items-start">
        <div>
          <a href="{{ route('admin.menu') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Menu</a>
          <h1 class="page-title mb-1">Add New Menu Item</h1>
          <p class="page-subtitle">Create a new menu item and assign it to a category.</p>
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
          <div class="d-flex align-items-center gap-2">
            <div class="admin-avatar admin-avatar-placeholder"><i class="bi bi-person-fill"></i></div>
            <div>
              <div style="font-weight:600;font-size:13px;">Admin User</div>
              <div style="font-size:10.5px;color:var(--muted);">Administrator</div>
            </div>
          </div>
        </div>
      </div>
    @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif
<form id="menuItemForm" method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data">
  @csrf
        <div class="row g-3">
          <!-- LEFT: form -->
          <div class="col-lg-9">

            <!-- Menu Information -->
<div class="form-card mb-3">
  <div class="form-card-title">Menu Information</div>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label-custom">Menu Item Name <span class="req">*</span></label>
      <input type="text" id="itemName" name="name" class="form-control form-control-custom" placeholder="e.g. Chicken Adobo" value="" required>
    </div>
    <div class="col-md-6">
      <label class="form-label-custom">Description <span class="req">*</span></label>
      <textarea id="itemDescription" name="description" class="form-control form-control-custom" rows="1" placeholder="Describe this menu item..." required></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label-custom">Category <span class="req">*</span></label>
      <select id="itemCategory" name="category" class="form-select form-control-custom" required>
        <<option value="">Select category</option>
    <option value="pasta">Pasta</option>
    <option value="vegetable">Vegetable</option>
    <option value="salad">Salad</option>
    <option value="beef">Beef</option>
    <option value="chicken">Chicken</option>
    <option value="pork">Pork</option>
    <option value="fish">Fish</option>
    <option value="dessert">Dessert</option>
    <option value="juice">Juice</option>
      </select>
    </div>
<div class="col-md-6">
  <label class="form-label-custom">Price</label>
  <div class="price-input-wrap">
    <span class="price-prefix">₱</span>
    <input type="number" id="itemPrice" name="price" class="form-control form-control-custom price-input" placeholder="e.g. 350" value="" min="0" step="0.01">
  </div>
</div>

    <div class="col-md-6">
      <label class="form-label-custom">Status</label>
      <div class="status-toggle-row d-flex align-items-center gap-2">
        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" id="itemStatus" name="is_active" value="1" checked>
        </div>
        <span class="status-toggle-label" id="itemStatusLabel">Active</span>
      </div>
      <div class="form-hint">Inactive package won't be visible to customers.</div>
    </div>
  </div>
</div>

<!-- Menu Image -->
<div class="form-card mb-3">
  <div class="form-card-title">Menu Image</div>
  <div class="form-card-subtitle">Add a photo to showcase this menu item.</div>

  <label class="upload-dropzone" id="uploadDropzone" for="itemImageInput">
    <img id="uploadPreviewImg" class="upload-preview-img d-none" src="" alt="">
    <div class="upload-icon"><i class="bi bi-image"></i></div>
    <div class="upload-text">Click to upload or drag and drop</div>
    <div class="upload-subtext">JPG, PNG or JPEG size 5 MB</div>
  </label>
  <input type="file" id="itemImageInput" name="image" accept="image/png, image/jpeg, image/jpg" class="d-none">
</div>

<!-- Additional Information -->
<div class="form-card mb-3">
  <div class="form-card-title">Additional Information <span class="optional-tag">(Optional)</span></div>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label-custom">Preparation Time</label>
      <input type="text" id="itemPrepTime" name="prep_time" class="form-control form-control-custom" placeholder="e.g. 20 - 30 mins" value="">
    </div>
    <div class="col-md-6">
      <label class="form-label-custom">Spicy Level</label>
      <select id="itemSpicyLevel" name="spicy_level" class="form-select form-control-custom">
        <option value="" selected>None</option>
        <option>Mild</option>
        <option>Medium</option>
        <option>Spicy</option>
        <option>Very Spicy</option>
      </select>
    </div>
  </div>
</div>

            <!-- Actions -->
            <div class="form-actions-card d-flex justify-content-end gap-2">
              <button type="button" class="btn-cancel-custom" id="cancelFormBtn">Cancel</button>
              <button type="submit" class="btn-save-custom"><i class="fa-regular fa-floppy-disk"></i> Save Menu</button>
            </div>

          </div>

          <!-- RIGHT: preview -->
          <div class="col-lg-3 preview-sticky-wrap">
            <div class="preview-card mb-3">
              <div class="form-card-title mb-2">Menu Item Preview</div>

              <div class="preview-img-wrap">
                <img id="previewImg" src="" alt="" class="d-none">
                <div id="previewImgEmpty" class="preview-img-empty"><i class="bi bi-image"></i></div>
              </div>

              <div class="preview-name" id="previewName">Menu Item Name</div>
              <div class="preview-price" id="previewPrice">₱0</div>
              <div class="preview-desc" id="previewDesc">Description will appear here...</div>

              <div class="preview-meta">
                <span><i class="bi bi-tag"></i> <span id="previewCategory">Category</span></span>
                <span><i class="bi bi-clock"></i> <span id="previewPrepTime">None</span></span>
              </div>
            </div>

            <div class="preview-tip-card">
              <div class="preview-tip-ico"><i class="bi bi-lightbulb-fill"></i></div>
              <div>
                <div class="preview-tip-title">Preview</div>
                <div class="preview-tip-text">This is how your menu item will appear to your customer.</div>
              </div>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Discard Changes confirmation modal (shown on Cancel) -->
<div class="cancel-modal-overlay d-none" id="cancelModalOverlay">
  <div class="cancel-modal-card">
    <div class="cancel-modal-ico">
      <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    <div class="cancel-modal-title">Discard Changes?</div>
    <div class="cancel-modal-text">
      Are you sure you want to cancel?<br>
      Any unsaved changes to this menu item will be lost.
    </div>

    <div class="cancel-modal-actions">
      <button type="button" class="btn-cancel-keep" id="cancelModalKeepBtn">Keep Editing</button>
      <button type="button" class="btn-cancel-discard" id="cancelModalDiscardBtn">
        <i class="bi bi-trash3"></i> Discard Changes
      </button>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/add-menu.js') }}"></script>

</body>
</html>