// calendar.js
 document.querySelectorAll('.view-tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      var view = tab.getAttribute('data-view');
      var eventsLabel = document.getElementById('summaryEventsLabel');
      var salesLabel = document.getElementById('summarySalesLabel');
      if (view === 'day') {
        eventsLabel.textContent = 'Total Events Today';
        salesLabel.textContent = 'Total Sales Today';
      } else if (view === 'week') {
        eventsLabel.textContent = 'Total Events This Week';
        salesLabel.textContent = 'Total Sales this Week';
      }
    });
  });
// ---- Reference "today" for this demo dataset ----

const REFERENCE_DATE = new Date(2026, 4, 22); // May 22, 2026

// ---- Sample events ----
// dayOffset is 0 (Mon) .. 6 (Sun) within whichever week is being viewed relative
// to REFERENCE_DATE's week. start/end are 24h decimal hours (13.5 = 1:30 PM).
const refOffset = (REFERENCE_DATE.getDay() + 6) % 7; // convert Sun=0 based to Mon=0 based

const events = [];

const DAY_RANGE_START = 8;   // 8 AM
const DAY_RANGE_END   = 20;  // 8 PM

const WEEK_RANGE_START = 7;  // 7 AM
const WEEK_RANGE_END   = 20; // 8 PM

const DAY_NAMES_SHORT = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
const DAY_NAMES_FULL  = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

let currentView = "day";
let currentDate = new Date(REFERENCE_DATE);

// ---- DOM refs ----
const viewTabs        = document.querySelectorAll(".view-tab");
const dayView          = document.getElementById("dayView");
const weekView          = document.getElementById("weekView");
const monthView          = document.getElementById("monthView");
const agendaView          = document.getElementById("agendaView");
const dateNavLabel        = document.getElementById("dateNavLabel");
const prevBtn              = document.getElementById("prevBtn");
const nextBtn               = document.getElementById("nextBtn");
const todayBtn                = document.getElementById("todayBtn");

const daySummaryLabel  = document.getElementById("daySummaryLabel");
const daySummaryDate    = document.getElementById("daySummaryDate");
const dayStatEvents      = document.getElementById("dayStatEvents");
const dayStatBooked       = document.getElementById("dayStatBooked");
const dayStatRevenue       = document.getElementById("dayStatRevenue");
const dayGridTitle           = document.getElementById("dayGridTitle");
const dayGrid                  = document.getElementById("dayGrid");

const weekGrid           = document.getElementById("weekGrid");
const weekStatEvents      = document.getElementById("weekStatEvents");
const weekStatBusiest       = document.getElementById("weekStatBusiest");
const weekStatRevenue         = document.getElementById("weekStatRevenue");
const weekStatSlots             = document.getElementById("weekStatSlots");

const upcomingEventsList = document.getElementById("upcomingEventsList");

// ---- Helpers ----
function getWeekStart(date){
  const d = new Date(date);
  const offset = (d.getDay() + 6) % 7; // Mon=0..Sun=6
  d.setDate(d.getDate() - offset);
  d.setHours(0,0,0,0);
  return d;
}

function getDateOffset(weekStart, dayOffset){
  const d = new Date(weekStart);
  d.setDate(d.getDate() + dayOffset);
  return d;
}

function formatFullDate(date){
  return date.toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
}

function formatHour(h){
  const hour = Math.floor(h);
  const min = Math.round((h - hour) * 60);
  const period = hour >= 12 ? 'PM' : 'AM';
  let displayHour = hour % 12;
  if(displayHour === 0) displayHour = 12;
  return min === 0 ? `${displayHour} ${period}` : `${displayHour}:${String(min).padStart(2,'0')} ${period}`;
}

function hourToRow(h, rangeStart){
  // 30-minute granularity: each row = 0.5 hour, 1-based grid-row line
  return Math.round((h - rangeStart) * 2) + 1;
}

function fmtCurrency(n){
  return '₱' + Number(n || 0).toLocaleString();
}

// Sample events are defined relative to *whichever* week is on screen
// (dayOffset within that week), so we resolve them against the current
// week rather than a fixed calendar date.
function sameWeek(a, b){
  return a.getTime() === b.getTime();
}

function getEventsForCurrentDay(){
  const weekStart = getWeekStart(currentDate);
  const offset = (currentDate.getDay() + 6) % 7;
  return events.filter(e => e.dayOffset === offset);
}

function getEventsForCurrentWeek(){
  return events; // all sample events are already scoped to "the current week" by design
}

function renderDayView(){
  const dayEvents = getEventsForCurrentDay();

  daySummaryLabel.textContent = DAY_NAMES_FULL[(currentDate.getDay() + 6) % 7];
  daySummaryDate.textContent = currentDate.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
  dayGridTitle.textContent = formatFullDate(currentDate);

  dayStatEvents.textContent = dayEvents.length;

  const totalHours = dayEvents.reduce((sum, e) => sum + (e.end - e.start), 0);
  dayStatBooked.textContent = dayEvents.length ? `${totalHours}h` : '0h';

  const revenue = dayEvents.length * 30000; // placeholder estimate — wire to real booking data
  dayStatRevenue.textContent = fmtCurrency(revenue);

  // Build grid: time column + single day column, 30-min rows
  dayGrid.innerHTML = '';
  const totalSlots = (DAY_RANGE_END - DAY_RANGE_START) * 2;

  for(let i = 0; i < totalSlots; i++){
    const hourVal = DAY_RANGE_START + i / 2;
    const isOnHour = hourVal % 1 === 0;

    const timeCell = document.createElement('div');
    timeCell.className = 'day-grid-time';
    timeCell.textContent = isOnHour ? formatHour(hourVal) : '';
    dayGrid.appendChild(timeCell);

    const slotCell = document.createElement('div');
    slotCell.className = 'day-grid-slot';
    dayGrid.appendChild(slotCell);
  }

  dayEvents.forEach(e => {
    const block = document.createElement('div');
    block.className = `day-event-block evt-${e.type}`;
    block.style.gridRow = `${hourToRow(e.start, DAY_RANGE_START)} / ${hourToRow(e.end, DAY_RANGE_START)}`;
    block.innerHTML = `<div>${e.title}</div><div class="day-event-time">${formatHour(e.start)} - ${formatHour(e.end)}</div>`;
    dayGrid.appendChild(block);
  });

  if(dayEvents.length === 0){
    const empty = document.createElement('div');
    empty.className = 'cal-grid-empty';
    empty.style.gridRow = `1 / ${totalSlots + 1}`;
    empty.innerHTML = `<i class="bi bi-calendar-x"></i><div>No events scheduled</div>`;
    dayGrid.appendChild(empty);
  }
}

// ---- Week view rendering ----
function renderWeekView(){
  const weekStart = getWeekStart(currentDate);
  const weekEnd = getDateOffset(weekStart, 6);
  const weekEvents = getEventsForCurrentWeek();

  weekGrid.innerHTML = '';

  // corner cell
  const corner = document.createElement('div');
  corner.className = 'week-grid-corner';
  weekGrid.appendChild(corner);

  // day headers
  for(let d = 0; d < 7; d++){
    const dayDate = getDateOffset(weekStart, d);
    const header = document.createElement('div');
    header.className = 'week-grid-day-header';
    header.innerHTML = `<span class="dow">${DAY_NAMES_SHORT[d]}</span>${dayDate.getDate()}`;
    weekGrid.appendChild(header);
  }

  const totalSlots = (WEEK_RANGE_END - WEEK_RANGE_START) * 2;

  for(let i = 0; i < totalSlots; i++){
    const hourVal = WEEK_RANGE_START + i / 2;
    const isOnHour = hourVal % 1 === 0;

    const timeCell = document.createElement('div');
    timeCell.className = 'week-grid-time';
    timeCell.textContent = isOnHour ? formatHour(hourVal) : '';
    weekGrid.appendChild(timeCell);

    for(let d = 0; d < 7; d++){
      const slot = document.createElement('div');
      slot.className = 'week-grid-slot';
      weekGrid.appendChild(slot);
    }
  }

  weekEvents.forEach(e => {
    const block = document.createElement('div');
    block.className = `week-event-block evt-${e.type}`;
    block.style.gridRow = `${hourToRow(e.start, WEEK_RANGE_START) + 1} / ${hourToRow(e.end, WEEK_RANGE_START) + 1}`;
    block.style.gridColumn = `${2 + e.dayOffset}`;
    block.textContent = e.title;
    weekGrid.appendChild(block);
  });

  if(weekEvents.length === 0){
    const empty = document.createElement('div');
    empty.className = 'cal-grid-empty';
    empty.style.gridRow = `2 / ${totalSlots + 2}`;
    empty.style.gridColumn = `2 / 9`;
    empty.innerHTML = `<i class="bi bi-calendar-x"></i><div>No events scheduled this week</div>`;
    weekGrid.appendChild(empty);
  }

  // Week stats
  weekStatEvents.textContent = weekEvents.length;

  const perDayCount = {};
  weekEvents.forEach(e => { perDayCount[e.dayOffset] = (perDayCount[e.dayOffset] || 0) + 1; });
  const busiestOffset = Object.keys(perDayCount).sort((a,b) => perDayCount[b]-perDayCount[a])[0];
  weekStatBusiest.textContent = busiestOffset !== undefined ? DAY_NAMES_SHORT[busiestOffset] : '—';

  weekStatRevenue.textContent = fmtCurrency(weekEvents.length * 30000);

  const bookedSlots = weekEvents.reduce((sum, e) => sum + (e.end - e.start) * 2, 0);
  const totalAvailableSlots = totalSlots * 7;
  weekStatSlots.textContent = Math.max(totalAvailableSlots - bookedSlots, 0);
}

// ---- Upcoming events (right panel) ----
function renderUpcomingEvents(){
  const weekStart = getWeekStart(currentDate);

  const upcoming = events
    .map(e => ({ ...e, date: getDateOffset(weekStart, e.dayOffset) }))
    .sort((a,b) => a.date - b.date || a.start - b.start)
    .slice(0, 4);

  if(upcoming.length === 0){
    upcomingEventsList.innerHTML = `<div class="upcoming-empty">No upcoming events.</div>`;
    return;
  }

  upcomingEventsList.innerHTML = upcoming.map(e => `
    <div class="upcoming-item type-${e.type}">
      <div>
        <div class="upcoming-item-name">${e.title}</div>
        <div class="upcoming-item-meta">${e.date.toLocaleDateString(undefined, { weekday:'short', month:'short', day:'numeric' })} • ${formatHour(e.start)}</div>
      </div>
    </div>
  `).join('');
}

// ---- Date nav label ----
function renderDateNavLabel(){
  if(currentView === 'day'){
    dateNavLabel.textContent = formatFullDate(currentDate);
  } else if(currentView === 'week'){
    const weekStart = getWeekStart(currentDate);
    const weekEnd = getDateOffset(weekStart, 6);
    const sameMonth = weekStart.getMonth() === weekEnd.getMonth();
    const startStr = weekStart.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
    const endStr = sameMonth
      ? weekEnd.toLocaleDateString(undefined, { day: 'numeric', year: 'numeric' })
      : weekEnd.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
    dateNavLabel.textContent = `${startStr} - ${endStr}`;
  } else {
    dateNavLabel.textContent = formatFullDate(currentDate);
  }
}

// ---- View switching ----
function switchView(view){
  currentView = view;

  viewTabs.forEach(tab => tab.classList.toggle('active', tab.dataset.view === view));

  dayView.classList.toggle('d-none', view !== 'day');
  weekView.classList.toggle('d-none', view !== 'week');
  monthView.classList.toggle('d-none', view !== 'month');
  agendaView.classList.toggle('d-none', view !== 'agenda');

  renderCurrentView();
}

function renderCurrentView(){
  renderDateNavLabel();

  if(currentView === 'day'){
    renderDayView();
  } else if(currentView === 'week'){
    renderWeekView();
  }
  // month/agenda are static placeholders — nothing to render

  renderUpcomingEvents();
}

// ---- Event listeners ----
viewTabs.forEach(tab => {
  tab.addEventListener('click', () => switchView(tab.dataset.view));
});

prevBtn.addEventListener('click', () => {
  if(currentView === 'week'){
    currentDate.setDate(currentDate.getDate() - 7);
  } else {
    currentDate.setDate(currentDate.getDate() - 1);
  }
  renderCurrentView();
});

nextBtn.addEventListener('click', () => {
  if(currentView === 'week'){
    currentDate.setDate(currentDate.getDate() + 7);
  } else {
    currentDate.setDate(currentDate.getDate() + 1);
  }
  renderCurrentView();
});

todayBtn.addEventListener('click', () => {
  currentDate = new Date(REFERENCE_DATE);
  renderCurrentView();
});

// ---- Init ----
switchView('day');