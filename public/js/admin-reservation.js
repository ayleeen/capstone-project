// ============================================================
// LOLAY'S CATERING SERVICES
// ADMIN RESERVATION JAVASCRIPT
// ============================================================

// ------------------------------------------------------------
// RESERVATION DATA FROM LARAVEL
// ------------------------------------------------------------

const reservations = Object.values(window.reservationData || {})
    .sort((a, b) => b.id - a.id);   // latest added on top

// Keep track of the last filtered list
let lastFilteredList = reservations;

// ------------------------------------------------------------
// DOM REFERENCES
// ------------------------------------------------------------

const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const eventTypeFilter = document.getElementById('eventTypeFilter');

const tableBody = document.getElementById('reservationTableBody');
const noResultsMsg = document.getElementById('noResultsMsg');
const tableFooter = document.getElementById('tableFooter');
const paginationControls = document.getElementById('paginationControls');

const statTotal = document.getElementById('statTotal');
const statPending = document.getElementById('statPending');
const statConfirmed = document.getElementById('statConfirmed');
const statCancelled = document.getElementById('statCancelled');
const statSales = document.getElementById('statSales');
const statUpcoming = document.getElementById('statUpcoming');

const newReservationBtn = document.getElementById('newReservationBtn');

const viewModalEl = document.getElementById('viewReservationModal');
const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
const detailsPanelBody = document.getElementById('viewModalBody');
const detailsPanelTitle = document.getElementById('detailsPanelTitle');


// ------------------------------------------------------------
// PAGINATION
// ------------------------------------------------------------

const PAGE_SIZE = 9;
let currentPage = 1;


// ------------------------------------------------------------
// LABELS
// ------------------------------------------------------------

const statusLabels = {
    confirmed: 'Confirmed',
    pending: 'Pending',
    cancelled: 'Cancelled'
};

const paymentLabels = {
    paid: 'Paid',
    unpaid: 'Unpaid',
    partial: 'Partial'
};

const EVENT_TYPE_LABELS = {
    wedding: 'Wedding',
    birthday: 'Birthday',
    corporate: 'Corporate Event',
    debut: 'Debut',
    christening: 'Christening'
};


// ------------------------------------------------------------
// HELPERS
// ------------------------------------------------------------

function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, function (char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char];
    });
}


function fmtMoney(value) {
    return '₱' + Number(value || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}


function formatDateDisplay(dateStr) {

    if (!dateStr) {
        return '—';
    }

    const d = new Date(dateStr + 'T00:00:00');

    if (isNaN(d.getTime())) {
        return esc(dateStr);
    }

    return d.toLocaleDateString('en-PH', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });
}


function formatTimeDisplay(timeStr) {

    if (!timeStr) {
        return '';
    }

    const parts = timeStr.split(':');

    if (parts.length < 2) {
        return timeStr;
    }

    let hour = parseInt(parts[0]);
    const minute = parts[1];

    const period = hour >= 12 ? 'PM' : 'AM';

    hour = hour % 12;

    if (hour === 0) {
        hour = 12;
    }

    return `${hour}:${minute} ${period}`;
}


function getReservation(id) {

    return reservations.find(function (reservation) {
        return String(reservation.id) === String(id);
    });
}


// ------------------------------------------------------------
// STATISTICS
// ------------------------------------------------------------

function renderStats() {

    if (!statTotal) return;

    statTotal.textContent = reservations.length;

    statPending.textContent =
        reservations.filter(r => r.status === 'pending').length;

    statConfirmed.textContent =
        reservations.filter(r => r.status === 'confirmed').length;

    statCancelled.textContent =
        reservations.filter(r => r.status === 'cancelled').length;

    const sales = reservations
        .filter(r => r.status === 'confirmed')
        .reduce(function (sum, r) {
            return sum + Number(r.total_amount || 0);
        }, 0);

    statSales.textContent = fmtMoney(sales);


    // Upcoming events this week
    const now = new Date();

    const startOfWeek = new Date(now);
    startOfWeek.setHours(0, 0, 0, 0);

    const day = startOfWeek.getDay();

    const diff = day === 0 ? -6 : 1 - day;

    startOfWeek.setDate(startOfWeek.getDate() + diff);


    const endOfWeek = new Date(startOfWeek);

    endOfWeek.setDate(startOfWeek.getDate() + 6);
    endOfWeek.setHours(23, 59, 59, 999);


    const upcoming = reservations.filter(function (r) {

        if (!r.start_date) {
            return false;
        }

        if (r.status === 'cancelled') {
            return false;
        }

        const date = new Date(r.start_date + 'T00:00:00');

        return date >= startOfWeek && date <= endOfWeek;
    });


    statUpcoming.textContent = upcoming.length;
}


// ------------------------------------------------------------
// TABLE RENDERING
// ------------------------------------------------------------

function renderRows(list) {

    const totalPages = Math.max(
        1,
        Math.ceil(list.length / PAGE_SIZE)
    );


    if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    if (currentPage < 1) {
        currentPage = 1;
    }


    const start = (currentPage - 1) * PAGE_SIZE;

    const pageItems = list.slice(
        start,
        start + PAGE_SIZE
    );


    if (list.length === 0) {

        tableBody.innerHTML = '';

        paginationControls.innerHTML = '';

        tableFooter.textContent =
            `Showing 0 of ${reservations.length} items`;

        noResultsMsg.textContent =
            reservations.length === 0
                ? 'No reservations found.'
                : 'No reservations match your search/filters.';

        noResultsMsg.classList.remove('d-none');

        renderStats();

        return;
    }


    noResultsMsg.classList.add('d-none');


    const rangeStart = start + 1;

    const rangeEnd = Math.min(
        start + PAGE_SIZE,
        list.length
    );


    tableFooter.textContent =
        `Showing ${rangeStart}-${rangeEnd} of ${list.length} items`;


    tableBody.innerHTML = pageItems.map(function (r) {

        const eventType =
            EVENT_TYPE_LABELS[r.event_type] ||
            r.event_type ||
            '—';


        return `
            <tr data-id="${esc(r.id)}">

                <td>
                    RES-${String(r.id).padStart(4, '0')}
                </td>

                <td>
                    ${esc(r.full_name)}
                </td>

                <td>
                    ${esc(eventType)}
                </td>

                <td>
                    ${formatDateDisplay(r.start_date)}
                </td>

                <td>
                    ${esc(r.package || 'N/A')}
                </td>

                <td>
                    ${esc(r.guests || 0)}
                </td>

                <td>
                    <span class="status-pill ${esc(r.status)}">
                        ${esc(statusLabels[r.status] || r.status)}
                    </span>
                </td>

                <td>
                    <span class="payment-pill ${esc(r.payment_status || 'unpaid')}">
                        ${esc(paymentLabels[r.payment_status] || r.payment_status || 'Unpaid')}
                    </span>
                </td>

                <td>
                    ${fmtMoney(r.total_amount)}
                </td>

                <td>

                    <div class="action-icons">

                        <button
                            type="button"
                            class="view-btn"
                            data-id="${esc(r.id)}"
                            title="View">

                            <i class="fa-solid fa-eye"></i>

                        </button>


                        <button
                            type="button"
                            class="edit-btn"
                            data-id="${esc(r.id)}"
                            title="Edit">

                            <i class="fa-solid fa-pen"></i>

                        </button>


                        <div class="action-menu-wrap">

                            <button
                                type="button"
                                class="more-btn"
                                data-id="${esc(r.id)}"
                                title="More">

                                <i class="fa-solid fa-ellipsis-vertical"></i>

                            </button>


                            <div class="action-menu">

                                <button
                                    type="button"
                                    class="view-details-btn"
                                    data-id="${esc(r.id)}">

                                    <i class="fa-solid fa-eye"></i>
                                    View Details

                                </button>


                                <button
                                    type="button"
                                    class="edit-reservation-btn"
                                    data-id="${esc(r.id)}">

                                    <i class="fa-solid fa-pen"></i>
                                    Edit Reservation

                                </button>


                                <button
                                    type="button"
                                    class="delete-reservation-btn danger"
                                    data-id="${esc(r.id)}">

                                    <i class="fa-solid fa-trash"></i>
                                    Delete Reservation

                                </button>

                            </div>

                        </div>

                    </div>

                </td>

            </tr>
        `;

    }).join('');


    attachRowActionHandlers();

    renderPagination(totalPages);

    renderStats();
}


// ------------------------------------------------------------
// PAGINATION
// ------------------------------------------------------------

function goToPage(page) {

    currentPage = page;

    applyFilters(false);
}


function renderPagination(totalPages) {

    if (totalPages <= 1) {

        paginationControls.innerHTML = '';

        return;
    }


    let buttons = '';


    buttons += `
        <button
            class="page-btn"
            data-page="prev"
            ${currentPage === 1 ? 'disabled' : ''}
            title="Previous">

            <i class="fa-solid fa-chevron-left"></i>

        </button>
    `;


    const pages = [];

    pages.push(1);


    for (
        let p = currentPage - 1;
        p <= currentPage + 1;
        p++
    ) {

        if (p > 1 && p < totalPages) {
            pages.push(p);
        }
    }


    if (totalPages > 1) {
        pages.push(totalPages);
    }


    const uniquePages =
        [...new Set(pages)].sort((a, b) => a - b);


    let previousPage = 0;


    uniquePages.forEach(function (page) {

        if (page - previousPage > 1) {

            buttons += `
                <span class="page-ellipsis">
                    &hellip;
                </span>
            `;
        }


        buttons += `
            <button
                class="page-btn ${page === currentPage ? 'active' : ''}"
                data-page="${page}">

                ${page}

            </button>
        `;


        previousPage = page;

    });


    buttons += `
        <button
            class="page-btn"
            data-page="next"
            ${currentPage === totalPages ? 'disabled' : ''}
            title="Next">

            <i class="fa-solid fa-chevron-right"></i>

        </button>
    `;


    paginationControls.innerHTML = buttons;


    paginationControls
        .querySelectorAll('.page-btn')
        .forEach(function (btn) {

            btn.addEventListener('click', function () {

                const page = btn.dataset.page;


                if (page === 'prev') {

                    goToPage(currentPage - 1);

                } else if (page === 'next') {

                    goToPage(currentPage + 1);

                } else {

                    goToPage(Number(page));

                }

            });

        });
}


// ------------------------------------------------------------
// FILTERING
// ------------------------------------------------------------

function applyFilters(resetPage = true) {

    const q = searchInput.value.trim().toLowerCase();
    const status = statusFilter.value;
    const eventType = eventTypeFilter.value;

    const filtered = reservations.filter(function (r) {

        const eventTypeText = String(
            EVENT_TYPE_LABELS[r.event_type] || r.event_type || ''
        );

        const resId = 'RES-' + String(r.id).padStart(4, '0');

        const haystack = [
            resId,
            r.id,
            r.full_name,
            r.event_title,
            eventTypeText,
            r.package,
            r.email,
            r.phone,
            r.location
        ].join(' ').toLowerCase();

        const matchesSearch = !q || haystack.includes(q);
        const matchesStatus = !status || r.status === status;
        const matchesEventType = !eventType || r.event_type === eventType;

        return matchesSearch && matchesStatus && matchesEventType;
    });

    lastFilteredList = filtered;

    if (resetPage) {
        currentPage = 1;
    }

    renderRows(filtered);
}


searchInput?.addEventListener(
    'input',
    () => applyFilters(true)
);

statusFilter?.addEventListener(
    'change',
    () => applyFilters(true)
);

eventTypeFilter?.addEventListener(
    'change',
    () => applyFilters(true)
);


// ------------------------------------------------------------
// ROW ACTIONS
// ------------------------------------------------------------

function attachRowActionHandlers() {

    document.querySelectorAll('.view-btn')
        .forEach(function (btn) {

            btn.addEventListener('click', function () {

                openDetailsPanel(btn.dataset.id);

            });

        });


    document.querySelectorAll('.view-details-btn')
        .forEach(function (btn) {

            btn.addEventListener('click', function () {

                openDetailsPanel(btn.dataset.id);

            });

        });


    document.querySelectorAll('.edit-btn')
        .forEach(function (btn) {

            btn.addEventListener('click', function () {

                openEditPanel(btn.dataset.id);

            });

        });


    document.querySelectorAll('.edit-reservation-btn')
        .forEach(function (btn) {

            btn.addEventListener('click', function () {

                openEditPanel(btn.dataset.id);

            });

        });


    document.querySelectorAll('.more-btn')
        .forEach(function (btn) {

            btn.addEventListener('click', function (e) {

                e.stopPropagation();


                const wrapper =
                    btn.closest('.action-menu-wrap');

                const menu =
                    wrapper.querySelector('.action-menu');


                const wasOpen =
                    menu.classList.contains('open');


                closeAllActionMenus();


                if (!wasOpen) {
                    menu.classList.add('open');
                }

            });

        });


    document.querySelectorAll('.delete-reservation-btn')
        .forEach(function (btn) {

            btn.addEventListener('click', function () {

                openDeleteConfirm(btn.dataset.id);

            });

        });
}


function closeAllActionMenus() {

    document
        .querySelectorAll('.action-menu.open')
        .forEach(function (menu) {

            menu.classList.remove('open');

        });
}


document.addEventListener(
    'click',
    closeAllActionMenus
);


// ------------------------------------------------------------
// DETAILS PANEL
// ------------------------------------------------------------

function renderDetailsPanel(r) {

    const eventType =
        EVENT_TYPE_LABELS[r.event_type] ||
        r.event_type ||
        '—';


    const paymentStatus =
        paymentLabels[r.payment_status] ||
        r.payment_status ||
        'Unpaid';
        
    const proofUrl = r.proof_of_payment || '';

    const proofHtml = proofUrl
        ? (/\.pdf$/i.test(proofUrl)
            ? `<a href="${esc(proofUrl)}" target="_blank" rel="noopener">View PDF</a>`
            : `<a href="${esc(proofUrl)}" target="_blank" rel="noopener"><img src="${esc(proofUrl)}" alt="Proof of payment" style="max-width:100%; max-height:340px; border-radius:10px;"></a>`)
        : `<div class="detail-value light">No proof of payment uploaded.</div>`;

    detailsPanelBody.innerHTML = `

        <div class="detail-grid">

            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Reservation ID
                    </div>

                    <div class="detail-value">
                        RES-${String(r.id).padStart(4, '0')}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-regular fa-user"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Client Name
                    </div>

                    <div class="detail-value">
                        ${esc(r.full_name)}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-solid fa-champagne-glasses"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Event Type
                    </div>

                    <div class="detail-value">
                        ${esc(eventType)}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-solid fa-heading"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Event Title
                    </div>

                    <div class="detail-value">
                        ${esc(r.event_title || '—')}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-solid fa-phone"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Contact No.
                    </div>

                    <div class="detail-value">
                        ${esc(r.phone || '—')}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-regular fa-clock"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Date & Time
                    </div>

                    <div class="detail-value">

                        ${formatDateDisplay(r.start_date)}

                        ${
                            r.start_time
                                ? ' &middot; ' +
                                  esc(formatTimeDisplay(r.start_time))
                                : ''
                        }

                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-regular fa-envelope"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Email Address
                    </div>

                    <div class="detail-value">
                        ${esc(r.email || '—')}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Event Location
                    </div>

                    <div class="detail-value">
                        ${esc(r.location || '—')}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div>
                    <div class="detail-label">
                        No. of Guests
                    </div>

                    <div class="detail-value">
                        ${esc(r.guests || 0)} pax
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Package
                    </div>

                    <div class="detail-value">
                        ${esc(r.package || '—')}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Total Amount
                    </div>

                    <div class="detail-value">
                        ${fmtMoney(r.total_amount)}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-regular fa-credit-card"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Payment Status
                    </div>

                    <div class="detail-value">
                        ${esc(paymentStatus)}
                    </div>
                </div>
            </div>


            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>

                <div>
                    <div class="detail-label">
                        Reservation Status
                    </div>

                    <div class="detail-value">
                        ${esc(statusLabels[r.status] || r.status)}
                    </div>
                </div>
            </div>

        </div>


        <div class="detail-note">

            <div class="detail-icon">
                <i class="fa-regular fa-note-sticky"></i>
            </div>

            <div>

                <div class="detail-label">
                    Note
                </div>

                <div class="detail-value light">
                    ${esc(r.notes || 'No additional notes.')}
                </div>

            </div>

        </div>

            <div class="detail-note" style="display:block;">
       <div class="detail-label" style="margin-bottom:8px;">Proof of Payment</div>
       ${proofHtml}
   </div>               
        <div class="details-actions">

            <button
                type="button"
                class="btn-detail-action primary"
                id="detailEditBtn">

                <i class="fa-solid fa-pen"></i>
                Edit Reservation

            </button>


            <button
                type="button"
                class="btn-detail-action"
                id="detailPrintBtn">

                <i class="fa-solid fa-print"></i>
                Print Details

            </button>
                        <button
                type="button"
                class="btn-detail-action"
                id="detailSendConfirmationBtn">
                <i class="fa-solid fa-paper-plane"></i>
                Send Confirmation
            </button>

            ${
                r.status !== 'cancelled'
                    ? `
                        <button
                            type="button"
                            class="btn-detail-action danger"
                            id="detailCancelBtn">

                            <i class="fa-solid fa-ban"></i>
                            Cancel Reservation

                        </button>
                    `
                    : ''
            }



        </div>
    `;


    document
        .getElementById('detailEditBtn')
        ?.addEventListener('click', function () {

            openEditPanel(r.id);

        });


    document
        .getElementById('detailPrintBtn')
        ?.addEventListener('click', function () {

            window.print();

        });

    document
    .getElementById('detailSendConfirmationBtn')
    ?.addEventListener('click', function () {

        if (!r.sendConfirmationUrl) {
            alert('Confirmation URL not found.');
            return;
        }

        const confirmed = confirm(
            `Send confirmation email to ${r.email}?`
        );

        if (!confirmed) {
            return;
        }

        const form = document.createElement('form');

        form.method = 'POST';
        form.action = r.sendConfirmationUrl;

        const csrf = document.createElement('input');

        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = window.csrfToken;

        form.appendChild(csrf);

        document.body.appendChild(form);

        form.submit();

    });


    document
        .getElementById('detailCancelBtn')
        ?.addEventListener('click', function () {

            openCancelConfirm(r.id);

        });

}


function openDetailsPanel(id) {

    const r = getReservation(id);


    if (!r) {

        console.error(
            'Reservation not found:',
            id,
            reservations
        );

        return;
    }


    detailsPanelTitle.textContent =
        'Reservation Details';


    renderDetailsPanel(r);


   viewModal.show();
}


function closeDetailsPanel() {

    viewModal.hide();

}


document
    .getElementById('closeDetailsPanel')
    ?.addEventListener(
        'click',
        closeDetailsPanel
    );


// ------------------------------------------------------------
// EDIT RESERVATION
// ------------------------------------------------------------

function renderEditPanel(r) {

    detailsPanelBody.innerHTML = `

        <form
            id="editReservationForm"
            method="POST"
            action="${esc(r.updateUrl)}">

            <input
                type="hidden"
                name="_token"
                value="${esc(window.csrfToken)}">

            <input
                type="hidden"
                name="_method"
                value="PUT">


            <div class="edit-section-title">
                Client Information
            </div>


            <div class="edit-field-grid">

                <div class="edit-field">

                    <label>
                        Full Name
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="full_name"
                        value="${esc(r.full_name || '')}"
                        required>

                </div>


                <div class="edit-field">

                    <label>
                        Email Address
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        name="email"
                        value="${esc(r.email || '')}"
                        required>

                </div>


                <div class="edit-field">

                    <label>
                        Contact Number
                    </label>

                    <input
                        type="tel"
                        class="form-control"
                        name="phone"
                        value="${esc(r.phone || '')}"
                        required>

                </div>

            </div>


            <div class="edit-section-title">
                Event Details
            </div>


            <div class="edit-field-grid">

                <div class="edit-field">

                    <label>
                        Event Type
                    </label>

                    <select
                        class="form-select"
                        name="event_type"
                        required>

                        <option value="wedding"
                            ${r.event_type === 'wedding' ? 'selected' : ''}>
                            Wedding
                        </option>

                        <option value="birthday"
                            ${r.event_type === 'birthday' ? 'selected' : ''}>
                            Birthday
                        </option>

                        <option value="corporate"
                            ${r.event_type === 'corporate' ? 'selected' : ''}>
                            Corporate Event
                        </option>

                        <option value="debut"
                            ${r.event_type === 'debut' ? 'selected' : ''}>
                            Debut
                        </option>

                        <option value="christening"
                            ${r.event_type === 'christening' ? 'selected' : ''}>
                            Christening
                        </option>

                    </select>

                </div>


                <div class="edit-field">

                    <label>
                        Event Title
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="event_title"
                        value="${esc(r.event_title || '')}"
                        required>

                </div>


                <div class="edit-field">

                    <label>
                        Start Date
                    </label>

                    <input
                        type="date"
                        class="form-control"
                        name="start_date"
                        value="${esc(r.start_date || '')}"
                        required>

                </div>


                <div class="edit-field">

                    <label>
                        Start Time
                    </label>

                    <input
                        type="time"
                        class="form-control"
                        name="start_time"
                        value="${esc(r.start_time || '')}">

                </div>


                <div class="edit-field">

                    <label>
                        End Date
                    </label>

                    <input
                        type="date"
                        class="form-control"
                        name="end_date"
                        value="${esc(r.end_date || '')}">

                </div>


                <div class="edit-field">

                    <label>
                        End Time
                    </label>

                    <input
                        type="time"
                        class="form-control"
                        name="end_time"
                        value="${esc(r.end_time || '')}">

                </div>


                <div class="edit-field full">

                    <label>
                        Location / Venue
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="location"
                        value="${esc(r.location || '')}">

                </div>


                <div class="edit-field">

                    <label>
                        Number of Guests
                    </label>

                    <input
                        type="number"
                        min="1"
                        class="form-control"
                        name="guests"
                        value="${esc(r.guests || '')}"
                        required>

                </div>


                <div class="edit-field">

                    <label>
                        Status
                    </label>

                    <select
                        class="form-select"
                        name="status"
                        required>

                        <option value="pending"
                            ${r.status === 'pending' ? 'selected' : ''}>
                            Pending
                        </option>

                        <option value="confirmed"
                            ${r.status === 'confirmed' ? 'selected' : ''}>
                            Confirmed
                        </option>

                        <option value="cancelled"
                            ${r.status === 'cancelled' ? 'selected' : ''}>
                            Cancelled
                        </option>

                    </select>

                </div>

            </div>


            <div class="edit-section-title">
                Additional Options
            </div>


            <div class="edit-field-grid">

                <div class="edit-field">

                    <label>
                        Package
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="package"
                        value="${esc(r.package || '')}">

                </div>


                <div class="edit-field">

                    <label>
                        Payment Status
                    </label>

                    <select
                        class="form-select"
                        name="payment_status">

                        <option value="unpaid"
                            ${r.payment_status === 'unpaid' ? 'selected' : ''}>
                            Unpaid
                        </option>

                        <option value="partial"
                            ${r.payment_status === 'partial' ? 'selected' : ''}>
                            Partial
                        </option>

                        <option value="paid"
                            ${r.payment_status === 'paid' ? 'selected' : ''}>
                            Paid
                        </option>

                    </select>

                </div>


                <div class="edit-field">

                    <label>
                        Total Amount
                    </label>

                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        class="form-control"
                        name="total_amount"
                        value="${esc(r.total_amount || 0)}">

                </div>

            </div>


            <div class="edit-section-title">
                Notes
            </div>


            <div class="edit-field full">

                <textarea
                    class="form-control"
                    name="notes"
                    rows="4">${esc(r.notes || '')}</textarea>

            </div>


            <div class="edit-panel-actions">

                <button
                    type="button"
                    class="btn-cancel-modal"
                    id="editCancelBtn">

                    Cancel

                </button>


                <button
                    type="submit"
                    class="btn-new-reservation">

                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Changes

                </button>

            </div>

        </form>
    `;


    document
        .getElementById('editCancelBtn')
        ?.addEventListener(
            'click',
            function () {

                openDetailsPanel(r.id);

            }
        );


    document
        .getElementById('editReservationForm')
        ?.addEventListener(
            'submit',
            function () {

                // Normal Laravel form submission.
                // The database will be updated by ReservationController.

            }
        );
}


function openEditPanel(id) {

    const r = getReservation(id);


    if (!r) {

        console.error(
            'Reservation not found:',
            id
        );

        return;
    }


    detailsPanelTitle.textContent =
        'Edit Reservation';


    renderEditPanel(r);


   viewModal.show();
}


// ------------------------------------------------------------
// EXPORT
// ------------------------------------------------------------

document
    .getElementById('exportBtn')
    ?.addEventListener(
        'click',
        function () {

            if (lastFilteredList.length === 0) {

                alert('No reservations to export.');

                return;
            }


            const header = [
                'ID',
                'Client',
                'Event Type',
                'Date',
                'Package',
                'Guest',
                'Status',
                'Payment',
                'Total Amount'
            ];


            const dataRows =
                lastFilteredList.map(function (r) {

                    return [
                        `RES-${String(r.id).padStart(4, '0')}`,
                        r.full_name,
                        EVENT_TYPE_LABELS[r.event_type] || r.event_type,
                        r.start_date,
                        r.package || 'N/A',
                        r.guests,
                        statusLabels[r.status] || r.status,
                        paymentLabels[r.payment_status] || r.payment_status,
                        r.total_amount || 0
                    ];

                });


            const csv = [
                header,
                ...dataRows
            ]
            .map(function (row) {

                return row
                    .map(function (value) {

                        return `"${String(value ?? '').replace(/"/g, '""')}"`;

                    })
                    .join(',');

            })
            .join('\n');


            const blob =
                new Blob(
                    [csv],
                    { type: 'text/csv;charset=utf-8;' }
                );


            const url =
                URL.createObjectURL(blob);


            const a =
                document.createElement('a');


            a.href = url;

            a.download =
                'reservations-export.csv';


            document.body.appendChild(a);

            a.click();

            a.remove();

            URL.revokeObjectURL(url);

        }
    );


// ------------------------------------------------------------
// DELETE / CANCEL MODALS
// ------------------------------------------------------------

const deleteConfirmModalEl =
    document.getElementById('deleteConfirmModal');

const deleteConfirmModal =
    deleteConfirmModalEl
        ? new bootstrap.Modal(deleteConfirmModalEl)
        : null;


const cancelConfirmModalEl =
    document.getElementById('cancelConfirmModal');

const cancelConfirmModal =
    cancelConfirmModalEl
        ? new bootstrap.Modal(cancelConfirmModalEl)
        : null;


let pendingDeleteId = null;
let pendingCancelId = null;


// ------------------------------------------------------------
// AVATAR
// ------------------------------------------------------------

function avatarUrlFor(r) {

    return `https://i.pravatar.cc/100?u=${encodeURIComponent(r.id)}`;

}


// ------------------------------------------------------------
// DELETE
// ------------------------------------------------------------

function openDeleteConfirm(id) {

    const r = getReservation(id);


    if (!r) return;


    pendingDeleteId = id;


    document.getElementById(
        'deleteClientAvatar'
    ).src = avatarUrlFor(r);


    document.getElementById(
        'deleteClientName'
    ).textContent = r.full_name;


    document.getElementById(
        'deleteClientEmail'
    ).textContent = r.email || '—';


    document.getElementById(
        'deleteClientPhone'
    ).textContent = r.phone || '';


    deleteConfirmModal?.show();
}


document
    .getElementById('confirmDeleteBtn')
    ?.addEventListener(
        'click',
        function () {

            if (!pendingDeleteId) return;


            const r =
                getReservation(pendingDeleteId);


            if (!r) return;


            const form =
                document.getElementById('deleteForm');


            form.action =
                r.deleteUrl;


            form.submit();

        }
    );


// ------------------------------------------------------------
// CANCEL
// ------------------------------------------------------------

function openCancelConfirm(id) {

    const r = getReservation(id);


    if (!r) return;


    pendingCancelId = id;


    document.getElementById(
        'cancelClientAvatar'
    ).src = avatarUrlFor(r);


    document.getElementById(
        'cancelClientName'
    ).textContent = r.full_name;


    document.getElementById(
        'cancelClientEmail'
    ).textContent = r.email || '—';


    document.getElementById(
        'cancelClientPhone'
    ).textContent = r.phone || '';


    cancelConfirmModal?.show();
}


document
    .getElementById('confirmCancelBtn')
    ?.addEventListener(
        'click',
        function () {

            if (!pendingCancelId) return;


            const r =
                getReservation(pendingCancelId);


            if (!r) return;


            const form =
                document.getElementById('cancelForm');


            form.action =
                r.cancelUrl;


            form.submit();

        }
    );


// ------------------------------------------------------------
// NEW RESERVATION
// ------------------------------------------------------------


const newReservationModalEl = document.getElementById('newReservationModal');
const newReservationModal = newReservationModalEl
    ? new bootstrap.Modal(newReservationModalEl)
    : null;

if (!newReservationBtn) {
    console.error('Button #newReservationBtn not found on this page.');
}
if (!newReservationModalEl) {
    console.error('Modal #newReservationModal not found. Check the modal id in your Blade file.');
}

function openNewReservation() {
    document.getElementById('newReservationForm')?.reset();
    newReservationModal?.show();
}

newReservationBtn?.addEventListener('click', openNewReservation);

// Opens automatically from the dashboard "New Reservation" quick action
if (new URLSearchParams(window.location.search).get('new') === '1') {
    openNewReservation();
}


// IMPORTANT:
// The New Reservation form is submitted normally to Laravel.
// We DO NOT preventDefault() here.
//
// This means the reservation is actually saved to MySQL.


// ------------------------------------------------------------
// INITIALIZE
// ------------------------------------------------------------

renderStats();

applyFilters();