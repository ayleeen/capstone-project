// ===== Settings =====
// Where the "Reserve Now" buttons go. Change this to your real reservation page,
// or remove the redirect if your own script already handles .reserve-now-btn.
const RESERVE_URL = "reservation.html";

// ===== Gallery data =====
// Set `image` to a real photo path (e.g. "../image/wedding1.jpg").
// If it is empty or fails to load, a colored placeholder with an icon is shown.
const events = [
  { title: "Romantic Wedding",          category: "weddings",  label: "Weddings",         icon: "fa-ring",              colors: ["#e8d9d3", "#b9a39a"], image: "../image/services/859884544_2577629652700102_5420390211546807245_n.jpg" },
  { title: "Golden Birthday Party",     category: "birthdays", label: "Birthdays",        icon: "fa-cake-candles",      colors: ["#6b3f14", "#c88a2d"], image: "../image/services/wedding.jpg" },
  { title: "Corporate Gala Dinner",     category: "corporate", label: "Corporate Events", icon: "fa-briefcase",         colors: ["#0c1b4d", "#2a58c4"], image: "../image/services/859884544_2577629652700102_5420390211546807245_n.jpg" },
  { title: "Elegant Debut Celebration", category: "special",   label: "Special Occasion", icon: "fa-champagne-glasses", colors: ["#d8b3b8", "#f3dfe0"], image: "../image/services/wedding.jpg" },
  { title: "Christening",               category: "special",   label: "Special Occasion", icon: "fa-dove",              colors: ["#efe6dc", "#cbb9a6"], image: "../image/services/859884544_2577629652700102_5420390211546807245_n.jpg" },
  { title: "Anniversary Dinner",        category: "special",   label: "Special Occasion", icon: "fa-heart",             colors: ["#7a3b2a", "#e0a16c"], image: "../image/services/wedding.jpg" },
  { title: "Family Reunion",            category: "special",   label: "Special Occasion", icon: "fa-people-group",      colors: ["#4d6a3a", "#a8bf8b"], image: "../image/services/859884544_2577629652700102_5420390211546807245_n.jpg" },
  { title: "Barangay Fiesta",           category: "special",   label: "Special Occasion", icon: "fa-flag",              colors: ["#1e4d2b", "#e2552b"], image: "../image/services/wedding.jpg" },
];

const LOREM = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";

const grid = document.getElementById("gGrid");
const filters = document.getElementById("gFilters");
const modal = document.getElementById("gModal");

// ===== Helpers =====
function imageParts(ev) {
  return {
    bg: `linear-gradient(135deg, ${ev.colors[0]}, ${ev.colors[1]})`,
    html: ev.image
      ? `<img src="${ev.image}" alt="${ev.title}" loading="lazy" onerror="this.outerHTML='<i class=&quot;fa-solid ${ev.icon}&quot;></i>'" />`
      : `<i class="fa-solid ${ev.icon}"></i>`,
  };
}

// ===== Render cards =====
function render(filter = "all") {
  const list = filter === "all" ? events : events.filter((e) => e.category === filter);
  grid.innerHTML = "";

  if (!list.length) {
    grid.innerHTML = `<p class="g-empty">No photos in this category yet.</p>`;
    return;
  }

  list.forEach((ev) => {
    const { bg, html } = imageParts(ev);
    const card = document.createElement("article");
    card.className = "g-card";
    card.innerHTML = `
      <div class="g-card-img" style="background:${bg}">${html}</div>
      <h4>${ev.title}</h4>
      <p>${LOREM}</p>
      <button type="button" class="g-view" data-index="${events.indexOf(ev)}">View Details</button>`;
    grid.appendChild(card);
  });
}

// ===== Filters =====
filters.addEventListener("click", (e) => {
  const btn = e.target.closest(".g-filter");
  if (!btn) return;
  filters.querySelectorAll(".g-filter").forEach((b) => b.classList.remove("active"));
  btn.classList.add("active");
  document.querySelector(".g-scroll").scrollTop = 0;
  render(btn.dataset.filter);
});

// ===== Details popup =====
grid.addEventListener("click", (e) => {
  const btn = e.target.closest(".g-view");
  if (!btn) return;
  const ev = events[btn.dataset.index];
  const { bg, html } = imageParts(ev);

  const img = document.getElementById("gModalImg");
  img.style.background = bg;
  img.innerHTML = html;
  document.getElementById("gModalTitle").textContent = ev.title;
  document.getElementById("gModalTag").textContent = ev.label;
  document.getElementById("gModalDesc").textContent = LOREM + " " + LOREM;

  modal.classList.add("open");
  modal.setAttribute("aria-hidden", "false");
});

function closeModal() {
  modal.classList.remove("open");
  modal.setAttribute("aria-hidden", "true");
}
document.getElementById("gModalClose").addEventListener("click", closeModal);
modal.addEventListener("click", (e) => { if (e.target === modal) closeModal(); });
document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeModal(); });

// ===== Reserve Now buttons =====
document.querySelectorAll(".reserve-now-btn").forEach((b) =>
  b.addEventListener("click", () => { window.location.href = "reservation-process-1.html"; })
);

// ===== Navbar (mobile menu) + footer year =====
const navToggle = document.getElementById("navToggleBtn");
const navLinks = document.getElementById("siteNavLinks");
if (navToggle && navLinks) {
  navToggle.addEventListener("click", () => navLinks.classList.toggle("open"));
}
const yr = document.getElementById("footerYear");
if (yr) yr.textContent = new Date().getFullYear();

// ===== Init =====
render();