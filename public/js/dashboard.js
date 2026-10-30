document.addEventListener('DOMContentLoaded', function () {
  const calBody  = document.getElementById('calBody');
  const calTitle = document.getElementById('calTitle');
  const prevBtn  = document.getElementById('calPrev');
  const nextBtn  = document.getElementById('calNext');

  if (!calBody) {
    console.error('Calendar init failed: no element with id="calBody" found.');
    return;
  }

  const monthNames = ['January','February','March','April','May','June',
    'July','August','September','October','November','December'];

  const events = {};
  (window.calendarEvents || []).forEach(function (e) { events[e.date] = e; });

  const today     = new Date();
  const realYear  = today.getFullYear();
  const realMonth = today.getMonth();
  const todayDate = today.getDate();
  let viewYear = realYear, viewMonth = realMonth;

  function pad(n) { return String(n).padStart(2, '0'); }

  function dayCell(year, month, d, isToday) {
    const key = `${year}-${pad(month + 1)}-${pad(d)}`;
    const ev  = events[key];
    let cls   = isToday ? 'today' : '';
    let title = '';
    if (ev) {
      cls += ` has-event ev-${ev.status}`;
      title = ` title="${(ev.titles || []).join(', ').replace(/"/g, '&quot;')}"`;
    }
    return `<td class="${cls.trim()}"${title}>${d}</td>`;
  }

  function renderCalendar(year, month) {
    if (calTitle) calTitle.textContent = `${monthNames[month]} ${year}`;
    const firstDay    = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const rowCount    = Math.ceil((firstDay + daysInMonth) / 7);
    const rowHeight   = (100 / rowCount).toFixed(4);

    let html = `<tr style="height:${rowHeight}%">`;
    let dayCount = 0;
    for (let i = 0; i < firstDay; i++) { html += '<td class="muted"></td>'; dayCount++; }
    for (let d = 1; d <= daysInMonth; d++) {
      if (dayCount % 7 === 0 && dayCount !== 0) html += `</tr><tr style="height:${rowHeight}%">`;
      const isToday = year === realYear && month === realMonth && d === todayDate;
      html += dayCell(year, month, d, isToday);
      dayCount++;
    }
    let nextDay = 1;
    while (dayCount % 7 !== 0) { html += `<td class="muted">${nextDay++}</td>`; dayCount++; }
    html += '</tr>';
    calBody.innerHTML = html;
  }

  prevBtn && prevBtn.addEventListener('click', function () {
    viewMonth--; if (viewMonth < 0) { viewMonth = 11; viewYear--; }
    renderCalendar(viewYear, viewMonth);
  });
  nextBtn && nextBtn.addEventListener('click', function () {
    viewMonth++; if (viewMonth > 11) { viewMonth = 0; viewYear++; }
    renderCalendar(viewYear, viewMonth);
  });

  renderCalendar(viewYear, viewMonth);
});