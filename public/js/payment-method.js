// payment-method.js

// ---- DOM refs ----
const paymentForm = document.getElementById('paymentForm');
const qrPreview   = document.getElementById('qrPreview');
const qrEmpty     = document.getElementById('qrEmpty');
const qrInput     = document.getElementById('qrInput');
const changeQrBtn = document.getElementById('changeQrBtn');
const removeQrBtn = document.getElementById('removeQrBtn');
const methodInputs = [...document.querySelectorAll('input[data-method]')];

const alertModalEl    = document.getElementById('alertModal');
const alertModal      = new bootstrap.Modal(alertModalEl);
const removeQrModalEl = document.getElementById('removeQrModal');
const removeQrModal   = new bootstrap.Modal(removeQrModalEl);

// ---- Settings ----
const STORAGE_KEY   = 'lolays.paymentMethods';
const MAX_QR_BYTES  = 2 * 1024 * 1024; // 2MB
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/svg+xml'];

// Starting values (all methods on, as in the design, and no QR code yet).
// Replace with data from your backend.
const DEFAULT_SETTINGS = {
  methods: { cod: true, gcash: true, paymaya: true, bank: true },
  qr: ''
};

let currentQr = '';

// ---- Alert modal (replaces alert()) ----
const ALERT_TYPES = {
  info:    { cls: 'info',    icon: 'fa-circle-info' },
  warning: { cls: 'warning', icon: 'fa-triangle-exclamation' },
  success: { cls: 'success', icon: 'fa-circle-check' },
  danger:  { cls: 'danger',  icon: 'fa-circle-xmark' }
};

function showAlert(message, { title = 'Notice', type = 'info', okText = 'OK', onClose = null } = {}){
  const cfg = ALERT_TYPES[type] || ALERT_TYPES.info;
  document.getElementById('alertIcon').className = `confirm-icon-circle ${cfg.cls}`;
  document.getElementById('alertIconGlyph').className = `fa-solid ${cfg.icon}`;
  document.getElementById('alertTitle').textContent = title;
  document.getElementById('alertMessage').textContent = message;
  document.getElementById('alertOkBtn').textContent = okText;

  if(onClose){
    // run once, whenever the modal finishes closing (OK button, backdrop, or Esc)
    alertModalEl.addEventListener('hidden.bs.modal', onClose, { once: true });
  }
  alertModal.show();
}

// ---- Load / save (localStorage so you can test without a backend) ----
function loadSettings(){
  try {
    const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
    if(!saved) return structuredClone(DEFAULT_SETTINGS);
    return {
      methods: { ...DEFAULT_SETTINGS.methods, ...(saved.methods || {}) },
      qr: typeof saved.qr === 'string' ? saved.qr : ''
    };
  } catch (err) {
    return structuredClone(DEFAULT_SETTINGS);
  }
}

function persistSettings(settings){
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
    return true;
  } catch (err) {
    return false; // storage full or blocked
  }
}

// ---- QR code ----
function setQr(src){
  currentQr = src || '';
  if(currentQr){
    qrPreview.src = currentQr;
    qrPreview.classList.remove('d-none');
    qrEmpty.classList.add('d-none');
  } else {
    qrPreview.removeAttribute('src');
    qrPreview.classList.add('d-none');
    qrEmpty.classList.remove('d-none');
  }
}

changeQrBtn.addEventListener('click', () => qrInput.click());

qrInput.addEventListener('change', () => {
  const file = qrInput.files[0];
  qrInput.value = ''; // lets the same file be picked again later
  if(!file) return;

  if(!ALLOWED_TYPES.includes(file.type)){
    showAlert('Please choose a JPG, PNG or SVG image.', { title: 'Unsupported File', type: 'warning' });
    return;
  }
  if(file.size > MAX_QR_BYTES){
    showAlert('That image is larger than 2MB. Please choose a smaller one.', { title: 'File Too Large', type: 'warning' });
    return;
  }

  const reader = new FileReader();
  reader.onload = () => setQr(reader.result);
  reader.onerror = () => showAlert('We couldn\'t read that file. Please try another image.', { title: 'Upload Failed', type: 'danger' });
  reader.readAsDataURL(file);
});

removeQrBtn.addEventListener('click', () => {
  if(!currentQr){
    showAlert('There is no QR code to remove.', { title: 'No QR Code', type: 'info' });
    return;
  }
  removeQrModal.show();
});

document.getElementById('confirmRemoveQrBtn').addEventListener('click', () => {
  setQr('');
  removeQrModal.hide();
});

// ---- Fill the form ----
function fillForm(settings){
  methodInputs.forEach(input => {
    input.checked = !!settings.methods[input.dataset.method];
  });
  setQr(settings.qr);
}

// ---- Save ----
paymentForm.addEventListener('submit', (e) => {
  e.preventDefault();

  const methods = {};
  methodInputs.forEach(input => { methods[input.dataset.method] = input.checked; });

  if(!Object.values(methods).some(Boolean)){
    showAlert('Please enable at least one payment method so customers can pay.', { title: 'No Payment Method Selected', type: 'warning' });
    return;
  }

  if(persistSettings({ methods, qr: currentQr })){
    let redirected = false;
    const goToSettings = () => {
      if(redirected) return;
      redirected = true;
      window.location.href = 'settings.html';
    };

    showAlert('Your payment settings have been updated successfully.', {
      title: 'Changes Saved',
      type: 'success',
      okText: 'Go to Settings',
      onClose: goToSettings
    });

    // automatic redirect, no click needed
    setTimeout(goToSettings, 1500);
  } else {
    showAlert('Your changes couldn\'t be stored in this browser. If you uploaded a QR code, try a smaller image.', { title: 'Could Not Save', type: 'danger' });
  }
});

// ---- Init ----
fillForm(loadSettings());