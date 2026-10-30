document.addEventListener('DOMContentLoaded', () => {

  // ---- Logged-in header: profile icon + name + notification bell ----
  // Reads the logged-in client saved by your login page. Match USER_KEY to
  // whatever your login saves, e.g.
  //   localStorage.setItem('user', JSON.stringify({ name:'Juan Dela Cruz', photo:'', notifications:[] }));
  // Remove that key on logout to bring back the Login/Register button.
  const USER_KEY = 'user';

  (function initUserHeader() {
    const loginBtn = document.getElementById('loginBtn');
    const userArea = document.getElementById('userArea');
    if (!loginBtn || !userArea) return;

    let user = null;
    try {
      const raw = sessionStorage.getItem(USER_KEY) || localStorage.getItem(USER_KEY);
      user = raw ? JSON.parse(raw) : null;
    } catch (e) { user = null; }

    if (!user) return;   // logged out: keep Login/Register

    loginBtn.hidden = true;
    userArea.hidden = false;

    const fullName = user.name || [user.firstName, user.lastName].filter(Boolean).join(' ') || 'My Account';
    document.getElementById('userName').textContent = fullName;

    if (user.photo) {
      const av = userArea.querySelector('.user-avatar');
      av.textContent = '';
      const img = document.createElement('img');
      img.src = user.photo;
      img.alt = '';
      av.appendChild(img);
    }

    // Notifications: array of strings or { text, time, url }
    const list = Array.isArray(user.notifications) ? user.notifications : [];
    const badge = document.getElementById('notifBadge');
    const menu  = document.getElementById('notifMenu');
    const btn   = document.getElementById('notifBtn');

    if (list.length) {
      badge.textContent = list.length > 99 ? '99+' : list.length;
      badge.hidden = false;
      menu.querySelector('.notif-empty').remove();
      list.forEach(n => {
        const item = document.createElement('a');
        item.className = 'notif-item';
        item.href = (n && n.url) || '#';
        item.textContent = typeof n === 'string' ? n : (n.text || '');
        if (n && n.time) {
          const t = document.createElement('small');
          t.textContent = n.time;
          item.appendChild(t);
        }
        menu.appendChild(item);
      });
    }

    // Profile dropdown (Settings: Profile Settings, Payment History, Security, Log out)
    const pBtn  = document.getElementById('profileBtn');
    const pMenu = document.getElementById('profileMenu');

    function setMenu(open) {
      menu.hidden = !open;
      btn.setAttribute('aria-expanded', String(open));
      if (open) setProfile(false);
    }
    function setProfile(open) {
      pMenu.hidden = !open;
      pBtn.setAttribute('aria-expanded', String(open));
      if (open) { menu.hidden = true; btn.setAttribute('aria-expanded', 'false'); }
    }
    btn.addEventListener('click', e => { e.stopPropagation(); setMenu(menu.hidden); });
    pBtn.addEventListener('click', e => { e.stopPropagation(); setProfile(pMenu.hidden); });
    document.addEventListener('click', e => {
      if (!menu.contains(e.target)) setMenu(false);
      if (!pMenu.contains(e.target)) setProfile(false);
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') { setMenu(false); setProfile(false); }
    });
  })();

(function menuSelectionFlow () {
  // const INCLUDED_HTML = `
  //   <section class="cm-course">
  //     <div class="cm-course-head" style="cursor:default">
  //       <span class="cm-course-name"><i class="fa-solid fa-bowl-rice"></i> Rice</span>
  //       <span class="cm-course-req ok"><i class="bi bi-check-circle-fill"></i> Included</span>
  //     </div>
  //     <div class="custom-menu-item"><span class="cmi-name">Steamed Rice</span></div>
  //   </section>
  //   <section class="cm-course">
  //     <div class="cm-course-head" style="cursor:default">
  //       <span class="cm-course-name"><i class="fa-solid fa-glass-water"></i> Purified Water</span>
  //       <span class="cm-course-req ok"><i class="bi bi-check-circle-fill"></i> Included</span>
  //     </div>
  //     <div class="custom-menu-item"><span class="cmi-name">Purified Drinking Water</span></div>
  //   </section>`;

  // function renderCustomMenu(){
  //   els.custom.innerHTML = COURSES.map((c, i) => {
  //     // ... existing code ...
  //   }).join('') + INCLUDED_HTML;
  // }
  // =========================================================
  // Settings
  // =========================================================
  const STORAGE_KEY = 'menuSelection';        // the Confirmation step can read this from sessionStorage
 const NEXT_URL   = window.NEXT_URL;
const CANCEL_URL = window.CANCEL_URL;

  // The courses, in the order the customer has to choose them.
  // min / max = how many items the customer must / may pick for that course.
  // Change the numbers here to change the rules.
const COURSES = [
  { id: 'pasta',     label: 'Pasta',     unit: 'pasta',     units: 'pasta',     icon: 'fa-bowl-food',      min: 1, max: 1 },
  { id: 'vegetable', label: 'Vegetable', unit: 'vegetable', units: 'vegetables', icon: 'fa-carrot',        min: 1, max: 1 },
  { id: 'salad',     label: 'Salad',     unit: 'salad',     units: 'salads',    icon: 'fa-leaf',           min: 1, max: 1 },
  { id: 'beef',      label: 'Beef',      unit: 'beef dish', units: 'beef dishes', icon: 'fa-cow',          min: 1, max: 1 },
  { id: 'chicken',   label: 'Chicken',   unit: 'chicken dish', units: 'chicken dishes', icon: 'fa-drumstick-bite', min: 1, max: 1 },
  { id: 'pork',      label: 'Pork',      unit: 'pork dish', units: 'pork dishes', icon: 'fa-bacon',        min: 1, max: 1 },
  { id: 'fish',      label: 'Fish',      unit: 'fish dish', units: 'fish dishes', icon: 'fa-fish',         min: 1, max: 1 },
  { id: 'dessert',   label: 'Dessert',   unit: 'dessert',   units: 'desserts',  icon: 'fa-ice-cream',      min: 1, max: 1 },
  { id: 'juice',     label: 'Juice',     unit: 'juice',     units: 'juices',    icon: 'fa-glass-water',    min: 1, max: 1 }
];
 
// admin category -> course sa page na ito
const CATEGORY_MAP = {
  pasta:     { course: 'pasta' },
  vegetable: { course: 'vegetable' },
  salad:     { course: 'salad' },
  beef:      { course: 'beef' },
  chicken:   { course: 'chicken' },
  pork:      { course: 'pork' },
  fish:      { course: 'fish' },
  dessert:   { course: 'dessert' },
  juice:     { course: 'juice' }
};

const menuItems = (window.MENU_ITEMS || [])
  .filter((i) => CATEGORY_MAP[i.category])
  .map((i) => ({
    id: String(i.id),              // string, dahil string ang galing sa data-id
    name: i.name,
    description: i.description,
    image: i.image,
    price: null,
    course: CATEGORY_MAP[i.category].course,
    subcategory: CATEGORY_MAP[i.category].subcategory || null
  }));

  // =========================================================
  // Elements
  // =========================================================
  const $ = (id) => document.getElementById(id);
  const els = {
    steps:       $('courseSteps'),
    notice:      $('courseNotice'),
    title:       $('courseTitle'),
    rule:        $('courseRule'),
    count:       $('courseCount'),
    subTabs:     $('subTabs'),
    list:        $('menuCatalogList'),
    back:        $('backCourseBtn'),
    next:        $('nextCourseBtn'),
    custom:      $('customMenu'),
    fill:        $('progressFill'),
    progressTxt: $('progressText'),
    restriction: $('restrictionInput'),
    continueBtn: $('continueBtn'),
    cancelBtn:   $('cancelBtn'),
    missingList: $('missingList'),
    incompleteText: $('incompleteText')
  };

  const incompleteModalEl = $('incompleteModal');
  const incompleteModal   = bootstrap.Modal.getOrCreateInstance(incompleteModalEl);
  const cancelModal       = bootstrap.Modal.getOrCreateInstance($('cancelModal'));

  // =========================================================
  // State + helpers
  // =========================================================
  const byId = new Map(menuItems.map((i) => [i.id, i]));
  const hasAnyItems = menuItems.length > 0;

  let selectedIds  = [];     // item ids, in the order they were picked
  let currentIndex = 0;      // which course is open on the left
  let activeSub    = 'all';  // beef / chicken / pork / fish tab
  let noticeTimer  = null;

  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (ch) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]
  ));

  const itemsOf     = (courseId) => menuItems.filter((i) => i.course === courseId);
  const selectedOf  = (courseId) => selectedIds.map((id) => byId.get(id)).filter((i) => i && i.course === courseId);
  const hasItems    = (c) => itemsOf(c.id).length > 0;
  // If a course has fewer items than its minimum, only ask for what exists (0 if it has none).
  const needed      = (c) => Math.min(c.min, itemsOf(c.id).length);
  const isComplete  = (c) => selectedOf(c.id).length >= needed(c);
  // A course opens only after every course before it has its minimum picks.
  const isUnlocked  = (index) => COURSES.slice(0, index).every(isComplete);
  const firstIncompleteIndex = () => COURSES.findIndex((c) => !isComplete(c));
  const countText   = (c, n) => `${n} ${n === 1 ? c.unit : c.units}`;
  const withNote    = (c, text) => (c.note ? `${text} (${c.note})` : text);

  function rangeText(c){
    const need = Math.max(needed(c), 1);
    return c.max > need ? `${need}–${c.max}` : `${need}`;
  }

  function ruleText(c){
    const need = needed(c);
    const what = withNote(c, c.units);
    return c.max > need
      ? `Choose ${need} to ${c.max} ${what}. At least ${need} is required before you can move on.`
      : `Choose ${countText(c, need)}${c.note ? ` (${c.note})` : ''}.`;
  }

  function nextIndex(from){
    let i = from + 1;
    if (i >= COURSES.length) return from;
    while (i < COURSES.length - 1 && !hasItems(COURSES[i])) i++;   // skip courses with nothing to pick
    return i;
  }
  function prevIndex(from){
    let i = from - 1;
    if (i < 0) return from;
    while (i > 0 && !hasItems(COURSES[i])) i--;
    return i;
  }

  // =========================================================
  // Notice (small yellow message above the course title)
  // =========================================================
  function showNotice(message){
    els.notice.textContent = message;
    els.notice.hidden = false;
    clearTimeout(noticeTimer);
    noticeTimer = setTimeout(() => { els.notice.hidden = true; }, 4500);
  }
  function hideNotice(){
    clearTimeout(noticeTimer);
    els.notice.hidden = true;
  }

  // =========================================================
  // Rendering
  // =========================================================
  function renderSteps(){
    els.steps.innerHTML = COURSES.map((c, i) => {
      const na     = !hasItems(c);
      const locked = !isUnlocked(i);
      const done   = !na && isComplete(c);
      const dot = na ? '–' : done ? '<i class="bi bi-check-lg"></i>' : locked ? '<i class="bi bi-lock-fill"></i>' : (i + 1);
      const cls = ['course-step', i === currentIndex ? 'current' : '', done ? 'done' : '', locked ? 'locked' : '', na ? 'na' : '']
        .filter(Boolean).join(' ');
      return `<li><button type="button" class="${cls}" data-index="${i}" ${i === currentIndex ? 'aria-current="step"' : ''}>
                <span class="cs-dot">${dot}</span><span>${esc(c.label)}</span></button></li>`;
    }).join('');
  }

  function renderCourse(){
    const c = COURSES[currentIndex];
    const picked = selectedOf(c.id).length;
    const need = needed(c);

    els.title.innerHTML = `<i class="fa-solid ${c.icon}"></i> ${esc(c.label)}`;
    els.rule.textContent = hasItems(c) ? ruleText(c) : 'Nothing to choose here yet.';

    els.count.textContent = hasItems(c) ? `${picked} selected` : '';
    els.count.hidden = !hasItems(c);
    els.count.classList.toggle('ok', picked >= need && picked > 0);

    // Beef / Chicken / Pork / Fish tabs (Main Courses only)
    if (c.subcategories && hasItems(c)) {
      const tabs = [{ id: 'all', label: 'All' }, ...c.subcategories];
      els.subTabs.innerHTML = tabs.map((t) => {
        const n = t.id === 'all' ? picked : selectedOf(c.id).filter((i) => i.subcategory === t.id).length;
        return `<button type="button" class="btn ${activeSub === t.id ? 'active' : ''}" data-sub="${t.id}">${esc(t.label)}${n ? ` (${n})` : ''}</button>`;
      }).join('');
      els.subTabs.hidden = false;
    } else {
      els.subTabs.hidden = true;
      els.subTabs.innerHTML = '';
    }

    // Items for this course
    let items = itemsOf(c.id);
    if (c.subcategories && activeSub !== 'all') items = items.filter((i) => i.subcategory === activeSub);

    if (!items.length) {
      const msg = !hasAnyItems
        ? 'No menu items available yet'
        : hasItems(c) ? 'No items in this category yet' : `No ${esc(c.label.toLowerCase())} available yet`;
      els.list.innerHTML = `<div class="empty-note"><i class="bi bi-journal-x"></i>${msg}</div>`;
    } else {
      els.list.innerHTML = items.map((it) => {
        const sel  = selectedIds.includes(it.id);
        const full = !sel && picked >= c.max;
        return `
          <div class="menu-item-row ${sel ? 'is-selected' : ''}" data-id="${esc(it.id)}">
            ${it.image
              ? `<img class="menu-item-thumb" src="${esc(it.image)}" alt="">`
              : `<div class="menu-item-thumb-empty"><i class="fa-solid ${c.icon}"></i></div>`}
            <div class="menu-item-info">
              <div class="menu-item-name">${esc(it.name)}</div>
              ${it.description ? `<div class="menu-item-desc">${esc(it.description)}</div>` : ''}
              ${it.price ? `<div class="menu-item-price">₱${Number(it.price).toLocaleString()}</div>` : ''}
            </div>
            <button type="button" class="btn-add-menu ${sel ? 'selected' : ''}" data-id="${esc(it.id)}"
              ${full ? `disabled title="You can choose up to ${countText(c, c.max)}"` : ''}>
              ${sel ? '<i class="bi bi-check-lg"></i> Selected' : 'Add Menu'}
            </button>
          </div>`;
      }).join('');
    }

    // Back / Next
    if (!hasAnyItems) {
      els.back.disabled = true;
      els.next.hidden = true;
    } else {
      els.back.disabled = prevIndex(currentIndex) === currentIndex;
      const next = nextIndex(currentIndex);
      els.next.hidden = next === currentIndex;
      els.next.innerHTML = `Next: ${esc(COURSES[next].label)} <i class="bi bi-arrow-right"></i>`;
    }
  }

function renderCustomMenu(){
  els.custom.innerHTML = COURSES.map((c, i) => {
    const picked = selectedOf(c.id);
    const na     = !hasItems(c);
    const ok     = !na && picked.length >= needed(c);
    const locked = !isUnlocked(i);
    const req = na ? 'Unavailable'
      : ok ? `<i class="bi bi-check-circle-fill"></i> ${picked.length} chosen`
      : `Pick ${rangeText(c)}`;

    const itemsHtml = picked.length
      ? picked.map((it) => `
          <div class="custom-menu-item">
            <span class="cmi-name">${esc(it.name)}</span>
            <button type="button" class="cmi-remove" data-id="${esc(it.id)}" aria-label="Remove ${esc(it.name)}"><i class="bi bi-x-lg"></i></button>
          </div>`).join('')
      : '<div class="custom-menu-empty">No items added</div>';

    return `
      <section class="cm-course ${i === currentIndex ? 'current' : ''} ${locked ? 'locked' : ''}">
        <button type="button" class="cm-course-head" data-index="${i}">
          <span class="cm-course-name"><i class="fa-solid ${c.icon}"></i> ${esc(c.label)}</span>
          <span class="cm-course-req ${ok ? 'ok' : ''}">${req}</span>
        </button>
        ${itemsHtml}
      </section>`;
  }).join('');
}

  function renderProgress(){
    const total = COURSES.filter(hasItems).length;
    const done  = COURSES.filter((c) => hasItems(c) && isComplete(c)).length;
    els.fill.style.width = total ? `${Math.round((done / total) * 100)}%` : '0';
    els.progressTxt.textContent = `${done} of ${total} course${total === 1 ? '' : 's'} complete`;
  }

  function renderAll(){
    renderSteps();
    renderCourse();
    renderCustomMenu();
    renderProgress();
  }

  // =========================================================
  // Saving (so the Confirmation step can show the menu)
  // =========================================================
  function save(){
    try {
      sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
        ids: selectedIds,
        items: selectedIds.map((id) => byId.get(id)).filter(Boolean).map((i) => ({
          id: i.id, name: i.name, course: i.course, subcategory: i.subcategory || null, price: i.price ?? null
        })),
        restriction: els.restriction.value.trim()
      }));
    } catch (e) { /* storage blocked: the menu simply isn't remembered */ }
  }

  function restore(){
    try {
      const saved = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || 'null');
      if (!saved) return;
      selectedIds = [...new Set(saved.ids || [])].filter((id) => byId.has(id));
      if (saved.restriction) els.restriction.value = saved.restriction;
    } catch (e) { /* ignore a corrupted value */ }
  }

  // =========================================================
  // Actions
  // =========================================================
  function ensureCurrentUnlocked(){
    if (isUnlocked(currentIndex)) return;
    const fi = firstIncompleteIndex();           // always an earlier course
    currentIndex = fi;
    activeSub = 'all';
    const c = COURSES[fi];
    showNotice(`Please choose at least ${countText(c, needed(c))} for ${c.label} to continue.`);
  }

  function afterChange(){
    ensureCurrentUnlocked();
    save();
    renderAll();
  }

  function toggleItem(id){
    const it = byId.get(id);
    if (!it) return;
    const c = COURSES.find((x) => x.id === it.course);

    if (selectedIds.includes(id)) {
      selectedIds = selectedIds.filter((x) => x !== id);
    } else {
      if (selectedOf(c.id).length >= c.max) {
        showNotice(`You can choose up to ${countText(c, c.max)}. Remove one to pick another.`);
        return;
      }
      selectedIds.push(id);
    }
    afterChange();
  }

  function goTo(index){
    if (!hasItems(COURSES[index]) && hasAnyItems) return;
    if (!isUnlocked(index)) {
      const c = COURSES[firstIncompleteIndex()];
      showNotice(`Please choose at least ${countText(c, needed(c))} for ${c.label} first.`);
      return;
    }
    currentIndex = index;
    activeSub = 'all';
    hideNotice();
    renderAll();
    els.steps.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  // =========================================================
  // Events
  // =========================================================
  els.steps.addEventListener('click', (e) => {
    const btn = e.target.closest('.course-step');
    if (btn) goTo(Number(btn.dataset.index));
  });

  els.subTabs.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-sub]');
    if (!btn) return;
    activeSub = btn.dataset.sub;
    renderCourse();
  });

  els.list.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-add-menu');
    if (btn && !btn.disabled) toggleItem(btn.dataset.id);
  });

  els.custom.addEventListener('click', (e) => {
    const remove = e.target.closest('.cmi-remove');
    if (remove) { toggleItem(remove.dataset.id); return; }
    const head = e.target.closest('.cm-course-head');
    if (head && hasItems(COURSES[Number(head.dataset.index)])) goTo(Number(head.dataset.index));
  });

  els.back.addEventListener('click', () => goTo(prevIndex(currentIndex)));

  els.next.addEventListener('click', () => {
    const c = COURSES[currentIndex];
    if (!isComplete(c)) {
      showNotice(`Please choose at least ${withNote(c, countText(c, needed(c)))} before moving on.`);
      return;
    }
    goTo(nextIndex(currentIndex));
  });

  els.restriction.addEventListener('input', save);

  els.continueBtn.addEventListener('click', () => {
    if (!hasAnyItems) {
      els.incompleteText.textContent = 'No menu items are available yet. Please check back soon.';
      els.missingList.innerHTML = '';
      incompleteModal.show();
      return;
    }

    const missing = COURSES.filter((c) => hasItems(c) && !isComplete(c));
    if (missing.length) {
      els.incompleteText.textContent = 'Please finish these courses before continuing:';
      els.missingList.innerHTML = missing.map((c) => {
        const have = selectedOf(c.id).length;
        return `<li><i class="bi bi-exclamation-circle"></i><strong>${esc(c.label)}</strong> — choose at least ${withNote(c, countText(c, needed(c)))} (you have ${have})</li>`;
      }).join('');

      // After the pop-up closes, take the customer to the first course that still needs picks
      incompleteModalEl.addEventListener('hidden.bs.modal', () => {
        const fi = firstIncompleteIndex();
        if (fi !== -1) { currentIndex = fi; activeSub = 'all'; renderAll(); }
      }, { once: true });
      incompleteModal.show();
      return;
    }

    save();
    fetch(window.SAVE_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': window.CSRF
      },
      body: JSON.stringify({
        menu_ids: selectedIds.map(Number),
        restriction: els.restriction.value.trim()
      })
    })
      .then((res) => { if (!res.ok) throw new Error(); window.location.href = NEXT_URL; })
      .catch(() => showNotice('Could not save your menu. Please try again.'));
  });

  els.cancelBtn.addEventListener('click', () => cancelModal.show());
  $('confirmCancelBtn').addEventListener('click', () => {
    try { sessionStorage.removeItem(STORAGE_KEY); } catch (e) {}
    window.location.href = CANCEL_URL;
  });

  // =========================================================
  // Start
  // =========================================================
  restore();
  const fi = firstIncompleteIndex();
  if (fi !== -1) {
    currentIndex = fi;
  } else if (hasAnyItems) {
    // everything was already chosen: open the last course that has items
    for (let i = COURSES.length - 1; i >= 0; i--) {
      if (hasItems(COURSES[i])) { currentIndex = i; break; }
    }
  }
  renderAll();
})();

});