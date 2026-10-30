// ============================================
// Lolay's Catering — Contact Us page behavior
// ============================================
document.addEventListener('DOMContentLoaded', () => {

  const mapEl = document.getElementById('contactMap');
  const zoomInBtn = document.getElementById('mapZoomIn');
  const zoomOutBtn = document.getElementById('mapZoomOut');

  let zoomLevel = 1;
  const MIN_ZOOM = 1;
  const MAX_ZOOM = 1.6;

  function applyZoom() {
    if (mapEl) {
      mapEl.style.backgroundSize = `${100 * zoomLevel}% ${100 * zoomLevel}%, ${100 * zoomLevel}% ${100 * zoomLevel}%, auto`;
    }
  }

  if (zoomInBtn) {
    zoomInBtn.addEventListener('click', () => {
      zoomLevel = Math.min(MAX_ZOOM, zoomLevel + 0.15);
      applyZoom();
    });
  } else {
    console.warn('contact.js: #mapZoomIn button not found.');
  }

  if (zoomOutBtn) {
    zoomOutBtn.addEventListener('click', () => {
      zoomLevel = Math.max(MIN_ZOOM, zoomLevel - 0.15);
      applyZoom();
    });
  } else {
    console.warn('contact.js: #mapZoomOut button not found.');
  }

  // "Reserve Now" CTA in the Planning an event bar
  const ctaReserveBtn = document.querySelector('.btn-cta-reserve');
  if (ctaReserveBtn) {
    ctaReserveBtn.addEventListener('click', () => {
      window.location.href = 'reservation-process1.html';
    });
  } else {
    console.warn('contact.js: .btn-cta-reserve button not found.');
  }

  // ---------- Navbar: Reserve Now, mobile menu, footer year ----------
  const navReserveBtn = document.getElementById('reserveNowNavBtn');
  if (navReserveBtn) {
    navReserveBtn.addEventListener('click', () => {
      window.location.href = 'reservation-process1.html';
    });
  }

  const navToggle = document.getElementById('navToggleBtn');
  const navLinks = document.getElementById('siteNavLinks');
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => navLinks.classList.toggle('open'));
  }

  const yearEl = document.getElementById('footerYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  // ---------- "Send Us a Message" form ----------
  const form = document.getElementById('contactForm');
  const statusEl = document.getElementById('cfStatus');

  if (form) {
    const fields = {
      name:    { el: document.getElementById('cfName'),    msg: 'Please enter your full name.' },
      email:   { el: document.getElementById('cfEmail'),   msg: 'Please enter a valid email address.' },
      phone:   { el: document.getElementById('cfPhone'),   msg: 'Please enter a valid PH mobile number (e.g. 09XX XXX XXXX).' },
      event:   { el: document.getElementById('cfEvent'),   msg: 'Please select an event type.' },
      message: { el: document.getElementById('cfMessage'), msg: 'Please tell us about your event.' },
    };

    const validators = {
      name:    (v) => v.trim().length >= 2,
      email:   (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()),
      phone:   (v) => /^(09|\+639)\d{9}$/.test(v.replace(/[\s-]/g, '')),
      event:   (v) => v !== '',
      message: (v) => v.trim().length >= 10,
    };

    function setInvalid(key, invalid) {
      const wrap = fields[key].el.closest('.cf-field');
      wrap.classList.toggle('invalid', invalid);
      wrap.querySelector('.cf-error').textContent = invalid ? fields[key].msg : '';
    }

    // Clear the error as soon as the user fixes the field
    Object.keys(fields).forEach((key) => {
      const evt = key === 'event' ? 'change' : 'input';
      fields[key].el.addEventListener(evt, () => {
        if (validators[key](fields[key].el.value)) setInvalid(key, false);
      });
    });

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      statusEl.textContent = '';
      statusEl.classList.remove('error');

      let allValid = true;
      Object.keys(fields).forEach((key) => {
        const ok = validators[key](fields[key].el.value);
        setInvalid(key, !ok);
        if (!ok) allValid = false;
      });

      if (!allValid) {
        statusEl.textContent = 'Please check the highlighted fields.';
        statusEl.classList.add('error');
        const firstBad = form.querySelector('.cf-field.invalid input, .cf-field.invalid select, .cf-field.invalid textarea');
        if (firstBad) firstBad.focus();
        return;
      }

      const payload = {
        name:      fields.name.el.value.trim(),
        email:     fields.email.el.value.trim(),
        phone:     fields.phone.el.value.trim(),
        eventType: fields.event.el.value,
        message:   fields.message.el.value.trim(),
      };

      // TODO: replace this with your real backend call, e.g.
      // fetch('send-message.php', { method: 'POST', body: JSON.stringify(payload) })
      console.log('Contact form submitted:', payload);

      const submitBtn = form.querySelector('.btn-send');
      submitBtn.disabled = true;
      setTimeout(() => {
        statusEl.textContent = 'Thank you! Your message has been sent. We\'ll get back to you soon.';
        form.reset();
        submitBtn.disabled = false;
      }, 400);
    });
  }

});