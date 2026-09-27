
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Email Verification | Lolay's Catering Services</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #eef9ff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
        }

        .wrapper {
            width: 100%;
            max-width: 950px;
            min-height: 560px;
            display: flex;
            background: #fff;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.10);
        }

        /* LEFT SIDE */
        .left-section {
            width: 42%;
            background: linear-gradient(145deg, #42a9df, #176b9c);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .left-section::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            top: -80px;
            left: -70px;
        }

        .left-section::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            bottom: -140px;
            right: -100px;
        }

        .logo {
            width: 125px;
            height: 125px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.9);
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .left-section h2 {
            font-size: 25px;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }

        .left-section p {
            font-size: 14px;
            line-height: 1.7;
            opacity: 0.9;
            max-width: 270px;
            position: relative;
            z-index: 1;
        }

        /* RIGHT SIDE */
        .right-section {
            width: 58%;
            padding: 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .mail-icon {
            width: 75px;
            height: 75px;
            border-radius: 20px;
            background: #e8f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin-bottom: 22px;
        }

        .right-section h1 {
            color: #1b5274;
            font-size: 32px;
            margin-bottom: 12px;
        }

        .description {
            color: #777;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 25px;
            max-width: 480px;
        }

        .info-box {
            background: #f5fbfe;
            border-left: 4px solid #42a9df;
            padding: 15px 18px;
            border-radius: 8px;
            color: #666;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .success {
            background: #eaf8ef;
            border: 1px solid #c6e8d1;
            color: #277542;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .resend-btn {
            width: 100%;
            max-width: 400px;
            padding: 14px;
            border: none;
            border-radius: 9px;
            background: #2995d0;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .resend-btn:hover {
            background: #1c7eaf;
        }

        .logout-form {
            margin-top: 13px;
        }

        .logout-btn {
            width: 100%;
            max-width: 400px;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 9px;
            background: white;
            color: #666;
            font-size: 14px;
            cursor: pointer;
            transition: 0.2s;
        }

        .logout-btn:hover {
            background: #f6f6f6;
        }

        .footer {
            margin-top: 30px;
            color: #aaa;
            font-size: 12px;
        }

        @media (max-width: 750px) {
            .wrapper {
                flex-direction: column;
            }

            .left-section,
            .right-section {
                width: 100%;
            }

            .left-section {
                padding: 35px 20px;
            }

            .logo {
                width: 90px;
                height: 90px;
            }

            .right-section {
                padding: 35px 25px;
            }

            .right-section h1 {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

<div class="wrapper">

    <!-- LEFT -->
    <div class="left-section">

        <img
            src="{{ asset('image/lolaysLogo.jpg') }}"
            alt="Lolay's Catering Services"
            class="logo"
        >

        <h2>Lolay's Catering Services</h2>

        <p>
            Delicious food, memorable moments,
            and exceptional service for every occasion.
        </p>

    </div>


    <!-- RIGHT -->
    <div class="right-section">

        <div class="mail-icon">
            ✉️
        </div>

        <h1>Verify Your Email</h1>

        <p class="description">
            Thanks for signing up! Before you can continue,
            please verify your email address by clicking the
            verification link we just sent to your inbox.
        </p>

        @if (session('status') == 'verification-link-sent')

            <div class="success">
                ✓ A new verification link has been sent to your email address.
            </div>

        @endif

        <div class="info-box">
            <strong>Didn't receive the email?</strong><br>
            Check your Spam or Junk folder first. If it's still
            missing, click the button below to send a new verification link.
        </div>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="resend-btn">
                Send Verification Email Again
            </button>
        </form>

        <form method="POST"
              action="{{ route('logout') }}"
              class="logout-form">

            @csrf

            <button type="submit" class="logout-btn">
                Log Out
            </button>

        </form>

        <div class="footer">
            © {{ date('Y') }} Lolay's Catering Services
        </div>

    </div>

</div>

</body>
</html>
```
