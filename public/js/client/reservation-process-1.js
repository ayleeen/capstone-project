  // ---- Capacity (data comes from Laravel via reservation-process-1.blade.php)
  const DAILY_CAPACITY = window.dailyCapacity || 1500;
  const bookedGuests   = window.bookedGuests || {};

  const PAX_STEP = 50;
  const PAX_MIN  = 50;
  let paxMax = DAILY_CAPACITY; // updated whenever a date is opened

  function remainingFor(date) {
    return Math.max(0, DAILY_CAPACITY - (bookedGuests[date] || 0));
  }

  // Fewer slots left than the minimum booking (50 guests) counts as full
  function statusFor(date) {
    const left = remainingFor(date);
    if (left < PAX_MIN) return 'unavailable';
    if (left <= DAILY_CAPACITY * 0.25) return 'limited';
    return 'available';
  }

  const reservationsData = {

  };

  const statusLabels = {
    available: 'Available',
    limited: 'Limited Availability',
    unavailable: 'Unavailable',
    past: 'Past date',
  };

  const weekdayLabels = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

  // Start on the current month
  const now = new Date();
  const baseMonth = new Date(now.getFullYear(), now.getMonth(), 1);
  const todayKey = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
  let monthOffset = 0;
  let selectedDate = null;

  // ---- Calendar rendering
  function pad(n) { return String(n).padStart(2, '0'); }

  function toDateKey(year, month, day) {
    return `${year}-${pad(month)}-${pad(day)}`;
  }

  function renderCalendar(offset) {
    const displayMonth = new Date(baseMonth);
    displayMonth.setMonth(displayMonth.getMonth() + offset);

    const year = displayMonth.getFullYear();
    const month = displayMonth.getMonth() + 1; // 1-indexed to match date keys

    const firstDayOfMonth = new Date(year, month - 1, 1);
    const startWeekday = firstDayOfMonth.getDay(); // 0 = Sunday
    const daysInMonth = new Date(year, month, 0).getDate();
    const daysInPrevMonth = new Date(year, month - 1, 0).getDate();

    const monthLabel = firstDayOfMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

    const cells = [];
    for (let i = startWeekday - 1; i >= 0; i--) {
      cells.push({ day: daysInPrevMonth - i, inMonth: false, status: null, date: null });
    }
    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr = toDateKey(year, month, d);
      const isPast = dateStr < todayKey;
cells.push({
  day: d,
  inMonth: true,
  status: isPast ? 'past' : statusFor(dateStr),
  date: dateStr
});
    }
    const remainder = cells.length % 7;
    if (remainder !== 0) {
      const trailing = 7 - remainder;
      for (let d = 1; d <= trailing; d++) {
        cells.push({ day: d, inMonth: false, status: null, date: null });
      }
    }
    const weeks = [];
    for (let i = 0; i < cells.length; i += 7) {
      weeks.push(cells.slice(i, i + 7));
    }

    // ---- Paint DOM ----
    document.getElementById('calMonthLabel').textContent = monthLabel;

    const weekdayRow = document.getElementById('calWeekdayRow');
    weekdayRow.innerHTML = weekdayLabels.map(wl => `<th>${wl}</th>`).join('');

    const calBody = document.getElementById('calBody');
    calBody.innerHTML = weeks.map(week => {
      const cellsHtml = week.map(cell => {
        if (!cell.inMonth) {
          return `<td><div class="day-cell out">${cell.day}</div></td>`;
        }
        let cls = 'day-cell ' + cell.status;
        if (selectedDate === cell.date) cls += ' selected';
        const full = cell.status === 'unavailable' || cell.status === 'past';
        return `<td>
          <button type="button"
                  class="${cls}"
                  data-date="${cell.date}"
                  data-status="${cell.status}"
                  title="${statusLabels[cell.status]}"
                  ${full ? 'disabled' : ''}>
            ${cell.day}
          </button>
        </td>`;
      }).join('');
      return `<tr>${cellsHtml}</tr>`;
    }).join('');

    attachDayCellHandlers();
  }

  // Month navigation
  document.getElementById('calPrev').addEventListener('click', (e) => {
    e.preventDefault();
    monthOffset -= 1;
    renderCalendar(monthOffset);
  });

  document.getElementById('calNext').addEventListener('click', (e) => {
    e.preventDefault();
    monthOffset += 1;
    renderCalendar(monthOffset);
  });

  // Modal wiring
  const reservationModalEl = document.getElementById('reservationModal');
  const reservationModal = new bootstrap.Modal(reservationModalEl);

  const modalDateTitle        = document.getElementById('modalDateTitle');
  const modalStatusBadge      = document.getElementById('modalStatusBadge');
  const modalReservationsList = document.getElementById('modalReservationsList');
  const modalUnavailableNote  = document.getElementById('modalUnavailableNote');
  const modalSelectedDate     = document.getElementById('modalSelectedDate');
  const modalContinueBtn      = document.getElementById('modalContinueBtn');
  const paxValue = document.getElementById('paxValue');
  const paxInput = document.getElementById('paxInput');
  const calSelectionAlert = document.getElementById('calSelectionAlert');
  const modalStartTime = document.getElementById('modalStartTime');
const modalEndTime = document.getElementById('modalEndTime');

modalStartTime.addEventListener('change', function () {
    if (!this.value) {
        modalEndTime.value = '';
        return;
    }

    const [hours, minutes] = this.value.split(':').map(Number);

    const endDate = new Date();
    endDate.setHours(hours + 4, minutes, 0, 0);

    const endHours = String(endDate.getHours()).padStart(2, '0');
    const endMinutes = String(endDate.getMinutes()).padStart(2, '0');

    modalEndTime.value = `${endHours}:${endMinutes}`;
});
  document.getElementById('paxMinus').addEventListener('click', () => {
    let v = Math.max(PAX_MIN, parseInt(paxValue.textContent, 10) - PAX_STEP);
    paxValue.textContent = v;
    paxInput.value = v;
  });
  document.getElementById('paxPlus').addEventListener('click', () => {
    const cap = Math.max(PAX_MIN, Math.floor(paxMax / PAX_STEP) * PAX_STEP);
    let v = Math.min(cap, parseInt(paxValue.textContent, 10) + PAX_STEP);
    paxValue.textContent = v;
    paxInput.value = v;
  });

  function formatDateLong(dateStr) {
    const [y, m, d] = dateStr.split('-').map(Number);
    const dt = new Date(y, m - 1, d);
    return dt.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  }

  function attachDayCellHandlers() {
    document.querySelectorAll('.day-cell[data-date]').forEach(btn => {
      btn.addEventListener('click', () => {
        const date = btn.dataset.date;
        const status = btn.dataset.status;
        const left = remainingFor(date);

        modalSelectedDate.value = date;
        modalDateTitle.textContent = formatDateLong(date);

        modalStatusBadge.textContent = status === 'limited'
          ? `${statusLabels[status]} · ${left} guests left`
          : (statusLabels[status] || 'Available');
        modalStatusBadge.className = 'status-badge ' + status;

        // Limit the guest counter to what is left on this date
        paxMax = left;
        let current = parseInt(paxValue.textContent, 10);
        if (current > left) {
          current = Math.max(PAX_MIN, Math.floor(left / PAX_STEP) * PAX_STEP);
          paxValue.textContent = current;
          paxInput.value = current;
        }

        const slots = reservationsData[date] || [];
        if (slots.length === 0) {
          modalReservationsList.innerHTML = '<div class="no-reservations">No existing reservations for this date.</div>';
        } else {
          modalReservationsList.innerHTML = slots.map(s =>
            `<div class="reservation-row"><span>${s.start}</span><span>${s.end}</span></div>`
          ).join('');
        }

        if ((status === 'limited' || status === 'unavailable') && slots.length > 0) {
          modalUnavailableNote.classList.remove('d-none');
        } else {
          modalUnavailableNote.classList.add('d-none');
        }

        if (status === 'unavailable') {
          modalContinueBtn.disabled = true;
          modalContinueBtn.textContent = 'Fully Booked';
        } else {
          modalContinueBtn.disabled = false;
          modalContinueBtn.textContent = 'Continue to Event Details';
        }

        reservationModal.show();
      });
    });
  }

  function formatTime12h(timeStr) {
    if (!timeStr) return '';
    let [h, m] = timeStr.split(':').map(Number);
    const period = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    if (h === 0) h = 12;
    return `${h}:${String(m).padStart(2, '0')} ${period}`;
  }

  document.getElementById('modalForm').addEventListener('submit', (e) => {
    const start = document.getElementById('modalStartTime').value;
    const end = document.getElementById('modalEndTime').value;

const [startHours, startMinutes] = start.split(':').map(Number);

const expectedEnd = new Date();
expectedEnd.setHours(startHours + 4, startMinutes, 0, 0);

const expectedEndTime =
    String(expectedEnd.getHours()).padStart(2, '0') + ':' +
    String(expectedEnd.getMinutes()).padStart(2, '0');

if (end !== expectedEndTime) {
    e.preventDefault();
    alert('End time must be exactly 4 hours after start time.');
    return;
}

    sessionStorage.setItem('reservation', JSON.stringify({
      date: modalSelectedDate.value,
      start: start,
      end: end,
      pax: paxInput.value,
    }));
  });

  renderCalendar(monthOffset);
  const left = window.dailyCapacity - ((window.bookedGuests || {})[selectedDate] || 0);
paxInput.max = left;