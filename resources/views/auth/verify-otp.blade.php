<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Email</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
  <div class="card p-4 text-center" style="max-width:380px;width:100%;">
    <h4>Verify your email</h4>
    <p class="text-muted">We sent a 4-digit code to <strong>{{ auth()->user()->email }}</strong>.</p>

    @if (session('status'))
      <div class="alert alert-success py-2">{{ session('status') }}</div>
    @endif
    @error('otp')
      <div class="alert alert-danger py-2">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('verification.otp') }}">
      @csrf
      <input type="text" name="otp" maxlength="4" inputmode="numeric" pattern="[0-9]{4}"
             class="form-control form-control-lg text-center mb-3" style="letter-spacing:12px;"
             placeholder="0000" autofocus required>
      <button type="submit" class="btn btn-success w-100">Verify</button>
    </form>

    <form method="POST" action="{{ route('verification.resend') }}" class="mt-3">
      @csrf
      <button type="submit" class="btn btn-link">Resend code</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" onsubmit="sessionStorage.clear()">
      @csrf
      <button type="submit" class="btn btn-link text-muted">Log out</button>
    </form>
  </div>
</body>
</html>