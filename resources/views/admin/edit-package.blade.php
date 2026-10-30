<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Package - {{ $package->package_name }}</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="{{ asset('css/edit-package.css') }}" rel="stylesheet">
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

                    <a href="{{ route('admin.reservation') }}" class="nav-link-custom">
                        <i class="bi bi-journal-text"></i>
                        Reservation
                    </a>

                    <a href="{{ route('admin.calendar') }}" class="nav-link-custom">
                        <i class="bi bi-calendar3"></i>
                        Calendar / Schedule
                    </a>

                    <a href="{{ route('admin.packages') }}" class="nav-link-custom active">
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


                <!-- Promo -->
                <div class="promo-card p-3 text-center mt-auto">

                    <div class="promo-ico rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">

                        <img
                            src="{{ asset('image/download.png') }}"
                            alt=""
                        >

                    </div>

                    <h4 class="mb-1">
                        Delicious Events<br>
                        Memorable Moments
                    </h4>

                    <p class="mb-0">
                        We make every occasion extra special.
                    </p>

                </div>


                <!-- Logout -->
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="mt-3"
                >
                    @csrf

                    <button
                        type="submit"
                        class="logout-link d-flex align-items-center justify-content-center gap-2 py-2 w-100 border-0 bg-transparent"
                    >
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                </form>

            </div>

        </div>


        <!-- MAIN CONTENT -->
        <div class="col-lg-10">

            <!-- TOPBAR -->
            <div class="topbar">

                <div>

                    <a
                        href="{{ route('admin.packages') }}"
                        class="back-link"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Back to Packages
                    </a>

                    <h1 class="page-title mb-1">
                        Edit Package
                    </h1>

                    <p class="page-subtitle">
                        Update package details and settings.
                    </p>

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

                        <img
                            src="https://i.pravatar.cc/100?img=12"
                            class="admin-avatar"
                            alt="Admin"
                        >

                        <div>

                            <div style="font-weight:600;font-size:13px;">
                                {{ auth()->user()->first_name }}
                                {{ auth()->user()->last_name }}
                            </div>

                            <div style="font-size:10.5px;color:var(--muted);">
                                Administrator
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- EDIT FORM -->
            <form
                id="packageForm"
                method="POST"
                action="{{ route('admin.packages.update', $package) }}"
                enctype="multipart/form-data"
            >

                @csrf
                @method('PUT')


                <div class="row g-3">

                    <!-- LEFT COLUMN -->
                    <div class="col-lg-9">

                        <!-- PACKAGE INFORMATION -->
                        <div class="panel">

                            <div class="panel-heading">
                                Package Information
                            </div>

                            <div class="row g-3 mb-1">

                                <!-- Package Name -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Package Name <span class="req">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="package_name"
                                        id="pkgName"
                                        class="form-control"
                                        value="{{ old('package_name', $package->package_name) }}"
                                        required
                                    >

                                </div>


                                <!-- Price -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Price <span class="req">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        name="price"
                                        id="pkgPrice"
                                        class="form-control"
                                        value="{{ old('price', $package->price) }}"
                                        min="0"
                                        step="0.01"
                                        required
                                    >

                                </div>


                                <!-- Category -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Category <span class="req">*</span>
                                    </label>

                                    <select
                                        class="form-select"
                                        name="category"
                                        id="pkgCategory"
                                        required
                                    >

                                        <option value="" disabled>
                                            Select category
                                        </option>

                                        <option
                                            value="Wedding"
                                            {{ old('category', $package->category) == 'Wedding' ? 'selected' : '' }}
                                        >
                                            Wedding
                                        </option>

                                        <option
                                            value="Birthday"
                                            {{ old('category', $package->category) == 'Birthday' ? 'selected' : '' }}
                                        >
                                            Birthday
                                        </option>

                                        <option
                                            value="Corporate"
                                            {{ old('category', $package->category) == 'Corporate' ? 'selected' : '' }}
                                        >
                                            Corporate
                                        </option>

                                        <option
                                            value="Garden Party"
                                            {{ old('category', $package->category) == 'Garden Party' ? 'selected' : '' }}
                                        >
                                            Garden Party
                                        </option>

                                    </select>

                                </div>


                                <!-- Guest Range -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Guest Range <span class="req">*</span>
                                    </label>

                                    <div class="guest-range-group">

                                        <input
                                            type="number"
                                            name="guest_min"
                                            class="form-control"
                                            id="pkgGuestMin"
                                            placeholder="Min"
                                            value="{{ old('guest_min', $package->guest_min) }}"
                                            min="1"
                                            required
                                        >

                                        <input
                                            type="number"
                                            name="guest_max"
                                            class="form-control"
                                            id="pkgGuestMax"
                                            placeholder="Max"
                                            value="{{ old('guest_max', $package->guest_max) }}"
                                            min="1"
                                            required
                                        >

                                        <span class="badge-guests">
                                            Guests
                                        </span>

                                    </div>

                                </div>


                                <!-- Description -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Description <span class="req">*</span>
                                    </label>

                                    <textarea
                                        class="form-control"
                                        name="description"
                                        id="pkgDescription"
                                        rows="3"
                                        placeholder="Describe this package..."
                                        required
                                    >{{ old('description', $package->description) }}</textarea>

                                </div>


                                <!-- Status -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <div class="status-row">

                                        <div class="form-check form-switch mb-0">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="status"
                                                value="1"
                                                id="statusSwitch"
                                                {{ old('status', $package->status) ? 'checked' : '' }}
                                            >

                                        </div>

                                        <span
                                            class="status-label"
                                            id="statusText"
                                        >
                                            {{ old('status', $package->status) ? 'Active' : 'Inactive' }}
                                        </span>

                                    </div>

                                    <div class="status-hint">
                                        Inactive package won't be visible to customers.
                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- MENU / INCLUSIONS -->
                        <div class="panel">

                            <div class="panel-heading">
                                Menu / Inclusions
                            </div>

                            <div class="panel-sub">
                                Add all menu items and inclusions included in this package.
                            </div>

                            <div class="table-responsive">

                                <table class="table menu-table mb-2">

                                  
<thead>
    <tr>
        <th style="width:85%;">Inclusion</th>
        <th style="width:15%;">Action</th>
    </tr>
</thead>


                                    <tbody id="menuRows">
                                        <!-- rows injected by JavaScript -->
                                    </tbody>

                                </table>

                            </div>

                            <button
                                type="button"
                                class="btn btn-add-item"
                                id="addMenuItemBtn"
                            >
                                <i class="fa-solid fa-plus me-1"></i>
                                Add Inclusion
                            </button>

                        </div>


                        <!-- FOOTER ACTIONS -->
                        <div class="footer-actions">

                            <a
                                href="{{ route('admin.packages') }}"
                                class="btn btn-cancel"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn btn-save"
                            >
                                <i class="fa-regular fa-floppy-disk me-1"></i>
                                Save Changes
                            </button>

                        </div>

                    </div>


                    <!-- RIGHT COLUMN -->
                    <div class="col-lg-3 right-col-sticky">

                        <!-- PACKAGE PHOTO -->
                        <div class="panel-white">

                            <div class="panel-heading">
                                Package Photo
                            </div>

                            <div
                                class="panel-sub"
                                style="margin-bottom:12px;"
                            >
                                Add a photo to showcase this package.
                            </div>


                            @if($package->photo)

                                <img
                                    id="photoPreview"
                                    src="{{ asset('storage/' . $package->photo) }}"
                                    class="photo-preview"
                                    alt="{{ $package->package_name }}"
                                >

                            @else

                                <div
                                    id="photoPlaceholder"
                                    class="photo-placeholder"
                                >
                                    <i class="fa-regular fa-image"></i>

                                    <span>
                                        No photo yet
                                    </span>
                                </div>

                            @endif


                            <input
                                type="file"
                                name="photo"
                                id="photoInput"
                                accept="image/png, image/jpeg, image/svg+xml"
                                class="d-none"
                            >


                            <button
                                type="button"
                                class="upload-btn"
                                id="uploadPhotoBtn"
                            >
                                <i class="fa-solid fa-upload me-1"></i>
                                Change Photo
                            </button>

                            <div class="filetype-hint">
                                JPEG/PNG or SVG. Max size 5MB
                            </div>

                        </div>


                        <!-- PACKAGE PREVIEW -->
                        <div class="preview-card mt-3">

                            <div class="preview-title">
                                Package Preview
                            </div>


                            <div
                                class="preview-package-name"
                                id="previewName"
                            >
                                {{ $package->package_name }}
                            </div>


                            <div class="preview-stars">

                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>

                                <span class="preview-reviews">
                                    (0 Reviews)
                                </span>

                            </div>


                            <div
                                class="preview-price"
                                id="previewPrice"
                            >
                                ₱{{ number_format($package->price, 2) }}
                            </div>


                            <div
                                class="preview-meta"
                                id="previewMeta"
                            >
                                {{ $package->guest_min }} -
                                {{ $package->guest_max }}
                                Guests
                            </div>


                            <div
                                class="preview-desc"
                                id="previewDesc"
                            >
                                {{ $package->description }}
                            </div>


                            <div class="inclusions-title">
                                Inclusions
                            </div>


                            <div id="inclusionsList">

                                <div
                                    class="inclusion-empty"
                                    id="inclusionsEmpty"
                                >
                                    No menu items added yet.
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script>
    window.EXISTING_INCLUSIONS = @json($package->inclusions ?? []);
    window.OLD_INCLUSIONS = @json(old('inclusions', []));
</script>


<script src="{{ asset('js/edit-package.js') }}"></script>

</body>
</html>