document.addEventListener('DOMContentLoaded', () => {

  // ---- Reservation rules ----
  const EDIT_CUTOFF_DAYS = 3;           // the date can only be changed up to 3 days before the event
  const MIN_LEAD_DAYS = 3;              // a new date must be at least this many days from today
  const PAGE_SIZE = 3;

  const $ = id => document.getElementById(id);
  const DAY = 24 * 60 * 60 * 1000;

  // Galing sa Laravel (naka-login na user), nilalagay ng blade sa window.CLIENT_NAME
  const CLIENT_NAME = window.CLIENT_NAME || '';

  // ================= Modals (no extra library needed) =================
  let lastFocus = null;
  function openModal(id) {
    const m = $(id);
    lastFocus = document.activeElement;
    m.hidden = false;
    document.body.classList.add('ps-modal-open');
    const f = m.querySelector('[data-autofocus]') || m.querySelector('input:not(:disabled), button:not([hidden]):not(:disabled):not(.rv-x)');
    if (f) f.focus();
  }
  function closeModal(m) {
    m.hidden = true;
    if (!document.querySelector('.ps-modal:not([hidden])')) document.body.classList.remove('ps-modal-open');
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  }
  document.querySelectorAll('.ps-modal').forEach(m => {
    m.addEventListener('click', e => {
      if (e.target === m || e.target.closest('[data-close]')) closeModal(m);
    });
  });
  document.addEventListener('keydown', e => {
    const open = document.querySelector('.ps-modal:not([hidden])');
    if (!open) return;
    if (e.key === 'Escape') { closeModal(open); return; }
    if (e.key === 'Tab') {
      const items = [...open.querySelectorAll('button:not([hidden]):not(:disabled), input:not(:disabled), a[href]:not(.disabled)')];
      if (!items.length) return;
      const first = items[0], last = items[items.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
  // Swap one popup for another without losing the original focus target
  function swapModal(fromId, toId) {
    const keep = lastFocus;
    $(fromId).hidden = true;
    openModal(toId);
    lastFocus = keep;
  }

  // ================= Log out (asks first) =================
  // Ang header (hdrLogoutBtn) at ang sidebar ay parehong dumadaan sa modal na ito
  function logout() {
    const f = $('hdrLogoutForm');
    if (f) f.submit();
  }
  $('sideLogout').addEventListener('click', () => openModal('logoutModal'));
  $('confirmLogoutBtn').addEventListener('click', logout);

  // ================= Data =================
  // Replace with your backend, e.g.  reservations = await (await fetch('/api/my-reservations')).json();
  // status: 'Pending' | 'Confirmed' | 'Completed' | 'Cancelled'
  // date: 'YYYY-MM-DD', time / endTime: 'HH:MM' (24h)
  // txn: transaction number, paidAt: 'YYYY-MM-DDTHH:MM:SS' (date of the latest payment, used on the receipt)
  // The sample dates are relative to today so the 3-day rule can be tried out.
  const pad = n => String(n).padStart(2, '0');
  const toISO = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
  const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate() + n); return x; };
  const now0 = new Date();

  // Galing sa database: ang controller ang gumagawa nito at nilalagay ng blade sa window.RESERVATIONS
  let reservations = Array.isArray(window.RESERVATIONS) ? window.RESERVATIONS : [];

  // Dates that are busy. Anything not listed counts as 'available'.
  // Replace with your backend, e.g.  availability = await (await fetch('/api/availability')).json();
  // value: 'limited' | 'unavailable'
  // Mga petsang puno/limitado: { 'YYYY-MM-DD': 'limited' | 'unavailable' }. Walang laman = lahat available.
  let availability = (window.AVAILABILITY && !Array.isArray(window.AVAILABILITY)) ? window.AVAILABILITY : {};

  // ================= Helpers =================
  const peso = n => '₱' + Number(n || 0).toLocaleString('en-PH');
  const parseDate = iso => { const [y, m, d] = iso.split('-').map(Number); return new Date(y, m - 1, d); };
  const fmtDate = iso => parseDate(iso).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  const fmtLong = d => d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
  function fmtTime(t) {
    if (!t) return '';
    const [h, m] = t.split(':').map(Number);
    return ((h % 12) || 12) + ':' + pad(m || 0) + ' ' + (h >= 12 ? 'PM' : 'AM');
  }
  const timeRange = r => r.endTime ? fmtTime(r.time) + ' - ' + fmtTime(r.endTime) : fmtTime(r.time);
  const eventStart = r => new Date(r.date + 'T' + (r.time || '00:00') + ':00');
  const chipClass = s => 'rv-chip-' + String(s).toLowerCase();
  const chipIcon = s => ({ Completed: 'fa-circle-check', Confirmed: 'fa-circle-check', Pending: 'fa-clock', Cancelled: 'fa-circle-xmark' }[s] || 'fa-circle');
  const typeIcon = t => ({ Birthday: 'fa-cake-candles', Wedding: 'fa-ring', 'Corporate Event': 'fa-briefcase' }[t] || 'fa-champagne-glasses');

  function el(tag, cls, text) {
    const e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text != null) e.textContent = text;
    return e;
  }
  function iconEl(classes) { const i = document.createElement('i'); i.className = classes; i.setAttribute('aria-hidden', 'true'); return i; }
  function paintChip(node, status) {
    node.className = 'rv-chip ' + chipClass(status);
    node.textContent = '';
    node.append(iconEl('fa-solid ' + chipIcon(status)), ' ' + status);
  }

  // ---- The 3-day rule ----
  // A reservation can be edited only while it is Pending/Confirmed AND today is
  // at least 3 days before the event starts. Only the DATE can be changed.
  function editInfo(r) {
    if (!['Pending', 'Confirmed'].includes(r.status)) {
      return { ok: false, reason: 'Only upcoming reservations can be edited.' };
    }
    const cutoff = new Date(eventStart(r).getTime() - EDIT_CUTOFF_DAYS * DAY);
    if (new Date() > cutoff) {
      return { ok: false, cutoff,
        reason: 'The event date can only be changed up to ' + EDIT_CUTOFF_DAYS + ' days before the event. To change it now, please contact us.' };
    }
    return { ok: true, cutoff };
  }
  const hasReceipt = r => r.status !== 'Cancelled' && Number(r.paid) > 0;

  // ================= Render list =================
  let page = 1;

  function render() {
    const list = $('rvList');
    list.innerHTML = '';
    const total = reservations.length;
    const pages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    page = Math.min(Math.max(page, 1), pages);
    const start = (page - 1) * PAGE_SIZE;
    const rows = reservations.slice(start, start + PAGE_SIZE);

    $('rvEmpty').hidden = total > 0;
    $('rvShowing').textContent = total
      ? 'Showing ' + (start + 1) + ' – ' + (start + rows.length) + ' of ' + total + ' reservation' + (total === 1 ? '' : 's')
      : 'Showing 0 reservations';

    rows.forEach(r => list.appendChild(card(r)));
    renderPager(pages);
  }

  function card(r) {
    const c = el('article', 'rv-card');

    // image + category tag
    const imgBox = el('div', 'rv-img');
    imgBox.appendChild(iconEl('fa-solid ' + typeIcon(r.type)));
    if (r.image) {
      const img = document.createElement('img');
      img.src = r.image; img.alt = ''; img.loading = 'lazy';
      img.addEventListener('error', () => img.remove());
      imgBox.appendChild(img);
    }
    const tag = el('span', 'rv-tag');
    tag.append(iconEl('fa-solid ' + typeIcon(r.type)), ' ' + r.type);
    imgBox.appendChild(tag);
    c.appendChild(imgBox);

    // details
    const info = el('div', 'rv-info');
    info.appendChild(el('h3', null, r.title));
    const ul = el('ul');
    [['fa-regular fa-calendar-days', fmtDate(r.date) + ' • ' + fmtTime(r.time)],
     ['fa-solid fa-location-dot', r.venue],
     ['fa-solid fa-user-group', r.guests + ' Guest']].forEach(([ic, text]) => {
      const li = el('li');
      li.append(iconEl(ic), el('span', null, text));
      ul.appendChild(li);
    });
    info.appendChild(ul);
    const chip = el('span');
    paintChip(chip, r.status);
    info.appendChild(chip);
    c.appendChild(info);

    // total
    const tot = el('div', 'rv-total');
    tot.append(el('span', null, 'Total Amount'), el('strong', null, peso(r.total)));
    c.appendChild(tot);

    // buttons
    const btns = el('div', 'rv-btns');
    const view = el('button', 'rv-btn');
    view.type = 'button';
    view.append(iconEl('fa-regular fa-eye'), ' View Details');
    view.addEventListener('click', () => openView(r));
    btns.appendChild(view);

    if (r.status === 'Pending') {
      const info2 = editInfo(r);
      const edit = el('button', 'rv-btn');
      edit.type = 'button';
      edit.append(iconEl('fa-solid fa-pen'), ' Edit Reservation');
      if (!info2.ok) { edit.setAttribute('aria-disabled', 'true'); edit.title = info2.reason; }
      edit.addEventListener('click', () => openEdit(r));
      btns.appendChild(edit);
    } else if (hasReceipt(r)) {
      const dl = el('button', 'rv-btn');
      dl.type = 'button';
      dl.append(iconEl('fa-solid fa-download'), ' Download Receipt');
      dl.addEventListener('click', () => openReceipt(r));
      btns.appendChild(dl);
    }
    c.appendChild(btns);
    return c;
  }

  function renderPager(pages) {
    const pager = $('rvPager');
    pager.innerHTML = '';
    const make = (html, p, opts = {}) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.innerHTML = html;
      if (opts.active) b.classList.add('active');
      if (opts.disabled) b.disabled = true;
      if (opts.label) b.setAttribute('aria-label', opts.label);
      b.addEventListener('click', () => { page = p; render(); });
      pager.appendChild(b);
    };
    make('<i class="fa-solid fa-chevron-left"></i>', page - 1, { disabled: page === 1, label: 'Previous page' });
    for (let p = 1; p <= pages; p++) make(String(p), p, { active: p === page });
    make('<i class="fa-solid fa-chevron-right"></i>', page + 1, { disabled: page === pages, label: 'Next page' });
  }

  // ================= View details =================
  let current = null;

  function openView(r) {
    current = r;
    const tag = $('viewTag');
    tag.textContent = '';
    tag.append(iconEl('fa-solid ' + typeIcon(r.type)), ' ' + r.type);

    $('viewTitle').textContent = r.title;
    paintChip($('viewStatus'), r.status);
    $('viewId').textContent = r.id;
    $('viewDate').textContent = fmtLong(parseDate(r.date));
    $('viewTime').textContent = timeRange(r);
    $('viewVenue').textContent = r.venue;
    $('viewGuests').textContent = r.guests + ' Guest';
    $('viewTotal').textContent = peso(r.total);
    $('viewPaid').textContent = peso(r.paid);
    $('viewBalance').textContent = peso(Math.max(0, r.total - r.paid));

    const ei = editInfo(r);
    const note = $('viewNote');
    note.className = 'rv-note';
    if (ei.ok) {
      note.classList.add('rv-note-info');
      note.textContent = 'You can change the event date until ' + fmtLong(ei.cutoff) + '.';
    } else if (r.status === 'Pending' || r.status === 'Confirmed') {
      note.classList.add('rv-note-warn');
      note.textContent = ei.reason;
    } else {
      note.textContent = '';
    }

    $('viewEditBtn').hidden = !ei.ok;
    $('viewReceiptBtn').hidden = !hasReceipt(r);
    openModal('viewModal');
  }
  $('viewEditBtn').addEventListener('click', () => { if (current) { swapModal('viewModal', 'editModal'); fillEdit(current); } });
  $('viewReceiptBtn').addEventListener('click', () => { if (current) { swapModal('viewModal', 'receiptModal'); fillReceipt(current); } });

  // ================= Edit (date only, picked on the calendar) =================
  const weekdayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const statusLabels = { available: 'Available', limited: 'Limited slots', unavailable: 'Fully booked' };
  const toDateKey = (y, m, d) => y + '-' + pad(m) + '-' + pad(d);   // m is 1-12
  const formatDateLong = key => parseDate(key).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  const minNewDate = () => toISO(addDays(new Date(), MIN_LEAD_DAYS));

  // calendar state (reset every time the edit popup opens)
  let baseMonth = new Date(now0.getFullYear(), now0.getMonth(), 1);
  let monthOffset = 0;
  let selectedDate = null;
  let todayKey = toISO(now0);
  let minKey = minNewDate();

  function showCalAlert(type, msg) {
    const box = $('calSelectionAlert');
    if (!msg) { box.className = 'alert mt-3 mb-0 d-none'; box.textContent = ''; return; }
    box.className = 'alert alert-' + type + ' mt-3 mb-0';
    box.textContent = msg;
  }

  function openEdit(r) {
    current = r;
    openModal('editModal');
    fillEdit(r);
  }

  function fillEdit(r) {
    const ei = editInfo(r);
    $('editSub').textContent = r.title + ' • currently ' + fmtDate(r.date);
    $('editId').textContent = r.id;
    $('editTime').textContent = timeRange(r);
    $('editVenue').textContent = r.venue;
    $('editGuests').textContent = r.guests + ' Guest';
    $('editError').textContent = '';

    const rule = $('editRule');
    if (ei.ok) {
      rule.className = 'rv-note rv-note-info';
      rule.textContent = 'Only the event date can be changed, and only up to ' + EDIT_CUTOFF_DAYS +
        ' days before your event (until ' + fmtLong(ei.cutoff) + '). The new date must be at least ' +
        MIN_LEAD_DAYS + ' days from today and is subject to availability.';
    } else {
      rule.className = 'rv-note rv-note-warn';
      rule.textContent = ei.reason;
    }

    // calendar
    const calCard = $('editForm').querySelector('.calendar-card');
    calCard.classList.toggle('d-none', !ei.ok);
    $('editSaveBtn').disabled = true;           // enabled once a valid date is picked
    selectedDate = null;
    monthOffset = 0;
    todayKey = toISO(new Date());
    minKey = minNewDate();
    baseMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
    showCalAlert();
    if (ei.ok) renderCalendar();
  }

  // ---- Calendar rendering ----
  function renderCalendar() {
    const displayMonth = new Date(baseMonth.getFullYear(), baseMonth.getMonth() + monthOffset, 1);
    const year = displayMonth.getFullYear();
    const month = displayMonth.getMonth() + 1;            // 1-12

    const startWeekday = displayMonth.getDay();           // 0 = Sunday
    const daysInMonth = new Date(year, month, 0).getDate();
    const daysInPrevMonth = new Date(year, month - 1, 0).getDate();

    $('calMonthLabel').textContent = displayMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    $('calWeekdayRow').innerHTML = weekdayLabels.map(w => '<th>' + w + '</th>').join('');

    const cells = [];
    for (let i = startWeekday - 1; i >= 0; i--) cells.push({ day: daysInPrevMonth - i, inMonth: false });
    for (let d = 1; d <= daysInMonth; d++) {
      const key = toDateKey(year, month, d);
      cells.push({
        day: d, inMonth: true, date: key,
        status: availability[key] || 'available',
        isPast: key < minKey,
        isToday: key === todayKey
      });
    }
    for (let d = 1; cells.length % 7 !== 0; d++) cells.push({ day: d, inMonth: false });

    let html = '';
    for (let i = 0; i < cells.length; i += 7) {
      html += '<tr>' + cells.slice(i, i + 7).map(cell => {
        if (!cell.inMonth) return '<td><div class="day-cell out">' + cell.day + '</div></td>';

        const todayCls = cell.isToday ? ' today' : '';
        const todayAttr = cell.isToday ? ' aria-current="date"' : '';

        if (cell.isPast) {
          const reason = cell.date < todayKey
            ? 'Past date'
            : 'Please book at least ' + MIN_LEAD_DAYS + ' day' + (MIN_LEAD_DAYS === 1 ? '' : 's') + ' ahead';
          return '<td><button type="button" class="day-cell past' + todayCls + '" disabled' + todayAttr +
            ' title="' + reason + '" aria-label="' + formatDateLong(cell.date) + ' (' + reason + ')">' + cell.day + '</button></td>';
        }

        let cls = 'day-cell ' + cell.status + todayCls;
        if (selectedDate === cell.date) cls += ' selected';
        return '<td><button type="button" class="' + cls + '" data-date="' + cell.date + '" data-status="' + cell.status + '"' + todayAttr +
          ' title="' + statusLabels[cell.status] + '" aria-label="' + formatDateLong(cell.date) + ', ' + statusLabels[cell.status] + '">' +
          cell.day + '</button></td>';
      }).join('') + '</tr>';
    }
    $('calBody').innerHTML = html;

    // can't go back before the current month
    const prev = $('calPrev');
    const atStart = monthOffset <= 0;
    prev.classList.toggle('disabled', atStart);
    prev.setAttribute('aria-disabled', String(atStart));
  }

  // Month navigation
  $('calPrev').addEventListener('click', e => {
    e.preventDefault();
    if (monthOffset <= 0) return;
    monthOffset -= 1;
    renderCalendar();
  });
  $('calNext').addEventListener('click', e => {
    e.preventDefault();
    monthOffset += 1;
    renderCalendar();
  });

  // Picking a day (one listener on the table body, survives re-renders)
  $('calBody').addEventListener('click', e => {
    const btn = e.target.closest('.day-cell[data-date]');
    if (!btn || !current) return;
    const date = btn.dataset.date;
    const status = btn.dataset.status;
    $('editError').textContent = '';

    if (date === current.date) {
      selectedDate = null;
      $('editSaveBtn').disabled = true;
      showCalAlert('info', 'That is already the date of your reservation. Please pick a different day.');
    } else if (status === 'unavailable') {
      selectedDate = null;
      $('editSaveBtn').disabled = true;
      showCalAlert('danger', formatDateLong(date) + ' is fully booked. Please choose another date.');
    } else {
      selectedDate = date;
      $('editSaveBtn').disabled = false;
      showCalAlert(status === 'limited' ? 'warning' : 'success',
        formatDateLong(date) + (status === 'limited' ? ' has limited slots left.' : ' is available.') +
        ' Press "Save New Date" to confirm.');
    }
    renderCalendar();
  });

  // Save
  $('editForm').addEventListener('submit', e => {
    e.preventDefault();
    const r = current;
    if (!r) return;
    const err = $('editError');

    const ei = editInfo(r);
    if (!ei.ok) { err.textContent = ei.reason; return; }
    if (!selectedDate) { err.textContent = 'Please pick a new date on the calendar.'; return; }
    if (selectedDate < minNewDate()) { err.textContent = 'The new date must be at least ' + MIN_LEAD_DAYS + ' days from today.'; return; }
    if (availability[selectedDate] === 'unavailable') { err.textContent = 'That date is fully booked.'; return; }

    // TODO: HINDI pa ito nase-save sa database. Pagpalit lang ito ng petsa sa page. Kailangan pa ng route na PATCH.
    r.date = selectedDate;

    closeModal($('editModal'));
    render();
    $('noticeTitle').textContent = 'Date updated';
    $('noticeText').textContent = 'Your event is now scheduled for ' + fmtLong(parseDate(r.date)) + '.';
    openModal('noticeModal');
  });

  // ================= Receipt =================
  function openReceipt(r) { current = r; openModal('receiptModal'); fillReceipt(r); }

  function fmtPaidAt(iso) {
    if (!iso) return '—';
    const d = new Date(iso);
    if (isNaN(d)) return '—';
    return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) + ' | ' +
           d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
  }

  function fillReceipt(r) {
    const balance = Math.max(0, r.total - r.paid);
    $('rcTxn').textContent = 'Transaction No.: ' + (r.txn || r.id);
    $('rcResId').textContent = r.id;
    $('rcClient').textContent = CLIENT_NAME || 'Client';
    $('rcEvent').textContent = r.type;
    $('rcDate').textContent = fmtPaidAt(r.paidAt);

    const pill = $('rcStatus');
    pill.textContent = balance === 0 ? 'Paid' : 'Partial';
    pill.className = 'rc-pill ' + (balance === 0 ? 'st-paid' : 'st-partial');

    $('rcTotal').textContent = peso(r.total);
    $('rcPaid').textContent = peso(r.paid);
    $('rcBalance').textContent = peso(balance);
  }

  $('printReceiptBtn').addEventListener('click', () => {
    document.body.classList.add('rv-printing');
    window.print();
  });
  window.addEventListener('afterprint', () => document.body.classList.remove('rv-printing'));

  // ================= Footer year + init =================
  const yr = $('footerYear');
  if (yr) yr.textContent = new Date().getFullYear();

  render();
});