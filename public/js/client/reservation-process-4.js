// Step 4 (Confirmation) page behavior.
// Cancel is a plain link back to Step 3 (no confirm dialog needed here since
// nothing on this page is being edited — it's a read-only summary).
// Continue to Payment moves on to Step 5.
document.addEventListener('DOMContentLoaded', () => {
  const cancelBtn = document.getElementById('cancelBtn');
  const cancelModalEl = document.getElementById('cancelModal');

  if (cancelBtn && cancelModalEl) {
    const cancelModal = new bootstrap.Modal(cancelModalEl);
    cancelBtn.addEventListener('click', () => cancelModal.show());
  }
});

// // Step 4 (Confirmation): reads everything saved by Steps 1-3, renders it,
// // supports Cancel (clears data + returns home) and Continue to Payment.
// document.addEventListener('DOMContentLoaded', () => {

//   // ---- Config: match these to whatever Steps 1-3 save ----
//   const STORAGE_KEY = 'reservation';
//   const STEP1_URL   = 'reservation-process-1.html';
//   const PAYMENT_URL = 'reservation-process-5.html';
//   const HOME_URL    = 'homepage.html';

 

//   const $ = id => document.getElementById(id);

//   function load() {
//     try {
//       const raw = sessionStorage.getItem(STORAGE_KEY) || localStorage.getItem(STORAGE_KEY);
//       return raw ? JSON.parse(raw) : null;
//     } catch (e) {
//       console.error('Could not read reservation data:', e);
//       return null;
//     }
//   }

//   function clearAll() {
//     sessionStorage.removeItem(STORAGE_KEY);
//     localStorage.removeItem(STORAGE_KEY);
//   }

//   const peso = n => '₱' + Number(n || 0).toLocaleString('en-PH', {
//     minimumFractionDigits: 2, maximumFractionDigits: 2
//   });

//   function fmtDate(iso) {
//     if (!iso) return '—';
//     const d = new Date(iso + 'T00:00:00');
//     if (isNaN(d)) return iso;
//     const long = d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
//     const day  = d.toLocaleDateString('en-US', { weekday: 'long' });
//     return `${long} (${day})`;
//   }

//   function fmtTime(t) {
//     if (!t) return '';
//     const [h, m] = t.split(':').map(Number);
//     if (isNaN(h)) return t;
//     const ampm = h >= 12 ? 'PM' : 'AM';
//     return `${(h % 12) || 12}:${String(m || 0).padStart(2, '0')} ${ampm}`;
//   }

//   const set = (id, val) => {
//     $(id).textContent = (val === undefined || val === null || val === '') ? '—' : val;
//   };

//   // ---- Cancel ----
//   const cancelModal = new bootstrap.Modal($('cancelModal'));
//   $('cancelBtn').addEventListener('click', () => cancelModal.show());
//   $('confirmCancelBtn').addEventListener('click', () => {
//     clearAll();
//     window.location.href = HOME_URL;
//   });

//   // ---- Load + render ----
//   const data = load();

//   if (!data) {
//     alert('No reservation found. Please start from Step 1.');
//     window.location.href = STEP1_URL;
//     return;
//   }

//   set('sumDate', fmtDate(data.date));
//   set('sumTime', data.startTime && data.endTime
//     ? `${fmtTime(data.startTime)} - ${fmtTime(data.endTime)}`
//     : fmtTime(data.startTime));
//   set('sumEventType', data.eventType);
//   set('sumGuests', data.guests);
//   set('sumRestriction', data.foodRestriction || 'None');

//   const c = data.contact || {};
//   set('sumName', c.name);
//   set('sumEmail', c.email);
//   set('sumPhone', c.phone);

//   const v = data.venue || {};
//   set('sumAddress', v.address);
//   set('sumLandmark', v.landmark);

//   // Price rows (textContent so user input can't inject HTML)
//   const rowsEl = $('priceRows');
//   rowsEl.innerHTML = '';
//   const items = Array.isArray(data.priceItems) ? data.priceItems : [];
//   let total = 0;
//   items.forEach(item => {
//     const amt = Number(item.amount) || 0;
//     total += amt;
//     const row = document.createElement('div');
//     row.className = 'price-row';
//     const l = document.createElement('span'); l.textContent = item.label;
//     const r = document.createElement('span'); r.textContent = peso(amt);
//     row.append(l, r);
//     rowsEl.appendChild(row);
//   });
//   set('sumTotal', peso(total));

//   // ---- Continue ----
//   $('continuePaymentBtn').addEventListener('click', () => {
//     data.total = total;   // saved for Step 5
//     sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data));
//     window.location.href = PAYMENT_URL;
//   });
// });