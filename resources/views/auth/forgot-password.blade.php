<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
  <div class="form-container">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="Lolay's Catering Services">
    <h2>Forgot Password</h2>
    <p class="form-note">Enter your email and we'll send you a 6-digit code.</p>

    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @error('email')
      <div class="alert alert-error">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
             autocomplete="email" required autofocus>
      <button type="submit">Send Code</button>
      <p><a href="{{ route('login') }}">Back to Log In</a></p>
    </form>
  </div>
</body>
</html>