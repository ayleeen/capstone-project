<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Lolay's Catering Services - Edit Menu</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="{{ asset('css/edit-menu.css') }}" rel="stylesheet">
</head>

<body>

<div class="container-fluid p-3">

<div class="row g-3">

<!-- =====================================================
     SIDEBAR
===================================================== -->

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
Calendar/ Schedule
</a>

<a href="{{ route('admin.packages') }}" class="nav-link-custom">
<i class="bi bi-box-seam"></i>
Packages
</a>

<a href="{{ route('admin.menu') }}" class="nav-link-custom active">
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
        <i class="bi bi-gear"></i> Settings
    </a>

</nav>

<div class="promo-card p-3 text-center mt-auto">

<div class="promo-ico rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
<img src="{{ asset('image/download.png') }}" alt="">
</div>

<h4 class="mb-1">
Delicious Events<br>
Memorable Moments
</h4>

<p class="mb-0">
We make every occasion extra special.
</p>

</div>

<form method="POST" action="{{ route('logout') }}" class="mt-3">

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

<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="col-lg-10">

<!-- TOPBAR -->

<div class="topbar">

<div>

<a href="{{ route('admin.menu') }}" class="back-link">
<i class="bi bi-arrow-left"></i>
Back to Menu
</a>

<h1 class="page-title mb-1">
Edit Menu Item
</h1>

<p class="page-subtitle">
Update the details of your menu item and assign it to a category.
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

<!-- SUCCESS / ERROR -->

@if(session('success'))

<div class="alert alert-success">
{{ session('success') }}
</div>

@endif

@if($errors->any())

<div class="alert alert-danger">

<strong>Please fix the following:</strong>

<ul class="mb-0 mt-2">

@foreach($errors->all() as $error)

<li>{{ $error }}</li>

@endforeach

</ul>

</div>

@endif

<!-- =====================================================
     EDIT FORM
===================================================== -->

<form
id="menuItemForm"
method="POST"
action="{{ route('admin.menu.update', $menuItem) }}"
enctype="multipart/form-data"
>

@csrf

@method('PUT')

<div class="row g-3">

<!-- =================================================
     LEFT COLUMN
================================================= -->

<div class="col-lg-9">

<!-- MENU INFORMATION -->

<div class="form-card mb-3">

<h6 class="form-card-title">
Menu Information
</h6>

<div class="row g-3">

<!-- NAME -->

<div class="col-md-6">

<label class="form-label-custom">
Menu Item Name <span class="req">*</span>
</label>

<input
type="text"
id="itemName"
name="name"
class="form-control form-control-custom"
value="{{ old('name', $menuItem->name) }}"
required

>

</div>

<!-- DESCRIPTION -->

<div class="col-md-6">

<label class="form-label-custom">
Description <span class="req">*</span>
</label>

<textarea
id="itemDescription"
name="description"
class="form-control form-control-custom"
rows="1"
style="min-height:78px;"
required
>{{ old('description', $menuItem->description) }}</textarea>

</div>

<div class="col-md-6">
      <label class="form-label-custom">Category <span class="req">*</span></label>
      <select id="itemCategory" name="category" class="form-select form-control-custom" required>
        
<option value="">Select category</option>
<option value="pasta" @selected(old('category', $menuItem->category) === 'pasta')>Pasta</option>
<option value="vegetable" @selected(old('category', $menuItem->category) === 'vegetable')>Vegetable</option>
<option value="salad" @selected(old('category', $menuItem->category) === 'salad')>Salad</option>
<option value="beef" @selected(old('category', $menuItem->category) === 'beef')>Beef</option>
<option value="chicken" @selected(old('category', $menuItem->category) === 'chicken')>Chicken</option>
<option value="pork" @selected(old('category', $menuItem->category) === 'pork')>Pork</option>
<option value="fish" @selected(old('category', $menuItem->category) === 'fish')>Fish</option>
<option value="dessert" @selected(old('category', $menuItem->category) === 'dessert')>Dessert</option>
<option value="juice" @selected(old('category', $menuItem->category) === 'juice')>Juice</option>

      </select>
    </div>

<!-- STATUS -->

<div class="col-md-6">

<label class="form-label-custom d-block">
Status
</label>

<div class="d-flex align-items-center gap-2 status-toggle-row">

<div class="form-check form-switch mb-0">

<input
class="form-check-input"
type="checkbox"
role="switch"
id="itemStatus"
name="is_active"
value="1"
{{ old('is_active', $menuItem->is_active) ? 'checked' : '' }}

>

</div>

<span
class="status-toggle-label"
id="itemStatusLabel"

>

{{ $menuItem->is_active ? 'Active' : 'Inactive' }} </span>

</div>

<div class="form-hint">
Inactive menu items won't be visible to customers.
</div>

</div>

<!-- PRICE -->

<div class="col-md-6">

<label class="form-label-custom">
Price <span class="req">*</span>
</label>

<!-- ADD-ON SETTINGS -->
<div class="col-12">
    <hr>

    <div class="form-check form-switch">
        <input
            class="form-check-input"
            type="checkbox"
            id="isAddon"
            name="is_addon"
            value="1"
            @checked(old('is_addon', $menuItem->is_addon))
        >

        <label class="form-check-label fw-semibold" for="isAddon">
            This is an Add-on
        </label>
    </div>

    <small class="text-muted">
        Enable this if the dish will be offered as an additional item.
    </small>
</div>

<!-- ADD-ON PRICING -->
<div
    class="col-12 {{ old('is_addon', $menuItem->is_addon) ? '' : 'd-none' }}"
    id="addonPricingSection"
>
    <div class="p-3 rounded border bg-light">

        <h6 class="fw-semibold mb-3">Add-on Pricing</h6>

        <p class="text-muted small">
            Set the individual price for each serving tier.
        </p>

        <div class="row g-3">

            @foreach([30, 40, 50] as $servings)
                @php
                    $savedPrice = $menuItem->addonPrices
                        ->firstWhere('servings', $servings)?->price;
                @endphp

                <div class="col-md-4">
                    <label class="form-label-custom">
                        {{ $servings }} Pax Price <span class="req">*</span>
                    </label>

                    <div class="price-input-wrap">
                        <span class="price-prefix">₱</span>

                        <input
                            type="number"
                            name="addon_prices[{{ $servings }}]"
                            class="form-control form-control-custom price-input addon-price-input"
                            placeholder="e.g. 1500"
                            min="0"
                            step="0.01"
                            value="{{ old('addon_prices.' . $servings, $savedPrice) }}"
                        >
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>

<div class="price-input-wrap">

<span class="price-prefix">
₱
</span>

<input
type="number"
id="itemPrice"
name="price"
class="form-control form-control-custom price-input"
value="{{ old('price', $menuItem->price) }}"
min="0"
step="0.01"


>

</div>

</div>

</div>

</div>

<!-- =================================================
     MENU IMAGE
================================================= -->

<div class="form-card mb-3">

<h6 class="form-card-title">
Menu Image
</h6>

<p class="form-card-subtitle">
Upload a new photo if you want to replace the current image.
</p>

<label
for="itemImageInput"
class="upload-dropzone"
id="uploadDropzone"

>

<div class="upload-icon">
<i class="bi bi-image"></i>
</div>

<div class="upload-text">
Click to upload or drag and drop.
</div>

<div class="upload-subtext">
JPG, PNG or JPEG size 5 MB
</div>

@if($menuItem->image_path)

<img
id="uploadPreviewImg"
class="upload-preview-img"
src="{{ asset('storage/' . $menuItem->image_path) }}"
alt="{{ $menuItem->name }}"

>

@else

<img
id="uploadPreviewImg"
class="upload-preview-img d-none"
alt="Preview"

>

@endif

</label>

<input
type="file"
id="itemImageInput"
name="image"
accept=".jpg,.jpeg,.png"
class="d-none"

>

</div>

<!-- =================================================
     ADDITIONAL INFORMATION
================================================= -->

<div class="form-card">

<h6 class="form-card-title">

Additional Information

<span class="optional-tag">
(Optional)
</span>

</h6>

<div class="row g-3">

<!-- PREP TIME -->

<div class="col-md-6">

<label class="form-label-custom">
Preparation Time
</label>

<input
type="text"
id="itemPrepTime"
name="prep_time"
class="form-control form-control-custom"
placeholder="e.g. 20 - 30 mins"
value="{{ old('prep_time', $menuItem->prep_time) }}"

>

</div>

<!-- SPICY LEVEL -->

<div class="col-md-6">

<label class="form-label-custom">
Spicy Level
</label>

<select
id="itemSpicyLevel"
name="spicy_level"
class="form-select form-control-custom"

>

<option value="">
None
</option>

<option value="Mild"
@selected(old('spicy_level', $menuItem->spicy_level) === 'Mild')
>
Mild
</option>

<option value="Medium"
@selected(old('spicy_level', $menuItem->spicy_level) === 'Medium')
>
Medium
</option>

<option value="Spicy"
@selected(old('spicy_level', $menuItem->spicy_level) === 'Spicy')
>
Spicy
</option>

<option value="Very Spicy"
@selected(old('spicy_level', $menuItem->spicy_level) === 'Very Spicy')
>
Very Spicy
</option>

</select>

</div>

</div>

</div>

<!-- =================================================
     ACTIONS
================================================= -->

<div class="form-actions-card mt-3 d-flex justify-content-end gap-2">

<a
href="{{ route('admin.menu') }}"
class="btn btn-cancel-custom"

>

Cancel </a>

<button
type="submit"
class="btn btn-save-custom"

>

<i class="bi bi-check2-circle"></i>

Save Changes

</button>

</div>

</div>

<!-- =================================================
     RIGHT COLUMN PREVIEW
================================================= -->

<div class="col-lg-3 preview-sticky-wrap">

<div class="preview-card">

<h6 class="form-card-title">
Menu Item Preview
</h6>

<div class="preview-img-wrap" id="previewImgWrap">

@if($menuItem->image_path)

<img
id="previewImg"
src="{{ asset('storage/' . $menuItem->image_path) }}"
alt="{{ $menuItem->name }}"

>

@else

<div
class="preview-img-empty"
id="previewImgEmpty"
>
<i class="bi bi-image"></i>
</div>

<img
id="previewImg"
src=""
alt="Preview"
class="d-none"

>

@endif

</div>

<div
class="preview-name"
id="previewName"
>
{{ $menuItem->name }}
</div>

<div
class="preview-price"
id="previewPrice"
>
₱{{ number_format($menuItem->price, 2) }}
</div>

<div
class="preview-desc"
id="previewDesc"
>
{{ $menuItem->description }}
</div>

<div class="preview-meta">

<span>

<i class="bi bi-tag"></i>

<span id="previewCategory">
{{ $menuItem->category }}
</span>

</span>

<span>

<i class="bi bi-clock"></i>

<span id="previewPrepTime">
{{ $menuItem->prep_time ?: '—' }}
</span>

</span>

</div>

</div>

<div class="preview-tip-card mt-3">

<div class="preview-tip-ico">
<i class="bi bi-lightbulb"></i>
</div>

<div>

<div class="preview-tip-title">
Preview
</div>

<div class="preview-tip-text">
This is how your menu item will appear to your customer.
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

<script src="{{ asset('js/edit-menu.js') }}"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkbox = document.getElementById('isAddon');
    const section = document.getElementById('addonPricingSection');
    const inputs = section.querySelectorAll('input');

    function toggleAddonPricing() {
        const enabled = checkbox.checked;

        section.classList.toggle('d-none', !enabled);

        inputs.forEach(input => {
            input.disabled = !enabled;
            input.required = enabled;
        });
    }

    checkbox.addEventListener('change', toggleAddonPricing);

    toggleAddonPricing();

});
</script>


</body>
</html>
