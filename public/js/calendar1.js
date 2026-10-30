(function () {
  'use strict';

  /* ---------- Config ---------- */
  var TYPES = {
    wedding: 'Wedding',
    corporate: 'Corporate Event',
    baptismal: 'Baptismal',
    birthday: 'Birthday',
    private: 'Private'
  };
  var STATUS = { confirmed: 'Confirmed', pending: 'Pending payment', cancelled: 'Cancelled' };
  var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  var DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  var DAYS_LONG = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  var HSTART = 7;
  var HEND = 22;
  var ROW = 56;
  var MAX_CHIPS = 3;

  /* ---------- Reservations ----------*/
 var events = window.CALENDAR_EVENTS || [];
  var state = { view: 'month', cur: startOfDay(new Date()), hidden: {} };

  /* ---------- Date helpers ---------- */
  function startOfDay(x) { return new Date(x.getFullYear(), x.getMonth(), x.getDate()); }
  function pad(n) { return String(n).padStart(2, '0'); }
  function iso(x) { return x.getFullYear() + '-' + pad(x.getMonth() + 1) + '-' + pad(x.getDate()); }
  function parseISO(s) { var p = s.split('-'); return new Date(+p[0], +p[1] - 1, +p[2]); }
  function addDays(x, n) { return new Date(x.getFullYear(), x.getMonth(), x.getDate() + n); }
  function toMin(t) { var p = t.split(':'); return +p[0] * 60 + +p[1]; }
  function endMin(e) { return e.endDate && e.endDate !== e.date ? 24 * 60 : toMin(e.end); }
  function h12(h) { return ((h + 11) % 12) + 1; }
  function fmtTime(t) {
    var p = t.split(':'), h = +p[0], m = +p[1];
    return h12(h) + ':' + pad(m) + ' ' + (h < 12 ? 'AM' : 'PM');
  }
  function fmtShort(t) {
    var p = t.split(':'), h = +p[0], m = +p[1];
    return h12(h) + (m ? ':' + pad(m) : '') + (h < 12 ? 'a' : 'p');
  }
  function fmtLong(x) { return DAYS_LONG[x.getDay()] + ', ' + MONTHS[x.getMonth()] + ' ' + x.getDate() + ', ' + x.getFullYear(); }
  function esc(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  /* ---------- Data helpers ---------- */
  function visible(e) { return !state.hidden[e.type]; }
  function eventsOn(day) {
    var key = iso(day);
    return events.filter(function (e) { return e.date === key && visible(e); })
      .sort(function (a, b) { return toMin(a.start) - toMin(b.start); });
  }
  function typeColor(type) { return 'var(--c-' + type + ')'; }
  function byId(id) { return events.filter(function (e) { return e.id === id; })[0]; }

  /* ---------- Rendering ---------- */
  var $cal = document.getElementById('calendar');
  var $title = document.getElementById('calTitle');

  function render() {
    var v = state.view;
    $title.textContent = titleText();
    document.querySelectorAll('[data-view]').forEach(function (b) {
      var on = b.dataset.view === v;
      b.classList.toggle('active', on);
      b.setAttribute('aria-pressed', on);
    });
    $cal.innerHTML = v === 'month' ? monthView()
      : v === 'week' ? timeGrid(weekDays())
      : v === 'day' ? timeGrid([state.cur])
      : agendaView();
    renderUpcoming();
    renderSummary();
  }

  function titleText() {
    var c = state.cur;
    if (state.view === 'day') return fmtLong(c);
    if (state.view === 'week') {
      var d = weekDays(), a = d[0], b = d[6];
      var left = MONTHS[a.getMonth()].slice(0, 3) + ' ' + a.getDate();
      var right = (a.getMonth() !== b.getMonth() ? MONTHS[b.getMonth()].slice(0, 3) + ' ' : '') + b.getDate();
      return left + ' – ' + right + ', ' + b.getFullYear();
    }
    return MONTHS[c.getMonth()] + ' ' + c.getFullYear();
  }

  function weekDays() {
    var s = addDays(state.cur, -state.cur.getDay());
    return [0, 1, 2, 3, 4, 5, 6].map(function (i) { return addDays(s, i); });
  }

  function chip(e) {
    return '<button type="button" class="evt is-' + e.status + '" style="--c:' + typeColor(e.type) + '" data-event="' + e.id + '"' +
      ' title="' + esc(e.name) + ', ' + fmtTime(e.start) + ' to ' + fmtTime(e.end) + '">' +
      '<span class="t">' + fmtShort(e.start) + '</span><span class="tx">' + esc(e.name) + '</span></button>';
  }

  /* Month */
  function monthView() {
    var y = state.cur.getFullYear(), m = state.cur.getMonth();
    var first = new Date(y, m, 1);
    var dim = new Date(y, m + 1, 0).getDate();
    var rows = Math.ceil((first.getDay() + dim) / 7);
    var start = addDays(first, -first.getDay());
    var today = iso(new Date());
    var h = '<div class="month">';
    DAYS.forEach(function (d) { h += '<div class="dow">' + d + '</div>'; });
    for (var i = 0; i < rows * 7; i++) {
      var day = addDays(start, i), key = iso(day), list = eventsOn(day);
      h += '<div class="cell' + (day.getMonth() !== m ? ' other' : '') + (key === today ? ' today' : '') + '" data-day="' + key + '">' +
        '<button type="button" class="num" data-day="' + key + '" aria-label="' + fmtLong(day) + '">' + day.getDate() + '</button>';
      list.slice(0, MAX_CHIPS).forEach(function (e) { h += chip(e); });
      if (list.length > MAX_CHIPS) {
        h += '<button type="button" class="more-link" data-day="' + key + '">+' + (list.length - MAX_CHIPS) + ' more</button>';
      }
      h += '</div>';
    }
    return h + '</div>';
  }

  /* Week and day: overlapping events share the column width */
  function layoutLanes(list) {
    var out = [], cluster = [], laneEnds = [], clusterEnd = 0;
    function flush() {
      var n = laneEnds.length;
      cluster.forEach(function (c) { c.n = n; out.push(c); });
      cluster = []; laneEnds = []; clusterEnd = 0;
    }
    list.forEach(function (e) {
      var s = toMin(e.start), en = endMin(e);
      if (cluster.length && s >= clusterEnd) flush();
      var lane = -1;
      for (var i = 0; i < laneEnds.length; i++) { if (laneEnds[i] <= s) { lane = i; break; } }
      if (lane < 0) { lane = laneEnds.length; laneEnds.push(en); } else { laneEnds[lane] = en; }
      clusterEnd = Math.max(clusterEnd, en);
      cluster.push({ e: e, lane: lane, s: s, en: en });
    });
    if (cluster.length) flush();
    return out;
  }

  function timeGrid(days) {
    var hours = HEND - HSTART;
    var today = iso(new Date());
    var now = new Date(), nowMin = now.getHours() * 60 + now.getMinutes();
    var h = '<div class="tg"><div class="tg-inner" style="--hours:' + hours + '">';

    h += '<div class="tg-head" style="--cols:' + days.length + '"><div class="corner"></div>';
    days.forEach(function (d) {
      h += '<button type="button" class="hd' + (iso(d) === today ? ' today' : '') + '" data-day="' + iso(d) + '">' +
        DAYS[d.getDay()] + '<span class="d">' + d.getDate() + '</span></button>';
    });
    h += '</div>';

    h += '<div class="tg-body" style="--cols:' + days.length + '"><div class="tg-gutter">';
    for (var i = 0; i < hours * 2; i++) {
      var hr = HSTART + Math.floor(i / 2), half = i % 2;
      h += '<span class="' + (half ? 'half' : '') + '" style="top:' + (i * ROW / 2) + 'px' + (i === 0 ? ';transform:none' : '') + '">' +
        h12(hr) + ':' + (half ? '30' : '00') + ' ' + (hr < 12 ? 'AM' : 'PM') + '</span>';
    }
    h += '</div>';

    days.forEach(function (d) {
      h += '<div class="tg-col">';
      layoutLanes(eventsOn(d)).forEach(function (c) {
        var e = c.e;
        var top = Math.max(0, (c.s - HSTART * 60) / 60 * ROW);
        var height = Math.max(24, (Math.min(c.en, HEND * 60) - Math.max(c.s, HSTART * 60)) / 60 * ROW - 2);
        var w = 100 / c.n;
        h += '<button type="button" class="evt is-' + e.status + '" data-event="' + e.id + '" style="--c:' + typeColor(e.type) +
          ';top:' + top + 'px;height:' + height + 'px;left:calc(' + (c.lane * w) + '% + 2px);width:calc(' + w + '% - 4px)">' +
          '<span class="nm">' + esc(e.name) + '</span>' + fmtShort(e.start) + ' – ' + fmtShort(e.end) +
          (days.length === 1 ? ' · ' + e.guests + ' guests' : '') + '</button>';
      });
      if (iso(d) === today && nowMin >= HSTART * 60 && nowMin < HEND * 60) {
        h += '<div class="now-line" style="top:' + ((nowMin - HSTART * 60) / 60 * ROW) + 'px"></div>';
      }
      h += '</div>';
    });
    return h + '</div></div></div>';
  }

  /* Agenda: every event in the current month, grouped by date */
  function agendaView() {
    var y = state.cur.getFullYear(), m = state.cur.getMonth();
    var dim = new Date(y, m + 1, 0).getDate();
    var today = iso(new Date()), h = '', any = false;
    for (var n = 1; n <= dim; n++) {
      var day = new Date(y, m, n), list = eventsOn(day);
      if (!list.length) continue;
      any = true;
      h += '<div class="agenda-day' + (iso(day) === today ? ' today' : '') + '">' +
        '<div class="agenda-date"><span class="d">' + n + '</span><span class="m">' + DAYS[day.getDay()] + '</span></div>' +
        '<div class="agenda-items">';
      list.forEach(function (e) {
        h += '<button type="button" class="agenda-item is-' + e.status + '" style="--c:' + typeColor(e.type) + '" data-event="' + e.id + '">' +
          '<span class="time">' + fmtTime(e.start) + ' – ' + fmtTime(e.end) + '</span>' +
          '<span class="nm">' + esc(e.name) + '</span>' +
          '<span class="meta">' + TYPES[e.type] + ' · ' + e.guests + ' guests</span>' +
          '<span class="status ' + e.status + '">' + STATUS[e.status] + '</span></button>';
      });
      h += '</div></div>';
    }
    return any ? h : '<div class="empty"><strong>No events this month</strong>New reservations will show up here.</div>';
  }

  /* Summary box: totals for whatever range is showing */
  function rangeDays() {
    var c = state.cur;
    if (state.view === 'day') return [c];
    if (state.view === 'week') return weekDays();
    var dim = new Date(c.getFullYear(), c.getMonth() + 1, 0).getDate(), out = [];
    for (var n = 1; n <= dim; n++) out.push(new Date(c.getFullYear(), c.getMonth(), n));
    return out;
  }

  function renderSummary() {
    var list = [];
    rangeDays().forEach(function (d) { list = list.concat(eventsOn(d)); });
    var live = list.filter(function (e) { return e.status !== 'cancelled'; });
    var guests = live.reduce(function (t, e) { return t + (e.guests || 0); }, 0);
    // only confirmed reservations count as sales
    var sales = live.filter(function (e) { return e.status === 'confirmed'; })
      .reduce(function (t, e) { return t + (e.sales || 0); }, 0);
    var range = state.view === 'day' ? 'Selected Day' : state.view === 'week' ? 'This Week' : 'This Month';

    document.getElementById('dsDate').textContent = MONTHS[state.cur.getMonth()].slice(0, 3) + ' ' + state.cur.getDate();
    document.getElementById('dsWeekday').textContent = DAYS_LONG[state.cur.getDay()] + ', ' + state.cur.getFullYear();
    document.getElementById('stEvents').textContent = live.length;
    document.getElementById('stGuests').textContent = guests.toLocaleString('en-PH');
    document.getElementById('stSales').textContent = '₱' + sales.toLocaleString('en-PH');
    ['stRange1', 'stRange2', 'stRange3'].forEach(function (id) { document.getElementById(id).textContent = range; });
  }

  /* Upcoming events panel */
  function renderUpcoming() {
    var today = iso(new Date());
    var list = events.filter(function (e) { return e.date >= today && e.status !== 'cancelled'; })
      .sort(function (a, b) { return (a.date + a.start).localeCompare(b.date + b.start); })
      .slice(0, 6);
    var $ul = document.getElementById('upcomingList');
    if (!list.length) {
      $ul.innerHTML = '<li class="empty"><strong>Nothing coming up</strong>Confirmed and pending bookings appear here.</li>';
      return;
    }
    $ul.innerHTML = list.map(function (e) {
      var d = parseISO(e.date);
      return '<li><button type="button" class="up-item" style="--c:' + typeColor(e.type) + '" data-event="' + e.id + '">' +
        '<span class="up-date"><span class="d">' + d.getDate() + '</span><span class="m">' + MONTHS[d.getMonth()].slice(0, 3) + '</span></span>' +
        '<span class="up-body"><span class="nm">' + esc(e.name) + '</span>' +
        '<span class="sub">' + fmtTime(e.start) + ' · ' + TYPES[e.type] + '</span>' +
        '<span class="status ' + e.status + '">' + STATUS[e.status] + '</span></span></button></li>';
    }).join('');
  }

  /* Type filters */
  function renderFilters() {
    var $f = document.getElementById('typeFilters');
    $f.innerHTML = Object.keys(TYPES).map(function (t) {
      var off = state.hidden[t];
      return '<button type="button" class="type-chip' + (off ? ' off' : '') + '" style="--c:' + typeColor(t) + '" data-type="' + t + '" aria-pressed="' + !off + '">' +
        '<span class="dot"></span>' + TYPES[t] + '</button>';
    }).join('');
  }

  /* ---------- Event detail modal ---------- */
  function openEvent(id) {
    var e = byId(id);
    if (!e) return;
    var badge = document.getElementById('mType');
    badge.textContent = TYPES[e.type];
    badge.style.setProperty('--c', typeColor(e.type));
    document.getElementById('eventModalTitle').textContent = e.name;
    document.getElementById('mDate').textContent = fmtLong(parseISO(e.date));
    var endNote = e.endDate && e.endDate !== e.date
      ? ' (' + MONTHS[parseISO(e.endDate).getMonth()].slice(0, 3) + ' ' + parseISO(e.endDate).getDate() + ')' : '';
    document.getElementById('mTime').textContent = fmtTime(e.start) + ' – ' + fmtTime(e.end) + endNote;
    document.getElementById('mGuests').textContent = e.guests + ' guests';
    document.getElementById('mVenue').textContent = e.venue || 'Not set';
    document.getElementById('mPackage').textContent = e.pkg || 'Not set';
    document.getElementById('mStatus').innerHTML = '<span class="status ' + e.status + '">' + STATUS[e.status] + '</span>';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('eventModal')).show();
  }

  /* ---------- Navigation ---------- */
  function step(n) {
    var c = state.cur;
    if (state.view === 'month' || state.view === 'agenda') state.cur = new Date(c.getFullYear(), c.getMonth() + n, 1);
    else if (state.view === 'week') state.cur = addDays(c, 7 * n);
    else state.cur = addDays(c, n);
    render();
  }

  document.getElementById('prevBtn').addEventListener('click', function () { step(-1); });
  document.getElementById('nextBtn').addEventListener('click', function () { step(1); });
  document.getElementById('todayBtn').addEventListener('click', function () {
    state.cur = startOfDay(new Date());
    render();
  });

  document.querySelectorAll('[data-view]').forEach(function (b) {
    b.addEventListener('click', function () { state.view = b.dataset.view; render(); });
  });

  document.getElementById('typeFilters').addEventListener('click', function (ev) {
    var b = ev.target.closest('[data-type]');
    if (!b) return;
    state.hidden[b.dataset.type] = !state.hidden[b.dataset.type];
    renderFilters();
    render();
  });

  $cal.addEventListener('click', function (ev) {
    var evt = ev.target.closest('[data-event]');
    if (evt) { openEvent(+evt.dataset.event); return; }
    var day = ev.target.closest('[data-day]');
    if (day) { state.cur = parseISO(day.dataset.day); state.view = 'day'; render(); }
  });

  document.getElementById('upcomingList').addEventListener('click', function (ev) {
    var b = ev.target.closest('[data-event]');
    if (b) openEvent(+b.dataset.event);
  });

  /* Mobile sidebar */
  var $sidebar = document.getElementById('sidebar');
  var $menu = document.getElementById('menuToggle');
  if ($menu) {
    $menu.addEventListener('click', function () { $sidebar.classList.toggle('open'); });
    document.addEventListener('click', function (ev) {
      if ($sidebar.classList.contains('open') && !$sidebar.contains(ev.target) && !$menu.contains(ev.target)) {
        $sidebar.classList.remove('open');
      }
    });
    document.addEventListener('keydown', function (ev) {
      if (ev.key === 'Escape') $sidebar.classList.remove('open');
    });
  }

  /* ---------- New reservation modal ---------- */
  var $resModal = document.getElementById('reservationModal');
  var $resForm = document.getElementById('reservationForm');

  function val(id) { return document.getElementById(id).value; }

  $resModal.addEventListener('show.bs.modal', function () {
    $resForm.reset();
    $resForm.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
    document.getElementById('rEndTime').setCustomValidity('');
    var t = iso(new Date());
    document.getElementById('rStartDate').value = t;
    document.getElementById('rEndDate').value = t;
  });

  $resForm.addEventListener('input', function (ev) {
    ev.target.classList.remove('is-invalid');
    if (/^r(Start|End)(Date|Time)$/.test(ev.target.id)) {
      document.getElementById('rEndTime').classList.remove('is-invalid');
    }
  });
  $resForm.addEventListener('change', function (ev) { ev.target.classList.remove('is-invalid'); });

  $resForm.addEventListener('submit', function (ev) {
    ev.preventDefault();

    var sd = val('rStartDate'), st = val('rStartTime'), ed = val('rEndDate'), et = val('rEndTime');
    var backwards = sd && st && ed && et && (ed < sd || (ed === sd && toMin(et) <= toMin(st)));
    document.getElementById('rEndTime').setCustomValidity(backwards ? 'End must be after start' : '');

    var ok = true, firstBad = null;
    $resForm.querySelectorAll('input, select, textarea').forEach(function (el) {
      var valid = el.checkValidity();
      el.classList.toggle('is-invalid', !valid);
      if (!valid) { ok = false; if (!firstBad) firstBad = el; }
    });
    if (!ok) { firstBad.focus(); return; }

    // TODO: send this to your server (fetch POST) and use the saved record's id
    var created = {
      id: events.reduce(function (m, e) { return Math.max(m, e.id); }, 0) + 1,
      name: val('rTitle').trim(),
      client: val('rName').trim(),
      email: val('rEmail').trim(),
      phone: val('rPhone').trim(),
      type: val('rType'),
      date: sd, start: st, end: et, endDate: ed,
      guests: +val('rGuests'),
      status: val('rStatus'),
      venue: val('rVenue').trim(),
      pkg: val('rPackage'),
      staff: val('rStaff'),
      budget: val('rBudget'),
      sales: +((document.getElementById('rBudget').selectedOptions[0] || {}).dataset || {}).amount || 0,
      notes: val('rNotes').trim()
    };
    events.push(created);

    state.cur = parseISO(sd);
    if (state.hidden[created.type]) { state.hidden[created.type] = false; renderFilters(); }
    render();

    $resModal.addEventListener('hidden.bs.modal', function () {
      var multiDay = ed !== sd;
      document.getElementById('sType').textContent = created.type;
      document.getElementById('sClient').textContent = created.client;
      document.getElementById('sDate').textContent = fmtLong(parseISO(sd)) +
        (multiDay ? ' to ' + MONTHS[parseISO(ed).getMonth()].slice(0, 3) + ' ' + parseISO(ed).getDate() : '');
      document.getElementById('sTime').textContent = fmtTime(st) + ' – ' + fmtTime(et);
      document.getElementById('sGuests').textContent = created.guests + ' guests';
      bootstrap.Modal.getOrCreateInstance(document.getElementById('successModal')).show();
    }, { once: true });
    bootstrap.Modal.getOrCreateInstance($resModal).hide();
  });

  renderFilters();
  render();
})();