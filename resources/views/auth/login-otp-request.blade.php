<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Log In with Email Code</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
  <div class="form-container">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="" style="display: block; margin: 0 auto 20px; height: 80px;">
    <h2>Log In with Email Code</h2>

    @if ($errors->any())
      <div style="color: red; margin-bottom: 10px;">
        @foreach ($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.otp.send') }}">
      @csrf
      <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required autofocus>
      <button type="submit">Send Code</button>
      <p><a href="{{ route('login') }}">Back to Log In</a></p>
    </form>
  </div>
</body>
</html>