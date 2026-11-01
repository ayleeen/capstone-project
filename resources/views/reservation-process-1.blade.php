<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reservation</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/client/homepage.css') }}" rel="stylesheet">
<link href="{{ asset('css/client/reservation-process-1.css') }}" rel="stylesheet">
</head>
<body>

<header class="site">
  <div class="brand">
    <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="Lolay's logo" class="logo-img">
    <div class="name">Lolay's<small>FOOD STATION &amp;<br>CATERING SERVICES</small></div>
  </div>
  <nav class="main-nav" id="siteNavLinks">
    <a href="{{ route('home') }}">Home</a>
    <a href="{{ route('about-us') }}">About Us</a>
    <a href="{{ route('package') }}">Packages</a>
    <a href="{{ route('menu') }}">Menu</a>
    <a href="{{ route('gallery') }}">Gallery</a>
    <a href="{{ route('contact') }}">Contact</a>
  </nav>
  <div class="header-actions">
    <a href="{{ route('reservation.process1') }}" class="btn-reserve" id="reserveNowNavBtn">
      <i class="fa-regular fa-calendar-check"></i> Reserve Now
    </a>

    @guest
      <a href="{{ route('login') }}" class="btn-outline-nav">
        <i class="bi bi-person"></i> Login/Register
      </a>
    @endguest

@auth
  <form method="POST" action="{{ route('logout') }}" style="display: inline;"
        onsubmit="sessionStorage.clear()">
    @csrf
    <button type="submit" class="btn-outline-nav">
      <i class="bi bi-box-arrow-right"></i> Logout
    </button>
  </form>
@endauth
  </div>
</header>

<section class="hero">
  <h1>Great Events Start with Great Planning.</h1>
  <span class="hero-highlight">Reserve Now!</span>
  <p>Check Availability and secure your date in just a few steps.</p>
</section>

<div class="container" style="max-width:1080px;">

<div class="steps-wrap">
  <div class="step done">
    <div class="circle">1</div>
    <div class="step-label">Step 1<br>Reservation</div>
  </div>
  <div class="step">
    <div class="circle">2</div>
    <div class="step-label">Step 2<br>Event Details</div>
  </div>
  <div class="step">
    <div class="circle">3</div>
    <div class="step-label">Step 3<br>Menu Selection</div>
  </div>
  <div class="step">
    <div class="circle">4</div>
    <div class="step-label">Step 4<br>Confirmation</div>
  </div>
   <div class="step">
    <div class="circle">5</div>
    <div class="step-label">Step 5<br>Payment</div>
  </div>
</div>

<div class="content">
  <!-- Sidebar -->
  <div>
    <div class="card">
      <h3>Check Date Availability</h3>
      <div class="sub">Select your preferred date</div>

      <div class="legend-item"><span class="dot available"></span> Available</div>
      <div class="legend-item"><span class="dot limited"></span> Limited Availability</div>
      <div class="legend-item"><span class="dot unavailable"></span> Unavailable</div>

      <div class="tip">
        <span>&#128205;</span>
        <div>
          <strong>Planning ahead?</strong>
          We recommend to book a week or 3 days on your preferred date.
        </div>
      </div>
    </div>

    <div class="card need-help" style="margin-top:18px;">
      <h3>Need Help?</h3>
      <p>Our team is here to assist you with event planning.</p>
      <div class="contact-row"><span class="ico">&#128222;</span>09876543234</div>
      <div class="contact-row"><span class="ico">&#9993;</span>
lolaycatering@gmail.com</div>
    </div>
  </div>

  <!-- Calendar -->
  <div class="card calendar-card">
    <div class="cal-header">
      @if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
      <h3 id="calMonthLabel"></h3>
      <div class="cal-nav">
        @if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
        <a href="#" id="calPrev">&#8249;</a>
        @if ($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
        <a href="#" id="calNext">&#8250;</a>
      </div>
    </div>

    <table class="calendar">
      <thead>
        <tr id="calWeekdayRow"></tr>
      </thead>
      <tbody id="calBody"></tbody>
    </table>

    <div id="calSelectionAlert" class="alert mt-3 mb-0 d-none" style="font-size:13px;"></div>
  </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

     <form id="modalForm" method="POST" action="{{ route('reservation.process1.store') }}">
  @csrf
        <input type="hidden" name="selected_date" id="modalSelectedDate" value="">

        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="modal-title-date" id="modalDateTitle">June 23, 2026</div>
          <span class="status-badge" id="modalStatusBadge">Limited Availability</span>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="panel">
                <h6>Existing Reservations</h6>
                <div id="modalReservationsList"></div>
                <div class="unavailable-note d-none" id="modalUnavailableNote">
                  <strong>Note:</strong>
                  The following time frames are unavailable for booking. Kindly select a time slot that is not listed above. Thank you.
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="panel">
                <h6>Specify the Event Time</h6>

                <div class="mb-2">
                  <label class="form-label mb-1">Start Time:</label>
                  <input type="time" class="form-control" name="start_time" id="modalStartTime" required>
                </div>
<div class="mb-3">
    <label class="form-label mb-1">End Time (Automatic - 4 Hours):</label>
    <input type="time"
           class="form-control"
           name="end_time"
           id="modalEndTime"
           readonly
           required>
</div>

                <div class="pax-label">Available Pax <span class="small">(Number of Guest)</span></div>
                <div class="pax-counter">
                  <button type="button" class="pax-btn" id="paxMinus">&minus;</button>
                  <span class="pax-value" id="paxValue">150</span>
                  <button type="button" class="pax-btn" id="paxPlus">&plus;</button>
                  <input type="hidden" name="pax" id="paxInput" value="150">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Back</button>
          <button type="submit" class="btn btn-continue text-white" id="modalContinueBtn">Continue to Event Details</button>
        </div>
      </form>

    </div>
  </div>
</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
  window.bookedGuests  = @json($bookedGuests);
  window.dailyCapacity = @json($dailyCapacity);
</script>
<script src="{{ asset('js/client/reservation-process-1.js') }}"></script>

</body>
</html>