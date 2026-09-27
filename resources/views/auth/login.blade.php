<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Log In Form</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
  <div class="form-container">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="" style="display: block; margin: 0 auto 20px; height: 80px;">
    <h2>Log In</h2>

    @if ($errors->any())
      <div style="color: red; margin-bottom: 10px;">
        @foreach ($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required autofocus>
      <div class="input-group">
        <input type="password" placeholder="Password" name="password" id="password" required>
        <i class="fa-solid fa-eye-slash passicon" id="pass-icon" onclick="togglePassword()"></i>
      </div>

      <label>
        <input type="checkbox" name="remember"> Remember me
      </label>

      <button type="submit">Log In</button>
      <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
    </form>
  </div>
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      const passIcon = document.getElementById("pass-icon");

      const isHidden = passwordInput.type === "password";
      passwordInput.type = isHidden ? "text" : "password";

      passIcon.classList.toggle("fa-eye");
      passIcon.classList.toggle("fa-eye-slash");
    }
  </script>
</body>
</html>