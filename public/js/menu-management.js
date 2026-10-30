document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');


    /*
    =========================================================
    SEARCH / FILTER / PAGINATION
    =========================================================
    */

    if (form && input) {

        let timer;
        let controller;


        function searchUrl() {

            const params = new URLSearchParams(
                new FormData(form)
            );

            return form.action + '?' + params.toString();
        }


        async function load(url) {

            if (controller) {
                controller.abort();
            }

            controller = new AbortController();


            try {

                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: controller.signal
                });


                const html = await res.text();


                const doc = new DOMParser()
                    .parseFromString(html, 'text/html');


                /*
                Replace table and tabs
                */

                ['.table-card', '.menu-tabs'].forEach(function (sel) {

                    const current = document.querySelector(sel);

                    const fresh = doc.querySelector(sel);


                    if (current && fresh) {

                        current.innerHTML = fresh.innerHTML;

                    }

                });


                history.replaceState(
                    null,
                    '',
                    url
                );


            } catch (err) {

                if (err.name !== 'AbortError') {

                    console.error(err);

                }

            }

        }


        /*
        LIVE SEARCH
        */

        input.addEventListener('input', function () {

            clearTimeout(timer);


            timer = setTimeout(function () {

                load(searchUrl());

            }, 250);

        });


        /*
        ENTER / SUBMIT
        */

        form.addEventListener('submit', function (e) {

            e.preventDefault();

            clearTimeout(timer);

            load(searchUrl());

        });

        /*
        CATEGORY / STATUS / SORT — AJAX FILTERING
        */

        ['categoryFilter', 'statusFilter', 'sortFilter'].forEach(function (id) {
            const filter = document.getElementById(id);

            if (filter) {
                filter.addEventListener('change', function () {
                    clearTimeout(timer);
                    load(searchUrl());
                });
            }
        });


        /*
        PAGINATION
        */

        document.addEventListener('click', function (e) {

            const link = e.target.closest(
                '.table-card .custom-pagination a'
            );


            if (!link) {
                return;
            }


            e.preventDefault();

            load(link.href);

        });

    }



    /*
    =========================================================
    VIEW MENU  (opens a pop-up modal)
    =========================================================
    */

    // ---- Pop-up styles (added once) ----
    if (!document.getElementById('menuViewModalStyles')) {

        const st = document.createElement('style');

        st.id = 'menuViewModalStyles';

        st.textContent = `
            .mvp-content{ border:none; border-radius:18px; overflow:hidden; position:relative; }
            .mvp-close{
                position:absolute; top:12px; right:12px; z-index:10;
                width:34px; height:34px; border:none; border-radius:50%;
                background:rgba(255,255,255,.92); color:#3a3f47; cursor:pointer;
                display:flex; align-items:center; justify-content:center;
                box-shadow:0 2px 8px rgba(0,0,0,.15);
            }
            .mvp-close:hover{ background:#fff; }
            .mvp-img-wrap{ position:relative; width:100%; height:240px; background:#f1f3f2; }
            .mvp-img{ width:100%; height:100%; object-fit:cover; display:block; }
            .mvp-status{
                position:absolute; left:16px; bottom:14px;
                font-size:11px; font-weight:700; padding:5px 14px; border-radius:999px;
                display:inline-flex; align-items:center; gap:6px;
            }
            .mvp-status.active{ background:#d6f5d6; color:#256330; }
            .mvp-status.inactive{ background:#e9ecef; color:#5b6472; }
            .mvp-body{ padding:20px 26px 8px; }
            .mvp-top{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:8px; }
            .mvp-cat{ background:#e6f4e8; color:#256330; font-size:11px; font-weight:700; padding:4px 12px; border-radius:999px; }
            .mvp-id{ font-size:11px; color:#8a8f98; font-weight:600; }
            .mvp-name{ margin:0 0 4px; font-size:21px; font-weight:700; color:#1c1c1c; }
            .mvp-price{ font-size:20px; font-weight:700; color:#2f7d3c; margin-bottom:12px; }
            .mvp-desc{ font-size:13px; color:#3a3f47; line-height:1.6; margin:0 0 6px; }
            .mvp-footer{ display:flex; justify-content:flex-end; gap:10px; padding:14px 26px 22px; }
            .mvp-btn-close{
                border:1px solid #ddd; background:#fff; color:#3a3f47;
                font-size:13px; font-weight:600; border-radius:10px; padding:10px 22px; cursor:pointer;
            }
            .mvp-btn-close:hover{ background:#f4f4f4; }
            .mvp-btn-edit{
                border:1px solid #2f7d3c; background:#2f7d3c; color:#fff;
                font-size:13px; font-weight:700; border-radius:10px; padding:10px 22px; cursor:pointer;
                display:inline-flex; align-items:center; gap:8px; text-decoration:none;
            }
            .mvp-btn-edit:hover{ background:#256330; border-color:#256330; color:#fff; }
        `;

        document.head.appendChild(st);
    }


    // ---- Pop-up HTML (added once) ----
    if (!document.getElementById('menuViewModal')) {

        document.body.insertAdjacentHTML('beforeend', `
            <div class="modal fade" id="menuViewModal" tabindex="-1" aria-labelledby="mvpName" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
                    <div class="modal-content mvp-content">

                        <button type="button" class="mvp-close" data-bs-dismiss="modal" aria-label="Close menu details">
                            <i class="bi bi-x-lg"></i>
                        </button>

                        <div class="mvp-img-wrap">
                            <img id="mvpImg" class="mvp-img" src="" alt="">
                            <span class="mvp-status" id="mvpStatus"></span>
                        </div>

                        <div class="mvp-body">
                            <div class="mvp-top">
                                <span class="mvp-cat" id="mvpCat"></span>
                                <span class="mvp-id" id="mvpId"></span>
                            </div>
                            <h2 class="mvp-name" id="mvpName"></h2>
                            <div class="mvp-price" id="mvpPrice"></div>
                            <p class="mvp-desc" id="mvpDesc"></p>
                        </div>

                        <div class="mvp-footer">
                            
                            <a href="#" class="mvp-btn-edit" id="mvpEdit">
                                <i class="bi bi-pencil-fill"></i> Edit Menu
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        `);
    }


    const menuViewModalEl = document.getElementById('menuViewModal');

    const menuViewModal = new bootstrap.Modal(menuViewModalEl);


    function openMenuModal(row) {

        const d = row.dataset;

        const active = d.active === '1';


        const img = document.getElementById('mvpImg');

        img.src = d.image || '';
        img.alt = d.name || '';
        img.style.visibility = 'visible';
        img.onerror = function () {
            this.style.visibility = 'hidden';
        };


        const status = document.getElementById('mvpStatus');

        status.className = 'mvp-status ' + (active ? 'active' : 'inactive');

        status.innerHTML = active
            ? '<i class="bi bi-check-circle-fill"></i> Active'
            : '<i class="bi bi-x-circle-fill"></i> Inactive';


        document.getElementById('mvpCat').textContent =
            d.category || 'No Category';

        document.getElementById('mvpId').textContent =
            'MN-' + String(d.id || '').padStart(3, '0');

        document.getElementById('mvpName').textContent =
            d.name || 'Unnamed Menu';

        document.getElementById('mvpPrice').textContent =
            '₱' + Number(d.price || 0).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        document.getElementById('mvpDesc').textContent =
            d.description || 'No description provided for this menu item.';

        document.getElementById('mvpEdit').href =
            d.editUrl || '#';


        menuViewModal.show();
    }


    // Delegated, so it keeps working after live search / filters / pagination.
    document.addEventListener('click', function (e) {

        const viewButton = e.target.closest('.js-menu-view');

        if (!viewButton) {
            return;
        }

        const row = viewButton.closest('tr');

        if (!row) {
            return;
        }

        openMenuModal(row);

    });



    /*
    =========================================================
    MANAGE CATEGORY
    =========================================================
    */

    document
        .getElementById('manageCategoryBtn')
        ?.addEventListener('click', function () {

            alert(
                'Manage Category — hindi pa ito naka-connect.'
            );

        });

});