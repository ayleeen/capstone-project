let packageData = window.packageData || null;
let selectedPackageId = null;

const hiddenInput = document.getElementById('selectedPackageId');

function renderPackages() {
  const grid = document.getElementById('packageGrid');
  grid.innerHTML = '';

  if (!packageData || !Array.isArray(packageData) || packageData.length === 0) {
    grid.innerHTML = `<div class="empty-state">No package data available.</div>`;
    return;
  }

  packageData.forEach((pkg, index) => {
    const card = document.createElement('div');
    card.className = 'package-card';
    card.dataset.id = pkg.id ?? index;

const inclusions = Array.isArray(pkg.inclusions)
  ? pkg.inclusions
  : [];

const inclusionsHtml = inclusions
  .map(item => {
    const li = document.createElement('li');
    li.textContent = item ?? '';
    return li.outerHTML;
  })
  .join('');

    card.innerHTML = `
      <div class="thumb" style="${pkg.image ? `background-image:url('${pkg.image}');background-size:cover;` : ''}"></div>
      <div class="radio-dot" role="radio" aria-checked="false"></div>
      <div class="info">
        <div class="title">${pkg.title ?? ''}</div>
        <div class="price">${pkg.price ?? ''}</div>
        <ul>${inclusionsHtml}</ul>
      </div>
    `;

    card.querySelector('.radio-dot').addEventListener('click', () => selectPackage(card.dataset.id));
    card.addEventListener('click', (e) => {
      if (e.target.classList.contains('radio-dot')) return;
      selectPackage(card.dataset.id);
    });

    grid.appendChild(card);
  });
}

function selectPackage(id) {
  selectedPackageId = id;
  hiddenInput.value = id;   // this is what gets submitted to Laravel

  document.querySelectorAll('.package-card').forEach(card => {
    const isSelected = card.dataset.id === String(id);
    card.classList.toggle('selected', isSelected);
    card.querySelector('.radio-dot').setAttribute('aria-checked', isSelected);
  });
  updateContinueState();
}

function updateContinueState() {
  document.getElementById('continueBtn').disabled = selectedPackageId === null;
}

document.getElementById('cancelBtn').addEventListener('click', () => {
  selectedPackageId = null;
  hiddenInput.value = '';
  document.getElementById('foodRestriction').value = '';
  renderPackages();
  updateContinueState();
});

renderPackages();

// re-select the package if the user comes back to this page
if (window.initialPackageId) {
  selectPackage(window.initialPackageId);
} else {
  updateContinueState();
}