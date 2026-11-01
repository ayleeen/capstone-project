document.addEventListener('DOMContentLoaded', () => {
(function menuSelectionFlow () {

  // ===== Settings =====
const STORAGE_KEY = 'menuSelection';
const NEXT_URL    = window.NEXT_URL;
const CANCEL_URL  = window.CANCEL_URL;
const DRAFT_ID    = String(window.RESERVATION_DRAFT_ID || '');

  const S = (id, label, cats, max, icon, note) => ({
    id, label, cats, max, min: max, icon, note, unit: 'dish', units: 'dishes'
  });

  const PASTA   = S('pasta',     'Pasta',     ['pasta'],     1, 'fa-bowl-food');
  const VEG     = S('vegetable', 'Vegetable', ['vegetable'], 1, 'fa-carrot');
  const PASTAVG = S('pastaveg',  'Pasta or Vegetable', ['pasta', 'vegetable'], 1, 'fa-bowl-food', 'pasta or vegetable');
  const SALAD   = S('salad',     'Salad',     ['salad'],     1, 'fa-leaf');
  const BEEF    = S('beef',      'Beef',      ['beef'],      1, 'fa-drumstick-bite');
  const CHICKEN = S('chicken',   'Chicken',   ['chicken'],   1, 'fa-drumstick-bite');
  const PORK    = S('pork',      'Pork',      ['pork'],      1, 'fa-bacon');
  const MEAT2   = S('meat2',     'Beef, Chicken or Pork', ['beef', 'chicken', 'pork'], 2, 'fa-drumstick-bite', 'chicken, beef or pork');
  const FISH    = S('fish',      'Fish',      ['fish'],      1, 'fa-fish');
  const DESSERT = S('dessert',   'Dessert',   ['dessert'],   1, 'fa-ice-cream');
  const JUICE   = S('juice',     'Juice',     ['juice'],     1, 'fa-glass-water');

  const TIERS = {
    700: [PASTA, VEG, SALAD, BEEF, CHICKEN, PORK, FISH, DESSERT, JUICE],
    600: [PASTAVG, SALAD, BEEF, CHICKEN, PORK, FISH, DESSERT, JUICE],
    500: [PASTAVG, SALAD, MEAT2, FISH, DESSERT, JUICE],
    400: [PASTAVG, MEAT2, FISH, DESSERT, JUICE]
  };

  let COURSES = [];

  const KNOWN = ['pasta','vegetable','salad','beef','chicken','pork','fish','dessert','juice'];
  const menuItems = (window.MENU_ITEMS || [])
    .filter((i) => KNOWN.includes(i.category))
    .map((i) => ({
      id: String(i.id),
      name: i.name,
      description: i.description,
      image: i.image,
      price: null,
      course: i.category
    }));

  // ===== Elements =====
  const $ = (id) => document.getElementById(id);
  const els = {
    steps: $('courseSteps'), notice: $('courseNotice'), title: $('courseTitle'),
    rule: $('courseRule'), count: $('courseCount'), subTabs: $('subTabs'),
    list: $('menuCatalogList'), back: $('backCourseBtn'), next: $('nextCourseBtn'),
    custom: $('customMenu'), fill: $('progressFill'), progressTxt: $('progressText'),
    restriction: $('restrictionInput'), continueBtn: $('continueBtn'),
    cancelBtn: $('cancelBtn'), missingList: $('missingList'), incompleteText: $('incompleteText')
  };

  const incompleteModalEl = $('incompleteModal');
  const incompleteModal   = bootstrap.Modal.getOrCreateInstance(incompleteModalEl);
  const cancelModal       = bootstrap.Modal.getOrCreateInstance($('cancelModal'));

  // ===== State + helpers =====

const byId = new Map(menuItems.map((i) => [i.id, i]));
const hasAnyItems = menuItems.length > 0;

const addonItems = (window.ADDON_ITEMS || []).map((item) => ({
  id: String(item.id),
  name: item.name,
  category: item.category,
  prices: item.prices || {}
}));

const addonById = new Map(addonItems.map((i) => [i.id, i]));

const peso = (n) => '₱' + Number(n).toLocaleString('en-PH');


let selectedIds  = [];
let pricePerHead = 0;
let addons       = [];
let currentIndex = 0;
let activeSub    = 'all';
let noticeTimer  = null;

// ===== MENU SETS =====
let menuSets = [];
let activeSetIndex = 0;

const MAX_PAX_PER_SET = 50;

function getRequiredSetCount() {
  const pax = Number(window.PAX) || 0;
  return Math.ceil(pax / MAX_PAX_PER_SET);
}

function getSetPax(index) {
  const pax = Number(window.PAX) || 0;
  const remaining = pax - (index * MAX_PAX_PER_SET);

  return Math.max(0, Math.min(MAX_PAX_PER_SET, remaining));
}

function syncActiveSet() {
  if (!menuSets[activeSetIndex]) return;

  menuSets[activeSetIndex].ids = [...selectedIds];
}

function renderMenuSets() {
  const list = $('menuSetsList');
  const count = $('menuSetCount');
  const limit = $('menuSetLimit');
  const createBtn = $('createMenuSetBtn');

  if (!list || !count || !limit || !createBtn) return;

  const required = getRequiredSetCount();

  count.textContent =
    `${menuSets.length} / ${required} Sets Created`;

  limit.textContent =
    `${required} set(s) required · Maximum 50 pax per set`;

  list.innerHTML = menuSets.map((set, index) => `
    <button
      type="button"
      class="btn ${index === activeSetIndex ? 'btn-primary' : 'btn-outline-primary'}"
      data-menu-set="${index}">
      Set ${index + 1}
      <small class="d-block">${set.pax} pax</small>
      ${index === activeSetIndex ? '<i class="bi bi-check-circle-fill"></i> Active' : ''}
    </button>
  `).join('');

  createBtn.disabled = menuSets.length >= required;
  createBtn.innerHTML = menuSets.length >= required
    ? '<i class="bi bi-check-circle"></i> All Sets Created'
    : '<i class="bi bi-plus-circle"></i> Create Set';
}

function switchMenuSet(index) {
  if (!menuSets[index]) return;

  syncActiveSet();

  activeSetIndex = index;
  selectedIds = [...menuSets[index].ids];

  currentIndex = COURSES.length
    ? Math.max(0, firstIncompleteIndex())
    : 0;

  activeSub = 'all';
  hideNotice();
  save();
  renderAll();
}

function createMenuSet() {
  const required = getRequiredSetCount();

  if (menuSets.length >= required) {
    showNotice('All required menu sets have already been created.');
    return;
  }

  syncActiveSet();

  // Copy the previous set's selections.
  const previousSet = menuSets[menuSets.length - 1];

  const newSet = {
    ids: [...previousSet.ids],
    pax: getSetPax(menuSets.length)
  };

  menuSets.push(newSet);

  activeSetIndex = menuSets.length - 1;
  selectedIds = [...newSet.ids];

  currentIndex = COURSES.length
    ? Math.max(0, firstIncompleteIndex())
    : 0;

  activeSub = 'all';

  save();
  renderAll();
}

  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (ch) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]
  ));
  const cap = (s) => s.charAt(0).toUpperCase() + s.slice(1);

  const slotById   = (id) => COURSES.find((c) => c.id === id);
  const itemsOf    = (id) => { const s = slotById(id); return s ? menuItems.filter((i) => s.cats.includes(i.course)) : []; };
  const selectedOf = (id) => { const s = slotById(id); return s ? selectedIds.map((x) => byId.get(x)).filter((i) => i && s.cats.includes(i.course)) : []; };
  const hasItems   = (c) => itemsOf(c.id).length > 0;
  const needed     = (c) => Math.min(c.min, itemsOf(c.id).length);
  const isComplete = (c) => selectedOf(c.id).length >= needed(c);
  const isUnlocked = (index) => COURSES.slice(0, index).every(isComplete);
  const firstIncompleteIndex = () => COURSES.findIndex((c) => !isComplete(c));
  const countText  = (c, n) => `${n} ${n === 1 ? c.unit : c.units}`;
  const withNote   = (c, text) => (c.note ? `${text} (${c.note})` : text);

  function buildCourses(){ COURSES = TIERS[pricePerHead] ? [...TIERS[pricePerHead]] : []; }

function pruneSetSelection(ids) {
    const kept = [];

    ids.forEach((id) => {
        const it = byId.get(id);
        const course = it && COURSES.find((c) => c.cats.includes(it.course));

        if (!course) return;

        const have = kept.filter((k) =>
            course.cats.includes(byId.get(k).course)
        ).length;

        if (have < course.max) {
            kept.push(id);
        }
    });

    return kept;
}

function pruneSelection() {
    menuSets.forEach((set) => {
        set.ids = pruneSetSelection(set.ids);
    });

    selectedIds = [...(menuSets[activeSetIndex]?.ids || [])];
}

  function rangeText(c){
    const need = Math.max(needed(c), 1);
    return c.max > need ? `${need}–${c.max}` : `${need}`;
  }
  function ruleText(c){
    const need = needed(c);
    return c.max > need
      ? `Choose ${need} to ${c.max} ${withNote(c, c.units)}.`
      : `Choose ${countText(c, need)}${c.note ? ` (${c.note})` : ''}.`;
  }
  function nextIndex(from){
    let i = from + 1;
    if (i >= COURSES.length) return from;
    while (i < COURSES.length - 1 && !hasItems(COURSES[i])) i++;
    return i;
  }
  function prevIndex(from){
    let i = from - 1;
    if (i < 0) return from;
    while (i > 0 && !hasItems(COURSES[i])) i--;
    return i;
  }

  function showNotice(message){
    els.notice.textContent = message;
    els.notice.hidden = false;
    clearTimeout(noticeTimer);
    noticeTimer = setTimeout(() => { els.notice.hidden = true; }, 4500);
  }
  function hideNotice(){ clearTimeout(noticeTimer); els.notice.hidden = true; }

  // ===== Rendering =====
  function renderTier(){
    document.querySelectorAll('.tier-btn').forEach((b) =>
      b.classList.toggle('selected', Number(b.dataset.price) === pricePerHead));
  }

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

    if (c.cats.length > 1 && hasItems(c)) {
      const tabs = [{ id: 'all', label: 'All' }, ...c.cats.map((k) => ({ id: k, label: cap(k) }))];
      els.subTabs.innerHTML = tabs.map((t) => {
        const n = t.id === 'all' ? picked : selectedOf(c.id).filter((i) => i.course === t.id).length;
        return `<button type="button" class="btn ${activeSub === t.id ? 'active' : ''}" data-sub="${t.id}">${esc(t.label)}${n ? ` (${n})` : ''}</button>`;
      }).join('');
      els.subTabs.hidden = false;
    } else {
      els.subTabs.hidden = true;
      els.subTabs.innerHTML = '';
    }

    let items = itemsOf(c.id);
    if (c.cats.length > 1 && activeSub !== 'all') items = items.filter((i) => i.course === activeSub);

    if (!items.length) {
      els.list.innerHTML = `<div class="empty-note"><i class="bi bi-journal-x"></i>No items in this category yet</div>`;
    } else {
      els.list.innerHTML = items.map((it) => {
        const sel  = selectedIds.includes(it.id);
        const full = !sel && c.max > 1 && picked >= c.max;
        return `
          <div class="menu-item-row ${sel ? 'is-selected' : ''}" data-id="${esc(it.id)}">
            ${it.image
              ? `<img class="menu-item-thumb" src="${esc(it.image)}" alt="">`
              : `<div class="menu-item-thumb-empty"><i class="fa-solid ${c.icon}"></i></div>`}
            <div class="menu-item-info">
              <div class="menu-item-name">${esc(it.name)}</div>
              ${it.description ? `<div class="menu-item-desc">${esc(it.description)}</div>` : ''}
            </div>
            <button type="button" class="btn-add-menu ${sel ? 'selected' : ''}" data-id="${esc(it.id)}"
              ${full ? `disabled title="You can choose up to ${countText(c, c.max)}"` : ''}>
              ${sel ? '<i class="bi bi-check-lg"></i> Selected' : 'Add Menu'}
            </button>
          </div>`;
      }).join('');
    }

    els.back.disabled = prevIndex(currentIndex) === currentIndex;
    const next = nextIndex(currentIndex);
    els.next.hidden = next === currentIndex;
    els.next.innerHTML = `Next: ${esc(COURSES[next].label)} <i class="bi bi-arrow-right"></i>`;
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
              ${c.cats.length > 1 ? `<span class="cm-item-tag">${esc(cap(it.course))}</span>` : ''}
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

  function renderEmpty(){
    els.steps.innerHTML = '';
    els.title.textContent = 'Choose your price per head';
    els.rule.textContent = 'Pick ₱400, ₱500, ₱600 or ₱700 above to see which dishes you can choose.';
    els.count.hidden = true;
    els.subTabs.hidden = true;
    els.list.innerHTML = '';
    els.back.disabled = true;
    els.next.hidden = true;
    els.custom.innerHTML = '';
    els.fill.style.width = '0';
    els.progressTxt.textContent = 'Select a price per head to start';
  }


function renderAddons(){
  const box = $('customAddons');

  if (!addons.length) {
    box.innerHTML = '';
    renderTotal();
    return;
  }

  let total = 0;

  const rows = addons.map((a) => {
    const it = addonById.get(String(a.id));
    if (!it) return '';

    const p = Number(it.prices[a.servings] || 0);
    total += p;

    return `<div class="custom-menu-item">
      <span class="cmi-name">
        ${esc(it.name)} <small>· ${a.servings} pax</small>
      </span>
      <span class="cm-item-tag">${peso(p)}</span>
      <button type="button" class="cmi-remove"
        data-addon="${esc(a.id)}"
        aria-label="Remove add-on">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>`;
  }).join('');

  box.innerHTML = `<section class="cm-course">
    <div class="cm-course-head" style="cursor:default">
      <span class="cm-course-name">
        <i class="fa-solid fa-plus"></i> Add-ons
      </span>
      <span class="cm-course-req ok">${peso(total)}</span>
    </div>
    ${rows}
  </section>`;

  renderTotal();
}



function initAddonForm(){
  const dishSelect = $('addonDish');
  const servingsSelect = $('addonServings');

  dishSelect.innerHTML =
    '<option value="">Select a dish</option>' +
    addonItems.map((item) =>
      `<option value="${esc(item.id)}">${esc(item.name)} (${esc(cap(item.category))})</option>`
    ).join('');

  function updateAddonServings(){
    const item = addonById.get(dishSelect.value);

    servingsSelect.innerHTML = '<option value="">Select pax</option>';

    if (!item) return;

    [30, 40, 50].forEach((servings) => {
      const price = item.prices[servings];

      if (price === undefined || price === null) return;

      const option = document.createElement('option');
      option.value = servings;
      option.textContent = `${servings} pax - ${peso(price)}`;
      servingsSelect.appendChild(option);
    });
  }

  dishSelect.addEventListener('change', updateAddonServings);

  updateAddonServings();
}


 function renderAll(){
  renderTier();
  renderMenuSets();
  renderAddons();

  if (!COURSES.length) {
    renderEmpty();
    return;
  }

  renderSteps();
  renderCourse();
  renderCustomMenu();
  renderProgress();
  renderTotal();
}

  // ===== Saving =====
function save(){
  try {
    syncActiveSet();

    sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
      draftId: DRAFT_ID,
      ids: selectedIds,
      menuSets,
      activeSetIndex,
      pricePerHead,
      addons,
      restriction: els.restriction.value.trim()
    }));
  } catch (e) {
    console.error('Unable to save menu:', e);
  }
}

function renderTotal(){
  const box = $('cmTotal');

  if (!pricePerHead) {
    box.innerHTML = '';
    return;
  }

  const pax = Number(window.PAX) || 0;
  const base = pricePerHead * pax;

  const extra = addons.reduce((sum, a) => {
    const item = addonById.get(String(a.id));
    return sum + Number(item?.prices?.[a.servings] || 0);
  }, 0);

  box.innerHTML = `
    <span>Grand Total</span>
    <span>${peso(base + extra)}</span>
  `;
}



function restore() {
  try {
    const saved = JSON.parse(
      sessionStorage.getItem(STORAGE_KEY) || 'null'
    );

    if (!saved) return;

    // Restore only if the saved data belongs to this reservation.
    if (!DRAFT_ID || saved.draftId !== DRAFT_ID) {
      sessionStorage.removeItem(STORAGE_KEY);
      return;
    }

    const requiredSets = getRequiredSetCount();

    if (requiredSets < 1) return;

    pricePerHead = Number(saved.pricePerHead) || 0;

    // Existing add-on restoration — unchanged.
    addons = (saved.addons || [])
      .filter((a) => {
        const item = addonById.get(String(a.id));
        return item &&
          item.prices[Number(a.servings)] !== undefined;
      })
      .map((a) => ({
        id: String(a.id),
        servings: Number(a.servings)
      }));

    buildCourses();

    // Restore only the number of sets required by current reservation.
    if (Array.isArray(saved.menuSets) && saved.menuSets.length) {
      menuSets = saved.menuSets
        .slice(0, requiredSets)
        .map((set, index) => ({
          ids: [...new Set(set.ids || [])]
            .map(String)
            .filter((id) => byId.has(id)),
          pax: getSetPax(index)
        }));

    } else {
      // Backward compatibility with the old single-menu format.
      menuSets = [{
        ids: [...new Set(saved.ids || [])]
          .map(String)
          .filter((id) => byId.has(id)),
        pax: getSetPax(0)
      }];
    }

    activeSetIndex = Math.max(
      0,
      Math.min(
        Number(saved.activeSetIndex) || 0,
        menuSets.length - 1
      )
    );

    selectedIds = [...menuSets[activeSetIndex].ids];

    pruneSelection();
    syncActiveSet();

    if (saved.restriction) {
      els.restriction.value = saved.restriction;
    }

  } catch (e) {
    console.error('Unable to restore menu:', e);
    sessionStorage.removeItem(STORAGE_KEY);
  }
}

  // ===== Actions =====
  function ensureCurrentUnlocked(){
    if (isUnlocked(currentIndex)) return;
    const fi = firstIncompleteIndex();
    currentIndex = fi;
    activeSub = 'all';
    const c = COURSES[fi];
    showNotice(`Please choose at least ${countText(c, needed(c))} for ${c.label} to continue.`);
  }

  function afterChange(){ ensureCurrentUnlocked(); save(); renderAll(); }

  function toggleItem(id){
    const it = byId.get(id);
    if (!it) return;
    if (!pricePerHead) {
      showNotice('Please choose a price per head first.');
      $('tierGrid').scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }
    const c = COURSES.find((x) => x.cats.includes(it.course));
    if (!c) return;

    if (selectedIds.includes(id)) {
      selectedIds = selectedIds.filter((x) => x !== id);
    } else {
      if (c.max === 1) {
        selectedIds = selectedIds.filter((x) => !c.cats.includes(byId.get(x)?.course));
      } else if (selectedOf(c.id).length >= c.max) {
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

  // ===== Events =====
  $('tierGrid').addEventListener('click', (e) => {
    const b = e.target.closest('.tier-btn');
    if (!b) return;
    pricePerHead = Number(b.dataset.price);
    buildCourses();
    pruneSelection();
    activeSub = 'all';
    const fi = firstIncompleteIndex();
    currentIndex = fi === -1 ? 0 : fi;
    hideNotice();
    save();
    renderAll();
  });

  // ===== MENU SET EVENTS =====
$('menuSetsList').addEventListener('click', (e) => {
  const btn = e.target.closest('[data-menu-set]');
  if (!btn) return;

  switchMenuSet(Number(btn.dataset.menuSet));
});

$('createMenuSetBtn').addEventListener('click', createMenuSet);

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


$('addonAddBtn').addEventListener('click', () => {
  const id = $('addonDish').value;
  const servings = Number($('addonServings').value);

  if (!id) {
    showNotice('Please select a dish for your add-on.');
    return;
  }

  if (!servings) {
    showNotice('Please select a pax tier.');
    return;
  }

  const item = addonById.get(id);

  if (!item || item.prices[servings] === undefined) {
    showNotice('The selected add-on price is unavailable.');
    return;
  }

  addons = addons.filter((a) => a.id !== id);
  addons.push({ id, servings });

  save();
  renderAddons();
});



$('customAddons').addEventListener('click', (e) => {
  const rm = e.target.closest('[data-addon]');
  if (!rm) return;

  addons = addons.filter((a) => a.id !== rm.dataset.addon);

  save();
  renderAddons();
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
    showNotice('No menu items are available yet.');
    return;
  }

  if (!pricePerHead) {
    showNotice('Please choose a price per head first.');
    $('tierGrid').scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
    return;
  }

  // Save the active set's latest selections.
  syncActiveSet();

  // Check if all required sets have been created.
  const requiredSets = getRequiredSetCount();

  if (menuSets.length !== requiredSets) {
    showNotice(`Please create all ${requiredSets} required menu sets first.`);
    $('menuSetsList').scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
    return;
  }

  // Check total pax allocation.
  const totalSetPax = menuSets.reduce(
    (total, set) => total + Number(set.pax),
    0
  );

  if (totalSetPax !== Number(window.PAX)) {
    showNotice('The pax allocation of your menu sets does not match your reservation.');
    return;
  }

  // Validate every menu set independently.
  const missingBySet = [];
  let firstMissingSetIndex = -1;

  const originalSelectedIds = [...selectedIds];

  menuSets.forEach((set, index) => {
    selectedIds = [...set.ids];

    const missing = COURSES.filter(
      (course) => hasItems(course) && !isComplete(course)
    );

    if (missing.length) {
      if (firstMissingSetIndex === -1) {
        firstMissingSetIndex = index;
      }

      missingBySet.push({
        setNumber: index + 1,
        pax: set.pax,
        courses: missing.map((course) => {
          const have = selectedOf(course.id).length;

          return `
            <li>
              <strong>${esc(course.label)}</strong> —
              choose at least
              ${withNote(course, countText(course, needed(course)))}
              (you have ${have})
            </li>
          `;
        })
      });
    }
  });

  // Restore the currently active set.
  selectedIds = originalSelectedIds;
  syncActiveSet();

  if (missingBySet.length) {
    els.incompleteText.textContent =
      'Please complete the required courses in these menu sets:';

    els.missingList.innerHTML = missingBySet.map((set) => `
      <li class="mb-2">
        <strong>Set ${set.setNumber} (${set.pax} pax)</strong>
        <ul>
          ${set.courses.join('')}
        </ul>
      </li>
    `).join('');

    incompleteModalEl.addEventListener('hidden.bs.modal', () => {
      switchMenuSet(firstMissingSetIndex);
    }, { once: true });

    incompleteModal.show();
    return;
  }

  save();

  fetch(window.SAVE_URL, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': window.CSRF
    },
    body: JSON.stringify({
      menu_sets: menuSets.map((set) => ({
        pax: Number(set.pax),
        menu_ids: set.ids.map(Number)
      })),
      price_per_head: pricePerHead,
      addons: addons.map((a) => ({
        id: Number(a.id),
        servings: a.servings
      })),
      restriction: els.restriction.value.trim()
    })
  })
    .then(async (res) => {
      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        throw new Error(data.message || 'Unable to save menu.');
      }

      return data;
    })
    .then(() => {
      window.location.href = NEXT_URL;
    })
    .catch((error) => {
      console.error('Continue error:', error);
      showNotice(error.message || 'Could not save your menu.');
    });
});

  els.cancelBtn.addEventListener('click', () => cancelModal.show());
  $('confirmCancelBtn').addEventListener('click', () => {
    try { sessionStorage.removeItem(STORAGE_KEY); } catch (e) {}
    window.location.href = CANCEL_URL;
  });

  // ===== Start =====
// ===== Start =====
initAddonForm();
restore();

// Create the first blank set if none exists.
if (menuSets.length === 0) {
  menuSets.push({
    ids: [],
    pax: getSetPax(0)
  });

  activeSetIndex = 0;
  selectedIds = [];
}

const fi = firstIncompleteIndex();
if (fi !== -1) currentIndex = fi;

renderAll();
save();

})();
});