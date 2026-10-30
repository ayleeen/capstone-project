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

<form method="POST" action="{{ route('login') }}">
  @csrf
  <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required autofocus>
  <div class="input-group">
    <input type="password" placeholder="Password" name="password" id="password" required>
    <i class="fa-solid fa-eye-slash passicon" id="pass-icon" onclick="togglePassword()"></i>
  </div>

  <div class="form-options">
    <label class="remember-me">
      <input type="checkbox" name="remember"> Remember me
    </label>
    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
  </div>

  <button type="submit">Log In</button>

  <div class="or-divider"><span>or</span></div>

  <button type="button" id="show-phone-login" class="btn-outline">
    <i class="fa-solid fa-mobile-screen"></i> Log in with phone number
  </button>

  <div id="phone-login" style="display:none;">
    <p id="phone-msg" class="form-note"></p>

    <div id="phone-step-1">
      <input type="tel" id="phone-input" placeholder="Mobile number (09XXXXXXXXX)" maxlength="15">
      <button type="button" id="send-code-btn">Send 4-digit code</button>
    </div>

    <div id="phone-step-2" style="display:none;">
      <p class="form-note">Enter the 4-digit code we sent to your phone.</p>
      <input type="text" id="code-input" class="code-input" placeholder="----" maxlength="4" inputmode="numeric" autocomplete="one-time-code">
      <button type="button" id="verify-code-btn">Verify &amp; Log In</button>
      <p class="form-note">
        <a href="#" id="resend-link">Resend code</a> ·
        <a href="#" id="change-number-link">Change number</a>
      </p>
    </div>
  </div>

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

        const csrf = "{{ csrf_token() }}";
    const phoneMsg = document.getElementById('phone-msg');
    const sendBtn = document.getElementById('send-code-btn');
    let cooldown = null;

    document.getElementById('show-phone-login').onclick = () => {
      const box = document.getElementById('phone-login');
      box.style.display = box.style.display === 'none' ? 'block' : 'none';
    };

    function showMsg(text, ok = false) {
      phoneMsg.textContent = text;
      phoneMsg.style.color = ok ? '#1b5e20' : '#b71c1c';
    }

    async function post(url, data) {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify(data)
      });
      const json = await res.json().catch(() => ({}));
      return { ok: res.ok, json };
    }

    async function sendCode() {
      const phone = document.getElementById('phone-input').value.trim();
      if (!phone) return showMsg('Please enter your mobile number.');

      sendBtn.disabled = true;
      const { ok, json } = await post("{{ route('login.phone.send') }}", { phone });
      sendBtn.disabled = false;

      if (!ok) return showMsg(json.message || 'Something went wrong.');

      showMsg('Code sent!', true);
      document.getElementById('phone-step-1').style.display = 'none';
      document.getElementById('phone-step-2').style.display = 'block';
      document.getElementById('code-input').focus();
      startCooldown();
    }

    function startCooldown() {
      const link = document.getElementById('resend-link');
      let left = 60;
      link.style.pointerEvents = 'none';
      link.style.opacity = '0.5';
      clearInterval(cooldown);
      cooldown = setInterval(() => {
        left--;
        link.textContent = left > 0 ? `Resend code (${left}s)` : 'Resend code';
        if (left <= 0) {
          clearInterval(cooldown);
          link.style.pointerEvents = 'auto';
          link.style.opacity = '1';
        }
      }, 1000);
    }

    sendBtn.onclick = sendCode;
    document.getElementById('resend-link').onclick = (e) => { e.preventDefault(); sendCode(); };

    document.getElementById('change-number-link').onclick = (e) => {
      e.preventDefault();
      document.getElementById('phone-step-2').style.display = 'none';
      document.getElementById('phone-step-1').style.display = 'block';
      document.getElementById('code-input').value = '';
      showMsg('');
    };

    document.getElementById('verify-code-btn').onclick = async () => {
      const phone = document.getElementById('phone-input').value.trim();
      const code = document.getElementById('code-input').value.trim();
      if (code.length !== 4) return showMsg('Please enter the 4-digit code.');

      const { ok, json } = await post("{{ route('login.phone.verify') }}", { phone, code });

      if (!ok) return showMsg(json.message || 'Invalid code.');
      window.location.href = json.redirect;
    };
  </script>
</body>
</html>