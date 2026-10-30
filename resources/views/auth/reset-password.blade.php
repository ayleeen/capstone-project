<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
  <div class="form-container">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="Lolay's Catering Services">
    <h2>Reset Password</h2>

    @if ($errors->any())
      <div class="alert alert-error">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $request->route('token') }}">

      <input type="email" name="email" placeholder="Email"
             value="{{ old('email', $request->email) }}" autocomplete="username" required>

      <div class="input-group">
        <input type="password" name="password" id="password" placeholder="New Password"
               autocomplete="new-password" required autofocus>
        <i class="fa-solid fa-eye-slash passicon" onclick="toggle('password', this)"></i>
      </div>

      <div class="input-group">
        <input type="password" name="password_confirmation" id="password_confirmation"
               placeholder="Confirm New Password" autocomplete="new-password" required>
        <i class="fa-solid fa-eye-slash passicon" onclick="toggle('password_confirmation', this)"></i>
      </div>

      <p class="form-note">At least 8 characters.</p>

      <button type="submit">Reset Password</button>
    </form>
  </div>

  <script>
    function toggle(id, icon) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
      icon.classList.toggle('fa-eye');
      icon.classList.toggle('fa-eye-slash');
    }
  </script>
</body>
</html>