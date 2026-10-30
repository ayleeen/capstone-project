// ---- Summary mula sa URL (galing process 1) ----
const query = new URLSearchParams(window.location.search);
const dateParam  = query.get('date');
const startParam = query.get('start');
const endParam   = query.get('end');
const paxParam   = query.get('pax');

function formatDateLong(dateStr) {
  const [y, m, d] = dateStr.split('-').map(Number);
  return new Date(y, m - 1, d).toLocaleDateString('en-US', {
    month: 'long', day: 'numeric', year: 'numeric',
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

const summaryEmptyState = document.getElementById('summaryEmptyState');
const summaryFilled     = document.getElementById('summaryFilled');

if (!dateParam) {
  // Walang napiling date: ipakita ang "Go to Step 1"
  summaryEmptyState.classList.remove('d-none');
  summaryFilled.classList.add('d-none');
} else {
  document.getElementById('summaryDate').textContent = formatDateLong(dateParam);
  document.getElementById('summaryTime').textContent =
    `${formatTime12h(startParam)} - ${formatTime12h(endParam)}`;
  document.getElementById('summaryPax').textContent = paxParam || '—';
}

// ---- Cancel ----
document.getElementById('cancelBtn').addEventListener('click', (e) => {
  e.preventDefault();
  if (confirm('Discard this reservation and go back to Step 1?')) {
    sessionStorage.removeItem('eventDetails');
    window.location.href = '/reservation-process-1';
  }
});

// ---- Continue to Step 3 ----
document.getElementById('eventDetailsForm').addEventListener('submit', (e) => {
  e.preventDefault();

  const formData = Object.fromEntries(new FormData(e.target).entries());
  sessionStorage.setItem('eventDetails', JSON.stringify(formData));

  window.location.href = '/reservation-process-3?' + query.toString();
});