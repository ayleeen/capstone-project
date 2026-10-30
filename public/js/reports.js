
// const reportsData = {
//   totalRevenue: null,       
//   totalBookings: null,       
//   pendingPayments: null,     
//   popularPackage: null,     
//   monthlyGrowth: null,       
//   repeatCustomerRate: null,  

//   salesOverview: {           
//     labels: [],
//     values: []
//   },

//   bookingsByEventType: {      
//     labels: [],
//     values: []
//   },

//   paymentStatus: {           
//     paid: null,
//     partial: null,
//     unpaid: null
//   },

//   rows: []                    // table rows: { id, event, customer, pkg, revenue, status, date }
// };

// // ---------- helpers ----------
// function fmtPeso(n){
//   if(n === null || n === undefined || isNaN(n)) return '₱0';
//   return '₱' + Number(n).toLocaleString();
// }
// function fmtPercent(n){
//   if(n === null || n === undefined || isNaN(n)) return '—';
//   return n + '%';
// }

// // ---------- DOM refs ----------
// const statTotalRevenue      = document.getElementById('statTotalRevenue');
// const statTotalBookings     = document.getElementById('statTotalBookings');
// const statPendingPayments   = document.getElementById('statPendingPayments');
// const statPopularPackage    = document.getElementById('statPopularPackage');
// const statMonthlyGrowth     = document.getElementById('statMonthlyGrowth');
// const statRepeatRate        = document.getElementById('statRepeatRate');

// const legendPaid    = document.getElementById('legendPaid');
// const legendPartial = document.getElementById('legendPartial');
// const legendUnpaid  = document.getElementById('legendUnpaid');

// const reportsTableBody       = document.getElementById('reportsTableBody');
// const reportsTableFooterText = document.getElementById('reportsTableFooterText');

// const insightTopPackage    = document.getElementById('insightTopPackage');
// const insightBestMonth     = document.getElementById('insightBestMonth');
// const insightTopClient     = document.getElementById('insightTopClient');
// const insightUpcoming      = document.getElementById('insightUpcoming');

// // ---------- stat cards ----------
// function renderStats(){
//   statTotalRevenue.textContent    = fmtPeso(reportsData.totalRevenue);
//   statTotalBookings.textContent   = reportsData.totalBookings ?? 0;
//   statPendingPayments.textContent = fmtPeso(reportsData.pendingPayments);
//   statPopularPackage.textContent  = reportsData.popularPackage || '—';
//   statMonthlyGrowth.textContent   = fmtPercent(reportsData.monthlyGrowth);
//   statRepeatRate.textContent      = fmtPercent(reportsData.repeatCustomerRate);
// }

// // ---------- charts ----------
// let salesChart, bookingsChart, paymentChart;

// function renderSalesChart(){
//   const { labels, values } = reportsData.salesOverview;
//   const empty = document.getElementById('salesOverviewEmpty');
//   empty.style.display = values.length ? 'none' : 'flex';

//   const ctx = document.getElementById('salesOverviewChart');
//   const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 230);
//   gradient.addColorStop(0, 'rgba(47,125,60,0.35)');
//   gradient.addColorStop(1, 'rgba(47,125,60,0)');

//   if(salesChart) salesChart.destroy();
//   salesChart = new Chart(ctx, {
//     type: 'line',
//     data: {
//       labels: labels,
//       datasets: [{
//         data: values,
//         borderColor: '#2f7d3c',
//         backgroundColor: gradient,
//         fill: true,
//         tension: 0.35,
//         pointRadius: 3,
//         pointBackgroundColor: '#2f7d3c'
//       }]
//     },
//     options: {
//       responsive: true,
//       maintainAspectRatio: false,
//       plugins: { legend: { display: false } },
//       scales: {
//         y: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { color: '#f1f1f1' } },
//         x: { ticks: { font: { size: 10 } }, grid: { display: false } }
//       }
//     }
//   });
// }

// function renderBookingsChart(){
//   const { labels, values } = reportsData.bookingsByEventType;
//   const empty = document.getElementById('bookingsEmpty');
//   empty.style.display = values.length ? 'none' : 'flex';

//   const palette = ['#2f7d3c', '#8fd19e', '#7ec8e3', '#8e44ad', '#f0b429'];

//   const ctx = document.getElementById('bookingsChart');
//   if(bookingsChart) bookingsChart.destroy();
//   bookingsChart = new Chart(ctx, {
//     type: 'bar',
//     data: {
//       labels: labels,
//       datasets: [{
//         data: values,
//         backgroundColor: labels.map((_, i) => palette[i % palette.length]),
//         borderRadius: 6,
//         maxBarThickness: 34
//       }]
//     },
//     options: {
//       responsive: true,
//       maintainAspectRatio: false,
//       plugins: { legend: { display: false } },
//       scales: {
//         y: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { color: '#f1f1f1' } },
//         x: { ticks: { font: { size: 10 } }, grid: { display: false } }
//       }
//     }
//   });
// }

// function renderPaymentChart(){
//   const { paid, partial, unpaid } = reportsData.paymentStatus;
//   const total = (paid || 0) + (partial || 0) + (unpaid || 0);
//   const empty = document.getElementById('paymentStatusEmpty');
//   empty.style.display = total > 0 ? 'none' : 'flex';

//   const pct = (v) => total > 0 ? Math.round((v || 0) / total * 100) : 0;
//   legendPaid.textContent    = `${pct(paid)}% (${fmtPeso(paid || 0)})`;
//   legendPartial.textContent = `${pct(partial)}% (${fmtPeso(partial || 0)})`;
//   legendUnpaid.textContent  = `${pct(unpaid)}% (${fmtPeso(unpaid || 0)})`;

//   const ctx = document.getElementById('paymentStatusChart');
//   if(paymentChart) paymentChart.destroy();
//   paymentChart = new Chart(ctx, {
//     type: 'doughnut',
//     data: {
//       labels: ['Paid', 'Partial', 'Unpaid'],
//       datasets: [{
//         data: total > 0 ? [paid || 0, partial || 0, unpaid || 0] : [1],
//         backgroundColor: total > 0 ? ['#2f7d3c', '#e8b62c', '#e05353'] : ['#eeeeee'],
//         borderWidth: 0
//       }]
//     },
//     options: {
//       responsive: true,
//       maintainAspectRatio: false,
//       cutout: '68%',
//       plugins: { legend: { display: false }, tooltip: { enabled: total > 0 } }
//     }
//   });
// }

// // ---------- insights ----------
// function renderInsights(){
//   insightTopPackage.textContent = '—';
//   insightBestMonth.textContent  = '—';
//   insightTopClient.textContent  = '—';
//   insightUpcoming.textContent   = '—';
//   // Populate these (and their *Sub labels) once real analytics are available, e.g.:
//   // insightTopPackage.textContent = 'Wedding Package';
//   // document.getElementById('insightTopPackageSub').textContent = '₱532,525 (42% of revenue)';
// }

// // ---------- table ----------
// function renderTable(){
//   if(reportsData.rows.length === 0){
//     reportsTableBody.innerHTML = `
//       <tr>
//         <td colspan="8">
//           <div class="empty-state-table">
//             <i class="bi bi-inbox"></i>
//             No records found
//           </div>
//         </td>
//       </tr>
//     `;
//     reportsTableFooterText.textContent = 'Showing 0 of 0 entries';
//     return;
//   }

//   reportsTableBody.innerHTML = reportsData.rows.map(r => `
//     <tr>
//       <td>${r.id}</td>
//       <td>${r.event}</td>
//       <td>${r.customer}</td>
//       <td>${r.pkg}</td>
//       <td>${fmtPeso(r.revenue)}</td>
//       <td><span class="status-pill-report ${r.status === 'Pending' ? 'pending' : ''}">${r.status}</span></td>
//       <td>${r.date}</td>
//       <td class="text-center">
//         <div class="d-flex align-items-center justify-content-center gap-1">
//           <button class="reports-action-btn" title="View Details" data-action="view" data-id="${r.id}"><i class="bi bi-eye"></i></button>
//           <button class="reports-action-btn" title="Edit" data-action="edit" data-id="${r.id}"><i class="bi bi-pencil"></i></button>
//           <div class="dropdown">
//             <button class="reports-action-btn" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
//             <ul class="dropdown-menu dropdown-menu-end">
//               <li><a class="dropdown-item" href="#" data-action="view" data-id="${r.id}"><i class="bi bi-eye me-2"></i>View Details</a></li>
//               <li><a class="dropdown-item" href="#" data-action="edit-customer" data-id="${r.id}"><i class="bi bi-pencil me-2"></i>Edit Customer</a></li>
//               <li><a class="dropdown-item" href="#" data-action="new-reservation" data-id="${r.id}"><i class="bi bi-calendar-plus me-2"></i>New Reservation</a></li>
//               <li><a class="dropdown-item text-danger" href="#" data-action="delete-customer" data-id="${r.id}"><i class="bi bi-trash me-2"></i>Delete Customer</a></li>
//             </ul>
//           </div>
//         </div>
//       </td>
//     </tr>
//   `).join('');

//   reportsTableFooterText.textContent = `Showing 1 to ${reportsData.rows.length} of ${reportsData.rows.length} entries`;

//   reportsTableBody.querySelectorAll('[data-action]').forEach(el => {
//     el.addEventListener('click', (e) => {
//       e.preventDefault();
//       const action = el.dataset.action;
//       const id = el.dataset.id;
//       console.log('Reports table action:', action, id);
//       alert(`"${action.replace('-', ' ')}" for ${id} — hook this up to your backend.`);
//     });
//   });
// }

// // ---------- render all ----------
// function renderAll(){
//   renderStats();
//   renderSalesChart();
//   renderBookingsChart();
//   renderPaymentChart();
//   renderInsights();
//   renderTable();
// }

// // ---------- filters ----------
// document.getElementById('dateRangeBtn')?.addEventListener('click', () => {
//   alert('Hook this up to your date-range picker of choice.');
// });

// ['eventTypeFilter', 'customerTypeFilter', 'paymentStatusFilter', 'paymentStateFilter'].forEach(id => {
//   document.getElementById(id)?.addEventListener('change', () => {
//     // Re-fetch / re-filter reportsData here based on the selected filters, then renderAll().
//     console.log('Filter changed:', id, document.getElementById(id).value);
//   });
// });

// document.getElementById('resetFiltersBtn')?.addEventListener('click', () => {
//   document.getElementById('dateRangeLabel').textContent = 'Select date range';
//   ['eventTypeFilter', 'customerTypeFilter', 'paymentStatusFilter', 'paymentStateFilter'].forEach(id => {
//     document.getElementById(id).selectedIndex = 0;
//   });
// });

// document.getElementById('salesPeriodSelect')?.addEventListener('change', () => {
//   // Re-fetch reportsData.salesOverview for the selected period, then renderSalesChart().
//   console.log('Sales period changed:', document.getElementById('salesPeriodSelect').value);
// });

// // ---------- quick actions ----------
// document.getElementById('exportReportBtn')?.addEventListener('click', () => {
//   alert('Export Report — wire this up to your backend export endpoint.');
// });
// document.getElementById('qaGenerateSalesReport')?.addEventListener('click', () => {
//   alert('Generate Sales Report (PDF) — wire this up to your backend.');
// });
// document.getElementById('qaExportExcel')?.addEventListener('click', () => {
//   alert('Export to Excel — wire this up to your backend.');
// });
// document.getElementById('qaPrintReport')?.addEventListener('click', () => {
//   window.print();
// });

// renderAll();
document.addEventListener('DOMContentLoaded', function () {
  const $ = (id) => document.getElementById(id);

  const DATA_URL = window.reportsDataUrl || '/admin/reports/data';
  const RESERVATION_URL = window.reportsReservationUrl || '/admin/reservation';
  const PAGE_SIZE = 8;

  const EVENT_LABELS = {
    wedding: 'Wedding', birthday: 'Birthday', corporate: 'Corporate Event',
    debut: 'Debut', christening: 'Christening'
  };
  const COLORS = ['#2f7d3c', '#e0a419', '#3f8ed9', '#7a4fd1', '#d6479b', '#d9534f'];

  if (typeof Chart === 'undefined') {
    console.error('Chart.js did not load. Check the Chart.js <script> tag in reports.blade.php.');
  } else {
    Chart.defaults.font.family = "'Poppins', sans-serif";
  }

  // ------------------------------------------------------------
  // STATE
  // ------------------------------------------------------------
  const state = {
    date_from: '', date_to: '', event_type: '', customer_type: '',
    payment_status: '', status: '', months: '6'
  };
  let rows = [];
  let page = 1;
  const charts = {};

  // ------------------------------------------------------------
  // HELPERS
  // ------------------------------------------------------------
  function esc(v) {
    return String(v ?? '').replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
    });
  }

  function money(v) {
    const n = Number(v || 0);
    return '₱' + n.toLocaleString('en-PH', {
      minimumFractionDigits: Number.isInteger(n) ? 0 : 2,
      maximumFractionDigits: 2
    });
  }

  function setText(id, text) {
    const el = $(id);
    if (el) el.textContent = text;
  }

  function fmtDate(iso) {
    if (!iso) return '';
    const d = new Date(iso + 'T00:00:00');
    return isNaN(d) ? iso : d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
  }

  function toggleEmpty(canvasId, emptyId, hasData) {
    const canvas = $(canvasId);
    const empty = $(emptyId);
    if (canvas) canvas.style.display = hasData ? '' : 'none';
    if (empty) empty.style.display = hasData ? 'none' : '';
  }

  function makeChart(key, canvasId, config) {
    if (typeof Chart === 'undefined') return;
    const canvas = $(canvasId);
    if (!canvas) return;
    if (charts[key]) charts[key].destroy();
    charts[key] = new Chart(canvas, config);
  }

  // ------------------------------------------------------------
  // LOAD DATA
  // ------------------------------------------------------------
  function load() {
    const params = new URLSearchParams();
    Object.keys(state).forEach(function (k) {
      if (state[k]) params.set(k, state[k]);
    });

    fetch(DATA_URL + '?' + params.toString(), {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
      .then(function (res) {
        if (!res.ok) throw new Error('Request failed: ' + res.status);
        return res.json();
      })
      .then(function (d) {
        renderSummary(d.summary);
        renderInsights(d.insights);
        renderCharts(d);
        rows = d.rows || [];
        page = 1;
        renderTable();
      })
      .catch(function (err) {
        console.error('Reports could not be loaded:', err);
      });
  }

  // ------------------------------------------------------------
  // SUMMARY CARDS
  // ------------------------------------------------------------
  function renderSummary(s) {
    setText('statTotalRevenue', money(s.total_revenue));
    setText('statTotalRevenueSub', 'Paid reservations');

    setText('statTotalBookings', s.total_bookings);
    setText('statTotalBookingsSub', 'Excluding cancelled');

    setText('statPendingPayments', money(s.pending_payments));
    setText('statPendingPaymentsSub', 'Unpaid and partial');

    if (s.popular_package) {
      setText('statPopularPackage', s.popular_package.name);
      setText('statPopularPackageSub', s.popular_package.count + ' bookings');
    } else {
      setText('statPopularPackage', '—');
      setText('statPopularPackageSub', '');
    }

    if (s.monthly_growth === null || s.monthly_growth === undefined) {
      setText('statMonthlyGrowth', '—');
      setText('statMonthlyGrowthSub', 'Not enough data');
    } else {
      setText('statMonthlyGrowth', (s.monthly_growth > 0 ? '+' : '') + s.monthly_growth + '%');
      setText('statMonthlyGrowthSub', 'vs last month');
    }

    if (s.repeat_rate === null || s.repeat_rate === undefined) {
      setText('statRepeatRate', '—');
      setText('statRepeatRateSub', '');
    } else {
      setText('statRepeatRate', s.repeat_rate + '%');
      setText('statRepeatRateSub', 'of customers');
    }
  }

  // ------------------------------------------------------------
  // INSIGHTS
  // ------------------------------------------------------------
  function renderInsights(i) {
    if (i.top_package) {
      setText('insightTopPackage', i.top_package.name);
      setText('insightTopPackageSub', i.top_package.count + ' bookings');
    } else {
      setText('insightTopPackage', '—');
      setText('insightTopPackageSub', 'No data yet');
    }

    if (i.best_month) {
      setText('insightBestMonth', i.best_month.month);
      setText('insightBestMonthSub', money(i.best_month.revenue) + ' · ' + i.best_month.bookings + ' bookings');
    } else {
      setText('insightBestMonth', '—');
      setText('insightBestMonthSub', 'No data yet');
    }

    if (i.top_client) {
      setText('insightTopClient', i.top_client.name);
      setText('insightTopClientSub', money(i.top_client.amount) + ' paid');
    } else {
      setText('insightTopClient', '—');
      setText('insightTopClientSub', 'No data yet');
    }

    const list = i.upcoming_high_value || [];
    const sub = $('insightUpcomingSub');
    if (list.length) {
      setText('insightUpcoming', list.length + (list.length === 1 ? ' event' : ' events'));
      if (sub) {
        sub.innerHTML = list.map(function (e) {
          return esc(e.title) + ' · ' + esc(e.date) + ' · ' + money(e.amount);
        }).join('<br>');
      }
    } else {
      setText('insightUpcoming', '—');
      setText('insightUpcomingSub', 'No data yet');
    }
  }

  // ------------------------------------------------------------
  // CHARTS
  // ------------------------------------------------------------
  function renderCharts(d) {
    const monthly = d.monthly || [];
    const labels = monthly.map(function (m) { return m.month; });
    const revenue = monthly.map(function (m) { return m.revenue; });
    const bookings = monthly.map(function (m) { return m.bookings; });

    // Sales Overview
    const hasSales = revenue.some(function (v) { return v > 0; });
    toggleEmpty('salesOverviewChart', 'salesOverviewEmpty', hasSales);
    if (hasSales) {
      makeChart('sales', 'salesOverviewChart', {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{ label: 'Revenue', data: revenue, backgroundColor: '#2f7d3c', borderRadius: 6 }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { callback: function (v) { return '₱' + Number(v).toLocaleString('en-PH'); } }
            }
          }
        }
      });
    }

    // Bookings by Event Type
    const byEvent = d.bookings_by_event || {};
    const eventKeys = Object.keys(byEvent);
    toggleEmpty('bookingsChart', 'bookingsEmpty', eventKeys.length > 0);
    if (eventKeys.length) {
      makeChart('bookings', 'bookingsChart', {
        type: 'doughnut',
        data: {
          labels: eventKeys.map(function (k) { return EVENT_LABELS[k] || k; }),
          datasets: [{ data: eventKeys.map(function (k) { return byEvent[k]; }), backgroundColor: COLORS }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom' } }
        }
      });
    }

    // Payment Status
    const p = d.payment_status || {};
    const paid = (p.paid && p.paid.amount) || 0;
    const partial = (p.partial && p.partial.amount) || 0;
    const unpaid = (p.unpaid && p.unpaid.amount) || 0;
    const total = paid + partial + unpaid;
    const pct = function (v) { return total > 0 ? Math.round((v / total) * 100) : 0; };

    setText('legendPaid', pct(paid) + '% (' + money(paid) + ')');
    setText('legendPartial', pct(partial) + '% (' + money(partial) + ')');
    setText('legendUnpaid', pct(unpaid) + '% (' + money(unpaid) + ')');

    toggleEmpty('paymentStatusChart', 'paymentStatusEmpty', total > 0);
    if (total > 0) {
      makeChart('payment', 'paymentStatusChart', {
        type: 'doughnut',
        data: {
          labels: ['Paid', 'Partial', 'Unpaid'],
          datasets: [{ data: [paid, partial, unpaid], backgroundColor: ['#2f7d3c', '#e0a419', '#d9534f'] }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: { legend: { display: false } }
        }
      });
    }

    // Monthly Performance (bookings = bars, revenue = line)
    const hasPerf = bookings.some(function (v) { return v > 0; }) || hasSales;
    toggleEmpty('monthlyPerformanceChart', 'monthlyPerformanceEmpty', hasPerf);
    if (hasPerf) {
      makeChart('performance', 'monthlyPerformanceChart', {
        data: {
          labels: labels,
          datasets: [
            { type: 'bar', label: 'Bookings', data: bookings, backgroundColor: '#3f8ed9', borderRadius: 6, yAxisID: 'y' },
            { type: 'line', label: 'Revenue', data: revenue, borderColor: '#2f7d3c', backgroundColor: '#2f7d3c', tension: 0.3, yAxisID: 'y1' }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom' } },
          scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            y1: {
              beginAtZero: true,
              position: 'right',
              grid: { drawOnChartArea: false },
              ticks: { callback: function (v) { return '₱' + Number(v).toLocaleString('en-PH'); } }
            }
          }
        }
      });
    }
  }

  // ------------------------------------------------------------
  // TABLE
  // ------------------------------------------------------------
  const STATUS_STYLE = {
    confirmed: 'background:#e5f4e8;color:#1f5c2a;',
    pending: 'background:#fdf3d9;color:#8a6100;',
    cancelled: 'background:#fbe4e2;color:#a8362d;'
  };

  function renderTable() {
    const body = $('reportsTableBody');
    if (!body) return;

    const totalPages = Math.max(1, Math.ceil(rows.length / PAGE_SIZE));
    if (page > totalPages) page = totalPages;
    const start = (page - 1) * PAGE_SIZE;
    const items = rows.slice(start, start + PAGE_SIZE);

    if (!rows.length) {
      body.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No reservations found.</td></tr>';
      setText('reportsTableFooterText', 'Showing 0 of 0 entries');
      renderPagination(1);
      return;
    }

    body.innerHTML = items.map(function (r) {
      const style = STATUS_STYLE[r.status] || '';
      const label = r.status ? r.status.charAt(0).toUpperCase() + r.status.slice(1) : '';
      return '<tr>' +
        '<td>' + esc(r.code) + '</td>' +
        '<td>' + esc(r.event || '—') + '</td>' +
        '<td>' + esc(r.customer) + '</td>' +
        '<td>' + esc(r.package || 'N/A') + '</td>' +
        '<td>' + money(r.amount) + '</td>' +
        '<td><span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;' + style + '">' + esc(label) + '</span></td>' +
        '<td>' + esc(r.date || '—') + '</td>' +
        '<td class="text-center"><a href="' + esc(window.reportsReservationUrl + '?view=' + encodeURIComponent(r.id)) + '" class="btn btn-sm btn-light border" title="View Details"><i class="bi bi-eye"></i></a></td>' +
        '</tr>';
    }).join('');

    setText('reportsTableFooterText',
      'Showing ' + (start + 1) + '-' + Math.min(start + PAGE_SIZE, rows.length) + ' of ' + rows.length + ' entries');
    renderPagination(totalPages);
  }

  function renderPagination(totalPages) {
    const box = $('paginationReport');
    if (!box) return;

    let html = '<button type="button" class="page-btn" data-page="prev"' + (page === 1 ? ' disabled' : '') + '>‹</button>';
    for (let p = 1; p <= totalPages; p++) {
      html += '<button type="button" class="page-btn' + (p === page ? ' active' : '') + '" data-page="' + p + '">' + p + '</button>';
    }
    html += '<button type="button" class="page-btn" data-page="next"' + (page === totalPages ? ' disabled' : '') + '>›</button>';
    box.innerHTML = html;

    box.querySelectorAll('.page-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const v = btn.dataset.page;
        if (v === 'prev') page--;
        else if (v === 'next') page++;
        else page = Number(v);
        renderTable();
      });
    });
  }

  // ------------------------------------------------------------
  // FILTERS
  // ------------------------------------------------------------
  function bindSelect(id, key) {
    const el = $(id);
    if (!el) return;
    el.addEventListener('change', function () {
      state[key] = el.value;
      load();
    });
  }

  bindSelect('eventTypeFilter', 'event_type');
  bindSelect('customerTypeFilter', 'customer_type');
  bindSelect('paymentStatusFilter', 'payment_status');
  bindSelect('paymentStateFilter', 'status');
  bindSelect('salesPeriodSelect', 'months');

  // Date range popover
  const dateBtn = $('dateRangeBtn');
  let pop = null;

  function closePop() {
    if (pop) { pop.remove(); pop = null; }
  }

  function updateDateLabel() {
    if (state.date_from && state.date_to) {
      setText('dateRangeLabel', fmtDate(state.date_from) + ' – ' + fmtDate(state.date_to));
    } else if (state.date_from) {
      setText('dateRangeLabel', 'From ' + fmtDate(state.date_from));
    } else if (state.date_to) {
      setText('dateRangeLabel', 'Until ' + fmtDate(state.date_to));
    } else {
      setText('dateRangeLabel', 'Select date range');
    }
  }

  if (dateBtn) {
    dateBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      if (pop) { closePop(); return; }

      const r = dateBtn.getBoundingClientRect();
      pop = document.createElement('div');
      pop.style.cssText =
        'position:fixed;top:' + (r.bottom + 6) + 'px;left:' + r.left + 'px;z-index:2000;' +
        'background:#fff;border:1px solid #e5e8e6;border-radius:12px;' +
        'box-shadow:0 8px 24px rgba(0,0,0,.12);padding:14px;width:260px;font-size:13px;';
      pop.innerHTML =
        '<label style="display:block;font-weight:600;margin-bottom:4px;">From</label>' +
        '<input type="date" id="rangeFrom" class="form-control form-control-sm mb-2" value="' + esc(state.date_from) + '">' +
        '<label style="display:block;font-weight:600;margin-bottom:4px;">To</label>' +
        '<input type="date" id="rangeTo" class="form-control form-control-sm mb-3" value="' + esc(state.date_to) + '">' +
        '<div class="d-flex gap-2 justify-content-end">' +
        '<button type="button" class="btn btn-sm btn-light border" id="rangeClear">Clear</button>' +
        '<button type="button" class="btn btn-sm btn-success" id="rangeApply">Apply</button>' +
        '</div>';
      document.body.appendChild(pop);
      pop.addEventListener('click', function (ev) { ev.stopPropagation(); });

      pop.querySelector('#rangeApply').addEventListener('click', function () {
        state.date_from = pop.querySelector('#rangeFrom').value;
        state.date_to = pop.querySelector('#rangeTo').value;
        updateDateLabel();
        closePop();
        load();
      });

      pop.querySelector('#rangeClear').addEventListener('click', function () {
        state.date_from = '';
        state.date_to = '';
        updateDateLabel();
        closePop();
        load();
      });
    });
  }

  document.addEventListener('click', closePop);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closePop(); });

  // Reset
  const resetBtn = $('resetFiltersBtn');
  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      state.date_from = ''; state.date_to = ''; state.event_type = '';
      state.customer_type = ''; state.payment_status = ''; state.status = '';
      state.months = '6';

      ['eventTypeFilter', 'customerTypeFilter', 'paymentStatusFilter', 'paymentStateFilter'].forEach(function (id) {
        const el = $(id);
        if (el) el.value = '';
      });
      const period = $('salesPeriodSelect');
      if (period) period.value = '6';

      updateDateLabel();
      load();
    });
  }

  // ------------------------------------------------------------
  // QUICK ACTIONS
  // ------------------------------------------------------------
  $('qaPrintReport')?.addEventListener('click', function () { window.print(); });

  // For now, "PDF" uses the browser: choose "Save as PDF" in the print window.
  $('qaGenerateSalesReport')?.addEventListener('click', function () { window.print(); });

  // "Excel" downloads a CSV that opens in Excel.
  $('qaExportExcel')?.addEventListener('click', function () {
    if (!rows.length) {
      alert('No data to export.');
      return;
    }

    const header = ['ID', 'Event', 'Customer', 'Package', 'Revenue', 'Status', 'Payment', 'Date'];
    const data = rows.map(function (r) {
      return [r.code, r.event, r.customer, r.package || 'N/A', r.amount, r.status, r.payment_status, r.date];
    });

    const csv = [header].concat(data).map(function (row) {
      return row.map(function (v) {
        return '"' + String(v ?? '').replace(/"/g, '""') + '"';
      }).join(',');
    }).join('\n');

    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'lolays-report.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  });

  // ------------------------------------------------------------
  // START
  // ------------------------------------------------------------
  updateDateLabel();
  load();
});