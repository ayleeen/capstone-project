<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registration Form</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function validateForm() {
        const form = document.forms["registerForm"];
        const password = form["password"].value;
        const confirmPassword = form["password_confirmation"].value;

        if (password.length < 8) {
            Swal.fire({
                icon: "error",
                title: "Weak Password",
                text: "Password must be at least 8 characters long."
            });
            return false;
        }

        if (password !== confirmPassword) {
            Swal.fire({
                icon: "error",
                title: "Password Mismatch",
                text: "Passwords do not match."
            });
            return false;
        }

        return true;
    }
  </script>
</head>
<body>
  <div class="form-container">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="" style="display: block; margin: 0 auto 20px; height: 60px;">
    <h2>Registration</h2>

    @if ($errors->any())
      <div style="color: red; margin-bottom: 10px;">
        @foreach ($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <form name="registerForm" method="POST" action="{{ route('register') }}" onsubmit="return validateForm()">
      @csrf
      <input type="text" placeholder="First Name" name="first_name" value="{{ old('first_name') }}" required>
      <input type="text" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}" required>
      <input type="text" placeholder="Contact Number" name="contact_number" value="{{ old('contact_number') }}" required>
      <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required>
      <div class="input-group">
        <input type="password" placeholder="Password" name="password" id="password" required>
        <i class="fa-solid fa-eye-slash passicon" id="pass-icon" onclick="togglePassword()"></i>
      </div>

      <div class="input-group">
        <input type="password" placeholder="Confirm Password" name="password_confirmation" id="confirmPassword" required>
        <i class="fa-solid fa-eye-slash passicon" id="conpass-icon" onclick="toggleConfirmPassword()"></i>
      </div>

      <button type="submit">Register</button>
      <p>Already have an account? <a href="{{ route('login') }}">Log In</a></p>
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

    function toggleConfirmPassword() {
      const confirmPasswordInput = document.getElementById("confirmPassword");
      const confirmPassIcon = document.getElementById("conpass-icon");
      const isHidden = confirmPasswordInput.type === "password";
      confirmPasswordInput.type = isHidden ? "text" : "password";
      confirmPassIcon.classList.toggle("fa-eye");
      confirmPassIcon.classList.toggle("fa-eye-slash");
    }
  </script>
</body>
</html>