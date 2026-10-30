
(function () {
  // ---- Mobile nav toggle ----
  const navToggleBtn = document.getElementById('navToggleBtn');
  const navLinks = document.getElementById('siteNavLinks');

  if (navToggleBtn && navLinks) {
    navToggleBtn.addEventListener('click', () => {
      navLinks.classList.toggle('open');
    });

    // Close the mobile menu after picking a link
    navLinks.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => navLinks.classList.remove('open'));
    });
  }

  // ---- Smooth scroll for in-page anchor links (e.g. "Packages" -> #featuredPackages) ----
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    const targetId = link.getAttribute('href').slice(1);
    if (!targetId) return;
    const target = document.getElementById(targetId);
    if (!target) return;

    link.addEventListener('click', (e) => {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  // ---- "View Packages" buttons (present on every hero slide) scroll to Featured Packages ----
  document.querySelectorAll('.view-packages-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const target = document.getElementById('featuredPackages');
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

function reserveNowStub() {
  window.location.href = "/reservation-process-1";
}
document.querySelectorAll('.reserve-now-btn').forEach((btn) => {
  btn.addEventListener('click', reserveNowStub);
});
})();