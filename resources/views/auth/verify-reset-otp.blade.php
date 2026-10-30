<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter Code</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <style>
    .form-container { text-align: center; }
    .otp-input { text-align: center; font-size: 24px; letter-spacing: 10px; }
    .link-btn { background: none; color: #429646; box-shadow: none; padding: 6px; font-weight: normal; }
    .link-btn:hover { background: none; box-shadow: none; text-decoration: underline; }
    .link-btn:disabled { color: #888; cursor: not-allowed; text-decoration: none; }
  </style>
</head>
<body>
  <div class="form-container">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="Lolay's Catering Services">
    <h2>Enter Code</h2>
    <p class="form-note">We sent a 6-digit code to <strong>{{ $email }}</strong>. It expires in 10 minutes.</p>

    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @error('otp')
      <div class="alert alert-error">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('password.otp.verify') }}">
      @csrf
      <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
             autocomplete="one-time-code" class="otp-input" placeholder="000000" autofocus required>
      <button type="submit">Verify Code</button>
    </form>

    <form method="POST" action="{{ route('password.otp.resend') }}">
      @csrf
      <button type="submit" class="link-btn" id="resendBtn" disabled>Resend code</button>
    </form>

    <p><a href="{{ route('login') }}">Back to Log In</a></p>
  </div>

  <script>
    (function () {
      const btn = document.getElementById('resendBtn');
      let left = 60;
      const tick = () => {
        if (left <= 0) { btn.disabled = false; btn.textContent = 'Resend code'; return; }
        btn.textContent = 'Resend code in ' + left + 's';
        left--;
        setTimeout(tick, 1000);
      };
      tick();
    })();
  </script>
</body>
</html>