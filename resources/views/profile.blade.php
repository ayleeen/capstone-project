<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Profile Setting</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css') }}" rel="stylesheet">
<link href="{{ asset('css/client/profile.css') }}" rel="stylesheet">
</head>
<body>

@include('client-header')

@php
  $avatar = !empty($user->profile_photo)
      ? asset('storage/' . $user->profile_photo)
      : 'https://ui-avatars.com/api/?background=1f6b34&color=fff&size=200&name=' . urlencode($user->name);
@endphp

<div class="ps-wrap">
  <a href="{{ route('home') }}" class="ps-back"><i class="bi bi-arrow-left"></i> Back</a>
  <h1 class="ps-title">Profile Setting</h1>
  <div class="ps-sub">Manage your account and personal information.</div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="ps-card">
    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PATCH')

      <div class="row g-4">

        {{-- LEFT: Profile picture --}}
        <div class="col-lg-4">
          <div class="ps-photo-card">
            <h6>Profile Picture</h6>
            <small>Upload a profile picture (JPG, PNG, Max 5 MB)</small>

            <img id="photoPreview" src="{{ $avatar }}" alt="Profile photo" class="ps-avatar">

            <div>
              <label for="photoInput" class="btn-ps-outline">
                <i class="bi bi-upload"></i> Change Photo
              </label>
              <input type="file" name="photo" id="photoInput" accept="image/png,image/jpeg" hidden>
            </div>

            @if($user->profile_photo)
              <button type="submit" form="removePhotoForm" class="ps-remove">Remove</button>
            @endif
          </div>
        </div>

        {{-- RIGHT: Fields --}}
        <div class="col-lg-8">
          <div class="ps-section">Basic Information</div>

          <div class="row">
            <div class="col-md-6">
              <div class="ps-label">First Name <span class="req">*</span></div>
              <input type="text" name="first_name" class="ps-input" value="{{ old('first_name', $user->first_name) }}" required>
            </div>
            <div class="col-md-6">
              <div class="ps-label">Last Name <span class="req">*</span></div>
              <input type="text" name="last_name" class="ps-input" value="{{ old('last_name', $user->last_name) }}" required>
            </div>
          </div>

          <div class="ps-label">Email Address</div>
          <input type="email" name="email" class="ps-input" value="{{ old('email', $user->email) }}" required>

          <div class="row">
            <div class="col-md-6">
              <div class="ps-label">Phone Number</div>
              <input type="text" name="contact_number" class="ps-input" value="{{ old('contact_number', $user->contact_number) }}">
            </div>
            <div class="col-md-6">
              <div class="ps-label">Secondary Phone Number</div>
              <input type="text" name="secondary_phone" class="ps-input" value="{{ old('secondary_phone', $user->secondary_phone) }}">
            </div>
          </div>

          <div class="ps-section mt-3">Additional Information</div>
          <div class="ps-hint">Update your personal details (Optional)</div>

          <div class="ps-label">Address Line 1</div>
          <input type="text" name="address_line1" class="ps-input" value="{{ old('address_line1', $user->address_line1) }}">

          <div class="ps-label">Address Line 2</div>
          <input type="text" name="address_line2" class="ps-input" value="{{ old('address_line2', $user->address_line2) }}">

          <div class="text-end mt-3">
            <button type="submit" class="btn-ps-save">Save Changes</button>
          </div>
        </div>

      </div>
    </form>

    {{-- Hiwalay na form para sa Remove (bawal ang nested form) --}}
    <form id="removePhotoForm" method="POST" action="{{ route('profile.photo.remove') }}">
      @csrf
      @method('DELETE')
    </form>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('photoInput').addEventListener('change', function (e) {
    var file = e.target.files[0];
    if (!file) return;
if (file.size > 5 * 1024 * 1024) {
  alert('Max 5 MB lang ang photo.');
  e.target.value = '';
  return;
}
    document.getElementById('photoPreview').src = URL.createObjectURL(file);
  });
</script>
<script src="{{ asset('js/client/homepage.js') }}"></script>
</body>
</html>