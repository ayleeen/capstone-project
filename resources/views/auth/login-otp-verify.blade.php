<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enter Login Code</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
  <div class="form-container">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="" style="display: block; margin: 0 auto 20px; height: 80px;">
    <h2>Enter Code</h2>
    <p>We sent a 6-digit code to {{ $email }}.</p>

    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div style="color: red; margin-bottom: 10px;">
        @foreach ($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.otp.check') }}">
      @csrf
      <input type="text" name="otp" placeholder="6-digit code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required autofocus>
      <button type="submit">Log In</button>
    </form>

    <form method="POST" action="{{ route('login.otp.resend') }}">
      @csrf
      <p><button type="submit" style="background:none;border:0;color:inherit;text-decoration:underline;cursor:pointer;">Resend code</button></p>
    </form>

    <p><a href="{{ route('login') }}">Back to Log In</a></p>
  </div>
</body>
</html>