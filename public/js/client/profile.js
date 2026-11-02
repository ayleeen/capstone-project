// public/js/client/profile.js
// Merge ng profile-setting.js (ka-group) + Laravel backend.
// Wala nang localStorage / fake login: Laravel na ang source ng user data at siya ang nagse-save.
document.addEventListener('DOMContentLoaded', function () {

  var MAX_PHOTO_BYTES = 5 * 1024 * 1024;   // 5 MB (palitan kung 2 MB ang piliin ng grupo, pati sa controller)
  var saved = window.PROFILE_SAVED || {};   // galing sa blade (huling na-save sa database)

  var $ = function (id) { return document.getElementById(id); };

  // ================= Modals (focus trap, Esc, backdrop) =================
  var lastFocus = null;

  function openModal(id) {
    var m = $(id);
    if (!m) return;
    lastFocus = document.activeElement;
    m.hidden = false;
    document.body.classList.add('ps-modal-open');
    var f = m.querySelector('[data-autofocus]') || m.querySelector('button');
    if (f) f.focus();
  }
  function closeModal(m) {
    m.hidden = true;
    if (!document.querySelector('.ps-modal:not([hidden])')) {
      document.body.classList.remove('ps-modal-open');
    }
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  }
  document.querySelectorAll('.ps-modal').forEach(function (m) {
    m.addEventListener('click', function (e) {
      if (e.target === m || e.target.closest('[data-close]')) closeModal(m);
    });
  });
  document.addEventListener('keydown', function (e) {
    var open = document.querySelector('.ps-modal:not([hidden])');
    if (!open) return;
    if (e.key === 'Escape') { closeModal(open); return; }
    if (e.key === 'Tab') {
      var items = open.querySelectorAll('button');
      var first = items[0], last = items[items.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });

  // Log out (sidebar) -> confirm modal -> submit ng #logoutForm (POST, may CSRF)
  var sideLogout = $('sideLogout');
  if (sideLogout) sideLogout.addEventListener('click', function () { openModal('logoutModal'); });

  // "Changes Saved!" modal pagkatapos mag-redirect back galing sa controller
  if (window.PROFILE_FLASH) openModal('savedModal');

  // ================= Form =================
  var form = $('profileForm');
  var photoInput = $('photoInput');
  var photoError = $('photoError');
  var saveBtn = $('saveBtn');

  var fields = {
    first_name:      $('first_name'),
    last_name:       $('last_name'),
    contact_number:  $('contact_number'),
    secondary_phone: $('secondary_phone'),
    dob:             $('dob'),
    address_line1:   $('address_line1'),
    address_line2:   $('address_line2')
  };

  // Bawal ang future date sa birthday
  var t = new Date();
  fields.dob.max = t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' + String(t.getDate()).padStart(2, '0');

  var phoneRe = /^[+\d][\d\s()-]{6,}$/;
  var rules = {
    first_name:      function (v) { return v.trim().length >= 2; },
    last_name:       function (v) { return v.trim().length >= 2; },
    contact_number:  function (v) { return phoneRe.test(v.trim()); },
    secondary_phone: function (v) { return v.trim() === '' || phoneRe.test(v.trim()); },
    dob:             function (v) { return v === '' || (!isNaN(new Date(v)) && v <= fields.dob.max); },
    address_line1:   function () { return true; },
    address_line2:   function () { return true; }
  };

  function check(name) {
    var input = fields[name];
    var ok = rules[name](input.value);
    input.closest('.ps-field').classList.toggle('invalid', !ok);
    return ok;
  }
  Object.keys(fields).forEach(function (name) {
    fields[name].addEventListener('blur', function () { check(name); });
    fields[name].addEventListener('input', function () {
      if (fields[name].closest('.ps-field').classList.contains('invalid')) check(name);
    });
  });

  // ---- Photo (sidebar + form na avatar, sabay na nag-uupdate) ----
  var avatars = [
    { img: $('formAvatarImg'), icon: $('formAvatarIcon') },
    { img: $('sideAvatarImg'), icon: $('sideAvatarIcon') }
  ];
  function paintPhoto(src) {
    avatars.forEach(function (a) {
      if (src) { a.img.src = src; a.img.hidden = false; a.icon.hidden = true; }
      else { a.img.removeAttribute('src'); a.img.hidden = true; a.icon.hidden = false; }
    });
  }

  document.querySelectorAll('[data-photo-trigger]').forEach(function (b) {
    b.addEventListener('click', function () { photoInput.click(); });
  });

  photoInput.addEventListener('change', function (e) {
    photoError.textContent = '';
    var file = e.target.files[0];
    if (!file) return;
    if (['image/jpeg', 'image/png'].indexOf(file.type) === -1) {
      photoError.textContent = 'Only JPG or PNG images are accepted.';
      e.target.value = '';
      return;
    }
    if (file.size > MAX_PHOTO_BYTES) {
      photoError.textContent = 'Image is too large. Maximum size is 5MB.';
      e.target.value = '';
      return;
    }
    paintPhoto(URL.createObjectURL(file));
  });

  // ---- Reset: balik sa huling na-save sa database ----
  $('resetBtn').addEventListener('click', function () {
    Object.keys(fields).forEach(function (name) {
      fields[name].value = saved[name] || '';
      fields[name].closest('.ps-field').classList.remove('invalid');
    });
    photoInput.value = '';
    photoError.textContent = '';
    paintPhoto(saved.photo || '');
  });

  // ---- Submit: i-validate muna; kapag ok, normal na POST papunta sa Laravel ----
  form.addEventListener('submit', function (e) {
    var allOk = Object.keys(fields).map(check).every(Boolean);
    if (!allOk) {
      e.preventDefault();
      var bad = form.querySelector('.ps-field.invalid input');
      if (bad) bad.focus();
      return;
    }
    saveBtn.disabled = true;   // iwas double-submit
  });

  // Kapag nag-Back ang user (bfcache), huwag naka-disable ang Save
  window.addEventListener('pageshow', function () { saveBtn.disabled = false; });
});