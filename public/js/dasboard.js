// Builds the calendar for whatever month/year is currently selected,
// with today highlighted only when viewing the real current month.
(function () {
  const calBody = document.getElementById('calBody');
  const calTitle = document.getElementById('calTitle');
  const prevBtn = document.getElementById('calPrev');
  const nextBtn = document.getElementById('calNext');

  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  const today = new Date();
  const realYear = today.getFullYear();
  const realMonth = today.getMonth();
  const todayDate = today.getDate();

  // current view state — starts on today's month
  let viewYear = realYear;
  let viewMonth = realMonth;

  function renderCalendar(year, month) {
    if (calTitle) {
      calTitle.textContent = `${monthNames[month]} ${year}`;
    }

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    let html = '<tr>';
    let dayCount = 0;

    for (let i = 0; i < firstDay; i++) {
      html += '<td class="muted"></td>';
      dayCount++;
    }

    for (let d = 1; d <= daysInMonth; d++) {
      if (dayCount % 7 === 0 && dayCount !== 0) html += '</tr><tr>';
      const isToday = year === realYear && month === realMonth && d === todayDate;
      const cls = isToday ? 'today' : '';
      html += `<td class="${cls}">${d}</td>`;
      dayCount++;
    }

    // trailing days of next month
    let nextDay = 1;
    while (dayCount % 7 !== 0) {
      html += `<td class="muted">${nextDay}</td>`;
      nextDay++;
      dayCount++;
    }
    html += '</tr>';
    calBody.innerHTML = html;
  }

  function goToPrevMonth() {
    viewMonth--;
    if (viewMonth < 0) {
      viewMonth = 11;
      viewYear--;
    }
    renderCalendar(viewYear, viewMonth);
  }

  function goToNextMonth() {
    viewMonth++;
    if (viewMonth > 11) {
      viewMonth = 0;
      viewYear++;
    }
    renderCalendar(viewYear, viewMonth);
  }

  if (prevBtn) prevBtn.addEventListener('click', goToPrevMonth);
  if (nextBtn) nextBtn.addEventListener('click', goToNextMonth);

  renderCalendar(viewYear, viewMonth);
})(); 